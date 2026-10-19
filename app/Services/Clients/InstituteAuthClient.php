<?php

namespace App\Services\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Calls an institute's parent API: {api_url}/api/parent/v1/...
 */
class InstituteAuthClient
{
    public const PREFIX = '/api/parent/v1';

    public function login(
        string $baseUrl,
        string $username,
        string $password
    ): Response {
        return $this->post($baseUrl, '/auth/login', [
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * POST {api_url}/api/parent/v1{path}
     */
    public function post(string $baseUrl, string $path, array $data = [], int $timeout = 10): Response
    {
        return Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post(rtrim($baseUrl, '/') . self::PREFIX . '/' . ltrim($path, '/'), $data);
    }
}
