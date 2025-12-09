<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broadcast extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_url',
        'thumbnail',
        'is_live',
        'scheduled_at',
        'started_at',
        'ended_at',
        'zoom_meeting_id',
        'zoom_meeting_password',
        'zoom_join_url',
        'zoom_start_url',
        'is_zoom_meeting',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_zoom_meeting' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(BroadcastLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BroadcastComment::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(BroadcastView::class);
    }

    public function scopeLive($query)
    {
        return $query->where('is_live', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('is_live', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get Zoom join URL for participants
     */
    public function getZoomJoinUrl(): ?string
    {
        return $this->zoom_join_url;
    }

    /**
     * Get Zoom start URL for host
     */
    public function getZoomStartUrl(): ?string
    {
        return $this->zoom_start_url;
    }
}
