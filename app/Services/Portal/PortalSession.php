<?php

namespace App\Services\Portal;

use Illuminate\Contracts\Session\Session;

/**
 * The logged-in student lives in the session (no local user row):
 * institute api_url + the student data the institute returned at login.
 */
class PortalSession
{
    protected const KEY = 'portal';

    public function __construct(private Session $session)
    {
    }

    public function start(string $username, array $loginResult): void
    {
        $this->session->regenerate();

        $this->session->put(self::KEY, [
            'username' => $username,
            'api_url' => $loginResult['api_url'],
            'institute' => $loginResult['institute'] ?? [],
            'student' => $loginResult['data'] ?? [],
            'logged_in_at' => now()->toIso8601String(),
        ]);
    }

    public function check(): bool
    {
        $portal = $this->session->get(self::KEY);

        return is_array($portal)
            && !empty($portal['api_url'])
            && !empty($portal['student']['student_id']);
    }

    public function studentId(): ?int
    {
        $id = $this->session->get(self::KEY . '.student.student_id');

        return $id ? (int) $id : null;
    }

    public function apiUrl(): ?string
    {
        return $this->session->get(self::KEY . '.api_url');
    }

    public function student(): array
    {
        return (array) $this->session->get(self::KEY . '.student', []);
    }

    public function institute(): array
    {
        return (array) $this->session->get(self::KEY . '.institute', []);
    }

    public function username(): ?string
    {
        return $this->session->get(self::KEY . '.username');
    }

    public function forget(): void
    {
        $this->session->forget(self::KEY);
    }
}
