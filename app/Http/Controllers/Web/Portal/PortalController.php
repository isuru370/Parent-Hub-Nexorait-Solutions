<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\Portal\PortalApi;
use App\Services\Portal\PortalSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use RuntimeException;

/**
 * Student / parent portal. Every page reads live data from the
 * student's institute through PortalApi. Everything is read only except
 * the password.
 */
class PortalController extends Controller
{
    public function __construct(
        private PortalApi $api,
        private PortalSession $portal
    ) {}

    public function dashboard(): View
    {
        return $this->page('portal.dashboard', function () {
            $dashboard = $this->api->dashboard();

            // Small summaries; one failing must not break the dashboard
            $attendance = rescue(fn () => $this->api->attendance()['summary'] ?? null, null, false);
            $payments = rescue(fn () => $this->api->payments(), null, false);

            $courses = is_array($dashboard['courses'] ?? null) ? $dashboard['courses'] : [];
            $this->portal->setFlag('has_courses', $courses !== []);
            $this->rememberInstitute($dashboard['institute'] ?? null);

            return [
                'dashboard' => $dashboard,
                'attendanceSummary' => $attendance,
                'paymentClasses' => $payments['classes'] ?? [],
                'courses' => $courses,
                'notice' => SystemSetting::get('portal_notice'),
            ];
        });
    }

    public function attendance(): View
    {
        return $this->page('portal.attendance', fn () => ['attendance' => $this->api->attendance()]);
    }

    public function payments(): View
    {
        return $this->page('portal.payments', fn () => ['payments' => $this->api->payments()]);
    }

    public function exams(): View
    {
        return $this->page('portal.exams', fn () => ['exams' => $this->api->exams()]);
    }

    public function result(int $exam): View
    {
        return $this->page('portal.result', fn () => ['result' => $this->api->result($exam)]);
    }

    public function schedule(): View
    {
        return $this->page('portal.schedule', function () {
            $items = collect($this->api->schedule())
                ->filter(fn ($row) => is_array($row))
                ->sortBy(fn ($row) => ($row['class_date'] ?? '') . ' ' . ($row['start_time'] ?? ''))
                ->values();

            $today = now()->toDateString();

            return [
                'upcoming' => $items->filter(fn ($r) => substr((string) ($r['class_date'] ?? ''), 0, 10) >= $today
                    && ($r['status'] ?? '') !== 'completed')->values(),
                'past' => $items->filter(fn ($r) => substr((string) ($r['class_date'] ?? ''), 0, 10) < $today
                    || ($r['status'] ?? '') === 'completed')->reverse()->take(20)->values(),
            ];
        });
    }

    public function teachers(): View
    {
        return $this->page('portal.teachers', function () {
            $teachers = collect($this->api->teachers())->filter(fn ($t) => is_array($t));

            return [
                'teachers' => $teachers->where('is_my_teacher', true)->values()->all(),
            ];
        });
    }

    public function courses(): View
    {
        return $this->page('portal.courses', function () {
            $data = $this->coursesData();

            return ['courses' => $data['courses'], 'coursesEnabled' => $data['enabled']];
        });
    }

    public function course(int $course): View
    {
        return $this->page('portal.course', function () use ($course) {
            $found = collect($this->coursesData()['courses'])->firstWhere('course_id', $course);

            if (!$found) {
                throw new RuntimeException('This course was not found.');
            }

            return ['course' => $found];
        });
    }

    public function idCard(): View
    {
        return $this->page('portal.id-card', fn () => ['profile' => $this->profileData()]);
    }

    public function notifications(Request $request): View
    {
        $page = max(1, (int) $request->query('page', 1));

        return $this->page('portal.notifications', fn () => [
            'notifications' => $this->api->notifications($page),
            'page' => $page,
        ]);
    }

    public function readAllNotifications(): RedirectResponse
    {
        try {
            $this->api->markAllNotificationsRead();
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function profile(): View
    {
        // The full profile comes from the institute; older institutes only
        // have what was returned at login.
        $profile = rescue(fn () => $this->profileData(), null, false);

        return view('portal.profile', [
            'profile' => $profile,
            'student' => $this->portal->student(),
            'institute' => $this->portal->institute(),
            'username' => $this->portal->username(),
            'supportPhone' => SystemSetting::get('support_phone') ?: '0768971213',
            'supportEmail' => SystemSetting::get('support_email'),
            'hasCourses' => (bool) $this->portal->flag('has_courses', false),
        ]);
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', 'different:current_password', Password::min(6)],
        ], [
            'new_password.different' => 'The new password must be different from the current one.',
        ]);

        try {
            $result = $this->api->changePassword(
                $data['current_password'],
                $data['new_password'],
                $data['new_password_confirmation'] ?? $request->input('new_password_confirmation')
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return $result['status']
            ? back()->with('success', 'Password changed. Use the new password next time you log in.')
            : back()->withErrors(['current_password' => $result['message']]);
    }

    /** ['enabled' => bool, 'courses' => list] */
    protected function coursesData(): array
    {
        try {
            $data = $this->api->courses();
        } catch (RuntimeException $e) {
            // Institute not updated yet: no /courses route
            if (str_contains(strtolower($e->getMessage()), 'not found')) {
                return ['enabled' => false, 'courses' => []];
            }

            throw $e;
        }

        $courses = array_values(array_filter((array) ($data['courses'] ?? []), 'is_array'));
        $this->portal->setFlag('has_courses', $courses !== []);

        return ['enabled' => (bool) ($data['enabled'] ?? false), 'courses' => $courses];
    }

    protected function profileData(): array
    {
        $profile = $this->api->profile();
        $this->rememberInstitute($profile['institute'] ?? null);

        return $profile;
    }

    /** Keep the institute's own name / logo when the hub registry has none. */
    protected function rememberInstitute(mixed $institute): void
    {
        if (!is_array($institute) || empty($institute['name'])) {
            return;
        }

        $current = $this->portal->institute();

        $this->portal->setFlag('institute', [
            'name' => $institute['name'],
            'logo' => $current['logo'] ?? ($institute['logo_url'] ?? null),
            'tagline' => $institute['tagline'] ?? null,
        ]);
    }

    /**
     * Render a page; if the institute cannot be reached show the error
     * inside the page instead of a 500.
     */
    protected function page(string $view, callable $load): View
    {
        try {
            $data = $load();
            $error = null;
        } catch (RuntimeException $e) {
            $data = [];
            $error = $e->getMessage();
        }

        // Empty values so the page still renders around the error message
        $defaults = [
            'dashboard' => [], 'attendanceSummary' => null, 'paymentClasses' => [], 'notice' => null,
            'attendance' => [], 'payments' => [], 'exams' => [], 'result' => [], 'courses' => [],
            'coursesEnabled' => false, 'course' => [], 'teachers' => [], 'profile' => [],
            'upcoming' => collect(), 'past' => collect(), 'notifications' => [], 'page' => 1,
        ];

        return view($view, $data + $defaults + [
            'error' => $error,
            'student' => $this->portal->student(),
            'institute' => $this->portal->institute(),
            'hasCourses' => (bool) $this->portal->flag('has_courses', false),
        ]);
    }
}
