<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can see all courses, instructor only sees their own
        $courses = $user->isAdmin()
            ? \App\Models\Course::all()
            : \App\Models\Course::where('instructor_id', $userId)->get();
        
        $query = $user->isAdmin()
            ? Exam::with(['course', 'chapter', 'questions'])
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with(['course', 'chapter', 'questions']);

        // Filter by course
        if ($request->has('course') && $request->course) {
            $query->where('course_id', $request->course);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $exams = $query->latest()->get();

        return view('instructor.exams.index', compact('exams', 'courses'));
    }

    public function create(Request $request, $chapterId = null)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can see all courses, instructor only sees their own
        $courses = $user->isAdmin()
            ? \App\Models\Course::all()
            : \App\Models\Course::where('instructor_id', $userId)->get();
        
        // Get chapter_id from route parameter or request
        $chapterId = $chapterId ?? $request->get('chapter_id');
        $chapter = null;
        
        if ($chapterId) {
            $chapterQuery = $user->isAdmin()
                ? \App\Models\Chapter::with('course')
                : \App\Models\Chapter::whereHas('course', function($q) use ($userId) {
                    $q->where('instructor_id', $userId);
                })->with('course');
            
            $chapter = $chapterQuery->findOrFail($chapterId);
        }
        
        return view('instructor.exams.create', compact('courses', 'chapter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'chapter_material_id' => 'nullable|exists:chapter_materials,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'duration_minutes' => 'nullable|integer|min:1',
            'minimum_passing_score' => 'nullable|integer|min:0|max:100',
            'is_required_for_progression' => 'nullable|boolean',
            'auto_complete_on_pass' => 'nullable|boolean',
        ]);

        // Verify course ownership (admin can access all courses)
        $user = Auth::user();
        $courseQuery = \App\Models\Course::where('id', $validated['course_id']);
        
        if (!$user->isAdmin()) {
            $courseQuery->where('instructor_id', Auth::id());
        }
        
        $course = $courseQuery->firstOrFail();

        $validated['is_active'] = $request->has('is_active');
        $validated['minimum_passing_score'] = $validated['minimum_passing_score'] ?? 70;
        $validated['is_required_for_progression'] = $request->has('is_required_for_progression');
        $validated['auto_complete_on_pass'] = $request->has('auto_complete_on_pass') ? true : ($validated['auto_complete_on_pass'] ?? true);

        // Update chapter has_quiz flag if chapter_id is provided
        if (isset($validated['chapter_id'])) {
            \App\Models\Chapter::where('id', $validated['chapter_id'])->update(['has_quiz' => true]);
        }

        // Update material has_quiz flag if chapter_material_id is provided
        if (isset($validated['chapter_material_id'])) {
            \App\Models\ChapterMaterial::where('id', $validated['chapter_material_id'])->update(['has_quiz' => true]);
        }

        $exam = Exam::create($validated);

        return redirect()->route('instructor.exams.questions', $exam->id)
            ->with('success', 'Quiz berhasil dibuat. Tambahkan pertanyaan sekarang.');
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::with(['course.chapters', 'chapter'])
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with(['course.chapters', 'chapter']);
        
        $exam = $query->findOrFail($id);

        // Admin can see all courses, instructor only sees their own
        $courses = $user->isAdmin()
            ? \App\Models\Course::all()
            : \App\Models\Course::where('instructor_id', $userId)->get();

        return view('instructor.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::query()
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $exam = $query->findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'chapter_material_id' => 'nullable|exists:chapter_materials,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'duration_minutes' => 'nullable|integer|min:1',
            'minimum_passing_score' => 'nullable|integer|min:0|max:100',
            'is_required_for_progression' => 'nullable|boolean',
            'auto_complete_on_pass' => 'nullable|boolean',
        ]);

        // Verify course ownership if changing course
        if ($validated['course_id'] != $exam->course_id) {
            $courseQuery = \App\Models\Course::where('id', $validated['course_id']);
            if (!$user->isAdmin()) {
                $courseQuery->where('instructor_id', $userId);
            }
            $courseQuery->firstOrFail();
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['minimum_passing_score'] = $validated['minimum_passing_score'] ?? 70;
        $validated['is_required_for_progression'] = $request->has('is_required_for_progression');
        $validated['auto_complete_on_pass'] = $request->has('auto_complete_on_pass');

        // Handle chapter changes
        $oldChapterId = $exam->chapter_id;
        $newChapterId = $validated['chapter_id'] ?? null;

        // If chapter changed, update has_quiz flags
        if ($oldChapterId != $newChapterId) {
            // Remove has_quiz from old chapter if no other exams
            if ($oldChapterId) {
                $otherExamsInOldChapter = Exam::where('chapter_id', $oldChapterId)
                    ->where('id', '!=', $exam->id)
                    ->count();
                if ($otherExamsInOldChapter === 0) {
                    \App\Models\Chapter::where('id', $oldChapterId)->update(['has_quiz' => false]);
                }
            }
            // Add has_quiz to new chapter
            if ($newChapterId) {
                \App\Models\Chapter::where('id', $newChapterId)->update(['has_quiz' => true]);
            }
        }

        $exam->update($validated);

        return redirect()->route('instructor.exams.show', $exam->id)
            ->with('success', 'Quiz berhasil diperbarui.');
    }

    public function show($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::with(['questions.answers', 'attempts.user'])
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with(['questions.answers', 'attempts.user']);
        
        $exam = $query->findOrFail($id);

        return view('instructor.exams.show', compact('exam'));
    }

    public function questions($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::with(['questions.answers'])
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })->with(['questions.answers']);
        
        $exam = $query->findOrFail($id);

        return view('instructor.exams.questions', compact('exam'));
    }

    public function addQuestion(Request $request, $examId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::query()
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $exam = $query->findOrFail($examId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|integer|min:1',
            'answers' => 'required_if:question_type,multiple_choice,true_false|array',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'nullable',
        ]);

        // Check if at least one answer is marked as correct for multiple choice/true false
        if (in_array($validated['question_type'], ['multiple_choice', 'true_false'])) {
            $hasCorrectAnswer = false;
            foreach ($validated['answers'] ?? [] as $answerData) {
                $isCorrect = false;
                if (isset($answerData['is_correct'])) {
                    $isCorrect = $answerData['is_correct'] == '1' || $answerData['is_correct'] === true || $answerData['is_correct'] === 'true';
                }
                if ($isCorrect) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }
            
            if (!$hasCorrectAnswer) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['answers' => 'Setidaknya satu jawaban harus ditandai sebagai benar.']);
            }
        }

        $order = ($exam->questions()->max('order') ?? 0) + 1;

        $question = Question::create([
            'exam_id' => $exam->id,
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'order' => $order,
        ]);

        if (in_array($validated['question_type'], ['multiple_choice', 'true_false'])) {
            foreach ($validated['answers'] as $index => $answerData) {
                // Handle checkbox value - can be '0', '1', or not set
                $isCorrect = false;
                if (isset($answerData['is_correct'])) {
                    $isCorrect = $answerData['is_correct'] == '1' || $answerData['is_correct'] === true || $answerData['is_correct'] === 'true';
                }
                
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['text'],
                    'is_correct' => $isCorrect,
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    public function deleteQuestion($examId, $questionId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::query()
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $exam = $query->findOrFail($examId);

        $question = Question::where('exam_id', $exam->id)->findOrFail($questionId);
        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus');
    }

    public function attempts($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::query()
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $exam = $query->findOrFail($id);

        $attempts = \App\Models\ExamAttempt::where('exam_id', $exam->id)
            ->with(['user'])
            ->latest()
            ->get();

        return view('instructor.exams.attempts', compact('exam', 'attempts'));
    }

    public function retakeRequests()
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = \App\Models\ExamAttempt::with([
            'exam.course',
            'user',
            'retakeApprovedBy'
        ]);
        
        // Admin can see all retake requests, instructor only sees their own courses
        if (!$user->isAdmin()) {
            $query->whereHas('exam.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $retakeRequests = $query
        ->where('retake_requested', true)
        ->where('retake_approved', false)
        ->where('status', 'failed')
        ->whereNull('retake_rejection_reason') // Hanya tampilkan yang belum ditolak
        ->latest('retake_requested_at')
        ->paginate(20);

        return view('instructor.exams.retake-requests', compact('retakeRequests'));
    }

    public function approveRetake($attemptId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = \App\Models\ExamAttempt::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('exam.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $attempt = $query->findOrFail($attemptId);

        if (!$attempt->retake_requested) {
            return redirect()->back()
                ->with('error', 'Tidak ada permintaan ulang quiz untuk attempt ini.');
        }

        $attempt->update([
            'retake_approved' => true,
            'retake_approved_at' => now(),
            'retake_approved_by' => Auth::id(),
            'retake_rejection_reason' => null,
        ]);

        // Create notification for student
        \App\Helpers\NotificationHelper::createQuizRetakeApproved($attempt);

        return redirect()->back()
            ->with('success', 'Permintaan ulang quiz telah disetujui. Siswa sekarang dapat mengulang quiz.');
    }

    public function rejectRetake(Request $request, $attemptId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        $query = \App\Models\ExamAttempt::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('exam.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $attempt = $query->findOrFail($attemptId);

        if (!$attempt->retake_requested) {
            return redirect()->back()
                ->with('error', 'Tidak ada permintaan ulang quiz untuk attempt ini.');
        }

        $attempt->update([
            'retake_approved' => false,
            'retake_approved_at' => null,
            'retake_approved_by' => null,
            'retake_rejection_reason' => $request->rejection_reason,
            'retake_requested' => false, // Set false agar tidak muncul di list lagi
        ]);

        // Create notification for student
        \App\Helpers\NotificationHelper::createQuizRetakeRejected($attempt);

        return redirect()->back()
            ->with('success', 'Permintaan ulang quiz telah ditolak. Data akan hilang dari list dan siswa dapat mengajukan ulang jika diperlukan.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = $user->isAdmin()
            ? Exam::query()
            : Exam::whereHas('course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        
        $exam = $query->findOrFail($id);

        // Update chapter has_quiz flag if this was the only quiz for the chapter
        if ($exam->chapter_id) {
            $otherExamsInChapter = Exam::where('chapter_id', $exam->chapter_id)
                ->where('id', '!=', $exam->id)
                ->count();
            
            if ($otherExamsInChapter === 0) {
                \App\Models\Chapter::where('id', $exam->chapter_id)->update(['has_quiz' => false]);
            }
        }

        // Update material has_quiz flag if this was the only quiz for the material
        if ($exam->chapter_material_id) {
            $otherExamsInMaterial = Exam::where('chapter_material_id', $exam->chapter_material_id)
                ->where('id', '!=', $exam->id)
                ->count();
            
            if ($otherExamsInMaterial === 0) {
                \App\Models\ChapterMaterial::where('id', $exam->chapter_material_id)->update(['has_quiz' => false]);
            }
        }

        // Delete all related data (questions, answers, attempts will be cascade deleted if set up)
        $exam->questions()->each(function($question) {
            $question->answers()->delete();
        });
        $exam->questions()->delete();
        $exam->attempts()->delete();
        $exam->delete();

        return redirect()->route('instructor.exams.index')
            ->with('success', 'Quiz berhasil dihapus.');
    }

    public function gradeAttempt($attemptId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = \App\Models\ExamAttempt::with(['exam.questions.answers', 'user']);
        
        if (!$user->isAdmin()) {
            $query->whereHas('exam.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $attempt = $query->findOrFail($attemptId);
        
        // Check if this attempt needs manual grading
        $hasEssay = $attempt->exam->questions()->where('question_type', 'essay')->exists();
        
        if (!$hasEssay && !in_array($attempt->status, ['submitted', 'graded'])) {
            return redirect()->route('instructor.exams.attempts', $attempt->exam_id)
                ->with('info', 'Quiz ini sudah di-grade secara otomatis.');
        }
        
        return view('instructor.exams.grade-attempt', compact('attempt'));
    }

    public function saveGrade(Request $request, $attemptId)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = \App\Models\ExamAttempt::with(['exam.questions']);
        
        if (!$user->isAdmin()) {
            $query->whereHas('exam.course', function($q) use ($userId) {
                $q->where('instructor_id', $userId);
            });
        }
        
        $attempt = $query->findOrFail($attemptId);
        
        $request->validate([
            'essay_scores' => 'required|array',
            'essay_scores.*' => 'required|numeric|min:0',
        ]);
        
        $essayScores = $request->essay_scores;
        
        // Validate that scores don't exceed max points
        foreach ($attempt->exam->questions as $question) {
            if ($question->question_type === 'essay' && isset($essayScores[$question->id])) {
                if ($essayScores[$question->id] > $question->points) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['essay_scores.' . $question->id => "Skor tidak boleh melebihi {$question->points} poin"]);
                }
            }
        }
        
        // Save essay scores
        $attempt->essay_scores = $essayScores;
        $attempt->save();
        
        // Calculate total score including essay scores
        $this->calculateTotalScore($attempt, $essayScores);
        
        // Auto-complete material/chapter if quiz passed and auto_complete_on_pass is enabled
        if ($attempt->isPassed() && $attempt->exam->auto_complete_on_pass) {
            $quizCompletionService = new \App\Services\QuizCompletionService();
            $quizCompletionService->completeMaterialOnQuizPass($attempt);
        }

        // Create notification for student
        \App\Helpers\NotificationHelper::createQuizGraded($attempt);

        return redirect()->route('instructor.exams.attempts', $attempt->exam_id)
            ->with('success', 'Nilai quiz berhasil disimpan.');
    }

    private function calculateTotalScore(\App\Models\ExamAttempt $attempt, array $essayScores): void
    {
        $exam = $attempt->exam;
        $questions = $exam->questions()->with('answers')->get();
        $userAnswers = $attempt->answers ?? [];
        $totalScore = 0;
        $totalPoints = 0;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            
            if ($question->question_type === 'essay') {
                // Use instructor-given score for essay
                $totalScore += $essayScores[$question->id] ?? 0;
            } else {
                // Auto-grade multiple choice and true/false
                if (isset($userAnswers[$question->id])) {
                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                    if ($correctAnswer && $userAnswers[$question->id] == $correctAnswer->id) {
                        $totalScore += $question->points;
                    }
                }
            }
        }

        $percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;
        $isPassed = $percentage >= $exam->minimum_passing_score;
        $status = $isPassed ? 'passed' : 'failed';

        $attempt->update([
            'score' => $totalScore,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'status' => $status,
        ]);
    }
}
