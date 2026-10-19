<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * php artisan hub:admin admin@nexorait.lk --name="Isuru"
 * (asks for the password; or pass --password=...)
 */
class CreateHubAdmin extends Command
{
    protected $signature = 'hub:admin
        {email : Admin email (used to log in)}
        {--name= : Display name}
        {--password= : Password (asked if not given)}';

    protected $description = 'Create a Parent Hub admin, or reset an existing admin\'s password';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $password = (string) ($this->option('password') ?: $this->secret('Password (min 8 characters)'));
        $name = (string) ($this->option('name') ?: User::query()->where('email', $email)->value('name') ?: 'Hub Admin');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', 'min:8']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'is_active' => true]
        );

        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated') . " admin {$email}. Log in at " . url('/login'));

        return self::SUCCESS;
    }
}
