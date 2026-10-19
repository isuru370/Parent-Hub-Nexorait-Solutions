<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\StudentRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $institute = (string) $request->query('institute');
        $status = $request->query('status');

        $students = StudentRegistry::query()
            ->with('institute:id,code,name,status')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('username', 'like', "%{$search}%")
                ->orWhere('student_custom_id', 'like', "%{$search}%")))
            ->when($institute !== '', fn ($q) => $q->where('institute_code', $institute))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($q) => $q->where('is_active', $status === 'active'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $institutes = Institute::query()->orderBy('name')->get(['code', 'name']);

        return view('admin.students.index', compact('students', 'institutes', 'search', 'institute', 'status'));
    }

    public function toggle(StudentRegistry $student): RedirectResponse
    {
        $student->update(['is_active' => !$student->is_active]);

        return back()->with('success', $student->is_active
            ? "{$student->username} can log in again."
            : "{$student->username} can no longer log in.");
    }
}
