<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Institute\RegisterInstituteRequest;
use App\Services\InstituteService;

class InstituteController extends Controller
{
    public function __construct(
        private InstituteService $instituteService
    ) {}

    public function register(
        RegisterInstituteRequest $request
    ) {

        if (
            $request->secret_key !==
            config('services.parent_hub.secret')
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $institute =
            $this->instituteService
                ->register(
                    $request->validated()
                );

        return response()->json([

            'success' => true,

            'message' =>
                'Institute registered successfully',

            'data' => [

                'id' => $institute->id,

                'name' => $institute->name,

                'code' => $institute->code,

            ]

        ]);
    }
}