<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Anda Telah Diterbitkan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .certificate-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4f46e5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Selamat!</h1>
        <p style="margin: 0; font-size: 18px;">Sertifikat Anda Telah Diterbitkan</p>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $user->full_name }}</strong>,</p>
        
        <p>Kami dengan senang hati menginformasikan bahwa Anda telah berhasil menyelesaikan kursus:</p>
        
        <div class="certificate-info">
            <h2 style="margin-top: 0; color: #1f2937;">{{ $course->title }}</h2>
            <p style="margin-bottom: 0;">
                <strong>Nomor Sertifikat:</strong> {{ $certificate->certificate_number }}<br>
                <strong>Diterbitkan:</strong> {{ $certificate->issued_at->format('d F Y') }}
            </p>
        </div>
        
        <p>Anda dapat mengunduh sertifikat PDF Anda dengan mengklik tombol di bawah ini:</p>
        
        <div style="text-align: center;">
            <a href="{{ route('certificates.download', $certificate->id) }}" class="button">
                Unduh Sertifikat PDF
            </a>
        </div>
        
        <p>Atau kunjungi halaman sertifikat Anda untuk melihat detail lengkap:</p>
        <p style="text-align: center;">
            <a href="{{ route('certificates.show', $certificate->id) }}" style="color: #4f46e5; text-decoration: none;">
                Lihat Sertifikat Saya
            </a>
        </p>
        
        <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 0; font-size: 14px;">
                <strong>💡 Tips:</strong> Simpan sertifikat Anda dengan aman. Anda dapat membagikan sertifikat ini dengan menggunakan kode verifikasi: <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">{{ $certificate->verification_code }}</code>
            </p>
        </div>
        
        <p>Terima kasih telah menjadi bagian dari program pembelajaran kami!</p>
        
        <p>Salam,<br>
        <strong>Tim LMS DC Tech</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon jangan membalas email ini.</p>
        <p>&copy; {{ date('Y') }} LMS DC Tech. All rights reserved.</p>
    </div>
</body>
</html>

