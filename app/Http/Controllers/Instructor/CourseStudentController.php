<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudentRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Download student's certificate (Instructor can download regardless of student progress)
     */
    public function downloadCertificate($courseId, $studentId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Verify course belongs to this instructor or user is admin
        $courseQuery = Course::where('id', $courseId)->with('instructor');
        
        if (!$user->isAdmin()) {
            $courseQuery->where('instructor_id', $userId);
        }
        
        $course = $courseQuery->firstOrFail();

        // Get the student
        $student = User::findOrFail($studentId);

        // Get the enrollment
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Siswa tidak terdaftar di kursus ini.');
        }

        // Get or create the certificate
        $certificate = Certificate::where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->with(['course', 'user', 'course.instructor'])
            ->first();

        // If certificate doesn't exist, create one on-the-fly (for instructor use only)
        if (!$certificate) {
            try {
                $certificate = Certificate::create([
                    'course_enrollment_id' => $enrollment->id,
                    'user_id' => $studentId,
                    'course_id' => $courseId,
                    'issued_at' => now(),
                    'is_valid' => true,
                ]);
                $certificate->load(['course', 'user', 'course.instructor']);
            } catch (\Exception $e) {
                \Log::error('Failed to create certificate: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
            }
        }

        // If PDF exists in storage, return it
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            try {
                return Storage::disk('public')->download(
                    $certificate->file_path,
                    'certificate-' . $certificate->certificate_number . '.pdf'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to download existing PDF: ' . $e->getMessage());
                // Fall through to generate on-the-fly
            }
        }

        // Otherwise, generate on-the-fly
        return $this->generatePdf($certificate);
    }

    /**
     * Generate PDF on-the-fly for certificate download
     */
    private function generatePdf(Certificate $certificate)
    {
        $certificate->load(['course', 'user', 'course.instructor']);

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'instructor' => $certificate->course->instructor ?? null,
        ];

        try {
            $pdf = Pdf::loadView('certificates.template', $data);
            $pdf->setPaper('a4', 'landscape');
            
            return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal membuat PDF sertifikat: ' . $e->getMessage());
        }
    }
}
