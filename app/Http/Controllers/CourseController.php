<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseRating;
use App\Models\CourseView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show($slug)
    {
        $course = Course::with(['category', 'level', 'instructor', 'ratings.user', 'chapters' => function($query) {
            $query->orderBy('order');
        }, 'chapters.materials'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Sort chapters by order after loading
        $course->chapters = $course->chapters->sortBy('order')->values();

        // Check if user is enrolled
        $isEnrolled = false;
        $enrollment = null;
        $canRate = false;
        $existingRating = null;
        
        if (Auth::check()) {
            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', Auth::id())
                ->first();
            $isEnrolled = $enrollment !== null;
            
            if ($isEnrolled) {
                $canRate = $enrollment->hasCompletedAllMaterials();
                $existingRating = CourseRating::where('course_id', $course->id)
                    ->where('user_id', Auth::id())
                    ->first();
            }
        }

        // Track view
        if (!$isEnrolled || !Auth::check()) {
            CourseView::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);
        }

        // Get similar courses
        $similarCourses = Course::published()
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->with(['category', 'level', 'instructor', 'ratings'])
            ->take(6)
            ->get();

        return view('courses.show', compact('course', 'similarCourses', 'isEnrolled', 'enrollment', 'canRate', 'existingRating'));
    }
}
