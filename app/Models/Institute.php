<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
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
}
