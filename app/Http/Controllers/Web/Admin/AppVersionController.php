<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    public function index(): View
    {
        $versions = AppVersion::query()
            ->get()
            ->sortBy([['platform', 'asc'], fn ($a, $b) => version_compare($b->version, $a->version)])
            ->values();

        $latest = collect(AppVersion::PLATFORMS)->mapWithKeys(fn ($p) => [$p => AppVersion::latestFor($p)]);

        return view('admin.app-versions.index', compact('versions', 'latest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform' => ['required', Rule::in(AppVersion::PLATFORMS)],
            'version' => [
                'required', 'string', 'max:50', 'regex:/^\d+(\.\d+){0,3}$/',
                Rule::unique('app_versions')->where('platform', $request->input('platform')),
            ],
            'force_update' => ['nullable', 'boolean'],
        ], [
            'version.regex' => 'Use a version number like 1.2.0',
            'version.unique' => 'This version already exists for that platform.',
        ]);

        AppVersion::create([
            'platform' => $data['platform'],
            'version' => $data['version'],
            'force_update' => (bool) ($data['force_update'] ?? false),
            'status' => true,
        ]);

        return back()->with('success', "Version {$data['version']} ({$data['platform']}) added.");
    }

    public function update(Request $request, AppVersion $appVersion): RedirectResponse
    {
        $field = $request->validate(['field' => ['required', Rule::in(['status', 'force_update'])]])['field'];

        $appVersion->update([$field => !$appVersion->{$field}]);

        return back()->with('success', "Version {$appVersion->version} updated.");
    }

    public function destroy(AppVersion $appVersion): RedirectResponse
    {
        $appVersion->delete();

        return back()->with('success', "Version {$appVersion->version} deleted.");
    }
}
