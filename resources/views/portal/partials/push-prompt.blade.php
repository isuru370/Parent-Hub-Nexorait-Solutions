{{-- Shown by push-script when this browser can get notifications but they are not on yet. --}}
<div id="pushPrompt" hidden class="mb-5 flex flex-wrap items-center gap-3 rounded-2xl border border-gold-200 bg-gold-50 p-4">
    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-navy-700 shadow-sm"><x-icon name="bell" class="h-5 w-5" /></span>
    <div class="min-w-[12rem] flex-1">
        <p class="font-semibold">Get notifications on this device</p>
        <p class="text-sm text-gray-600">Attendance, payments and results, as soon as they happen.</p>
    </div>
    <div class="flex gap-2">
        <button type="button" data-push-dismiss class="rounded-xl px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-white">Not now</button>
        <button type="button" data-push-enable class="btn-primary py-2 disabled:opacity-60">Turn on</button>
    </div>
</div>
