# Plan 02: Register Feature

## Overview
Implement user registration with email, first name, last name, password confirmation, and WhatsApp number.

## Requirements
- Email (unique, required)
- First Name (Nama Depan) - required
- Last Name (Nama Belakang) - required
- Password (Kata Sandi) - required, min 8 characters
- Password Confirmation (Konfirmasi Kata Sandi) - required, must match
- WhatsApp Number (Nomor WhatsApp) - required
- Role assignment (default: student)

## Role Assignment Process
- **Default Role**: All new registrations are automatically assigned the 'student' role
- **Becoming an Instructor**: Users cannot self-register as instructors. To become an instructor:
  1. Register as a student (default)
  2. Contact an administrator
  3. Admin will assign the instructor role via the User Management panel (see Plan 23)
- **Admin Role**: Admin roles are assigned manually by existing admins only

## Database Changes

### New Migration: Add Fields to Users Table
File: `database/migrations/xxxx_add_registration_fields_to_users_table.php`

Fields to add:
- `first_name` (string, nullable initially, then required)
- `last_name` (string, nullable initially, then required)
- `whatsapp_number` (string, nullable initially, then required)
- `role` (enum: 'student', 'instructor', 'admin', default: 'student')
- `phone_verified_at` (timestamp, nullable)

## Implementation Steps

### 1. Create Migration
```bash
php artisan make:migration add_registration_fields_to_users_table
```

### 2. Update User Model
File: `app/Models/User.php`
- Add to fillable: `first_name`, `last_name`, `whatsapp_number`, `role`
- Add accessor for `full_name` (combines first_name + last_name)
- Add method to check if user is instructor/admin

### 3. Update Registration Controller
File: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify `store` method to handle new fields
- Add validation rules:
  - `first_name`: required, string, max:255
  - `last_name`: required, string, max:255
  - `whatsapp_number`: required, string, unique:users (or regex validation)
  - `password`: required, confirmed, min:8
- Hash password before saving
- Set default role to 'student'

### 4. Create Registration View
File: `resources/views/auth/register.blade.php`
- Form fields:
  - Email input
  - First Name input (Nama Depan)
  - Last Name input (Nama Belakang)
  - Password input (Kata Sandi)
  - Password Confirmation input (Konfirmasi Kata Sandi)
  - WhatsApp Number input (Nomor WhatsApp)
- Add validation error display
- Indonesian language labels
- Add client-side validation (optional)

### 5. Update UserFactory
File: `database/factories/UserFactory.php`
- Update to include new fields with fake data

### 6. Add Validation Rules
File: `app/Rules/WhatsAppNumber.php` (optional)
- Custom validation rule for WhatsApp number format
- Ensure proper format (e.g., +62xxxxxxxxxxx or 08xxxxxxxxxx)

### 7. Email Verification (Optional)
- Can be implemented later if needed
- Use Laravel's built-in email verification

## Files to Create/Modify
- `database/migrations/xxxx_add_registration_fields_to_users_table.php` (new)
- `app/Models/User.php` (modify)
- `app/Http/Controllers/Auth/RegisteredUserController.php` (modify)
- `resources/views/auth/register.blade.php` (modify via Breeze)
- `database/factories/UserFactory.php` (modify)
- `app/Rules/WhatsAppNumber.php` (new, optional)

## Dependencies
- Laravel Breeze authentication (from plan 01)
- Database migration system

## Validation Rules
```php
'email' => 'required|string|email|max:255|unique:users',
'first_name' => 'required|string|max:255',
'last_name' => 'required|string|max:255',
'whatsapp_number' => 'required|string|unique:users|regex:/^(\+62|62|0)[0-9]{9,13}$/',
'password' => 'required|string|min:8|confirmed',
```

## Testing Considerations
- Test registration with all valid fields
- Test validation for each field
- Test unique email constraint
- Test unique WhatsApp number constraint
- Test password confirmation matching
- Test default role assignment (should always be 'student')
- Test redirect after registration
- Verify that users cannot register with instructor/admin roles directly

## Related Features
- **Plan 23: Admin User Management** - Admins can assign instructor/admin roles to users after registration
- Users must register as students first, then request or be assigned instructor role by admin

