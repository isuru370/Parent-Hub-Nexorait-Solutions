@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Attendance')
@section('heading', 'Attendance')
@section('subheading', 'Classes your child attended, by class.')

@section('content')
    @php
        $summary = $attendance['summary'] ?? [];
        $classes = $attendance['classes'] ?? [];
        $pct = (float) ($summary['attendance_percentage'] ?? 0);
        $barColor = fn ($p) => $p >= 80 ? 'bg-emerald-500' : ($p >= 60 ? 'bg-amber-500' : 'bg-red-500');
    @endphp

    <section class="rounded-3xl border border-stone-200 bg-white p-5">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Overall</p>
                <p class="text-4xl font-extrabold">{{ Fmt::number($pct) }}%</p>
            </div>
            <div class="flex gap-6 text-right text-sm">
                <div><p class="text-xl font-extrabold text-emerald-600">{{ $summary['present_classes'] ?? 0 }}</p><p class="text-gray-500">Present</p></div>
                <div><p class="text-xl font-extrabold text-red-600">{{ $summary['absent_classes'] ?? 0 }}</p><p class="text-gray-500">Absent</p></div>
            </div>
        </div>
        <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-stone-100" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
            <div class="h-full rounded-full {{ $barColor($pct) }}" style="width: {{ min(100, $pct) }}%"></div>
        </div>
    </section>

    <div class="mt-5 space-y-4">
        @forelse ($classes as $class)
            @php $cp = (float) ($class['attendance_percentage'] ?? 0); $history = $class['attendance_history'] ?? []; @endphp
            <details class="group overflow-hidden rounded-2xl border border-stone-200 bg-white" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none items-center gap-4 p-4">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold">{{ $class['class_name'] ?? 'Class' }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $class['category_name'] ?? '' }}@if (!empty($class['teacher'])) · {{ $class['teacher'] }}@endif</p>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-stone-100"><div class="h-full {{ $barColor($cp) }}" style="width: {{ min(100, $cp) }}%"></div></div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-extrabold">{{ Fmt::number($cp) }}%</p>
                        <p class="text-xs text-gray-500">{{ $class['present_classes'] ?? 0 }}/{{ ($class['present_classes'] ?? 0) + ($class['absent_classes'] ?? 0) }}</p>
                    </div>
                    <x-icon name="chevron-right" class="h-5 w-5 text-gray-400 transition group-open:rotate-90" />
                </summary>
                <ul class="divide-y divide-stone-100 border-t border-stone-100">
                    @forelse (collect($history)->sortByDesc('class_date') as $day)
                        <li class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                            <div>
                                <p class="font-semibold">{{ Fmt::date($day['class_date'] ?? null, 'D, d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ Fmt::time($day['start_time'] ?? null) }} - {{ Fmt::time($day['end_time'] ?? null) }}</p>
                            </div>
                            @if (!empty($day['is_present']))
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <x-icon name="check" class="h-3.5 w-3.5" /> Present {{ Fmt::date($day['attended_at'] ?? null, 'g:i A') !== '-' ? '· ' . Fmt::date($day['attended_at'] ?? null, 'g:i A') : '' }}
                                </span>
                            @else
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Absent</span>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-gray-500">No classes held yet.</li>
                    @endforelse
                </ul>
            </details>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-gray-500">No attendance yet.</div>
        @endforelse
    </div>
@endsection
