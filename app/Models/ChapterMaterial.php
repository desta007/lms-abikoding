<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChapterMaterial extends Model
{
    protected $fillable = [
        'chapter_id',
        'material_type',
        'title',
        'content',
        'file_path',
        'file_size',
        'file_mime_type',
        'order',
        'duration',
        'video_url',
        'video_file_path',
        'video_file_size',
        'video_file_mime_type',
        'pdf_file_path',
        'pdf_file_size',
        'pdf_file_mime_type',
        'image_file_path',
        'image_file_size',
        'image_file_mime_type',
        'text_content',
        'has_quiz',
        'quiz_required_for_next',
    ];

    protected $casts = [
        'material_type' => 'string',
        'has_quiz' => 'boolean',
        'quiz_required_for_next' => 'boolean',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function exam(): HasOne
    {
        return $this->hasOne(Exam::class, 'chapter_material_id');
    }

    /**
     * Check if material has required quiz
     */
    public function hasRequiredQuiz(): bool
    {
        return Exam::where('chapter_material_id', $this->id)
            ->where('is_required_for_progression', true)
            ->exists();
    }

    /**
     * Check if student can proceed to next material
     */
    public function canStudentProceed(\App\Models\User $user, \App\Models\CourseEnrollment $enrollment): bool
    {
        if (!$this->quiz_required_for_next) {
            return true;
        }

        $requiredExam = Exam::where('chapter_material_id', $this->id)
            ->where('is_required_for_progression', true)
            ->first();

        if (!$requiredExam) {
            return true;
        }

        return $requiredExam->canProceed($user, $enrollment);
    }
}
