@extends('layouts.admin')
@use('App\Support\Fmt')

@section('title', 'Institutes')
@section('subtitle', 'Each NexoraEDU system registers itself here. Disable an institute to stop its students from logging in.')

@section('content')
    @include('admin.partials.filters', [
        'action' => route('admin.institutes.index'),
        'placeholder' => 'Name, code or URL',
        'search' => $search,
        'selects' => [['status', $status, ['active' => 'Active', 'inactive' => 'Disabled'], 'All statuses']],
    ])

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Institute</th>
                        <th class="px-5 py-3">System URL</th>
                        <th class="px-5 py-3 text-right">Students</th>
                        <th class="px-5 py-3">Last seen</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($institutes as $institute)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.institutes.show', $institute) }}" class="font-semibold hover:text-red-600">{{ $institute->name }}</a>
                                <p class="text-xs text-gray-500">{{ $institute->code }}@if ($institute->app_version) · v{{ $institute->app_version }}@endif</p>
                            </td>
                            <td class="max-w-[220px] truncate px-5 py-3 text-gray-600">{{ $institute->api_url }}</td>
                            <td class="px-5 py-3 text-right">
                                <span class="font-semibold">{{ Fmt::number($institute->active_students_count) }}</span>
                                @if ($institute->students_count !== $institute->active_students_count)
                                    <span class="text-xs text-gray-500">/ {{ Fmt::number($institute->students_count) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">@include('admin.partials.seen', ['institute' => $institute])</td>
                            <td class="px-5 py-3">@include('admin.partials.status', ['on' => $institute->status])</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.institutes.show', $institute) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-red-600 hover:text-red-700">
                                    Open <x-icon name="chevron-right" class="h-4 w-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">No institutes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $institutes->links() }}</div>
@endsection
