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
        $course = Course::where('id', $courseId)
            ->where('instructor_id', Auth::id())
            ->with(['enrollments.user', 'enrollments.progress'])
            ->firstOrFail();

        $enrollments = $course->enrollments()
            ->with(['user', 'progress'])
            ->latest('enrolled_at')
            ->paginate(20);

        // Load ratings for each student
        $studentRatings = StudentRating::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->get()
            ->keyBy('student_id');

        return view('instructor.courses.students', compact('course', 'enrollments', 'studentRatings'));
    }

    /**
     * Show student details and rating form
     */
    public function show($courseId, $studentId)
    {
        $course = Course::where('id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->with(['user', 'progress.chapterMaterial'])
            ->firstOrFail();

        $studentRating = StudentRating::where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->where('instructor_id', Auth::id())
            ->first();

        return view('instructor.courses.student-detail', compact('course', 'enrollment', 'studentRating'));
    }
}
