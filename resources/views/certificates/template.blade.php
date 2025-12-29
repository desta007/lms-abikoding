<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Penyelesaian</title>
    
    @php
        // 1. PROSES LOGO (Mengatasi Image Not Found)
        $logoBase64 = '';
        $logoPath = public_path('storage/logodctech.jpg'); // Logo DC Tech dari folder storage
        
        if (file_exists($logoPath)) {
            try {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) {
                // Jika gagal baca gambar, biarkan kosong agar PDF tetap terbit
            }
        }

        // 2. PROSES QR CODE (Mengatasi Library Error)
        $qrCodeImage = '';
        $qrContent = $instructor ? $instructor->full_name : 'Verifikasi Sertifikat';
        
        try {
            // Cek apakah library QrCode terinstall
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                // Kita gunakan format SVG (Lebih aman drpd PNG karena tidak butuh Imagick)
                $qrRaw = SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->margin(1)->generate($qrContent);
                // Encode ke base64 agar bisa masuk tag <img>
                $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrRaw);
            }
        } catch (\Exception $e) {
            // Jika library error, variable $qrCodeImage tetap kosong
        }
    @endphp

    <style>
        /* SETUP HALAMAN */
        @page { margin: 0px; size: A4 landscape; }
        body { 
            margin: 0px; 
            padding: 0px; 
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
        }

        /* FRAME BORDER */
        .border-frame {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 10px solid #1e40af; /* Biru Tua */
            box-sizing: border-box;
            text-align: center;
        }

        /* KONTEN UTAMA */
        .content-wrapper {
            padding-top: 50px;
            padding-left: 50px;
            padding-right: 50px;
            padding-bottom: 30px;
        }

        .header-title {
            font-size: 36px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .text-regular {
            font-size: 22px;
            color: #555;
            margin-bottom: 5px;
        }

        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            margin: 15px auto;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
            display: inline-block;
            min-width: 60%;
        }

        .course-title {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb; /* Biru Cerah */
            font-style: italic;
            margin: 15px 0;
        }

        .date-text {
            font-size: 20px;
            color: #666;
            margin-top: 20px;
        }

        .cert-no {
            font-size: 20px;
            font-weight: bold;
            color: #aaa;
            margin-top: 10px;
            font-family: monospace;
        }

        /* FOOTER (Instruktur & Logo Kanan) */
        .footer-fixed {
            position: absolute;
            bottom: 60px;
            left: 40px;
            right: 40px;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }

        .col-sign {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
        }

        /* Styling Gambar (Logo & QR) */
        .img-signature-area {
            height: 90px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 10px;
        }

        .qr-img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto;
        }

        .logo-img {
            width: 150px; /* Ukuran Logo Diperbesar */
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .sign-line {
            border-top: 2px solid #333;
            width: 60%;
            margin: 0 auto 8px auto;
        }

        .sign-name { font-weight: bold; font-size: 16px; color: #333; }
        .sign-role { font-size: 14px; color: #777; }

    </style>
</head>
<body>

    <div class="border-frame">
        
        <div class="content-wrapper">
            <div class="header-title">SERTIFIKAT PENYELESAIAN</div>

            <div class="text-regular">Dengan ini menyatakan bahwa:</div>

            <div class="student-name">
                {{ $user->full_name }}
            </div>

            <div class="text-regular">Telah berhasil menyelesaikan kursus:</div>

            <div class="course-title">
                "{{ $course->title }}"
            </div>

            <div class="date-text">
                Diterbitkan pada tanggal {{ $certificate->issued_at->format('d F Y') }}
            </div>

            <div class="cert-no">
                No. Sertifikat: {{ $certificate->certificate_number }}
            </div>
        </div>

        <div class="footer-fixed">
            <table class="sign-table">
                <tr>
                    <td class="col-sign">
                        <div class="img-signature-area">
                            @if(!empty($qrCodeImage))
                                <img src="{{ $qrCodeImage }}" class="qr-img" alt="QR Code">
                            @else
                                <div style="width:80px; height:80px; margin:0 auto;"></div> 
                            @endif
                        </div>
                        
                        <div class="sign-line"></div>
                        <div class="sign-name">{{ $instructor ? $instructor->full_name : 'Instruktur' }}</div>
                        <div class="sign-role">Instruktur</div>
                    </td>

                    <td class="col-sign">
                        <div class="img-signature-area">
                            @if(!empty($logoBase64))
                                <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                            @else
                                <div style="font-size:10px; color:red;">Logo Not Found</div>
                            @endif
                        </div>

                        <div class="sign-line"></div>
                        <div class="sign-name">abikoding by DC Tech</div>
                        <div class="sign-role">Platform Pendidikan</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>