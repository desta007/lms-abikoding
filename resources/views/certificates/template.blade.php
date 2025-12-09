<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { 
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .certificate-container {
            background: white;
            border: 15px solid #d4af37;
            padding: 60px;
            text-align: center;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
        }
        .certificate-header {
            margin-bottom: 40px;
        }
        .certificate-title-id {
            font-size: 36px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .certificate-title-en {
            font-size: 28px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .certificate-body {
            margin: 40px 0;
        }
        .certificate-text {
            font-size: 16px;
            color: #4a5568;
            margin: 15px 0;
            line-height: 1.8;
        }
        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #1a202c;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .course-name {
            font-size: 32px;
            font-weight: 600;
            color: #2d3748;
            margin: 30px 0;
            font-style: italic;
        }
        .certificate-date {
            font-size: 18px;
            color: #4a5568;
            margin: 20px 0;
        }
        .instructor-name {
            font-size: 18px;
            color: #4a5568;
            margin-top: 10px;
        }
        .qr-code-container {
            margin: 40px 0;
            display: inline-block;
        }
        .qr-code-container img {
            width: 150px;
            height: 150px;
            border: 2px solid #e2e8f0;
            padding: 10px;
            background: white;
        }
        .certificate-number {
            font-size: 14px;
            color: #718096;
            margin-top: 30px;
            font-weight: 600;
        }
        .certificate-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }
        .signature-area {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 2px solid #1a202c;
            margin: 60px 20px 10px;
        }
        .signature-name {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }
        .signature-title {
            font-size: 14px;
            color: #718096;
            margin-top: 5px;
        }
        .decorative-border {
            position: absolute;
            border: 3px solid #d4af37;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="decorative-border"></div>
        
        <div class="certificate-header">
            <div class="certificate-title-id">SERTIFIKAT PENYELESAIAN</div>
            <div class="certificate-title-en">CERTIFICATE OF COMPLETION</div>
        </div>

        <div class="certificate-body">
            <div class="certificate-text">
                Dengan ini menyatakan bahwa<br>
                <span style="font-style: italic;">This certifies that</span>
            </div>

            <div class="student-name">
                {{ $user->full_name }}
            </div>

            <div class="certificate-text">
                telah menyelesaikan kursus<br>
                <span style="font-style: italic;">has successfully completed the course</span>
            </div>

            <div class="course-name">
                "{{ $course->title }}"
            </div>

            <div class="certificate-date">
                Pada tanggal {{ $certificate->issued_at->format('d F Y') }}<br>
                <span style="font-style: italic;">On {{ $certificate->issued_at->format('F d, Y') }}</span>
            </div>

            @if($instructor)
            <div class="instructor-name">
                Instruktur: {{ $instructor->full_name }}<br>
                <span style="font-style: italic;">Instructor: {{ $instructor->full_name }}</span>
            </div>
            @endif
        </div>

        <div class="qr-code-container">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            <div style="font-size: 12px; color: #718096; margin-top: 10px;">
                Scan untuk verifikasi / Scan to verify
            </div>
        </div>

        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>

        <div class="certificate-footer">
            <div class="signature-area">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $instructor ? $instructor->full_name : 'Instructor' }}</div>
                    <div class="signature-title">Instruktur / Instructor</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">LMS DC TECH</div>
                    <div class="signature-title">Platform Pendidikan</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

