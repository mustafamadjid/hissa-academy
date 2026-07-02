<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $certificate->studentName }}</title>
    <style>
        @page { size: A4 landscape; margin: 0; }

        * { box-sizing: border-box; }

        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: "DejaVu Sans", sans-serif;
            color: #101828;
            background: #fff;
        }

        .certificate {
            display: block;
            position: relative;
            width: 1070px;
            height: 710px;
            padding: 24px;
            overflow: hidden;
            background: #f5f8f4;
        }

        .outer-border {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 9px;
            border: 5px solid #064e3b;
            background: #fff;
        }

        .inner-border {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border: 2px solid #087443;
            background: #fff;
        }

        .accent {
            position: absolute;
            right: 0;
            left: 0;
            height: 12px;
            background: #064e3b;
        }

        .accent-top { top: 0; }
        .accent-bottom { bottom: 0; }

        .corner {
            position: absolute;
            width: 112px;
            height: 112px;
            border-color: #087443;
            opacity: .14;
        }

        .corner-tl { top: 28px; left: 28px; border-top: 16px solid #087443; border-left: 16px solid #087443; }
        .corner-tr { top: 28px; right: 28px; border-top: 16px solid #087443; border-right: 16px solid #087443; }
        .corner-bl { bottom: 28px; left: 28px; border-bottom: 16px solid #087443; border-left: 16px solid #087443; }
        .corner-br { right: 28px; bottom: 28px; border-right: 16px solid #087443; border-bottom: 16px solid #087443; }

        .content {
            position: relative;
            z-index: 2;
            padding: 30px 80px 0;
            text-align: center;
        }

        .brand-logo {
            display: block;
            width: 62px;
            height: 62px;
            margin: 0 auto 6px;
        }

        .brand-name {
            margin: 0;
            color: #064e3b;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .eyebrow {
            margin: 13px 0 4px;
            color: #087443;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            color: #064e3b;
            font-family: "DejaVu Serif", serif;
            font-size: 42px;
            letter-spacing: 5px;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .divider {
            width: 88px;
            height: 3px;
            margin: 13px auto 15px;
            background: #087443;
        }

        .presented-to, .description {
            margin: 0;
            color: #667085;
            font-size: 13px;
            line-height: 1.5;
        }

        .student-name {
            margin: 6px auto 5px;
            color: #101828;
            font-family: "DejaVu Serif", serif;
            font-size: 31px;
            font-weight: bold;
            line-height: 1.25;
        }

        .student-line {
            width: 490px;
            height: 1px;
            margin: 0 auto 12px;
            background: #d0d5dd;
        }

        .course-name {
            margin: 5px auto 12px;
            color: #064e3b;
            font-size: 21px;
            font-weight: bold;
            line-height: 1.35;
        }

        .meta-table {
            width: 500px;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .meta-table td { padding: 3px 7px; font-size: 11px; }
        .meta-label { width: 50%; color: #667085; text-align: right; }
        .meta-value { width: 50%; font-weight: bold; text-align: left; }

        .footer {
            position: absolute;
            right: 52px;
            bottom: 34px;
            left: 52px;
            z-index: 3;
        }

        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; }
        .certificate-info { width: 36%; text-align: left; }
        .issuer { width: 38%; text-align: center; }
        .qr-wrapper { width: 26%; text-align: right; }

        .info-label {
            margin-bottom: 4px;
            color: #667085;
            font-size: 8px;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .certificate-number { color: #064e3b; font-size: 11px; font-weight: bold; }
        .signature-space { height: 30px; }
        .signature-line { width: 180px; height: 1px; margin: 0 auto 6px; background: #98a2b3; }
        .issuer-name { margin: 0; font-size: 11px; font-weight: bold; }
        .issuer-title { margin: 2px 0 0; color: #667085; font-size: 8px; }
        .qr-code { width: 72px; height: 72px; }
        .qr-caption { margin-top: 3px; color: #667085; font-size: 7px; line-height: 1.3; }

        .verification-note {
            position: absolute;
            right: 70px;
            bottom: 17px;
            left: 70px;
            color: #98a2b3;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
    @endphp

    <div class="certificate">
        <div class="outer-border">
            <div class="inner-border">
                <div class="accent accent-top"></div>
                <div class="accent accent-bottom"></div>
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>

                <div class="content">
                    @if ($logoDataUri)
                        <img class="brand-logo" src="{{ $logoDataUri }}" alt="Logo HISSA Academy">
                    @endif
                    <p class="brand-name">HISSA Academy</p>
                    <p class="eyebrow">Certificate of Completion</p>
                    <h1>Sertifikat</h1>
                    <div class="divider"></div>
                    <p class="presented-to">Sertifikat ini diberikan kepada</p>
                    <div class="student-name">{{ $certificate->studentName }}</div>
                    <div class="student-line"></div>
                    <p class="description">sebagai bentuk penghargaan atas keberhasilannya menyelesaikan seluruh rangkaian pembelajaran pada course</p>
                    <div class="course-name">{{ $certificate->courseName }}</div>

                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Tanggal diterbitkan:</td>
                            <td class="meta-value">{{ $certificate->issuedAt->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Berlaku sampai:</td>
                            <td class="meta-value">{{ $certificate->validUntil->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <table class="footer-table">
                        <tr>
                            <td class="certificate-info">
                                <div class="info-label">Nomor Sertifikat</div>
                                <div class="certificate-number">{{ $certificate->certificateNumber }}</div>
                            </td>
                            <td class="issuer">
                                <div class="signature-space"></div>
                                <div class="signature-line"></div>
                                <p class="issuer-name">HISSA Academy</p>
                                <p class="issuer-title">Penyelenggara Program</p>
                            </td>
                            <td class="qr-wrapper">
                                <img class="qr-code" src="{{ $certificate->qrCodeDataUri }}" alt="QR Code verifikasi sertifikat">
                                <div class="qr-caption">Pindai untuk memverifikasi<br>keaslian sertifikat</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="verification-note">
                    Sertifikat ini diterbitkan secara digital oleh HISSA Academy. Keasliannya dapat diverifikasi melalui QR Code atau nomor sertifikat.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
