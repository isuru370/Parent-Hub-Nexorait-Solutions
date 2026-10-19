@extends('layouts.admin')
@use('App\Support\Fmt')

@section('title', $institute->name)
@section('subtitle')
    Code {{ $institute->code }} · registered {{ $institute->created_at?->format('d M Y') }}
@endsection

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.institutes.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            <x-icon name="arrow-left" class="h-4 w-4" /> Institutes
        </a>
        <form method="POST" action="{{ route('admin.institutes.toggle', $institute) }}"
            data-confirm="{{ $institute->status ? 'Disable this institute? Its students will not be able to log in.' : 'Enable this institute again?' }}">
            @csrf @method('PATCH')
            <button class="rounded-xl px-4 py-2.5 text-sm font-semibold {{ $institute->status ? 'border border-red-200 bg-white text-red-700 hover:bg-red-50' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                {{ $institute->status ? 'Disable institute' : 'Enable institute' }}
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5">
            <p class="text-sm font-semibold text-gray-500">Active students</p>
            <p class="mt-2 text-3xl font-extrabold">{{ Fmt::number($institute->active_students_count) }}</p>
            <p class="text-xs text-gray-500">{{ Fmt::number($institute->students_count) }} registered in total</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5">
            <p class="text-sm font-semibold text-gray-500">Logins (7 days)</p>
            <p class="mt-2 text-3xl font-extrabold">{{ Fmt::number($institute->logins_week_count) }}</p>
            <p class="text-xs text-gray-500">Different students</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5">
            <p class="text-sm font-semibold text-gray-500">Last seen</p>
            <p class="mt-3">@include('admin.partials.seen', ['institute' => $institute])</p>
            <p class="mt-1 text-xs text-gray-500">Updated when the institute registers or re-registers.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 *:min-w-0 xl:grid-cols-5">
        <section class="rounded-2xl border border-gray-200 bg-white xl:col-span-3">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold">Details</h2>
                @include('admin.partials.status', ['on' => $institute->status])
            </div>
            <form method="POST" action="{{ route('admin.institutes.update', $institute) }}" class="grid gap-4 p-5 sm:grid-cols-2">
                @csrf @method('PUT')
                @foreach ([
                    ['name', 'Name', 'text', true],
                    ['api_url', 'System URL (NexoraEDU)', 'url', true],
                    ['contact_number', 'Contact number', 'text', false],
                    ['contact_email', 'Contact email', 'email', false],
                    ['logo', 'Logo URL', 'url', false],
                ] as [$field, $label, $type, $required])
                    <div class="{{ in_array($field, ['api_url', 'logo']) ? 'sm:col-span-2' : '' }}">
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $institute->{$field}) }}" @required($required)
                            class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15 {{ $errors->has($field) ? 'border-red-500' : 'border-gray-300' }}">
                        @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <p class="text-xs text-gray-500 sm:col-span-2">
                    The system URL is where parent logins are checked. The institute sends it again every time it registers, so change it here only if logins are failing.
                </p>
                <div class="sm:col-span-2">
                    <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Save details</button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white xl:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold">Newest students</h2>
                <a href="{{ route('admin.students.index', ['institute' => $institute->code]) }}" class="text-sm font-semibold text-red-600 hover:text-red-700">All students</a>
            </div>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($students as $student)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div>
                            <p class="font-semibold">{{ $student->username }}</p>
                            <p class="text-xs text-gray-500">Last login: {{ $student->last_login_at?->diffForHumans() ?? 'never' }}</p>
                        </div>
                        @include('admin.partials.status', ['on' => $student->is_active])
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-gray-500">No students yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
