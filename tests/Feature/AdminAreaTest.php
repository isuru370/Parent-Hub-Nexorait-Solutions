<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use App\Models\Institute;
use App\Models\StudentRegistry;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Institute $institute;
    private StudentRegistry $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create(['name' => 'Isuru', 'email' => 'admin@nexorait.lk', 'password' => 'secret123', 'is_active' => true]);
        $this->institute = Institute::create(['name' => 'Savidya', 'code' => 'SAV', 'api_url' => 'https://sav.lk', 'status' => true, 'last_ping_at' => now()]);
        $this->student = StudentRegistry::create(['username' => 'SA10001', 'student_custom_id' => 'SA10001', 'institute_code' => 'SAV', 'is_active' => true]);
    }

    public function test_admin_logs_in_with_email_and_sees_every_page(): void
    {
        $this->post('/login', ['login' => 'admin@nexorait.lk', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
        $this->assertNotNull($this->admin->fresh()->last_login_at);

        foreach ([
            route('admin.dashboard'),
            route('admin.institutes.index'),
            route('admin.institutes.index', ['search' => 'sav', 'status' => 'active']),
            route('admin.institutes.show', $this->institute),
            route('admin.students.index'),
            route('admin.students.index', ['institute' => 'SAV', 'status' => 'active', 'search' => 'SA1']),
            route('admin.app-versions.index'),
            route('admin.admins.index'),
            route('admin.settings'),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get(route('admin.dashboard'))->assertSee('Savidya')->assertSee('SA10001');
    }

    public function test_wrong_password_and_disabled_admin_cannot_log_in(): void
    {
        $this->post('/login', ['login' => 'admin@nexorait.lk', 'password' => 'wrong'])->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->admin->update(['is_active' => false]);
        $this->post('/login', ['login' => 'admin@nexorait.lk', 'password' => 'secret123'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        config(['hub.login_attempts_per_minute' => 3]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['login' => 'admin@nexorait.lk', 'password' => 'wrong']);
        }

        $this->post('/login', ['login' => 'admin@nexorait.lk', 'password' => 'secret123'])->assertSessionHasErrors('login');
        $this->assertStringStartsWith('Too many attempts.', session('errors')->first('login'));
        $this->assertGuest();
    }

    public function test_guests_cannot_open_the_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.students.index'))->assertRedirect(route('login'));
    }

    public function test_admin_actions(): void
    {
        $this->actingAs($this->admin);

        $this->patch(route('admin.institutes.toggle', $this->institute))->assertRedirect();
        $this->assertFalse($this->institute->fresh()->status);

        $this->put(route('admin.institutes.update', $this->institute), ['name' => 'Savidya Institute', 'api_url' => 'https://new.sav.lk'])->assertRedirect();
        $this->assertSame('https://new.sav.lk', $this->institute->fresh()->api_url);

        $this->patch(route('admin.students.toggle', $this->student))->assertRedirect();
        $this->assertFalse($this->student->fresh()->is_active);

        $this->post(route('admin.app-versions.store'), ['platform' => 'android', 'version' => '1.0.3', 'force_update' => '1'])->assertSessionHasNoErrors();
        $this->post(route('admin.app-versions.store'), ['platform' => 'android', 'version' => '1.0.3'])->assertSessionHasErrors('version');
        $this->post(route('admin.app-versions.store'), ['platform' => 'android', 'version' => 'abc'])->assertSessionHasErrors('version');
        $version = AppVersion::first();
        $this->patch(route('admin.app-versions.update', $version), ['field' => 'force_update']);
        $this->assertFalse($version->fresh()->force_update);
        $this->delete(route('admin.app-versions.destroy', $version));
        $this->assertSame(0, AppVersion::count());

        $this->put(route('admin.settings.update'), ['support_phone' => '0771234567', 'support_email' => 'help@nexorait.lk', 'portal_notice' => 'Hello'])->assertSessionHasNoErrors();
        $this->assertSame('Hello', SystemSetting::get('portal_notice'));

        $this->post(route('admin.admins.store'), ['name' => 'Kamal', 'email' => 'kamal@nexorait.lk', 'password' => 'password99', 'password_confirmation' => 'password99'])->assertSessionHasNoErrors();
        $kamal = User::where('email', 'kamal@nexorait.lk')->first();
        $this->patch(route('admin.admins.toggle', $kamal));
        $this->assertFalse($kamal->fresh()->is_active);

        // cannot disable yourself / the last active admin
        $this->patch(route('admin.admins.toggle', $this->admin))->assertSessionHas('error');
        $this->assertTrue($this->admin->fresh()->is_active);

        $this->put(route('admin.password'), ['current_password' => 'secret123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])->assertSessionHasNoErrors();
    }

    public function test_hub_admin_command_creates_admin(): void
    {
        $this->artisan('hub:admin', ['email' => 'New@Nexorait.lk', '--name' => 'New', '--password' => 'longpassword'])->assertSuccessful();
        $this->assertTrue(User::where('email', 'new@nexorait.lk')->exists());

        $this->artisan('hub:admin', ['email' => 'bad', '--password' => 'short'])->assertFailed();
    }
}
