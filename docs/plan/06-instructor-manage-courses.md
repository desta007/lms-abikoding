# Plan 06: Instructor Manage Courses

## Overview
Create interface for instructors to view, edit, and manage their created courses.

## Requirements
- Display list of all courses created by instructor
- Show course status (published/unpublished)
- Quick actions: Edit, Delete, View
- Bulk actions (optional)
- Search and filter courses

## Database Changes
- No new tables needed (uses existing courses table)

## Implementation Steps

### 1. Update Course Controller
File: `app/Http/Controllers/Instructor/CourseController.php`
- `index()` method:
  - Get courses where instructor_id = auth()->id()
  - Support pagination
  - Support search
  - Support filter by status, category
  - Return view with courses
- `show($id)` method:
  - Show course details (read-only)
  - Ensure course belongs to instructor
- `edit($id)` method:
  - Load course with chapters and materials
  - Ensure course belongs to instructor
- `update(Request $request, $id)` method:
  - Validate and update course
  - Ensure course belongs to instructor
- `destroy($id)` method:
  - Delete course and related data
  - Ensure course belongs to instructor
- `publish($id)` method:
  - Toggle publish status
  - Ensure course belongs to instructor

### 2. Create Routes
File: `routes/web.php`
- `GET /instructor/courses` → CourseController@index
- `GET /instructor/courses/{id}` → CourseController@show
- `GET /instructor/courses/{id}/edit` → CourseController@edit
- `PUT /instructor/courses/{id}` → CourseController@update
- `DELETE /instructor/courses/{id}` → CourseController@destroy
- `POST /instructor/courses/{id}/publish` → CourseController@publish

### 3. Create Course List View
File: `resources/views/instructor/courses/index.blade.php`
- Header section:
  - Title: "Kelola Kursus"
  - Create Course button
  - Search bar
- Filter section:
  - Filter by status (All, Published, Unpublished)
  - Filter by category
- Course table/list:
  - Thumbnail
  - Course title
  - Category badge
  - Level badge
  - Status badge (Published/Unpublished)
  - Statistics: Students enrolled, Views
  - Actions: View, Edit, Delete, Publish/Unpublish
- Pagination

### 4. Create Course Card Component (for list view)
File: `resources/views/components/instructor/course-card.blade.php`
- Display course information
- Action buttons
- Status indicator

### 5. Create Course Detail View
File: `resources/views/instructor/courses/show.blade.php`
- Display all course information
- Show chapters and materials
- Statistics section
- Action buttons (Edit, Delete, Publish)

### 6. Update Course Edit View
File: `resources/views/instructor/courses/edit.blade.php`
- Similar to create form
- Pre-populate with existing data
- Show existing chapters and materials
- Allow editing and reordering

### 7. Implement Authorization
- Use Policy or middleware to ensure instructor owns course
- File: `app/Policies/CoursePolicy.php`
  - `view()`, `update()`, `delete()` methods

### 8. Add Soft Deletes (Optional)
- Add `deleted_at` column to courses table
- Use SoftDeletes trait in Course model
- Allows restoration of deleted courses

### 9. Create Confirmation Modal
File: `resources/views/components/modal.blade.php`
- Reusable modal component
- For delete confirmation

## Files to Create/Modify
- `app/Http/Controllers/Instructor/CourseController.php` (modify)
- `app/Policies/CoursePolicy.php` (new)
- `resources/views/instructor/courses/index.blade.php` (new)
- `resources/views/instructor/courses/show.blade.php` (new)
- `resources/views/instructor/courses/edit.blade.php` (modify)
- `resources/views/components/instructor/course-card.blade.php` (new)
- `resources/views/components/modal.blade.php` (new)
- `routes/web.php` (modify)
- `app/Providers/AuthServiceProvider.php` (modify - register policy)

## Authorization Logic
```php
// In CoursePolicy
public function update(User $user, Course $course)
{
    return $user->id === $course->instructor_id && $user->role === 'instructor';
}
```

## Features to Implement

### Search
- Search by course title, subtitle
- Use Laravel's whereLike or full-text search

### Filter
- By status (published/unpublished)
- By category
- By level
- By date created

### Bulk Actions (Optional)
- Bulk publish/unpublish
- Bulk delete
- Bulk category change

## Dependencies
- Laravel Policies for authorization
- Optional: Laravel Scout for advanced search

## Testing Considerations
- Test course listing (only own courses)
- Test edit access (only own courses)
- Test delete functionality
- Test publish/unpublish toggle
- Test search functionality
- Test filters
- Test pagination
- Test unauthorized access prevention

