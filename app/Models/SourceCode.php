<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SourceCode extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'thumbnail',
        'description',
        'source_code_category_id',
        'level_id',
        'instructor_id',
        'price',
        'github_url',
        'demo_url',
        'technologies',
        'download_url',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2',
        'technologies' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sourceCode) {
            if (empty($sourceCode->slug)) {
                $sourceCode->slug = Str::slug($sourceCode->title);
            }
        });

        static::updating(function ($sourceCode) {
            // Regenerate slug if title changed
            if ($sourceCode->isDirty('title')) {
                $sourceCode->slug = Str::slug($sourceCode->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SourceCodeCategory::class, 'source_code_category_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('source_code_category_id', $categoryId);
    }

    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function getTechnologiesArrayAttribute(): array
    {
        return $this->technologies ?? [];
    }
}
