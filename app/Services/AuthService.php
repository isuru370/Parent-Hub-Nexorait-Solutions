<?php

namespace App\Services;

use App\Models\Institute;
use App\Models\StudentRegistry;
use App\Services\Clients\InstituteAuthClient;
use Illuminate\Http\Client\ConnectionException;

class AuthService
{
    public function __construct(
        private InstituteAuthClient $client
    ) {}

    public function studentLogin(
        string $username,
        string $password
    ): array {
        $student = StudentRegistry::query()
            ->where('username', $username)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return [
                'status' => false,
                'message' => 'Invalid student or inactive account',
            ];
        }

        $institute = Institute::query()
            ->where('code', $student->institute_code)
            ->where('status', true)
            ->first();

        if (!$institute) {
            return [
                'status' => false,
                'message' => 'Institute not found or inactive',
            ];
        }

        try {

            $response = $this->client->login(
                $institute->api_url,
                $username,
                $password
            );

            $data = $response->json();

            if (!$response->successful()) {
                return [
                    'status' => false,
                    'message' => $data['message'] ?? 'Authentication failed',
                ];
            }

            return [
                'status' => true,
                'message' => $data['message'] ?? 'Login successful',
                'api_url' => $institute->api_url,
                'data' => $data['data'] ?? [],
            ];
        } catch (ConnectionException $e) {

            report($e);

            return [
                'status' => false,
                'message' => 'Institute service unavailable',
            ];
        } catch (\Throwable $e) {

            report($e);

            return [
                'status' => false,
                'message' => 'Something went wrong',
            ];
        }
    }
}
