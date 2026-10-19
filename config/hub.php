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

    /*
    | Firebase web push for the parent portal (Firebase console > Project settings >
    | General > Your apps > Web app, and Cloud Messaging > Web Push certificates).
    | Use the SAME Firebase project as the parent mobile app and the institutes.
    | Push is switched off until all of these are set.
    */
    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
        'sdk_version' => env('FIREBASE_JS_SDK_VERSION', '10.12.2'),
    ],

];
