# Plan 14: Admin Payment

## Overview
Payment management system for **recruitment administration processes** (admin-initiated payments for job placement services). This is separate from course enrollment payments, which are handled in Plan 19.

## Requirements
- Process payments for recruitment administration fees
- Track payment status
- Generate invoices/receipts
- Payment history
- Integration with Midtrans payment gateway
- **Note**: Course enrollment payments are handled separately in Plan 19

## Database Changes

### Create Payments Table
File: `database/migrations/xxxx_create_payments_table.php`

Fields:
- `id` (bigInteger)
- `user_id` (foreign key) - student/applicant
- `job_application_id` (foreign key, nullable)
- `course_id` (foreign key, nullable) - **Note**: Added for Plan 19 compatibility, can be null for admin payments
- `payment_type` (enum: 'recruitment_fee', 'processing_fee', 'course_enrollment', 'other')
  - **Note**: 'course_enrollment' type is used by Plan 19, admin payments use 'recruitment_fee' or 'processing_fee'
- `amount` (decimal)
- `currency` (string, default: 'IDR')
- `status` (enum: 'pending', 'processing', 'completed', 'failed', 'cancelled')
- `payment_method` (string, nullable) - 'bank_transfer', 'credit_card', 'e_wallet', etc.
- `midtrans_order_id` (string, nullable) - Midtrans order ID
- `midtrans_transaction_id` (string, nullable) - Midtrans transaction ID
- `midtrans_payment_type` (string, nullable)
- `paid_at` (timestamp, nullable)
- `due_date` (date, nullable)
- `notes` (text, nullable)
- `metadata` (json, nullable) - store additional payment data
- `created_at`, `updated_at`

### Create Payment Items Table (Optional - for detailed invoices)
File: `database/migrations/xxxx_create_payment_items_table.php`

Fields:
- `id` (bigInteger)
- `payment_id` (foreign key)
- `description` (string)
- `quantity` (integer, default: 1)
- `price` (decimal)
- `total` (decimal)
- `created_at`, `updated_at`

### Create Invoices Table
File: `database/migrations/xxxx_create_invoices_table.php`

Fields:
- `id` (bigInteger)
- `payment_id` (foreign key)
- `invoice_number` (string, unique)
- `invoice_path` (string, nullable) - PDF path
- `issued_at` (timestamp)
- `created_at`, `updated_at`

## Models to Create

### Payment Model
File: `app/Models/Payment.php`
- Relationships: belongsTo(User, JobApplication), hasMany(PaymentItem), hasOne(Invoice)
- Scopes: pending(), completed(), failed()
- Methods: generateInvoiceNumber(), markAsPaid()

### PaymentItem Model (Optional)
File: `app/Models/PaymentItem.php`
- Relationships: belongsTo(Payment)

### Invoice Model
File: `app/Models/Invoice.php`
- Relationships: belongsTo(Payment)

## Implementation Steps

### 1. Install Midtrans Package
```bash
composer require midtrans/midtrans-php
```

### 2. Configure Midtrans
File: `config/midtrans.php` (create)
- Server key
- Client key
- Environment (sandbox/production)
- Notification URL

### 3. Create Migrations
```bash
php artisan make:migration create_payments_table
php artisan make:migration create_payment_items_table
php artisan make:migration create_invoices_table
```

### 4. Create Models
```bash
php artisan make:model Payment
php artisan make:model PaymentItem
php artisan make:model Invoice
```

### 5. Create Payment Service
File: `app/Services/PaymentService.php`
- `createPayment()` method:
  - Create payment record
  - Generate Midtrans order
  - Return payment URL
- `processNotification()` method:
  - Handle Midtrans webhook
  - Update payment status
  - Generate invoice if completed
- `generateInvoice()` method:
  - Create invoice PDF
  - Store invoice file
  - Return invoice path

### 6. Create Payment Controller
File: `app/Http/Controllers/Admin/PaymentController.php`
- `index()` method:
  - List all payments
  - Filter by status, date range, user
  - Support pagination
- `create()` method:
  - Show payment creation form
- `store(Request $request)` method:
  - Create payment
  - Initialize Midtrans payment
  - Return payment URL
- `show($id)` method:
  - Show payment details
  - Show invoice download link
- `update(Request $request, $id)` method:
  - Update payment status manually
- `destroy($id)` method:
  - Cancel payment

### 7. Create Payment Webhook Controller
File: `app/Http/Controllers/API/PaymentWebhookController.php`
- `handle()` method:
  - Receive Midtrans notification
  - Verify signature
  - Update payment status
  - Generate invoice if paid
  - Return response

### 8. Create Invoice Controller
File: `app/Http/Controllers/Admin/InvoiceController.php`
- `show($id)` method:
  - Display invoice
- `download($id)` method:
  - Download invoice PDF
- `generate($paymentId)` method:
  - Generate invoice PDF

### 9. Create Routes
File: `routes/web.php`
- `GET /admin/payments` → Admin\PaymentController@index
- `GET /admin/payments/create` → Admin\PaymentController@create
- `POST /admin/payments` → Admin\PaymentController@store
- `GET /admin/payments/{id}` → Admin\PaymentController@show
- `PUT /admin/payments/{id}` → Admin\PaymentController@update
- `DELETE /admin/payments/{id}` → Admin\PaymentController@destroy
- `GET /admin/invoices/{id}` → Admin\InvoiceController@show
- `GET /admin/invoices/{id}/download` → Admin\InvoiceController@download

API routes:
- `POST /api/payments/webhook` → API\PaymentWebhookController@handle

### 10. Create Payment List View
File: `resources/views/admin/payments/index.blade.php`
- Filter panel
- Payment table:
  - Payment ID
  - User/Student name
  - Amount
  - Status badge
  - Payment method
  - Date
  - Actions: View, Generate Invoice
- Export button

### 11. Create Payment Form View
File: `resources/views/admin/payments/create.blade.php`
- Form fields:
  - Student selector
  - Job application selector (optional)
  - Payment type
  - Amount
  - Currency
  - Due date
  - Notes
- Submit button

### 12. Create Payment Detail View
File: `resources/views/admin/payments/show.blade.php`
- Display payment information
- Show payment status
- Show Midtrans transaction details
- Show invoice link
- Action buttons: Update Status, Generate Invoice

### 13. Create Invoice View
File: `resources/views/admin/invoices/show.blade.php`
- Display invoice:
  - Invoice number
  - Date
  - Student/applicant info
  - Payment items
  - Total amount
  - Payment status
  - Download PDF button

### 14. Implement Invoice PDF Generation
- Use DomPDF or similar
- Template: `resources/views/invoices/template.blade.php`
- Include:
  - Company logo
  - Invoice number
  - Date
  - Student/applicant details
  - Payment items
  - Total
  - Payment instructions

### 15. Implement Midtrans Integration
- Initialize Snap token
- Handle payment notifications
- Verify payment status
- Update database accordingly

### 16. Create Payment Status Update Job (Optional)
File: `app/Jobs/UpdatePaymentStatus.php`
- Periodically check pending payments
- Update status from Midtrans API

## Files to Create/Modify
- `database/migrations/xxxx_create_payments_table.php` (new)

- `database/migrations/xxxx_create_payment_items_table.php` (new, optional)
- `database/migrations/xxxx_create_invoices_table.php` (new)
- `app/Models/Payment.php` (new)
- `app/Models/PaymentItem.php` (new, optional)
- `app/Models/Invoice.php` (new)
- `app/Services/PaymentService.php` (new)
- `app/Http/Controllers/Admin/PaymentController.php` (new)
- `app/Http/Controllers/API/PaymentWebhookController.php` (new)
- `app/Http/Controllers/Admin/InvoiceController.php` (new)
- `app/Jobs/UpdatePaymentStatus.php` (new, optional)
- `config/midtrans.php` (new)
- `resources/views/admin/payments/index.blade.php` (new)
- `resources/views/admin/payments/create.blade.php` (new)
- `resources/views/admin/payments/show.blade.php` (new)
- `resources/views/admin/invoices/show.blade.php` (new)
- `resources/views/invoices/template.blade.php` (new)
- `routes/web.php` (modify)
- `routes/api.php` (modify)

## Dependencies
- midtrans/midtrans-php package
- DomPDF or similar for PDF generation
- Laravel Queue for background jobs (optional)
- **Plan 13**: JobApplication model (for linking payments to job applications)
- **Plan 19**: Course payment system shares the same Payment model but uses different payment_type values

## Important Notes
- This plan handles **admin-initiated payments** for recruitment services
- **Plan 19** handles **student-initiated payments** for course enrollments
- Both plans share the same `payments` table but differentiate via `payment_type` field
- The PaymentService can be shared between both plans or separated based on payment type

## Midtrans Configuration
```php
// config/midtrans.php
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];
```

## Payment Flow
1. Admin creates payment record
2. Generate Midtrans Snap token
3. Redirect user to Midtrans payment page
4. User completes payment
5. Midtrans sends webhook notification
6. Update payment status
7. Generate invoice if payment successful

## Testing Considerations
- Test payment creation
- Test Midtrans integration (sandbox)
- Test webhook handling
- Test invoice generation
- Test payment status updates
- Test PDF generation with Japanese text
- Test currency formatting
- Test payment history display

