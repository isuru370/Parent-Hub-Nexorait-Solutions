<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $values = collect(SystemSetting::KEYS)->mapWithKeys(fn ($label, $key) => [$key => SystemSetting::get($key)]);

        return view('admin.settings', ['labels' => SystemSetting::KEYS, 'values' => $values]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'support_phone' => ['nullable', 'string', 'max:30'],
            'support_email' => ['nullable', 'email', 'max:150'],
            'portal_notice' => ['nullable', 'string', 'max:500'],
            'android_app_url' => ['nullable', 'url:https', 'max:300'],
            'ios_app_url' => ['nullable', 'url:https', 'max:300'],
        ]);

        foreach (array_keys(SystemSetting::KEYS) as $key) {
            SystemSetting::put($key, $data[$key] ?? null);
        }

        return back()->with('success', 'Settings saved.');
    }
}
