<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Portal\PortalApi;
use App\Services\Portal\PortalSession;
use App\Support\WebPush;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * The portal page sends this browser's FCM token here after the parent allows
 * notifications. It is saved in the student's institute (fcm_tokens table),
 * so the institute's push notifications reach this browser too.
 */
class PushTokenController extends Controller
{
    public function __construct(
        private PortalApi $api,
        private PortalSession $portal
    ) {}

    public function store(Request $request): JsonResponse
    {
        abort_unless(WebPush::enabled(), 404);

        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9_.:-]+$/'],
        ]);

        $deviceId = ($data['device_id'] ?? '') ?: 'web-' . substr(hash('sha256', $data['token']), 0, 40);
        $saved = $this->portal->pushDevice();

        // Already sent in this login: nothing to do.
        if ($saved && $saved['token'] === $data['token'] && $saved['device_id'] === $deviceId) {
            return response()->json(['status' => true, 'message' => 'Notifications are on for this device.']);
        }

        try {
            $result = $this->api->saveFcmToken($data['token'], $deviceId, WebPush::deviceName($request->userAgent()));
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 502);
        }

        if (!$result['status']) {
            return response()->json(['status' => false, 'message' => $result['message']], 502);
        }

        $this->portal->setPushDevice([
            'token' => $data['token'],
            'device_id' => $deviceId,
            'device_type' => $result['device_type'],
        ]);

        return response()->json(['status' => true, 'message' => $result['message']]);
    }
}
