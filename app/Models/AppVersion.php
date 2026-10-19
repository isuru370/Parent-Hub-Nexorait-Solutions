<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Parent app releases. The newest active row per platform is the latest
 * version; force_update = old versions must update before use.
 */
class AppVersion extends Model
{
    public const PLATFORMS = ['android', 'ios'];

    protected $fillable = [
        'platform',
        'version',
        'force_update',
        'status',
    ];

    protected $casts = [
        'force_update' => 'boolean',
        'status' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /** Latest active release for a platform (by version number, not date). */
    public static function latestFor(string $platform): ?self
    {
        return static::query()
            ->active()
            ->where('platform', $platform)
            ->get()
            ->sort(fn (self $a, self $b) => version_compare($b->version, $a->version))
            ->first();
    }
}
