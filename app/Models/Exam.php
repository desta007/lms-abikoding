<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'course_id',
        'chapter_id',
        'order',
        'title',
        'description',
        'is_active',
        'start_date',
        'end_date',
        'duration_minutes',
        'minimum_passing_score',
        'is_required_for_progression',
        'chapter_material_id',
        'auto_complete_on_pass',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_required_for_progression' => 'boolean',
        'auto_complete_on_pass' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function chapterMaterial(): BelongsTo
    {
        return $this->belongsTo(ChapterMaterial::class);
    }

    /**
     * Check if an exam attempt has passed based on minimum passing score
     */
    public function isPassed(\App\Models\ExamAttempt $attempt): bool
    {
        if (!$attempt->percentage) {
            return false;
        }
        
        return $attempt->percentage >= $this->minimum_passing_score;
    }

    /**
     * Check if student can proceed to next material/chapter
     */
    public function canProceed(\App\Models\User $user, \App\Models\CourseEnrollment $enrollment): bool
    {
        if (!$this->is_required_for_progression) {
            return true;
        }

        // Check for passed attempt
        // Status 'passed' means already passed
        // Status 'graded' needs to check if percentage >= minimum_passing_score
        $passedAttempt = $this->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', ['passed', 'graded', 'failed'])
            ->get()
            ->filter(function ($attempt) {
                // For 'passed' status, it's already passed
                if ($attempt->status === 'passed') {
                    return true;
                }
                // For 'graded' or 'failed' status, check if score meets minimum passing score
                // (failed status might have retake that passed)
                if ($attempt->percentage && $attempt->percentage >= $this->minimum_passing_score) {
                    return true;
                }
                return false;
            })
            ->first();

        return $passedAttempt !== null;
    }
}
