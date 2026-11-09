@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', $course['name'] ?? 'Course')

@section('content')
    @php
        $f = $course['fees'] ?? [];
        $total = (float) ($f['total_fee'] ?? 0);
        $paid = (float) ($f['paid'] ?? 0);
        $pct = $total > 0 ? min(100, $paid / $total * 100) : 100;
        $att = $course['attendance'] ?? [];
        $statusLabel = ['upcoming' => 'Starts soon', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'inactive' => 'Inactive'];
        $instTone = [
            'paid' => ['Paid', 'bg-emerald-50 text-emerald-700'],
            'partly_paid' => ['Partly paid', 'bg-gold-100 text-gold-700'],
            'overdue' => ['Overdue', 'bg-red-50 text-red-700'],
            'due' => ['Due', 'bg-navy-50 text-navy-700'],
        ];
    @endphp

    <a href="{{ route('portal.courses') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-navy-900">
        <x-icon name="arrow-left" class="h-4 w-4" /> My Courses
    </a>

    @if ($course)
        <section class="hero relative overflow-hidden rounded-3xl p-6 sm:p-8">
            <div class="pointer-events-none absolute -right-14 -top-14 h-52 w-52 rounded-full bg-gold-400/10"></div>
            <div class="relative flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gold-300">{{ $course['code'] ?? 'Course' }}</p>
                    <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $course['name'] ?? 'Course' }}</h1>
                </div>
                <span class="chip bg-gold-400 text-navy-900">{{ $statusLabel[$course['status'] ?? ''] ?? ucfirst($course['status'] ?? '') }}</span>
            </div>
            <dl class="relative mt-5 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-navy-200">Dates</dt><dd class="font-semibold">{{ Fmt::date($course['start_date'] ?? null) }} – {{ Fmt::date($course['end_date'] ?? null) }}</dd></div>
                <div><dt class="text-navy-200">Class days</dt><dd class="font-semibold">{{ $course['schedule'] ?: '-' }}</dd></div>
                <div><dt class="text-navy-200">Branch</dt><dd class="font-semibold">{{ $course['branch'] ?? '-' }}</dd></div>
                <div><dt class="text-navy-200">Joined</dt><dd class="font-semibold">{{ Fmt::date($course['enrolled_at'] ?? null) }}</dd></div>
            </dl>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- Fee --}}
                <section class="card p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-navy-900">Course fee</h2>
                        <span class="chip bg-navy-50 text-navy-700">{{ ($f['payment_plan'] ?? '') === 'installment' ? 'Installments' : 'Full payment' }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Course fee</p><p class="font-extrabold">{{ Fmt::money($total) }}</p></div>
                        <div class="rounded-xl bg-emerald-50 p-3"><p class="text-xs text-emerald-700">Paid</p><p class="font-extrabold text-emerald-700">{{ Fmt::money($paid) }}</p></div>
                        <div class="rounded-xl bg-navy-50 p-3"><p class="text-xs text-navy-700">Balance</p><p class="font-extrabold text-navy-900">{{ Fmt::money($f['balance'] ?? 0) }}</p></div>
                        <div class="rounded-xl {{ (float) ($f['overdue'] ?? 0) > 0 ? 'bg-red-50' : 'bg-slate-50' }} p-3"><p class="text-xs {{ (float) ($f['overdue'] ?? 0) > 0 ? 'text-red-700' : 'text-slate-500' }}">Overdue</p><p class="font-extrabold {{ (float) ($f['overdue'] ?? 0) > 0 ? 'text-red-700' : '' }}">{{ Fmt::money($f['overdue'] ?? 0) }}</p></div>
                    </div>
                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ (float) ($f['overdue'] ?? 0) > 0 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div></div>
                    @if (!empty($f['special_fee']) && (float) ($f['discount'] ?? 0) > 0)
                        <p class="mt-3 text-xs text-slate-500">Special fee: {{ Fmt::money($f['discount']) }} less than the usual {{ Fmt::money($f['standard_fee'] ?? 0) }}.</p>
                    @endif
                </section>

                {{-- Installments --}}
                @if (!empty($f['installments']))
                    <section class="card overflow-hidden">
                        <h2 class="border-b border-slate-100 px-5 py-4 text-lg font-bold text-navy-900">Installments</h2>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($f['installments'] as $i)
                                @php [$label, $tone] = $instTone[$i['status'] ?? 'due'] ?? $instTone['due']; @endphp
                                <li class="flex items-center gap-4 px-5 py-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-100 text-sm font-extrabold text-navy-900">{{ $i['installment_no'] ?? '' }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold">{{ Fmt::money($i['amount'] ?? 0) }}</p>
                                        <p class="text-xs text-slate-500">Due {{ Fmt::date($i['due_date'] ?? null) }}@if (($i['status'] ?? '') === 'partly_paid') · {{ Fmt::money($i['remaining'] ?? 0) }} left @endif</p>
                                    </div>
                                    <span class="chip {{ $tone }}">{{ $label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Attendance --}}
                <section class="card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <h2 class="text-lg font-bold text-navy-900">Attendance</h2>
                        @if ((int) ($att['held'] ?? 0) > 0)
                            <span class="chip bg-emerald-50 text-emerald-700">{{ $att['attended'] ?? 0 }}/{{ $att['held'] }} · {{ Fmt::number($att['percentage'] ?? 0) }}%</span>
                        @endif
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse ($att['history'] ?? [] as $d)
                            <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                                <span>{{ Fmt::date($d['date'] ?? null, 'D, d M Y') }} · {{ Fmt::time($d['start_time'] ?? null) }}</span>
                                @if (!empty($d['present']))
                                    <span class="chip bg-emerald-50 text-emerald-700"><x-icon name="check" class="h-3.5 w-3.5" /> Present</span>
                                @else
                                    <span class="chip bg-red-50 text-red-700">Absent</span>
                                @endif
                            </li>
                        @empty
                            <li class="px-5 py-5 text-sm text-slate-500">No class days held yet.</li>
                        @endforelse
                    </ul>
                </section>
            </div>

            <div class="space-y-6">
                {{-- Next class days --}}
                <section class="card overflow-hidden">
                    <h2 class="border-b border-slate-100 px-5 py-4 text-lg font-bold text-navy-900">Next class days</h2>
                    <ul class="divide-y divide-slate-100">
                        @forelse ($course['upcoming_sessions'] ?? [] as $s)
                            @php $d = Fmt::carbon($s['date'] ?? null); @endphp
                            <li class="flex items-center gap-3 px-5 py-3">
                                <div class="w-12 shrink-0 rounded-xl bg-navy-900 py-1.5 text-center text-white">
                                    <p class="text-lg font-extrabold leading-none">{{ $d?->format('d') }}</p>
                                    <p class="text-[11px] text-navy-200">{{ $d?->format('M') }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold">{{ $d?->format('l') }}</p>
                                    <p class="text-xs text-slate-500">{{ Fmt::time($s['start_time'] ?? null) }} - {{ Fmt::time($s['end_time'] ?? null) }}@if (!empty($s['hall'])) · {{ $s['hall'] }}@endif</p>
                                    @if (!empty($s['is_extra']))<span class="chip mt-1 bg-gold-100 text-gold-700">Extra class</span>@endif
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-5 text-sm text-slate-500">No class days scheduled.</li>
                        @endforelse
                    </ul>
                </section>

                @if (!empty($course['cancelled_sessions']))
                    <section class="card overflow-hidden">
                        <h2 class="border-b border-slate-100 px-5 py-4 text-lg font-bold text-navy-900">Cancelled class days</h2>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($course['cancelled_sessions'] as $s)
                                <li class="px-5 py-3 text-sm">
                                    <p class="font-semibold text-slate-500 line-through">{{ Fmt::date($s['date'] ?? null, 'D, d M') }} · {{ Fmt::time($s['start_time'] ?? null) }}</p>
                                    @if (!empty($s['cancel_reason']))<p class="text-xs font-semibold text-red-600">Reason: {{ $s['cancel_reason'] }}</p>@endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Payments --}}
                <section class="card overflow-hidden">
                    <h2 class="border-b border-slate-100 px-5 py-4 text-lg font-bold text-navy-900">Payments</h2>
                    <ul class="divide-y divide-slate-100">
                        @forelse ($course['payments'] ?? [] as $p)
                            <li class="flex items-center justify-between gap-3 px-5 py-3">
                                <div>
                                    <p class="text-sm font-bold text-emerald-700">{{ Fmt::money($p['amount'] ?? 0) }}</p>
                                    <p class="text-xs text-slate-500">{{ Fmt::date($p['paid_at'] ?? null) }}@if (!empty($p['receipt_number'])) · {{ $p['receipt_number'] }}@endif</p>
                                </div>
                                <x-icon name="check" class="h-5 w-5 text-emerald-600" />
                            </li>
                        @empty
                            <li class="px-5 py-5 text-sm text-slate-500">No payments yet.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>
    @endif
@endsection
