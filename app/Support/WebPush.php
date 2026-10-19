<?php

namespace App\Support;

/**
 * Firebase web push settings for the parent portal (config/hub.php -> firebase).
 */
class WebPush
{
    protected const REQUIRED = ['api_key', 'project_id', 'messaging_sender_id', 'app_id', 'vapid_key'];

    public static function enabled(): bool
    {
        foreach (self::REQUIRED as $key) {
            if (blank(config("hub.firebase.$key"))) {
                return false;
            }
        }

        return true;
    }

    /** The object passed to firebase.initializeApp() in the browser. */
    public static function clientConfig(): array
    {
        return array_filter([
            'apiKey' => config('hub.firebase.api_key'),
            'authDomain' => config('hub.firebase.auth_domain'),
            'projectId' => config('hub.firebase.project_id'),
            'storageBucket' => config('hub.firebase.storage_bucket'),
            'messagingSenderId' => config('hub.firebase.messaging_sender_id'),
            'appId' => config('hub.firebase.app_id'),
        ]);
    }

    public static function vapidKey(): ?string
    {
        return config('hub.firebase.vapid_key');
    }

    /** e.g. sdk('firebase-messaging-compat.js') */
    public static function sdk(string $file): string
    {
        $version = preg_replace('/[^0-9.]/', '', (string) config('hub.firebase.sdk_version', '10.12.2')) ?: '10.12.2';

        return "https://www.gstatic.com/firebasejs/{$version}/{$file}";
    }

    /** "Chrome on Windows" from a User-Agent header. */
    public static function deviceName(?string $userAgent): string
    {
        $ua = (string) $userAgent;

        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'SamsungBrowser') => 'Samsung Internet',
            str_contains($ua, 'Firefox/') || str_contains($ua, 'FxiOS') => 'Firefox',
            str_contains($ua, 'Chrome/') || str_contains($ua, 'CriOS') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Browser',
        };

        $os = match (true) {
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'CrOS') => 'Chromebook',
            str_contains($ua, 'Linux') => 'Linux',
            default => null,
        };

        return $os ? "$browser on $os" : $browser;
    }
}
