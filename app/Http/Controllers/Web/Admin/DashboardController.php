<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\Institute;
use App\Models\StudentRegistry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'institutes' => Institute::count(),
            'institutes_active' => Institute::active()->count(),
            'institutes_seen' => Institute::active()
                ->where('last_ping_at', '>=', now()->subDays(Institute::ONLINE_WITHIN_DAYS))
                ->count(),
            'students' => StudentRegistry::count(),
            'students_active' => StudentRegistry::where('is_active', true)->count(),
            'logins_today' => StudentRegistry::where('last_login_at', '>=', today())->count(),
            'logins_week' => StudentRegistry::where('last_login_at', '>=', now()->subDays(7))->count(),
            'new_students_week' => StudentRegistry::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $institutes = Institute::query()
            ->withCount([
                'students',
                'students as active_students_count' => fn ($q) => $q->where('is_active', true),
                'students as logins_week_count' => fn ($q) => $q->where('last_login_at', '>=', now()->subDays(7)),
            ])
            ->orderByDesc('status')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $recentStudents = StudentRegistry::query()
            ->with('institute:id,code,name')
            ->latest()
            ->limit(8)
            ->get();

        $latestVersions = collect(AppVersion::PLATFORMS)
            ->mapWithKeys(fn ($p) => [$p => AppVersion::latestFor($p)]);

        return view('admin.dashboard', compact('stats', 'institutes', 'recentStudents', 'latestVersions'));
    }
}
