@extends('layouts.public')

@section('title', 'Nexora Parent Hub - Attendance, fees and results for parents')

@section('content')
@php
    $loginUrl = $account['url'] ?? route('login');
    $loginLabel = $account['label'] ?? 'Log in';
    $features = [
        ['clipboard', 'Attendance', 'See every class your child attended or missed, with the date and time they were marked.', 'bg-red-50 text-red-600'],
        ['cash', 'Class fees', 'A month-by-month view for each class: paid months in green, unpaid months in red.', 'bg-orange-50 text-orange-600'],
        ['trophy', 'Exam results', 'Marks, grades and rank for each exam as soon as the institute publishes them.', 'bg-amber-50 text-amber-600'],
        ['calendar', 'Class schedule', "Today's classes and what is coming up, with the hall and the teacher.", 'bg-sky-50 text-sky-600'],
        ['bell', 'Messages', 'Payment receipts, attendance alerts and notices from the institute in one place.', 'bg-emerald-50 text-emerald-600'],
        ['shield', 'Private by design', "Each login shows one student's information only. Your password is checked by your own institute.", 'bg-gray-100 text-gray-700'],
    ];
    $steps = [
        ['The institute registers your child', 'When your child joins a class at an institute that uses NexoraEDU, they are added to the hub automatically.'],
        ['You receive an SMS', 'The SMS has the student ID and a password for the parent portal. Keep it safe.'],
        ['Log in on the web or the app', 'Use the student ID and password here, or in the parent app. Change the password any time from your profile.'],
    ];
    $faqs = [
        ['I did not get the SMS. What do I do?', 'Ask the institute office. They can resend the login details or reset the password for you.'],
        ['I forgot the password.', 'Passwords are kept by your institute, not by the hub, so the institute office can reset it for you.'],
        ['I have two children. Do I need two logins?', "Yes. Each child has their own student ID. Log out and log in with the other child's ID to switch."],
        ['Can I pay fees here?', 'Not yet. You can see which months are paid and which are not; payments are made at the institute.'],
        ['Does it cost anything?', 'No. The portal and the parent app are free for parents.'],
    ];
@endphp

<main>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute -right-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-orange-200/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-40 top-40 h-96 w-96 rounded-full bg-red-100/60 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-14 px-4 pb-20 pt-14 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:pb-28 lg:pt-20">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                    <x-icon name="academic" class="h-4 w-4" /> For parents of NexoraEDU institutes
                </p>

                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.4rem]">
                    Know how your child is doing,
                    <span class="bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent">without calling the office.</span>
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-relaxed text-gray-600">
                    Attendance, class fees, exam results and the class schedule, straight from your child's institute. On the web or on your phone.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ $loginUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-red-500/25 hover:bg-red-700">
                        {{ $account ? $loginLabel : 'Log in to the portal' }}
                        <x-icon name="chevron-right" class="h-5 w-5" />
                    </a>
                    @if ($androidUrl)
                        <a href="{{ $androidUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3.5 text-base font-semibold text-gray-900 hover:border-gray-300 hover:bg-gray-50">
                            <x-icon name="phone" class="h-5 w-5" /> Get the Android app
                        </a>
                    @else
                        <a href="#how-it-works" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3.5 text-base font-semibold text-gray-900 hover:border-gray-300 hover:bg-gray-50">
                            How it works
                        </a>
                    @endif
                </div>

                <p class="mt-5 flex items-center gap-2 text-sm text-gray-500">
                    <x-icon name="key" class="h-4 w-4 shrink-0" />
                    Use the student ID and password your institute sent by SMS.
                </p>
            </div>

            {{-- Phone preview of the portal (sample data) --}}
            <div class="relative mx-auto w-full max-w-[22rem]" aria-hidden="true">
                <div class="rounded-[2.6rem] bg-gray-900 p-3 shadow-2xl shadow-red-900/20">
                    <div class="overflow-hidden rounded-[2rem] bg-stone-50">
                        <div class="flex items-center gap-2.5 border-b border-stone-200 bg-white px-4 py-3">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-red-600 to-orange-500 text-[11px] font-extrabold text-white">SI</span>
                            <span class="leading-tight">
                                <span class="block text-[13px] font-extrabold">Sample Institute</span>
                                <span class="block text-[11px] text-gray-500">K. Perera</span>
                            </span>
                            <x-icon name="bell" class="ml-auto h-5 w-5 text-gray-500" />
                        </div>

                        <div class="space-y-3 p-3.5">
                            <div class="rounded-2xl bg-gradient-to-br from-red-600 to-orange-500 p-4 text-white">
                                <p class="text-lg font-extrabold">K. Perera</p>
                                <p class="text-xs text-white/85">Grade 11 · SA10023</p>
                            </div>

                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-xl border border-stone-200 bg-white p-2.5">
                                    <p class="text-[10px] font-semibold text-gray-500">Attendance</p>
                                    <p class="text-lg font-extrabold">96%</p>
                                </div>
                                <div class="rounded-xl border border-stone-200 bg-white p-2.5">
                                    <p class="text-[10px] font-semibold text-gray-500">Fees</p>
                                    <p class="text-lg font-extrabold text-emerald-600">3/3</p>
                                </div>
                                <div class="rounded-xl border border-stone-200 bg-white p-2.5">
                                    <p class="text-[10px] font-semibold text-gray-500">Messages</p>
                                    <p class="text-lg font-extrabold text-red-600">2</p>
                                </div>
                            </div>

                            <p class="px-1 pt-1 text-xs font-bold">Today's classes</p>
                            @foreach ([['3:00 PM', 'Combined Maths', 'Hall 2'], ['5:30 PM', 'Physics', 'Hall 4']] as [$time, $class, $hall])
                                <div class="flex items-center gap-3 rounded-xl border border-stone-200 bg-white p-3">
                                    <span class="w-14 text-xs font-bold">{{ $time }}</span>
                                    <span class="min-w-0 flex-1 border-l border-stone-200 pl-3">
                                        <span class="block truncate text-[13px] font-semibold">{{ $class }}</span>
                                        <span class="block text-[11px] text-gray-500">{{ $hall }}</span>
                                    </span>
                                </div>
                            @endforeach

                            <p class="px-1 pt-1 text-xs font-bold">Fees for this month</p>
                            <div class="flex items-center justify-between rounded-xl border border-stone-200 bg-white p-3">
                                <span class="text-[13px] font-semibold">Combined Maths</span>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Paid</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 border-t border-stone-200 bg-white py-2 text-[9px] font-semibold text-gray-500">
                            @foreach ([['home', 'Home'], ['clipboard', 'Attendance'], ['cash', 'Fees'], ['academic', 'Exams'], ['calendar', 'Classes']] as $i => [$icon, $label])
                                <span class="flex flex-col items-center gap-0.5 {{ $i === 0 ? 'text-red-600' : '' }}"><x-icon :name="$icon" class="h-5 w-5" />{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="absolute -right-10 top-24 hidden items-center gap-2.5 rounded-2xl border border-gray-100 bg-white px-3.5 py-2.5 shadow-xl sm:flex">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-600"><x-icon name="check" class="h-5 w-5" /></span>
                    <span class="leading-tight">
                        <span class="block text-xs font-bold">Payment received</span>
                        <span class="block text-[11px] text-gray-500">October fee · Rs. 2,500</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="scroll-mt-16 border-t border-gray-100 bg-stone-50 py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-wider text-red-600">What you can see</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Everything the institute knows about your child's classes.</h2>
                <p class="mt-3 text-lg text-gray-600">The information comes live from the institute's NexoraEDU system, so it is always the latest.</p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as [$icon, $title, $text, $tone])
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <span class="grid h-11 w-11 place-items-center rounded-xl {{ $tone }}"><x-icon :name="$icon" class="h-6 w-6" /></span>
                        <h3 class="mt-4 text-lg font-bold">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="scroll-mt-16 py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-wider text-red-600">How it works</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">No sign-up. Your institute sets it up for you.</h2>
            </div>

            <ol class="mt-12 grid gap-8 md:grid-cols-3 md:gap-6">
                @foreach ($steps as $i => [$title, $text])
                    <li class="relative">
                        <div class="flex items-center gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gradient-to-br from-red-600 to-orange-500 text-lg font-extrabold text-white shadow-lg shadow-red-500/25">{{ $i + 1 }}</span>
                            @if (!$loop->last)
                                <span class="hidden h-px flex-1 bg-gradient-to-r from-red-200 to-transparent md:block"></span>
                            @endif
                        </div>
                        <h3 class="mt-5 text-lg font-bold">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Parent app --}}
    <section id="app" class="scroll-mt-16 px-4 sm:px-6">
        <div class="relative mx-auto max-w-6xl overflow-hidden rounded-3xl bg-gray-950 px-6 py-14 text-white sm:px-12 lg:py-16">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-red-600/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 left-1/3 h-80 w-80 rounded-full bg-orange-500/20 blur-3xl"></div>

            <div class="relative grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-orange-400">Parent app</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Get a notification the moment something happens.</h2>
                    <p class="mt-3 text-lg text-white/70">The parent app uses the same login. It tells you when your child is marked present, when a payment is received and when results are out.</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @if ($androidUrl)
                            <a href="{{ $androidUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-gray-900 hover:bg-gray-100">
                                <x-icon name="phone" class="h-5 w-5" /> Google Play
                            </a>
                        @endif
                        @if ($iosUrl)
                            <a href="{{ $iosUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-gray-900 hover:bg-gray-100">
                                <x-icon name="phone" class="h-5 w-5" /> App Store
                            </a>
                        @endif
                        @if (!$androidUrl && !$iosUrl)
                            <span class="inline-flex items-center gap-2 rounded-xl border border-white/20 px-5 py-3 text-sm font-semibold text-white/80">
                                <x-icon name="clock" class="h-5 w-5" /> The app is coming to the stores soon
                            </span>
                        @endif
                        <a href="{{ $loginUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 font-semibold text-white hover:bg-red-700">
                            Use it in the browser <x-icon name="chevron-right" class="h-5 w-5" />
                        </a>
                    </div>
                </div>

                <ul class="space-y-3">
                    @foreach ([
                        ['check', 'Attendance alerts', 'Know your child reached the class.'],
                        ['cash', 'Payment receipts', 'Every fee you pay, confirmed on your phone.'],
                        ['trophy', 'Results', 'See marks as soon as they are published.'],
                    ] as [$icon, $title, $text])
                        <li class="flex gap-4 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white/10 text-orange-300"><x-icon :name="$icon" class="h-5 w-5" /></span>
                            <span>
                                <span class="block font-semibold">{{ $title }}</span>
                                <span class="block text-sm text-white/60">{{ $text }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-16 py-20 lg:py-24">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_1.6fr]">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-red-600">Help</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Common questions</h2>
                <p class="mt-3 text-gray-600">For anything about your child's classes, fees or login, contact the institute office.</p>
            </div>

            <div class="divide-y divide-gray-200 rounded-2xl border border-gray-200">
                @foreach ($faqs as [$q, $a])
                    <details class="group p-5 [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold">
                            {{ $q }}
                            <x-icon name="plus" class="h-5 w-5 shrink-0 text-gray-400 transition group-open:rotate-45" />
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- For institutes --}}
    <section class="px-4 pb-20 sm:px-6 lg:pb-24">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 rounded-3xl border border-red-100 bg-gradient-to-br from-red-50 to-orange-50 p-8 sm:p-10 md:flex-row md:items-center">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white text-red-600 shadow-sm"><x-icon name="building" class="h-7 w-7" /></span>
            <div class="flex-1">
                <h2 class="text-xl font-extrabold">Run an institute on NexoraEDU?</h2>
                <p class="mt-1 text-gray-600">Your parents get this portal and the app once your system is connected to the hub. Talk to us to switch it on.</p>
            </div>
            @if ($supportPhone || $supportEmail)
                <div class="flex flex-wrap gap-3">
                    @if ($supportPhone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 font-semibold text-white hover:bg-red-700">
                            <x-icon name="phone" class="h-5 w-5" /> {{ $supportPhone }}
                        </a>
                    @endif
                    @if ($supportEmail)
                        <a href="mailto:{{ $supportEmail }}" class="inline-flex items-center rounded-xl border border-red-200 bg-white px-5 py-3 font-semibold text-red-700 hover:bg-red-50">{{ $supportEmail }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
