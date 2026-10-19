<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InstituteController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $institutes = Institute::query()
            ->withCount([
                'students',
                'students as active_students_count' => fn ($q) => $q->where('is_active', true),
            ])
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('api_url', 'like', "%{$search}%")))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($q) => $q->where('status', $status === 'active'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.institutes.index', compact('institutes', 'search', 'status'));
    }

    public function show(Institute $institute): View
    {
        $institute->loadCount([
            'students',
            'students as active_students_count' => fn ($q) => $q->where('is_active', true),
            'students as logins_week_count' => fn ($q) => $q->where('last_login_at', '>=', now()->subDays(7)),
        ]);

        $students = $institute->students()->latest()->limit(10)->get();

        return view('admin.institutes.show', compact('institute', 'students'));
    }

    public function update(Request $request, Institute $institute): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'api_url' => ['required', 'url', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'url', 'max:255'],
        ]);

        $institute->update($data);

        return back()->with('success', 'Institute details saved.');
    }

    public function toggle(Institute $institute): RedirectResponse
    {
        $institute->update(['status' => !$institute->status]);

        return back()->with('success', $institute->status
            ? "{$institute->name} is active again. Its students can log in."
            : "{$institute->name} is disabled. Its students cannot log in.");
    }
}
