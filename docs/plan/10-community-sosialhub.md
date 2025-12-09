# Plan 10: Community SosialHub

## Overview
Create a social hub similar to Facebook for students to post, interact, and share learning progress.

## Requirements
- Facebook-like feed display
- Student posts
- Profile sections: About, Members, Courses, Bundle
- Profile status: post count and points
- Like/comment functionality
- Friend/follow system (optional)

## Database Changes

### Create Posts Table
File: `database/migrations/xxxx_create_posts_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key)
- `content` (text)
- `post_type` (enum: 'text', 'image', 'video', 'link')
- `media_path` (string, nullable) - for image/video posts
- `link_url` (string, nullable) - for link posts
- `link_preview` (json, nullable) - store link metadata
- `is_public` (boolean, default: true)
- `created_at`, `updated_at`

### Create Post Likes Table
File: `database/migrations/xxxx_create_post_likes_table.php`

Fields:
- `id` (bigInteger)
- `post_id` (foreign key)
- `user_id` (foreign key)
- `created_at`, `updated_at`
- Unique constraint: post_id + user_id

### Create Post Comments Table
File: `database/migrations/xxxx_create_post_comments_table.php`

Fields:
- `id` (bigInteger)
- `post_id` (foreign key)
- `user_id` (foreign key)
- `parent_id` (bigInteger, nullable) - for threaded comments
- `content` (text)
- `created_at`, `updated_at`

### Create User Profiles Table
File: `database/migrations/xxxx_create_user_profiles_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key, unique)
- `bio` (text, nullable)
- `avatar` (string, nullable)
- `cover_photo` (string, nullable)
- `location` (string, nullable)
- `website` (string, nullable)
- `points` (integer, default: 0)
- `created_at`, `updated_at`

### Create User Relationships Table (Optional - for friends/followers)
File: `database/migrations/xxxx_create_user_relationships_table.php`

Fields:
- `id` (bigInteger)
- `follower_id` (foreign key)
- `following_id` (foreign key)
- `status` (enum: 'pending', 'accepted', 'blocked')
- `created_at`, `updated_at`
- Unique constraint: follower_id + following_id

## Models to Create

### Post Model
File: `app/Models/Post.php`
- Relationships: belongsTo(User), hasMany(PostLike, PostComment)
- Scopes: public(), byUser(), recent()

### PostLike Model
File: `app/Models/PostLike.php`
- Relationships: belongsTo(Post, User)

### PostComment Model
File: `app/Models/PostComment.php`
- Relationships: belongsTo(Post, User, PostComment as 'parent')
- Relationships: hasMany(PostComment as 'replies')

### UserProfile Model
File: `app/Models/UserProfile.php`
- Relationships: belongsTo(User)

### UserRelationship Model (Optional)
File: `app/Models/UserRelationship.php`
- Relationships: belongsTo(User as 'follower', User as 'following')

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_posts_table
php artisan make:migration create_post_likes_table
php artisan make:migration create_post_comments_table
php artisan make:migration create_user_profiles_table
php artisan make:migration create_user_relationships_table
```

### 2. Create Models
```bash
php artisan make:model Post
php artisan make:model PostLike
php artisan make:model PostComment
php artisan make:model UserProfile
php artisan make:model UserRelationship
```

### 3. Create Post Controller
File: `app/Http/Controllers/Community/PostController.php`
- `index()` method:
  - Display feed of posts
  - Support pagination
  - Filter by following users (if follow system implemented)
- `store(Request $request)` method:
  - Create new post
  - Handle media upload
  - Award points
- `show($id)` method:
  - Show single post with comments
- `destroy($id)` method:
  - Delete own post

### 4. Create Like Controller
File: `app/Http/Controllers/Community/LikeController.php`
- `toggle(Request $request)` method:
  - Like/unlike post
  - Return JSON response

### 5. Create Comment Controller
File: `app/Http/Controllers/Community/CommentController.php`
- `store(Request $request)` method:
  - Create comment on post
- `destroy($id)` method:
  - Delete own comment

### 6. Create Profile Controller
File: `app/Http/Controllers/Community/ProfileController.php`
- `show($username)` method:
  - Show user profile
  - Display posts, courses, points
- `edit()` method:
  - Edit own profile
- `update(Request $request)` method:
  - Update profile

### 7. Create Routes
File: `routes/web.php`
- `GET /community` → PostController@index
- `POST /community/posts` → PostController@store
- `GET /community/posts/{id}` → PostController@show
- `DELETE /community/posts/{id}` → PostController@destroy
- `POST /community/posts/{id}/like` → LikeController@toggle
- `POST /community/posts/{id}/comments` → CommentController@store
- `DELETE /community/comments/{id}` → CommentController@destroy
- `GET /community/profile/{username}` → ProfileController@show
- `GET /community/profile/edit` → ProfileController@edit
- `PUT /community/profile` → ProfileController@update

### 8. Create SosialHub View
File: `resources/views/community/index.blade.php`
- Post creation form at top
- Feed of posts:
  - User avatar and name
  - Post content
  - Media (if any)
  - Like button and count
  - Comment button and count
  - Timestamp
  - Actions menu (edit/delete for own posts)

### 9. Create Post Component
File: `resources/views/components/community/post.blade.php`
- Reusable post display component
- Show user info
- Show content and media
- Show interactions (like, comment)
- Show comment section

### 10. Create Profile View
File: `resources/views/community/profile.blade.php`
- Cover photo section
- Avatar section
- Profile info (bio, location, website)
- Tabs:
  - About
  - Posts
  - Members (if groups implemented)
  - Courses (user's enrolled courses)
  - Bundle (if implemented)
- Stats: Post count, Points

### 11. Implement Points System
- Award points for:
  - Creating posts
  - Receiving likes
  - Completing courses
  - Daily login (optional)
- Update UserProfile points
- Create service: `app/Services/PointsService.php`

### 12. Implement Media Upload
- Handle image uploads
- Handle video uploads
- Resize images
- Store in `storage/app/public/community/`

### 13. Create JavaScript for Interactions
File: `resources/js/community.js`
- AJAX for like/unlike
- AJAX for comments
- Real-time updates (optional, with Laravel Broadcasting)
- Infinite scroll for feed

## Files to Create/Modify
- `database/migrations/xxxx_create_posts_table.php` (new)
- `database/migrations/xxxx_create_post_likes_table.php` (new)
- `database/migrations/xxxx_create_post_comments_table.php` (new)
- `database/migrations/xxxx_create_user_profiles_table.php` (new)
- `database/migrations/xxxx_create_user_relationships_table.php` (new, optional)
- `app/Models/Post.php` (new)
- `app/Models/PostLike.php` (new)
- `app/Models/PostComment.php` (new)
- `app/Models/UserProfile.php` (new)
- `app/Models/UserRelationship.php` (new, optional)
- `app/Models/User.php` (modify - add hasOne profile, hasMany posts)
- `app/Http/Controllers/Community/PostController.php` (new)
- `app/Http/Controllers/Community/LikeController.php` (new)
- `app/Http/Controllers/Community/CommentController.php` (new)
- `app/Http/Controllers/Community/ProfileController.php` (new)
- `app/Services/PointsService.php` (new)
- `resources/views/community/index.blade.php` (new)
- `resources/views/community/profile.blade.php` (new)
- `resources/views/components/community/post.blade.php` (new)
- `resources/js/community.js` (new)
- `routes/web.php` (modify)

## Dependencies
- Laravel Storage for media uploads
- Image manipulation library (Intervention Image)
- JavaScript for AJAX interactions
- Optional: Laravel Broadcasting for real-time updates

## Points System Implementation
```php
// In PointsService
public function awardPoints(User $user, string $action, int $points)
{
    $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);
    $profile->increment('points', $points);
    
    // Log points transaction (optional)
}
```

## Testing Considerations
- Test post creation
- Test media upload
- Test like/unlike functionality
- Test comment functionality
- Test profile display
- Test points awarding
- Test feed pagination
- Test permission checks (edit/delete own posts)

