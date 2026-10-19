<?php

return [

    /*
    | First admin, created by `php artisan db:seed` (or `php artisan hub:admin`).
    */
    'admin' => [
        'name' => env('HUB_ADMIN_NAME', 'Hub Admin'),
        'email' => env('HUB_ADMIN_EMAIL'),
        'password' => env('HUB_ADMIN_PASSWORD'),
    ],

    /*
    | Login attempts per minute for one username + IP.
    */
    'login_attempts_per_minute' => (int) env('HUB_LOGIN_ATTEMPTS', 5),

];
