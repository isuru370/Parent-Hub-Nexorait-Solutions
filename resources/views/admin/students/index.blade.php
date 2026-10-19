@extends('layouts.admin')

@section('title', 'Students')
@section('subtitle', 'Portal usernames sent by the institutes. Passwords are not stored here - the institute checks them.')

@section('content')
    @include('admin.partials.filters', [
        'action' => route('admin.students.index'),
        'placeholder' => 'Username or student ID',
        'search' => $search,
        'selects' => [
            ['institute', $institute, $institutes->pluck('name', 'code')->all(), 'All institutes'],
            ['status', $status, ['active' => 'Can log in', 'inactive' => 'Blocked'], 'All statuses'],
        ],
    ])

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Username</th>
                        <th class="px-5 py-3">Student ID</th>
                        <th class="px-5 py-3">Institute</th>
                        <th class="px-5 py-3">Last login</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-semibold">{{ $student->username }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $student->student_custom_id }}</td>
                            <td class="px-5 py-3">
                                @if ($student->institute)
                                    <a href="{{ route('admin.institutes.show', $student->institute) }}" class="hover:text-red-600">{{ $student->institute->name }}</a>
                                    @unless ($student->institute->status)
                                        <span class="ml-1 text-xs text-amber-700">(institute disabled)</span>
                                    @endunless
                                @else
                                    <span class="text-gray-500">{{ $student->institute_code }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $student->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="px-5 py-3">@include('admin.partials.status', ['on' => $student->is_active, 'onText' => 'Can log in', 'offText' => 'Blocked'])</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.students.toggle', $student) }}"
                                    data-confirm="{{ $student->is_active ? 'Block ' . $student->username . ' from logging in?' : 'Allow ' . $student->username . ' to log in again?' }}">
                                    @csrf @method('PATCH')
                                    <button class="text-sm font-semibold {{ $student->is_active ? 'text-red-600 hover:text-red-700' : 'text-emerald-700 hover:text-emerald-800' }}">
                                        {{ $student->is_active ? 'Block' : 'Allow' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $students->links() }}</div>
@endsection
