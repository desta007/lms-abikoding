<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('name', $username)
            ->orWhere('email', $username)
            ->with(['profile', 'posts.likes', 'posts.comments'])
            ->firstOrFail();

        $postsCount = $user->posts()->count();
        $points = $user->profile->points ?? 0;
        $enrolledCourses = CourseEnrollment::where('user_id', $user->id)->count();

        return view('community.profile', compact('user', 'postsCount', 'points', 'enrolledCourses'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? \App\Models\UserProfile::create(['user_id' => $user->id]);

        return view('community.profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? \App\Models\UserProfile::create(['user_id' => $user->id]);

        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'cover_photo' => 'nullable|image|max:5120',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('profiles/avatars', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            if ($profile->cover_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->cover_photo);
            }
            $validated['cover_photo'] = $request->file('cover_photo')->store('profiles/covers', 'public');
        }

        $profile->update($validated);

        return redirect()->route('community.profile.show', $user->id)
            ->with('success', 'Profil berhasil diperbarui');
    }
}
