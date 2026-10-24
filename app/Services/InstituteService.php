<?php

namespace App\Services;

use App\Models\Institute;

class InstituteService
{
    /**
     * Called by the institute on every deploy (nexora:deploy).
     *
     * The institute's .env is the master copy, so name / URL / contacts are
     * always overwritten. "status" is only set for a new institute: an
     * institute disabled in the hub admin stays disabled after a deploy.
     */
    public function register(array $data): Institute
    {
        $institute = Institute::firstOrNew([
            'code' => $data['institute_code'],
        ]);

        $institute->fill([
            'name' => $data['institute_name'],
            'api_url' => $data['api_url'],
            'contact_number' => $data['contact_mobile'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'app_version' => $data['app_version'] ?? null,
            'last_ping_at' => now(),
        ]);

        // Older institute versions do not send a logo: keep the saved one
        if (array_key_exists('logo', $data)) {
            $institute->logo = $data['logo'];
        }

        if (!$institute->exists) {
            $institute->status = true;
        }

        $institute->save();

        return $institute;
    }
}
