<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b1a3f">
    <title>@yield('title', 'Home') - {{ $institute['name'] ?? 'Nexora Parent Hub' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100/70 font-[Inter] text-slate-900 antialiased">
@php
    $hasCourses = $hasCourses ?? false;
    // [route, active pattern, icon, label, on phone bottom bar]
    $nav = array_values(array_filter([
        ['portal.dashboard', 'portal.dashboard', 'home', 'Home', true],
        ['portal.attendance', 'portal.attendance', 'clipboard', 'Attendance', true],
        ['portal.payments', 'portal.payments', 'cash', 'Fees', true],
        ['portal.exams', 'portal.exams|portal.result', 'academic', 'Exams & Results', false],
        ['portal.schedule', 'portal.schedule', 'calendar', 'Class Schedule', false],
        $hasCourses ? ['portal.courses', 'portal.courses|portal.course', 'book', 'My Courses', false] : null,
        ['portal.teachers', 'portal.teachers', 'users', 'Teachers', false],
        ['portal.notifications', 'portal.notifications', 'bell', 'Messages', false],
        ['portal.id-card', 'portal.id-card', 'qr', 'Digital ID', false],
        ['portal.profile', 'portal.profile', 'user', 'Profile', true],
    ]));
    $isActive = fn ($pattern) => collect(explode('|', $pattern))->contains(fn ($p) => request()->routeIs($p));
    $studentName = $student['initial_name'] ?? $student['full_name'] ?? 'Student';
    $instituteName = $institute['name'] ?? 'Nexora Parent Hub';
    $logo = $institute['logo'] ?? null;
@endphp

{{-- ===================== Sidebar (desktop) ===================== --}}
<aside class="hero fixed inset-y-0 left-0 z-40 hidden w-64 flex-col lg:flex">
    <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 px-5 pb-5 pt-6">
        @if ($logo)
            <img src="{{ $logo }}" alt="" class="h-11 w-11 rounded-xl bg-white object-contain p-1" onerror="this.remove()">
        @else
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-gold-300 to-gold-600 text-base font-extrabold text-navy-900">{{ \App\Support\Fmt::initials($instituteName) }}</span>
        @endif
        <span class="min-w-0 leading-tight">
            <span class="block truncate text-sm font-bold">{{ $instituteName }}</span>
            <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-gold-300">Parent Portal</span>
        </span>
    </a>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3" aria-label="Portal">
        @foreach ($nav as [$route, $pattern, $icon, $label])
            <a href="{{ route($route) }}"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $isActive($pattern) ? 'bg-white/12 text-white shadow-inner ring-1 ring-gold-400/40' : 'text-navy-100/80 hover:bg-white/6 hover:text-white' }}"
                @if ($isActive($pattern)) aria-current="page" @endif>
                <x-icon :name="$icon" class="h-5 w-5 {{ $isActive($pattern) ? 'text-gold-300' : '' }}" />
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="m-3 rounded-2xl bg-white/6 p-4 ring-1 ring-white/10">
        <p class="text-xs text-navy-200">Signed in for</p>
        <p class="truncate text-sm font-bold">{{ $studentName }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">
                <x-icon name="logout" class="h-4 w-4" /> Log out
            </button>
        </form>
    </div>
    <p class="pb-4 text-center text-[11px] text-navy-300/70">Powered by NexoraIT</p>
</aside>

<div class="lg:pl-64">
    {{-- ===================== Top bar ===================== --}}
    <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-4 sm:px-6">
            <button type="button" data-menu-open class="-ml-1 rounded-xl p-2 text-navy-900 hover:bg-slate-100 lg:hidden" aria-label="Menu">
                <x-icon name="menu" class="h-6 w-6" />
            </button>
            <a href="{{ route('portal.dashboard') }}" class="flex min-w-0 items-center gap-2.5 lg:hidden">
                @if ($logo)
                    <img src="{{ $logo }}" alt="" class="h-8 w-8 rounded-lg object-contain" onerror="this.remove()">
                @else
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-navy-900 text-xs font-extrabold text-gold-300">{{ \App\Support\Fmt::initials($instituteName) }}</span>
                @endif
                <span class="truncate text-sm font-bold">{{ $instituteName }}</span>
            </a>
            <p class="hidden truncate text-sm text-slate-500 lg:block">
                {{ now()->format('l, d F Y') }}
            </p>

            <div class="ml-auto flex items-center gap-1">
                <a href="{{ route('portal.notifications') }}" class="relative rounded-full p-2 text-slate-600 hover:bg-slate-100 {{ request()->routeIs('portal.notifications') ? 'text-navy-700' : '' }}" aria-label="Messages">
                    <x-icon name="bell" class="h-6 w-6" />
                </a>
                <a href="{{ route('portal.profile') }}" class="flex items-center gap-2 rounded-full py-1 pl-1 pr-1 hover:bg-slate-100 sm:pr-3" aria-label="Profile">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-gold-300 to-gold-600 text-xs font-extrabold text-navy-900">{{ \App\Support\Fmt::initials($studentName) }}</span>
                    <span class="hidden max-w-40 truncate text-sm font-semibold sm:block">{{ $studentName }}</span>
                </a>
            </div>
        </div>
    </header>

    {{-- ===================== Drawer (phones) ===================== --}}
    <div data-menu hidden class="fixed inset-0 z-50 lg:hidden">
        <div data-menu-close class="absolute inset-0 bg-navy-950/50"></div>
        <aside class="hero absolute inset-y-0 left-0 flex w-72 flex-col">
            <div class="flex items-center justify-between px-5 pb-4 pt-6">
                <span class="min-w-0">
                    <span class="block truncate text-sm font-bold">{{ $instituteName }}</span>
                    <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-gold-300">Parent Portal</span>
                </span>
                <button type="button" data-menu-close class="rounded-lg p-1.5 hover:bg-white/10" aria-label="Close"><x-icon name="x" class="h-5 w-5" /></button>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto px-3">
                @foreach ($nav as [$route, $pattern, $icon, $label])
                    <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ $isActive($pattern) ? 'bg-white/12 text-white ring-1 ring-gold-400/40' : 'text-navy-100/80' }}">
                        <x-icon :name="$icon" class="h-5 w-5 {{ $isActive($pattern) ? 'text-gold-300' : '' }}" /> {{ $label }}
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="p-4">
                @csrf
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2.5 text-sm font-semibold"><x-icon name="logout" class="h-4 w-4" /> Log out</button>
            </form>
        </aside>
    </div>

    <main class="mx-auto max-w-6xl px-4 pb-28 pt-6 sm:px-6 lg:pb-12">
        @hasSection('heading')
            <div class="mb-6">
                <h1 class="text-2xl font-extrabold tracking-tight text-navy-900">@yield('heading')</h1>
                @hasSection('subheading')
                    <p class="mt-1 text-sm text-slate-500">@yield('subheading')</p>
                @endif
            </div>
        @endif

        @include('partials.flash')

        @if (\App\Support\WebPush::enabled() && request()->routeIs('portal.dashboard') && empty($error))
            @include('portal.partials.push-prompt')
        @endif

        @if (!empty($error))
            <div class="card p-8 text-center">
                <x-icon name="warning" class="mx-auto h-9 w-9 text-gold-600" />
                <p class="mt-3 font-semibold text-navy-900">{{ $error }}</p>
                <a href="{{ url()->current() }}" class="btn-primary mt-5">Try again</a>
            </div>
        @else
            @yield('content')
        @endif

        <p class="mt-10 text-center text-xs text-slate-400">Details are kept by your institute · Powered by NexoraIT</p>
    </main>
</div>

{{-- ===================== Bottom bar (phones) ===================== --}}
<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur lg:hidden" aria-label="Portal">
    <div class="grid grid-cols-5">
        @php $bottom = array_values(array_filter($nav, fn ($n) => $n[4])); array_splice($bottom, 3, 0, [['portal.id-card', 'portal.id-card', 'qr', 'ID', true]]); @endphp
        @foreach ($bottom as [$route, $pattern, $icon, $label])
            <a href="{{ route($route) }}"
                class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-semibold {{ $isActive($pattern) ? 'text-navy-900' : 'text-slate-400' }}"
                @if ($isActive($pattern)) aria-current="page" @endif>
                <span class="grid h-7 w-12 place-items-center rounded-full {{ $isActive($pattern) ? 'bg-gold-100' : '' }}"><x-icon :name="$icon" class="h-5 w-5" /></span>
                {{ $label }}
            </a>
        @endforeach
    </div>
</nav>

<script>
    (() => {
        const menu = document.querySelector('[data-menu]');
        document.querySelectorAll('[data-menu-open]').forEach(b => b.addEventListener('click', () => { menu.hidden = false; }));
        document.querySelectorAll('[data-menu-close]').forEach(b => b.addEventListener('click', () => { menu.hidden = true; }));
    })();
</script>

@if (\App\Support\WebPush::enabled())
    @include('portal.partials.push-script')
@endif
</body>
</html>
