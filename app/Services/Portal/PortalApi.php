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

    protected function call(string $path, array $extra = []): array
    {
        $apiUrl = $this->portal->apiUrl();
        $studentId = $this->portal->studentId();

        if (!$apiUrl || !$studentId) {
            throw new RuntimeException('Please log in again.');
        }

        try {
            $response = $this->client->post($apiUrl, $path, ['student_id' => $studentId] + $extra, 15);
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
