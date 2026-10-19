@php
    // Changes on every login, so a new login always re-sends the token.
    $pushKey = 'nexoraPush:' . substr(hash('sha256', session('portal.student.student_id') . '|' . session('portal.logged_in_at')), 0, 16);
@endphp
<script type="module">
    import { initializeApp } from '{{ \App\Support\WebPush::sdk('firebase-app.js') }}';
    import { getMessaging, getToken, onMessage, isSupported } from '{{ \App\Support\WebPush::sdk('firebase-messaging.js') }}';

    const firebaseConfig = @json(\App\Support\WebPush::clientConfig());
    const vapidKey = @json(\App\Support\WebPush::vapidKey());
    const swUrl = @json(route('firebase.sw'));
    const saveUrl = @json(route('portal.push-token'));
    const csrf = @json(csrf_token());
    const pushKey = @json($pushKey);
    const icon = @json(asset('favicon.ico'));

    const storage = (area) => ({
        get(key) { try { return window[area].getItem(key); } catch (e) { return null; } },
        set(key, value) { try { window[area].setItem(key, value); } catch (e) {} },
    });
    const local = storage('localStorage');
    const session = storage('sessionStorage');

    const prompt = document.getElementById('pushPrompt');
    const sections = document.querySelectorAll('[data-push-section]');
    const statuses = document.querySelectorAll('[data-push-status]');
    const buttons = document.querySelectorAll('[data-push-enable]');

    const setStatus = (text, tone) => statuses.forEach((el) => { el.textContent = text; el.dataset.tone = tone; });
    const showButtons = (show) => buttons.forEach((el) => { el.hidden = !show; el.disabled = false; });
    const showPrompt = (show) => { if (prompt) prompt.hidden = !show; };
    const recentlyDismissed = () => Date.now() - Number(local.get('nexoraPushDismissed') || 0) < 14 * 864e5;

    function deviceId() {
        let id = local.get('nexoraDeviceId');
        if (!id) {
            id = 'web-' + (crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2));
            local.set('nexoraDeviceId', id);
        }
        return id;
    }

    let supported = false;
    try {
        supported = 'Notification' in window && 'serviceWorker' in navigator && await isSupported();
    } catch (e) {}

    if (!supported) {
        sections.forEach((el) => { el.hidden = false; });
        showButtons(false);
        setStatus('This browser cannot show notifications. On iPhone, add this site to the Home Screen first.', 'muted');
    } else {
        sections.forEach((el) => { el.hidden = false; });
        const messaging = getMessaging(initializeApp(firebaseConfig));
        let registration = null;

        async function sendToken() {
            registration = registration || await navigator.serviceWorker.register(swUrl);
            const token = await getToken(messaging, { vapidKey, serviceWorkerRegistration: registration });
            if (!token) throw new Error('Could not get a notification token from the browser.');
            if (session.get(pushKey) === token) return;

            const response = await fetch(saveUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ token, device_id: deviceId() }),
            });
            const json = await response.json().catch(() => ({}));
            if (!response.ok || !json.status) throw new Error(json.message || 'Could not turn on notifications.');
            session.set(pushKey, token);
        }

        function showState() {
            if (Notification.permission === 'denied') {
                showButtons(false);
                showPrompt(false);
                setStatus('Blocked in this browser. Allow notifications for this site in the browser settings, then reload.', 'error');
            } else {
                showButtons(true);
                showPrompt(!recentlyDismissed());
                setStatus('Off on this device.', 'muted');
            }
        }

        async function enable() {
            buttons.forEach((el) => { el.disabled = true; });
            setStatus('Turning on...', 'muted');
            try {
                const permission = Notification.permission === 'granted' ? 'granted' : await Notification.requestPermission();
                if (permission !== 'granted') return showState();
                await sendToken();
                showButtons(false);
                showPrompt(false);
                setStatus('On. This device gets the institute\'s notifications.', 'on');
            } catch (e) {
                console.warn('[push]', e);
                showButtons(true);
                setStatus(e.message || 'Could not turn on notifications.', 'error');
            }
        }

        // While the portal is open, show the notification ourselves.
        onMessage(messaging, (payload) => {
            const n = payload.notification || payload.data || {};
            if (registration && (n.title || n.body)) {
                registration.showNotification(n.title || 'Nexora Parent Hub', { body: n.body || '', icon });
            }
        });

        buttons.forEach((el) => el.addEventListener('click', enable));
        document.querySelectorAll('[data-push-dismiss]').forEach((el) => el.addEventListener('click', () => {
            local.set('nexoraPushDismissed', String(Date.now()));
            showPrompt(false);
        }));

        Notification.permission === 'granted' ? enable() : showState();
    }
</script>
