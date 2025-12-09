<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    /**
     * Show verification form
     */
    public function form()
    {
        return view('certificates.verify-form');
    }

    /**
     * Verify certificate by code
     */
    public function verify(Request $request, ?string $code = null)
    {
        $verificationCode = $code ?? $request->input('code');

        if (!$verificationCode) {
            return redirect()->route('certificates.verify.form')
                ->with('error', 'Kode verifikasi diperlukan');
        }

        $certificate = Certificate::where('verification_code', $verificationCode)
            ->with(['course', 'user', 'course.instructor'])
            ->first();

        if (!$certificate) {
            return redirect()->route('certificates.verify.form')
                ->with('error', 'Sertifikat tidak ditemukan');
        }

        // Log verification
        $certificate->verify();

        $isValid = $certificate->is_valid && !$certificate->revoked_at;

        return view('certificates.verify', compact('certificate', 'isValid'));
    }
}
