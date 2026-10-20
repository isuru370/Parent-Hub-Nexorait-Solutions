<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\WebPush;
use Illuminate\Http\Response;

/**
 * /firebase-messaging-sw.js - shows push notifications while the portal is closed.
 * Built from config so the Firebase keys live only in .env.
 */
class FirebaseServiceWorkerController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(WebPush::enabled(), 404);

        $config = json_encode(WebPush::clientConfig(), JSON_UNESCAPED_SLASHES);
        $openUrl = json_encode(route('portal.notifications'), JSON_UNESCAPED_SLASHES);
        $icon = json_encode(asset('favicon.ico'), JSON_UNESCAPED_SLASHES);
        $app = json_encode(WebPush::sdk('firebase-app-compat.js'), JSON_UNESCAPED_SLASHES);
        $messaging = json_encode(WebPush::sdk('firebase-messaging-compat.js'), JSON_UNESCAPED_SLASHES);

        $js = <<<JS
// Nexora Parent Hub - push notifications
// Registered before Firebase so a click opens the portal's Messages page.
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var url = {$openUrl};
    event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
        for (var i = 0; i < list.length; i++) {
            if (list[i].url.indexOf(self.location.origin) === 0 && 'focus' in list[i]) {
                list[i].navigate(url);
                return list[i].focus();
            }
        }
        return clients.openWindow(url);
    }));
});

importScripts({$app});
importScripts({$messaging});

firebase.initializeApp({$config});

// Messages with a "notification" part are shown by Firebase automatically.
// Data-only messages are shown here.
firebase.messaging().onBackgroundMessage(function (payload) {
    if (payload.notification) return;
    var data = payload.data || {};
    if (!data.title && !data.body) return;
    self.registration.showNotification(data.title || 'Nexora Parent Hub', {
        body: data.body || '',
        icon: {$icon},
        data: data
    });
});
JS;

        return response($js, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'Service-Worker-Allowed' => '/',
        ]);
    }
}
