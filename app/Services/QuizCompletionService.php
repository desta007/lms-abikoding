<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\StudentProgress;
use App\Models\CourseEnrollment;

class QuizCompletionService
{
    /**
     * Complete material/chapter when quiz is passed
     */
    public function completeMaterialOnQuizPass(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        
        // Check if exam has auto_complete_on_pass enabled
        if (!$exam->auto_complete_on_pass) {
            return;
        }

        // Check if attempt passed
        if (!$attempt->isPassed()) {
            return;
        }

        // Get enrollment
        $enrollment = CourseEnrollment::where('user_id', $attempt->user_id)
            ->where('course_id', $exam->course_id)
            ->first();

        if (!$enrollment) {
            return;
        }

        // If exam is linked to a specific material
        if ($exam->chapter_material_id) {
            $this->completeMaterial($enrollment, $exam->chapter_material_id, $attempt);
        }
        // If exam is linked to a chapter
        elseif ($exam->chapter_id) {
            $this->completeChapter($enrollment, $exam->chapter_id, $attempt);
        }
    }

    /**
     * Complete a specific material
     */
    private function completeMaterial(CourseEnrollment $enrollment, int $materialId, ExamAttempt $attempt): void
    {
        $material = \App\Models\ChapterMaterial::find($materialId);
        if (!$material) {
            return;
        }

        StudentProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollment->id,
                'chapter_material_id' => $materialId,
            ],
            [
                'chapter_id' => $material->chapter_id,
                'is_completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100,
                'completion_method' => 'quiz_passed',
                'quiz_attempt_id' => $attempt->id,
                'is_instructor_approved' => true, // Auto-approved when passed quiz
                'approved_at' => now(),
            ]
        );
    }

    /**
     * Complete all materials in a chapter
     */
    private function completeChapter(CourseEnrollment $enrollment, int $chapterId, ExamAttempt $attempt): void
    {
        $chapter = \App\Models\Chapter::find($chapterId);
        if (!$chapter) {
            return;
        }

        // Mark all materials in chapter as complete
        foreach ($chapter->materials as $material) {
            StudentProgress::updateOrCreate(
                [
                    'course_enrollment_id' => $enrollment->id,
                    'chapter_material_id' => $material->id,
                ],
                [
                    'chapter_id' => $chapterId,
                    'is_completed' => true,
                    'completed_at' => now(),
                    'progress_percentage' => 100,
                    'completion_method' => 'quiz_passed',
                    'quiz_attempt_id' => $attempt->id,
                    'is_instructor_approved' => true, // Auto-approved when passed quiz
                    'approved_at' => now(),
                ]
            );
        }
    }
}

