<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    protected $fillable = [
        'course_enrollment_id',
        'chapter_id',
        'chapter_material_id',
        'is_completed',
        'completed_at',
        'progress_percentage',
        'last_position',
        'is_instructor_approved',
        'approved_at',
        'approved_by',
        'completion_method',
        'quiz_attempt_id',
        'is_rejected',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'is_instructor_approved' => 'boolean',
        'approved_at' => 'datetime',
        'is_rejected' => 'boolean',
        'rejected_at' => 'datetime',
    ];

    public function courseEnrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function chapterMaterial(): BelongsTo
    {
        return $this->belongsTo(ChapterMaterial::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * Approve progress by instructor
     */
    public function approveBy(\App\Models\User $instructor): void
    {
        $this->update([
            'is_instructor_approved' => true,
            'approved_at' => now(),
            'approved_by' => $instructor->id,
            'completion_method' => 'instructor_approved',
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if progress is truly completed (approved or quiz passed)
     */
    public function isCompleted(): bool
    {
        if (!$this->is_completed) {
            return false;
        }

        // If completed via quiz, it's automatically valid
        if ($this->completion_method === 'quiz_passed') {
            return true;
        }

        // If completed manually, need instructor approval
        if ($this->completion_method === 'manual' || $this->completion_method === 'instructor_approved') {
            return $this->is_instructor_approved;
        }

        return false;
    }

    /**
     * Check if student can mark as complete (they can't anymore, but keep for backward compatibility)
     */
    public function canMarkComplete(): bool
    {
        return false; // Students can no longer mark complete manually
    }
}
