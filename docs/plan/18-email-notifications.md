# Plan 18: Email Notification System

## Overview
Implement comprehensive email notification system for various events across the LMS (registration, enrollment, comments, payments, etc.).

## Requirements
- Send emails for key events:
  - Welcome email after registration
  - Course enrollment confirmation
  - Payment receipts
  - Comment replies
  - Course completion certificates
  - Password reset (covered in Plan 17)
  - Event reminders (calendar)
  - Job application status updates
- Email templates in Indonesian
- Queue support for async sending
- Email preferences (opt-out per user)

## Database Changes

### Create Email Notifications Table
File: `database/migrations/xxxx_create_email_notifications_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key, nullable)
- `email` (string) - recipient email
- `subject` (string)
- `body` (text)
- `type` (enum: 'welcome', 'enrollment', 'payment', 'comment_reply', 'certificate', 'event_reminder', 'job_application', 'password_reset')
- `status` (enum: 'pending', 'sent', 'failed')
- `sent_at` (timestamp, nullable)
- `error_message` (text, nullable)
- `metadata` (json, nullable) - store additional data
- `created_at`, `updated_at`

### Create User Notification Preferences Table
File: `database/migrations/xxxx_create_notification_preferences_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key, unique)
- `email_welcome` (boolean, default: true)
- `email_enrollment` (boolean, default: true)
- `email_payment` (boolean, default: true)
- `email_comment_reply` (boolean, default: true)
- `email_certificate` (boolean, default: true)
- `email_event_reminder` (boolean, default: true)
- `email_job_application` (boolean, default: true)
- `created_at`, `updated_at`

## Models to Create

### EmailNotification Model
File: `app/Models/EmailNotification.php`
- Relationships: belongsTo(User)
- Scopes: pending(), sent(), failed(), byType()
- Methods: markAsSent(), markAsFailed()

### NotificationPreference Model
File: `app/Models/NotificationPreference.php`
- Relationships: belongsTo(User)
- Methods: isEnabled($type), enable($type), disable($type)

## Implementation Steps

### 1. Configure Email Service
File: `.env`
- Set up SMTP or mail service
- Configure queue driver (database, redis, etc.)

### 2. Create Notification Classes
File: `app/Notifications/WelcomeNotification.php`
- Send welcome email after registration

File: `app/Notifications/EnrollmentNotification.php`
- Send enrollment confirmation

File: `app/Notifications/PaymentReceiptNotification.php`
- Send payment receipt

File: `app/Notifications/CommentReplyNotification.php`
- Send comment reply notification

File: `app/Notifications/CourseCompletionNotification.php`
- Send certificate notification

File: `app/Notifications/EventReminderNotification.php`
- Send event reminder

File: `app/Notifications/JobApplicationStatusNotification.php`
- Send job application status update

### 3. Create Email Templates
File: `resources/views/emails/welcome.blade.php`
- Welcome message in Indonesian
- Include app name and user name
- Call-to-action buttons

File: `resources/views/emails/enrollment.blade.php`
- Course enrollment confirmation
- Course details
- Access link

File: `resources/views/emails/payment-receipt.blade.php`
- Payment details
- Invoice download link
- Payment summary

File: `resources/views/emails/comment-reply.blade.php`
- Comment reply notification
- Link to comment thread

File: `resources/views/emails/certificate.blade.php`
- Course completion congratulations
- Certificate download link

File: `resources/views/emails/event-reminder.blade.php`
- Event details
- Date and time
- Location

File: `resources/views/emails/job-application-status.blade.php`
- Application status update
- Next steps if applicable

File: `resources/views/emails/layout.blade.php`
- Base email layout
- Header with logo
- Footer with unsubscribe link
- Responsive design

### 4. Create Notification Service
File: `app/Services/NotificationService.php`
- `sendWelcomeEmail(User $user)` method
- `sendEnrollmentEmail(User $user, Course $course)` method
- `sendPaymentReceipt(User $user, Payment $payment)` method
- `sendCommentReply(User $user, Comment $comment)` method
- `sendCourseCompletion(User $user, Course $course)` method
- `sendEventReminder(User $user, Event $event)` method
- `sendJobApplicationStatus(User $user, JobApplication $application)` method
- Check user preferences before sending

### 5. Integrate with Existing Features

#### Registration (Plan 02)
File: `app/Http/Controllers/Auth/RegisteredUserController.php`
- After user registration, send welcome email

#### Course Enrollment (Plan 08)
File: `app/Http/Controllers/CourseEnrollmentController.php`
- After enrollment, send enrollment confirmation email

#### Payment (Plan 14)
File: `app/Services/PaymentService.php`
- After successful payment, send payment receipt email

#### Comments (Plan 07)
File: `app/Http/Controllers/CommentController.php`
- After comment reply, send notification to original commenter

#### Course Completion (Plan 09)
File: `app/Http/Controllers/CourseContentController.php`
- After course completion, send certificate email

#### Calendar Events (Plan 12)
File: `app/Http/Controllers/Community/EventController.php`
- Send reminder emails before event (use Laravel scheduler)

#### Job Applications (Plan 13)
File: `app/Http/Controllers/Admin/JobApplicationController.php`
- After status update, send notification email

### 6. Create Notification Preferences Controller
File: `app/Http/Controllers/NotificationPreferenceController.php`
- `index()` method: Show preferences form
- `update(Request $request)` method: Update preferences

### 7. Create Notification Preferences View
File: `resources/views/settings/notifications.blade.php`
- Toggle switches for each notification type
- Save button
- Indonesian labels

### 8. Create Queue Tables (if using database queue)
```bash
php artisan queue:table
php artisan migrate
```

### 9. Configure Queue Worker
- Set up supervisor or systemd service
- Run queue worker: `php artisan queue:work`

### 10. Create Email Test Command
File: `app/Console/Commands/TestEmailCommand.php`
- Test email sending functionality
- Useful for debugging

## Files to Create/Modify
- `database/migrations/xxxx_create_email_notifications_table.php` (new)
- `database/migrations/xxxx_create_notification_preferences_table.php` (new)
- `app/Models/EmailNotification.php` (new)
- `app/Models/NotificationPreference.php` (new)
- `app/Notifications/WelcomeNotification.php` (new)
- `app/Notifications/EnrollmentNotification.php` (new)
- `app/Notifications/PaymentReceiptNotification.php` (new)
- `app/Notifications/CommentReplyNotification.php` (new)
- `app/Notifications/CourseCompletionNotification.php` (new)
- `app/Notifications/EventReminderNotification.php` (new)
- `app/Notifications/JobApplicationStatusNotification.php` (new)
- `app/Services/NotificationService.php` (new)
- `app/Http/Controllers/NotificationPreferenceController.php` (new)
- `resources/views/emails/layout.blade.php` (new)
- `resources/views/emails/welcome.blade.php` (new)
- `resources/views/emails/enrollment.blade.php` (new)
- `resources/views/emails/payment-receipt.blade.php` (new)
- `resources/views/emails/comment-reply.blade.php` (new)
- `resources/views/emails/certificate.blade.php` (new)
- `resources/views/emails/event-reminder.blade.php` (new)
- `resources/views/emails/job-application-status.blade.php` (new)
- `resources/views/settings/notifications.blade.php` (new)
- `app/Http/Controllers/Auth/RegisteredUserController.php` (modify)
- `app/Http/Controllers/CourseEnrollmentController.php` (modify)
- `app/Services/PaymentService.php` (modify)
- `app/Http/Controllers/CommentController.php` (modify)
- `routes/web.php` (modify)

## Dependencies
- Laravel Notifications
- Laravel Queue (for async sending)
- Email service (SMTP, Mailgun, SendGrid, etc.)
- Queue driver (database, redis, etc.)

## Email Template Structure
```blade
{{-- resources/views/emails/layout.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header>
        <img src="{{ asset('logo.png') }}" alt="LMS Logo">
    </header>
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        <p>© {{ date('Y') }} LMS Eong. All rights reserved.</p>
        <p><a href="{{ route('settings.notifications') }}">Kelola preferensi email</a></p>
    </footer>
</body>
</html>
```

## Queue Configuration
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),

// .env
QUEUE_CONNECTION=database
```

## Testing Considerations
- Test each notification type
- Test email delivery
- Test queue processing
- Test user preferences (opt-out)
- Test email templates rendering
- Test with invalid email addresses
- Test bulk sending performance
- Test unsubscribe functionality

## Notification Types Reference
- `welcome`: Sent after user registration
- `enrollment`: Sent after course enrollment
- `payment`: Sent after successful payment
- `comment_reply`: Sent when user receives reply to comment
- `certificate`: Sent when course is completed
- `event_reminder`: Sent before calendar event
- `job_application`: Sent when job application status changes
- `password_reset`: Sent for password reset (handled by Plan 17)

