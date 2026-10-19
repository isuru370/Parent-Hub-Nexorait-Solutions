<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * First admin from HUB_ADMIN_EMAIL / HUB_ADMIN_PASSWORD (.env).
     * Existing admins are not changed.
     */
    public function run(): void
    {
        $email = config('hub.admin.email');
        $password = config('hub.admin.password');

        if (!$email || !$password) {
            $this->command?->warn('HUB_ADMIN_EMAIL / HUB_ADMIN_PASSWORD not set - no admin created. Use: php artisan hub:admin you@example.com');

            return;
        }

        $user = User::query()->firstOrCreate(
            ['email' => strtolower($email)],
            ['name' => config('hub.admin.name'), 'password' => $password, 'is_active' => true]
        );

        $this->command?->info(($user->wasRecentlyCreated ? 'Created' : 'Already exists:') . " admin {$user->email}");
    }
}
