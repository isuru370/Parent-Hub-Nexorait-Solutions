{{--
    Search + optional selects.
    $action, $placeholder, $search, $selects = [[name, current, [value => label], 'All ...'], ...]
--}}
<form method="GET" action="{{ $action }}" class="mb-4 flex flex-wrap gap-2">
    <label class="relative min-w-[200px] flex-1">
        <span class="sr-only">Search</span>
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        <input type="search" name="search" value="{{ $search }}" placeholder="{{ $placeholder }}"
            class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15">
    </label>
    @foreach ($selects ?? [] as [$name, $current, $options, $allLabel])
        <select name="{{ $name }}" aria-label="{{ $allLabel }}" onchange="this.form.submit()"
            class="min-w-0 max-w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/15">
            <option value="">{{ $allLabel }}</option>
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach
    <button class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Search</button>
    @if (request()->query())
        <a href="{{ $action }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear</a>
    @endif
</form>
