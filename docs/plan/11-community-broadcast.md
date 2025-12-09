# Plan 11: Community Broadcast (Siaran)

## Overview
Allow students to share learning achievements via broadcasts (posts) containing video, PDF, voice, or text.

## Requirements
- Display broadcasts feed
- Students can create broadcasts with:
  - Video
  - PDF
  - Voice recording
  - Text
- Show learning progress/achievements
- Like and comment functionality

## Database Changes

### Create Broadcasts Table
File: `database/migrations/xxxx_create_broadcasts_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key)
- `course_id` (foreign key, nullable) - related course
- `chapter_id` (foreign key, nullable) - related chapter
- `title` (string)
- `content` (text, nullable)
- `broadcast_type` (enum: 'video', 'pdf', 'voice', 'text')
- `media_path` (string, nullable)
- `media_duration` (integer, nullable) - for video/voice in seconds
- `file_size` (bigInteger, nullable)
- `file_mime_type` (string, nullable)
- `is_featured` (boolean, default: false)
- `views_count` (integer, default: 0)
- `created_at`, `updated_at`

### Create Broadcast Likes Table
File: `database/migrations/xxxx_create_broadcast_likes_table.php`

Fields:
- `id` (bigInteger)
- `broadcast_id` (foreign key)
- `user_id` (foreign key)
- `created_at`, `updated_at`
- Unique constraint: broadcast_id + user_id

### Create Broadcast Comments Table
File: `database/migrations/xxxx_create_broadcast_comments_table.php`

Fields:
- `id` (bigInteger)
- `broadcast_id` (foreign key)
- `user_id` (foreign key)
- `content` (text)
- `created_at`, `updated_at`

### Create Broadcast Views Table
File: `database/migrations/xxxx_create_broadcast_views_table.php`

Fields:
- `id` (bigInteger)
- `broadcast_id` (foreign key)
- `user_id` (foreign key, nullable)
- `ip_address` (string)
- `viewed_at` (timestamp)
- `created_at`, `updated_at`

## Models to Create

### Broadcast Model
File: `app/Models/Broadcast.php`
- Relationships: belongsTo(User, Course, Chapter), hasMany(BroadcastLike, BroadcastComment, BroadcastView)
- Scopes: byType(), recent(), featured()

### BroadcastLike Model
File: `app/Models/BroadcastLike.php`
- Relationships: belongsTo(Broadcast, User)

### BroadcastComment Model
File: `app/Models/BroadcastComment.php`
- Relationships: belongsTo(Broadcast, User)

### BroadcastView Model
File: `app/Models/BroadcastView.php`
- Relationships: belongsTo(Broadcast, User)

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_broadcasts_table
php artisan make:migration create_broadcast_likes_table
php artisan make:migration create_broadcast_comments_table
php artisan make:migration create_broadcast_views_table
```

### 2. Create Models
```bash
php artisan make:model Broadcast
php artisan make:model BroadcastLike
php artisan make:model BroadcastComment
php artisan make:model BroadcastView
```

### 3. Create Broadcast Controller
File: `app/Http/Controllers/Community/BroadcastController.php`
- `index()` method:
  - Display broadcasts feed
  - Filter by type if requested
  - Support pagination
- `create()` method:
  - Show broadcast creation form
- `store(Request $request)` method:
  - Validate and create broadcast
  - Handle file upload (video, PDF, voice)
  - Extract metadata (duration, file size)
  - Award points
- `show($id)` method:
  - Display single broadcast
  - Track view
  - Show comments
- `destroy($id)` method:
  - Delete own broadcast

### 4. Create Broadcast Like Controller
File: `app/Http/Controllers/Community/BroadcastLikeController.php`
- `toggle(Request $request)` method:
  - Like/unlike broadcast
  - Return JSON response

### 5. Create Broadcast Comment Controller
File: `app/Http/Controllers/Community/BroadcastCommentController.php`
- `store(Request $request)` method:
  - Create comment on broadcast
- `destroy($id)` method:
  - Delete own comment

### 6. Create Routes
File: `routes/web.php`
- `GET /community/broadcasts` → BroadcastController@index
- `GET /community/broadcasts/create` → BroadcastController@create
- `POST /community/broadcasts` → BroadcastController@store
- `GET /community/broadcasts/{id}` → BroadcastController@show
- `DELETE /community/broadcasts/{id}` → BroadcastController@destroy
- `POST /community/broadcasts/{id}/like` → BroadcastLikeController@toggle
- `POST /community/broadcasts/{id}/comments` → BroadcastCommentController@store
- `DELETE /community/comments/{id}` → BroadcastCommentController@destroy

### 7. Create Broadcast Feed View
File: `resources/views/community/broadcasts/index.blade.php`
- Filter tabs: All, Video, PDF, Voice, Text
- Create Broadcast button
- Feed display:
  - Broadcast cards/list
  - Show type icon
  - Show preview/thumbnail
  - Show title and excerpt
  - Show likes and comments count
  - Show views count

### 8. Create Broadcast Creation View
File: `resources/views/community/broadcasts/create.blade.php`
- Form with:
  - Broadcast type selector (radio buttons)
  - Title input
  - Content textarea (for text broadcasts)
  - File upload:
    - Video upload (accept video/*)
    - PDF upload (accept .pdf)
    - Voice upload (accept audio/*)
  - Course selector (optional - link to course)
  - Chapter selector (optional - if course selected)
  - Submit button

### 9. Create Broadcast Detail View
File: `resources/views/community/broadcasts/show.blade.php`
- Display broadcast:
  - Title
  - User info
  - Video player (if video)
  - PDF viewer (if PDF)
  - Audio player (if voice)
  - Text content (if text)
- Related course/chapter info
- Likes and comments section
- View count

### 10. Create Broadcast Components

#### Broadcast Card Component
File: `resources/views/components/community/broadcast-card.blade.php`
- Display broadcast preview
- Type icon
- Thumbnail/preview
- Title and excerpt
- Stats (likes, comments, views)

#### Broadcast Player Component
File: `resources/views/components/community/broadcast-player.blade.php`
- Video player for broadcasts
- Audio player for voice recordings
- PDF viewer for PDFs

### 11. Implement File Upload Handling
- Video: Store in `storage/app/public/broadcasts/videos/`
- PDF: Store in `storage/app/public/broadcasts/pdfs/`
- Voice: Store in `storage/app/public/broadcasts/voices/`
- Validate file types and sizes:
  - Video: mp4, max 200MB
  - PDF: max 50MB
  - Voice: mp3, wav, max 100MB

### 12. Implement Metadata Extraction
- Extract video duration using FFmpeg or similar
- Extract audio duration
- Get file size and MIME type
- Store metadata in database

### 13. Implement View Tracking
- Track views when broadcast is viewed
- Increment views_count
- Store IP address and user_id (if authenticated)

### 14. Create JavaScript for Interactions
File: `resources/js/broadcasts.js`
- AJAX for like/unlike
- AJAX for comments
- File upload progress
- Video/audio player controls

## Files to Create/Modify
- `database/migrations/xxxx_create_broadcasts_table.php` (new)
- `database/migrations/xxxx_create_broadcast_likes_table.php` (new)
- `database/migrations/xxxx_create_broadcast_comments_table.php` (new)
- `database/migrations/xxxx_create_broadcast_views_table.php` (new)
- `app/Models/Broadcast.php` (new)
- `app/Models/BroadcastLike.php` (new)
- `app/Models/BroadcastComment.php` (new)
- `app/Models/BroadcastView.php` (new)
- `app/Models/Course.php` (modify - add hasMany broadcasts)
- `app/Models/Chapter.php` (modify - add hasMany broadcasts)
- `app/Http/Controllers/Community/BroadcastController.php` (new)
- `app/Http/Controllers/Community/BroadcastLikeController.php` (new)
- `app/Http/Controllers/Community/BroadcastCommentController.php` (new)
- `resources/views/community/broadcasts/index.blade.php` (new)
- `resources/views/community/broadcasts/create.blade.php` (new)
- `resources/views/community/broadcasts/show.blade.php` (new)
- `resources/views/components/community/broadcast-card.blade.php` (new)
- `resources/views/components/community/broadcast-player.blade.php` (new)
- `resources/js/broadcasts.js` (new)
- `routes/web.php` (modify)

## Dependencies
- Laravel Storage for file uploads
- FFmpeg or similar for video/audio metadata extraction (optional)
- Video.js or HTML5 video player
- PDF.js for PDF viewing
- Audio.js or HTML5 audio player

## File Upload Validation
```php
'video' => 'required_if:broadcast_type,video|file|mimes:mp4,avi,mov|max:204800',
'pdf' => 'required_if:broadcast_type,pdf|file|mimes:pdf|max:51200',
'voice' => 'required_if:broadcast_type,voice|file|mimes:mp3,wav|max:102400',
'content' => 'required_if:broadcast_type,text|string',
```

## Testing Considerations
- Test broadcast creation for each type
- Test file upload validation
- Test video/audio playback
- Test PDF viewing
- Test like/unlike functionality
- Test comment functionality
- Test view tracking
- Test file serving security

