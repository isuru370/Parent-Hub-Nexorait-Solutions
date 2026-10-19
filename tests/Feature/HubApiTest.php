<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use App\Models\Institute;
use App\Models\StudentRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HubApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.parent_hub.secret' => 'hub-secret']);
    }

    private function registerInstitute(string $code, string $url): void
    {
        $this->postJson('/api/v1/register-institute', [
            'secret_key' => 'hub-secret',
            'institute_code' => $code,
            'institute_name' => "Institute {$code}",
            'api_url' => $url,
        ])->assertOk();
    }

    public function test_wrong_or_missing_secret_is_rejected(): void
    {
        $this->postJson('/api/v1/register-institute', ['secret_key' => 'nope', 'institute_code' => 'A', 'institute_name' => 'A', 'api_url' => 'https://a.lk'])
            ->assertUnauthorized();

        config(['services.parent_hub.secret' => null]);
        $this->postJson('/api/v1/register-institute', ['secret_key' => '', 'institute_code' => 'A', 'institute_name' => 'A', 'api_url' => 'https://a.lk'])
            ->assertUnauthorized();
    }

    public function test_student_of_unknown_institute_is_rejected(): void
    {
        $this->postJson('/api/v1/register-student', [
            'secret_key' => 'hub-secret', 'username' => 'SA1', 'student_custom_id' => 'SA1', 'institute_code' => 'NOPE',
        ])->assertStatus(422)->assertJsonValidationErrors('institute_code');
    }

    public function test_same_username_in_two_institutes_keeps_both(): void
    {
        $this->registerInstitute('AAA', 'https://a.lk');
        $this->registerInstitute('BBB', 'https://b.lk');

        foreach (['AAA', 'BBB'] as $code) {
            $this->postJson('/api/v1/register-student', [
                'secret_key' => 'hub-secret', 'username' => 'SA10001', 'student_custom_id' => 'SA10001', 'institute_code' => $code,
            ])->assertOk();
        }

        // Re-sending the same student updates, never duplicates
        $this->postJson('/api/v1/register-student', [
            'secret_key' => 'hub-secret', 'username' => 'SA10001', 'student_custom_id' => 'SA10001', 'institute_code' => 'AAA', 'status' => false,
        ])->assertOk();

        $this->assertSame(2, StudentRegistry::count());
        $this->assertFalse(StudentRegistry::where('institute_code', 'AAA')->first()->is_active);
        $this->assertTrue(StudentRegistry::where('institute_code', 'BBB')->first()->is_active);
    }

    public function test_login_finds_the_right_institute_and_records_last_login(): void
    {
        $this->registerInstitute('AAA', 'https://a.lk');
        $this->registerInstitute('BBB', 'https://b.lk');
        StudentRegistry::create(['username' => 'SA10001', 'student_custom_id' => 'SA10001', 'institute_code' => 'AAA', 'is_active' => true]);
        StudentRegistry::create(['username' => 'SA10001', 'student_custom_id' => 'SA10001', 'institute_code' => 'BBB', 'is_active' => true]);

        Http::fake([
            'a.lk/*' => Http::response(['status' => false, 'message' => 'Invalid username or password'], 401),
            'b.lk/*' => Http::response($this->fixture('auth_login'), 200),
        ]);

        $this->postJson('/api/v1/login', ['username' => 'SA10001', 'password' => 'pass1234'])
            ->assertOk()
            ->assertJson(['status' => true, 'api_url' => 'https://b.lk', 'institute' => ['code' => 'BBB']])
            ->assertJsonPath('data.student_id', 567);

        $this->assertNotNull(StudentRegistry::where('institute_code', 'BBB')->first()->last_login_at);
        $this->assertNull(StudentRegistry::where('institute_code', 'AAA')->first()->last_login_at);
    }

    public function test_login_fails_for_blocked_student_disabled_institute_and_unreachable_institute(): void
    {
        $this->registerInstitute('AAA', 'https://a.lk');
        StudentRegistry::create(['username' => 'S1', 'student_custom_id' => 'S1', 'institute_code' => 'AAA', 'is_active' => false]);
        Http::fake(['a.lk/*' => Http::response($this->fixture('auth_login'))]);

        $this->postJson('/api/v1/login', ['username' => 'S1', 'password' => 'x'])->assertUnauthorized();

        StudentRegistry::query()->update(['is_active' => true]);
        Institute::query()->update(['status' => false]);
        $this->postJson('/api/v1/login', ['username' => 'S1', 'password' => 'x'])->assertUnauthorized();

        Institute::query()->update(['status' => true]);
        Http::fake(['a.lk/*' => Http::failedConnection()]);
        $this->postJson('/api/v1/login', ['username' => 'S1', 'password' => 'x'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Your institute could not be reached. Please try again in a few minutes.');
    }

    public function test_app_version_check(): void
    {
        AppVersion::create(['platform' => 'android', 'version' => '1.2.0', 'force_update' => true, 'status' => true]);
        AppVersion::create(['platform' => 'android', 'version' => '1.10.0', 'force_update' => false, 'status' => true]);
        AppVersion::create(['platform' => 'android', 'version' => '2.0.0', 'force_update' => true, 'status' => false]);

        // 1.10.0 is newer than 1.2.0 (compared as versions, not text); hidden 2.0.0 ignored
        $this->getJson('/api/v1/app-version?platform=android&current=1.1.0')
            ->assertOk()
            ->assertJsonPath('data.latest_version', '1.10.0')
            ->assertJsonPath('data.update_available', true)
            ->assertJsonPath('data.force_update', true);

        $this->getJson('/api/v1/app-version?platform=android&current=1.5.0')
            ->assertJsonPath('data.update_available', true)
            ->assertJsonPath('data.force_update', false);

        $this->getJson('/api/v1/app-version?platform=android&current=1.10.0')
            ->assertJsonPath('data.update_available', false);

        $this->getJson('/api/v1/app-version?platform=ios')->assertJsonPath('data.latest_version', null);
        $this->getJson('/api/v1/app-version?platform=windows')->assertStatus(422);
    }
}
