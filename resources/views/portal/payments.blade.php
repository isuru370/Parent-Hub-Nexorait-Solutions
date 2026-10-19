@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Fees')
@section('heading', 'Class fees')
@section('subheading', 'Green = paid, red = not paid. The outlined month is this month.')

@section('content')
    @php
        $summary = $payments['summary'] ?? [];
        $classes = $payments['classes'] ?? [];
    @endphp

    <div class="grid grid-cols-2 gap-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-semibold text-gray-500">Total paid</p>
            <p class="mt-1 text-xl font-extrabold">{{ Fmt::money($summary['total_paid_amount'] ?? 0) }}</p>
            <p class="text-xs text-gray-500">{{ $summary['total_payments'] ?? 0 }} payments</p>
        </div>
        <div class="rounded-2xl border bg-white p-4 {{ ($summary['total_unpaid_months'] ?? 0) > 0 ? 'border-red-200' : 'border-stone-200' }}">
            <p class="text-xs font-semibold text-gray-500">Months not paid</p>
            <p class="mt-1 text-xl font-extrabold {{ ($summary['total_unpaid_months'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $summary['total_unpaid_months'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">across all classes</p>
        </div>
    </div>

    <div class="mt-5 space-y-4">
        @forelse ($classes as $class)
            <section class="rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="truncate font-bold">{{ $class['class_name'] ?? 'Class' }}</h2>
                        <p class="truncate text-xs text-gray-500">{{ $class['category_name'] ?? '' }}@if (!empty($class['teacher'])) · {{ $class['teacher'] }}@endif</p>
                    </div>
                    @if (!empty($class['is_current_month_paid']))
                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ now()->format('F') }} paid</span>
                    @else
                        <span class="shrink-0 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">{{ now()->format('F') }} not paid</span>
                    @endif
                </div>

                <ul class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-6">
                    @foreach ($class['month_status'] ?? [] as $month)
                        @php $paid = !empty($month['is_paid']); @endphp
                        <li class="rounded-xl px-2 py-2 text-center text-xs font-semibold
                            {{ $paid ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}
                            {{ !empty($month['is_current']) ? 'ring-2 ring-offset-1 ' . ($paid ? 'ring-emerald-400' : 'ring-red-400') : '' }}"
                            title="{{ $month['month_name'] ?? '' }}: {{ $paid ? 'paid' : 'not paid' }}">
                            {{ Fmt::month($month['month'] ?? '', 'M') }}
                            <span class="block text-[10px] font-medium opacity-70">{{ Fmt::month($month['month'] ?? '', 'Y') }}</span>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-3 text-xs text-gray-500">
                    {{ $class['payment_count'] ?? 0 }} payments, {{ Fmt::money($class['total_paid_amount'] ?? 0) }} in total.
                    @if (($class['unpaid_months_count'] ?? 0) > 0)
                        <span class="font-semibold text-red-600">{{ $class['unpaid_months_count'] }} month(s) not paid.</span>
                    @endif
                </p>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-gray-500">No class fees yet.</div>
        @endforelse
    </div>
@endsection
