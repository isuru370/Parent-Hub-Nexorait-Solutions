<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\StudentRegistry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Institute::create(['name' => 'Savidya Institute', 'code' => 'SAV', 'api_url' => 'https://sav.lk', 'status' => true]);
        StudentRegistry::create(['username' => 'S1', 'student_custom_id' => 'S1', 'institute_code' => 'SAV', 'is_active' => true]);
    }

    /** Fake the institute with the real NexoraEDU responses. */
    private function fakeInstitute(): void
    {
        Http::fake(function (Request $request) {
            $path = str_replace('https://sav.lk/api/parent/v1/', '', $request->url());

            return match ($path) {
                'auth/login' => $request['password'] === 'pass1234'
                    ? Http::response($this->fixture('auth_login'))
                    : Http::response(['status' => false, 'message' => 'Invalid username or password'], 401),
                'dashboard' => Http::response($this->fixture('dashboard')),
                'attendance' => Http::response($this->fixture('attendance')),
                'payments' => Http::response($this->fixture('payments')),
                'exams' => Http::response($this->fixture('exams')),
                'results' => Http::response($this->fixture('results')),
                'schedule' => Http::response($this->fixture('schedule')),
                'notifications' => Http::response($this->fixture('notifications')),
                'notifications/read-all' => Http::response(['success' => true, 'message' => 'All read']),
                'auth/change-password' => $request['current_password'] === 'pass1234'
                    ? Http::response($this->fixture('auth_change-password'))
                    : Http::response(['status' => false, 'message' => 'Current password is incorrect.'], 400),
                default => Http::response(['status' => false, 'message' => 'Not found'], 404),
            };
        });
    }

    private function login(): void
    {
        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234'])->assertRedirect(route('portal.dashboard'));
    }

    public function test_student_logs_in_with_username_and_sees_every_page(): void
    {
        $this->fakeInstitute();
        $this->login();
        $this->assertGuest(); // not an admin

        $this->get(route('portal.dashboard'))->assertOk()
            ->assertSee('A. Silva')->assertSee('Savidya Institute')->assertSee('Payment received');
        $this->get(route('portal.attendance'))->assertOk()->assertSee('Maths 10 CMB')->assertSee('100%');
        $this->get(route('portal.payments'))->assertOk()->assertSee('not paid')->assertSee('Jan');
        $this->get(route('portal.exams'))->assertOk()->assertSee('Term test');
        $this->get(route('portal.result', 1))->assertOk()->assertSee('78')->assertSee('(your child)', false);
        $this->get(route('portal.schedule'))->assertOk()->assertSee('Maths 10 CMB');
        $this->get(route('portal.notifications'))->assertOk()->assertSee('October fee Rs. 2,000 paid.');
        $this->get(route('portal.profile'))->assertOk()->assertSee('S1');

        // the student id sent to the institute always comes from the session
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/payments') && $r['student_id'] === 567);

        // a student cannot open the admin
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get(route('portal.dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_password_shows_the_institute_message(): void
    {
        $this->fakeInstitute();

        $this->post('/login', ['login' => 'S1', 'password' => 'bad'])
            ->assertSessionHasErrors(['login' => 'Invalid username or password']);

        $this->get(route('portal.dashboard'))->assertRedirect(route('login'));
    }

    public function test_unreachable_institute_shows_a_message_not_an_error_page(): void
    {
        $this->fakeInstitute();
        $this->login();

        Http::fake(fn () => Http::failedConnection());

        $this->get(route('portal.payments'))->assertOk()
            ->assertSee('Your institute could not be reached');
        $this->get(route('portal.dashboard'))->assertOk()->assertSee('Try again');
    }

    public function test_change_password_and_mark_read(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->put(route('portal.password'), ['current_password' => 'wrong1', 'new_password' => 'newpass99', 'new_password_confirmation' => 'newpass99'])
            ->assertSessionHasErrors(['current_password' => 'Current password is incorrect.']);

        $this->put(route('portal.password'), ['current_password' => 'pass1234', 'new_password' => 'newpass99', 'new_password_confirmation' => 'newpass99'])
            ->assertSessionHas('success');

        $this->put(route('portal.password'), ['current_password' => 'pass1234', 'new_password' => 'pass1234', 'new_password_confirmation' => 'pass1234'])
            ->assertSessionHasErrors('new_password');

        $this->post(route('portal.notifications.read-all'))->assertSessionHas('success');
    }

    public function test_admin_login_replaces_a_student_session(): void
    {
        $this->fakeInstitute();
        $this->login();

        User::create(['name' => 'Admin', 'email' => 'a@b.lk', 'password' => 'secret123', 'is_active' => true]);
        $this->post('/login', ['login' => 'a@b.lk', 'password' => 'secret123'])->assertRedirect(route('admin.dashboard'));

        $this->get(route('portal.dashboard'))->assertRedirect(route('login'));
    }
}
