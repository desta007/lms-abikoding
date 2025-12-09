<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Post;
use App\Models\Broadcast;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_courses' => Course::count(),
            'total_enrollments' => CourseEnrollment::count(),
            'total_posts' => Post::count(),
            'total_broadcasts' => Broadcast::count(),
            'total_events' => Event::count(),
            'active_enrollments' => CourseEnrollment::where('updated_at', '>=', now()->subDays(30))->count(),
        ];

        $recentStudents = User::where('role', 'student')
            ->latest()
            ->take(10)
            ->get();

        $recentCourses = Course::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentStudents', 'recentCourses'));
    }
}
