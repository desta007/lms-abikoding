<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Notifications\CourseEnrolledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Anda sudah terdaftar dalam kursus ini');
        }

        // Check if course is paid
        if ($course->price > 0) {
            // Redirect to payment page
            return redirect()->route('payments.checkout', $course->id)
                ->with('info', 'Silakan selesaikan pembayaran untuk mengakses kursus');
        }

        // Enroll directly for free courses
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'progress_percentage' => 0,
        ]);

        // Send notification
        Auth::user()->notify(new CourseEnrolledNotification($course));

        return redirect()->route('courses.show', $course->slug)
            ->with('success', 'Berhasil mendaftar ke kursus!');
    }
}
