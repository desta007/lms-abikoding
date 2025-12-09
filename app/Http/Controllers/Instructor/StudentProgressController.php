<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentProgress::with([
            'courseEnrollment.user',
            'courseEnrollment.course',
            'chapter',
            'chapterMaterial',
            'quizAttempt.exam'
        ]);
        
        // Admin can see all progress, instructor only sees their own courses
        if (!$user->isAdmin()) {
            $query->whereHas('courseEnrollment.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }

        // Filter by course
        if ($request->has('course') && $request->course) {
            $query->whereHas('courseEnrollment', function($q) use ($request) {
                $q->where('course_id', $request->course);
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'pending') {
                $query->where('is_completed', true)
                      ->where('is_instructor_approved', false)
                      ->where('completion_method', '!=', 'quiz_passed');
            } elseif ($request->status === 'approved') {
                $query->where('is_instructor_approved', true);
            } elseif ($request->status === 'quiz_passed') {
                $query->where('completion_method', 'quiz_passed');
            }
        } else {
            // Default: show pending approvals (when no status filter is set)
            $query->where('is_completed', true)
                  ->where('is_instructor_approved', false)
                  ->where('completion_method', '!=', 'quiz_passed');
        }

        $progresses = $query->latest('updated_at')->paginate(20);
        $courses = $user->isAdmin()
            ? Course::all()
            : Course::where('instructor_id', $userId)->get();

        return view('instructor.progress.index', compact('progresses', 'courses'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentProgress::with([
            'courseEnrollment.user',
            'courseEnrollment.course',
            'chapter',
            'chapterMaterial',
            'quizAttempt.exam',
            'approvedBy'
        ]);
        
        if (!$user->isAdmin()) {
            $query->whereHas('courseEnrollment.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $progress = $query->findOrFail($id);

        return view('instructor.progress.show', compact('progress'));
    }

    public function approve($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentProgress::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('courseEnrollment.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $progress = $query->findOrFail($id);

        $progress->approveBy(Auth::user());

        // Update enrollment progress
        $enrollment = $progress->courseEnrollment;
        $newProgress = $enrollment->calculateProgress();
        $enrollment->update(['progress_percentage' => $newProgress]);

        // Create notification for student
        \App\Helpers\NotificationHelper::createMaterialApproved($progress);

        return redirect()->back()->with('success', 'Progress siswa berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentProgress::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('courseEnrollment.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $progress = $query->findOrFail($id);

        $progress->update([
            'is_completed' => false,
            'is_instructor_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
            'completed_at' => null,
            'completion_method' => 'manual',
            'is_rejected' => true,
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Update enrollment progress
        $enrollment = $progress->courseEnrollment;
        $newProgress = $enrollment->calculateProgress();
        $enrollment->update(['progress_percentage' => $newProgress]);

        // Create notification for student
        \App\Helpers\NotificationHelper::createMaterialRejected($progress);

        return redirect()->back()->with('success', 'Progress siswa ditolak. Siswa akan melihat notifikasi dan dapat mengajukan ulang.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'progress_ids' => 'required',
        ]);

        // Handle both array and JSON string
        $progressIds = is_array($request->progress_ids) 
            ? $request->progress_ids 
            : json_decode($request->progress_ids, true);

        if (!is_array($progressIds) || empty($progressIds)) {
            return redirect()->back()->with('error', 'Tidak ada progress yang dipilih');
        }

        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentProgress::whereIn('id', $progressIds);
        
        if (!$user->isAdmin()) {
            $query->whereHas('courseEnrollment.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $progresses = $query->get();

        foreach ($progresses as $progress) {
            $progress->approveBy(Auth::user());
            
            // Update enrollment progress
            $enrollment = $progress->courseEnrollment;
            $newProgress = $enrollment->calculateProgress();
            $enrollment->update(['progress_percentage' => $newProgress]);
        }

        return redirect()->back()->with('success', count($progresses) . ' progress berhasil disetujui');
    }
}
