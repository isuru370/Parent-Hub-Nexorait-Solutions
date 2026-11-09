<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\StudentRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Premium portal: dashboard with courses, profile (read only), digital ID,
 * teachers, courses and the welcome page.
 */
class PortalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Institute::create(['name' => 'Savidya Institute', 'code' => 'SAV', 'api_url' => 'https://sav.lk', 'status' => true]);
        StudentRegistry::create(['username' => 'S1', 'student_custom_id' => 'S1', 'institute_code' => 'SAV', 'is_active' => true]);
    }

    /** @param  array<string, mixed>  $override  path => response */
    private function fakeInstitute(array $override = []): void
    {
        Http::fake(function (Request $request) use ($override) {
            $path = str_replace('https://sav.lk/api/parent/v1/', '', $request->url());

            if (array_key_exists($path, $override)) {
                return $override[$path];
            }

            return match ($path) {
                'auth/login' => Http::response($this->fixture('auth_login_token')),
                'dashboard' => Http::response($this->fixture('dashboard_v2')),
                'attendance' => Http::response($this->fixture('attendance')),
                'payments' => Http::response($this->fixture('payments')),
                'profile' => Http::response($this->fixture('profile')),
                'courses' => Http::response($this->fixture('courses')),
                'teachers' => Http::response($this->fixture('teachers')),
                'notifications' => Http::response($this->fixture('notifications')),
                default => Http::response(['status' => false, 'message' => 'Not found'], 404),
            };
        });
    }

    private function login(): void
    {
        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234'])->assertRedirect(route('portal.dashboard'));
    }

    public function test_dashboard_shows_the_student_institute_and_courses(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->get(route('portal.dashboard'))->assertOk()
            ->assertSee('A. Silva')
            ->assertSee('Savidya Institute')
            ->assertSee('Digital ID card')
            ->assertSee('Diploma in IT')
            ->assertSee('My Courses'); // menu shows courses once the student has one
    }

    public function test_profile_is_read_only_with_institute_classes_and_password_form(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->get(route('portal.profile'))->assertOk()
            ->assertSee('Savidya Institute')
            ->assertSee('Colombo')
            ->assertSee('Maths 10 CMB')
            ->assertSee('Rs. 2,000.00 / month', false)
            ->assertSee("can't be changed here", false)
            ->assertSee('name="current_password"', false)
            ->assertSee('0768971213')
            ->assertDontSee('name="full_name"', false);
    }

    public function test_profile_falls_back_to_login_details_for_old_institutes(): void
    {
        $this->fakeInstitute(['profile' => Http::response(['message' => 'Not found'], 404)]);
        $this->login();

        $this->get(route('portal.profile'))->assertOk()
            ->assertSee('Showing the details saved when you logged in.')
            ->assertSee('name="current_password"', false);
    }

    public function test_digital_id_shows_the_card_qr(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->get(route('portal.id-card'))->assertOk()
            ->assertSee('data:image/svg+xml;base64,', false)
            ->assertSee('Permanent card');
    }

    public function test_teachers_courses_and_course_detail(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->get(route('portal.teachers'))->assertOk()->assertSee('Nimal Perera')->assertSee('Enrolled');
        $this->get(route('portal.courses'))->assertOk()->assertSee('Diploma in IT')->assertSee('Paid Rs. 2,500.00');
        $this->get(route('portal.course', 1))->assertOk()
            ->assertSee('Installments')->assertSee('Partly paid')->assertSee('Rs. 9,500.00');
        $this->get(route('portal.course', 99))->assertOk()->assertSee('This course was not found.');
    }

    public function test_courses_page_when_the_institute_has_no_courses(): void
    {
        $this->fakeInstitute(['courses' => Http::response(['status' => true, 'data' => ['enabled' => false, 'courses' => []]])]);
        $this->login();

        $this->get(route('portal.courses'))->assertOk()->assertSee('This institute does not have courses.');
    }

    public function test_welcome_page(): void
    {
        $this->get('/')->assertOk()
            ->assertSee("Your child's education,", false)
            ->assertSee('Digital ID card')
            ->assertSee('Courses')
            ->assertSee('0768971213');
    }
}
