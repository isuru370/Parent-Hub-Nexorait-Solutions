<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\StudentRegistry;
use App\Support\WebPush;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebPushTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'fcm-web-token-abc123:APA91bHxyz';

    protected function setUp(): void
    {
        parent::setUp();

        Institute::create(['name' => 'Savidya Institute', 'code' => 'SAV', 'api_url' => 'https://sav.lk', 'status' => true]);
        StudentRegistry::create(['username' => 'S1', 'student_custom_id' => 'S1', 'institute_code' => 'SAV', 'is_active' => true]);
    }

    private function enableFirebase(): void
    {
        config(['hub.firebase' => [
            'api_key' => 'AIzaTestKey',
            'auth_domain' => 'nexora-parent.firebaseapp.com',
            'project_id' => 'nexora-parent',
            'storage_bucket' => null,
            'messaging_sender_id' => '1234567890',
            'app_id' => '1:1234567890:web:abc',
            'vapid_key' => 'BTestVapidKey',
            'sdk_version' => '10.12.2',
        ]]);
    }

    /**
     * @param  callable|null  $fcm  (Request) => response for fcm/* paths
     */
    private function fakeInstitute(?callable $fcm = null): void
    {
        Http::fake(function (Request $request) use ($fcm) {
            $path = str_replace('https://sav.lk/api/parent/v1/', '', $request->url());

            return match (true) {
                $path === 'auth/login' => Http::response($this->fixture('auth_login')),
                $path === 'dashboard' => Http::response($this->fixture('dashboard')),
                str_starts_with($path, 'fcm/') => $fcm
                    ? $fcm($request)
                    : Http::response(['status' => true, 'message' => 'FCM token saved successfully.']),
                default => Http::response(['status' => false, 'message' => 'Not found'], 404),
            };
        });
    }

    private function login(): void
    {
        $this->post('/login', ['login' => 'S1', 'password' => 'pass1234'])->assertRedirect(route('portal.dashboard'));
    }

    public function test_push_is_off_until_firebase_is_configured(): void
    {
        $this->fakeInstitute();
        $this->login();

        $this->assertFalse(WebPush::enabled());
        $this->get(route('portal.dashboard'))->assertOk()->assertDontSee('firebase-messaging')->assertDontSee('pushPrompt');
        $this->get('/firebase-messaging-sw.js')->assertNotFound();
        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN])->assertNotFound();
    }

    public function test_service_worker_is_built_from_config(): void
    {
        $this->enableFirebase();

        $this->get('/firebase-messaging-sw.js')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->assertSee('"messagingSenderId":"1234567890"', false)
            ->assertSee('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js', false)
            ->assertSee(route('portal.notifications'), false)
            ->assertDontSee('BTestVapidKey');
    }

    public function test_portal_pages_load_the_push_script(): void
    {
        $this->enableFirebase();
        $this->fakeInstitute();
        $this->login();

        $this->get(route('portal.dashboard'))->assertOk()
            ->assertSee('id="pushPrompt"', false)
            ->assertSee('BTestVapidKey')
            ->assertSee(str_replace('/', '\\/', route('portal.push-token')), false);

        $this->get(route('portal.profile'))->assertOk()
            ->assertSee('Notifications on this device')
            ->assertDontSee('id="pushPrompt"', false);
    }

    public function test_token_is_saved_in_the_students_institute(): void
    {
        $this->enableFirebase();
        $this->fakeInstitute();
        $this->login();

        $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0 Safari/537.36')
            ->postJson(route('portal.push-token'), ['token' => self::TOKEN, 'device_id' => 'web-1b2c3d'])
            ->assertOk()
            ->assertJson(['status' => true]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://sav.lk/api/parent/v1/fcm/token'
            && $r['student_id'] === 567
            && $r['token'] === self::TOKEN
            && $r['device_id'] === 'web-1b2c3d'
            && $r['device_type'] === 'web'
            && $r['device_name'] === 'Chrome on Windows'
            && $r['app_version'] === 'web');

        // The same token again in this login is not sent twice.
        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN, 'device_id' => 'web-1b2c3d'])->assertOk();
        $this->assertCount(1, Http::recorded(fn (Request $r) => str_ends_with($r->url(), '/fcm/token')));

        // Logging out marks the token inactive at the institute.
        $this->post(route('logout'))->assertRedirect(route('login'));
        Http::assertSent(fn (Request $r) => $r->url() === 'https://sav.lk/api/parent/v1/fcm/logout'
            && $r['student_id'] === 567
            && $r['token'] === self::TOKEN
            && $r['device_id'] === 'web-1b2c3d');
    }

    public function test_institute_without_web_support_gets_the_token_as_android(): void
    {
        $this->enableFirebase();
        $this->fakeInstitute(fn (Request $r) => $r['device_type'] === 'web'
            ? Http::response(['message' => 'The selected device type is invalid.', 'errors' => ['device_type' => ['The selected device type is invalid.']]], 422)
            : Http::response(['status' => true, 'message' => 'FCM token saved successfully.']));
        $this->login();

        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN])->assertOk();

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/fcm/token') && $r['device_type'] === 'android'
            && str_starts_with($r['device_id'], 'web-'));
    }

    public function test_institute_errors_are_reported_to_the_page(): void
    {
        $this->enableFirebase();
        $this->fakeInstitute(fn () => Http::response(['status' => false, 'message' => 'Student not found.'], 404));
        $this->login();

        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN])
            ->assertStatus(502)
            ->assertJson(['status' => false, 'message' => 'Student not found.']);

        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN, 'device_id' => 'bad id <script>'])
            ->assertStatus(422);
    }

    public function test_guests_cannot_send_tokens(): void
    {
        $this->enableFirebase();

        $this->postJson(route('portal.push-token'), ['token' => self::TOKEN])->assertStatus(401);
    }

    public function test_device_names(): void
    {
        $this->assertSame('Chrome on Android', WebPush::deviceName('Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/129.0 Mobile Safari/537.36'));
        $this->assertSame('Edge on Windows', WebPush::deviceName('Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 Chrome/129.0 Safari/537.36 Edg/129.0'));
        $this->assertSame('Safari on iPhone', WebPush::deviceName('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Version/17.0 Mobile/15E148 Safari/604.1'));
        $this->assertSame('Browser', WebPush::deviceName(null));
    }
}
