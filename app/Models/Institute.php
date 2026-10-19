<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institute extends Model
{
    /** No ping for this long = shown as "offline" in the admin. */
    public const ONLINE_WITHIN_DAYS = 7;

    protected $fillable = [
        'name',
        'code',
        'api_url',
        'logo',
        'contact_number',
        'contact_email',
        'status',
        'last_ping_at',
        'app_version',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_ping_at' => 'datetime',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(StudentRegistry::class, 'institute_code', 'code');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function isRecentlySeen(): bool
    {
        return $this->last_ping_at !== null
            && $this->last_ping_at->gt(now()->subDays(self::ONLINE_WITHIN_DAYS));
    }
}
