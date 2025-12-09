<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Get student's enrollments
        $enrollments = CourseEnrollment::where('user_id', $userId)
            ->with(['course.category', 'course.level', 'course.instructor'])
            ->orderBy('enrolled_at', 'desc')
            ->take(5)
            ->get();
        
        // Calculate statistics
        $totalEnrollments = CourseEnrollment::where('user_id', $userId)->count();
        $completedCourses = CourseEnrollment::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();
        
        $inProgressCourses = CourseEnrollment::where('user_id', $userId)
            ->whereNull('completed_at')
            ->count();
        
        $totalCertificates = Auth::user()->certificates()->count();
        
        // Get upcoming exams (exams the student hasn't completed yet)
        $upcomingExams = Exam::whereHas('course.enrollments', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->where('is_active', true)
        ->where(function($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        })
        ->whereDoesntHave('attempts', function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->whereIn('status', ['submitted', 'graded']);
        })
        ->with('course')
        ->orderBy('start_date', 'asc')
        ->take(5)
        ->get();
        
        $stats = [
            'total_enrollments' => $totalEnrollments,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'total_certificates' => $totalCertificates,
        ];
        
        return view('student.dashboard', compact('stats', 'enrollments', 'upcomingExams'));
    }
}

