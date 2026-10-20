<?php

namespace App\Services\Portal;

use App\Services\Clients\InstituteAuthClient;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Reads the logged-in student's data from their institute.
 *
 * The student_id always comes from the session, never from the browser,
 * so a parent can only see their own child.
 */
class PortalApi
{
    public function __construct(
        private PortalSession $portal,
        private InstituteAuthClient $client
    ) {}

    public function dashboard(): array
    {
        return $this->data('/dashboard');
    }

    public function attendance(): array
    {
        return $this->data('/attendance');
    }

    public function payments(): array
    {
        return $this->data('/payments');
    }

    public function exams(): array
    {
        return $this->data('/exams');
    }

    public function result(int $examId): array
    {
        return $this->data('/results', ['exam_id' => $examId]);
    }

    public function schedule(): array
    {
        return $this->data('/schedule');
    }

    public function notifications(int $page = 1): array
    {
        return $this->data('/notifications', ['page' => $page]);
    }

    public function markAllNotificationsRead(): array
    {
        return $this->call('/notifications/read-all');
    }

    /**
     * @return array{status: bool, message: string}
     */
    public function changePassword(string $current, string $new, string $confirmation): array
    {
        $response = $this->call('/auth/change-password', [
            'current_password' => $current,
            'new_password' => $new,
            'new_password_confirmation' => $confirmation,
        ]);

        return [
            'status' => $this->ok($response),
            'message' => (string) ($response['message'] ?? 'Could not change the password.'),
        ];
    }

    /**
     * Save this browser's FCM token in the institute's fcm_tokens table.
     *
     * Institutes that have not been updated yet only accept android / ios,
     * so a rejected "web" is retried as "android" (FCM delivers the same way).
     *
     * @return array{status: bool, message: string, device_type: ?string}
     */
    public function saveFcmToken(string $token, string $deviceId, ?string $deviceName): array
    {
        $payload = [
            'token' => $token,
            'device_id' => $deviceId,
            'device_name' => $deviceName ? mb_substr($deviceName, 0, 255) : null,
            'app_version' => 'web',
        ];

        foreach (['web', 'android'] as $deviceType) {
            $response = $this->call('/fcm/token', $payload + ['device_type' => $deviceType]);

            if ($this->ok($response)) {
                return ['status' => true, 'message' => 'Notifications are on for this device.', 'device_type' => $deviceType];
            }

            $rejectedType = ($response['_http_status'] ?? 0) === 422
                && isset($response['errors']['device_type']);

            if (!$rejectedType) {
                break;
            }
        }

        Log::warning('Institute did not save the web FCM token', [
            'student_id' => $this->portal->studentId(),
            'status' => $response['_http_status'] ?? null,
            'message' => $response['message'] ?? null,
        ]);

        return [
            'status' => false,
            'message' => (string) ($response['message'] ?? 'The institute could not turn on notifications.'),
            'device_type' => null,
        ];
    }

    /**
     * Log out at the institute: revokes the parent API token and turns off
     * this browser's notifications. Never throws.
     */
    public function logout(?array $pushDevice): void
    {
        if (!$this->portal->token()) {
            if ($pushDevice) {
                $this->removeFcmToken($pushDevice);
            }

            return;
        }

        try {
            $this->call('/auth/logout', ['device_id' => $pushDevice['device_id'] ?? null], 5);
        } catch (Throwable $e) {
            Log::info('Could not log out at the institute', ['error' => $e->getMessage()]);
        }
    }

    /** Mark this browser's token inactive at the institute (on log out). Never throws. */
    public function removeFcmToken(array $device): void
    {
        try {
            $this->call('/fcm/logout', [
                'token' => $device['token'],
                'device_id' => $device['device_id'],
                'device_type' => $device['device_type'] ?? 'web',
            ], 5);
        } catch (Throwable $e) {
            Log::info('Could not remove the web FCM token at log out', ['error' => $e->getMessage()]);
        }
    }

    /** The "data" part of a successful response. */
    protected function data(string $path, array $extra = []): array
    {
        $response = $this->call($path, $extra);

        if (!$this->ok($response)) {
            throw new RuntimeException((string) ($response['message'] ?? 'The institute returned an error.'));
        }

        $data = $response['data'] ?? [];

        return is_array($data) ? $data : [];
    }

    protected function call(string $path, array $extra = [], int $timeout = 15): array
    {
        $apiUrl = $this->portal->apiUrl();
        $studentId = $this->portal->studentId();

        if (!$apiUrl || !$studentId) {
            throw new RuntimeException('Please log in again.');
        }

        try {
            $response = $this->client->post($apiUrl, $path, ['student_id' => $studentId] + $extra, $timeout, $this->portal->token());
        } catch (Throwable $e) {
            Log::warning('Institute API unreachable', ['path' => $path, 'error' => $e->getMessage()]);

            throw new RuntimeException('Your institute could not be reached. Please try again in a few minutes.');
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('The institute returned an unexpected response.');
        }

        if ($response->status() === 422 && isset($json['errors'])) {
            $json['message'] = collect($json['errors'])->flatten()->first() ?? ($json['message'] ?? null);
        }

        $json['_http_status'] = $response->status();

        // The institute no longer accepts this login's token (expired, password changed, ...)
        if ($response->status() === 401 && $this->portal->token()) {
            throw new RuntimeException('Your login has expired. Please log out and log in again.');
        }

        return $json;
    }

    /** Institute APIs use either "status" or "success". */
    protected function ok(array $response): bool
    {
        if (($response['_http_status'] ?? 200) >= 400) {
            return false;
        }

        return (bool) ($response['status'] ?? $response['success'] ?? false);
    }
}
