<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StudentLoginDetailsRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class StudentLoginDetailsController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function studentLoginDetails(
        StudentLoginDetailsRequest $request
    ): JsonResponse {
        $result = $this->authService->studentLogin(
            $request->username,
            $request->password
        );

        return response()->json(
            $result,
            $result['status'] ? 200 : 401
        );
    }
}