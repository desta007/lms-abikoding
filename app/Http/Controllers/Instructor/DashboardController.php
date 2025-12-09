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
        $userId = Auth::id();
        $user = Auth::user();
        
        // Get courses (admin sees all, instructor sees their own)
        $courses = $user->isAdmin() 
            ? Course::all()
            : Course::where('instructor_id', $userId)->get();
        
        // Calculate statistics
        $enrollmentQuery = CourseEnrollment::query();
        $materialQuery = ChapterMaterial::query();
        $visitQuery = CourseView::query();
        $activeUserQuery = CourseEnrollment::query();
        $examQuery = Exam::query();
        
        if (!$user->isAdmin()) {
            $enrollmentQuery->whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
            $materialQuery->whereHas('chapter.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
            $visitQuery->whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
            $activeUserQuery->whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
            $examQuery->whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $totalEnrolledStudents = $enrollmentQuery->distinct('user_id')->count('user_id');
        $totalLessons = $materialQuery->count();
        $totalVisits = $visitQuery->count();
        $activeUsers = $activeUserQuery->where('updated_at', '>=', now()->subDays(30))
            ->distinct('user_id')->count('user_id');
        $activeExams = $examQuery->where('is_active', true)
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
