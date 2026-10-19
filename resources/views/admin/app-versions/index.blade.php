@extends('layouts.admin')

@section('title', 'App versions')
@section('subtitle', 'The parent app checks this list to show "update available" or to require an update.')

@section('content')
    <div class="grid gap-6 *:min-w-0 xl:grid-cols-3">
        <section class="rounded-2xl border border-gray-200 bg-white xl:col-span-2">
            <div class="border-b border-gray-100 px-5 py-4"><h2 class="font-bold">Releases</h2></div>
            <div class="relative overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Platform</th>
                            <th class="px-5 py-3">Version</th>
                            <th class="px-5 py-3">Update</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($versions as $version)
                            @php $isLatest = optional($latest[$version->platform] ?? null)->is($version); @endphp
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $version->platform === 'ios' ? 'iOS' : 'Android' }}</td>
                                <td class="px-5 py-3">
                                    <span class="font-semibold">{{ $version->version }}</span>
                                    @if ($isLatest)
                                        <span class="ml-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Latest</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <form method="POST" action="{{ route('admin.app-versions.update', $version) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="field" value="force_update">
                                        <button class="text-sm font-semibold {{ $version->force_update ? 'text-red-700' : 'text-gray-500' }} hover:underline"
                                            title="Click to change">
                                            {{ $version->force_update ? 'Required' : 'Optional' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3">
                                    <form method="POST" action="{{ route('admin.app-versions.update', $version) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="field" value="status">
                                        <button title="Click to change">@include('admin.partials.status', ['on' => $version->status, 'onText' => 'Live', 'offText' => 'Hidden'])</button>
                                    </form>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.app-versions.destroy', $version) }}" data-confirm="Delete version {{ $version->version }}?">
                                        @csrf @method('DELETE')
                                        <button class="text-sm font-semibold text-gray-500 hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-500">No versions yet. Add the version that is on the Play Store.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-5 py-4"><h2 class="font-bold">Add a release</h2></div>
            <form method="POST" action="{{ route('admin.app-versions.store') }}" class="space-y-4 p-5">
                @csrf
                <div>
                    <label for="platform" class="mb-1.5 block text-sm font-semibold">Platform</label>
                    <select id="platform" name="platform" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15">
                        <option value="android" @selected(old('platform') === 'android')>Android</option>
                        <option value="ios" @selected(old('platform') === 'ios')>iOS</option>
                    </select>
                </div>
                <div>
                    <label for="version" class="mb-1.5 block text-sm font-semibold">Version number</label>
                    <input id="version" name="version" value="{{ old('version') }}" placeholder="1.2.0" required
                        class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15 {{ $errors->has('version') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('version')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" name="force_update" value="1" @checked(old('force_update')) class="mt-0.5 h-4 w-4 rounded border-gray-300 text-red-600">
                    <span><span class="font-semibold">Require this update</span><br><span class="text-gray-500">Older apps must update before they can be used.</span></span>
                </label>
                <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Add release</button>
                <p class="text-xs text-gray-500">The app calls <code>GET /api/v1/app-version?platform=android&amp;current=1.0.0</code></p>
            </form>
        </section>
    </div>
@endsection
