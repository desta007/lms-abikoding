# Plan 05: Instructor Create Course

## Overview
Allow instructors to create courses with all required fields and chapters/materials.

## Requirements
- Course form fields:
  - Slug (auto-generated from title, editable)
  - Subtitle (Subjudul Kursus)
  - Thumbnail (image upload)
  - Category (dropdown)
  - About Course (Tentang Kursus) - rich text editor
  - Instructional Level (Tingkat Instruksional)
  - About Instructor (Tentang Instruktur) - rich text editor
  - Submit button (Buat Kursus)
- Chapter/Material management:
  - Add multiple chapters
  - Each chapter can contain: PDF, Image, Video, Audio, Text
  - Chapter order management

## Database Changes

### Create Chapters Table
File: `database/migrations/xxxx_create_chapters_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `title` (string)
- `description` (text, nullable)
- `order` (integer) - for ordering chapters
- `is_published` (boolean, default: false)
- `created_at`, `updated_at`

### Create Chapter Materials Table
File: `database/migrations/xxxx_create_chapter_materials_table.php`

Fields:
- `id` (bigInteger)
- `chapter_id` (foreign key)
- `material_type` (enum: 'pdf', 'image', 'video', 'audio', 'text')
- `title` (string)
- `content` (text, nullable) - for text materials
- `file_path` (string, nullable) - for file-based materials
- `file_size` (bigInteger, nullable)
- `file_mime_type` (string, nullable)
- `order` (integer) - for ordering materials within chapter
- `duration` (integer, nullable) - for video/audio in seconds
- `created_at`, `updated_at`

### Update Courses Table
Ensure courses table has all required fields (from plan 03):
- `slug`, `subtitle`, `thumbnail`, `about_course`, `about_instructor`

## Models to Create

### Chapter Model
File: `app/Models/Chapter.php`
- Relationships: belongsTo(Course), hasMany(ChapterMaterial)
- Scopes: published(), ordered()

### ChapterMaterial Model
File: `app/Models/ChapterMaterial.php`
- Relationships: belongsTo(Chapter)
- Casts: material_type enum

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_chapters_table
php artisan make:migration create_chapter_materials_table
```

### 2. Create Models
```bash
php artisan make:model Chapter
php artisan make:model ChapterMaterial
```

### 3. Create Course Controller
File: `app/Http/Controllers/Instructor/CourseController.php`
- `create()` - Show create form
- `store(Request $request)` - Save course
  - Validate all fields
  - Handle thumbnail upload
  - Generate slug if not provided
  - Set instructor_id from authenticated user
- `edit($id)` - Show edit form
- `update(Request $request, $id)` - Update course
- `destroy($id)` - Delete course

### 4. Create Chapter Controller
File: `app/Http/Controllers/Instructor/ChapterController.php`
- `store(Request $request)` - Create chapter
- `update(Request $request, $id)` - Update chapter
- `destroy($id)` - Delete chapter
- `reorder(Request $request)` - Update chapter order

### 5. Create Chapter Material Controller
File: `app/Http/Controllers/Instructor/ChapterMaterialController.php`
- `store(Request $request)` - Upload/add material
- `update(Request $request, $id)` - Update material
- `destroy($id)` - Delete material
- `reorder(Request $request)` - Update material order

### 6. Create Routes
File: `routes/web.php`
- `GET /instructor/courses/create` → CourseController@create
- `POST /instructor/courses` → CourseController@store
- `GET /instructor/courses/{id}/edit` → CourseController@edit
- `PUT /instructor/courses/{id}` → CourseController@update
- `DELETE /instructor/courses/{id}` → CourseController@destroy
- `POST /instructor/courses/{courseId}/chapters` → ChapterController@store
- `PUT /instructor/chapters/{id}` → ChapterController@update
- `DELETE /instructor/chapters/{id}` → ChapterController@destroy
- `POST /instructor/chapters/{chapterId}/materials` → ChapterMaterialController@store
- `PUT /instructor/materials/{id}` → ChapterMaterialController@update
- `DELETE /instructor/materials/{id}` → ChapterMaterialController@destroy

### 7. Create Course Form View
File: `resources/views/instructor/courses/create.blade.php`
- Form sections:
  - Basic Info (title, subtitle, slug)
  - Thumbnail upload (with preview)
  - Category and Level select
  - About Course (rich text editor)
  - About Instructor (rich text editor)
- Chapter management section:
  - Add Chapter button
  - Chapter list (draggable for reordering)
  - Each chapter expandable to add materials
- Submit button

### 8. Create Chapter Form Component
File: `resources/views/components/instructor/chapter-form.blade.php`
- Chapter title input
- Chapter description textarea
- Material upload section:
  - Material type selector (PDF, Image, Video, Audio, Text)
  - File upload input (for files)
  - Content textarea (for text)
  - Add material button
- Material list display

### 9. Implement File Upload Handling
- Use Laravel Storage facade
- Store files in `storage/app/public/courses/`
- Create symbolic link: `php artisan storage:link`
- Validate file types and sizes:
  - PDF: max 10MB
  - Images: jpg, png, max 5MB
  - Videos: mp4, max 100MB
  - Audio: mp3, wav, max 50MB

### 10. Implement Slug Generation
- Auto-generate from title
- Make editable
- Ensure uniqueness
- Use Str::slug() helper

### 11. Create Rich Text Editor
- Use TinyMCE, CKEditor, or Quill.js
- For "About Course" and "About Instructor" fields

### 12. Implement Drag and Drop Ordering
- Use Sortable.js or similar library
- AJAX request to update order in database

## Files to Create/Modify
- `database/migrations/xxxx_create_chapters_table.php` (new)
- `database/migrations/xxxx_create_chapter_materials_table.php` (new)
- `app/Models/Chapter.php` (new)
- `app/Models/ChapterMaterial.php` (new)
- `app/Models/Course.php` (modify - add hasMany chapters)
- `app/Http/Controllers/Instructor/CourseController.php` (new)
- `app/Http/Controllers/Instructor/ChapterController.php` (new)
- `app/Http/Controllers/Instructor/ChapterMaterialController.php` (new)
- `app/Http/Requests/CreateCourseRequest.php` (new, for validation)
- `app/Http/Requests/UpdateCourseRequest.php` (new, for validation)
- `resources/views/instructor/courses/create.blade.php` (new)
- `resources/views/instructor/courses/edit.blade.php` (new)
- `resources/views/components/instructor/chapter-form.blade.php` (new)
- `routes/web.php` (modify)
- `config/filesystems.php` (verify disk configuration)

## Dependencies
- Laravel Storage for file uploads
- Rich text editor library (TinyMCE/CKEditor)
- JavaScript library for drag-and-drop (Sortable.js)
- Image manipulation library (Intervention Image, optional)

## Validation Rules

### Course Creation
```php
'title' => 'required|string|max:255',
'subtitle' => 'required|string|max:255',
'slug' => 'nullable|string|max:255|unique:courses',
'thumbnail' => 'required|image|max:2048',
'category_id' => 'required|exists:categories,id',
'level_id' => 'required|exists:levels,id',
'about_course' => 'required|string',
'about_instructor' => 'required|string',
```

### Chapter Creation
```php
'title' => 'required|string|max:255',
'description' => 'nullable|string',
'order' => 'nullable|integer',
```

### Material Creation
```php
'material_type' => 'required|in:pdf,image,video,audio,text',
'title' => 'required|string|max:255',
'file' => 'required_if:material_type,pdf,image,video,audio|file|max:102400',
'content' => 'required_if:material_type,text|string',
```

## Testing Considerations
- Test course creation with all fields
- Test thumbnail upload
- Test slug generation and uniqueness
- Test chapter creation and ordering
- Test material upload for each type
- Test file validation (type, size)
- Test form validation
- Test unauthorized access prevention

