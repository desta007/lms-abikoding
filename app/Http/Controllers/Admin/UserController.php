<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $query = User::with(['profile']);

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

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

        $users = $query->latest()->paginate(20);

        // Get counts for filter tabs
        $roleCounts = [
            'all' => User::count(),
            'student' => User::where('role', 'student')->count(),
            'instructor' => User::where('role', 'instructor')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['profile', 'enrollments.course', 'posts'])
            ->findOrFail($id);

        $statistics = $this->getUserStatistics($user);

        return view('admin.users.show', compact('user', 'statistics'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'whatsapp_number' => 'nullable|string',
            'role' => ['required', Rule::in(['student', 'instructor', 'admin'])],
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Prevent removing last admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['role' => 'Cannot remove the last admin user.'])
                    ->withInput();
            }
        }

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Log role change if different
        if ($user->role !== $validated['role']) {
            // Role changed - you might want to log this
            // TODO: Add activity log for role changes
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Update user role specifically.
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['student', 'instructor', 'admin'])],
        ]);

        // Prevent removing last admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['role' => 'Cannot remove the last admin user.'])
                    ->withInput();
            }
        }

        // Prevent admin from removing their own admin role
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return redirect()->back()
                ->withErrors(['role' => 'You cannot remove your own admin role.'])
                ->withInput();
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        // TODO: Add activity log for role changes
        // TODO: Send notification to user about role change

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User role changed from {$oldRole} to {$validated['role']}");
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Cannot delete the last admin user.']);
            }
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Get user statistics based on role.
     */
    private function getUserStatistics(User $user)
    {
        $stats = [
            'role' => $user->role,
        ];

        switch ($user->role) {
            case 'student':
                $stats['total_enrollments'] = $user->enrollments()->count();
                $stats['completed_courses'] = $user->enrollments()->whereNotNull('completed_at')->count();
                $stats['total_posts'] = $user->posts()->count();
                $stats['total_points'] = $user->profile->points ?? 0;
                break;

            case 'instructor':
                $stats['total_courses'] = Course::where('instructor_id', $user->id)->count();
                $stats['published_courses'] = Course::where('instructor_id', $user->id)
                    ->where('status', 'published')->count();
                $stats['total_students'] = CourseEnrollment::whereHas('course', function($q) use ($user) {
                    $q->where('instructor_id', $user->id);
                })->distinct('user_id')->count();
                $stats['total_enrollments'] = CourseEnrollment::whereHas('course', function($q) use ($user) {
                    $q->where('instructor_id', $user->id);
                })->count();
                break;

            case 'admin':
                $stats['total_users'] = User::count();
                $stats['total_students'] = User::where('role', 'student')->count();
                $stats['total_instructors'] = User::where('role', 'instructor')->count();
                break;
        }

        return $stats;
    }
}

