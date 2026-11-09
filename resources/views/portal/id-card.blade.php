@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Digital ID')
@section('heading', 'Digital ID card')
@section('subheading', 'Show this QR code at the institute if the ID card is not with the student.')

@section('content')
    @php
        $s = $profile['student'] ?? [];
        $card = $profile['card'] ?? [];
        $name = ($s['initial_name'] ?? '') ?: ($s['full_name'] ?? 'Student');
        $photo = filter_var($s['img_url'] ?? '', FILTER_VALIDATE_URL) ? $s['img_url'] : null;
        $instName = $profile['institute']['name'] ?? $institute['name'] ?? 'Institute';
        $temporary = ($card['qr_type'] ?? null) === 'temporary';
    @endphp

    <div class="mx-auto max-w-md">
        <section class="hero relative overflow-hidden rounded-[28px] shadow-2xl shadow-navy-900/25 print:shadow-none">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-gold-400/10"></div>
            <div class="relative flex items-center gap-3 px-6 pt-6">
                <span class="h-9 w-1.5 rounded-full bg-gradient-to-b from-gold-300 to-gold-600"></span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-extrabold uppercase tracking-wide">{{ $instName }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-gold-300">Student identity card</p>
                </div>
            </div>
            <div class="relative mt-5 flex items-center gap-4 px-6">
                <span class="shrink-0 rounded-full bg-gradient-to-br from-gold-200 to-gold-600 p-[3px]">
                    @if ($photo)
                        <img src="{{ $photo }}" alt="" class="h-16 w-16 rounded-full border-2 border-white object-cover">
                    @else
                        <span class="grid h-16 w-16 place-items-center rounded-full border-2 border-white bg-navy-50 text-xl font-extrabold text-navy-900">{{ Fmt::initials($name) }}</span>
                    @endif
                </span>
                <div class="min-w-0">
                    <p class="truncate text-lg font-bold">{{ $name }}</p>
                    <p class="text-sm text-navy-200">{{ $s['custom_id'] ?? '' }}@if (!empty($s['grade_name'])) · Grade {{ $s['grade_name'] }}@endif</p>
                </div>
            </div>
            <div class="relative m-4 mt-5 rounded-3xl bg-white p-6 text-center text-navy-900">
                @if (!empty($card['qr_svg']))
                    <img src="{{ $card['qr_svg'] }}" alt="QR code {{ $card['qr_code'] }}" class="mx-auto h-56 w-56">
                    <p class="mt-3 font-mono text-xl font-extrabold tracking-[0.2em]">{{ $card['qr_code'] }}</p>
                    <span class="chip mt-2 {{ $temporary ? 'bg-gold-100 text-gold-700' : 'bg-navy-50 text-navy-700' }}">
                        {{ $temporary ? 'Temporary card' . (!empty($card['expires_at']) ? ' · valid until ' . Fmt::date($card['expires_at']) : '') : 'Permanent card' }}
                    </span>
                @elseif (!empty($card['qr_code']))
                    <p class="font-mono text-2xl font-extrabold tracking-[0.2em]">{{ $card['qr_code'] }}</p>
                    <p class="mt-2 text-xs text-slate-500">Your institute needs an update to show the QR code here.</p>
                @else
                    <x-icon name="qr" class="mx-auto h-14 w-14 text-slate-300" />
                    <p class="mt-2 font-bold">No active ID card yet</p>
                    <p class="text-xs text-slate-500">Please contact the institute.</p>
                @endif
            </div>
        </section>

        <div class="mt-5 flex justify-center gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="btn-light"><x-icon name="printer" class="h-5 w-5" /> Print</button>
            <a href="{{ route('portal.profile') }}" class="btn-primary"><x-icon name="user" class="h-5 w-5" /> Profile</a>
        </div>
    </div>
@endsection
