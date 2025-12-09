<?php

namespace App\Services;

use App\Models\User;
use App\Models\Chapter;
use App\Models\ChapterMaterial;
use App\Models\CourseEnrollment;

class ProgressionService
{
    /**
     * Check if user can access a material
     */
    public function canAccessMaterial(User $user, ChapterMaterial $material, CourseEnrollment $enrollment): array
    {
        $chapter = $material->chapter;
        
        // Check if all previous materials in the same chapter are completed and approved
        $previousMaterialsCheck = $this->checkPreviousMaterials($material, $enrollment);
        if (!$previousMaterialsCheck['allowed']) {
            return $previousMaterialsCheck;
        }

        // Check if material requires quiz
        if (!$material->quiz_required_for_next) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check if there's a required exam for this material
        $requiredExam = \App\Models\Exam::where('chapter_material_id', $material->id)
            ->where('is_required_for_progression', true)
            ->first();

        if (!$requiredExam) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check if user has passed the required exam
        if (!$requiredExam->canProceed($user, $enrollment)) {
            return [
                'allowed' => false,
                'reason' => "Anda harus lulus quiz '{$requiredExam->title}' terlebih dahulu untuk mengakses materi ini.",
                'exam' => $requiredExam,
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Check if all previous materials in the chapter are completed and approved
     */
    private function checkPreviousMaterials(ChapterMaterial $material, CourseEnrollment $enrollment): array
    {
        $chapter = $material->chapter;
        $materials = $chapter->materials()->orderBy('order')->get();
        
        $currentIndex = $materials->search(function ($m) use ($material) {
            return $m->id === $material->id;
        });

        // First material in chapter, check previous chapter
        if ($currentIndex === 0) {
            return $this->checkPreviousChapter($chapter, $enrollment);
        }

        // Check all previous materials in the same chapter
        for ($i = 0; $i < $currentIndex; $i++) {
            $prevMaterial = $materials[$i];
            $progress = \App\Models\StudentProgress::where('course_enrollment_id', $enrollment->id)
                ->where('chapter_material_id', $prevMaterial->id)
                ->first();

            // Material must exist in progress and be completed
            if (!$progress || !$progress->is_completed) {
                return [
                    'allowed' => false,
                    'reason' => "Anda harus menyelesaikan materi '{$prevMaterial->title}' terlebih dahulu.",
                ];
            }

            // Material must be approved by instructor OR completed via quiz
            if (!$progress->isCompleted()) {
                return [
                    'allowed' => false,
                    'reason' => "Materi '{$prevMaterial->title}' harus disetujui oleh instruktur terlebih dahulu sebelum Anda dapat melanjutkan.",
                ];
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Check if previous chapter is completed and approved
     */
    private function checkPreviousChapter(Chapter $chapter, CourseEnrollment $enrollment): array
    {
        $course = $chapter->course;
        $chapters = $course->chapters()->orderBy('order')->get();
        
        $currentIndex = $chapters->search(function ($ch) use ($chapter) {
            return $ch->id === $chapter->id;
        });

        // First chapter, always allowed
        if ($currentIndex === 0) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check previous chapter
        $prevChapter = $chapters[$currentIndex - 1];
        $prevMaterials = $prevChapter->materials()->orderBy('order')->get();

        // Check if all materials in previous chapter are completed and approved
        foreach ($prevMaterials as $prevMaterial) {
            $progress = \App\Models\StudentProgress::where('course_enrollment_id', $enrollment->id)
                ->where('chapter_material_id', $prevMaterial->id)
                ->first();

            if (!$progress || !$progress->is_completed) {
                return [
                    'allowed' => false,
                    'reason' => "Anda harus menyelesaikan semua materi di bab '{$prevChapter->title}' terlebih dahulu.",
                ];
            }

            if (!$progress->isCompleted()) {
                return [
                    'allowed' => false,
                    'reason' => "Semua materi di bab '{$prevChapter->title}' harus disetujui oleh instruktur terlebih dahulu sebelum Anda dapat melanjutkan.",
                ];
            }
        }

        // Check if previous chapter has required quizzes that must be passed
        $prevChapterExams = $prevChapter->exams()
            ->where('is_required_for_progression', true)
            ->orderBy('order')
            ->get();

        if ($prevChapterExams->isNotEmpty()) {
            $user = \App\Models\User::find($enrollment->user_id);
            // Check if all required quizzes in previous chapter are passed
            foreach ($prevChapterExams as $exam) {
                if (!$exam->canProceed($user, $enrollment)) {
                    return [
                        'allowed' => false,
                        'reason' => "Anda harus lulus quiz '{$exam->title}' di bab '{$prevChapter->title}' terlebih dahulu untuk mengakses materi ini.",
                        'exam' => $exam,
                    ];
                }
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Check if user can access a chapter
     */
    public function canAccessChapter(User $user, Chapter $chapter, CourseEnrollment $enrollment): array
    {
        $course = $chapter->course;
        $chapters = $course->chapters()->orderBy('order')->get();
        
        $currentIndex = $chapters->search(function ($ch) use ($chapter) {
            return $ch->id === $chapter->id;
        });

        // First chapter, always allowed
        if ($currentIndex === 0) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check previous chapter completion
        $prevChapter = $chapters[$currentIndex - 1];
        $prevMaterials = $prevChapter->materials()->orderBy('order')->get();

        // Check if all materials in previous chapter are completed and approved
        foreach ($prevMaterials as $prevMaterial) {
            $progress = \App\Models\StudentProgress::where('course_enrollment_id', $enrollment->id)
                ->where('chapter_material_id', $prevMaterial->id)
                ->first();

            if (!$progress || !$progress->is_completed) {
                return [
                    'allowed' => false,
                    'reason' => "Anda harus menyelesaikan semua materi di bab '{$prevChapter->title}' terlebih dahulu.",
                ];
            }

            if (!$progress->isCompleted()) {
                return [
                    'allowed' => false,
                    'reason' => "Semua materi di bab '{$prevChapter->title}' harus disetujui oleh instruktur terlebih dahulu sebelum Anda dapat melanjutkan.",
                ];
            }
        }

        // Check if previous chapter has required quizzes that must be passed
        $prevChapterExams = $prevChapter->exams()
            ->where('is_required_for_progression', true)
            ->orderBy('order')
            ->get();

        if ($prevChapterExams->isNotEmpty()) {
            // Check if all required quizzes in previous chapter are passed
            foreach ($prevChapterExams as $exam) {
                if (!$exam->canProceed($user, $enrollment)) {
                    return [
                        'allowed' => false,
                        'reason' => "Anda harus lulus quiz '{$exam->title}' di bab '{$prevChapter->title}' terlebih dahulu untuk mengakses bab ini.",
                        'exam' => $exam,
                    ];
                }
            }
        }

        // Check if current chapter requires quiz
        if (!$chapter->quiz_required_for_next) {
            return ['allowed' => true, 'reason' => null];
        }

        // Get all required exams for this chapter
        $requiredExams = $chapter->exams()
            ->where('is_required_for_progression', true)
            ->orderBy('order')
            ->get();

        if ($requiredExams->isEmpty()) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check if user has passed all required exams
        foreach ($requiredExams as $exam) {
            if (!$exam->canProceed($user, $enrollment)) {
                return [
                    'allowed' => false,
                    'reason' => "Anda harus lulus quiz '{$exam->title}' terlebih dahulu untuk mengakses bab ini.",
                    'exam' => $exam,
                ];
            }
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Check if user can proceed to next material in sequence
     */
    public function canProceedToNextMaterial(User $user, ChapterMaterial $currentMaterial, CourseEnrollment $enrollment): array
    {
        $chapter = $currentMaterial->chapter;
        $materials = $chapter->materials()->orderBy('order')->get();
        
        $currentIndex = $materials->search(function ($material) use ($currentMaterial) {
            return $material->id === $currentMaterial->id;
        });

        if ($currentIndex === false || $currentIndex === $materials->count() - 1) {
            // Last material in chapter, check if can proceed to next chapter
            return $this->canProceedToNextChapter($user, $chapter, $enrollment);
        }

        $nextMaterial = $materials[$currentIndex + 1];
        return $this->canAccessMaterial($user, $nextMaterial, $enrollment);
    }

    /**
     * Check if user can proceed to next chapter
     */
    public function canProceedToNextChapter(User $user, Chapter $currentChapter, CourseEnrollment $enrollment): array
    {
        $course = $currentChapter->course;
        $chapters = $course->chapters()->orderBy('order')->get();
        
        $currentIndex = $chapters->search(function ($chapter) use ($currentChapter) {
            return $chapter->id === $currentChapter->id;
        });

        if ($currentIndex === false || $currentIndex === $chapters->count() - 1) {
            // Last chapter, can proceed (course completion)
            return ['allowed' => true, 'reason' => null];
        }

        $nextChapter = $chapters[$currentIndex + 1];
        return $this->canAccessChapter($user, $nextChapter, $enrollment);
    }
}

