<!-- 614387e1-7aa1-4fd2-8adb-befe702d3fbe e613fabc-0738-4df8-b21d-465776a8d3ce -->
# Plan 24: Revision Features Implementation

## Overview

Implementasi fitur-fitur revisi berdasarkan requirement user: integrasi Zoom untuk live broadcast, sistem completion berbasis quiz, kontrol completion oleh instruktur, menu quiz per chapter, minimum passing score, dan kontrol progresi materi.

## Requirements Analysis

### 1. Zoom Integration untuk Live Broadcast

- Live session di komunitas pada fitur broadcast bisa dikoneksikan ke Zoom
- User dapat membuat live session dengan link Zoom

### 2. Quiz-based Lesson Completion

- Pelajaran dianggap selesai apabila siswa berhasil lulus test/quiz dengan memenuhi nilai minimal yang ditetapkan instruktur
- Completion otomatis berdasarkan hasil quiz, bukan manual oleh siswa

### 3. Instructor-controlled Module Completion

- Instruktur yang menentukan selesai atau tidaknya modul
- Siswa tidak boleh menandai selesai secara manual
- Instruktur dapat approve/reject completion

### 4. Quiz Menu per Chapter

- Dalam masing-masing bab harus ada menu tambah quiz
- Instruktur dapat menambahkan quiz dari halaman chapter management

### 5. Minimum Passing Score

- Instruktur menandai berapa minimal nilai harus lolos pada tes agar bisa lolos quiz per materi atau per bab
- Setiap quiz dapat memiliki minimum passing score yang berbeda

### 6. Optional Quiz dengan Progression Control

- Modul atau bab tidak wajib ada quiznya
- Instruktur dapat menentukan apakah quiz wajib untuk lanjut ke materi/bab selanjutnya
- Sistem akan memblokir akses ke materi berikutnya jika quiz wajib belum lulus

## Database Changes

### 1. Update Broadcasts Table

File: `database/migrations/xxxx_add_zoom_fields_to_broadcasts_table.php`

Add fields:

- `zoom_meeting_id` (string, nullable) - Zoom meeting ID
- `zoom_meeting_password` (string, nullable) - Zoom meeting password
- `zoom_join_url` (string, nullable) - Zoom join URL
- `zoom_start_url` (string, nullable) - Zoom start URL (for host)
- `is_zoom_meeting` (boolean, default: false) - Flag untuk Zoom meeting

### 2. Update Exams Table

File: `database/migrations/xxxx_add_quiz_completion_fields_to_exams_table.php`

Add fields:

- `minimum_passing_score` (integer, default: 70) - Minimum score untuk lulus (percentage)
- `is_required_for_progression` (boolean, default: false) - Wajib lulus untuk lanjut ke materi/bab berikutnya
- `chapter_material_id` (foreign key, nullable) - Link ke material spesifik (optional)
- `auto_complete_on_pass` (boolean, default: true) - Auto-complete material/chapter saat lulus

### 3. Update Chapters Table

File: `database/migrations/xxxx_add_quiz_fields_to_chapters_table.php`

Add fields:

- `has_quiz` (boolean, default: false) - Flag apakah chapter punya quiz
- `quiz_required_for_next` (boolean, default: false) - Quiz wajib untuk lanjut ke chapter berikutnya

### 4. Update Chapter Materials Table

File: `database/migrations/xxxx_add_quiz_fields_to_chapter_materials_table.php`

Add fields:

- `has_quiz` (boolean, default: false) - Flag apakah material punya quiz
- `quiz_required_for_next` (boolean, default: false) - Quiz wajib untuk lanjut ke material berikutnya

### 5. Update Student Progress Table

File: `database/migrations/xxxx_add_instructor_approval_to_student_progress_table.php`

Add fields:

- `is_instructor_approved` (boolean, default: false) - Approval dari instruktur
- `approved_at` (timestamp, nullable) - Waktu approval
- `approved_by` (foreign key to users, nullable) - User yang approve (instructor)
- `completion_method` (enum: 'manual', 'quiz_passed', 'instructor_approved', default: 'manual')
- `quiz_attempt_id` (foreign key to exam_attempts, nullable) - Link ke quiz attempt yang membuat complete

### 6. Create Chapter Quiz Junction Table (Optional)

File: `database/migrations/xxxx_create_chapter_exams_table.php`

Fields:

- `id` (bigInteger)
- `chapter_id` (foreign key)
- `exam_id` (foreign key)
- `is_required` (boolean, default: false)
- `order` (integer)
- `created_at`, `updated_at`

Note: Jika menggunakan relasi langsung exam->chapter_id, table ini mungkin tidak diperlukan.

## Models to Update/Create

### 1. Update Broadcast Model

File: `app/Models/Broadcast.php`

- Add fillable fields: `zoom_meeting_id`, `zoom_meeting_password`, `zoom_join_url`, `zoom_start_url`, `is_zoom_meeting`
- Add casts: `is_zoom_meeting` => 'boolean'
- Add methods: `createZoomMeeting()`, `getZoomJoinUrl()`, `getZoomStartUrl()`

### 2. Update Exam Model

File: `app/Models/Exam.php`

- Add fillable fields: `minimum_passing_score`, `is_required_for_progression`, `chapter_material_id`, `auto_complete_on_pass`
- Add casts: `is_required_for_progression` => 'boolean', `auto_complete_on_pass` => 'boolean'
- Add relationship: `belongsTo(ChapterMaterial)` if chapter_material_id exists
- Add methods: `isPassed(ExamAttempt $attempt)`, `canProceed(User $user, CourseEnrollment $enrollment)`

### 3. Update Chapter Model

File: `app/Models/Chapter.php`

- Add fillable fields: `has_quiz`, `quiz_required_for_next`
- Add casts: `has_quiz` => 'boolean', `quiz_required_for_next` => 'boolean'
- Add relationship: `hasMany(Exam)` - quizzes for this chapter
- Add methods: `hasRequiredQuiz()`, `canStudentProceed(User $user, CourseEnrollment $enrollment)`

### 4. Update ChapterMaterial Model

File: `app/Models/ChapterMaterial.php`

- Add fillable fields: `has_quiz`, `quiz_required_for_next`
- Add casts: `has_quiz` => 'boolean', `quiz_required_for_next` => 'boolean`
- Add relationship: `hasOne(Exam)` - quiz for this material
- Add methods: `hasRequiredQuiz()`, `canStudentProceed(User $user, CourseEnrollment $enrollment)`

### 5. Update StudentProgress Model

File: `app/Models/StudentProgress.php`

- Add fillable fields: `is_instructor_approved`, `approved_at`, `approved_by`, `completion_method`, `quiz_attempt_id`
- Add casts: `is_instructor_approved` => 'boolean', `approved_at` => 'datetime'
- Add relationships: `belongsTo(User, as: 'approvedBy')`, `belongsTo(ExamAttempt)`
- Add methods: `approveBy(User $instructor)`, `isCompleted()`, `canMarkComplete()`

### 6. Create Zoom Service

File: `app/Services/ZoomService.php`

- Methods: `createMeeting()`, `updateMeeting()`, `deleteMeeting()`, `getMeeting()`
- Handle Zoom API integration
- Store credentials in config/env

## Implementation Steps

### Phase 1: Zoom Integration untuk Live Broadcast

#### 1.1 Install Zoom SDK/Package

- Install Zoom API package atau use HTTP client untuk Zoom API
- Add Zoom credentials to `.env`: `ZOOM_API_KEY`, `ZOOM_API_SECRET`, `ZOOM_ACCOUNT_ID`

#### 1.2 Create Zoom Service

File: `app/Services/ZoomService.php`

- Implement Zoom API integration
- Methods untuk create, update, delete meetings

#### 1.3 Update Broadcast Controller

File: `app/Http/Controllers/Community/BroadcastController.php`

- Update `store()` method: Add option to create Zoom meeting
- Add `createZoomMeeting($broadcastId)` method
- Update `start()` method: Generate Zoom start URL if Zoom meeting

#### 1.4 Update Broadcast Create View

File: `resources/views/community/broadcasts/create.blade.php`

- Add checkbox: "Gunakan Zoom Meeting"
- Show Zoom meeting fields when checked
- Display Zoom join URL after creation

#### 1.5 Update Broadcast Show View

File: `resources/views/community/broadcasts/show.blade.php`

- Show Zoom join button if `is_zoom_meeting` is true
- Display Zoom meeting info (ID, password if needed)
- Show "Start Meeting" button for host

### Phase 2: Quiz-based Completion System

#### 2.1 Update Exam Migration

- Add `minimum_passing_score`, `is_required_for_progression`, `chapter_material_id`, `auto_complete_on_pass` fields

#### 2.2 Update Exam Model

- Add new fields and relationships
- Implement `isPassed()` method: Check if attempt score >= minimum_passing_score

#### 2.3 Update ExamAttempt Model

File: `app/Models/ExamAttempt.php`

- Add method: `isPassed(Exam $exam)` - Check if score >= exam->minimum_passing_score
- Update `grade()` method: Set status to 'passed' or 'failed' based on minimum_passing_score

#### 2.4 Create Quiz Completion Service

File: `app/Services/QuizCompletionService.php`

- Method: `completeMaterialOnQuizPass(ExamAttempt $attempt)`
- Auto-complete material/chapter when quiz passed
- Update StudentProgress with completion_method = 'quiz_passed'

#### 2.5 Update Student Exam Controller

File: `app/Http/Controllers/Student/ExamController.php`

- Update `submit()` method: Call QuizCompletionService after grading
- Auto-complete material if quiz passed and `auto_complete_on_pass` is true

### Phase 3: Instructor-controlled Completion

#### 3.1 Remove Student Mark Complete Functionality

File: `app/Http/Controllers/CourseContentController.php`

- Remove or disable `markComplete()` method for students
- Add middleware to check user role
- Only allow instructors to mark complete

#### 3.2 Create Instructor Progress Controller

File: `app/Http/Controllers/Instructor/StudentProgressController.php`

- `index()` - List all student progress for instructor's courses
- `show($progressId)` - View student progress details
- `approve($progressId)` - Approve student completion
- `reject($progressId)` - Reject and reset completion
- `bulkApprove()` - Bulk approve multiple progress

#### 3.3 Update Student Progress View

File: `resources/views/courses/material.blade.php`

- Remove "Mark as Complete" button for students
- Show status: "Pending Instructor Approval" if completed but not approved
- Show "Approved" status if instructor approved

#### 3.4 Create Instructor Progress Management View

File: `resources/views/instructor/progress/index.blade.php`

- List all pending approvals
- Filter by course, chapter, student
- Approve/Reject buttons
- Show quiz results if completion from quiz

### Phase 4: Quiz Menu per Chapter

#### 4.1 Update Chapter Management View

File: `resources/views/instructor/chapters/edit.blade.php` or create new view

- Add "Tambah Quiz" button/menu item
- Show list of existing quizzes for chapter
- Link to create/edit quiz

#### 4.2 Update Exam Controller (Instructor)

File: `app/Http/Controllers/Instructor/ExamController.php`

- Update `create()` method: Accept `chapter_id` parameter
- Pre-fill chapter_id when creating from chapter page
- Add route: `GET /instructor/chapters/{chapterId}/exams/create`

#### 4.3 Create Chapter Quiz Management View

File: `resources/views/instructor/chapters/quizzes.blade.php`

- List all quizzes for chapter
- Add quiz button
- Edit/Delete quiz options
- Set minimum passing score
- Set required for progression toggle

### Phase 5: Minimum Passing Score

#### 5.1 Update Exam Create/Edit Form

File: `resources/views/instructor/exams/create.blade.php`

File: `resources/views/instructor/exams/edit.blade.php`

- Add field: "Minimum Passing Score" (percentage, default: 70)
- Add field: "Wajib Lulus untuk Lanjut" (checkbox)
- Add field: "Auto-complete saat Lulus" (checkbox)

#### 5.2 Update Exam Controller

- Validate minimum_passing_score (0-100)
- Save to database

#### 5.3 Update Quiz Results View

File: `resources/views/student/exams/results.blade.php`

- Show minimum passing score
- Highlight if passed or failed
- Show message if can proceed to next material

### Phase 6: Progression Control

#### 6.1 Create Progression Service

File: `app/Services/ProgressionService.php`

- Method: `canAccessMaterial(User $user, ChapterMaterial $material, CourseEnrollment $enrollment)`
- Method: `canAccessChapter(User $user, Chapter $chapter, CourseEnrollment $enrollment)`
- Check if required quiz is passed
- Return boolean and reason if blocked

#### 6.2 Update Course Content Controller

File: `app/Http/Controllers/CourseContentController.php`

- Update `showMaterial()` method: Check ProgressionService before allowing access
- Show message if access blocked: "Anda harus lulus quiz [quiz name] terlebih dahulu"
- Show link to quiz if blocked

#### 6.3 Update Chapter View

File: `resources/views/courses/chapter.blade.php`

- Check ProgressionService for each material
- Disable/gray out materials that require quiz
- Show "Locked" icon for blocked materials
- Show quiz status and link

#### 6.4 Update Course Content Index

File: `resources/views/courses/content.blade.php`

- Show progression status for each chapter
- Indicate which chapters are locked
- Show quiz requirements

## Files to Create/Modify

### Migrations

- `database/migrations/xxxx_add_zoom_fields_to_broadcasts_table.php` (new)
- `database/migrations/xxxx_add_quiz_completion_fields_to_exams_table.php` (new)
- `database/migrations/xxxx_add_quiz_fields_to_chapters_table.php` (new)
- `database/migrations/xxxx_add_quiz_fields_to_chapter_materials_table.php` (new)
- `database/migrations/xxxx_add_instructor_approval_to_student_progress_table.php` (new)

### Models

- `app/Models/Broadcast.php` (modify)
- `app/Models/Exam.php` (modify)
- `app/Models/Chapter.php` (modify)
- `app/Models/ChapterMaterial.php` (modify)
- `app/Models/StudentProgress.php` (modify)
- `app/Models/ExamAttempt.php` (modify)

### Services

- `app/Services/ZoomService.php` (new)
- `app/Services/QuizCompletionService.php` (new)
- `app/Services/ProgressionService.php` (new)

### Controllers

- `app/Http/Controllers/Community/BroadcastController.php` (modify)
- `app/Http/Controllers/Instructor/ExamController.php` (modify)
- `app/Http/Controllers/Student/ExamController.php` (modify)
- `app/Http/Controllers/CourseContentController.php` (modify)
- `app/Http/Controllers/Instructor/StudentProgressController.php` (new)

### Views

- `resources/views/community/broadcasts/create.blade.php` (modify)
- `resources/views/community/broadcasts/show.blade.php` (modify)
- `resources/views/instructor/exams/create.blade.php` (modify)
- `resources/views/instructor/exams/edit.blade.php` (modify)
- `resources/views/instructor/chapters/quizzes.blade.php` (new)
- `resources/views/instructor/progress/index.blade.php` (new)
- `resources/views/student/exams/results.blade.php` (modify)
- `resources/views/courses/material.blade.php` (modify)
- `resources/views/courses/chapter.blade.php` (modify)
- `resources/views/courses/content.blade.php` (modify)

### Routes

- `routes/web.php` (modify - add new routes)

## Dependencies

- Plan 11: Community Broadcast (existing)
- Plan 21: Quiz and Exam System (existing Exam model)
- Plan 09: Student Course Materials (existing progress system)
- Zoom API credentials
- Existing Exam/Question/Answer models

## Configuration

Add to `.env`:

```
ZOOM_API_KEY=your_zoom_api_key
ZOOM_API_SECRET=your_zoom_api_secret
ZOOM_ACCOUNT_ID=your_zoom_account_id
```

## Testing Considerations

1. Test Zoom meeting creation and joining
2. Test quiz-based auto-completion
3. Test instructor approval workflow
4. Test progression blocking when quiz not passed
5. Test minimum passing score validation
6. Test optional vs required quiz scenarios

## Notes

- Existing Exam model will be used (not creating new Quiz model)
- Student can no longer manually mark complete
- All completions must be approved by instructor or via quiz passing
- Progression control is optional per quiz (instructor decides)