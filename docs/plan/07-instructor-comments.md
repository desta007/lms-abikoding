# Plan 07: Instructor Comments

## Overview
Allow instructors to view and manage comments from students on course materials/chapters.

## Requirements
- Display comments per chapter/material
- Monitor student learning progress
- Reply to comments
- Moderate comments (approve/delete)

## Database Changes

### Create Comments Table
File: `database/migrations/xxxx_create_comments_table.php`

Fields:
- `id` (bigInteger)
- `chapter_id` (foreign key)
- `chapter_material_id` (foreign key, nullable) - for material-specific comments
- `user_id` (foreign key to student)
- `parent_id` (bigInteger, nullable) - for threaded replies
- `content` (text)
- `is_approved` (boolean, default: true)
- `created_at`, `updated_at`

### Create Comment Reactions Table (Optional)
File: `database/migrations/xxxx_create_comment_reactions_table.php`

Fields:
- `id` (bigInteger)
- `comment_id` (foreign key)
- `user_id` (foreign key)
- `reaction_type` (enum: 'like', 'helpful', etc.)
- `created_at`, `updated_at`

## Models to Create

### Comment Model
File: `app/Models/Comment.php`
- Relationships: belongsTo(Chapter, ChapterMaterial, User)
- Relationships: belongsTo(Comment, as: 'parent') - for parent comment
- Relationships: hasMany(Comment, as: 'replies') - for replies
- Scopes: approved(), byChapter(), byMaterial()

### CommentReaction Model (Optional)
File: `app/Models/CommentReaction.php`
- Relationships: belongsTo(Comment, User)

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_comments_table
php artisan make:migration create_comment_reactions_table
```

### 2. Create Models
```bash
php artisan make:model Comment
php artisan make:model CommentReaction
```

### 3. Update Chapter and ChapterMaterial Models
- Add hasMany(Comment) relationships

### 4. Create Comment Controller
File: `app/Http/Controllers/Instructor/CommentController.php`
- `index()` method:
  - Show all comments for instructor's courses
  - Filter by course, chapter, material
  - Filter by approved/unapproved
  - Support pagination
- `show($id)` method:
  - Show comment details with replies
- `update($id)` method:
  - Approve/unapprove comment
  - Edit comment (if needed)
- `destroy($id)` method:
  - Delete comment
- `reply(Request $request, $id)` method:
  - Create reply to comment

### 5. Create Student Comment Controller (for students to post)
File: `app/Http/Controllers/CommentController.php`
- `store(Request $request)` method:
  - Create new comment
  - Validate: content required, chapter_id required
- `update(Request $request, $id)` method:
  - Update own comment
- `destroy($id)` method:
  - Delete own comment

### 6. Create Routes
File: `routes/web.php`

Instructor routes:
- `GET /instructor/comments` → Instructor\CommentController@index
- `GET /instructor/comments/{id}` → Instructor\CommentController@show
- `PUT /instructor/comments/{id}` → Instructor\CommentController@update
- `DELETE /instructor/comments/{id}` → Instructor\CommentController@destroy
- `POST /instructor/comments/{id}/reply` → Instructor\CommentController@reply

Student routes:
- `POST /comments` → CommentController@store
- `PUT /comments/{id}` → CommentController@update
- `DELETE /comments/{id}` → CommentController@destroy

### 7. Create Instructor Comments View
File: `resources/views/instructor/comments/index.blade.php`
- Filter section:
  - Select course
  - Select chapter
  - Filter by status (All, Approved, Unapproved)
- Comments list:
  - Display comment with user info
  - Show chapter/material context
  - Show replies count
  - Actions: Approve, Delete, Reply
  - Threaded display (parent and replies)

### 8. Create Comment Component
File: `resources/views/components/comment.blade.php`
- Reusable comment display component
- Show user avatar/name
- Show comment content
- Show timestamp
- Show reply button
- Show approve/delete buttons (for instructors)

### 9. Create Comment Form Component
File: `resources/views/components/comment-form.blade.php`
- Textarea for comment
- Submit button
- Can be used for new comments and replies

### 10. Implement Notification System (Optional)
- Notify instructor when new comment is posted
- Use Laravel Notifications

## Files to Create/Modify
- `database/migrations/xxxx_create_comments_table.php` (new)
- `database/migrations/xxxx_create_comment_reactions_table.php` (new, optional)
- `app/Models/Comment.php` (new)
- `app/Models/CommentReaction.php` (new, optional)
- `app/Models/Chapter.php` (modify - add hasMany comments)
- `app/Models/ChapterMaterial.php` (modify - add hasMany comments)
- `app/Http/Controllers/Instructor/CommentController.php` (new)
- `app/Http/Controllers/CommentController.php` (new)
- `resources/views/instructor/comments/index.blade.php` (new)
- `resources/views/components/comment.blade.php` (new)
- `resources/views/components/comment-form.blade.php` (new)
- `routes/web.php` (modify)

## Dependencies
- Laravel Query Builder for filtering
- Optional: Laravel Notifications
- Optional: Laravel Broadcasting for real-time updates

## Comment Display Structure
```
Course: Japanese N5 Basics
Chapter: Chapter 1 - Introduction
Material: Video Lesson 1

Student Name - 2 hours ago
  Comment content here...
  
  Reply from Instructor - 1 hour ago
    Reply content...

Student Name 2 - 1 day ago
  Another comment...
```

## Testing Considerations
- Test comment creation
- Test comment approval/unapproval
- Test comment deletion
- Test reply functionality
- Test filtering by course/chapter
- Test threaded display
- Test unauthorized access prevention
- Test instructor can moderate all comments
- Test students can only edit/delete own comments

