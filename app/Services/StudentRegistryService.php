<?php

namespace App\Services;

use App\Models\StudentRegistry;

class StudentRegistryService
{
    public function register(
        array $data
    ): StudentRegistry {

        return StudentRegistry::updateOrCreate(

            [
                'username' =>
                    $data['username'],
            ],

            [
                'student_custom_id' =>
                    $data['student_custom_id'],

                'institute_code' =>
                    $data['institute_code'],

                'is_active' =>
                    $data['status'] ?? true,
            ]
        );
    }
}