<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function calculateProgress(): int
    {
        $course = $this->course;
        $totalMaterials = $course->chapters->sum(fn($ch) => $ch->materials->count());
        
        if ($totalMaterials == 0) {
            return 0;
        }
        
        // Only count materials that are completed AND approved (or passed via quiz)
        $completedMaterials = $this->progress()
            ->get()
            ->filter(function ($progress) {
                return $progress->isCompleted();
            })
            ->count();
        
        return round(($completedMaterials / $totalMaterials) * 100);
    }

    public function completedChaptersCount(): int
    {
        return $this->progress()
            ->where('is_completed', true)
            ->distinct('chapter_id')
            ->count('chapter_id');
    }

    public function totalProgressPercentage(): int
    {
        return $this->calculateProgress();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if student has completed all materials in the course
     */
    public function hasCompletedAllMaterials(): bool
    {
        $course = $this->course;
        $totalMaterials = $course->chapters->sum(fn($ch) => $ch->materials->count());
        
        if ($totalMaterials == 0) {
            return false; // No materials to complete
        }
        
        $completedMaterials = $this->progress()
            ->where('is_completed', true)
            ->count();
        
        return $completedMaterials >= $totalMaterials;
    }

    /**
     * Check if student has completed all chapters
     */
    public function hasCompletedAllChapters(): bool
    {
        $course = $this->course;
        $totalChapters = $course->chapters->count();
        
        if ($totalChapters == 0) {
            return false;
        }
        
        // Get all unique chapter IDs that have all materials completed
        $chapters = $course->chapters;
        $completedChapters = 0;
        
        foreach ($chapters as $chapter) {
            $totalMaterials = $chapter->materials->count();
            if ($totalMaterials == 0) {
                continue; // Skip chapters with no materials
            }
            
            $completedMaterials = $this->progress()
                ->where('chapter_id', $chapter->id)
                ->where('is_completed', true)
                ->count();
            
            if ($completedMaterials >= $totalMaterials) {
                $completedChapters++;
            }
        }
        
        return $completedChapters >= $totalChapters;
    }
}
