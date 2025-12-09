<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudentRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseStudentController extends Controller
{
    /**
     * Display list of enrolled students for a course
     */
    public function index($courseId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $courseQuery = Course::where('id', $courseId)->with(['enrollments.user', 'enrollments.progress']);
        
        if (!$user->isAdmin()) {
            $courseQuery->where('instructor_id', $userId);
        }
        
        $course = $courseQuery->firstOrFail();

        $enrollments = $course->enrollments()
            ->with(['user', 'progress'])
            ->latest('enrolled_at')
            ->paginate(20);

        // Load ratings for each student
        $ratingQuery = StudentRating::where('course_id', $courseId);
        
        if (!$user->isAdmin()) {
            $ratingQuery->where('instructor_id', $userId);
        }
        
        $studentRatings = $ratingQuery->get()->keyBy('student_id');

        return view('instructor.courses.students', compact('course', 'enrollments', 'studentRatings'));
    }

    /**
     * Show student details and rating form
     */
    public function show($courseId, $studentId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $courseQuery = Course::where('id', $courseId);
        
        if (!$user->isAdmin()) {
            $courseQuery->where('instructor_id', $userId);
        }
        
        $course = $courseQuery->firstOrFail();

        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->with(['user', 'progress.chapterMaterial'])
            ->firstOrFail();

        $ratingQuery = StudentRating::where('course_id', $courseId)
            ->where('student_id', $studentId);
        
        if (!$user->isAdmin()) {
            $ratingQuery->where('instructor_id', $userId);
        }
        
        $studentRating = $ratingQuery->first();

        return view('instructor.courses.student-detail', compact('course', 'enrollment', 'studentRating'));
    }
}
