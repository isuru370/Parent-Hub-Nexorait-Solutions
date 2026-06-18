<?php

namespace App\Services;

use App\Models\Institute;

class InstituteService
{
    public function register(array $data): Institute
    {
        return Institute::updateOrCreate(

            [
                'code' => $data['institute_code'],
            ],

            [
                'name' => $data['institute_name'],

                'api_url' => $data['api_url'],

                'contact_number' =>
                $data['contact_mobile'] ?? null,

                'contact_email' =>
                $data['contact_email'] ?? null,

                'app_version' =>
                $data['app_version'] ?? null,

                'last_ping_at' => now(),

                'status' => true,
            ]
        );
    }
}
