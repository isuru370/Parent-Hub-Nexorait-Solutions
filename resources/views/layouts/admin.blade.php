<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Nexora Parent Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-[Inter] text-gray-900 antialiased">
@php
    $nav = [
        ['admin.dashboard', 'admin.dashboard', 'home', 'Dashboard'],
        ['admin.institutes.index', 'admin.institutes.*', 'building', 'Institutes'],
        ['admin.students.index', 'admin.students.*', 'users', 'Students'],
        ['admin.app-versions.index', 'admin.app-versions.*', 'phone', 'App versions'],
        ['admin.admins.index', 'admin.admins.*', 'shield', 'Admins'],
        ['admin.settings', 'admin.settings*', 'cog', 'Settings'],
    ];
@endphp

<div class="lg:pl-64">
    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform lg:translate-x-0">
        <a href="{{ route('admin.dashboard') }}" class="flex h-16 items-center gap-3 border-b border-gray-100 px-5">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-red-600 to-orange-500 text-sm font-extrabold text-white">N</span>
            <span class="leading-tight">
                <span class="block text-sm font-extrabold">Nexora Parent Hub</span>
                <span class="block text-xs text-gray-500">Admin</span>
            </span>
        </a>

        <nav class="flex-1 space-y-1 overflow-y-auto p-3" aria-label="Admin">
            @foreach ($nav as [$route, $pattern, $icon, $label])
                @php $active = request()->routeIs($pattern); @endphp
                <a href="{{ route($route) }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                    @if ($active) aria-current="page" @endif>
                    <x-icon :name="$icon" class="h-5 w-5 {{ $active ? 'text-red-600' : 'text-gray-400' }}" />
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-gray-900 text-xs font-bold text-white">{{ \App\Support\Fmt::initials(auth()->user()->name) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <x-icon name="logout" class="h-4 w-4" /> Log out
                </button>
            </form>
        </div>
    </aside>
    <div id="sidebarBackdrop" class="fixed inset-0 z-30 hidden bg-gray-900/40 lg:hidden"></div>

    {{-- Top bar (mobile) --}}
    <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-gray-200 bg-white/90 px-4 backdrop-blur lg:hidden">
        <button id="openSidebar" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100" aria-label="Open menu">
            <x-icon name="menu" class="h-6 w-6" />
        </button>
        <span class="font-extrabold">@yield('title', 'Admin')</span>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight">@yield('title', 'Admin')</h1>
                @hasSection('subtitle')
                    <p class="mt-1 text-sm text-gray-500">@yield('subtitle')</p>
                @endif
            </div>
            @yield('actions')
        </div>

        @include('partials.flash')

        @yield('content')
    </main>
</div>

<script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        function toggle(open) {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop.classList.toggle('hidden', !open);
        }
        document.getElementById('openSidebar').addEventListener('click', function () { toggle(true); });
        backdrop.addEventListener('click', function () { toggle(false); });

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!confirm(form.getAttribute('data-confirm'))) e.preventDefault();
            });
        });
    })();
</script>
</body>
</html>
