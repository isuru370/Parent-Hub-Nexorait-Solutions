<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Which institute a portal username belongs to.
 * The password is NOT stored here - it is checked by the institute.
 */
class StudentRegistry extends Model
{
    protected $fillable = [
        'username',
        'student_custom_id',
        'institute_code',
        'is_active',
        'last_login_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_code', 'code');
    }
}
