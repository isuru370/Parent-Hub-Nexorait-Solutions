<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->get('/')->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee('How it works')
            ->assertDontSee('Google Play');
        $this->get('/login')->assertOk()->assertSee('Student ID or email');
        $this->get('/privacy-policy')->assertOk()->assertSee('Firebase Cloud Messaging');
    }

    public function test_home_page_shows_app_links_and_support_from_settings(): void
    {
        SystemSetting::put('android_app_url', 'https://play.google.com/store/apps/details?id=lk.nexorait.parent');
        SystemSetting::put('support_phone', '077 123 4567');

        $this->get('/')->assertOk()
            ->assertSee('https://play.google.com/store/apps/details?id=lk.nexorait.parent', false)
            ->assertSee('Google Play')
            ->assertSee('tel:0771234567', false);
    }

    public function test_home_page_links_signed_in_admin_to_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee('Admin dashboard');
    }

    public function test_admin_can_save_app_links(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), ['android_app_url' => 'http://not-https.example'])
            ->assertSessionHasErrors('android_app_url');

        $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), ['ios_app_url' => 'https://apps.apple.com/app/id123'])
            ->assertSessionHasNoErrors();

        $this->assertSame('https://apps.apple.com/app/id123', SystemSetting::get('ios_app_url'));
    }
}
