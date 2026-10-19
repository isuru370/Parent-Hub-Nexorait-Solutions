@extends('layouts.admin')

@section('title', 'Settings')
@section('subtitle', 'Shown to parents on the student portal.')

@section('content')
    <section class="max-w-2xl rounded-2xl border border-gray-200 bg-white">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5 p-5">
            @csrf @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach (['support_phone' => 'tel', 'support_email' => 'email'] as $key => $type)
                    <div>
                        <label for="{{ $key }}" class="mb-1.5 block text-sm font-semibold">{{ $labels[$key] }}</label>
                        <input id="{{ $key }}" name="{{ $key }}" type="{{ $type }}" value="{{ old($key, $values[$key]) }}"
                            class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15 {{ $errors->has($key) ? 'border-red-500' : 'border-gray-300' }}">
                        @error($key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <div>
                <label for="portal_notice" class="mb-1.5 block text-sm font-semibold">{{ $labels['portal_notice'] }}</label>
                <textarea id="portal_notice" name="portal_notice" rows="3" maxlength="500"
                    class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15"
                    placeholder="e.g. The portal will be down for maintenance on Sunday 8-10 AM.">{{ old('portal_notice', $values['portal_notice']) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Leave empty to show nothing.</p>
            </div>

            <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Save settings</button>
        </form>
    </section>
@endsection
