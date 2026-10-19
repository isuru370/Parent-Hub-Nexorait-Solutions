<?php

namespace App\Services;

use App\Models\StudentRegistry;
use App\Services\Clients\InstituteAuthClient;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

/**
 * Student / parent login: find the student's institute(s) in the registry
 * and let the institute check the password.
 */
class AuthService
{
    public function __construct(
        private InstituteAuthClient $client
    ) {}

    /**
     * @return array{status: bool, message: string, api_url?: string, institute?: array, data?: array}
     */
    public function studentLogin(string $username, string $password): array
    {
        $username = trim($username);

        // The same username may exist in more than one institute
        $registrations = StudentRegistry::query()
            ->with('institute')
            ->where('username', $username)
            ->where('is_active', true)
            ->get()
            ->filter(fn (StudentRegistry $row) => $row->institute && $row->institute->status)
            ->values();

        if ($registrations->isEmpty()) {
            return [
                'status' => false,
                'message' => 'Invalid username or password',
            ];
        }

        $unreachable = false;
        $lastMessage = 'Invalid username or password';

        foreach ($registrations as $registration) {
            $institute = $registration->institute;

            try {
                $response = $this->client->login($institute->api_url, $username, $password);
                $data = $response->json() ?? [];

                if (!$response->successful() || ($data['status'] ?? true) === false) {
                    $lastMessage = $data['message'] ?? $lastMessage;

                    continue;
                }

                $registration->forceFill(['last_login_at' => now()])->save();

                return [
                    'status' => true,
                    'message' => $data['message'] ?? 'Login successful',
                    'api_url' => $institute->api_url,
                    'institute' => [
                        'code' => $institute->code,
                        'name' => $institute->name,
                        'logo' => $institute->logo,
                    ],
                    'data' => $data['data'] ?? [],
                ];
            } catch (ConnectionException $e) {
                report($e);
                $unreachable = true;
            } catch (Throwable $e) {
                report($e);
                $unreachable = true;
            }
        }

        return [
            'status' => false,
            'message' => $unreachable && $registrations->count() === 1
                ? 'Your institute could not be reached. Please try again in a few minutes.'
                : $lastMessage,
        ];
    }
}
