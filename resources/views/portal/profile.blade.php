@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Profile')
@section('heading', 'Profile')

@section('content')
    @php
        $s = $student ?? [];
        $rows = [
            'Full name' => $s['full_name'] ?? null,
            'Student ID' => $s['custom_id'] ?? $s['temporary_id'] ?? null,
            'Grade' => $s['grade_name'] ?? null,
            'School' => $s['student_school'] ?? null,
            'Guardian' => trim(($s['guardian_fname'] ?? '') . ' ' . ($s['guardian_lname'] ?? '')) ?: null,
            'Guardian mobile' => $s['guardian_mobile'] ?? null,
            'Institute' => $institute['name'] ?? null,
            'Portal username' => $username,
        ];
    @endphp

    <div class="grid gap-5 md:grid-cols-2">
        <section class="rounded-2xl border border-stone-200 bg-white">
            <h2 class="border-b border-stone-100 px-5 py-4 font-bold">Student details</h2>
            <dl class="divide-y divide-stone-100 text-sm">
                @foreach ($rows as $label => $value)
                    <div class="flex justify-between gap-4 px-5 py-3">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="text-right font-semibold">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="px-5 pb-4 text-xs text-gray-500">Details are wrong? Ask the institute to correct them.</p>
        </section>

        <div class="space-y-5">
            <section class="rounded-2xl border border-stone-200 bg-white">
                <h2 class="border-b border-stone-100 px-5 py-4 font-bold">Change password</h2>
                <form method="POST" action="{{ route('portal.password') }}" class="space-y-4 p-5">
                    @csrf @method('PUT')
                    @foreach ([['current_password', 'Current password', 'current-password'], ['new_password', 'New password', 'new-password'], ['new_password_confirmation', 'Type the new password again', 'new-password']] as [$field, $label, $auto])
                        <div>
                            <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="password" autocomplete="{{ $auto }}" required
                                class="w-full rounded-xl border px-3 py-2.5 outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15 {{ $errors->has($field) ? 'border-red-500' : 'border-stone-300' }}">
                            @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 font-semibold text-white hover:bg-red-700">Change password</button>
                </form>
            </section>

            @if (\App\Support\WebPush::enabled())
                <section data-push-section hidden class="rounded-2xl border border-stone-200 bg-white p-5">
                    <div class="flex items-start gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-red-50 text-red-600"><x-icon name="bell" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-bold">Notifications on this device</h2>
                            <p data-push-status data-tone="muted" class="mt-1 text-sm text-gray-500 data-[tone=error]:text-red-700 data-[tone=on]:text-emerald-700">Checking...</p>
                        </div>
                    </div>
                    <button type="button" data-push-enable hidden class="mt-4 w-full rounded-xl bg-red-600 px-4 py-2.5 font-semibold text-white hover:bg-red-700 disabled:opacity-60">Turn on notifications</button>
                </section>
            @endif

            @if ($supportPhone || $supportEmail)
                <section class="rounded-2xl border border-stone-200 bg-white p-5 text-sm">
                    <h2 class="font-bold">Need help?</h2>
                    @if ($supportPhone)<p class="mt-2"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="font-semibold text-red-600">{{ $supportPhone }}</a></p>@endif
                    @if ($supportEmail)<p class="mt-1"><a href="mailto:{{ $supportEmail }}" class="font-semibold text-red-600">{{ $supportEmail }}</a></p>@endif
                </section>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-4 py-3 font-semibold text-gray-700 hover:bg-stone-50">
                    <x-icon name="logout" class="h-5 w-5" /> Log out
                </button>
            </form>
        </div>
    </div>
@endsection
