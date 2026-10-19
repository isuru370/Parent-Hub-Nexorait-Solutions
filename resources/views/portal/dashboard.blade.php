@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Home')

@section('content')
    @php
        $s = array_merge($student ?? [], $dashboard['student'] ?? []);
        $name = $s['initial_name'] ?? $s['full_name'] ?? 'Student';
        $studentCode = $s['custom_id'] ?? $s['temporary_qr_code'] ?? $s['temporary_id'] ?? '-';
        $photo = $s['img_url'] ?? '';
        $photo = filter_var($photo, FILTER_VALIDATE_URL) ? $photo : null;
        $todayClasses = $dashboard['today_classes'] ?? [];
        $unread = (int) ($dashboard['notification']['unread_count'] ?? 0);
        $latestUnread = $dashboard['notification']['latest_unread'] ?? [];
        $paidCount = collect($paymentClasses)->where('is_current_month_paid', true)->count();
        $classCount = count($paymentClasses);
    @endphp

    {{-- Student card --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 to-orange-500 p-5 text-white shadow-lg shadow-red-500/20 sm:p-6">
        <div class="flex items-center gap-4">
            @if ($photo)
                <img src="{{ $photo }}" alt="" class="h-20 w-20 shrink-0 rounded-2xl object-cover ring-4 ring-white/25">
            @else
                <span class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-white/15 text-2xl font-extrabold ring-4 ring-white/20">{{ Fmt::initials($name) }}</span>
            @endif
            <div class="min-w-0">
                <p class="truncate text-xl font-extrabold sm:text-2xl">{{ $name }}</p>
                <p class="mt-0.5 text-sm text-white/85">Grade {{ $s['grade'] ?? $s['grade_name'] ?? '-' }}</p>
                <p class="mt-2 inline-flex items-center gap-2 rounded-lg bg-white/15 px-2.5 py-1 font-mono text-sm font-semibold tracking-wide">{{ $studentCode }}</p>
            </div>
        </div>
        <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
    </section>

    @if ($notice)
        <div class="mt-4 flex gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
            <x-icon name="info" class="h-5 w-5 shrink-0" />
            <p>{{ $notice }}</p>
        </div>
    @endif

    {{-- At a glance --}}
    <div class="mt-4 grid grid-cols-3 gap-3">
        <a href="{{ route('portal.attendance') }}" class="rounded-2xl border border-stone-200 bg-white p-4 hover:border-red-200">
            <p class="text-xs font-semibold text-gray-500">Attendance</p>
            <p class="mt-1 text-2xl font-extrabold">{{ isset($attendanceSummary['attendance_percentage']) ? Fmt::number($attendanceSummary['attendance_percentage']) . '%' : '-' }}</p>
            <p class="text-xs text-gray-500">{{ $attendanceSummary ? ($attendanceSummary['present_classes'] ?? 0) . ' of ' . ($attendanceSummary['total_classes'] ?? 0) . ' classes' : 'Not available' }}</p>
        </a>
        <a href="{{ route('portal.payments') }}" class="rounded-2xl border bg-white p-4 {{ $classCount && $paidCount < $classCount ? 'border-red-200' : 'border-stone-200' }} hover:border-red-200">
            <p class="text-xs font-semibold text-gray-500">Fees this month</p>
            <p class="mt-1 text-2xl font-extrabold {{ $classCount && $paidCount < $classCount ? 'text-red-600' : 'text-emerald-600' }}">{{ $classCount ? $paidCount . '/' . $classCount : '-' }}</p>
            <p class="text-xs text-gray-500">{{ $classCount ? 'classes paid' : 'No classes' }}</p>
        </a>
        <a href="{{ route('portal.notifications') }}" class="rounded-2xl border border-stone-200 bg-white p-4 hover:border-red-200">
            <p class="text-xs font-semibold text-gray-500">Messages</p>
            <p class="mt-1 text-2xl font-extrabold {{ $unread ? 'text-red-600' : '' }}">{{ $unread }}</p>
            <p class="text-xs text-gray-500">unread</p>
        </a>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-2">
        {{-- Today --}}
        <section>
            <h2 class="mb-3 font-bold">Today's classes</h2>
            <div class="space-y-3">
                @forelse ($todayClasses as $class)
                    @php
                        $live = $class['live_status'] ?? $class['status'] ?? '';
                        $tone = match ($live) {
                            'ongoing', 'started' => 'bg-emerald-50 text-emerald-700',
                            'completed' => 'bg-stone-100 text-gray-600',
                            'cancelled' => 'bg-red-50 text-red-700',
                            default => 'bg-sky-50 text-sky-700',
                        };
                    @endphp
                    <div class="flex items-center gap-4 rounded-2xl border border-stone-200 bg-white p-4">
                        <div class="w-16 shrink-0 text-center">
                            <p class="text-sm font-extrabold">{{ Fmt::time($class['start_time'] ?? null) }}</p>
                            <p class="text-xs text-gray-500">{{ Fmt::time($class['end_time'] ?? null) }}</p>
                        </div>
                        <div class="min-w-0 flex-1 border-l border-stone-100 pl-4">
                            <p class="truncate font-semibold">{{ $class['student_class']['class_name'] ?? 'Class' }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $class['student_class']['teacher']['full_name'] ?? '' }}</p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $tone }}">{{ str_replace('_', ' ', $live ?: 'scheduled') }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-6 text-center text-sm text-gray-500">No classes today.</div>
                @endforelse
            </div>
        </section>

        {{-- Fees --}}
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-bold">Fees for {{ now()->format('F') }}</h2>
                <a href="{{ route('portal.payments') }}" class="text-sm font-semibold text-red-600">All months</a>
            </div>
            <div class="divide-y divide-stone-100 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                @forelse ($paymentClasses as $class)
                    <div class="flex items-center justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <p class="truncate font-semibold">{{ $class['class_name'] ?? 'Class' }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $class['category_name'] ?? '' }}</p>
                        </div>
                        @if (!empty($class['is_current_month_paid']))
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><x-icon name="check" class="h-3.5 w-3.5" />Paid</span>
                        @else
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Not paid</span>
                        @endif
                    </div>
                @empty
                    <p class="p-6 text-center text-sm text-gray-500">No fee details yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    @if (!empty($latestUnread))
        <section class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-bold">New messages</h2>
                <a href="{{ route('portal.notifications') }}" class="text-sm font-semibold text-red-600">See all</a>
            </div>
            <div class="space-y-2">
                @foreach (array_slice($latestUnread, 0, 3) as $n)
                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <p class="font-semibold">{{ $n['title'] ?? 'Notification' }}</p>
                        <p class="mt-0.5 text-sm text-gray-600">{{ $n['body'] ?? $n['message'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
