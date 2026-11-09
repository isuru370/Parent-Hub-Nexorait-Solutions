@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Teachers')
@section('heading', 'Teachers')
@section('subheading', "Your child's teachers and the classes they take.")

@section('content')
    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($teachers as $t)
            @php
                $classes = collect($t['classes'] ?? [])->filter(fn ($c) => is_array($c));
                $mine = $classes->where('is_my_class', true);
                $shown = $mine->isNotEmpty() ? $mine : $classes;
            @endphp
            <section class="card overflow-hidden">
                <div class="flex items-center gap-4 p-5">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-navy-700 to-navy-900 text-lg font-extrabold text-gold-300">{{ Fmt::initials($t['full_name'] ?? 'T') }}</span>
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-lg font-bold text-navy-900">{{ $t['full_name'] ?? 'Teacher' }}</h2>
                        <p class="text-xs text-slate-500">{{ $t['custom_id'] ?? '' }}</p>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs">
                            @if (!empty($t['mobile']))<a href="tel:{{ preg_replace('/[^0-9+]/', '', $t['mobile']) }}" class="link text-xs">{{ $t['mobile'] }}</a>@endif
                            @if (!empty($t['email']))<a href="mailto:{{ $t['email'] }}" class="link truncate text-xs">{{ $t['email'] }}</a>@endif
                        </div>
                    </div>
                </div>
                <ul class="divide-y divide-slate-100 border-t border-slate-100">
                    @forelse ($shown as $c)
                        <li class="flex items-center justify-between gap-3 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold">{{ $c['class_name'] ?? 'Class' }}</p>
                                <p class="truncate text-xs text-slate-500">
                                    @if (!empty($c['grade']['grade_name']))Grade {{ $c['grade']['grade_name'] }} · @endif{{ $c['medium'] ?? '' }}
                                    @if (!empty($c['categories'])) · {{ collect($c['categories'])->pluck('category_name')->filter()->implode(', ') }}@endif
                                </p>
                            </div>
                            <span class="chip shrink-0 capitalize {{ !empty($c['is_my_class']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ !empty($c['is_my_class']) ? 'Enrolled' : ($c['class_type'] ?? '') }}</span>
                        </li>
                    @empty
                        <li class="px-5 py-4 text-sm text-slate-500">No classes.</li>
                    @endforelse
                </ul>
            </section>
        @empty
            <div class="empty md:col-span-2">No teachers yet.</div>
        @endforelse
    </div>
@endsection
