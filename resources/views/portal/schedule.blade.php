@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Classes')
@section('heading', 'Class schedule')
@section('subheading', 'Upcoming classes, and the ones that already happened.')

@section('content')
    @php
        $row = function ($item) {
            return [
                'date' => Fmt::carbon($item['class_date'] ?? null),
                'class' => $item['student_class']['class_name'] ?? 'Class',
                'teacher' => $item['student_class']['teacher']['full_name'] ?? '',
                'category' => $item['class_category_fee']['category']['category_name'] ?? '',
                'hall' => $item['hall']['hall_name'] ?? $item['hall']['name'] ?? null,
                'start' => Fmt::time($item['start_time'] ?? null),
                'end' => Fmt::time($item['end_time'] ?? null),
                'status' => $item['status'] ?? 'scheduled',
                'note' => $item['note'] ?? null,
            ];
        };
    @endphp

    <section>
        <h2 class="mb-3 font-bold">Coming up</h2>
        <div class="space-y-3">
            @forelse ($upcoming as $item)
                @php $c = $row($item); $cancelled = $c['status'] === 'cancelled'; @endphp
                <div class="flex gap-4 rounded-2xl border bg-white p-4 {{ $cancelled ? 'border-red-200' : 'border-stone-200' }}">
                    <div class="w-14 shrink-0 rounded-xl {{ $c['date']?->isToday() ? 'bg-red-600 text-white' : 'bg-stone-100' }} py-2 text-center">
                        <p class="text-[11px] font-semibold uppercase opacity-80">{{ $c['date']?->format('D') }}</p>
                        <p class="text-xl font-extrabold leading-none">{{ $c['date']?->format('d') }}</p>
                        <p class="text-[11px] font-semibold opacity-80">{{ $c['date']?->format('M') }}</p>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold {{ $cancelled ? 'line-through text-gray-500' : '' }}">{{ $c['class'] }}</p>
                        <p class="text-sm text-gray-600">{{ $c['start'] }} - {{ $c['end'] }}@if ($c['hall']) · {{ $c['hall'] }}@endif</p>
                        <p class="text-xs text-gray-500">{{ $c['category'] }}@if ($c['teacher']) · {{ $c['teacher'] }}@endif</p>
                        @if ($cancelled)
                            <p class="mt-1 text-xs font-semibold text-red-600">Cancelled{{ $c['note'] ? ': ' . $c['note'] : '' }}</p>
                        @elseif ($c['note'])
                            <p class="mt-1 text-xs text-gray-600">{{ $c['note'] }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-6 text-center text-sm text-gray-500">No upcoming classes scheduled yet.</div>
            @endforelse
        </div>
    </section>

    @if ($past->isNotEmpty())
        <section class="mt-6">
            <h2 class="mb-3 font-bold">Recent classes</h2>
            <ul class="divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white text-sm">
                @foreach ($past as $item)
                    @php $c = $row($item); @endphp
                    <li class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold">{{ $c['class'] }}</p>
                            <p class="text-xs text-gray-500">{{ $c['date']?->format('D, d M') }} · {{ $c['start'] }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $c['status'] === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-stone-100 text-gray-600' }}">{{ $c['status'] }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
