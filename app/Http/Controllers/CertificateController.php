<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Show user's certificate history
     */
    public function history()
    {
        $certificates = Certificate::byUser(Auth::id())
            ->with(['course', 'user'])
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('certificates.history', compact('certificates'));
    }

    /**
     * Generate certificate for a course
     */
    public function generate($courseId)
    {
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->with('course')
            ->firstOrFail();

        // Check if course is completed
        if ($enrollment->progress_percentage < 100) {
            return redirect()->back()->with('error', 'Anda harus menyelesaikan kursus terlebih dahulu (Progress: ' . $enrollment->progress_percentage . '%)');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('course_enrollment_id', $enrollment->id)->first();
        if ($existingCertificate) {
            return redirect()->route('certificates.show', $existingCertificate->id)
                ->with('info', 'Sertifikat sudah ada');
        }

        // Ensure completed_at is set if progress is 100%
        if ($enrollment->progress_percentage >= 100 && !$enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
        }

        try {
            $certificate = $this->certificateService->generate(
                Auth::user(),
                $enrollment->course,
                $enrollment
            );

            // Refresh certificate to get latest data
            $certificate->refresh();

            // Redirect to download page to automatically download PDF
            return redirect()->route('certificates.download', $certificate->id)
                ->with('success', 'Sertifikat berhasil dibuat dan sedang diunduh');
        } catch (\Exception $e) {
            \Log::error('Certificate generation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Show certificate details
     */
    public function show($id)
    {
        $certificate = Certificate::where('user_id', Auth::id())
            ->with(['course', 'user', 'course.instructor'])
            ->findOrFail($id);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download($id)
    {
        $certificate = Certificate::where('user_id', Auth::id())
            ->with(['course', 'user', 'course.instructor'])
            ->findOrFail($id);

        // If PDF exists in storage, return it
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            try {
                return Storage::disk('public')->download(
                    $certificate->file_path,
                    'certificate-' . $certificate->certificate_number . '.pdf'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to download existing PDF: ' . $e->getMessage());
                // Fall through to generate on-the-fly
            }
        }

        // Otherwise, generate on-the-fly
        return $this->downloadPdf($certificate);
    }

    /**
     * Generate PDF on-the-fly
     */
    private function downloadPdf(Certificate $certificate)
    {
        $certificate->load(['course', 'user', 'course.instructor']);
        
        // Generate QR code
        try {
            $verificationUrl = route('certificates.verify', $certificate->verification_code);
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($verificationUrl));
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code: ' . $e->getMessage());
            $qrCode = '';
        }

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'instructor' => $certificate->course->instructor ?? null,
            'qrCode' => $qrCode,
        ];

        try {
            $pdf = Pdf::loadView('certificates.template', $data);
            $pdf->setPaper('a4', 'landscape');
            
            return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Gagal membuat PDF sertifikat: ' . $e->getMessage());
        }
    }
}
