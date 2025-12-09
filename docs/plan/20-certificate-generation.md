# Plan 20: Certificate Generation System

## Overview
Generate and issue digital certificates for students upon course completion. Certificates should be downloadable PDFs with proper verification.

## Requirements
- Generate PDF certificates upon course completion
- Certificate includes:
  - Student name
  - Course name
  - Completion date
  - Instructor name
  - Certificate number (unique)
  - QR code for verification
  - Course category/level
- Download certificate as PDF
- Certificate verification system
- Certificate history for students

## Database Changes

### Create Certificates Table
File: `database/migrations/xxxx_create_certificates_table.php`

Fields:
- `id` (bigInteger)
- `course_enrollment_id` (foreign key)
- `user_id` (foreign key)
- `course_id` (foreign key)
- `certificate_number` (string, unique)
- `issued_at` (timestamp)
- `file_path` (string) - PDF file path
- `verification_code` (string, unique) - for QR code
- `is_valid` (boolean, default: true)
- `revoked_at` (timestamp, nullable)
- `revoked_reason` (text, nullable)
- `metadata` (json, nullable) - store additional data
- `created_at`, `updated_at`

### Create Certificate Verifications Table
File: `database/migrations/xxxx_create_certificate_verifications_table.php`

Fields:
- `id` (bigInteger)
- `certificate_id` (foreign key)
- `verified_at` (timestamp)
- `ip_address` (string)
- `user_agent` (string, nullable)
- `created_at`, `updated_at`

## Models to Create

### Certificate Model
File: `app/Models/Certificate.php`
- Relationships: belongsTo(CourseEnrollment, User, Course)
- Scopes: valid(), revoked(), byUser()
- Methods: generateVerificationCode(), revoke(), verify()
- Accessors: verificationUrl()

### CertificateVerification Model
File: `app/Models/CertificateVerification.php`
- Relationships: belongsTo(Certificate)

## Implementation Steps

### 1. Create Certificate Generation Service
File: `app/Services/CertificateService.php`
- `generate(User $user, Course $course, CourseEnrollment $enrollment)` method:
  - Check if course completion requirements met
  - Generate certificate number
  - Generate verification code
  - Create certificate record
  - Generate PDF
  - Save PDF file
  - Send certificate email
  - Return certificate
- `generateCertificateNumber()` method:
  - Format: CERT-YYYYMMDD-XXXXXX (sequential)
- `generateVerificationCode()` method:
  - Random string (e.g., 16 characters)
- `createPDF(Certificate $certificate)` method:
  - Generate PDF using DomPDF or similar
  - Include certificate template
  - Add QR code
  - Return file path

### 2. Create Certificate PDF Template
File: `resources/views/certificates/template.blade.php`
- Professional certificate design
- Logo
- Certificate title (in Indonesian and English)
- Student name
- Course name
- Completion date
- Instructor name
- Certificate number
- QR code
- Signature area (optional)
- Border/decoration

### 3. Create Certificate Controller
File: `app/Http/Controllers/CertificateController.php`
- `show($id)` method:
  - Display certificate details
- `download($id)` method:
  - Download PDF certificate
- `verify($code)` method:
  - Verify certificate by code
  - Show verification result
- `history()` method:
  - Show user's certificates

### 4. Create Certificate Verification Controller
File: `app/Http/Controllers/CertificateVerificationController.php`
- `verify($code)` method:
  - Verify certificate
  - Log verification
  - Return verification result

### 5. Create Certificate Views
File: `resources/views/certificates/show.blade.php`
- Display certificate as image/PDF preview
- Download button
- Certificate details
- Verification code display
- Share buttons (optional)

File: `resources/views/certificates/history.blade.php`
- List of user's certificates
- Filter by course
- Download buttons
- Verification status

File: `resources/views/certificates/verify.blade.php`
- Verification form (code input)
- Verification result display

### 6. Update Course Completion Logic
File: `app/Http/Controllers/CourseContentController.php` (from Plan 09)
- After course completion:
  - Check completion requirements
  - Generate certificate via CertificateService
  - Update enrollment completion status

### 7. Create Certificate Number Generator
File: `app/Helpers/CertificateHelper.php` (optional)
- Generate unique certificate numbers
- Format: CERT-YYYYMMDD-XXXXXX

### 8. Install PDF Generation Library
```bash
composer require barryvdh/laravel-dompdf
```

### 9. Install QR Code Library
```bash
composer require simplesoftwareio/simple-qrcode
```

### 10. Create Certificate Routes
File: `routes/web.php`
- `GET /certificates` → CertificateController@history
- `GET /certificates/{id}` → CertificateController@show
- `GET /certificates/{id}/download` → CertificateController@download
- `GET /certificates/verify/{code}` → CertificateVerificationController@verify
- `GET /verify-certificate` → CertificateVerificationController@form
- `POST /verify-certificate` → CertificateVerificationController@verify

### 11. Create Certificate Component
File: `resources/views/components/certificate-card.blade.php`
- Display certificate preview
- Course name
- Issue date
- Download button
- Verification badge

### 12. Integrate with Course Completion
File: `app/Services/CourseCompletionService.php` (new, optional)
- Check course completion requirements:
  - All chapters completed
  - All materials completed
  - Optional: Quiz passed (if implemented)
- Trigger certificate generation

### 13. Create Certificate Email Template
File: `resources/views/emails/certificate.blade.php`
- Congratulations message
- Certificate download link
- Verification information
- Share certificate option

## Files to Create/Modify
- `database/migrations/xxxx_create_certificates_table.php` (new)
- `database/migrations/xxxx_create_certificate_verifications_table.php` (new)
- `app/Models/Certificate.php` (new)
- `app/Models/CertificateVerification.php` (new)
- `app/Services/CertificateService.php` (new)
- `app/Http/Controllers/CertificateController.php` (new)
- `app/Http/Controllers/CertificateVerificationController.php` (new)
- `resources/views/certificates/template.blade.php` (new)
- `resources/views/certificates/show.blade.php` (new)
- `resources/views/certificates/history.blade.php` (new)
- `resources/views/certificates/verify.blade.php` (new)
- `resources/views/components/certificate-card.blade.php` (new)
- `resources/views/emails/certificate.blade.php` (new)
- `app/Http/Controllers/CourseContentController.php` (modify - from Plan 09)
- `routes/web.php` (modify)
- `composer.json` (modify - add packages)

## Dependencies
- barryvdh/laravel-dompdf (PDF generation)
- simplesoftwareio/simple-qrcode (QR code generation)
- Japanese font support (for PDFs)
- Certificate template design

## PDF Generation Configuration
```php
// config/dompdf.php
return [
    'font_dir' => storage_path('fonts'),
    'font_cache' => storage_path('fonts'),
    'default_font' => 'noto-sans',
    'enable_font_subsetting' => true,
    'dpi' => 300,
    'enable_remote' => true,
];
```

## Certificate Template Example
```blade
{{-- resources/views/certificates/template.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            margin: 0;
            padding: 40px;
        }
        .certificate {
            border: 10px solid #gold;
            padding: 60px;
            text-align: center;
        }
        .certificate-number {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
        .qr-code {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>SERTIFIKAT PENYELESAIAN</h1>
        <h2>CERTIFICATE OF COMPLETION</h2>
        
        <p>Dengan ini menyatakan bahwa</p>
        <p>This certifies that</p>
        
        <h3>{{ $user->full_name }}</h3>
        
        <p>telah menyelesaikan kursus</p>
        <p>has completed the course</p>
        
        <h3>{{ $course->title }}</h3>
        
        <p>Pada tanggal {{ $certificate->issued_at->format('d F Y') }}</p>
        <p>On {{ $certificate->issued_at->format('F d, Y') }}</p>
        
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
        </div>
        
        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>
    </div>
</body>
</html>
```

## Certificate Number Format
- Format: `CERT-YYYYMMDD-XXXXXX`
- Example: `CERT-20240115-000123`
- Sequential number per day

## Verification Code Format
- Format: Random 16-character alphanumeric
- Example: `A7B9C2D4E6F8G0H1`
- Stored in database for verification

## QR Code Content
- URL: `https://lms-eong.com/verify-certificate/{verification_code}`
- Contains verification code for quick verification

## Testing Considerations
- Test certificate generation
- Test PDF generation with Japanese text
- Test certificate download
- Test certificate verification
- Test duplicate certificate prevention
- Test certificate revocation
- Test email delivery
- Test QR code generation and scanning
- Test certificate history display

## Integration with Other Plans
- Plan 09: Trigger on course completion
- Plan 18: Send certificate email
- Plan 19: Ensure payment completed before certificate
- Plan 15: Support Japanese text in PDF

## Security Considerations
- Prevent certificate forgery
- Secure certificate storage
- Verify certificate authenticity
- Log all verification attempts
- Prevent unauthorized certificate generation

