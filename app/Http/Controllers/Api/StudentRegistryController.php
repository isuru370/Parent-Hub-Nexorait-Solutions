<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegistry\RegisterStudentRequest;
use App\Services\StudentRegistryService;

class StudentRegistryController extends Controller
{
    public function __construct(
        private StudentRegistryService $studentRegistryService
    ) {}

    public function register(
        RegisterStudentRequest $request
    ) {

        // secret_key is checked by the hub.secret middleware

        $student =
            $this->studentRegistryService
                ->register(
                    $request->validated()
                );

        return response()->json([

            'success' => true,

            'message' =>
                'Student registered successfully',

            'data' => [

                'id' =>
                    $student->id,

                'username' =>
                    $student->username,

                'student_custom_id' =>
                    $student->student_custom_id,

                'institute_code' =>
                    $student->institute_code,

            ]

        ]);
    }
}