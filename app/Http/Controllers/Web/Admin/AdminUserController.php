<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::query()->orderByDesc('is_active')->orderBy('name')->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'is_active' => true,
        ]);

        return back()->with('success', "Admin {$data['email']} added.");
    }

    public function toggle(User $admin): RedirectResponse
    {
        if ($admin->is(Auth::user())) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        if ($admin->is_active && User::where('is_active', true)->count() <= 1) {
            return back()->with('error', 'At least one admin must stay active.');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        return back()->with('success', $admin->is_active ? "{$admin->email} enabled." : "{$admin->email} disabled.");
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Your password was changed.');
    }
}
