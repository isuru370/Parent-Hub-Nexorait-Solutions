@extends('layouts.public')

@section('title', 'Nexora Parent Hub - Attendance, fees, results and courses for parents')

@section('content')
@php
    $loginUrl = $account['url'] ?? route('login');
    $loginLabel = $account['label'] ?? 'Log in';
    $supportPhone = $supportPhone ?: '0768971213';
    $supportDigits = preg_replace('/[^0-9]/', '', $supportPhone);
    $whatsApp = 'https://wa.me/' . (str_starts_with($supportDigits, '0') ? '94' . substr($supportDigits, 1) : $supportDigits);

    $features = [
        ['clipboard', 'Attendance', 'Every class your child attended or missed, with the date and time they were marked.'],
        ['cash', 'Class fees', 'Month by month for each class: which months are paid and which are not.'],
        ['trophy', 'Exam results', 'Marks, grade and class rank as soon as the institute publishes them.'],
        ['calendar', 'Class schedule', "Today's classes and what is coming up, with the hall and the teacher."],
        ['book', 'Courses', 'Course fees, installments and due dates, class days and attendance.'],
        ['qr', 'Digital ID card', "The student's ID card QR, ready to show if the card is left at home."],
        ['users', 'Teachers', "Your child's teachers and the classes they take."],
        ['bell', 'Messages', 'Payment receipts, attendance alerts and notices from the institute.'],
        ['shield', 'Private by design', "Each login shows one student's details only, checked by your own institute."],
    ];
    $steps = [
        ['The institute registers your child', 'When your child joins an institute that uses NexoraEDU, they are added to the hub automatically.'],
        ['You receive an SMS', 'The SMS has the student ID and a password for the parent portal. Keep it safe.'],
        ['Log in on the web or the app', 'Use the student ID and password here or in the Nexora Connect app. Change the password any time.'],
    ];
    $faqs = [
        ['I did not get the SMS. What do I do?', 'Ask the institute office. They can resend the login details or reset the password for you.'],
        ['I forgot the password.', 'Passwords are kept by your institute, not by the hub, so the institute office can reset it for you.'],
        ['Can I change my child\'s details?', 'No. The details are kept by the institute. Ask the institute office to correct anything. Only the password can be changed here.'],
        ['I have two children. Do I need two logins?', "Yes. Each child has their own student ID. Log out and log in with the other child's ID to switch."],
        ['Can I pay fees here?', 'Not yet. You can see which months and installments are paid; payments are made at the institute.'],
        ['Does it cost anything?', 'No. The portal and the parent app are free for parents.'],
    ];
@endphp

<main>
    {{-- ===================== Hero ===================== --}}
    <section class="hero relative overflow-hidden">
        <div class="pointer-events-none absolute -right-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-gold-400/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-navy-500/20 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:py-24">
            <div>
                <span class="chip bg-white/10 text-gold-300 ring-1 ring-gold-400/30"><x-icon name="sparkles" class="h-3.5 w-3.5" /> For parents of NexoraEDU institutes</span>
                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.4rem]">
                    Your child's education,<br><span class="bg-gradient-to-r from-gold-200 to-gold-500 bg-clip-text text-transparent">all in one place.</span>
                </h1>
                <p class="mt-5 max-w-xl text-lg text-navy-100/85">Attendance, class fees, exam results, courses, class schedule and messages from the institute, live and private, on the web or the parent app.</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $loginUrl }}" class="btn-gold px-6 py-3.5 text-base shadow-lg shadow-gold-500/20">
                        {{ $account ? $loginLabel : 'Log in to the portal' }} <x-icon name="chevron-right" class="h-5 w-5" />
                    </a>
                    @if ($androidUrl)
                        <a href="{{ $androidUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-6 py-3.5 text-base font-semibold ring-1 ring-white/20 hover:bg-white/15">
                            <x-icon name="phone" class="h-5 w-5" /> Get the app
                        </a>
                    @endif
                </div>
                <p class="mt-5 text-sm text-navy-200">Use the student ID and password from the institute's SMS.</p>
            </div>

            {{-- Preview card --}}
            <div class="relative mx-auto w-full max-w-sm">
                <div class="rounded-[28px] bg-white/8 p-3 ring-1 ring-white/15 backdrop-blur">
                    <div class="rounded-3xl bg-white p-5 text-slate-900 shadow-2xl">
                        <div class="flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-gold-200 to-gold-600 text-sm font-extrabold text-navy-900">AS</span>
                            <div>
                                <p class="font-bold text-navy-900">A. Silva</p>
                                <p class="text-xs text-slate-500">Grade 10 · SA10001</p>
                            </div>
                            <span class="chip ml-auto bg-emerald-50 text-emerald-700">Present</span>
                        </div>
                        <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-2xl bg-navy-50 p-3"><p class="text-lg font-extrabold text-navy-900">96%</p><p class="text-[11px] text-slate-500">Attendance</p></div>
                            <div class="rounded-2xl bg-emerald-50 p-3"><p class="text-lg font-extrabold text-emerald-700">3/3</p><p class="text-[11px] text-slate-500">Fees paid</p></div>
                            <div class="rounded-2xl bg-gold-50 p-3"><p class="text-lg font-extrabold text-gold-700">A</p><p class="text-[11px] text-slate-500">Last result</p></div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 p-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-navy-900 text-[11px] font-bold text-white">3 PM</span>
                                <div class="text-sm"><p class="font-semibold">Mathematics</p><p class="text-xs text-slate-500">Today · Hall 2</p></div>
                            </div>
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 p-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gold-100 text-gold-700"><x-icon name="bell" class="h-4 w-4" /></span>
                                <div class="text-sm"><p class="font-semibold">Payment received</p><p class="text-xs text-slate-500">November fee · Rs. 2,000</p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-center text-xs text-navy-300">Sample view</p>
            </div>
        </div>
    </section>

    {{-- ===================== Features ===================== --}}
    <section id="features" class="scroll-mt-16 bg-slate-50 py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-gold-600">What you can see</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">Everything about your child's classes.</h2>
                <p class="mt-3 text-slate-600">The information comes live from the institute's NexoraEDU system, so it is always the latest.</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as [$icon, $title, $text])
                    <div class="card p-6 transition hover:-translate-y-0.5 hover:shadow-lg">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy-900 text-gold-300"><x-icon :name="$icon" class="h-6 w-6" /></span>
                        <h3 class="mt-4 text-lg font-bold text-navy-900">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== How it works ===================== --}}
    <section id="how-it-works" class="scroll-mt-16 py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-gold-600">How it works</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">No sign-up. Your institute sets it up for you.</h2>
            </div>
            <ol class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ($steps as $i => [$title, $text])
                    <li class="relative">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-gold-300 to-gold-600 text-lg font-extrabold text-navy-900 shadow-lg shadow-gold-500/20">{{ $i + 1 }}</span>
                        <h3 class="mt-5 text-lg font-bold text-navy-900">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm text-slate-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ===================== Parent app ===================== --}}
    <section id="app" class="scroll-mt-16 px-4 sm:px-6">
        <div class="hero relative mx-auto max-w-6xl overflow-hidden rounded-[32px] px-6 py-14 sm:px-12">
            <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-gold-400/10"></div>
            <div class="relative grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-gold-300">Nexora Connect app</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Get a notification the moment something happens.</h2>
                    <p class="mt-4 text-navy-100/85">The parent app uses the same login. It tells you when your child is marked present, when a payment is received, when an installment is due and when results are out.</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        @if ($androidUrl)
                            <a href="{{ $androidUrl }}" target="_blank" rel="noopener" class="btn-gold"><x-icon name="phone" class="h-5 w-5" /> Google Play</a>
                        @endif
                        @if ($iosUrl)
                            <a href="{{ $iosUrl }}" target="_blank" rel="noopener" class="btn-gold"><x-icon name="phone" class="h-5 w-5" /> App Store</a>
                        @endif
                        @if (!$androidUrl && !$iosUrl)
                            <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-semibold ring-1 ring-white/20"><x-icon name="clock" class="h-5 w-5" /> Coming to the stores soon</span>
                        @endif
                        <a href="{{ $loginUrl }}" class="inline-flex items-center gap-1 rounded-xl px-4 py-3 text-sm font-semibold text-gold-300 hover:text-gold-200">Use it in the browser <x-icon name="chevron-right" class="h-4 w-4" /></a>
                    </div>
                </div>
                <ul class="space-y-3">
                    @foreach ([
                        ['check', 'Attendance alerts', 'Know your child reached the class.'],
                        ['cash', 'Payment receipts', 'Every fee you pay, confirmed on your phone.'],
                        ['book', 'Installment reminders', 'A reminder before a course installment is due.'],
                        ['trophy', 'Results', 'See marks as soon as they are published.'],
                    ] as [$icon, $title, $text])
                        <li class="flex items-start gap-4 rounded-2xl bg-white/6 p-4 ring-1 ring-white/10">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold-400 text-navy-900"><x-icon :name="$icon" class="h-5 w-5" /></span>
                            <span>
                                <span class="block font-bold">{{ $title }}</span>
                                <span class="block text-sm text-navy-200">{{ $text }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ===================== FAQ ===================== --}}
    <section id="faq" class="scroll-mt-16 py-20 lg:py-24">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_1.4fr]">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-gold-600">Help</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">Common questions</h2>
                <p class="mt-3 text-slate-600">For anything about your child's classes, fees or login, contact the institute office.</p>
            </div>
            <div class="space-y-3">
                @foreach ($faqs as [$q, $a])
                    <details class="group card p-5 open:shadow-md">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-navy-900">
                            {{ $q }}
                            <x-icon name="plus" class="h-5 w-5 shrink-0 text-gold-600 transition group-open:rotate-45" />
                        </summary>
                        <p class="mt-3 text-sm text-slate-600">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Support / institutes ===================== --}}
    <section class="px-4 pb-20 sm:px-6 lg:pb-24">
        <div class="card mx-auto flex max-w-6xl flex-col gap-6 p-8 md:flex-row md:items-center">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-navy-900 text-gold-300"><x-icon name="building" class="h-7 w-7" /></span>
            <div class="flex-1">
                <h2 class="text-xl font-extrabold text-navy-900">Help with the portal, or run an institute?</h2>
                <p class="mt-1 text-sm text-slate-600">NexoraIT builds NexoraEDU: attendance, fees, exams, courses, staff salaries and this parent hub.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="tel:{{ $supportDigits }}" class="btn-primary"><x-icon name="phone" class="h-5 w-5" /> {{ $supportPhone }}</a>
                <a href="{{ $whatsApp }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700"><x-icon name="chat" class="h-5 w-5" /> WhatsApp</a>
                @if ($supportEmail)
                    <a href="mailto:{{ $supportEmail }}" class="btn-light">{{ $supportEmail }}</a>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection
