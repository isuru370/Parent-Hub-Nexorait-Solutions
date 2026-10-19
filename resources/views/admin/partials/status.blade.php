@if ($on)
    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ $onText ?? 'Active' }}
    </span>
@else
    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>{{ $offText ?? 'Disabled' }}
    </span>
@endif
