<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\StudentProgress;
use App\Models\ExamAttempt;
use App\Models\User;

class NotificationHelper
{
    /**
     * Create notification for material approval request
     */
    public static function createMaterialApprovalRequest(StudentProgress $progress): void
    {
        $enrollment = $progress->courseEnrollment;
        $course = $enrollment->course;
        $material = $progress->chapterMaterial;
        $student = $enrollment->user;
        $instructor = $course->instructor;

        Notification::create([
            'user_id' => $instructor->id,
            'type' => 'material_approval_request',
            'title' => 'Permintaan Persetujuan Materi',
            'message' => "Siswa {$student->full_name} meminta persetujuan untuk materi '{$material->title}' di kursus '{$course->title}'",
            'link' => route('instructor.progress.index', ['status' => 'pending']),
            'data' => [
                'progress_id' => $progress->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'material_id' => $material->id,
            ],
        ]);
    }

    /**
     * Create notification for quiz retake request
     */
    public static function createQuizRetakeRequest(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        $course = $exam->course;
        $student = $attempt->user;
        $instructor = $course->instructor;

        Notification::create([
            'user_id' => $instructor->id,
            'type' => 'quiz_retake_request',
            'title' => 'Permintaan Ulang Quiz',
            'message' => "Siswa {$student->full_name} meminta izin untuk mengulang quiz '{$exam->title}' di kursus '{$course->title}'",
            'link' => route('instructor.exams.retake-requests'),
            'data' => [
                'attempt_id' => $attempt->id,
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
            ],
        ]);
    }

    /**
     * Create notification for material approved
     */
    public static function createMaterialApproved(StudentProgress $progress): void
    {
        $enrollment = $progress->courseEnrollment;
        $course = $enrollment->course;
        $material = $progress->chapterMaterial;
        $student = $enrollment->user;

        Notification::create([
            'user_id' => $student->id,
            'type' => 'material_approved',
            'title' => 'Materi Disetujui',
            'message' => "Materi '{$material->title}' di kursus '{$course->title}' telah disetujui oleh instruktur. Anda dapat melanjutkan ke materi berikutnya.",
            'link' => route('courses.material', [$course->id, $material->chapter_id, $material->id]),
            'data' => [
                'progress_id' => $progress->id,
                'course_id' => $course->id,
                'material_id' => $material->id,
            ],
        ]);
    }

    /**
     * Create notification for material rejected
     */
    public static function createMaterialRejected(StudentProgress $progress): void
    {
        $enrollment = $progress->courseEnrollment;
        $course = $enrollment->course;
        $material = $progress->chapterMaterial;
        $student = $enrollment->user;
        $reason = $progress->rejection_reason;

        Notification::create([
            'user_id' => $student->id,
            'type' => 'material_rejected',
            'title' => 'Materi Ditolak',
            'message' => "Materi '{$material->title}' di kursus '{$course->title}' ditolak oleh instruktur." . ($reason ? " Alasan: {$reason}" : ''),
            'link' => route('courses.material', [$course->id, $material->chapter_id, $material->id]),
            'data' => [
                'progress_id' => $progress->id,
                'course_id' => $course->id,
                'material_id' => $material->id,
                'rejection_reason' => $reason,
            ],
        ]);
    }

    /**
     * Create notification for quiz retake approved
     */
    public static function createQuizRetakeApproved(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        $course = $exam->course;
        $student = $attempt->user;

        Notification::create([
            'user_id' => $student->id,
            'type' => 'quiz_retake_approved',
            'title' => 'Izin Quiz Ulang Disetujui',
            'message' => "Permintaan Anda untuk mengulang quiz '{$exam->title}' di kursus '{$course->title}' telah disetujui. Anda dapat mengulang quiz sekarang.",
            'link' => route('student.exams.show', $exam->id),
            'data' => [
                'attempt_id' => $attempt->id,
                'exam_id' => $exam->id,
                'course_id' => $course->id,
            ],
        ]);
    }

    /**
     * Create notification for quiz retake rejected
     */
    public static function createQuizRetakeRejected(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        $course = $exam->course;
        $student = $attempt->user;
        $reason = $attempt->retake_rejection_reason;

        Notification::create([
            'user_id' => $student->id,
            'type' => 'quiz_retake_rejected',
            'title' => 'Izin Quiz Ulang Ditolak',
            'message' => "Permintaan Anda untuk mengulang quiz '{$exam->title}' di kursus '{$course->title}' ditolak." . ($reason ? " Alasan: {$reason}" : ''),
            'link' => route('student.exams.result', $attempt->id),
            'data' => [
                'attempt_id' => $attempt->id,
                'exam_id' => $exam->id,
                'course_id' => $course->id,
                'rejection_reason' => $reason,
            ],
        ]);
    }
}

