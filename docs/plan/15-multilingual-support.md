# Plan 15: Multilingual Support (Indonesian and Japanese)

## Overview
Ensure the LMS properly supports both Indonesian and Japanese text throughout the application, especially for quiz/exam creation and course content.

## Requirements
- Support Japanese text input (Hiragana, Katakana, Kanji)
- Support Indonesian text input
- Proper display of both languages
- Database UTF-8 encoding
- Quiz/exam questions in Japanese
- PDF generation with Japanese characters
- Email notifications with Japanese text

## Database Configuration

### Ensure UTF-8 Encoding
- Database charset: `utf8mb4`
- Database collation: `utf8mb4_unicode_ci`
- Verify in `config/database.php`:
```php
'charset' => 'utf8mb4',
'collation' => 'utf8mb4_unicode_ci',
```

### Update Existing Migrations
- Ensure all string/text columns support UTF-8
- Use `utf8mb4` charset for text fields

## Implementation Steps

### 1. Database Configuration
File: `config/database.php`
- Set default charset to `utf8mb4`
- Set default collation to `utf8mb4_unicode_ci`
- Verify all connections use UTF-8

### 2. Application Configuration
File: `config/app.php`
- Set locale to `id` (Indonesian) as default
- Ensure UTF-8 support

### 3. Create Quiz/Exam Tables (if not exists)
File: `database/migrations/xxxx_create_quizzes_table.php`

Fields:
- `id` (bigInteger)
- `course_id` (foreign key)
- `chapter_id` (foreign key, nullable)
- `title` (string) - Support Japanese
- `description` (text) - Support Japanese
- `instructions` (text) - Support Japanese
- `duration_minutes` (integer)
- `is_active` (boolean)
- `created_at`, `updated_at`

File: `database/migrations/xxxx_create_quiz_questions_table.php`

Fields:
- `id` (bigInteger)
- `quiz_id` (foreign key)
- `question_text` (text) - Support Japanese
- `question_type` (enum: 'multiple_choice', 'true_false', 'short_answer', 'essay')
- `points` (integer)
- `order` (integer)
- `created_at`, `updated_at`

File: `database/migrations/xxxx_create_quiz_options_table.php`

Fields:
- `id` (bigInteger)
- `question_id` (foreign key)
- `option_text` (text) - Support Japanese
- `is_correct` (boolean)
- `order` (integer)
- `created_at`, `updated_at`

### 4. Create Quiz Models
File: `app/Models/Quiz.php`
- Support Japanese text in all fields

File: `app/Models/QuizQuestion.php`
- Support Japanese text in question_text

File: `app/Models/QuizOption.php`
- Support Japanese text in option_text

### 5. Update Form Views
- Ensure all text inputs accept Japanese characters
- Use proper character encoding in HTML:
```html
<meta charset="UTF-8">
```

### 6. Update Rich Text Editors
- Configure TinyMCE/CKEditor to support Japanese:
```javascript
language: 'id',
content_lang: 'id',
fontSize_sizes: '小さい/0.75em 小/0.875em 標準/1em 中/1.125em 大きい/1.25em 大/1.5em 特大/2em',
```

### 7. Update PDF Generation
- Use fonts that support Japanese (e.g., Noto Sans JP)
- Configure DomPDF for Japanese:
```php
$dompdf = new Dompdf([
    'fontDir' => storage_path('fonts'),
    'fontCache' => storage_path('fonts'),
]);
$dompdf->loadHtml($html, 'UTF-8');
```

### 8. Create Quiz Controller
File: `app/Http/Controllers/Instructor/QuizController.php`
- `create()` method:
  - Show quiz creation form
  - Support Japanese text input
- `store(Request $request)` method:
  - Validate and save quiz
  - Handle Japanese text
- `edit($id)` method:
  - Show edit form
  - Properly display Japanese text

### 9. Create Quiz Creation View
File: `resources/views/instructor/quizzes/create.blade.php`
- Form fields:
  - Title (accepts Japanese)
  - Description (accepts Japanese)
  - Instructions (accepts Japanese)
  - Questions section:
    - Question text (accepts Japanese)
    - Question type selector
    - Options (accepts Japanese)
    - Points input
- Japanese input method indicator (optional)

### 10. Create Quiz Taking View
File: `resources/views/student/quizzes/show.blade.php`
- Display questions with Japanese text
- Display options with Japanese text
- Proper rendering of Japanese characters

### 11. Update Email Templates
- Ensure emails support Japanese text
- Use UTF-8 encoding in email headers
- Test with Japanese content

### 12. Create Validation Rules
- Ensure validation messages can be in Indonesian
- Create custom validation for Japanese text (if needed)

### 13. Update Search Functionality
- Ensure search works with Japanese characters
- Use proper collation for search queries

### 14. Testing Japanese Text
- Test input: Hiragana (あいうえお), Katakana (アイウエオ), Kanji (漢字)
- Test display in all views
- Test PDF generation
- Test email sending
- Test database storage and retrieval

## Files to Create/Modify
- `config/database.php` (modify)
- `config/app.php` (modify)
- `database/migrations/xxxx_create_quizzes_table.php` (new)
- `database/migrations/xxxx_create_quiz_questions_table.php` (new)
- `database/migrations/xxxx_create_quiz_options_table.php` (new)
- `app/Models/Quiz.php` (new)
- `app/Models/QuizQuestion.php` (new)
- `app/Models/QuizOption.php` (new)
- `app/Http/Controllers/Instructor/QuizController.php` (new)
- `resources/views/instructor/quizzes/create.blade.php` (new)
- `resources/views/instructor/quizzes/edit.blade.php` (new)
- `resources/views/student/quizzes/show.blade.php` (new)
- `resources/views/layouts/app.blade.php` (modify - add charset meta)
- All form views (verify UTF-8 support)

## Dependencies
- UTF-8 compatible fonts for PDF generation
- Japanese font files (for PDFs)
- Proper text editor configuration

## Key Considerations

### Database
- Always use `utf8mb4` charset
- Use `utf8mb4_unicode_ci` collation
- Test with complex Japanese characters

### Forms
- Ensure proper encoding in HTML forms
- Use `accept-charset="UTF-8"` in forms
- Test Japanese input in all text fields

### Display
- Use proper CSS fonts that support Japanese
- Test rendering in different browsers
- Ensure proper line breaks for Japanese text

### PDF Generation
- Install Japanese fonts
- Configure PDF library for UTF-8
- Test PDF generation with Japanese content

### Email
- Set proper charset in email headers
- Test email delivery with Japanese content

## Testing Checklist
- [ ] Test Japanese text input in all forms
- [ ] Test Japanese text display in all views
- [ ] Test quiz creation with Japanese questions
- [ ] Test quiz taking with Japanese content
- [ ] Test PDF generation with Japanese text
- [ ] Test email with Japanese content
- [ ] Test database storage/retrieval of Japanese text
- [ ] Test search functionality with Japanese characters
- [ ] Test Indonesian text alongside Japanese text

