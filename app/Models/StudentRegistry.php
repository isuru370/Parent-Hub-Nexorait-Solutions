<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
