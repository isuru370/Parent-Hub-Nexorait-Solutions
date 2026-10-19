@extends('layouts.admin')
@use('App\Support\Fmt')

@section('title', 'Dashboard')
@section('subtitle', 'Institutes connected to the hub and how parents are using it.')

@section('content')
    @php
        $cards = [
            ['Institutes', $stats['institutes_active'], 'of ' . $stats['institutes'] . ' active, ' . $stats['institutes_seen'] . ' seen this week', 'building', 'text-red-600 bg-red-50'],
            ['Students', $stats['students'], Fmt::number($stats['students_active']) . ' active, ' . Fmt::number($stats['new_students_week']) . ' added this week', 'users', 'text-orange-600 bg-orange-50'],
            ['Logins today', $stats['logins_today'], 'Parents who opened the portal or app', 'signal', 'text-emerald-600 bg-emerald-50'],
            ['Logins this week', $stats['logins_week'], 'Different students, last 7 days', 'clock', 'text-sky-600 bg-sky-50'],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as [$label, $value, $note, $icon, $tone])
            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-500">{{ $label }}</p>
                    <span class="grid h-9 w-9 place-items-center rounded-lg {{ $tone }}"><x-icon :name="$icon" class="h-5 w-5" /></span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight">{{ Fmt::number($value) }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $note }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 *:min-w-0 xl:grid-cols-3">
        <section class="rounded-2xl border border-gray-200 bg-white xl:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold">Institutes</h2>
                <a href="{{ route('admin.institutes.index') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">View all</a>
            </div>
            <div class="relative overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Institute</th>
                            <th class="px-5 py-3 text-right">Students</th>
                            <th class="px-5 py-3 text-right">Logins (7 days)</th>
                            <th class="px-5 py-3">Last seen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($institutes as $institute)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.institutes.show', $institute) }}" class="font-semibold hover:text-red-600">{{ $institute->name }}</a>
                                    <span class="ml-1 text-xs text-gray-500">{{ $institute->code }}</span>
                                    @unless ($institute->status)
                                        <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Disabled</span>
                                    @endunless
                                </td>
                                <td class="px-5 py-3 text-right font-semibold">{{ Fmt::number($institute->active_students_count) }}</td>
                                <td class="px-5 py-3 text-right">{{ Fmt::number($institute->logins_week_count) }}</td>
                                <td class="px-5 py-3">
                                    @include('admin.partials.seen', ['institute' => $institute])
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">No institutes yet. They appear here after <code class="text-xs">php artisan parenthub:register</code> runs on the institute's server.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-2xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold">Parent app versions</h2>
                    <a href="{{ route('admin.app-versions.index') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">Manage</a>
                </div>
                <dl class="divide-y divide-gray-100 text-sm">
                    @foreach ($latestVersions as $platform => $version)
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="font-semibold capitalize">{{ $platform === 'ios' ? 'iOS' : $platform }}</dt>
                            <dd>
                                @if ($version)
                                    <span class="font-semibold">{{ $version->version }}</span>
                                    @if ($version->force_update)
                                        <span class="ml-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Required</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold">Newest students</h2>
                    <a href="{{ route('admin.students.index') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">View all</a>
                </div>
                <ul class="divide-y divide-gray-100 text-sm">
                    @forelse ($recentStudents as $student)
                        <li class="flex items-center justify-between gap-3 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $student->username }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $student->institute->name ?? $student->institute_code }}</p>
                            </div>
                            <span class="shrink-0 text-xs text-gray-500">{{ $student->created_at?->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-gray-500">No students registered yet.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
@endsection
