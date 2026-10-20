<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\StudentRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * NexoraEDU institutes now return a parent API token at login and refuse
 * parent API calls without "Authorization: Bearer <token>".
 */
class InstituteTokenTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = '12|PlainParentApiTokenFromInstitute0123456789';

    protected function setUp(): void
    {
        parent::setUp();

        Institute::create(['name' => 'Savidya Institute', 'code' => 'SAV', 'api_url' => 'https://sav.lk', 'status' => true]);
        StudentRegistry::create(['username' => 'S1', 'student_custom_id' => 'S1', 'institute_code' => 'SAV', 'is_active' => true]);
    }

    /** An institute that requires the token, like NexoraEDU with PARENT_API_REQUIRE_TOKEN=true. */
    private function fakeSecureInstitute(bool $expired = false): void
    {
        Http::fake(function (Request $request) use ($expired) {
            $path = str_replace('https://sav.lk/api/parent/v1/', '', $request->url());

            if ($path === 'auth/login') {
                return Http::response($this->fixture('auth_login_token'));
            }

            if ($expired || $request->header('Authorization') !== ['Bearer ' . self::TOKEN]) {
                return Http::response(['status' => false, 'message' => 'Please log in again.'], 401);
            }

            return match ($path) {
                'dashboard' => Http::response($this->fixture('dashboard')),
                'payments' => Http::response($this->fixture('payments')),
                'auth/logout' => Http::response(['status' => true, 'message' => 'Logout successful.']),
                default => Http::response(['status' => false, 'message' => 'Not found'], 404),
            };
        });
    }

    public function test_portal_sends_the_token_on_every_call_and_never_shows_it(): void
    {
        $this->fakeSecureInstitute();

        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234'])->assertRedirect(route('portal.dashboard'));

        $this->get(route('portal.dashboard'))->assertOk()->assertSee('A. Silva')->assertDontSee(self::TOKEN);
        $this->get(route('portal.payments'))->assertOk()->assertDontSee('Please log in again');
        $this->get(route('portal.profile'))->assertOk()->assertDontSee(self::TOKEN);

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/payments')
            && $r->header('Authorization') === ['Bearer ' . self::TOKEN]);

        // Logging out revokes the token at the institute.
        $this->post(route('logout'))->assertRedirect(route('login'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/logout')
            && $r->header('Authorization') === ['Bearer ' . self::TOKEN]);
    }

    public function test_a_token_the_institute_no_longer_accepts_asks_to_log_in_again(): void
    {
        $this->fakeSecureInstitute(expired: true);

        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234']);

        $this->get(route('portal.dashboard'))->assertOk()->assertSee('Your login has expired. Please log out and log in again.');
    }

    public function test_hub_api_login_gives_the_app_the_token(): void
    {
        $this->fakeSecureInstitute();

        $this->postJson('/api/v1/login', ['username' => 'S1', 'password' => 'pass1234'])
            ->assertOk()
            ->assertJsonPath('token', self::TOKEN)
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('data.token', self::TOKEN)
            ->assertJsonPath('api_url', 'https://sav.lk');
    }

    public function test_older_institutes_without_tokens_still_work(): void
    {
        Http::fake(function (Request $request) {
            $path = str_replace('https://sav.lk/api/parent/v1/', '', $request->url());

            return match ($path) {
                'auth/login' => Http::response($this->fixture('auth_login')),
                'dashboard' => Http::response($this->fixture('dashboard')),
                default => Http::response(['status' => true]),
            };
        });

        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234']);
        $this->get(route('portal.dashboard'))->assertOk()->assertSee('A. Silva');

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/dashboard') && !$r->hasHeader('Authorization'));
        $this->postJson('/api/v1/login', ['username' => 'S1', 'password' => 'pass1234'])->assertJsonPath('token', null);
    }
}
