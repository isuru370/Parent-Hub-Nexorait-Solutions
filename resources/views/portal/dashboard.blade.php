@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Home')

@section('content')
    @php
        $s = array_merge($student ?? [], $dashboard['student'] ?? []);
        $name = $s['initial_name'] ?? $s['full_name'] ?? 'Student';
        $studentCode = $s['custom_id'] ?? $s['temporary_qr_code'] ?? $s['temporary_id'] ?? '-';
        $grade = $s['grade'] ?? $s['grade_name'] ?? null;
        $photo = $s['img_url'] ?? '';
        $photo = filter_var($photo, FILTER_VALIDATE_URL) ? $photo : null;
        $instituteName = $dashboard['institute']['name'] ?? $institute['name'] ?? null;

        $todayClasses = $dashboard['today_classes'] ?? [];
        $weekClasses = collect($dashboard['this_week_classes'] ?? [])
            ->reject(fn ($c) => (bool) Fmt::carbon($c['class_date'] ?? null)?->isToday())
            ->take(6);
        $exams = array_slice($dashboard['upcoming_exams'] ?? [], 0, 4);
        $recentPayments = array_slice($dashboard['recent_payments'] ?? [], 0, 4);
        $unread = (int) ($dashboard['notification']['unread_count'] ?? 0);
        $latestUnread = $dashboard['notification']['latest_unread'] ?? [];

        $paidCount = collect($paymentClasses)->where('is_current_month_paid', true)->count();
        $classCount = count($paymentClasses);
        $attPct = isset($attendanceSummary['attendance_percentage']) ? (float) $attendanceSummary['attendance_percentage'] : null;
        $courseBalance = collect($courses)->sum(fn ($c) => (float) ($c['balance'] ?? 0));
        $courseOverdue = collect($courses)->sum(fn ($c) => (float) ($c['overdue'] ?? 0));

        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        $statusTone = fn ($live) => match ($live) {
            'ongoing', 'started' => 'bg-emerald-50 text-emerald-700',
            'completed' => 'bg-slate-100 text-slate-600',
            'cancelled' => 'bg-red-50 text-red-700',
            default => 'bg-navy-50 text-navy-700',
        };
    @endphp

    {{-- ===================== Hero ===================== --}}
    <section class="hero relative overflow-hidden rounded-3xl p-6 shadow-xl shadow-navy-900/15 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gold-400/10"></div>
        <div class="pointer-events-none absolute -bottom-24 right-24 h-56 w-56 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-center">
            <div class="flex min-w-0 flex-1 items-center gap-5">
                <span class="shrink-0 rounded-full bg-gradient-to-br from-gold-200 to-gold-600 p-[3px]">
                    @if ($photo)
                        <img src="{{ $photo }}" alt="" class="h-20 w-20 rounded-full border-2 border-white object-cover sm:h-24 sm:w-24">
                    @else
                        <span class="grid h-20 w-20 place-items-center rounded-full border-2 border-white bg-navy-50 text-2xl font-extrabold text-navy-900 sm:h-24 sm:w-24">{{ Fmt::initials($name) }}</span>
                    @endif
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-navy-100/80">{{ $greeting }}</p>
                    <h1 class="truncate text-2xl font-extrabold sm:text-3xl">{{ $name }}</h1>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if ($grade)<span class="chip bg-white/10 text-white ring-1 ring-white/15"><x-icon name="academic" class="h-3.5 w-3.5" /> Grade {{ $grade }}</span>@endif
                        <span class="chip bg-white/10 font-mono tracking-wide text-white ring-1 ring-white/15">{{ $studentCode }}</span>
                    </div>
                    @if ($instituteName)
                        <p class="mt-3 flex items-center gap-2 text-sm font-semibold text-gold-300"><x-icon name="building" class="h-4 w-4" /> {{ $instituteName }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 md:flex-col">
                <a href="{{ route('portal.id-card') }}" class="btn-gold"><x-icon name="qr" class="h-5 w-5" /> Digital ID card</a>
                <a href="{{ route('portal.profile') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold ring-1 ring-white/15 hover:bg-white/15"><x-icon name="user" class="h-5 w-5" /> Profile</a>
            </div>
        </div>
    </section>

    @if ($notice)
        <div class="mt-4 flex gap-3 rounded-2xl border border-gold-200 bg-gold-50 p-4 text-sm text-gold-700">
            <x-icon name="info" class="h-5 w-5 shrink-0" />
            <p>{{ $notice }}</p>
        </div>
    @endif

    {{-- ===================== Stat tiles ===================== --}}
    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <a href="{{ route('portal.payments') }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gold-100 text-gold-700"><x-icon name="cash" class="h-5 w-5" /></span>
                <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-slate-500" />
            </div>
            <p class="mt-3 text-xs font-semibold text-slate-500">Fees for {{ now()->format('F') }}</p>
            <p class="mt-0.5 text-2xl font-extrabold {{ $classCount && $paidCount < $classCount ? 'text-red-600' : 'text-emerald-600' }}">{{ $classCount ? $paidCount . '/' . $classCount : '-' }}</p>
            <p class="text-xs text-slate-500">{{ $classCount ? 'classes paid' : 'No classes' }}</p>
        </a>
        <a href="{{ route('portal.attendance') }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-navy-50 text-navy-700"><x-icon name="clipboard" class="h-5 w-5" /></span>
                <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-slate-500" />
            </div>
            <p class="mt-3 text-xs font-semibold text-slate-500">Attendance</p>
            <p class="mt-0.5 text-2xl font-extrabold text-navy-900">{{ $attPct !== null ? Fmt::number($attPct) . '%' : '-' }}</p>
            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ ($attPct ?? 0) >= 80 ? 'bg-emerald-500' : (($attPct ?? 0) >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $attPct ?? 0) }}%"></div></div>
        </a>
        <a href="{{ route('portal.notifications') }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-violet-700"><x-icon name="bell" class="h-5 w-5" /></span>
                <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-slate-500" />
            </div>
            <p class="mt-3 text-xs font-semibold text-slate-500">New messages</p>
            <p class="mt-0.5 text-2xl font-extrabold {{ $unread ? 'text-violet-700' : 'text-navy-900' }}">{{ $unread }}</p>
            <p class="text-xs text-slate-500">unread</p>
        </a>
        @if ($courses)
            <a href="{{ route('portal.courses') }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><x-icon name="book" class="h-5 w-5" /></span>
                    <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-slate-500" />
                </div>
                <p class="mt-3 text-xs font-semibold text-slate-500">Course balance</p>
                <p class="mt-0.5 text-xl font-extrabold {{ $courseOverdue > 0 ? 'text-red-600' : 'text-navy-900' }}">{{ $courseBalance > 0 ? Fmt::money($courseBalance) : 'Paid' }}</p>
                <p class="text-xs {{ $courseOverdue > 0 ? 'font-semibold text-red-600' : 'text-slate-500' }}">{{ $courseOverdue > 0 ? Fmt::money($courseOverdue) . ' overdue' : count($courses) . ' course(s)' }}</p>
            </a>
        @else
            <a href="{{ route('portal.schedule') }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><x-icon name="calendar" class="h-5 w-5" /></span>
                    <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-slate-500" />
                </div>
                <p class="mt-3 text-xs font-semibold text-slate-500">Classes</p>
                <p class="mt-0.5 text-2xl font-extrabold text-navy-900">{{ (int) ($dashboard['total_classes'] ?? $classCount) }}</p>
                <p class="text-xs text-slate-500">{{ count($todayClasses) }} today</p>
            </a>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- ===================== Left (2 columns) ===================== --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Today --}}
            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-navy-900">Today's classes</h2>
                    <a href="{{ route('portal.schedule') }}" class="link">Full schedule</a>
                </div>
                <div class="space-y-3">
                    @forelse ($todayClasses as $class)
                        @php $live = $class['live_status'] ?? $class['status'] ?? 'scheduled'; @endphp
                        <div class="card flex items-center gap-4 p-4">
                            <div class="w-20 shrink-0 rounded-xl bg-navy-900 py-2 text-center text-white">
                                <p class="text-sm font-extrabold">{{ Fmt::time($class['start_time'] ?? null) }}</p>
                                <p class="text-[11px] text-navy-200">{{ Fmt::time($class['end_time'] ?? null) }}</p>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold">{{ $class['student_class']['class_name'] ?? 'Class' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $class['student_class']['subject']['subject_name'] ?? '' }}@if (!empty($class['student_class']['teacher']['full_name'])) · {{ $class['student_class']['teacher']['full_name'] }}@endif</p>
                            </div>
                            <span class="chip capitalize {{ $statusTone($live) }}">{{ str_replace('_', ' ', $live) }}</span>
                        </div>
                    @empty
                        <div class="empty">No classes today.</div>
                    @endforelse
                </div>
            </section>

            {{-- Fees this month --}}
            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-navy-900">Fees for {{ now()->format('F') }}</h2>
                    <a href="{{ route('portal.payments') }}" class="link">All months</a>
                </div>
                <div class="card divide-y divide-slate-100 overflow-hidden">
                    @forelse ($paymentClasses as $class)
                        <div class="flex items-center justify-between gap-3 p-4">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $class['class_name'] ?? 'Class' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $class['category_name'] ?? '' }}@if (!empty($class['monthly_fee'])) · {{ Fmt::money($class['monthly_fee']) }} / month @endif</p>
                            </div>
                            @if (!empty($class['is_current_month_paid']))
                                <span class="chip bg-emerald-50 text-emerald-700"><x-icon name="check" class="h-3.5 w-3.5" />Paid</span>
                            @else
                                <span class="chip bg-red-50 text-red-700">Not paid</span>
                            @endif
                        </div>
                    @empty
                        <p class="p-6 text-center text-sm text-slate-500">No fee details yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- Courses --}}
            @if ($courses)
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-navy-900">My courses</h2>
                        <a href="{{ route('portal.courses') }}" class="link">View all</a>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($courses as $c)
                            <a href="{{ route('portal.course', $c['course_id'] ?? 0) }}" class="card flex items-center gap-4 p-4 hover:shadow-md">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gold-100 text-gold-700"><x-icon name="book" class="h-5 w-5" /></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold">{{ $c['name'] ?? 'Course' }}</p>
                                    @if ((float) ($c['overdue'] ?? 0) > 0)
                                        <p class="text-xs font-semibold text-red-600">Overdue {{ Fmt::money($c['overdue']) }}</p>
                                    @elseif ((float) ($c['balance'] ?? 0) <= 0)
                                        <p class="text-xs font-semibold text-emerald-600">Fully paid</p>
                                    @elseif (!empty($c['next_due_date']))
                                        <p class="text-xs text-slate-500">Next {{ Fmt::money($c['next_due_amount'] ?? 0) }} on {{ Fmt::date($c['next_due_date']) }}</p>
                                    @else
                                        <p class="text-xs text-slate-500">Balance {{ Fmt::money($c['balance'] ?? 0) }}</p>
                                    @endif
                                </div>
                                <x-icon name="chevron-right" class="h-5 w-5 text-slate-300" />
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- This week --}}
            @if ($weekClasses->isNotEmpty())
                <section>
                    <h2 class="mb-3 text-lg font-bold text-navy-900">Later this week</h2>
                    <div class="card divide-y divide-slate-100 overflow-hidden">
                        @foreach ($weekClasses as $class)
                            @php $d = Fmt::carbon($class['class_date'] ?? null); @endphp
                            <div class="flex items-center gap-4 p-4">
                                <div class="w-12 shrink-0 rounded-xl bg-slate-100 py-1.5 text-center">
                                    <p class="text-[11px] font-semibold uppercase text-slate-500">{{ $d?->format('D') }}</p>
                                    <p class="text-lg font-extrabold leading-none text-navy-900">{{ $d?->format('d') }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold">{{ $class['student_class']['class_name'] ?? 'Class' }}</p>
                                    <p class="text-xs text-slate-500">{{ Fmt::time($class['start_time'] ?? null) }} - {{ Fmt::time($class['end_time'] ?? null) }}</p>
                                </div>
                                @if (($class['status'] ?? '') === 'cancelled')<span class="chip bg-red-50 text-red-700">Cancelled</span>@endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- ===================== Right column ===================== --}}
        <div class="space-y-6">
            {{-- Quick links --}}
            <section class="card p-4">
                <h2 class="mb-3 font-bold text-navy-900">Quick links</h2>
                <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold">
                    @foreach ([['portal.exams', 'trophy', 'Results'], ['portal.schedule', 'calendar', 'Schedule'], ['portal.teachers', 'users', 'Teachers'], ['portal.id-card', 'qr', 'Digital ID'], ['portal.notifications', 'bell', 'Messages'], ['portal.profile', 'user', 'Profile']] as [$route, $icon, $label])
                        <a href="{{ route($route) }}" class="rounded-xl bg-slate-50 px-2 py-3 text-slate-700 hover:bg-navy-50 hover:text-navy-900">
                            <x-icon :name="$icon" class="mx-auto mb-1 h-5 w-5 text-navy-700" /> {{ $label }}
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Upcoming exams --}}
            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-navy-900">Upcoming exams</h2>
                    <a href="{{ route('portal.exams') }}" class="link">All</a>
                </div>
                <div class="space-y-2">
                    @forelse ($exams as $exam)
                        @php $d = Fmt::carbon($exam['exam_date'] ?? null); @endphp
                        <div class="card flex items-center gap-3 p-3">
                            <div class="w-12 shrink-0 rounded-xl bg-gold-100 py-1.5 text-center text-gold-700">
                                <p class="text-lg font-extrabold leading-none">{{ $d?->format('d') }}</p>
                                <p class="text-[11px] font-semibold">{{ $d?->format('M') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold">{{ $exam['title'] ?? 'Exam' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $exam['student_class']['class_name'] ?? '' }} · {{ Fmt::time($exam['start_time'] ?? null) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty p-5">No exams coming up.</div>
                    @endforelse
                </div>
            </section>

            {{-- Recent payments --}}
            @if ($recentPayments)
                <section>
                    <h2 class="mb-3 text-lg font-bold text-navy-900">Recent payments</h2>
                    <div class="card divide-y divide-slate-100 overflow-hidden">
                        @foreach ($recentPayments as $p)
                            <div class="flex items-center justify-between gap-3 p-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold">{{ $p['class_name'] ?? 'Class fee' }}</p>
                                    <p class="text-xs text-slate-500">{{ Fmt::month($p['payment_month'] ?? '', 'F Y') }} · {{ Fmt::date($p['paid_at'] ?? null, 'd M') }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-bold text-emerald-700">{{ Fmt::money($p['amount'] ?? 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Messages --}}
            @if (!empty($latestUnread))
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-navy-900">New messages</h2>
                        <a href="{{ route('portal.notifications') }}" class="link">See all</a>
                    </div>
                    <div class="space-y-2">
                        @foreach (array_slice($latestUnread, 0, 3) as $n)
                            <div class="card border-l-4 border-l-gold-400 p-4">
                                <p class="font-semibold">{{ $n['title'] ?? 'Notification' }}</p>
                                <p class="mt-0.5 whitespace-pre-line text-sm text-slate-600">{{ $n['body'] ?? $n['message'] ?? '' }}</p>
                                <p class="mt-1 text-[11px] text-slate-400">{{ Fmt::date($n['created_at'] ?? null, 'd M, g:i A') }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
