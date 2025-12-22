<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

Route::get('/courses/{slug}', [App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{courseId}/enroll', [App\Http\Controllers\CourseEnrollmentController::class, 'store'])->middleware('auth')->name('courses.enroll');

// Public Source Code routes
Route::get('/source-codes', [App\Http\Controllers\SourceCodeController::class, 'index'])->name('source-codes.index');
Route::get('/source-codes/{slug}', [App\Http\Controllers\SourceCodeController::class, 'show'])->name('source-codes.show');

// Course Rating Routes (Student)
Route::middleware('auth')->group(function () {
    Route::post('/courses/{courseId}/ratings', [App\Http\Controllers\CourseRatingController::class, 'store'])->name('course-ratings.store');
    Route::put('/course-ratings/{id}', [App\Http\Controllers\CourseRatingController::class, 'update'])->name('course-ratings.update');
    Route::delete('/course-ratings/{id}', [App\Http\Controllers\CourseRatingController::class, 'destroy'])->name('course-ratings.destroy');
});

// Course Content Routes (requires enrollment)
Route::middleware(['auth', 'enrolled'])->group(function () {
    Route::get('/courses/{courseId}/content', [App\Http\Controllers\CourseContentController::class, 'index'])->name('courses.content');
    Route::get('/courses/{courseId}/chapters/{chapterId}', [App\Http\Controllers\CourseContentController::class, 'showChapter'])->name('courses.chapter');
    Route::get('/courses/{courseId}/chapters/{chapterId}/materials/{materialId}', [App\Http\Controllers\CourseContentController::class, 'showMaterial'])->name('courses.material');
});

// Progress API routes (auth required, enrollment verified in controller)
Route::middleware('auth')->group(function () {
    Route::post('/api/progress/complete', [App\Http\Controllers\CourseContentController::class, 'markComplete'])->name('progress.complete');
    Route::post('/api/progress/update', [App\Http\Controllers\CourseContentController::class, 'updateProgress'])->name('progress.update');
});

// Certificate routes
Route::middleware('auth')->group(function () {
    Route::get('/certificates', [App\Http\Controllers\CertificateController::class, 'history'])->name('certificates.history');
    Route::get('/certificates/{courseId}/generate', [App\Http\Controllers\CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/{id}', [App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/{id}/download', [App\Http\Controllers\CertificateController::class, 'download'])->name('certificates.download');
});

// Certificate verification routes (public)
Route::get('/verify-certificate', [App\Http\Controllers\CertificateVerificationController::class, 'form'])->name('certificates.verify.form');
Route::post('/verify-certificate', [App\Http\Controllers\CertificateVerificationController::class, 'verify'])->name('certificates.verify.post');
Route::get('/certificates/verify/{code}', [App\Http\Controllers\CertificateVerificationController::class, 'verify'])->name('certificates.verify');

// Payment routes
Route::middleware(['auth', 'midtrans.csp'])->group(function () {
    Route::get('/payments/checkout/{courseId}', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/process/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/success/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'failed'])->name('payments.failed');
});

// Payment callback (no auth required for Midtrans)
Route::post('/payments/callback', [App\Http\Controllers\PaymentCallbackController::class, 'handle'])->name('payments.callback');

// Notification routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/all', [App\Http\Controllers\NotificationController::class, 'all'])->name('notifications.all');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/exams', [App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{id}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{id}/submit', [App\Http\Controllers\Student\ExamController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/result/{attemptId}', [App\Http\Controllers\Student\ExamController::class, 'result'])->name('exams.result');
    Route::post('/exams/{attemptId}/request-retake', [App\Http\Controllers\Student\ExamController::class, 'requestRetake'])->name('exams.request-retake');
});

// Instructor routes (also accessible by admin)
Route::middleware(['auth', 'role:instructor,admin'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('courses', App\Http\Controllers\Instructor\CourseController::class);
    Route::post('/courses/{id}/publish', [App\Http\Controllers\Instructor\CourseController::class, 'publish'])->name('courses.publish');
    
    // Chapter routes
    Route::post('/courses/{courseId}/chapters', [App\Http\Controllers\Instructor\ChapterController::class, 'store'])->name('chapters.store');
    Route::put('/chapters/{id}', [App\Http\Controllers\Instructor\ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/chapters/{id}', [App\Http\Controllers\Instructor\ChapterController::class, 'destroy'])->name('chapters.destroy');
    Route::post('/chapters/reorder', [App\Http\Controllers\Instructor\ChapterController::class, 'reorder'])->name('chapters.reorder');
    
    // Material routes
    Route::get('/chapters/{chapterId}/materials/create', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'create'])->name('materials.create');
    Route::post('/chapters/{chapterId}/materials', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{id}/edit', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{id}', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{id}', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'destroy'])->name('materials.destroy');
    Route::post('/materials/reorder', [App\Http\Controllers\Instructor\ChapterMaterialController::class, 'reorder'])->name('materials.reorder');
    
    Route::resource('comments', App\Http\Controllers\Instructor\CommentController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('/comments/{id}/reply', [App\Http\Controllers\Instructor\CommentController::class, 'reply'])->name('comments.reply');
    
    // Chapter exam creation (must be before resource route to avoid conflict)
    Route::get('/chapters/{chapterId}/exams/create', [App\Http\Controllers\Instructor\ExamController::class, 'create'])->name('chapters.exams.create');
    
    // Retake requests (must be before resource route to avoid conflict)
    Route::get('/exams/retake-requests', [App\Http\Controllers\Instructor\ExamController::class, 'retakeRequests'])->name('exams.retake-requests');
    Route::post('/exams/attempts/{attemptId}/approve-retake', [App\Http\Controllers\Instructor\ExamController::class, 'approveRetake'])->name('exams.approve-retake');
    Route::post('/exams/attempts/{attemptId}/reject-retake', [App\Http\Controllers\Instructor\ExamController::class, 'rejectRetake'])->name('exams.reject-retake');
    
    Route::resource('exams', App\Http\Controllers\Instructor\ExamController::class)->except(['show']);
    Route::get('/exams/{id}', [App\Http\Controllers\Instructor\ExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{id}/questions', [App\Http\Controllers\Instructor\ExamController::class, 'questions'])->name('exams.questions');
    Route::post('/exams/{id}/questions', [App\Http\Controllers\Instructor\ExamController::class, 'addQuestion'])->name('exams.questions.add');
    Route::delete('/exams/{examId}/questions/{questionId}', [App\Http\Controllers\Instructor\ExamController::class, 'deleteQuestion'])->name('exams.questions.delete');
    Route::get('/exams/{id}/attempts', [App\Http\Controllers\Instructor\ExamController::class, 'attempts'])->name('exams.attempts');
    
    // Student Progress Management
    Route::get('/progress', [App\Http\Controllers\Instructor\StudentProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/{id}', [App\Http\Controllers\Instructor\StudentProgressController::class, 'show'])->name('progress.show');
    Route::post('/progress/{id}/approve', [App\Http\Controllers\Instructor\StudentProgressController::class, 'approve'])->name('progress.approve');
    Route::post('/progress/{id}/reject', [App\Http\Controllers\Instructor\StudentProgressController::class, 'reject'])->name('progress.reject');
    Route::post('/progress/bulk-approve', [App\Http\Controllers\Instructor\StudentProgressController::class, 'bulkApprove'])->name('progress.bulk-approve');
    
    // Course Students Routes
    Route::get('/courses/{courseId}/students', [App\Http\Controllers\Instructor\CourseStudentController::class, 'index'])->name('courses.students');
    Route::get('/courses/{courseId}/students/{studentId}', [App\Http\Controllers\Instructor\CourseStudentController::class, 'show'])->name('courses.students.show');
    
    // Student Rating Routes
    Route::post('/courses/{courseId}/students/{studentId}/ratings', [App\Http\Controllers\Instructor\StudentRatingController::class, 'store'])->name('student-ratings.store');
    Route::put('/student-ratings/{id}', [App\Http\Controllers\Instructor\StudentRatingController::class, 'update'])->name('student-ratings.update');
    Route::delete('/student-ratings/{id}', [App\Http\Controllers\Instructor\StudentRatingController::class, 'destroy'])->name('student-ratings.destroy');
    
    // Source Code Routes
    Route::resource('source-codes', App\Http\Controllers\Instructor\SourceCodeController::class);
    Route::post('/source-codes/{id}/publish', [App\Http\Controllers\Instructor\SourceCodeController::class, 'publish'])->name('source-codes.publish');
});

// Community routes
Route::middleware('auth')->prefix('community')->name('community.')->group(function () {
    Route::get('/', [App\Http\Controllers\Community\PostController::class, 'index'])->name('index');
    Route::post('/posts', [App\Http\Controllers\Community\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}', [App\Http\Controllers\Community\PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{id}', [App\Http\Controllers\Community\PostController::class, 'destroy'])->name('posts.destroy');
    
    Route::post('/posts/{id}/like', [App\Http\Controllers\Community\LikeController::class, 'toggle'])->name('posts.like');
    
    Route::post('/comments', [App\Http\Controllers\Community\CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}', [App\Http\Controllers\Community\CommentController::class, 'destroy'])->name('comments.destroy');
    
    Route::get('/broadcasts', [App\Http\Controllers\Community\BroadcastController::class, 'index'])->name('broadcasts.index');
    Route::get('/broadcasts/create', [App\Http\Controllers\Community\BroadcastController::class, 'create'])->name('broadcasts.create');
    Route::post('/broadcasts', [App\Http\Controllers\Community\BroadcastController::class, 'store'])->name('broadcasts.store');
    Route::get('/broadcasts/{id}', [App\Http\Controllers\Community\BroadcastController::class, 'show'])->name('broadcasts.show');
    Route::post('/broadcasts/{id}/start', [App\Http\Controllers\Community\BroadcastController::class, 'start'])->name('broadcasts.start');
    Route::post('/broadcasts/{id}/end', [App\Http\Controllers\Community\BroadcastController::class, 'end'])->name('broadcasts.end');
    Route::post('/broadcasts/{id}/zoom', [App\Http\Controllers\Community\BroadcastController::class, 'createZoomMeeting'])->name('broadcasts.zoom');
    
    Route::post('/broadcasts/{id}/like', [App\Http\Controllers\Community\BroadcastLikeController::class, 'toggle'])->name('broadcasts.like');
    Route::post('/broadcasts/comments', [App\Http\Controllers\Community\BroadcastCommentController::class, 'store'])->name('broadcasts.comments.store');
    Route::delete('/broadcasts/comments/{id}', [App\Http\Controllers\Community\BroadcastCommentController::class, 'destroy'])->name('broadcasts.comments.destroy');
    
    Route::get('/events', [App\Http\Controllers\Community\EventController::class, 'index'])->name('events.index');
    Route::get('/events/calendar', [App\Http\Controllers\Community\EventController::class, 'calendar'])->name('events.calendar');
    Route::get('/events/create', [App\Http\Controllers\Community\EventController::class, 'create'])->name('events.create');
    Route::post('/events', [App\Http\Controllers\Community\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}', [App\Http\Controllers\Community\EventController::class, 'show'])->name('events.show');
    Route::post('/events/{id}/register', [App\Http\Controllers\Community\EventController::class, 'register'])->name('events.register');
    Route::post('/events/{id}/cancel', [App\Http\Controllers\Community\EventController::class, 'cancel'])->name('events.cancel');
    Route::get('/events/my-events', [App\Http\Controllers\Community\EventController::class, 'myEvents'])->name('events.my-events');
    
    Route::get('/profile/{username}', [App\Http\Controllers\Community\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Community\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Community\ProfileController::class, 'update'])->name('profile.update');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management (all users)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.update-role');
    Route::get('/users/instructors/create', [App\Http\Controllers\Admin\UserController::class, 'createInstructor'])->name('users.create-instructor');
    Route::post('/users/instructors', [App\Http\Controllers\Admin\UserController::class, 'storeInstructor'])->name('users.store-instructor');
    
    // Student Management (legacy - can be kept for backward compatibility)
    Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
    Route::get('/students/{id}/enrollments', [App\Http\Controllers\Admin\StudentController::class, 'enrollments'])->name('students.enrollments');
    Route::post('/students/{id}/suspend', [App\Http\Controllers\Admin\StudentController::class, 'suspend'])->name('students.suspend');
    
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::put('/payments/{id}/status', [App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.update-status');
    Route::get('/invoices', [App\Http\Controllers\Admin\PaymentController::class, 'invoices'])->name('payments.invoices');
    Route::post('/invoices/generate', [App\Http\Controllers\Admin\PaymentController::class, 'generateInvoice'])->name('payments.generate-invoice');
    
    // Course Management (view all courses)
    Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    
    // Source Code Management (view all source codes)
    Route::get('/source-codes', [App\Http\Controllers\Admin\SourceCodeController::class, 'index'])->name('source-codes.index');
});

Route::middleware('auth')->group(function () {
    // General dashboard route that redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match($user->role) {
            'instructor' => redirect()->route('instructor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/enrollments', [ProfileController::class, 'enrollments'])->name('profile.enrollments');
    Route::get('/profile/certificates', [ProfileController::class, 'certificates'])->name('profile.certificates');
});

require __DIR__.'/auth.php';
