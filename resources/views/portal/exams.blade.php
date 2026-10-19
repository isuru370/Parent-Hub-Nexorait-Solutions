@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Exams')
@section('heading', 'Exams')
@section('subheading', 'Tap a finished exam to see the marks.')

@section('content')
    @php $classes = $exams['classes'] ?? []; @endphp

    <div class="space-y-5">
        @forelse ($classes as $class)
            <section>
                <h2 class="mb-2 font-bold">{{ $class['class_name'] ?? 'Class' }} <span class="text-sm font-normal text-gray-500">{{ $class['subject_name'] ?? '' }}</span></h2>
                <div class="divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                    @forelse ($class['exams'] ?? [] as $exam)
                        @php
                            $status = $exam['status'] ?? 'scheduled';
                            $done = $status === 'completed';
                            $tone = match ($status) {
                                'completed' => 'bg-emerald-50 text-emerald-700',
                                'ongoing' => 'bg-amber-50 text-amber-700',
                                'cancelled' => 'bg-stone-100 text-gray-500 line-through',
                                default => 'bg-sky-50 text-sky-700',
                            };
                        @endphp
                        <a @if ($done) href="{{ route('portal.result', $exam['exam_id']) }}" @endif
                            class="flex items-center gap-4 p-4 {{ $done ? 'hover:bg-stone-50' : '' }}">
                            <div class="w-12 shrink-0 rounded-xl bg-stone-100 py-1.5 text-center">
                                <p class="text-lg font-extrabold leading-none">{{ Fmt::date($exam['exam_date'] ?? null, 'd') }}</p>
                                <p class="text-[11px] font-semibold text-gray-500">{{ Fmt::date($exam['exam_date'] ?? null, 'M') }}</p>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold">{{ $exam['title'] ?? 'Exam' }}</p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ Fmt::time($exam['start_time'] ?? null) }} - {{ Fmt::time($exam['end_time'] ?? null) }}
                                    @if (!empty($exam['hall']['name'])) · {{ $exam['hall']['name'] }}@endif
                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $tone }}">{{ $done ? 'Results' : $status }}</span>
                            @if ($done)<x-icon name="chevron-right" class="h-5 w-5 text-gray-400" />@endif
                        </a>
                    @empty
                        <p class="p-5 text-center text-sm text-gray-500">No exams for this class yet.</p>
                    @endforelse
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-gray-500">No exams yet.</div>
        @endforelse
    </div>
@endsection
