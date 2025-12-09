<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ChapterMaterial;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseView;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();
        
        // Get instructor's courses
        $courses = Course::where('instructor_id', $instructorId)->get();
        
        // Calculate statistics
        $totalEnrolledStudents = CourseEnrollment::whereHas('course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->distinct('user_id')->count('user_id');
        
        $totalLessons = ChapterMaterial::whereHas('chapter.course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->count();
        
        $totalVisits = CourseView::whereHas('course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->count();
        
        $activeUsers = CourseEnrollment::whereHas('course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->where('updated_at', '>=', now()->subDays(30))
        ->distinct('user_id')->count('user_id');
        
        $activeExams = Exam::whereHas('course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->where('is_active', true)
        ->where(function($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        })
        ->count();
        
        $stats = [
            'total_enrolled_students' => $totalEnrolledStudents,
            'total_lessons' => $totalLessons,
            'total_visits' => $totalVisits,
            'active_users' => $activeUsers,
            'active_exams' => $activeExams,
        ];
        
        return view('instructor.dashboard', compact('stats', 'courses'));
    }
}
