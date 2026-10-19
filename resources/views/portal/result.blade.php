@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Result')

@section('content')
    @php
        $exam = $result['exam'] ?? [];
        $mine = $result['student_result'] ?? null;
        $top = $result['top_rankings'] ?? [];
        $myId = $student['student_id'] ?? null;
        $gradeTone = ['A' => 'text-emerald-600', 'B' => 'text-sky-600', 'C' => 'text-violet-600', 'S' => 'text-amber-600', 'F' => 'text-red-600'];
    @endphp

    <a href="{{ route('portal.exams') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-gray-600 hover:text-gray-900">
        <x-icon name="arrow-left" class="h-4 w-4" /> Exams
    </a>

    <h1 class="text-2xl font-extrabold">{{ $exam['title'] ?? 'Exam' }}</h1>
    <p class="mt-1 text-sm text-gray-500">
        {{ $exam['student_class']['class_name'] ?? '' }} · {{ Fmt::date($exam['exam_date'] ?? null, 'd M Y') }}
        @if (!empty($exam['teacher']['full_name'])) · {{ $exam['teacher']['full_name'] }}@endif
    </p>

    @if (!$mine)
        <div class="mt-5 rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-gray-500">Marks are not entered yet.</div>
    @elseif (!empty($mine['is_absent']))
        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center font-semibold text-amber-800">Absent for this exam</div>
    @else
        <section class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="col-span-2 rounded-3xl bg-gradient-to-br from-red-600 to-orange-500 p-5 text-white sm:col-span-2">
                <p class="text-sm font-semibold text-white/85">Marks</p>
                <p class="mt-1 text-5xl font-extrabold">{{ Fmt::number($mine['marks'] ?? 0) }}<span class="text-2xl text-white/80"> / {{ Fmt::number($mine['max_marks'] ?? 100) }}</span></p>
                <p class="mt-1 text-sm text-white/85">{{ Fmt::number($mine['percentage'] ?? 0) }}%</p>
            </div>
            <div class="rounded-2xl border border-stone-200 bg-white p-4">
                <p class="text-xs font-semibold text-gray-500">Grade</p>
                <p class="mt-1 text-4xl font-extrabold {{ $gradeTone[$mine['grade'] ?? ''] ?? '' }}">{{ $mine['grade'] ?? '-' }}</p>
                <p class="text-xs capitalize text-gray-500">{{ $mine['status'] ?? '' }}</p>
            </div>
            <div class="rounded-2xl border border-stone-200 bg-white p-4">
                <p class="text-xs font-semibold text-gray-500">Class rank</p>
                <p class="mt-1 text-4xl font-extrabold">{{ $mine['rank'] ?? '-' }}</p>
            </div>
        </section>
        @if (!empty($mine['remark']))
            <p class="mt-3 rounded-2xl border border-stone-200 bg-white p-4 text-sm"><span class="font-semibold">Teacher's remark:</span> {{ $mine['remark'] }}</p>
        @endif
    @endif

    @if ($top)
        <section class="mt-6">
            <h2 class="mb-2 font-bold">Top of the class</h2>
            <ol class="divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                @foreach ($top as $row)
                    @php $isMe = $myId && ($row['student']['id'] ?? null) == $myId; @endphp
                    <li class="flex items-center gap-4 px-4 py-3 {{ $isMe ? 'bg-red-50' : '' }}">
                        <span class="grid h-8 w-8 place-items-center rounded-full text-sm font-extrabold {{ ($row['rank'] ?? 9) <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-stone-100' }}">{{ $row['rank'] ?? '-' }}</span>
                        <span class="flex-1 font-semibold">{{ $row['student']['initial_name'] ?? 'Student' }} @if ($isMe)<span class="text-xs font-semibold text-red-600">(your child)</span>@endif</span>
                        <span class="text-sm font-semibold">{{ !empty($row['is_absent']) ? 'Absent' : Fmt::number($row['marks'] ?? 0) }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
@endsection
