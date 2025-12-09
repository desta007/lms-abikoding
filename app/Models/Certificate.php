<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'course_enrollment_id',
        'user_id',
        'course_id',
        'certificate_number',
        'verification_code',
        'file_path',
        'is_valid',
        'issued_at',
        'revoked_at',
        'revoked_reason',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_valid' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = static::generateVerificationCode();
            }
            // is_valid has default value in database, so we don't need to set it here
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CertificateVerification::class);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_valid', true)->whereNull('revoked_at');
    }

    public function scopeRevoked(Builder $query): Builder
    {
        return $query->where('is_valid', false)->orWhereNotNull('revoked_at');
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->verification_code);
    }

    public function revoke(?string $reason = null): bool
    {
        return $this->update([
            'is_valid' => false,
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);
    }

    public function verify(?string $ipAddress = null, ?string $userAgent = null): CertificateVerification
    {
        return $this->verifications()->create([
            'verified_at' => now(),
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    public static function generateCertificateNumber(): string
    {
        $date = date('Ymd');
        $prefix = "CERT-{$date}-";
        
        // Get the last certificate number for today
        $lastCertificate = static::where('certificate_number', 'like', "{$prefix}%")
            ->orderBy('certificate_number', 'desc')
            ->first();
        
        if ($lastCertificate) {
            // Extract the sequential number
            $lastNumber = (int) substr($lastCertificate->certificate_number, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(16));
        } while (static::where('verification_code', $code)->exists());
        
        return $code;
    }
}
