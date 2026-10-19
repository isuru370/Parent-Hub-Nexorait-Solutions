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
 * student's institute through PortalApi.
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

            return [
                'dashboard' => $dashboard,
                'attendanceSummary' => $attendance,
                'paymentClasses' => $payments['classes'] ?? [],
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
        return view('portal.profile', [
            'student' => $this->portal->student(),
            'institute' => $this->portal->institute(),
            'username' => $this->portal->username(),
            'supportPhone' => SystemSetting::get('support_phone'),
            'supportEmail' => SystemSetting::get('support_email'),
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
            'attendance' => [], 'payments' => [], 'exams' => [], 'result' => [],
            'upcoming' => collect(), 'past' => collect(), 'notifications' => [], 'page' => 1,
        ];

        return view($view, $data + $defaults + [
            'error' => $error,
            'student' => $this->portal->student(),
            'institute' => $this->portal->institute(),
        ]);
    }
}
