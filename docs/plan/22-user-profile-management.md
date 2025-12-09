# Plan 22: User Profile Management

## Overview
Allow users (students, instructors, admins) to manage their profiles, including personal information, preferences, and account settings.

## Requirements
- Edit profile information (name, email, phone, bio)
- Upload profile picture/avatar
- Upload cover photo (for students/instructors)
- Change password
- Manage notification preferences
- Manage privacy settings
- View account activity
- Delete account (optional)

## Database Changes

### Update User Profiles Table (from Plan 10)
File: `database/migrations/xxxx_update_user_profiles_table.php`

Fields to add/modify:
- `bio` (text, nullable)
- `avatar` (string, nullable)
- `cover_photo` (string, nullable)
- `location` (string, nullable)
- `website` (string, nullable)
- `date_of_birth` (date, nullable)
- `gender` (enum: 'male', 'female', 'other', nullable)
- `privacy_show_email` (boolean, default: false)
- `privacy_show_phone` (boolean, default: false)
- `privacy_show_location` (boolean, default: true)
- `timezone` (string, default: 'Asia/Jakarta')
- `language_preference` (enum: 'id', 'en', 'ja', default: 'id')
- `updated_at`

### Create User Password History Table (for security)
File: `database/migrations/xxxx_create_password_history_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key)
- `password_hash` (string)
- `created_at`, `updated_at`

## Models to Create/Modify

### Update UserProfile Model (from Plan 10)
File: `app/Models/UserProfile.php`
- Add accessors: avatarUrl(), coverPhotoUrl()
- Add methods: updateAvatar(), updateCoverPhoto()

### PasswordHistory Model
File: `app/Models/PasswordHistory.php`
- Relationships: belongsTo(User)
- Methods: addPassword(), canReusePassword()

## Implementation Steps

### 1. Create Profile Controller
File: `app/Http/Controllers/ProfileController.php`
- `show()` method: Show profile page
- `edit()` method: Show edit form
- `update(Request $request)` method: Update profile
- `updateAvatar(Request $request)` method: Update avatar
- `updateCoverPhoto(Request $request)` method: Update cover photo
- `updatePassword(Request $request)` method: Change password
- `destroy()` method: Delete account (optional)

### 2. Create Profile Settings Controller
File: `app/Http/Controllers/ProfileSettingsController.php`
- `index()` method: Show settings page
- `updateNotifications(Request $request)` method: Update notification preferences
- `updatePrivacy(Request $request)` method: Update privacy settings
- `updatePreferences(Request $request)` method: Update language/timezone

### 3. Create Profile View
File: `resources/views/profile/show.blade.php`
- Display user information
- Profile picture
- Cover photo
- Bio
- Location
- Website
- Social links (if applicable)
- Edit button (for own profile)

### 4. Create Profile Edit View
File: `resources/views/profile/edit.blade.php`
- Form sections:
  - Personal Information:
    - First name
    - Last name
    - Email
    - WhatsApp number
    - Date of birth
    - Gender
    - Location
    - Website
    - Bio (textarea)
  - Profile Pictures:
    - Avatar upload (with preview)
    - Cover photo upload (with preview)
  - Privacy Settings:
    - Show email toggle
    - Show phone toggle
    - Show location toggle
  - Language & Timezone:
    - Language preference dropdown
    - Timezone dropdown
  - Save button

### 5. Create Profile Settings View
File: `resources/views/profile/settings.blade.php`
- Tabs:
  - General (profile info)
  - Notifications (from Plan 18)
  - Privacy
  - Security (password change)
  - Account (delete account)

### 6. Create Change Password View
File: `resources/views/profile/change-password.blade.php`
- Current password input
- New password input
- Confirm password input
- Password strength indicator
- Save button

### 7. Create Avatar Upload Component
File: `resources/views/components/avatar-upload.blade.php`
- Image preview
- Upload button
- Crop functionality (optional)
- Remove button

### 8. Create Image Upload Service
File: `app/Services/ImageUploadService.php`
- `uploadAvatar($file, User $user)` method:
  - Validate image
  - Resize to 200x200px
  - Store in storage/app/public/avatars/
  - Delete old avatar if exists
  - Return file path
- `uploadCoverPhoto($file, User $user)` method:
  - Validate image
  - Resize to 1200x400px
  - Store in storage/app/public/covers/
  - Delete old cover if exists
  - Return file path

### 9. Create Password Validation Rule
File: `app/Rules/Password.php`
- Check password strength
- Check password history (prevent reuse)
- Minimum 8 characters
- At least one uppercase, lowercase, number

### 10. Update User Model
File: `app/Models/User.php`
- Add accessor: fullName()
- Add relationship: hasOne(UserProfile)
- Add method: updatePassword($password)

### 11. Create Profile Routes
File: `routes/web.php`
- `GET /profile` → ProfileController@show
- `GET /profile/edit` → ProfileController@edit
- `PUT /profile` → ProfileController@update
- `POST /profile/avatar` → ProfileController@updateAvatar
- `POST /profile/cover-photo` → ProfileController@updateCoverPhoto
- `PUT /profile/password` → ProfileController@updatePassword
- `GET /profile/settings` → ProfileSettingsController@index
- `PUT /profile/settings/notifications` → ProfileSettingsController@updateNotifications
- `PUT /profile/settings/privacy` → ProfileSettingsController@updatePrivacy
- `DELETE /profile` → ProfileController@destroy (optional)

### 12. Create Profile Navigation Component
File: `resources/views/components/profile-nav.blade.php`
- Profile menu items
- Active state indication

### 13. Update Registration Flow
File: `app/Http/Controllers/Auth/RegisteredUserController.php` (from Plan 02)
- Create UserProfile record after registration
- Set default preferences

## Files to Create/Modify
- `database/migrations/xxxx_update_user_profiles_table.php` (new)
- `database/migrations/xxxx_create_password_history_table.php` (new)
- `app/Models/UserProfile.php` (modify - from Plan 10)
- `app/Models/PasswordHistory.php` (new)
- `app/Models/User.php` (modify)
- `app/Http/Controllers/ProfileController.php` (new)
- `app/Http/Controllers/ProfileSettingsController.php` (new)
- `app/Services/ImageUploadService.php` (new)
- `app/Rules/Password.php` (new)
- `resources/views/profile/show.blade.php` (new)
- `resources/views/profile/edit.blade.php` (new)
- `resources/views/profile/settings.blade.php` (new)
- `resources/views/profile/change-password.blade.php` (new)
- `resources/views/components/avatar-upload.blade.php` (new)
- `resources/views/components/profile-nav.blade.php` (new)
- `app/Http/Controllers/Auth/RegisteredUserController.php` (modify - from Plan 02)
- `routes/web.php` (modify)

## Dependencies
- Intervention Image or similar (for image processing)
- Laravel Storage
- Plan 18: Notification preferences integration

## Image Upload Validation
```php
'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=200,min_height=200',
'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120|dimensions:min_width=1200,min_height=400',
```

## Password Change Validation
```php
'current_password' => 'required|current_password',
'password' => ['required', 'string', 'min:8', 'confirmed', new Password],
```

## Profile Update Validation
```php
'first_name' => 'required|string|max:255',
'last_name' => 'required|string|max:255',
'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
'whatsapp_number' => 'required|string|unique:users,whatsapp_number,' . auth()->id(),
'bio' => 'nullable|string|max:1000',
'location' => 'nullable|string|max:255',
'website' => 'nullable|url|max:255',
'date_of_birth' => 'nullable|date|before:today',
'gender' => 'nullable|in:male,female,other',
```

## Testing Considerations
- Test profile update
- Test avatar upload
- Test cover photo upload
- Test password change
- Test notification preferences update
- Test privacy settings
- Test image validation
- Test password strength validation
- Test duplicate email prevention
- Test file deletion when updating images

## Integration with Other Plans
- Plan 02: Create profile on registration
- Plan 10: SosialHub profile display
- Plan 18: Notification preferences
- Plan 15: Language preference for multilingual support

## Security Considerations
- Validate file uploads
- Resize images to prevent large uploads
- Secure file storage paths
- Prevent password reuse
- Validate email uniqueness
- Rate limit profile updates

## Privacy Settings
- `show_email`: Show email on public profile
- `show_phone`: Show phone on public profile
- `show_location`: Show location on public profile

## Language Preferences
- Indonesian (id) - default
- English (en)
- Japanese (ja)

