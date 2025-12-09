# Plan 21: Quiz and Exam System

## Overview
Complete quiz and exam system allowing instructors to create quizzes/exams and students to take them. This expands on Plan 15's multilingual support for quiz content.

## Requirements
- Instructor: Create quizzes/exams with questions
- Instructor: Set time limits, passing scores
- Student: Take quizzes/exams
- Automatic grading for multiple choice
- Manual grading for essay questions
- Quiz results and feedback
- Japanese text support in questions and answers

## Database Changes

### Create Quizzes Table (from Plan 15, enhanced)
File: `database/migrations/xxxx_create_quizzes_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `chapter_id` (foreign key, nullable)
- `title` (string) - Support Japanese
- `description` (text) - Support Japanese
- `instructions` (text) - Support Japanese
- `quiz_type` (enum: 'quiz', 'exam', 'assignment')
- `duration_minutes` (integer, nullable)
- `passing_score` (integer, default: 70) - percentage
- `max_attempts` (integer, nullable) - null = unlimited
- `is_published` (boolean, default: false)
- `start_date` (timestamp, nullable)
- `end_date` (timestamp, nullable)
- `show_results` (boolean, default: true)
- `shuffle_questions` (boolean, default: false)
- `shuffle_options` (boolean, default: false)
- `created_at`, `updated_at`

### Create Quiz Questions Table (from Plan 15, enhanced)
File: `database/migrations/xxxx_create_quiz_questions_table.php`

Fields:
- `id` (bigInteger)
- `quiz_id` (foreign key)
- `question_text` (text) - Support Japanese
- `question_type` (enum: 'multiple_choice', 'true_false', 'short_answer', 'essay', 'fill_blank')
- `points` (integer, default: 1)
- `order` (integer)
- `explanation` (text, nullable) - Support Japanese - shown after answer
- `created_at`, `updated_at`

### Create Quiz Options Table (from Plan 15)
File: `database/migrations/xxxx_create_quiz_options_table.php`

Fields:
- `id` (bigInteger)
- `question_id` (foreign key)
- `option_text` (text) - Support Japanese
- `is_correct` (boolean)
- `order` (integer)
- `created_at`, `updated_at`

### Create Quiz Attempts Table
File: `database/migrations/xxxx_create_quiz_attempts_table.php`

Fields:
- `id` (bigInteger)
- `quiz_id` (foreign key)
- `user_id` (foreign key)
- `course_enrollment_id` (foreign key)
- `started_at` (timestamp)
- `submitted_at` (timestamp, nullable)
- `time_taken_minutes` (integer, nullable)
- `score` (decimal, nullable) - percentage
- `points_earned` (decimal, nullable)
- `total_points` (decimal, nullable)
- `status` (enum: 'in_progress', 'submitted', 'graded', 'passed', 'failed')
- `is_graded` (boolean, default: false)
- `graded_at` (timestamp, nullable)
- `graded_by` (foreign key to users, nullable)
- `feedback` (text, nullable)
- `metadata` (json, nullable) - store additional data
- `created_at`, `updated_at`

### Create Quiz Answers Table
File: `database/migrations/xxxx_create_quiz_answers_table.php`

Fields:
- `id` (bigInteger)
- `attempt_id` (foreign key)
- `question_id` (foreign key)
- `answer_text` (text, nullable) - for text answers
- `selected_options` (json, nullable) - array of option IDs for multiple choice
- `is_correct` (boolean, nullable) - auto-graded
- `points_earned` (decimal, nullable)
- `max_points` (decimal)
- `feedback` (text, nullable) - instructor feedback
- `created_at`, `updated_at`

## Models to Create

### Quiz Model
File: `app/Models/Quiz.php`
- Relationships: belongsTo(Course, Chapter), hasMany(QuizQuestion, QuizAttempt)
- Scopes: published(), active(), expired()
- Methods: isAvailable(), canAttempt(User $user), calculateScore()

### QuizQuestion Model
File: `app/Models/QuizQuestion.php`
- Relationships: belongsTo(Quiz), hasMany(QuizOption, QuizAnswer)
- Scopes: ordered()
- Methods: isCorrect(Answer $answer), autoGrade()

### QuizOption Model
File: `app/Models/QuizOption.php`
- Relationships: belongsTo(QuizQuestion)

### QuizAttempt Model
File: `app/Models/QuizAttempt.php`
- Relationships: belongsTo(Quiz, User, CourseEnrollment), hasMany(QuizAnswer)
- Scopes: inProgress(), submitted(), graded(), passed(), failed()
- Methods: submit(), grade(), isPassed(), getRemainingTime()

### QuizAnswer Model
File: `app/Models/QuizAnswer.php`
- Relationships: belongsTo(QuizAttempt, QuizQuestion)
- Methods: isCorrect(), grade()

## Implementation Steps

### 1. Create Quiz Controller (Instructor)
File: `app/Http/Controllers/Instructor/QuizController.php`
- `index()` method: List all quizzes for instructor's courses
- `create()` method: Show quiz creation form
- `store(Request $request)` method: Create quiz
- `show($id)` method: Show quiz details
- `edit($id)` method: Edit quiz
- `update(Request $request, $id)` method: Update quiz
- `destroy($id)` method: Delete quiz
- `publish($id)` method: Publish/unpublish quiz

### 2. Create Quiz Question Controller (Instructor)
File: `app/Http/Controllers/Instructor/QuizQuestionController.php`
- `store(Request $request)` method: Add question to quiz
- `update(Request $request, $id)` method: Update question
- `destroy($id)` method: Delete question
- `reorder(Request $request)` method: Reorder questions

### 3. Create Quiz Attempt Controller (Student)
File: `app/Http/Controllers/Student/QuizAttemptController.php`
- `start($quizId)` method: Start quiz attempt
- `show($attemptId)` method: Show quiz taking interface
- `saveAnswer(Request $request)` method: Save answer (AJAX)
- `submit(Request $request, $attemptId)` method: Submit quiz
- `results($attemptId)` method: Show quiz results

### 4. Create Quiz Grading Controller (Instructor)
File: `app/Http/Controllers/Instructor/QuizGradingController.php`
- `index()` method: List quizzes needing grading
- `show($attemptId)` method: Show attempt for grading
- `grade(Request $request, $attemptId)` method: Grade quiz attempt
- `gradeAnswer(Request $request, $answerId)` method: Grade individual answer

### 5. Create Quiz Service
File: `app/Services/QuizService.php`
- `createQuiz(array $data)` method: Create quiz with questions
- `startAttempt(User $user, Quiz $quiz)` method: Start quiz attempt
- `saveAnswer(QuizAttempt $attempt, QuizQuestion $question, $answer)` method: Save answer
- `submitAttempt(QuizAttempt $attempt)` method: Submit and auto-grade
- `gradeAttempt(QuizAttempt $attempt, array $grades)` method: Manual grading
- `autoGrade(QuizAttempt $attempt)` method: Auto-grade multiple choice/true-false
- `calculateScore(QuizAttempt $attempt)` method: Calculate final score

### 6. Create Quiz Creation View (Instructor)
File: `resources/views/instructor/quizzes/create.blade.php`
- Form fields:
  - Quiz title (Japanese support)
  - Description (Japanese support)
  - Instructions (Japanese support)
  - Quiz type
  - Duration (minutes)
  - Passing score (%)
  - Max attempts
  - Start/end dates
  - Settings (shuffle, show results)
- Questions section:
  - Add question button
  - Question form (type, text, points)
  - Options (for multiple choice)
  - Correct answer selection
  - Explanation field
- Save/Publish buttons

### 7. Create Quiz Taking View (Student)
File: `resources/views/student/quizzes/take.blade.php`
- Timer display (if time limit)
- Progress indicator
- Question display:
  - Question number
  - Question text (Japanese support)
  - Options (for multiple choice)
  - Answer input (for text/essay)
- Navigation: Previous/Next buttons
- Save answer button
- Submit quiz button
- Warning before submission

### 8. Create Quiz Results View (Student)
File: `resources/views/student/quizzes/results.blade.php`
- Overall score display
- Pass/Fail status
- Points earned/total
- Question review:
  - Question text
  - Student answer
  - Correct answer
  - Points earned
  - Feedback (if available)
- Retake button (if attempts remaining)

### 9. Create Quiz Grading View (Instructor)
File: `resources/views/instructor/quizzes/grade.blade.php`
- Student information
- Quiz information
- Answer review:
  - Question text
  - Student answer
  - Correct answer (if applicable)
  - Points input
  - Feedback textarea
- Grade all button
- Overall feedback

### 10. Create Quiz List View (Student)
File: `resources/views/student/quizzes/index.blade.php`
- List of quizzes/exams for enrolled courses
- Quiz status: Not started, In progress, Completed
- Best score display
- Attempts remaining
- Start/Take button

### 11. Create Quiz Attempt Timer Component
File: `resources/js/quiz-timer.js`
- Countdown timer
- Auto-submit when time expires
- Warning notifications (5 min, 1 min remaining)

### 12. Create Quiz Answer Storage Component
File: `resources/js/quiz-answers.js`
- Auto-save answers periodically
- Save on navigation
- Prevent data loss

### 13. Update Course Content View
File: `resources/views/courses/chapter.blade.php` (from Plan 09)
- Show quiz/exam link if available
- Show quiz status

### 14. Create Quiz Validation Rules
File: `app/Http/Requests/CreateQuizRequest.php`
- Validate quiz data
- Validate questions and options

## Files to Create/Modify
- `database/migrations/xxxx_create_quizzes_table.php` (new - enhanced from Plan 15)
- `database/migrations/xxxx_create_quiz_questions_table.php` (new - enhanced from Plan 15)
- `database/migrations/xxxx_create_quiz_options_table.php` (new - from Plan 15)
- `database/migrations/xxxx_create_quiz_attempts_table.php` (new)
- `database/migrations/xxxx_create_quiz_answers_table.php` (new)
- `app/Models/Quiz.php` (new)
- `app/Models/QuizQuestion.php` (new)
- `app/Models/QuizOption.php` (new)
- `app/Models/QuizAttempt.php` (new)
- `app/Models/QuizAnswer.php` (new)
- `app/Http/Controllers/Instructor/QuizController.php` (new)
- `app/Http/Controllers/Instructor/QuizQuestionController.php` (new)
- `app/Http/Controllers/Instructor/QuizGradingController.php` (new)
- `app/Http/Controllers/Student/QuizAttemptController.php` (new)
- `app/Services/QuizService.php` (new)
- `app/Http/Requests/CreateQuizRequest.php` (new)
- `resources/views/instructor/quizzes/create.blade.php` (new - enhanced from Plan 15)
- `resources/views/instructor/quizzes/edit.blade.php` (new)
- `resources/views/instructor/quizzes/index.blade.php` (new)
- `resources/views/instructor/quizzes/grade.blade.php` (new)
- `resources/views/student/quizzes/index.blade.php` (new)
- `resources/views/student/quizzes/take.blade.php` (new)
- `resources/views/student/quizzes/results.blade.php` (new)
- `resources/js/quiz-timer.js` (new)
- `resources/js/quiz-answers.js` (new)
- `routes/web.php` (modify)
- `resources/views/courses/chapter.blade.php` (modify - from Plan 09)

## Dependencies
- Plan 15: Multilingual support for Japanese text
- Plan 09: Course enrollment and progress tracking
- JavaScript for timer and auto-save
- Rich text editor for question formatting (optional)

## Quiz Types
- `quiz`: Practice quiz (can retake)
- `exam`: Final exam (limited attempts)
- `assignment`: Graded assignment (manual grading)

## Question Types
- `multiple_choice`: Single or multiple correct answers
- `true_false`: True/False question
- `short_answer`: Short text answer (auto-graded if exact match)
- `essay`: Long form answer (manual grading)
- `fill_blank`: Fill in the blank (auto-graded)

## Auto-Grading Logic
```php
// Multiple choice
if ($question->type === 'multiple_choice') {
    $selectedOptions = $answer->selected_options;
    $correctOptions = $question->options->where('is_correct', true)->pluck('id');
    
    if ($selectedOptions === $correctOptions->toArray()) {
        $answer->is_correct = true;
        $answer->points_earned = $question->points;
    } else {
        $answer->is_correct = false;
        $answer->points_earned = 0;
    }
}
```

## Testing Considerations
- Test quiz creation
- Test question creation with Japanese text
- Test quiz taking interface
- Test timer functionality
- Test auto-save functionality
- Test auto-grading
- Test manual grading
- Test quiz submission
- Test results display
- Test quiz retake (if allowed)
- Test time limit enforcement
- Test attempt limit enforcement
- Test access control (only enrolled students)

## Integration with Other Plans
- Plan 09: Link quizzes to chapters
- Plan 15: Support Japanese text in questions/answers
- Plan 20: Require quiz completion for certificate (optional)
- Plan 04: Track quiz attempts in dashboard

## Security Considerations
- Prevent quiz manipulation during taking
- Secure answer submission
- Prevent time manipulation
- Rate limit quiz attempts
- Validate quiz access (enrolled students only)

