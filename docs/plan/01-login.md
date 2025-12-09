# Plan 01: Login Feature

## Overview
Implement user login functionality using Laravel Breeze with email and password authentication.

## Requirements
- Input fields: Email and Password (Kata Sandi)
- Session-based authentication
- Remember me functionality
- Redirect after successful login based on user role (Student, Instructor, Admin)

## Database Changes
- No changes needed - uses existing `users` table
- **Note**: The `role` field will be added in Plan 02 (Register Feature). This plan assumes the role field exists or will be added as part of the registration implementation.

## Dependencies
- **Plan 02 (Register)**: The `role` field must be added to the users table. This can be done either:
  1. Before implementing login (if you want to add role field separately)
  2. As part of Plan 02 implementation (recommended)

## Implementation Steps

### 1. Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

### 2. Update User Migration (if role field doesn't exist yet)
**Note**: If implementing Plan 02 first, skip this step as role field will be added there.

Add `role` field to users table:
- `role` enum: 'student', 'instructor', 'admin'
- Default: 'student'

### 3. Update User Model
File: `app/Models/User.php`
- Add `role` to fillable array
- Add role casting

### 4. Customize Login View
File: `resources/views/auth/login.blade.php`
- Customize to match LMS design
- Ensure Indonesian language support ("Kata Sandi" for password)
- Add validation error display

### 5. Implement Role-Based Redirect
File: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Override `authenticated` method
- Redirect based on role:
  - Student → `/` (home page)
  - Instructor → `/instructor/dashboard`
  - Admin → `/admin/dashboard`

### 6. Create Middleware for Role Checks
File: `app/Http/Middleware/EnsureUserHasRole.php`
- Check user role for protected routes
- Register in `bootstrap/app.php`

### 7. Protect Routes
File: `routes/web.php`
- Add authentication middleware
- Define role-based route groups

## Files to Create/Modify
- `database/migrations/xxxx_add_role_to_users_table.php` (new)
- `app/Models/User.php` (modify)
- `app/Http/Middleware/EnsureUserHasRole.php` (new)
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (modify)
- `resources/views/auth/login.blade.php` (modify via Breeze)
- `routes/web.php` (modify)
- `bootstrap/app.php` (modify)

## Dependencies
- Laravel Breeze package
- Laravel authentication scaffolding
- Session driver configured

## Testing Considerations
- Test login with valid credentials
- Test login with invalid credentials
- Test remember me functionality
- Test role-based redirects
- Test protected routes access

