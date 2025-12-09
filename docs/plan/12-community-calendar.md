# Plan 12: Community Calendar

## Overview
Create calendar system for scheduling and managing events (classes, meetings, exams, etc.).

## Requirements
- Create events with:
  - Event icon selection
  - Date
  - Time
  - Event description
  - Event color
  - Staff/Organizer
  - Event title
  - Location
  - Participants (multiple users)
  - Duration
  - Event banner/image
- Calendar view (month, week, day)
- Event list view
- Event details view

## Database Changes

### Create Events Table
File: `database/migrations/xxxx_create_events_table.php`

Fields:
- `id` (bigInteger)
- `title` (string)
- `description` (text, nullable)
- `icon` (string, nullable) - icon name/class
- `color` (string, default: '#3b82f6') - hex color code
- `start_date` (timestamp)
- `end_date` (timestamp)
- `duration_minutes` (integer, nullable) - calculated from start/end
- `location` (string, nullable)
- `organizer_id` (foreign key to users) - staff/organizer
- `banner` (string, nullable) - banner image path
- `event_type` (enum: 'class', 'meeting', 'exam', 'workshop', 'other')
- `is_public` (boolean, default: true)
- `max_participants` (integer, nullable)
- `created_at`, `updated_at`

### Create Event Participants Table
File: `database/migrations/xxxx_create_event_participants_table.php`

Fields:
- `id` (bigInteger)
- `event_id` (foreign key)
- `user_id` (foreign key)
- `status` (enum: 'pending', 'accepted', 'declined', 'attended')
- `registered_at` (timestamp)
- `created_at`, `updated_at`
- Unique constraint: event_id + user_id

### Create Event Reminders Table (Optional)
File: `database/migrations/xxxx_create_event_reminders_table.php`

Fields:
- `id` (bigInteger)
- `event_id` (foreign key)
- `user_id` (foreign key)
- `reminder_time` (timestamp) - when to send reminder
- `reminder_sent` (boolean, default: false)
- `created_at`, `updated_at`

## Models to Create

### Event Model
File: `app/Models/Event.php`
- Relationships: belongsTo(User as 'organizer'), hasMany(EventParticipant, EventReminder)
- Scopes: upcoming(), past(), byDate(), public()

### EventParticipant Model
File: `app/Models/EventParticipant.php`
- Relationships: belongsTo(Event, User)

### EventReminder Model (Optional)
File: `app/Models/EventReminder.php`
- Relationships: belongsTo(Event, User)

## Implementation Steps

### 1. Create Migrations
```bash
php artisan make:migration create_events_table
php artisan make:migration create_event_participants_table
php artisan make:migration create_event_reminders_table
```

### 2. Create Models
```bash
php artisan make:model Event
php artisan make:model EventParticipant
php artisan make:model EventReminder
```

### 3. Create Event Controller
File: `app/Http/Controllers/Community/EventController.php`
- `index()` method:
  - Display calendar view
  - Support month/week/day views
  - Filter events
- `create()` method:
  - Show event creation form
- `store(Request $request)` method:
  - Validate and create event
  - Handle banner upload
  - Add organizer as participant
  - Create reminders if set
- `show($id)` method:
  - Display event details
  - Show participants list
- `edit($id)` method:
  - Show edit form
- `update(Request $request, $id)` method:
  - Update event
- `destroy($id)` method:
  - Delete event
- `register(Request $request, $id)` method:
  - Register user for event
- `unregister(Request $request, $id)` method:
  - Unregister user from event

### 4. Create Event API Controller (for calendar data)
File: `app/Http/Controllers/API/EventController.php`
- `index()` method:
  - Return events as JSON
  - Filter by date range
  - Format for calendar libraries

### 5. Create Routes
File: `routes/web.php`
- `GET /community/calendar` → EventController@index
- `GET /community/events/create` → EventController@create
- `POST /community/events` → EventController@store
- `GET /community/events/{id}` → EventController@show
- `GET /community/events/{id}/edit` → EventController@edit
- `PUT /community/events/{id}` → EventController@update
- `DELETE /community/events/{id}` → EventController@destroy
- `POST /community/events/{id}/register` → EventController@register
- `POST /community/events/{id}/unregister` → EventController@unregister

API routes:
- `GET /api/events` → API\EventController@index

### 6. Create Calendar View
File: `resources/views/community/calendar/index.blade.php`
- Calendar display (using FullCalendar.js or similar)
- View switcher (Month, Week, Day)
- Create Event button
- Event list sidebar (optional)
- Filter options

### 7. Create Event Form View
File: `resources/views/community/events/create.blade.php`
- Form fields:
  - Event title
  - Description (rich text editor)
  - Icon selector (dropdown or icon picker)
  - Color picker
  - Start date and time picker
  - End date and time picker (or duration)
  - Location input
  - Organizer/Staff selector (dropdown)
  - Banner upload
  - Participants selector (multi-select or search)
  - Max participants input
  - Public/Private toggle
  - Event type selector
- Submit button

### 8. Create Event Detail View
File: `resources/views/community/events/show.blade.php`
- Display event:
  - Banner image
  - Title with icon and color
  - Date/time
  - Location
  - Organizer info
  - Description
  - Participants list
  - Register/Unregister button
  - Edit/Delete buttons (for organizer)

### 9. Implement Calendar Library
- Use FullCalendar.js or similar
- Load events via AJAX
- Handle click events
- Display event details in modal

### 10. Implement Icon Selector
- Create icon picker component
- Use Font Awesome or similar icon library
- Store icon class name

### 11. Implement Color Picker
- Use color picker library (colpick, spectrum, or native HTML5)
- Store hex color code

### 12. Implement Date/Time Picker
- Use Flatpickr, Bootstrap Datepicker, or similar
- Handle timezone (optional)

### 13. Implement Participant Selection
- Use Select2 or similar for multi-select
- Search users by name
- Show selected participants

### 14. Implement Banner Upload
- Handle image upload
- Resize banner image
- Store in `storage/app/public/events/banners/`

### 15. Create Reminder System (Optional)
- Create reminders when event is created
- Send email/notification reminders
- Use Laravel Queue for scheduled reminders

## Files to Create/Modify
- `database/migrations/xxxx_create_events_table.php` (new)
- `database/migrations/xxxx_create_event_participants_table.php` (new)
- `database/migrations/xxxx_create_event_reminders_table.php` (new, optional)
- `app/Models/Event.php` (new)
- `app/Models/EventParticipant.php` (new)
- `app/Models/EventReminder.php` (new, optional)
- `app/Http/Controllers/Community/EventController.php` (new)
- `app/Http/Controllers/API/EventController.php` (new)
- `resources/views/community/calendar/index.blade.php` (new)
- `resources/views/community/events/create.blade.php` (new)
- `resources/views/community/events/show.blade.php` (new)
- `resources/views/community/events/edit.blade.php` (new)
- `resources/js/calendar.js` (new)
- `routes/web.php` (modify)
- `routes/api.php` (modify)

## Dependencies
- FullCalendar.js or similar calendar library
- Date/time picker library (Flatpickr)
- Color picker library
- Icon library (Font Awesome)
- Select2 or similar for multi-select
- Image manipulation library (Intervention Image)

## Calendar Event Format (JSON)
```json
{
  "id": 1,
  "title": "Japanese N5 Class",
  "start": "2024-01-15T10:00:00",
  "end": "2024-01-15T11:30:00",
  "color": "#3b82f6",
  "url": "/community/events/1"
}
```

## Validation Rules
```php
'title' => 'required|string|max:255',
'description' => 'nullable|string',
'start_date' => 'required|date|after_or_equal:today',
'end_date' => 'required|date|after:start_date',
'location' => 'nullable|string|max:255',
'organizer_id' => 'required|exists:users,id',
'banner' => 'nullable|image|max:2048',
'max_participants' => 'nullable|integer|min:1',
```

## Testing Considerations
- Test event creation with all fields
- Test date/time validation
- Test participant registration
- Test calendar display
- Test event filtering
- Test banner upload
- Test color and icon selection
- Test event edit/delete
- Test permission checks (organizer-only actions)

