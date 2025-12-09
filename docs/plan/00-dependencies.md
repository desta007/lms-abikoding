# Plan Dependency Reference

## Overview
This document clarifies dependencies between plans to ensure proper implementation order.

## Implementation Order

### Phase 1: Foundation (Must be done first)
1. **Plan 01: Login** - Basic authentication
   - Dependency: Plan 02 (role field)
   - Optional: Can add role field separately before Plan 02

2. **Plan 02: Register** - User registration
   - Adds role field to users table
   - Creates UserProfile (if Plan 22 is implemented)

3. **Plan 15: Multilingual Support** - UTF-8 and Japanese text support
   - Should be configured early for proper text handling
   - Database charset configuration

4. **Plan 16: Responsive Layout** - Mobile-first design
   - Foundation for all views

### Phase 2: Core Course Features
5. **Plan 03: Home Page** - Course listing
   - Creates Courses, Categories, Levels tables
   - Foundation for course system

6. **Plan 05: Instructor Create Course** - Course creation
   - Creates Chapters and ChapterMaterials tables
   - Depends on Plan 03 (Categories, Levels)

7. **Plan 04: Instructor Dashboard** - Instructor statistics
   - Depends on Plan 03 (Courses)
   - Depends on Plan 05 (Chapters) for "Total Lessons" stat
   - Can be implemented partially without Plan 05

8. **Plan 06: Instructor Manage Courses** - Course management
   - Depends on Plan 05

9. **Plan 07: Instructor Comments** - Comment system
   - Depends on Plan 05 (Chapters)

### Phase 3: Student Features
10. **Plan 08: Student Course View** - Course detail page
    - Depends on Plan 03, 05, 04, 07
    - Note: Enrollment logic references Plan 19 for payments

11. **Plan 19: Course Enrollment Payment** - Payment for courses
    - Depends on Plan 08 (enrollment flow)
    - Integrates with Plan 14 (Payment model)

12. **Plan 09: Student Course Materials** - Material viewing
    - Depends on Plan 08, 19 (enrollment must be completed)
    - Requires enrollment gating

13. **Plan 21: Quiz and Exam System** - Quiz/exam functionality
    - Can be implemented independently
    - References Plan 15 for multilingual support

14. **Plan 20: Certificate Generation** - Completion certificates
    - Depends on Plan 09 (course completion)
    - Optional: Can require Plan 21 (quiz completion)

### Phase 4: Community Features
15. **Plan 10: Community SosialHub** - Social feed
    - Creates Posts, UserProfiles tables
    - Can reference Plan 22 for profile management

16. **Plan 11: Community Broadcast** - Learning achievements
    - Similar to Plan 10 but separate feature
    - Can share some components

17. **Plan 12: Community Calendar** - Event management
    - Independent feature

### Phase 5: Admin Features
18. **Plan 13: Admin Student Management** - Student data management
    - Depends on Plan 10 (for activity tracking)
    - Creates JobApplication, Company models

19. **Plan 14: Admin Payment** - Admin payment processing
    - Shares Payment model with Plan 19
    - Depends on Plan 13 (JobApplication)

### Phase 6: Supporting Features (Can be implemented anytime)
20. **Plan 17: Password Reset** - Password recovery
    - Independent feature
    - Uses Laravel Breeze default

21. **Plan 18: Email Notifications** - Email system
    - Can be integrated into any plan
    - Should be implemented early for better UX

22. **Plan 22: User Profile Management** - Profile editing
    - Depends on Plan 10 (UserProfile table)
    - Can be integrated earlier if UserProfile is created in Plan 02

## Critical Dependencies

### Role Field
- **Plan 01** needs `role` field
- **Plan 02** adds `role` field
- **Solution**: Either add role field before Plan 01, or implement Plan 02 first, or add role field as part of Plan 02 migration

### Course System
- **Plan 03** creates Courses table
- **Plan 05** creates Chapters (requires Courses)
- **Plan 08** displays courses (requires Plan 03, 05)
- **Plan 09** accesses materials (requires Plan 08)

### Payment System
- **Plan 14** creates Payment model for admin payments
- **Plan 19** uses Payment model for course payments
- **Shared**: Both use same Payment table with different `payment_type` values

### Enrollment Flow
- **Plan 08** handles enrollment UI
- **Plan 19** handles enrollment payment
- **Plan 09** requires completed enrollment
- **Order**: Plan 08 → Plan 19 → Plan 09

## Recommended Implementation Sequence

### Week 1: Foundation
1. Plan 15 (Multilingual setup)
2. Plan 16 (Layout system)
3. Plan 02 (Register with role field)
4. Plan 01 (Login)

### Week 2: Core Course Features
5. Plan 03 (Home page)
6. Plan 05 (Course creation)
7. Plan 04 (Instructor dashboard)

### Week 3: Student Features
8. Plan 08 (Course view)
9. Plan 19 (Payment integration)
10. Plan 09 (Material access)

### Week 4: Supporting Features
11. Plan 18 (Email notifications)
12. Plan 17 (Password reset)
13. Plan 22 (Profile management)

### Week 5: Advanced Features
14. Plan 21 (Quiz system)
15. Plan 20 (Certificates)

### Week 6: Community & Admin
16. Plan 10, 11, 12 (Community features)
17. Plan 13, 14 (Admin features)

## Notes
- Plans can be implemented in parallel if they don't have dependencies
- Some features (like Plan 18 Email Notifications) can be added incrementally
- Database migrations should be run in order to avoid foreign key errors
- Test database relationships after each phase

