<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'online_link',
        'event_type',
        'start_date',
        'end_date',
        'timezone',
        'max_attendees',
        'is_recurring',
        'recurrence_type',
        'recurrence_end_date',
        'is_public',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurrence_end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function canRegister(): bool
    {
        if ($this->max_attendees && $this->attendees()->where('status', 'registered')->count() >= $this->max_attendees) {
            return false;
        }
        return $this->start_date > now();
    }

    public function isFull(): bool
    {
        return $this->max_attendees && $this->attendees()->where('status', 'registered')->count() >= $this->max_attendees;
    }
}
