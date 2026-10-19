<?php

namespace App\Support;

use Carbon\Carbon;
use Throwable;

/**
 * Small display helpers for values that come from the institute API.
 * Dates arrive as "2026-10-19" or as UTC ISO strings
 * ("2026-10-18T18:30:00.000000Z" = 19 Oct in Sri Lanka).
 */
class Fmt
{
    public static function carbon(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            $date = Carbon::parse((string) $value);

            return str_contains((string) $value, 'T')
                ? $date->setTimezone(config('app.timezone'))
                : $date;
        } catch (Throwable) {
            return null;
        }
    }

    public static function date(mixed $value, string $format = 'd M Y'): string
    {
        return self::carbon($value)?->format($format) ?? '-';
    }

    /** "15:00" -> "3:00 PM" */
    public static function time(mixed $value): string
    {
        if (!$value) {
            return '-';
        }

        try {
            return Carbon::parse((string) $value)->format('g:i A');
        } catch (Throwable) {
            return (string) $value;
        }
    }

    /** "2026-10" -> "Oct 2026" */
    public static function month(mixed $value, string $format = 'M Y'): string
    {
        try {
            return Carbon::createFromFormat('Y-m-d', substr((string) $value, 0, 7) . '-01')->format($format);
        } catch (Throwable) {
            return (string) $value;
        }
    }

    public static function money(mixed $value): string
    {
        return 'Rs. ' . number_format((float) $value, 2);
    }

    public static function number(mixed $value): string
    {
        $n = (float) $value;

        return floor($n) == $n ? number_format($n) : rtrim(rtrim(number_format($n, 2), '0'), '.');
    }

    public static function initials(?string $name): string
    {
        $parts = preg_split('/[\s.]+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return strtoupper(implode('', array_map(fn ($p) => mb_substr($p, 0, 1), count($parts) > 1 ? [$parts[0], end($parts)] : $parts))) ?: '?';
    }
}
