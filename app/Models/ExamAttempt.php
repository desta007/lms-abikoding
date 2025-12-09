<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'score',
        'total_points',
        'percentage',
        'answers',
        'status',
        'retake_requested',
        'retake_requested_at',
        'retake_approved',
        'retake_approved_at',
        'retake_approved_by',
        'retake_rejection_reason',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
        'percentage' => 'decimal:2',
        'retake_requested' => 'boolean',
        'retake_requested_at' => 'datetime',
        'retake_approved' => 'boolean',
        'retake_approved_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function retakeApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retake_approved_by');
    }

    public function calculateScore(): int
    {
        $exam = $this->exam;
        $questions = $exam->questions()->with('answers')->get();
        $userAnswers = $this->answers ?? [];
        $score = 0;

        foreach ($questions as $question) {
            $questionId = $question->id;
            if (!isset($userAnswers[$questionId])) {
                continue;
            }

            $userAnswer = $userAnswers[$questionId];
            
            if ($question->question_type === 'multiple_choice') {
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                if ($correctAnswer && $userAnswer == $correctAnswer->id) {
                    $score += $question->points;
                }
            } elseif ($question->question_type === 'true_false') {
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                if ($correctAnswer && $userAnswer == $correctAnswer->id) {
                    $score += $question->points;
                }
            }
            // Essay questions need manual grading
        }

        return $score;
    }

    public function grade(): void
    {
        $totalPoints = $this->exam->questions()->sum('points');
        $score = $this->calculateScore();
        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;

        // Determine if passed based on minimum passing score
        $isPassed = $percentage >= $this->exam->minimum_passing_score;
        $status = $isPassed ? 'passed' : 'failed';

        $this->update([
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'status' => $status,
        ]);
    }

    /**
     * Check if attempt has passed the exam
     */
    public function isPassed(): bool
    {
        if (!$this->percentage || !$this->exam) {
            return false;
        }

        return $this->percentage >= $this->exam->minimum_passing_score;
    }
}
