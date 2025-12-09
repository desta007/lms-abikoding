<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CertificateService
{
    /**
     * Generate certificate for a user upon course completion
     */
    public function generate(User $user, Course $course, CourseEnrollment $enrollment): Certificate
    {
        // Check if course completion requirements are met
        if (!$this->checkCompletionRequirements($enrollment)) {
            throw new \Exception('Course completion requirements not met');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('course_enrollment_id', $enrollment->id)->first();
        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Create certificate record
        $certificate = Certificate::create([
            'course_enrollment_id' => $enrollment->id,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issued_at' => now(),
        ]);

        // Generate PDF
        try {
            $pdfPath = $this->createPDF($certificate);
            
            // Update certificate with PDF path
            $certificate->update(['file_path' => $pdfPath]);
        } catch (\Exception $e) {
            Log::error('Failed to create PDF: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            // Don't fail certificate creation if PDF fails, but log the error
            // PDF can be regenerated later
        }

        // Send certificate email
        try {
            $this->sendCertificateEmail($certificate);
        } catch (\Exception $e) {
            Log::error('Failed to send certificate email: ' . $e->getMessage());
        }

        return $certificate->fresh();
    }

    /**
     * Check if course completion requirements are met
     */
    protected function checkCompletionRequirements(CourseEnrollment $enrollment): bool
    {
        // Refresh enrollment to get latest data
        $enrollment->refresh();

        // Check if progress is 100%
        if ($enrollment->progress_percentage < 100) {
            return false;
        }

        // If progress is 100% but completed_at is not set, set it now
        if ($enrollment->progress_percentage >= 100 && !$enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
            $enrollment->refresh();
        }

        // Additional checks can be added here (e.g., quiz passed, payment completed)

        return true;
    }

    /**
     * Create PDF certificate
     */
    protected function createPDF(Certificate $certificate): string
    {
        $certificate->load(['course', 'user', 'course.instructor']);

        // Generate QR code
        try {
            $verificationUrl = route('certificates.verify', $certificate->verification_code);
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($verificationUrl));
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage());
            // Use empty QR code if generation fails
            $qrCode = '';
        }

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'instructor' => $certificate->course->instructor ?? null,
            'qrCode' => $qrCode,
        ];

        // Generate PDF
        try {
            $pdf = Pdf::loadView('certificates.template', $data);
            $pdf->setPaper('a4', 'landscape');
        } catch (\Exception $e) {
            Log::error('Failed to load PDF view: ' . $e->getMessage());
            throw new \Exception('Gagal membuat PDF: ' . $e->getMessage());
        }

        // Ensure certificates directory exists
        $certificatesDir = storage_path('app/public/certificates');
        if (!file_exists($certificatesDir)) {
            mkdir($certificatesDir, 0755, true);
        }

        // Save PDF to storage
        $filename = 'certificates/' . $certificate->certificate_number . '.pdf';
        try {
            Storage::disk('public')->put($filename, $pdf->output());
        } catch (\Exception $e) {
            Log::error('Failed to save PDF: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan PDF: ' . $e->getMessage());
        }

        return $filename;
    }

    /**
     * Send certificate email to user
     */
    protected function sendCertificateEmail(Certificate $certificate): void
    {
        $certificate->load(['course', 'user']);

        Mail::send('emails.certificate', [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
        ], function ($message) use ($certificate) {
            $message->to($certificate->user->email, $certificate->user->full_name)
                ->subject('Selamat! Sertifikat Anda Telah Diterbitkan - ' . $certificate->course->title);
        });
    }

    /**
     * Regenerate PDF for existing certificate
     */
    public function regeneratePDF(Certificate $certificate): string
    {
        // Delete old PDF if exists
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        // Generate new PDF
        $pdfPath = $this->createPDF($certificate);
        
        // Update certificate with new PDF path
        $certificate->update(['file_path' => $pdfPath]);

        return $pdfPath;
    }
}

