@extends('layouts.admin')

@section('title', 'Admins')
@section('subtitle', 'People who can log in to this admin area with their email.')

@section('content')
    <div class="grid gap-6 *:min-w-0 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white xl:col-span-2">
            <div class="relative overflow-x-auto">
                <table class="w-full min-w-[520px] text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Last login</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($admins as $admin)
                            <tr>
                                <td class="px-5 py-3">
                                    <p class="font-semibold">{{ $admin->name }} @if ($admin->is(auth()->user()))<span class="text-xs font-normal text-gray-500">(you)</span>@endif</p>
                                    <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $admin->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="px-5 py-3">@include('admin.partials.status', ['on' => $admin->is_active])</td>
                                <td class="px-5 py-3 text-right">
                                    @unless ($admin->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.admins.toggle', $admin) }}"
                                            data-confirm="{{ $admin->is_active ? 'Disable ' . $admin->email . '?' : 'Enable ' . $admin->email . '?' }}">
                                            @csrf @method('PATCH')
                                            <button class="text-sm font-semibold {{ $admin->is_active ? 'text-red-600' : 'text-emerald-700' }} hover:underline">
                                                {{ $admin->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4"><h2 class="font-bold">Add an admin</h2></div>
                <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4 p-5">
                    @csrf
                    @foreach ([['name', 'Name', 'text', 'name'], ['email', 'Email', 'email', 'email'], ['password', 'Password', 'password', 'new-password'], ['password_confirmation', 'Confirm password', 'password', 'new-password']] as [$field, $label, $type, $auto])
                        <div>
                            <label for="new_{{ $field }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
                            <input id="new_{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $auto }}" required
                                @if ($type !== 'password') value="{{ old($field) }}" @endif
                                class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15 {{ $errors->has($field) ? 'border-red-500' : 'border-gray-300' }}">
                            @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Add admin</button>
                </form>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4"><h2 class="font-bold">Change my password</h2></div>
                <form method="POST" action="{{ route('admin.password') }}" class="space-y-4 p-5">
                    @csrf @method('PUT')
                    @foreach ([['current_password', 'Current password', 'current-password'], ['password', 'New password', 'new-password'], ['password_confirmation', 'Confirm new password', 'new-password']] as [$field, $label, $auto])
                        <div>
                            <label for="my_{{ $field }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
                            <input id="my_{{ $field }}" name="{{ $field }}" type="password" autocomplete="{{ $auto }}" required
                                class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15">
                        </div>
                    @endforeach
                    <button class="w-full rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Change password</button>
                </form>
            </section>
        </div>
    </div>
@endsection
