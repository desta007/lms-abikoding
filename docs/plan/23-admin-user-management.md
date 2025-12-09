# Plan 23: Admin User Management

## Overview
Allow administrators to manage all users in the system, including the ability to view, edit, and assign roles (student, instructor, admin) to users.

## Requirements
- View all users (students, instructors, admins)
- Filter users by role
- Search users by name, email
- View user details
- Edit user information
- Assign/change user roles (e.g., assign student as instructor)
- Suspend/activate users
- View user statistics (enrollments, courses, posts, etc.)

## Database Changes
- No new migrations needed - uses existing `users` table with `role` field

## Implementation Steps

### 1. Create User Management Controller
File: `app/Http/Controllers/Admin/UserController.php`
- `index()` method:
  - List all users (students, instructors, admins)
  - Filter by role
  - Search by name, email
  - Support pagination
- `show($id)` method:
  - Display full user profile
  - Show role-specific statistics
  - Show activities based on role
- `edit($id)` method:
  - Show edit form with role selection
- `update(Request $request, $id)` method:
  - Update user information
  - Update user role (with validation)
  - Prevent admin from removing last admin
- `updateRole(Request $request, $id)` method:
  - Specifically for role assignment
  - Validate role changes
  - Log role changes
- `destroy($id)` method:
  - Delete user (with safety checks)

### 2. Create Routes
File: `routes/web.php`
- `GET /admin/users` → Admin\UserController@index
- `GET /admin/users/{id}` → Admin\UserController@show
- `GET /admin/users/{id}/edit` → Admin\UserController@edit
- `PUT /admin/users/{id}` → Admin\UserController@update
- `POST /admin/users/{id}/role` → Admin\UserController@updateRole
- `DELETE /admin/users/{id}` → Admin\UserController@destroy

### 3. Create User List View
File: `resources/views/admin/users/index.blade.php`
- Search and filter panel
- User table:
  - Avatar/photo
  - Name
  - Email
  - Role (with badge)
  - Registration date
  - Status
  - Actions: View, Edit, Change Role
- Pagination
- Role filter tabs/buttons

### 4. Create User Detail View
File: `resources/views/admin/users/show.blade.php`
- User information card
- Role badge with change role button
- Statistics based on role:
  - Students: enrollments, completed courses, posts
  - Instructors: courses created, students taught, ratings
  - Admins: actions performed
- Activity timeline
- Quick actions: Edit, Change Role, Suspend

### 5. Create User Edit View
File: `resources/views/admin/users/edit.blade.php`
- Form sections:
  - Personal Information:
    - First name
    - Last name
    - Email
    - WhatsApp number
  - Role Assignment:
    - Role dropdown (student, instructor, admin)
    - Warning message for role changes
  - Account Settings:
    - Password reset (optional)
    - Email verification status
- Save and Cancel buttons

### 6. Create Change Role Modal/Component
File: `resources/views/admin/users/partials/change-role-modal.blade.php`
- Role selection dropdown
- Confirmation message
- Warning about role change effects

### 7. Update Admin Dashboard
File: `resources/views/admin/dashboard.blade.php`
- Add link to User Management
- Show user statistics by role

### 8. Add Role Change Validation
- Prevent removing last admin
- Validate role transitions
- Log role changes for audit

## Files to Create/Modify
- `app/Http/Controllers/Admin/UserController.php` (new)
- `resources/views/admin/users/index.blade.php` (new)
- `resources/views/admin/users/show.blade.php` (new)
- `resources/views/admin/users/edit.blade.php` (new)
- `resources/views/admin/users/partials/change-role-modal.blade.php` (new)
- `routes/web.php` (modify)
- `resources/views/admin/dashboard.blade.php` (modify)

## Role Assignment Rules
- Only admins can assign roles
- Cannot remove the last admin user
- When changing a user to instructor:
  - User gains access to instructor dashboard
  - User can create and manage courses
- When changing a user from instructor to student:
  - User loses instructor access
  - Existing courses remain but may need reassignment
- When changing a user to admin:
  - User gains full admin access
  - Should be done carefully

## Security Considerations
- Only admins can access user management
- Validate role changes
- Log all role changes for audit trail
- Prevent self-role removal (admin cannot remove their own admin role)
- Confirm destructive actions

## Testing Considerations
- Test admin access control
- Test role assignment
- Test role change validation
- Test search and filtering
- Test pagination
- Test user deletion with safety checks
- Test preventing last admin removal

## Integration with Other Plans
- Plan 13: Admin Student Management (can be extended or merged)
- Plan 22: User Profile Management (uses profile data)

## Notes
- This feature extends the existing student management to cover all user types
- Role assignment is a critical feature that should be used carefully
- Consider adding an audit log for role changes in the future

