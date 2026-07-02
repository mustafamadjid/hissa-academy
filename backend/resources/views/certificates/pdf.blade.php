<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 22px; }
        body { margin: 0; color: #183153; font-family: DejaVu Sans, sans-serif; }
        .frame { height: 510px; border: 10px solid #183153; padding: 30px 48px; text-align: center; }
        .inner { height: 490px; border: 2px solid #c89b3c; position: relative; }
        h1 { margin: 36px 0 8px; font-size: 42px; letter-spacing: 4px; text-transform: uppercase; }
        .subtitle { color: #c89b3c; font-size: 18px; letter-spacing: 2px; }
        .student { margin: 28px 0 10px; font-size: 34px; font-weight: bold; }
        .course { margin: 10px 0 28px; font-size: 24px; font-weight: bold; }
        .meta { font-size: 13px; line-height: 1.7; }
        .qr { position: absolute; right: 30px; bottom: 20px; width: 105px; }
        .verify { position: absolute; right: 18px; bottom: 7px; width: 130px; font-size: 8px; color: #555; }
        .number { position: absolute; left: 30px; bottom: 34px; text-align: left; font-size: 12px; }
    </style>
</head>
<body>
<div class="frame">
    <div class="inner">
        <h1>Sertifikat</h1>
        <div class="subtitle">PENYELESAIAN COURSE</div>
        <p>Diberikan kepada</p>
        <div class="student">{{ $certificate->studentName }}</div>
        <p>atas keberhasilan menyelesaikan course</p>
        <div class="course">{{ $certificate->courseName }}</div>
        <div class="meta">
            Diterbitkan: {{ $certificate->issuedAt->format('d F Y') }}<br>
            Berlaku sampai: {{ $certificate->validUntil->format('d F Y') }}
        </div>
        <div class="number">Nomor sertifikat<br><strong>{{ $certificate->certificateNumber }}</strong></div>
        <img class="qr" src="{{ $certificate->qrCodeDataUri }}" alt="QR verifikasi">
        <div class="verify">Pindai untuk verifikasi</div>
    </div>
</div>
</body>
</html>
