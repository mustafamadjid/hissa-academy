<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Sertifikat HISSA</title>
    <style>
        body {
            margin: 0;
            background: #f6f7f9;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 720px;
            margin: 48px auto;
            padding: 32px;
            background: #ffffff;
            border: 1px solid #d9dee7;
            border-radius: 8px;
        }

        h1 {
            margin: 0 0 24px;
            font-size: 28px;
        }

        dl {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 12px 20px;
            margin: 0;
        }

        dt {
            color: #5d6879;
            font-weight: 700;
        }

        dd {
            margin: 0;
        }
    </style>
</head>
<body>
    <main>
        @if ($certificate === null)
            <h1>Sertifikat tidak ditemukan</h1>
            <p>Nomor sertifikat {{ $certificateNumber }} tidak terdaftar di sistem HISSA.</p>
        @else
            <h1>Hasil Verifikasi Sertifikat</h1>
            <dl>
                <dt>Nomor Sertifikat</dt>
                <dd>{{ $certificate->certificate_number }}</dd>

                <dt>Status</dt>
                <dd>{{ $certificate->status }}</dd>

                <dt>Nama Peserta</dt>
                <dd>{{ $certificate->user?->full_name }}</dd>

                <dt>Kursus</dt>
                <dd>{{ $certificate->course?->course_name }}</dd>

                <dt>Tanggal Terbit</dt>
                <dd>{{ $certificate->issued_at?->format('d M Y') }}</dd>
            </dl>
        @endif
    </main>
</body>
</html>
