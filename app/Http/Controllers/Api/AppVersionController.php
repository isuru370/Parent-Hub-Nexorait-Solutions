<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * GET /api/v1/app-version?platform=android&current=1.0.3
 *
 * Tells the parent app whether a newer version exists and whether the
 * update is required (force_update).
 */
class AppVersionController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => ['required', Rule::in(AppVersion::PLATFORMS)],
            'current' => ['nullable', 'string', 'max:50'],
        ]);

        $latest = AppVersion::latestFor($validated['platform']);
        $current = $validated['current'] ?? null;

        if (!$latest) {
            return response()->json([
                'success' => true,
                'data' => [
                    'platform' => $validated['platform'],
                    'latest_version' => null,
                    'update_available' => false,
                    'force_update' => false,
                ],
            ]);
        }

        $updateAvailable = $current !== null && version_compare($current, $latest->version, '<');

        // Must update when ANY newer active release is marked force_update
        $forceUpdate = $updateAvailable && AppVersion::query()
            ->active()
            ->where('platform', $validated['platform'])
            ->where('force_update', true)
            ->get()
            ->contains(fn (AppVersion $v) => version_compare($current, $v->version, '<'));

        return response()->json([
            'success' => true,
            'data' => [
                'platform' => $validated['platform'],
                'latest_version' => $latest->version,
                'update_available' => $updateAvailable,
                'force_update' => $forceUpdate,
            ],
        ]);
    }
}
