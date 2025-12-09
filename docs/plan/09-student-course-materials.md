# Plan 09: Student Course Materials

## Overview
Display course materials (chapters with videos, PDFs, audio, and text) for enrolled students.

## Requirements
- Display list of chapters (e.g., Chapter 1-5)
- Each chapter contains:
  - Video materials
  - PDF materials
  - Audio recordings
  - Material descriptions
- Track student progress
- Mark materials as complete
- Navigation between materials

## Database Changes

### Create Student Progress Table
File: `database/migrations/xxxx_create_student_progress_table.php`

Fields:
- `id` (bigInteger)
- `course_enrollment_id` (foreign key)
- `chapter_id` (foreign key, nullable)
- `chapter_material_id` (foreign key, nullable)
- `is_completed` (boolean, default: false)
- `completed_at` (timestamp, nullable)
- `progress_percentage` (integer, default: 0) - for videos
- `last_position` (integer, nullable) - video/audio position in seconds
- `created_at`, `updated_at`
- Unique constraint: course_enrollment_id + chapter_material_id

## Models to Create

### StudentProgress Model
File: `app/Models/StudentProgress.php`
- Relationships: belongsTo(CourseEnrollment, Chapter, ChapterMaterial)

### Update CourseEnrollment Model
- Relationship: hasMany(StudentProgress)
- Methods: calculateProgress(), completedChaptersCount(), totalProgressPercentage()

## Implementation Steps

### 1. Create Migration
```bash
php artisan make:migration create_student_progress_table
```

### 2. Create Model
```bash
php artisan make:model StudentProgress
```

### 3. Create Course Content Controller
File: `app/Http/Controllers/CourseContentController.php`
- `index($courseId)` method:
  - Show course content page (chapters list)
  - Ensure user is enrolled
  - Load progress data
- `showChapter($courseId, $chapterId)` method:
  - Show chapter materials
  - Load progress for each material
  - Return view
- `showMaterial($courseId, $chapterId, $materialId)` method:
  - Show individual material player/viewer
  - Track view
  - Return view
- `markComplete(Request $request)` method:
  - Mark material as complete
  - Update progress
  - Return JSON response
- `updateProgress(Request $request)` method:
  - Update video/audio position
  - Update progress percentage
  - Return JSON response

### 4. Create Routes
File: `routes/web.php`
- `GET /courses/{courseId}/content` → CourseContentController@index
- `GET /courses/{courseId}/chapters/{chapterId}` → CourseContentController@showChapter
- `GET /courses/{courseId}/chapters/{chapterId}/materials/{materialId}` → CourseContentController@showMaterial
- `POST /api/progress/complete` → CourseContentController@markComplete
- `POST /api/progress/update` → CourseContentController@updateProgress

### 5. Create Course Content View
File: `resources/views/courses/content.blade.php`
- Sidebar: Chapter list with progress indicators
- Main content: Current chapter/material display
- Progress bar at top
- Navigation buttons (Previous/Next)

### 6. Create Chapter Display View
File: `resources/views/courses/chapter.blade.php`
- Display chapter title and description
- List all materials in chapter
- Show completion status for each material
- Material type icons
- Click to access material

### 7. Create Material Viewers

#### Video Player Component
File: `resources/views/components/video-player.blade.php`
- Use HTML5 video or video.js
- Track playback position
- Save progress on pause/close
- Show completion checkbox

#### PDF Viewer Component
File: `resources/views/components/pdf-viewer.blade.php`
- Use PDF.js or embed PDF
- Show download button
- Track view/completion

#### Audio Player Component
File: `resources/views/components/audio-player.blade.php`
- Use HTML5 audio or wavesurfer.js
- Track playback position
- Show completion checkbox

#### Text Viewer Component
File: `resources/views/components/text-viewer.blade.php`
- Display formatted text content
- Show completion checkbox

### 8. Create Progress Tracker Component
File: `resources/views/components/progress-tracker.blade.php`
- Display overall course progress
- Show chapter completion status
- Visual progress indicators

### 9. Implement Material Access Control
- Middleware to check enrollment
- File: `app/Http/Middleware/EnsureEnrolled.php`
- Verify user is enrolled before accessing materials

### 10. Implement Progress Calculation
- Calculate chapter progress
- Calculate overall course progress
- Update course_enrollment progress_percentage

### 11. Create JavaScript for Progress Tracking
File: `resources/js/course-progress.js`
- Track video position
- Auto-save progress (debounced)
- Mark complete on video end
- Handle play/pause events

### 12. Implement File Serving
- Serve PDFs securely (only for enrolled users)
- Stream videos securely
- Use Laravel Storage with signed URLs (optional)

## Files to Create/Modify
- `database/migrations/xxxx_create_student_progress_table.php` (new)
- `app/Models/StudentProgress.php` (new)
- `app/Models/CourseEnrollment.php` (modify)
- `app/Http/Controllers/CourseContentController.php` (new)
- `app/Http/Middleware/EnsureEnrolled.php` (new)
- `resources/views/courses/content.blade.php` (new)
- `resources/views/courses/chapter.blade.php` (new)
- `resources/views/components/video-player.blade.php` (new)
- `resources/views/components/pdf-viewer.blade.php` (new)
- `resources/views/components/audio-player.blade.php` (new)
- `resources/views/components/text-viewer.blade.php` (new)
- `resources/views/components/progress-tracker.blade.php` (new)
- `resources/js/course-progress.js` (new)
- `routes/web.php` (modify)
- `bootstrap/app.php` (modify - register middleware)

## Dependencies
- HTML5 Video/Audio or video.js/audio.js
- PDF.js for PDF viewing
- Laravel Storage for file serving
- JavaScript for progress tracking

## Progress Calculation Logic
```php
// Chapter progress
$chapterMaterials = $chapter->materials->count();
$completedMaterials = StudentProgress::where('course_enrollment_id', $enrollment->id)
    ->where('chapter_id', $chapter->id)
    ->where('is_completed', true)
    ->count();
$chapterProgress = ($completedMaterials / $chapterMaterials) * 100;

// Course progress
$totalMaterials = $course->chapters->sum(fn($ch) => $ch->materials->count());
$completedMaterials = StudentProgress::where('course_enrollment_id', $enrollment->id)
    ->where('is_completed', true)
    ->count();
$courseProgress = ($completedMaterials / $totalMaterials) * 100;
```

## Security Considerations
- Ensure only enrolled students can access materials
- Validate file paths to prevent directory traversal
- Use signed URLs for file downloads (optional)
- Rate limit API endpoints for progress updates

## Testing Considerations
- Test material access (enrolled vs non-enrolled)
- Test progress tracking
- Test video position saving
- Test completion marking
- Test progress calculation accuracy
- Test chapter navigation
- Test different material types
- Test file serving security

