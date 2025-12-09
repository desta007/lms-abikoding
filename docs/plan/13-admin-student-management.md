# Plan 13: Admin Student Management

## Overview
Admin panel to manage student data, view activities, and prepare candidates for job placement in Japan.

## Requirements
- View student biodata
- Access all student activities and posts
- Offer candidates to Japanese companies
- Manage candidate profiles

## Database Changes

### Create Admin Logs Table (Optional)
File: `database/migrations/xxxx_create_admin_logs_table.php`

Fields:
- `id` (bigInteger)
- `admin_id` (foreign key)
- `action` (string) - 'view_student', 'offer_candidate', etc.
- `target_user_id` (foreign key, nullable)
- `details` (json, nullable)
- `ip_address` (string)
- `created_at`, `updated_at`

### Create Job Applications Table
File: `database/migrations/xxxx_create_job_applications_table.php`

Fields:
- `id` (bigInteger)
- `student_id` (foreign key)
- `company_id` (foreign key)
- `status` (enum: 'pending', 'reviewed', 'accepted', 'rejected')
- `admin_notes` (text, nullable)
- `applied_at` (timestamp)
- `created_at`, `updated_at`

### Create Companies Table
File: `database/migrations/xxxx_create_companies_table.php`

Fields:
- `id` (bigInteger)
- `name` (string)
- `name_japanese` (string, nullable) - Japanese company name
- `industry` (string, nullable)
- `location` (string, nullable)
- `contact_email` (string, nullable)
- `contact_phone` (string, nullable)
- `status` (enum: 'active', 'inactive')
- `created_at`, `updated_at`

### Create Student Profiles Extended Table (or extend existing)
Additional fields for job placement:
- `resume_path` (string, nullable)
- `cv_path` (string, nullable)
- `language_proficiency` (json, nullable) - store JLPT levels
- `work_experience` (json, nullable)
- `education` (json, nullable)
- `availability_date` (date, nullable)
- `preferred_locations` (json, nullable)
- `job_preferences` (json, nullable)

## Models to Create

### AdminLog Model (Optional)
File: `app/Models/AdminLog.php`
- Relationships: belongsTo(User as 'admin', User as 'target')

### JobApplication Model
File: `app/Models/JobApplication.php`
- Relationships: belongsTo(User as 'student', Company)

### Company Model
File: `app/Models/Company.php`
- Relationships: hasMany(JobApplication)

### Update UserProfile Model
- Add job placement related fields

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_admin_logs_table
php artisan make:migration create_job_applications_table
php artisan make:migration create_companies_table
php artisan make:migration add_job_fields_to_user_profiles_table
```

### 2. Create Models
```bash
php artisan make:model AdminLog
php artisan make:model JobApplication
php artisan make:model Company
```

### 3. Create Admin Dashboard Controller
File: `app/Http/Controllers/Admin/DashboardController.php`
- `index()` method:
  - Display admin dashboard
  - Show statistics:
    - Total students
    - Active students
    - Students ready for placement
    - Companies registered
    - Active applications

### 4. Create Student Management Controller
File: `app/Http/Controllers/Admin/StudentController.php`
- `index()` method:
  - List all students
  - Filter by: language level, availability, status
  - Search by name, email
  - Support pagination
- `show($id)` method:
  - Display full student profile
  - Show all activities
  - Show all posts
  - Show course progress
  - Show language proficiency
- `update(Request $request, $id)` method:
  - Update student profile
  - Update job placement status
- `export()` method:
  - Export student data to CSV/Excel

### 5. Create Activity Controller
File: `app/Http/Controllers/Admin/ActivityController.php`
- `index()` method:
  - Show all student activities
  - Filter by student, date range, activity type
- `show($id)` method:
  - Show detailed activity log

### 6. Create Company Controller
File: `app/Http/Controllers/Admin/CompanyController.php`
- `index()` method:
  - List all companies
- `create()` method:
  - Show company creation form
- `store(Request $request)` method:
  - Create new company
- `edit($id)` method:
  - Show edit form
- `update(Request $request, $id)` method:
  - Update company

### 7. Create Job Application Controller
File: `app/Http/Controllers/Admin/JobApplicationController.php`
- `index()` method:
  - List all job applications
  - Filter by status, company, student
- `create()` method:
  - Show application form
  - Select student and company
- `store(Request $request)` method:
  - Create job application
  - Notify student and company
- `update(Request $request, $id)` method:
  - Update application status
  - Add admin notes

### 8. Create Routes
File: `routes/web.php`
- `GET /admin` → Admin\DashboardController@index
- `GET /admin/students` → Admin\StudentController@index
- `GET /admin/students/{id}` → Admin\StudentController@show
- `PUT /admin/students/{id}` → Admin\StudentController@update
- `GET /admin/students/export` → Admin\StudentController@export
- `GET /admin/activities` → Admin\ActivityController@index
- `GET /admin/companies` → Admin\CompanyController@index
- `GET /admin/companies/create` → Admin\CompanyController@create
- `POST /admin/companies` → Admin\CompanyController@store
- `GET /admin/applications` → Admin\JobApplicationController@index
- `POST /admin/applications` → Admin\JobApplicationController@store
- `PUT /admin/applications/{id}` → Admin\JobApplicationController@update

### 9. Create Admin Dashboard View
File: `resources/views/admin/dashboard.blade.php`
- Statistics cards
- Recent activities
- Quick actions
- Charts/graphs

### 10. Create Student List View
File: `resources/views/admin/students/index.blade.php`
- Search and filter panel
- Student table:
  - Photo/avatar
  - Name
  - Email
  - Language level (JLPT)
  - Courses completed
  - Status
  - Actions: View, Edit, Offer
- Pagination

### 11. Create Student Detail View
File: `resources/views/admin/students/show.blade.php`
- Tabs:
  - Profile: Biodata, resume, CV
  - Activities: All activity logs
  - Posts: All community posts
  - Courses: Enrolled courses and progress
  - Applications: Job applications history
- Action buttons: Edit, Offer to Company

### 12. Create Company Management View
File: `resources/views/admin/companies/index.blade.php`
- List companies
- Action buttons: Create, Edit, Delete

### 13. Create Job Application View
File: `resources/views/admin/applications/index.blade.php`
- List applications
- Filter by status
- Show student and company info
- Status update form

### 14. Implement Student Activity Aggregation
- Collect activities from:
  - Course enrollments
  - Course completions
  - Posts (SosialHub)
  - Broadcasts
  - Calendar events
  - Comments

### 15. Implement Candidate Offering
- Select student(s)
- Select company
- Create job application
- Send notification

## Files to Create/Modify
- `database/migrations/xxxx_create_admin_logs_table.php` (new, optional)
- `database/migrations/xxxx_create_job_applications_table.php` (new)
- `database/migrations/xxxx_create_companies_table.php` (new)
- `database/migrations/xxxx_add_job_fields_to_user_profiles_table.php` (new)
- `app/Models/AdminLog.php` (new, optional)
- `app/Models/JobApplication.php` (new)
- `app/Models/Company.php` (new)
- `app/Models/UserProfile.php` (modify)
- `app/Http/Controllers/Admin/DashboardController.php` (new)
- `app/Http/Controllers/Admin/StudentController.php` (new)
- `app/Http/Controllers/Admin/ActivityController.php` (new)
- `app/Http/Controllers/Admin/CompanyController.php` (new)
- `app/Http/Controllers/Admin/JobApplicationController.php` (new)
- `resources/views/admin/dashboard.blade.php` (new)
- `resources/views/admin/students/index.blade.php` (new)
- `resources/views/admin/students/show.blade.php` (new)
- `resources/views/admin/companies/index.blade.php` (new)
- `resources/views/admin/applications/index.blade.php` (new)
- `routes/web.php` (modify)

## Dependencies
- Laravel Excel or similar for export functionality
- Laravel Notifications for job application notifications

## Multilingual Support
- Database: Ensure UTF-8 encoding for Japanese text
- Forms: Accept Japanese characters in all text inputs
- Display: Properly render Japanese text (Hiragana, Katakana, Kanji)
- PDF generation: Support Japanese fonts in resume/CV exports

## Testing Considerations
- Test admin access control
- Test student profile viewing
- Test activity aggregation
- Test job application creation
- Test company management
- Test data export functionality
- Test Japanese text handling in all fields

