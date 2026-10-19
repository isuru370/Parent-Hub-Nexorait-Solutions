<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in - Nexora Parent Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-[Inter] text-gray-900 antialiased">
<div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">

    {{-- Brand side --}}
    <aside class="relative hidden overflow-hidden bg-gradient-to-br from-red-600 via-red-600 to-orange-500 p-12 text-white lg:flex lg:flex-col">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/15 font-extrabold ring-1 ring-white/30">N</span>
            <span class="text-lg font-extrabold">Nexora <span class="font-medium text-white/80">Parent Hub</span></span>
        </a>

        <div class="mt-auto max-w-md">
            <h1 class="text-4xl font-extrabold leading-tight">Your child's classes, attendance and fees in one place.</h1>
            <p class="mt-4 text-lg text-white/85">Use the student ID and password your institute sent by SMS.</p>

            <dl class="mt-10 space-y-4 text-sm">
                <div class="flex gap-3 rounded-2xl bg-white/10 p-4 ring-1 ring-white/20">
                    <x-icon name="academic" class="h-6 w-6 shrink-0" />
                    <div>
                        <dt class="font-semibold">Parents and students</dt>
                        <dd class="text-white/80">Log in with the student ID, for example <span class="font-semibold">SA10001</span>.</dd>
                    </div>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/10 p-4 ring-1 ring-white/20">
                    <x-icon name="shield" class="h-6 w-6 shrink-0" />
                    <div>
                        <dt class="font-semibold">Hub admins</dt>
                        <dd class="text-white/80">Log in with your email address.</dd>
                    </div>
                </div>
            </dl>
        </div>

        <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-orange-400/30 blur-3xl"></div>
    </aside>

    {{-- Form side --}}
    <main class="flex items-center justify-center px-5 py-10 sm:px-10">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}" class="mb-10 flex items-center gap-3 lg:hidden">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-red-600 to-orange-500 font-extrabold text-white">N</span>
                <span class="text-lg font-extrabold">Nexora <span class="font-medium text-red-600">Parent Hub</span></span>
            </a>

            <h2 class="text-2xl font-extrabold">Log in</h2>
            <p class="mt-1 text-sm text-gray-500">One login for parents, students and hub admins.</p>

            @if (session('status'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5" novalidate>
                @csrf

                <div>
                    <label for="login" class="mb-1.5 block text-sm font-semibold">Student ID or email</label>
                    <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                        autocomplete="username" autocapitalize="none" spellcheck="false"
                        class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-500/15 @error('login') border-red-500 @enderror"
                        placeholder="SA10001">
                    <p id="loginHint" class="mt-1.5 text-xs text-gray-500" aria-live="polite">Parents: use the student ID from the SMS.</p>
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            class="block w-full rounded-xl border border-gray-300 px-4 py-3 pr-16 text-base outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-500/15">
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-gray-500 hover:text-gray-800">Show</button>
                    </div>
                </div>

                @error('login')
                    <div class="flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        <x-icon name="warning" class="mt-0.5 h-5 w-5 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <label id="rememberRow" class="hidden items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600">
                    Keep me logged in
                </label>

                <button type="submit" id="submitBtn"
                    class="w-full rounded-xl bg-red-600 px-4 py-3 text-base font-semibold text-white shadow-lg shadow-red-500/25 transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500/30 disabled:opacity-60">
                    Log in
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-gray-500">
                Forgot the password? Ask your institute to reset it.
                <br>
                <a href="{{ route('privacy.policy') }}" class="mt-2 inline-block underline hover:text-gray-800">Privacy policy</a>
            </p>
        </div>
    </main>
</div>

<script>
    (function () {
        var input = document.getElementById('login');
        var hint = document.getElementById('loginHint');
        var remember = document.getElementById('rememberRow');
        var password = document.getElementById('password');

        function update() {
            var isAdmin = input.value.indexOf('@') !== -1;
            hint.textContent = isAdmin
                ? 'Logging in as a hub admin.'
                : 'Parents: use the student ID from the SMS.';
            remember.classList.toggle('hidden', !isAdmin);
            remember.classList.toggle('flex', isAdmin);
        }

        input.addEventListener('input', update);
        update();

        document.getElementById('togglePassword').addEventListener('click', function () {
            var show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            this.textContent = show ? 'Hide' : 'Show';
        });

        input.closest('form').addEventListener('submit', function () {
            var btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Checking...';
        });
    })();
</script>
</body>
</html>
