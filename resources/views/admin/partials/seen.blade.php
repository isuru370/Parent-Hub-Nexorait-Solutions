@if ($institute->last_ping_at)
    <span class="inline-flex items-center gap-1.5 text-sm {{ $institute->isRecentlySeen() ? 'text-emerald-700' : 'text-amber-700' }}">
        <span class="h-2 w-2 rounded-full {{ $institute->isRecentlySeen() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
        {{ $institute->last_ping_at->diffForHumans() }}
    </span>
@else
    <span class="text-sm text-gray-400">Never</span>
@endif
