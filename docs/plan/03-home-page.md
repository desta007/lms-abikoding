# Plan 03: Home Page (Halaman Utama)

## Overview
Create the main landing page displaying course listings with search, filter, sort, and view options.

## Requirements
- Display list of courses
- Search functionality
- Grid View / List View toggle
- Sort by: Highest Rating, Newest, Oldest
- Filter by: Price, Category, Level, Language, Instructor
- Course cards showing: thumbnail, title, instructor, rating, price, level

## Database Changes

### Create Courses Table
File: `database/migrations/xxxx_create_courses_table.php`

Fields:
- `id` (bigInteger, primary key)
- `slug` (string, unique)
- `title` (string)
- `subtitle` (string)
- `thumbnail` (string)
- `description` (text)
- `about_course` (longText)
- `about_instructor` (longText)
- `category_id` (foreign key)
- `level_id` (foreign key)
- `instructor_id` (foreign key to users)
- `price` (decimal)
- `language` (string, default: 'Indonesian')
- `is_published` (boolean, default: false)
- `created_at`, `updated_at`

### Create Categories Table
File: `database/migrations/xxxx_create_categories_table.php`

Fields:
- `id` (bigInteger)
- `name` (string) - N5, N4, N3, N2, N1
- `slug` (string, unique)
- `description` (text, nullable)
- `created_at`, `updated_at`

### Create Levels Table
File: `database/migrations/xxxx_create_levels_table.php`

Fields:
- `id` (bigInteger)
- `name` (string) - Beginner, Intermediate, Advanced
- `slug` (string, unique)
- `order` (integer)
- `created_at`, `updated_at`

### Create Course Ratings Table
File: `database/migrations/xxxx_create_course_ratings_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `user_id` (foreign key)
- `rating` (integer, 1-5)
- `review` (text, nullable)
- `created_at`, `updated_at`

## Models to Create

### Course Model
File: `app/Models/Course.php`
- Relationships: 
  - belongsTo(Category)
  - belongsTo(Level)
  - belongsTo(User, as: 'instructor') - instructor_id foreign key
  - hasMany(CourseRating, as: 'ratings')
  - hasMany(Chapter) - Note: Chapters will be created in Plan 05
  - hasMany(CourseEnrollment) - Note: CourseEnrollment will be created in Plan 04
- Scopes: published(), byCategory(), byLevel(), byInstructor()
- Accessors: averageRating, totalStudents
- Methods: isPublished(), isFree(), canEnroll(User $user)

### Category Model
File: `app/Models/Category.php`
- Relationships: hasMany(Course)

### Level Model
File: `app/Models/Level.php`
- Relationships: hasMany(Course)

### CourseRating Model
File: `app/Models/CourseRating.php`
- Relationships: belongsTo(Course, User)

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_courses_table
php artisan make:migration create_categories_table
php artisan make:migration create_levels_table
php artisan make:migration create_course_ratings_table
```

### 2. Create Models
```bash
php artisan make:model Course
php artisan make:model Category
php artisan make:model Level
php artisan make:model CourseRating
```

### 3. Create Course Controller
File: `app/Http/Controllers/CourseController.php`
- `index()` method:
  - Handle search query
  - Handle filters (price, category, level, language, instructor)
  - Handle sorting (rating, newest, oldest)
  - Handle pagination
  - Return view with courses

### 4. Create Home Controller
File: `app/Http/Controllers/HomeController.php`
- `index()` method for home page
- Can combine with CourseController or separate

### 5. Create Routes
File: `routes/web.php`
- `GET /` → HomeController@index
- `GET /courses` → CourseController@index (optional, if separate from home)

### 6. Create Home View
File: `resources/views/home.blade.php`
- Header section
- Search bar
- Filter panel (sidebar or dropdown)
  - Price range slider
  - Category checkboxes
  - Level checkboxes
  - Language dropdown
  - Instructor dropdown
- Sort dropdown
- View toggle (Grid/List)
- Course cards grid/list
- Pagination

### 7. Create Course Card Component
File: `resources/views/components/course-card.blade.php`
- Display thumbnail
- Course title
- Instructor name
- Star rating display
- Price
- Level badge
- Link to course detail

### 8. Implement Search Logic
- Use Laravel Scout (optional) or database LIKE queries
- Search in: title, subtitle, description

### 9. Implement Filter Logic
- Use query builder with where clauses
- Price range: between min and max
- Category: whereHas on category relationship
- Level: whereHas on level relationship
- Language: where clause
- Instructor: whereInstructorId

### 10. Implement Sort Logic
- Rating: orderBy average rating (subquery or join)
- Newest: orderBy created_at desc
- Oldest: orderBy created_at asc

### 11. Create Seeder for Categories and Levels
File: `database/seeders/CategorySeeder.php`
- Seed N5, N4, N3, N2, N1

File: `database/seeders/LevelSeeder.php`
- Seed Beginner, Intermediate, Advanced

## Files to Create/Modify
- `database/migrations/xxxx_create_courses_table.php` (new)
- `database/migrations/xxxx_create_categories_table.php` (new)
- `database/migrations/xxxx_create_levels_table.php` (new)
- `database/migrations/xxxx_create_course_ratings_table.php` (new)
- `app/Models/Course.php` (new)
- `app/Models/Category.php` (new)
- `app/Models/Level.php` (new)
- `app/Models/CourseRating.php` (new)
- `app/Http/Controllers/CourseController.php` (new)
- `app/Http/Controllers/HomeController.php` (new)
- `resources/views/home.blade.php` (new)
- `resources/views/components/course-card.blade.php` (new)
- `resources/views/home/filters.blade.php` (new, optional)
- `routes/web.php` (modify)
- `database/seeders/CategorySeeder.php` (new)
- `database/seeders/LevelSeeder.php` (new)

## Dependencies
- Laravel Query Builder
- Blade Components
- Optional: Laravel Scout for advanced search
- **Plan 02**: User registration must be implemented first (for instructor dropdown)
- **Plan 05**: Chapter relationships will be added when chapters are implemented
- **Plan 04**: CourseEnrollment relationships will be added when enrollments are implemented

## JavaScript Considerations
- View toggle functionality (Grid/List)
- Filter form submission (AJAX or form submit)
- Price range slider (use a library like noUiSlider or native HTML5 range)
- Real-time search (optional, with debounce)

## Testing Considerations
- Test course listing display
- Test search functionality
- Test each filter option
- Test sorting options
- Test view toggle
- Test pagination
- Test empty states
- Test with large dataset

