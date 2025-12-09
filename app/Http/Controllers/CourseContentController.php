<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudentProgress;
use App\Services\ProgressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContentController extends Controller
{
    public function index($courseId)
    {
        $course = Course::with(['chapters.materials'])->findOrFail($courseId);
        
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $progress = StudentProgress::where('course_enrollment_id', $enrollment->id)
            ->with(['chapterMaterial'])
            ->get()
            ->keyBy('chapter_material_id');

        // Load exams for each chapter
        $exams = \App\Models\Exam::where('course_id', $courseId)
            ->where('is_active', true)
            ->with(['questions', 'chapter'])
            ->get()
            ->groupBy('chapter_id');

        $overallProgress = $enrollment->calculateProgress();

        return view('courses.content', compact('course', 'enrollment', 'progress', 'overallProgress', 'exams'));
    }

    public function showChapter($courseId, $chapterId)
    {
        $course = Course::with(['chapters.materials'])->findOrFail($courseId);
        
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $chapter = $course->chapters()->with('materials')->findOrFail($chapterId);

        // Check progression access for chapter
        $progressionService = new ProgressionService();
        $chapterAccessCheck = $progressionService->canAccessChapter(Auth::user(), $chapter, $enrollment);
        
        if (!$chapterAccessCheck['allowed']) {
            return redirect()->route('courses.content', $courseId)
                ->with('error', $chapterAccessCheck['reason']);
        }

        $progress = StudentProgress::where('course_enrollment_id', $enrollment->id)
            ->where('chapter_id', $chapterId)
            ->get()
            ->keyBy('chapter_material_id');

        // Load exams for this chapter (ordered by order field)
        $exams = \App\Models\Exam::where('course_id', $courseId)
            ->where('chapter_id', $chapterId)
            ->where('is_active', true)
            ->with(['questions'])
            ->orderBy('order')
            ->get();

        return view('courses.chapter', compact('course', 'chapter', 'enrollment', 'progress', 'exams', 'chapterAccessCheck'));
    }

    public function showMaterial($courseId, $chapterId, $materialId)
    {
        $course = Course::with(['chapters.materials'])->findOrFail($courseId);
        
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $chapter = $course->chapters()->findOrFail($chapterId);
        $material = $chapter->materials()->findOrFail($materialId);

        // Check progression access
        $progressionService = new ProgressionService();
        $accessCheck = $progressionService->canAccessMaterial(Auth::user(), $material, $enrollment);
        
        if (!$accessCheck['allowed']) {
            return redirect()->route('courses.chapter', [$courseId, $chapterId])
                ->with('error', $accessCheck['reason']);
        }

        $progress = StudentProgress::firstOrCreate([
            'course_enrollment_id' => $enrollment->id,
            'chapter_id' => $chapterId,
            'chapter_material_id' => $materialId,
        ]);
        
        // Refresh to get latest data including rejection status
        $progress->refresh();

        return view('courses.material', compact('course', 'chapter', 'material', 'progress', 'enrollment', 'accessCheck'));
    }

    public function markComplete(Request $request)
    {
        // Students can mark as complete (pending approval), instructors can approve
        $isInstructor = Auth::user()->hasRole('instructor');
        
        if (!$isInstructor) {
            // Student marking as complete - set pending approval
            try {
                $request->validate([
                    'course_enrollment_id' => 'required|exists:course_enrollments,id',
                    'chapter_material_id' => 'required|exists:chapter_materials,id',
                    'chapter_id' => 'nullable|exists:chapters,id',
                    'is_completed' => 'nullable|boolean',
                ]);

                $enrollment = CourseEnrollment::where('id', $request->course_enrollment_id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                // Get the chapter_id from the material if not provided
                $chapterId = $request->chapter_id;
                if (!$chapterId) {
                    $material = \App\Models\ChapterMaterial::findOrFail($request->chapter_material_id);
                    $chapterId = $material->chapter_id;
                }

                // Determine if we're marking as complete or incomplete
                $isCompleted = $request->has('is_completed') ? (bool)$request->is_completed : true;

                $progress = StudentProgress::updateOrCreate(
                    [
                        'course_enrollment_id' => $enrollment->id,
                        'chapter_material_id' => $request->chapter_material_id,
                    ],
                    [
                        'chapter_id' => $chapterId,
                        'is_completed' => $isCompleted,
                        'completed_at' => $isCompleted ? now() : null,
                        'progress_percentage' => $isCompleted ? 100 : 0,
                        'completion_method' => 'manual',
                        'is_instructor_approved' => false,
                        'is_rejected' => false, // Reset rejection when student marks again
                        'rejected_at' => null,
                        'rejection_reason' => null,
                    ]
                );

                // Create notification for instructor if material is marked as complete
                if ($isCompleted) {
                    \App\Helpers\NotificationHelper::createMaterialApprovalRequest($progress);
                }

                return response()->json([
                    'success' => true,
                    'message' => $isCompleted ? 'Materi ditandai selesai. Menunggu persetujuan instruktur.' : 'Status materi direset.',
                    'is_completed' => $progress->is_completed,
                    'is_pending' => $progress->is_completed && !$progress->is_instructor_approved
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Error marking material as complete: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            $request->validate([
                'course_enrollment_id' => 'required|exists:course_enrollments,id',
                'chapter_material_id' => 'required|exists:chapter_materials,id',
                'chapter_id' => 'nullable|exists:chapters,id',
                'is_completed' => 'nullable|boolean',
            ]);

            $enrollment = CourseEnrollment::findOrFail($request->course_enrollment_id);
            
            // Verify instructor owns the course
            if ($enrollment->course->instructor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah progress ini.'
                ], 403);
            }

            // Get the chapter_id from the material if not provided
            $chapterId = $request->chapter_id;
            if (!$chapterId) {
                $material = \App\Models\ChapterMaterial::findOrFail($request->chapter_material_id);
                $chapterId = $material->chapter_id;
            }

            // Determine if we're marking as complete or incomplete
            $isCompleted = $request->has('is_completed') ? (bool)$request->is_completed : true;

            $progress = StudentProgress::updateOrCreate(
                [
                    'course_enrollment_id' => $enrollment->id,
                    'chapter_material_id' => $request->chapter_material_id,
                ],
                [
                    'chapter_id' => $chapterId,
                    'is_completed' => $isCompleted,
                    'completed_at' => $isCompleted ? now() : null,
                    'progress_percentage' => $isCompleted ? 100 : 0,
                ]
            );

            // Update enrollment progress
            $newProgress = $enrollment->calculateProgress();
            $enrollment->update([
                'progress_percentage' => $newProgress,
            ]);
            $enrollment->refresh();

            // Check if course is completed
            if ($enrollment->progress_percentage >= 100 && !$enrollment->completed_at) {
                $enrollment->update(['completed_at' => now()]);
                $enrollment->refresh();
                
                // Generate certificate automatically upon completion
                try {
                    $certificateService = app(\App\Services\CertificateService::class);
                    $certificateService->generate(Auth::user(), $enrollment->course, $enrollment);
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    \Illuminate\Support\Facades\Log::error('Failed to generate certificate: ' . $e->getMessage());
                    \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
                }
                
                // Fire course completed event if it exists
                if (class_exists(\App\Events\CourseCompleted::class)) {
                    event(new \App\Events\CourseCompleted(Auth::user(), $enrollment->course));
                }
            } elseif ($enrollment->progress_percentage < 100 && $enrollment->completed_at) {
                // If progress drops below 100%, remove completion status
                $enrollment->update(['completed_at' => null]);
            }

            return response()->json([
                'success' => true, 
                'progress' => $enrollment->progress_percentage,
                'is_completed' => $progress->is_completed
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error marking material as complete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'course_enrollment_id' => 'required|exists:course_enrollments,id',
            'chapter_material_id' => 'required|exists:chapter_materials,id',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'last_position' => 'nullable|integer',
        ]);

        $enrollment = CourseEnrollment::where('id', $request->course_enrollment_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        StudentProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollment->id,
                'chapter_material_id' => $request->chapter_material_id,
            ],
            [
                'chapter_id' => $request->chapter_id,
                'progress_percentage' => $request->progress_percentage,
                'last_position' => $request->last_position,
            ]
        );

        return response()->json(['success' => true]);
    }
}
