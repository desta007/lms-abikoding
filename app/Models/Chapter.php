<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
        'has_quiz',
        'quiz_required_for_next',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'has_quiz' => 'boolean',
        'quiz_required_for_next' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ChapterMaterial::class)->orderBy('order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class)->orderBy('order');
    }

    /**
     * Check if chapter has required quiz
     */
    public function hasRequiredQuiz(): bool
    {
        return $this->exams()
            ->where('is_required_for_progression', true)
            ->exists();
    }

    /**
     * Check if student can proceed to next chapter
     */
    public function canStudentProceed(\App\Models\User $user, \App\Models\CourseEnrollment $enrollment): bool
    {
        if (!$this->quiz_required_for_next) {
            return true;
        }

        $requiredExams = $this->exams()
            ->where('is_required_for_progression', true)
            ->get();

        foreach ($requiredExams as $exam) {
            if (!$exam->canProceed($user, $enrollment)) {
                return false;
            }
        }

        return true;
    }
}
