<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['profile', 'enrollments.course', 'posts', 'certificates.course']);
        
        $statistics = [
            'total_enrollments' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->whereNotNull('completed_at')->count(),
            'total_posts' => $user->posts()->count(),
            'total_certificates' => $user->certificates()->count(),
            'points' => $user->profile->points ?? 0,
        ];

        return view('profile.show', compact('user', 'statistics'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? \App\Models\UserProfile::create(['user_id' => $user->id]);
        
        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? \App\Models\UserProfile::create(['user_id' => $user->id]);

        // Support both simple form (name/email) and extended form (first_name/last_name)
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'whatsapp_number' => 'nullable|string',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'cover_photo' => 'nullable|image|max:5120',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Determine the name to use
        if ($request->filled('first_name') && $request->filled('last_name')) {
            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            $firstName = $validated['first_name'];
            $lastName = $validated['last_name'];
        } elseif ($request->filled('name')) {
            $fullName = $validated['name'];
            $nameParts = explode(' ', $validated['name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
        } else {
            $fullName = $user->name;
            $firstName = $user->first_name;
            $lastName = $user->last_name;
        }

        // Update user info
        $user->update([
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'whatsapp_number' => $validated['whatsapp_number'] ?? $user->whatsapp_number,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak benar']);
            }
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('profiles/avatars', 'public');
        }

        // Handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            if ($profile->cover_photo) {
                Storage::disk('public')->delete($profile->cover_photo);
            }
            $validated['cover_photo'] = $request->file('cover_photo')->store('profiles/covers', 'public');
        }

        // Update profile
        $profile->update([
            'bio' => $validated['bio'] ?? null,
            'avatar' => $validated['avatar'] ?? $profile->avatar,
            'cover_photo' => $validated['cover_photo'] ?? $profile->cover_photo,
            'location' => $validated['location'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function enrollments()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()
            ->with(['course.category', 'course.level', 'course.instructor'])
            ->latest()
            ->paginate(10);

        return view('profile.enrollments', compact('enrollments'));
    }

    public function certificates()
    {
        $user = Auth::user();
        $certificates = $user->certificates()
            ->with(['course'])
            ->latest()
            ->get();

        return view('profile.certificates', compact('certificates'));
    }
}
