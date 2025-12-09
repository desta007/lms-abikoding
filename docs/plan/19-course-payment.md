# Plan 19: Course Enrollment Payment Flow

## Overview
Implement payment integration for course enrollment, allowing students to purchase courses before accessing materials. This complements Plan 14 (admin payment) by handling student-side course payments.

## Requirements
- Payment required for paid courses (optional for free courses)
- Integration with Midtrans payment gateway
- Payment status tracking
- Enrollment only after successful payment
- Payment history for students
- Refund handling (optional)

## Database Changes

### Update Course Enrollments Table
File: `database/migrations/xxxx_add_payment_to_enrollments_table.php`

Fields to add:
- `payment_id` (foreign key to payments table, nullable)
- `payment_status` (enum: 'pending', 'paid', 'failed', 'refunded', nullable)
- `enrolled_via` (enum: 'free', 'paid', default: 'free')
- `enrolled_at` (timestamp)

### Create Course Payments Table (if separate from Plan 14)
- Use existing `payments` table from Plan 14
- Add `payment_type` to distinguish course payments vs admin payments

### Update Courses Table (if not exists)
- Ensure `price` field exists (from Plan 03)
- Add `is_free` (boolean, default: false) - quick check for free courses

## Models to Create/Modify

### Update CourseEnrollment Model
File: `app/Models/CourseEnrollment.php`
- Relationships: belongsTo(Payment)
- Scopes: paid(), pending(), free()
- Methods: isPaid(), markAsPaid()

### Update Payment Model (from Plan 14)
- Add scope for course payments: `coursePayments()`
- Add relationship to CourseEnrollment

## Implementation Steps

### 1. Update Course Enrollment Controller
File: `app/Http/Controllers/CourseEnrollmentController.php` (from Plan 08)
- Modify `store(Request $request)` method:
  - Check if course is paid
  - If paid, create payment record
  - Redirect to payment page
  - If free, enroll directly
- Add `paymentSuccess($paymentId)` method:
  - Verify payment status
  - Create enrollment
  - Redirect to course page
- Add `paymentFailed($paymentId)` method:
  - Show error message
  - Allow retry

### 2. Create Course Payment Service
File: `app/Services/CoursePaymentService.php`
- `initiatePayment(User $user, Course $course)` method:
  - Create payment record
  - Generate Midtrans Snap token
  - Return payment URL
- `processPaymentCallback($paymentId, $midtransData)` method:
  - Verify payment with Midtrans
  - Update payment status
  - Create enrollment if paid
  - Send confirmation email
- `checkPaymentStatus(Payment $payment)` method:
  - Check status from Midtrans API
  - Update local status

### 3. Update Course Detail View
File: `resources/views/courses/show.blade.php` (from Plan 08)
- Show course price prominently
- Show "Gratis" badge for free courses
- Show "Beli Sekarang" button for paid courses
- Show "Daftar" button for free courses
- Show "Sudah Terdaftar" if enrolled
- Show enrollment status if payment pending

### 4. Create Payment Page View
File: `resources/views/payments/checkout.blade.php`
- Display course information
- Show course price
- Show payment method options (Midtrans)
- Show payment summary
- Display Midtrans payment widget
- Loading state during payment process

### 5. Create Payment Success View
File: `resources/views/payments/success.blade.php`
- Success message
- Course access link
- Payment receipt link
- Continue learning button

### 6. Create Payment Failed View
File: `resources/views/payments/failed.blade.php`
- Error message
- Retry payment button
- Contact support link

### 7. Create Payment History View
File: `resources/views/payments/history.blade.php`
- List of all payments
- Filter by status
- Show payment details
- Download receipt link
- Refund request button (if applicable)

### 8. Update Routes
File: `routes/web.php`
- `POST /courses/{id}/enroll` → CourseEnrollmentController@store (modify)
- `GET /payments/checkout/{courseId}` → PaymentController@checkout
- `POST /payments/checkout/{courseId}` → PaymentController@processCheckout
- `GET /payments/success/{paymentId}` → PaymentController@success
- `GET /payments/failed/{paymentId}` → PaymentController@failed
- `GET /payments/history` → PaymentController@history
- `POST /api/payments/webhook` → PaymentController@webhook (update from Plan 14)

### 9. Create Payment Controller
File: `app/Http/Controllers/PaymentController.php`
- `checkout($courseId)` method:
  - Verify course exists
  - Verify user authenticated
  - Check if already enrolled
  - Show checkout page
- `processCheckout(Request $request, $courseId)` method:
  - Validate request
  - Create payment via CoursePaymentService
  - Redirect to Midtrans payment page
- `success($paymentId)` method:
  - Verify payment
  - Show success page
- `failed($paymentId)` method:
  - Show failure page
- `history()` method:
  - Show user's payment history
- `webhook(Request $request)` method:
  - Handle Midtrans callback
  - Update payment status
  - Create enrollment if paid

### 10. Update Course Model
File: `app/Models/Course.php`
- Add `isFree()` accessor
- Add `isPaid()` accessor
- Add `canEnroll(User $user)` method
- Add `enrollmentPrice()` accessor (for discounts)

### 11. Implement Enrollment Gating
File: `app/Http/Middleware/EnsureEnrolled.php` (from Plan 09)
- Check if user is enrolled
- Check if payment is completed (for paid courses)
- Redirect to payment page if not paid

### 12. Create Payment Status Component
File: `resources/views/components/payment-status.blade.php`
- Display payment status badge
- Show payment method
- Show payment date

## Files to Create/Modify
- `database/migrations/xxxx_add_payment_to_enrollments_table.php` (new)
- `app/Models/CourseEnrollment.php` (modify)
- `app/Models/Payment.php` (modify - from Plan 14)
- `app/Models/Course.php` (modify)
- `app/Services/CoursePaymentService.php` (new)
- `app/Http/Controllers/CourseEnrollmentController.php` (modify - from Plan 08)
- `app/Http/Controllers/PaymentController.php` (new)
- `resources/views/courses/show.blade.php` (modify - from Plan 08)
- `resources/views/payments/checkout.blade.php` (new)
- `resources/views/payments/success.blade.php` (new)
- `resources/views/payments/failed.blade.php` (new)
- `resources/views/payments/history.blade.php` (new)
- `resources/views/components/payment-status.blade.php` (new)
- `routes/web.php` (modify)
- `routes/api.php` (modify - webhook)

## Dependencies
- Midtrans PHP SDK (from Plan 14)
- Midtrans configuration (from Plan 14)
- Payment model (from Plan 14)
- CourseEnrollment model (from Plan 04)
- Email notifications (from Plan 18)

## Payment Flow
1. Student views course detail page
2. Student clicks "Beli Sekarang" for paid course
3. System checks if course is paid
4. System creates payment record
5. System generates Midtrans Snap token
6. Student redirected to Midtrans payment page
7. Student completes payment
8. Midtrans sends webhook to application
9. System verifies payment
10. System creates enrollment record
11. System sends enrollment confirmation email
12. Student redirected to course content

## Free Course Flow
1. Student views course detail page
2. Student clicks "Daftar" for free course
3. System creates enrollment immediately
4. System sends enrollment confirmation email
5. Student redirected to course content

## Payment Status States
- `pending`: Payment initiated, awaiting completion
- `paid`: Payment successful, enrollment active
- `failed`: Payment failed or cancelled
- `refunded`: Payment refunded (enrollment revoked)

## Testing Considerations
- Test free course enrollment
- Test paid course enrollment flow
- Test Midtrans payment integration
- Test payment webhook handling
- Test payment success redirect
- Test payment failure handling
- Test duplicate enrollment prevention
- Test payment history display
- Test enrollment gating after payment
- Test refund flow (if implemented)

## Integration with Other Plans
- Plan 08: Course enrollment endpoint
- Plan 09: Material access gating
- Plan 14: Payment model and service
- Plan 18: Enrollment confirmation email

## Security Considerations
- Verify payment status before enrollment
- Validate webhook signature from Midtrans
- Prevent duplicate enrollments
- Rate limit payment attempts
- Log all payment transactions

