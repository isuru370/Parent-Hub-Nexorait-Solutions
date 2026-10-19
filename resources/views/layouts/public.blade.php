<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#dc2626">
    <meta name="description" content="@yield('description', 'Nexora Parent Hub: see your child\'s attendance, class fees, exam results and class schedule from any NexoraEDU institute, on the web or the parent app.')">
    <title>@yield('title', 'Nexora Parent Hub')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-[Inter] text-gray-900 antialiased">
@php
    $account = $account ?? null;
    $loginUrl = $account['url'] ?? route('login');
    $loginLabel = $account['label'] ?? 'Log in';
    $home = request()->routeIs('home') ? '' : route('home');
@endphp

{{-- Navigation --}}
<header class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4 sm:px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-red-600 to-orange-500 text-sm font-extrabold text-white">N</span>
            <span class="text-base font-extrabold">Nexora <span class="font-medium text-red-600">Parent Hub</span></span>
        </a>

        <nav class="ml-4 hidden items-center gap-1 text-sm font-semibold text-gray-600 md:flex" aria-label="Sections">
            <a href="{{ $home }}#features" class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-gray-900">Features</a>
            <a href="{{ $home }}#how-it-works" class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-gray-900">How it works</a>
            <a href="{{ $home }}#app" class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-gray-900">Parent app</a>
            <a href="{{ $home }}#faq" class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-gray-900">Help</a>
        </nav>

        <a href="{{ $loginUrl }}" class="ml-auto rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-500/25 hover:bg-red-700">{{ $loginLabel }}</a>
    </div>
</header>

@yield('content')

<footer class="border-t border-gray-100 bg-stone-50">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 text-sm text-gray-500 sm:px-6 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-2.5">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-red-600 to-orange-500 text-xs font-extrabold text-white">N</span>
            <span>© {{ date('Y') }} Nexora IT Solutions</span>
        </div>
        <nav class="flex flex-wrap gap-x-6 gap-y-2 font-medium" aria-label="Footer">
            <a href="{{ $home }}#features" class="hover:text-gray-900">Features</a>
            <a href="{{ $home }}#faq" class="hover:text-gray-900">Help</a>
            <a href="{{ route('privacy.policy') }}" class="hover:text-gray-900">Privacy policy</a>
            <a href="{{ route('login') }}" class="hover:text-gray-900">Log in</a>
        </nav>
    </div>
</footer>
</body>
</html>
