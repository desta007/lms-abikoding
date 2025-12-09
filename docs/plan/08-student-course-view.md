# Plan 08: Student Course View

## Overview
Create course detail page for students showing course information, chapters, reviews, instructor info, and discussions.

## Requirements
- Course Header:
  - Star rating (1-5)
  - Course name
  - Number of participants
  - Course level (Beginner/Intermediate/Advanced)
- Tab Bar:
  - Course Content (Konten Kursus): Expandable/collapsible chapter list
  - Course Description (Gambaran Kursus)
  - Course Reviews (Review Kursus)
  - Instructor (Instruktur)
  - Discussion (Diskusi)
- Bottom Section:
  - Similar courses recommendations

## Database Changes
- Use existing tables: courses, chapters, course_enrollments, course_ratings, comments

### Create Course Recommendations Logic
- Can be based on:
  - Same category
  - Same instructor
  - Same level
  - User enrollment history

## Implementation Steps

### 1. Create Course Detail Controller
File: `app/Http/Controllers/CourseController.php`
- `show($slug)` method:
  - Load course with relationships (chapters, instructor, ratings, enrollments)
  - Calculate average rating
  - Get total enrolled students
  - Get similar courses
  - Check if user is enrolled
  - Return view

### 2. Create Enrollment Controller
File: `app/Http/Controllers/CourseEnrollmentController.php`
- `store(Request $request)` method:
  - Enroll student in course
  - Create CourseEnrollment record
  - Redirect to course page

### 3. Create Routes
File: `routes/web.php`
- `GET /courses/{slug}` → CourseController@show
- `POST /courses/{id}/enroll` → CourseEnrollmentController@store
- `GET /courses/{id}/content` → CourseController@content (for enrolled students)

### 4. Create Course Detail View
File: `resources/views/courses/show.blade.php`
- Header section:
  - Course thumbnail
  - Course title
  - Star rating display (read-only)
  - Enrolled students count
  - Level badge
  - Enroll button (if not enrolled)
- Tab navigation:
  - Use Bootstrap tabs or custom tab component
- Tab content sections:
  - Course Content tab
  - Course Description tab
  - Reviews tab
  - Instructor tab
  - Discussion tab
- Similar courses section (bottom)

### 5. Create Course Header Component
File: `resources/views/components/course-header.blade.php`
- Display thumbnail, title, rating, participants, level
- Enroll button

### 6. Create Chapter List Component
File: `resources/views/components/chapter-list.blade.php`
- Display chapters in expandable/collapsible format
- Show chapter title
- Show materials count
- Show completion status (if enrolled)
- Use JavaScript for expand/collapse

### 7. Create Course Reviews Section
File: `resources/views/courses/partials/reviews.blade.php`
- Display course ratings and reviews
- Show average rating
- Display individual reviews with:
  - User name/avatar
  - Rating stars
  - Review text
  - Date
- Pagination for reviews
- Allow enrolled students to add review

### 8. Create Instructor Section
File: `resources/views/courses/partials/instructor.blade.php`
- Display instructor information:
  - Profile picture
  - Name
  - About instructor (from course)
  - Other courses by instructor
  - Total students
  - Rating

### 9. Create Discussion Section
File: `resources/views/courses/partials/discussion.blade.php`
- Display course comments/discussions
- Allow students to post comments
- Show threaded replies
- Use Comment component from plan 07

### 10. Create Similar Courses Component
File: `resources/views/components/similar-courses.blade.php`
- Display grid/list of similar courses
- Filter by: same category, same level, same instructor
- Show course cards

### 11. Implement Enrollment Logic
- Check if user is already enrolled
- **Payment Handling**: 
  - For free courses: Enroll directly
  - For paid courses: Redirect to payment flow (see Plan 19 for complete payment integration)
  - Payment status must be verified before enrollment completes
- Create enrollment record
- Redirect to course content (or payment page if paid)

### 12. Implement Rating Display
- Calculate average rating from CourseRating model
- Display star rating component
- Handle fractional ratings (e.g., 4.5 stars)

## Files to Create/Modify
- `app/Http/Controllers/CourseController.php` (modify - add show method)
- `app/Http/Controllers/CourseEnrollmentController.php` (new)
- `resources/views/courses/show.blade.php` (new)
- `resources/views/courses/partials/reviews.blade.php` (new)
- `resources/views/courses/partials/instructor.blade.php` (new)
- `resources/views/courses/partials/discussion.blade.php` (new)
- `resources/views/components/course-header.blade.php` (new)
- `resources/views/components/chapter-list.blade.php` (new)
- `resources/views/components/similar-courses.blade.php` (new)
- `resources/views/components/star-rating.blade.php` (new)
- `routes/web.php` (modify)

## Dependencies
- Laravel Query Builder
- JavaScript for tab switching and expand/collapse
- Optional: Laravel Scout for similar courses search
- **Plan 03**: Courses, Categories, Levels tables
- **Plan 05**: Chapters table (for course content display)
- **Plan 04**: CourseEnrollment table (for enrollment functionality)
- **Plan 07**: Comments table (for discussion section)
- **Plan 19**: Course payment integration (for paid course enrollment)

## JavaScript Functionality
- Tab switching
- Chapter expand/collapse
- Smooth scrolling
- AJAX for enrollment (optional)

## Similar Courses Algorithm
```php
// Get courses with same category
$similarCourses = Course::where('category_id', $course->category_id)
    ->where('id', '!=', $course->id)
    ->where('is_published', true)
    ->with(['instructor', 'ratings'])
    ->take(6)
    ->get();
```

## Testing Considerations
- Test course detail page display
- Test tab switching
- Test chapter expand/collapse
- Test enrollment functionality
- Test review display
- Test similar courses display
- Test access control (enrolled vs non-enrolled)
- Test rating calculation

