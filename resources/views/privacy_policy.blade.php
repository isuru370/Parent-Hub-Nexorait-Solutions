@extends('layouts.public')

@section('title', 'Privacy policy - Nexora Parent Hub')
@section('description', 'How Nexora Parent Hub collects, uses and protects the information of parents and students.')

@section('content')
@php
    $sections = [
        ['Introduction', [
            'Welcome to <strong>Nexora Parent Hub</strong>. This Privacy Policy explains how we collect, use, and protect your personal information when you use our platform.',
            'By using this platform, you agree to the practices described in this Privacy Policy.',
        ]],
        ['Information We Collect', ['We may collect the following information:'], [
            'Student ID', 'Student Name', 'Grade Information', 'Profile Photograph',
            'Parent Contact Details', 'Notification Token (Firebase Cloud Messaging)', 'Device Information',
        ]],
        ['How We Use Your Information', [], [
            'Authenticate your account', 'Display student information', 'Show class schedules',
            'Display upcoming examinations', 'Display payment information', 'Send important notifications',
            'Improve application performance',
        ]],
        ['Notifications', [
            'Nexora Parent Hub uses <strong>Firebase Cloud Messaging (FCM)</strong> to send important educational notifications, announcements, reminders, and updates.',
        ]],
        ['Data Security', [
            'We implement appropriate technical and organizational measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction.',
        ]],
        ['Data Sharing', [
            'We do not sell or rent your personal information. Your information is shared only with your educational institute for providing educational services.',
        ]],
        ["Children's Privacy", [
            'This platform is intended for parents registered with an educational institute. Student information is managed only by the respective institution.',
        ]],
        ['Changes to this Privacy Policy', [
            'We may update this Privacy Policy from time to time. Any changes will be published on this page.',
        ]],
        ['Contact Us', [
            'If you have any questions regarding this Privacy Policy, please contact your educational institute or the system administrator.',
        ]],
    ];
@endphp

<main class="relative overflow-hidden">
    <div class="pointer-events-none absolute -right-40 -top-40 h-[28rem] w-[28rem] rounded-full bg-orange-200/40 blur-3xl"></div>

    <div class="relative mx-auto max-w-3xl px-4 pb-20 pt-14 sm:px-6 lg:pt-20">
        <p class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
            <x-icon name="shield" class="h-4 w-4" /> Privacy policy
        </p>
        <h1 class="mt-5 text-4xl font-extrabold tracking-tight sm:text-5xl">
            Nexora Parent Hub <span class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent">Privacy Policy</span>
        </h1>
        <p class="mt-3 text-sm text-gray-500">Last updated: October 2026</p>

        <nav class="mt-8 rounded-2xl border border-gray-200 bg-stone-50 p-5" aria-label="Contents">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Contents</p>
            <ol class="mt-3 grid gap-x-6 gap-y-1.5 text-sm sm:grid-cols-2">
                @foreach ($sections as $i => [$title])
                    <li><a href="#section-{{ $i + 1 }}" class="text-gray-700 hover:text-red-600">{{ $i + 1 }}. {{ $title }}</a></li>
                @endforeach
            </ol>
        </nav>

        <div class="mt-10 space-y-10">
            @foreach ($sections as $i => $section)
                @php [$title, $paragraphs] = $section; $items = $section[2] ?? []; @endphp
                <section id="section-{{ $i + 1 }}" class="scroll-mt-20">
                    <h2 class="flex items-center gap-3 text-xl font-extrabold">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-red-50 text-sm font-bold text-red-600">{{ $i + 1 }}</span>
                        {{ $title }}
                    </h2>
                    @foreach ($paragraphs as $paragraph)
                        <p class="mt-3 leading-relaxed text-gray-600">{!! $paragraph !!}</p>
                    @endforeach
                    @if ($items)
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($items as $item)
                                <li class="flex items-center gap-2.5 rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-700">
                                    <x-icon name="check" class="h-4 w-4 shrink-0 text-red-600" /> {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>

        <a href="{{ route('home') }}" class="mt-12 inline-flex items-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            <x-icon name="arrow-left" class="h-4 w-4" /> Back to home
        </a>
    </div>
</main>
@endsection
