<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExamAttempt;

class GradeSubmittedAttempts extends Command
{
    protected $signature = 'exams:grade-submitted {--attempt= : Specific attempt ID to grade}';
    protected $description = 'Grade all submitted exam attempts that should have been auto-graded';

    public function handle()
    {
        $attemptId = $this->option('attempt');
        
        $query = ExamAttempt::where('status', 'submitted')
            ->with('exam.questions');
        
        if ($attemptId) {
            $query->where('id', $attemptId);
        }
        
        $attempts = $query->get();
        
        if ($attempts->isEmpty()) {
            $this->info('No submitted attempts found to grade.');
            return 0;
        }
        
        $graded = 0;
        $skipped = 0;
        
        foreach ($attempts as $attempt) {
            // Check if exam has essay questions
            $hasEssay = $attempt->exam->questions()->where('question_type', 'essay')->exists();
            
            if ($hasEssay) {
                $this->warn("Attempt #{$attempt->id} has essay questions - requires manual grading.");
                $skipped++;
                continue;
            }
            
            // Grade the attempt
            $attempt->grade();
            $this->info("Graded attempt #{$attempt->id}: {$attempt->percentage}% - Status: {$attempt->status}");
            $graded++;
            
            // Auto-complete material/chapter if quiz passed and auto_complete_on_pass is enabled
            if ($attempt->isPassed() && $attempt->exam->auto_complete_on_pass) {
                $quizCompletionService = new \App\Services\QuizCompletionService();
                $quizCompletionService->completeMaterialOnQuizPass($attempt);
                $this->info("  -> Material/chapter auto-completed for passing student.");
            }
        }
        
        $this->info("\nCompleted: {$graded} graded, {$skipped} skipped (require manual grading).");
        
        return 0;
    }
}
