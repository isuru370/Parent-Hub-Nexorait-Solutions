<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#dc2626">
    <title>@yield('title', 'Home') - {{ $institute['name'] ?? 'Parent Hub' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-[Inter] text-gray-900 antialiased">
@php
    $tabs = [
        ['portal.dashboard', 'portal.dashboard', 'home', 'Home'],
        ['portal.attendance', 'portal.attendance', 'clipboard', 'Attendance'],
        ['portal.payments', 'portal.payments', 'cash', 'Fees'],
        ['portal.exams', 'portal.exams|portal.result', 'academic', 'Exams'],
        ['portal.schedule', 'portal.schedule', 'calendar', 'Classes'],
    ];
    $isActive = fn ($pattern) => collect(explode('|', $pattern))->contains(fn ($p) => request()->routeIs($p));
    $studentName = $student['initial_name'] ?? $student['full_name'] ?? 'Student';
@endphp

<header class="sticky top-0 z-30 border-b border-stone-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-5xl items-center gap-3 px-4">
        <a href="{{ route('portal.dashboard') }}" class="flex min-w-0 items-center gap-3">
            @if (!empty($institute['logo']))
                <img src="{{ $institute['logo'] }}" alt="" class="h-9 w-9 rounded-lg object-cover">
            @else
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-red-600 to-orange-500 text-sm font-extrabold text-white">{{ \App\Support\Fmt::initials($institute['name'] ?? 'N') }}</span>
            @endif
            <span class="min-w-0 leading-tight">
                <span class="block truncate text-sm font-extrabold">{{ $institute['name'] ?? 'Parent Hub' }}</span>
                <span class="block truncate text-xs text-gray-500">{{ $studentName }}</span>
            </span>
        </a>

        <nav class="ml-6 hidden items-center gap-1 md:flex" aria-label="Portal">
            @foreach ($tabs as [$route, $pattern, $icon, $label])
                <a href="{{ route($route) }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold {{ $isActive($pattern) ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-stone-100' }}"
                    @if ($isActive($pattern)) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-1">
            <a href="{{ route('portal.notifications') }}" class="relative rounded-full p-2 text-gray-600 hover:bg-stone-100 {{ request()->routeIs('portal.notifications') ? 'text-red-600' : '' }}" aria-label="Notifications">
                <x-icon name="bell" class="h-6 w-6" />
            </a>
            <a href="{{ route('portal.profile') }}" class="rounded-full p-2 text-gray-600 hover:bg-stone-100 {{ request()->routeIs('portal.profile') ? 'text-red-600' : '' }}" aria-label="Profile">
                <x-icon name="user" class="h-6 w-6" />
            </a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-5xl px-4 pb-28 pt-5 md:pb-12">
    @hasSection('heading')
        <div class="mb-5">
            <h1 class="text-2xl font-extrabold tracking-tight">@yield('heading')</h1>
            @hasSection('subheading')
                <p class="mt-1 text-sm text-gray-500">@yield('subheading')</p>
            @endif
        </div>
    @endif

    @include('partials.flash')

    @if (!empty($error))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
            <x-icon name="warning" class="mx-auto h-8 w-8 text-amber-600" />
            <p class="mt-3 font-semibold text-amber-900">{{ $error }}</p>
            <a href="{{ url()->current() }}" class="mt-4 inline-block rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Try again</a>
        </div>
    @else
        @yield('content')
    @endif
</main>

{{-- Bottom tabs (phones) --}}
<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-stone-200 bg-white pb-[env(safe-area-inset-bottom)] md:hidden" aria-label="Portal">
    <div class="grid grid-cols-5">
        @foreach ($tabs as [$route, $pattern, $icon, $label])
            <a href="{{ route($route) }}"
                class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-semibold {{ $isActive($pattern) ? 'text-red-600' : 'text-gray-500' }}"
                @if ($isActive($pattern)) aria-current="page" @endif>
                <x-icon :name="$icon" class="h-6 w-6" />
                {{ $label }}
            </a>
        @endforeach
    </div>
</nav>
</body>
</html>
