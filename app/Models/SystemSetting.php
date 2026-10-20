<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Simple key / value settings edited in Admin > Settings.
 */
class SystemSetting extends Model
{
    /** Keys shown in the admin, with their labels. */
    public const KEYS = [
        'support_phone' => 'Support phone',
        'support_email' => 'Support email',
        'portal_notice' => 'Notice shown on the student portal',
        'android_app_url' => 'Android app link (Google Play)',
        'ios_app_url' => 'iPhone app link (App Store)',
    ];

    protected const CACHE_KEY = 'system_settings.v1';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(static::CACHE_KEY));
        static::deleted(fn () => Cache::forget(static::CACHE_KEY));
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $all = Cache::rememberForever(
                static::CACHE_KEY,
                fn () => static::query()->pluck('value', 'key')->all()
            );
        } catch (Throwable) {
            return $default;
        }

        $value = $all[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::CACHE_KEY);
    }
}
