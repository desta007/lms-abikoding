# Plan 04: Instructor Dashboard

## Overview
Create dashboard for instructors showing statistics about their courses, students, and activities.

## Requirements
- Display statistics:
  - Total enrolled students
  - Total lessons created
  - Total visits/views
  - Active users
  - Active tests/exams

## Database Changes

### Create Course Enrollments Table
File: `database/migrations/xxxx_create_course_enrollments_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `user_id` (foreign key to students)
- `enrolled_at` (timestamp)
- `completed_at` (timestamp, nullable)
- `progress_percentage` (integer, default: 0)
- `created_at`, `updated_at`
- Unique constraint: course_id + user_id

### Create Course Views Table
File: `database/migrations/xxxx_create_course_views_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `user_id` (foreign key, nullable - for anonymous views)
- `ip_address` (string)
- `viewed_at` (timestamp)
- `created_at`, `updated_at`

### Create Exams/Quizzes Table
File: `database/migrations/xxxx_create_exams_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `chapter_id` (foreign key, nullable)
- `title` (string)
- `description` (text)
- `is_active` (boolean, default: true)
- `start_date` (timestamp, nullable)
- `end_date` (timestamp, nullable)
- `duration_minutes` (integer, nullable)
- `created_at`, `updated_at`

### Create User Activity Log Table (Optional)
File: `database/migrations/xxxx_create_user_activity_logs_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key)
- `course_id` (foreign key, nullable)
- `activity_type` (string) - 'login', 'course_view', 'lesson_complete', etc.
- `description` (text)
- `created_at`, `updated_at`

## Models to Create/Modify

### CourseEnrollment Model
File: `app/Models/CourseEnrollment.php`
- Relationships: belongsTo(Course, User)

### CourseView Model
File: `app/Models/CourseView.php`
- Relationships: belongsTo(Course, User)

### Exam Model
File: `app/Models/Exam.php`
- Relationships: belongsTo(Course, Chapter)
- Relationships: hasMany(ExamAttempt)

### UserActivityLog Model (Optional)
File: `app/Models/UserActivityLog.php`
- Relationships: belongsTo(User, Course)

### Update Course Model
File: `app/Models/Course.php`
- Relationships: hasMany(CourseEnrollment, CourseView, Exam)
- Methods: totalEnrollments(), totalViews(), activeUsersCount()

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_course_enrollments_table
php artisan make:migration create_course_views_table
php artisan make:migration create_exams_table
php artisan make:migration create_user_activity_logs_table
```

### 2. Create Models
```bash
php artisan make:model CourseEnrollment
php artisan make:model CourseView
php artisan make:model Exam
php artisan make:model UserActivityLog
```

### 3. Create Instructor Dashboard Controller
File: `app/Http/Controllers/Instructor/DashboardController.php`
- `index()` method:
  - Get instructor's courses
  - Calculate statistics:
    - Total enrolled students (count unique users across all courses)
    - Total lessons/chapters created
    - Total course views
    - Active users (users who accessed course in last 7/30 days)
    - Active exams (exams with is_active = true and within date range)
  - Return view with statistics

### 4. Create Routes
File: `routes/web.php`
- `GET /instructor/dashboard` → Instructor\DashboardController@index
- Add middleware: auth, role:instructor

### 5. Create Dashboard View
File: `resources/views/instructor/dashboard.blade.php`
- Layout with sidebar navigation
- Statistics cards:
  - Card 1: Total Enrolled Students (with icon)
  - Card 2: Total Lessons Created
  - Card 3: Total Visits
  - Card 4: Active Users
  - Card 5: Active Exams
- Chart section (optional): Line chart for enrollments over time
- Recent activity section
- Quick actions section

### 6. Create Statistics Card Component
File: `resources/views/components/instructor/stat-card.blade.php`
- Reusable card component for statistics

### 7. Implement Statistics Calculation
- Use Eloquent queries with aggregates
- Use joins for complex calculations
- Cache statistics if needed (for performance)

### 8. Create Service Class (Optional)
File: `app/Services/InstructorStatisticsService.php`
- Extract statistics calculation logic
- Make it reusable and testable

## Files to Create/Modify
- `database/migrations/xxxx_create_course_enrollments_table.php` (new)
- `database/migrations/xxxx_create_course_views_table.php` (new)
- `database/migrations/xxxx_create_exams_table.php` (new)
- `database/migrations/xxxx_create_user_activity_logs_table.php` (new, optional)
- `app/Models/CourseEnrollment.php` (new)
- `app/Models/CourseView.php` (new)
- `app/Models/Exam.php` (new)
- `app/Models/UserActivityLog.php` (new, optional)
- `app/Models/Course.php` (modify)
- `app/Http/Controllers/Instructor/DashboardController.php` (new)
- `resources/views/instructor/dashboard.blade.php` (new)
- `resources/views/components/instructor/stat-card.blade.php` (new)
- `resources/views/layouts/instructor.blade.php` (new)
- `app/Services/InstructorStatisticsService.php` (new, optional)
- `routes/web.php` (modify)

## Dependencies
- Laravel Query Builder
- Optional: Chart.js or similar for charts
- Optional: Cache facade for performance
- **Plan 03**: Courses table must exist
- **Plan 05**: Chapter model required for "Total Lessons Created" statistic (can be deferred until Plan 05 is implemented)
- **Plan 21**: Quiz/Exam model can be used instead of Exam model if Plan 21 is implemented first

## Statistics Calculation Examples

### Total Enrolled Students
```php
CourseEnrollment::whereHas('course', function($q) use ($instructorId) {
    $q->where('instructor_id', $instructorId);
})->distinct('user_id')->count('user_id');
```

### Total Lessons Created
```php
// Note: Chapter model will be created in Plan 05
Chapter::whereHas('course', function($q) use ($instructorId) {
    $q->where('instructor_id', $instructorId);
})->count();
```

### Active Users (last 30 days)
```php
CourseEnrollment::whereHas('course', function($q) use ($instructorId) {
    $q->where('instructor_id', $instructorId);
})->where('updated_at', '>=', now()->subDays(30))
->distinct('user_id')->count('user_id');
```

## Testing Considerations
- Test dashboard access (requires instructor role)
- Test statistics calculation accuracy
- Test with multiple courses
- Test with no data (empty states)
- Test performance with large datasets
- Test caching if implemented

