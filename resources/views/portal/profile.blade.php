@extends('layouts.portal')
@use('App\Support\Fmt')

@section('title', 'Profile')

@section('content')
    @php
        // Live profile from the institute, or the details saved at login
        $p = $profile ?? null;
        $s = $p['student'] ?? [];
        $login = $student ?? [];
        $pick = fn (...$values) => collect($values)->first(fn ($v) => $v !== null && $v !== '');

        $name = $pick($s['initial_name'] ?? null, $login['initial_name'] ?? null, $s['full_name'] ?? null, $login['full_name'] ?? null, 'Student');
        $photo = $pick($s['img_url'] ?? null, $login['img_url'] ?? null);
        $photo = filter_var($photo, FILTER_VALIDATE_URL) ? $photo : null;
        $address = $pick($s['address'] ?? null, collect([$login['address1'] ?? null, $login['address2'] ?? null, $login['address3'] ?? null])->filter()->implode(', '));
        $guardian = $p['guardian'] ?? [];
        $branch = $p['branch'] ?? [];
        $instName = $pick($p['institute']['name'] ?? null, $institute['name'] ?? null);
        $bday = $pick($s['bday'] ?? null, $login['bday'] ?? null);
        $active = $s['is_active'] ?? (($login['is_active'] ?? true) && empty($login['student_disable']));

        $sections = [
            ['Student details', 'user', [
                'Full name' => $pick($s['full_name'] ?? null, $login['full_name'] ?? null),
                'Name with initials' => $pick($s['initial_name'] ?? null, $login['initial_name'] ?? null),
                'Student ID' => $pick($s['custom_id'] ?? null, $login['custom_id'] ?? null),
                'Gender' => ucfirst((string) $pick($s['gender'] ?? null, $login['gender'] ?? null)),
                'Date of birth' => $bday ? Fmt::date($bday, 'd F Y') : null,
                'NIC' => $pick($s['nic'] ?? null, $login['nic'] ?? null),
                'Grade' => $pick($s['grade_name'] ?? null, $login['grade_name'] ?? null),
                'School' => $pick($s['student_school'] ?? null, $login['student_school'] ?? null),
                'Class type' => ucfirst((string) $pick($s['class_type'] ?? null, $login['class_type'] ?? null)),
                'Registered on' => !empty($s['registered_at']) ? Fmt::date($s['registered_at'], 'd F Y') : null,
            ]],
            ['Contact', 'phone', [
                'Mobile' => $pick($s['mobile'] ?? null, $login['mobile'] ?? null),
                'WhatsApp' => $pick($s['whatsapp_mobile'] ?? null, $login['whatsapp_mobile'] ?? null),
                'Email' => $pick($s['email'] ?? null, $login['email'] ?? null),
                'Address' => $address,
            ]],
            ['Parent / Guardian', 'users', [
                'Name' => $pick($guardian['name'] ?? null, trim(($login['guardian_fname'] ?? '') . ' ' . ($login['guardian_lname'] ?? ''))),
                'Mobile' => $pick($guardian['mobile'] ?? null, $login['guardian_mobile'] ?? null),
                'NIC' => $pick($guardian['nic'] ?? null, $login['guardian_nic'] ?? null),
            ]],
            ['Institute', 'building', [
                'Institute' => $instName,
                'Branch' => $branch['name'] ?? ($login['branch_name'] ?? null),
                'Branch address' => $branch['address'] ?? null,
                'Branch phone' => $branch['mobile'] ?? null,
                'Branch email' => $branch['email'] ?? null,
                'Portal username' => $username,
            ]],
        ];
        $classes = $p['classes'] ?? [];
        $supportDigits = preg_replace('/[^0-9]/', '', (string) $supportPhone);
        $whatsApp = 'https://wa.me/' . (str_starts_with($supportDigits, '0') ? '94' . substr($supportDigits, 1) : $supportDigits);
    @endphp

    {{-- ===================== Hero ===================== --}}
    <section class="hero relative overflow-hidden rounded-3xl p-6 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 h-60 w-60 rounded-full bg-gold-400/10"></div>
        <div class="relative flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
            <span class="shrink-0 rounded-full bg-gradient-to-br from-gold-200 to-gold-600 p-[3px]">
                @if ($photo)
                    <img src="{{ $photo }}" alt="" class="h-24 w-24 rounded-full border-2 border-white object-cover">
                @else
                    <span class="grid h-24 w-24 place-items-center rounded-full border-2 border-white bg-navy-50 text-3xl font-extrabold text-navy-900">{{ Fmt::initials($name) }}</span>
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-extrabold">{{ $name }}</h1>
                <p class="mt-1 text-sm text-navy-200">{{ collect([$pick($s['custom_id'] ?? null, $login['custom_id'] ?? null), ($g = $pick($s['grade_name'] ?? null, $login['grade_name'] ?? null)) ? 'Grade ' . $g : null])->filter()->implode(' · ') }}</p>
                @if ($instName)
                    <p class="mt-3 inline-flex items-center gap-2 rounded-xl bg-white/8 px-3 py-2 text-sm font-semibold text-gold-300 ring-1 ring-gold-400/30">
                        <x-icon name="building" class="h-4 w-4" /> {{ $instName }}@if (!empty($branch['name'])) <span class="font-normal text-navy-200">· {{ $branch['name'] }} branch</span>@endif
                    </p>
                @endif
            </div>
            <div class="flex flex-col items-center gap-2 sm:items-end">
                <span class="chip {{ $active ? 'bg-emerald-400/15 text-emerald-300' : 'bg-red-400/15 text-red-300' }}">{{ $active ? 'Active' : 'Inactive' }}</span>
                <a href="{{ route('portal.id-card') }}" class="btn-gold"><x-icon name="qr" class="h-5 w-5" /> Digital ID card</a>
            </div>
        </div>
    </section>

    <div class="mt-4 flex items-start gap-3 rounded-2xl bg-navy-50 p-4 text-sm text-navy-800">
        <x-icon name="lock" class="h-5 w-5 shrink-0 text-navy-700" />
        <p>These details are kept by the institute and can't be changed here. To correct anything, please contact the institute. Only the password can be changed.</p>
    </div>
    @if (!$p)
        <p class="mt-2 text-xs text-slate-500">Showing the details saved when you logged in.</p>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($sections as [$title, $icon, $rows])
                    @php $rows = array_filter($rows, fn ($v) => $v !== null && $v !== ''); @endphp
                    @if ($rows)
                        <section class="card overflow-hidden">
                            <h2 class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 font-bold text-navy-900"><x-icon :name="$icon" class="h-5 w-5 text-gold-600" /> {{ $title }}</h2>
                            <dl class="divide-y divide-slate-100 text-sm">
                                @foreach ($rows as $label => $value)
                                    <div class="flex justify-between gap-4 px-5 py-3">
                                        <dt class="shrink-0 text-slate-500">{{ $label }}</dt>
                                        <dd class="text-right font-semibold">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </section>
                    @endif
                @endforeach
            </div>

            @if ($classes)
                <section class="card overflow-hidden">
                    <h2 class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 font-bold text-navy-900"><x-icon name="academic" class="h-5 w-5 text-gold-600" /> Classes ({{ count($classes) }})</h2>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($classes as $c)
                            <li class="flex items-center gap-4 px-5 py-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-navy-50 text-navy-700"><x-icon name="book" class="h-5 w-5" /></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold">{{ $c['class_name'] ?? 'Class' }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ collect([$c['teacher'] ?? null, $c['subject'] ?? null, $c['category'] ?? null, $c['medium'] ?? null, $c['branch'] ?? null])->filter()->implode(' · ') }}</p>
                                </div>
                                @if (!empty($c['is_free_card']))
                                    <span class="chip bg-emerald-50 text-emerald-700">Free card</span>
                                @else
                                    <span class="chip shrink-0 bg-gold-100 text-gold-700">{{ Fmt::money($c['monthly_fee'] ?? 0) }} / month</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Password: the only thing that can be changed --}}
            <section class="card overflow-hidden">
                <h2 class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 font-bold text-navy-900"><x-icon name="key" class="h-5 w-5 text-gold-600" /> Change password</h2>
                <form method="POST" action="{{ route('portal.password') }}" class="space-y-4 p-5">
                    @csrf @method('PUT')
                    @foreach ([['current_password', 'Current password', 'current-password'], ['new_password', 'New password', 'new-password'], ['new_password_confirmation', 'Type the new password again', 'new-password']] as [$field, $label, $auto])
                        <div>
                            <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="password" autocomplete="{{ $auto }}" required
                                class="w-full rounded-xl border px-3 py-2.5 outline-none focus:border-navy-600 focus:ring-4 focus:ring-navy-600/15 {{ $errors->has($field) ? 'border-red-500' : 'border-slate-300' }}">
                            @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <button class="btn-primary w-full py-3">Change password</button>
                </form>
            </section>

            @if (\App\Support\WebPush::enabled())
                <section data-push-section hidden class="card p-5">
                    <div class="flex items-start gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-navy-50 text-navy-700"><x-icon name="bell" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-bold text-navy-900">Notifications on this device</h2>
                            <p data-push-status data-tone="muted" class="mt-1 text-sm text-slate-500 data-[tone=error]:text-red-700 data-[tone=on]:text-emerald-700">Checking...</p>
                        </div>
                    </div>
                    <button type="button" data-push-enable hidden class="btn-primary mt-4 w-full">Turn on notifications</button>
                </section>
            @endif

            {{-- Help & Support --}}
            <section class="card p-5">
                <h2 class="flex items-center gap-2 font-bold text-navy-900"><x-icon name="chat" class="h-5 w-5 text-gold-600" /> Help & Support</h2>
                <p class="mt-1 text-sm text-slate-500">Questions about the portal or the app? Contact NexoraIT. For class, fee or attendance details, please contact the institute.</p>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="tel:{{ $supportDigits }}" class="btn-primary"><x-icon name="phone" class="h-4 w-4" /> Call</a>
                    <a href="{{ $whatsApp }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700"><x-icon name="chat" class="h-4 w-4" /> WhatsApp</a>
                </div>
                <p class="mt-3 text-center text-sm font-semibold text-navy-900">{{ $supportPhone }}</p>
                @if ($supportEmail)<p class="text-center text-xs"><a href="mailto:{{ $supportEmail }}" class="link text-xs">{{ $supportEmail }}</a></p>@endif
            </section>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-light w-full py-3 text-red-600 hover:bg-red-50"><x-icon name="logout" class="h-5 w-5" /> Log out</button>
            </form>
        </div>
    </div>
@endsection
