@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Messages')
@section('heading', 'Messages from the institute')

@section('content')
    @php
        $items = $notifications['items'] ?? [];
        $pagination = $notifications['pagination'] ?? [];
        $unread = (int) ($notifications['stats']['unread'] ?? 0);
        $lastPage = (int) ($pagination['last_page'] ?? 1);
    @endphp

    @if ($unread > 0)
        <form method="POST" action="{{ route('portal.notifications.read-all') }}" class="mb-4">
            @csrf
            <button class="rounded-xl border border-stone-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-stone-50">Mark all {{ $unread }} as read</button>
        </form>
    @endif

    <div class="space-y-3">
        @forelse ($items as $n)
            @php $isRead = !empty($n['is_read']) || !empty($n['read_at']); @endphp
            <article class="rounded-2xl border bg-white p-4 {{ $isRead ? 'border-stone-200' : 'border-red-200' }}">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-semibold">
                        @unless ($isRead)<span class="mr-1 inline-block h-2 w-2 rounded-full bg-red-500 align-middle" aria-label="Unread"></span>@endunless
                        {{ $n['title'] ?? 'Notification' }}
                    </h2>
                    <time class="shrink-0 text-xs text-gray-500">{{ Fmt::date($n['created_at'] ?? $n['sent_at'] ?? null, 'd M, g:i A') }}</time>
                </div>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $n['body'] ?? $n['message'] ?? '' }}</p>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-gray-500">
                <x-icon name="bell" class="mx-auto mb-2 h-8 w-8 text-gray-300" />
                No messages yet.
            </div>
        @endforelse
    </div>

    @if ($lastPage > 1)
        <nav class="mt-5 flex items-center justify-between text-sm" aria-label="Pages">
            @if ($page > 1)
                <a href="{{ route('portal.notifications', ['page' => $page - 1]) }}" class="rounded-xl border border-stone-300 bg-white px-4 py-2 font-semibold">Newer</a>
            @else <span></span> @endif
            <span class="text-gray-500">Page {{ $page }} of {{ $lastPage }}</span>
            @if ($page < $lastPage)
                <a href="{{ route('portal.notifications', ['page' => $page + 1]) }}" class="rounded-xl border border-stone-300 bg-white px-4 py-2 font-semibold">Older</a>
            @else <span></span> @endif
        </nav>
    @endif
@endsection
