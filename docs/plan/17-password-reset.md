# Plan 17: Password Reset Feature

## Overview
Implement password reset functionality allowing users to reset their forgotten passwords via email link.

## Requirements
- Forgot password form (email input)
- Password reset email with secure token
- Reset password form (token, email, new password, confirmation)
- Token expiration (default: 60 minutes)
- One-time use token
- Secure password validation

## Database Changes

### Update Users Table (if not exists)
- Uses existing `password_reset_tokens` table (Laravel Breeze default)
- Token storage handled by Laravel

## Implementation Steps

### 1. Verify Laravel Breeze Setup
- Laravel Breeze includes password reset by default
- Verify routes are registered
- Verify views exist

### 2. Customize Password Reset Views
File: `resources/views/auth/forgot-password.blade.php`
- Customize design to match LMS theme
- Indonesian language labels ("Lupa Kata Sandi")
- Email input field
- Back to login link
- Validation error display

File: `resources/views/auth/reset-password.blade.php`
- Customize design to match LMS theme
- Indonesian language labels:
  - "Reset Kata Sandi"
  - "Email"
  - "Token" (hidden, auto-filled)
  - "Kata Sandi Baru"
  - "Konfirmasi Kata Sandi Baru"
- Password strength indicator (optional)
- Validation error display
- Submit button

### 3. Create Password Reset Controller (if custom needed)
File: `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `store()` method: Send reset link
- Validate email exists
- Send reset email

File: `app/Http/Controllers/Auth/NewPasswordController.php`
- `store()` method: Reset password
- Validate token
- Validate password requirements
- Update user password
- Invalidate token

### 4. Update Routes
File: `routes/web.php`
- `GET /forgot-password` → PasswordResetLinkController@create
- `POST /forgot-password` → PasswordResetLinkController@store
- `GET /reset-password/{token}` → NewPasswordController@create
- `POST /reset-password` → NewPasswordController@store

### 5. Customize Email Template
File: `resources/views/emails/reset-password.blade.php`
- Indonesian language email
- Include reset link with token
- Expiration notice
- Security warning

### 6. Configure Email Settings
File: `.env`
- Configure SMTP or mail service
- Set app name and from address

### 7. Add Password Validation Rules
File: `app/Rules/Password.php` (optional - custom rule)
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character (optional)

## Files to Create/Modify
- `resources/views/auth/forgot-password.blade.php` (modify via Breeze)
- `resources/views/auth/reset-password.blade.php` (modify via Breeze)
- `resources/views/emails/reset-password.blade.php` (modify via Breeze)
- `app/Http/Controllers/Auth/PasswordResetLinkController.php` (modify if needed)
- `app/Http/Controllers/Auth/NewPasswordController.php` (modify if needed)
- `routes/web.php` (verify routes exist)
- `.env` (configure email settings)

## Dependencies
- Laravel Breeze (includes password reset)
- Email service configured (SMTP, Mailgun, etc.)
- Queue system (for async email sending, optional)

## Security Considerations
- Tokens expire after 60 minutes (configurable)
- Tokens are one-time use only
- Tokens are hashed in database
- Rate limiting on reset requests (prevent abuse)
- Validate email exists before sending (security best practice)

## Email Configuration Example
```php
// config/mail.php
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lms-eong.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Testing Considerations
- Test forgot password form submission
- Test email delivery
- Test reset link with valid token
- Test reset link with expired token
- Test reset link with invalid token
- Test password validation
- Test rate limiting
- Test token one-time use
- Test redirect after successful reset

## Password Reset Flow
1. User clicks "Lupa Kata Sandi" link
2. User enters email address
3. System validates email exists
4. System generates reset token
5. System sends email with reset link
6. User clicks link in email
7. User enters new password and confirmation
8. System validates token and password
9. System updates password
10. System invalidates token
11. User redirected to login with success message

