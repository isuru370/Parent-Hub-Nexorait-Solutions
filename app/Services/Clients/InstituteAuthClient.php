<?php

namespace App\Services\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class InstituteAuthClient
{
    public function login(
        string $baseUrl,
        string $username,
        string $password
    ): Response {
        return Http::timeout(10)
            ->acceptJson()
            ->asJson()
            ->post(
                rtrim($baseUrl, '/') . '/api/parent/v1/auth/login',
                [
                    'username' => $username,
                    'password' => $password,
                ]
            );
    }
}