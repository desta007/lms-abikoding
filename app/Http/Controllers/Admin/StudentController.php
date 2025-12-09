<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student')
            ->with(['profile', 'enrollments']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $students = $query->latest()->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function show($id)
    {
        $student = User::where('role', 'student')
            ->with(['profile', 'enrollments.course', 'posts'])
            ->findOrFail($id);

        $statistics = [
            'total_enrollments' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->whereNotNull('completed_at')->count(),
            'total_posts' => $student->posts()->count(),
            'total_points' => $student->profile->points ?? 0,
        ];

        return view('admin.students.show', compact('student', 'statistics'));
    }

    public function edit($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'whatsapp_number' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $student->update($validated);

        return redirect()->route('admin.students.show', $student->id)
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    public function suspend($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        // Add suspension logic here (you might want to add a suspended_at column)
        return redirect()->back()->with('success', 'Siswa berhasil ditangguhkan');
    }

    public function enrollments($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $enrollments = CourseEnrollment::where('user_id', $student->id)
            ->with(['course.category', 'course.level'])
            ->latest()
            ->paginate(20);

        return view('admin.students.enrollments', compact('student', 'enrollments'));
    }
}
