<?php

namespace App\Services;

use App\Models\StudentRegistry;

class StudentRegistryService
{
    /**
     * Add or update a student of an institute.
     *
     * The same username can exist in two institutes, so a row is matched by
     * institute + username (or institute + student id when the username
     * changed) - never by username alone.
     */
    public function register(array $data): StudentRegistry
    {
        $instituteCode = $data['institute_code'];

        $student = StudentRegistry::query()
            ->where('institute_code', $instituteCode)
            ->where(function ($query) use ($data) {
                $query->where('username', $data['username'])
                    ->orWhere('student_custom_id', $data['student_custom_id']);
            })
            ->orderByRaw('CASE WHEN username = ? THEN 0 ELSE 1 END', [$data['username']])
            ->first() ?? new StudentRegistry(['institute_code' => $instituteCode]);

        $student->fill([
            'username' => $data['username'],
            'student_custom_id' => $data['student_custom_id'],
            'is_active' => array_key_exists('status', $data) && $data['status'] !== null
                ? (bool) $data['status']
                : true,
        ])->save();

        return $student;
    }
}
