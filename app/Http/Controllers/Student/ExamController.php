<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\QuizCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $enrolledCourseIds = \App\Models\CourseEnrollment::where('user_id', Auth::id())
            ->pluck('course_id');

        $exams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->with(['course', 'chapter'])
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->get();

        return view('student.exams.index', compact('exams'));
    }

    public function show($id)
    {
        $exam = Exam::with(['course', 'chapter', 'questions.answers'])
            ->where('is_active', true)
            ->findOrFail($id);

        // Check if user is enrolled
        $enrollment = \App\Models\CourseEnrollment::where('course_id', $exam->course_id)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('courses.content', $exam->course_id)
                ->with('error', 'Anda harus terdaftar dalam kursus ini untuk mengakses quiz.');
        }

        // Check if there are previous quizzes in the same chapter that must be passed first
        if ($exam->chapter_id) {
            $chapter = $exam->chapter;
            $allChapterExams = $chapter->exams()
                ->where('is_active', true)
                ->where('is_required_for_progression', true)
                ->orderBy('order')
                ->get();

            $currentIndex = $allChapterExams->search(function ($e) use ($exam) {
                return $e->id === $exam->id;
            });

            // If not first quiz, check if all previous quizzes are passed
            if ($currentIndex !== false && $currentIndex > 0) {
                for ($i = 0; $i < $currentIndex; $i++) {
                    $prevExam = $allChapterExams[$i];
                    if (!$prevExam->canProceed(Auth::user(), $enrollment)) {
                        return redirect()->route('courses.chapter', [$exam->course_id, $exam->chapter_id])
                            ->with('error', "Anda harus menyelesaikan dan lulus quiz '{$prevExam->title}' terlebih dahulu sebelum dapat mengakses quiz ini.");
                    }
                }
            }
        }

        // Check if user already has a completed attempt
        $completedAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['submitted', 'graded', 'passed', 'failed'])
            ->latest()
            ->first();

        if ($completedAttempt) {
            // If failed and retake is approved, allow retake (create new attempt below)
            if ($completedAttempt->status === 'failed' && $completedAttempt->retake_approved) {
                // Reset retake approval flag after allowing retake
                $completedAttempt->update([
                    'retake_approved' => false,
                    'retake_requested' => false,
                ]);
                // Allow retake - will create new attempt below
            } else {
                // Redirect to result page if already completed and no retake approval
                return redirect()->route('student.exams.result', $completedAttempt->id)
                    ->with('info', 'Anda sudah menyelesaikan quiz ini. Berikut adalah hasilnya.');
            }
        }

        // Check if user already has an in-progress attempt
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return view('student.exams.show', compact('exam', 'attempt'));
    }

    public function submit(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
        ]);

        $attempt->update([
            'answers' => $request->answers,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Auto-grade if no essay questions
        $hasEssay = $exam->questions()->where('question_type', 'essay')->exists();
        if (!$hasEssay) {
            $attempt->grade();
            
            // Auto-complete material/chapter if quiz passed and auto_complete_on_pass is enabled
            if ($attempt->isPassed() && $exam->auto_complete_on_pass) {
                $quizCompletionService = new QuizCompletionService();
                $quizCompletionService->completeMaterialOnQuizPass($attempt);
            }
        }

        // Redirect to result page after submission
        return redirect()->route('student.exams.result', $attempt->id)
            ->with('success', 'Quiz berhasil dikirim');
    }

    public function result($attemptId)
    {
        $attempt = ExamAttempt::where('user_id', Auth::id())
            ->with(['exam.questions.answers', 'exam.course'])
            ->findOrFail($attemptId);

        return view('student.exams.result', compact('attempt'));
    }

    public function requestRetake($attemptId)
    {
        $attempt = ExamAttempt::where('user_id', Auth::id())
            ->findOrFail($attemptId);

        // Only allow retake request if failed
        if ($attempt->status !== 'failed') {
            return redirect()->back()
                ->with('error', 'Anda hanya dapat meminta ulang quiz jika tidak lulus.');
        }

        // Update retake request - reset semua field dan set sebagai request baru
        $attempt->update([
            'retake_requested' => true,
            'retake_requested_at' => now(),
            'retake_approved' => false,
            'retake_approved_at' => null,
            'retake_approved_by' => null,
            'retake_rejection_reason' => null, // Clear rejection reason agar muncul di list lagi
        ]);

        // Create notification for instructor
        \App\Helpers\NotificationHelper::createQuizRetakeRequest($attempt);

        return redirect()->back()
            ->with('success', 'Permintaan ulang quiz telah dikirim. Menunggu persetujuan instruktur.');
    }
}
