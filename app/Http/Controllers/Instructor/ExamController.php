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
        $courses = \App\Models\Course::where('instructor_id', Auth::id())->get();
        
        $query = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
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
        $courses = \App\Models\Course::where('instructor_id', Auth::id())->get();
        
        // Get chapter_id from route parameter or request
        $chapterId = $chapterId ?? $request->get('chapter_id');
        $chapter = null;
        
        if ($chapterId) {
            $chapter = \App\Models\Chapter::whereHas('course', function($q) {
                $q->where('instructor_id', Auth::id());
            })->with('course')->findOrFail($chapterId);
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

        // Verify course ownership
        $course = \App\Models\Course::where('id', $validated['course_id'])
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

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

    public function show($id)
    {
        $exam = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->with(['questions.answers', 'attempts.user'])
            ->findOrFail($id);

        return view('instructor.exams.show', compact('exam'));
    }

    public function questions($id)
    {
        $exam = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->with(['questions.answers'])
            ->findOrFail($id);

        return view('instructor.exams.questions', compact('exam'));
    }

    public function addQuestion(Request $request, $examId)
    {
        $exam = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($examId);

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
        $exam = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($examId);

        $question = Question::where('exam_id', $exam->id)->findOrFail($questionId);
        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus');
    }

    public function attempts($id)
    {
        $exam = Exam::whereHas('course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($id);

        $attempts = \App\Models\ExamAttempt::where('exam_id', $exam->id)
            ->with(['user'])
            ->latest()
            ->get();

        return view('instructor.exams.attempts', compact('exam', 'attempts'));
    }

    public function retakeRequests()
    {
        $instructorId = Auth::id();
        
        $retakeRequests = \App\Models\ExamAttempt::with([
            'exam.course',
            'user',
            'retakeApprovedBy'
        ])
        ->whereHas('exam.course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })
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
        $attempt = \App\Models\ExamAttempt::whereHas('exam.course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($attemptId);

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

        $attempt = \App\Models\ExamAttempt::whereHas('exam.course', function($q) {
            $q->where('instructor_id', Auth::id());
        })->findOrFail($attemptId);

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
}
