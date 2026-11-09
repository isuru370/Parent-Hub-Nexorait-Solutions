@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'My Courses')
@section('heading', 'My Courses')
@section('subheading', 'Course fees, installments, class days and attendance.')

@section('content')
    @php
        $statusLabel = ['upcoming' => 'Starts soon', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'inactive' => 'Inactive'];
    @endphp

    <div class="grid gap-5 md:grid-cols-2">
        @forelse ($courses as $c)
            @php
                $f = $c['fees'] ?? [];
                $total = (float) ($f['total_fee'] ?? 0);
                $paid = (float) ($f['paid'] ?? 0);
                $pct = $total > 0 ? min(100, $paid / $total * 100) : 100;
                $next = $f['next_installment'] ?? null;
                $att = $c['attendance'] ?? [];
            @endphp
            <a href="{{ route('portal.course', $c['course_id'] ?? 0) }}" class="card group overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="hero flex items-start justify-between gap-3 p-5">
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold">{{ $c['name'] ?? 'Course' }}</h2>
                        <p class="text-xs text-navy-200">{{ collect([$c['code'] ?? null, $c['branch'] ?? null])->filter()->implode(' · ') }}</p>
                    </div>
                    <span class="chip shrink-0 bg-gold-400 text-navy-900">{{ $statusLabel[$c['status'] ?? ''] ?? ucfirst($c['status'] ?? '') }}</span>
                </div>
                <div class="p-5">
                    <div class="flex items-baseline justify-between text-sm">
                        <span class="font-bold">Paid {{ Fmt::money($paid) }}</span>
                        <span class="text-xs text-slate-500">of {{ Fmt::money($total) }}</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ (float) ($f['overdue'] ?? 0) > 0 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div></div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ((float) ($f['balance'] ?? 0) <= 0)
                            <span class="chip bg-emerald-50 text-emerald-700">Fully paid</span>
                        @else
                            <span class="chip bg-navy-50 text-navy-700">Balance {{ Fmt::money($f['balance']) }}</span>
                        @endif
                        @if ((float) ($f['overdue'] ?? 0) > 0)
                            <span class="chip bg-red-50 text-red-700">Overdue {{ Fmt::money($f['overdue']) }}</span>
                        @elseif ($next)
                            <span class="chip bg-gold-100 text-gold-700">Next {{ Fmt::money($next['remaining'] ?? 0) }} · {{ Fmt::date($next['due_date'] ?? null, 'd M') }}</span>
                        @endif
                        @if ((int) ($att['held'] ?? 0) > 0)
                            <span class="chip bg-emerald-50 text-emerald-700">Attendance {{ Fmt::number($att['percentage'] ?? 0) }}%</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="empty md:col-span-2">
                <x-icon name="book" class="mx-auto mb-2 h-8 w-8 text-slate-300" />
                {{ $coursesEnabled ? 'No courses yet. Courses the student joins at the institute will show here.' : 'This institute does not have courses.' }}
            </div>
        @endforelse
    </div>
@endsection
