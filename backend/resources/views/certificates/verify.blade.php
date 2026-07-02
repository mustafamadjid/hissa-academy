<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $certificate === null
            ? 'Sertifikat Tidak Ditemukan'
            : 'Verifikasi Sertifikat HISSA'
        }}
    </title>

    <style>
        :root {
            --primary-green: #087443;
            --primary-dark-green: #064e3b;

            --background: #f8faf6;
            --surface: #ffffff;
            --surface-muted: #f2f7f3;

            --text-primary: #101828;
            --text-secondary: #667085;

            --border: #dfe6e1;

            --success-background: #eaf7ef;
            --success-text: #066338;

            --danger-background: #fef2f2;
            --danger-border: #fecaca;
            --danger-text: #b42318;

            --shadow:
                0 20px 50px rgba(16, 24, 40, 0.08),
                0 4px 12px rgba(16, 24, 40, 0.04);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--background);
            color: var(--text-primary);
            font-family:
                Inter,
                "Plus Jakarta Sans",
                Arial,
                sans-serif;
        }

        .page {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 48px 20px;
        }

        .container {
            width: 100%;
            max-width: 860px;
        }

       .brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 24px;
}

.brand-icon {
    display: flex;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
}

.brand-icon img {
    display: block;
    width: 120%;
    height: 100%;
    object-fit: contain;
}

.brand-name {
    margin: 0;
    color: #064e3b;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

        .card {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .card-header {
            padding: 32px;
            border-bottom: 1px solid var(--border);
        }

        .header-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
        }

        .header-main {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .status-icon {
            display: flex;
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
        }

        .status-icon svg {
            width: 28px;
            height: 28px;
        }

        .status-icon.valid {
            background: var(--success-background);
            color: var(--primary-green);
        }

        .status-icon.invalid {
            background: var(--danger-background);
            color: var(--danger-text);
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--primary-green);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.25;
            letter-spacing: -0.03em;
        }

        .subtitle {
            max-width: 590px;
            margin: 12px 0 0;
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.7;
        }

        .badge {
            display: inline-flex;
            flex-shrink: 0;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
        }

        .badge.valid {
            background: var(--success-background);
            color: var(--success-text);
        }

        .badge.valid .badge-dot {
            background: var(--primary-green);
        }

        .badge.invalid {
            background: var(--danger-background);
            color: var(--danger-text);
        }

        .badge.invalid .badge-dot {
            background: var(--danger-text);
        }

        .card-body {
            padding: 32px;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin: 0;
        }

        .detail-item {
            min-width: 0;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface-muted);
        }

        .detail-item.full {
            grid-column: 1 / -1;
        }

        dt {
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
        }

        dd {
            margin: 0;
            color: var(--text-primary);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.5;
            overflow-wrap: break-word;
        }

        .footer-note {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 24px;
            padding: 16px;
            border: 1px solid rgba(8, 116, 67, 0.16);
            border-radius: 16px;
            background: rgba(8, 116, 67, 0.06);
        }

        .footer-note svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 1px;
            color: var(--primary-green);
        }

        .footer-note p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.65;
        }

        .not-found-card {
            padding: 48px 32px;
            text-align: center;
        }

        .not-found-icon {
            display: flex;
            width: 72px;
            height: 72px;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border-radius: 22px;
            background: var(--danger-background);
            color: var(--danger-text);
        }

        .not-found-icon svg {
            width: 34px;
            height: 34px;
        }

        .not-found-card h1 {
            font-size: 28px;
        }

        .not-found-card p {
            max-width: 560px;
            margin: 14px auto 0;
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.7;
        }

        .certificate-number {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 15px;
            border: 1px solid var(--danger-border);
            border-radius: 12px;
            background: var(--danger-background);
            color: var(--danger-text);
            font-family: Consolas, Monaco, monospace;
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .page-footer {
            margin-top: 20px;
            color: var(--text-secondary);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 700px) {
            .page {
                align-items: flex-start;
                padding: 28px 16px;
            }

            .card {
                border-radius: 20px;
            }

            .card-header,
            .card-body {
                padding: 24px;
            }

            .header-content {
                flex-direction: column;
            }

            .header-main {
                flex-direction: column;
            }

            h1,
            .not-found-card h1 {
                font-size: 24px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .detail-item.full {
                grid-column: auto;
            }

            .not-found-card {
                padding: 40px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="container">
            <div class="brand">
                <div class="brand-icon" aria-hidden="true">
                    <img src="{{asset('images/logo.webp')}}" alt="">
                </div>

                <p class="brand-name">HISSA Academy</p>
            </div>

            @if ($certificate === null)
                <main class="card not-found-card">
                    <div class="not-found-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M9.5 9.5l5 5"></path>
                            <path d="M14.5 9.5l-5 5"></path>
                        </svg>
                    </div>

                    <p class="eyebrow">Hasil Verifikasi</p>

                    <h1>Sertifikat tidak ditemukan</h1>

                    <p>
                        Nomor sertifikat yang Anda masukkan tidak terdaftar
                        dalam sistem resmi HISSA Academy. Periksa kembali
                        susunan huruf, angka, dan tanda hubung.
                    </p>

                    <div class="certificate-number">
                        {{ $certificateNumber }}
                    </div>
                </main>
            @else
                @php
                    $isValid = $certificate->status === 'issued';
                @endphp

                <main class="card">
                    <header class="card-header">
                        <div class="header-content">
                            <div class="header-main">
                                <div
                                    class="status-icon {{ $isValid ? 'valid' : 'invalid' }}"
                                    aria-hidden="true"
                                >
                                    @if ($isValid)
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M9 12l2 2 4-4"></path>
                                            <path d="M12 3l7 4v5c0 4.4-3 7.8-7 9-4-1.2-7-4.6-7-9V7l7-4z"></path>
                                        </svg>
                                    @else
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M12 9v4"></path>
                                            <path d="M12 17h.01"></path>
                                            <path d="M10.3 3.8L2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <p class="eyebrow">Hasil Verifikasi</p>

                                    <h1>
                                        {{ $isValid
                                            ? 'Sertifikat terverifikasi'
                                            : 'Sertifikat tidak berlaku'
                                        }}
                                    </h1>

                                    <p class="subtitle">
                                        {{ $isValid
                                            ? 'Data sertifikat sesuai dengan catatan resmi HISSA Academy.'
                                            : 'Sertifikat ditemukan, tetapi statusnya sudah tidak aktif atau telah dicabut.'
                                        }}
                                    </p>
                                </div>
                            </div>

                            <span class="badge {{ $isValid ? 'valid' : 'invalid' }}">
                                <span class="badge-dot"></span>

                                {{ $isValid ? 'Valid' : 'Tidak Berlaku' }}
                            </span>
                        </div>
                    </header>

                    <section class="card-body">
                        <dl class="details">
                            <div class="detail-item full">
                                <dt>Nomor Sertifikat</dt>
                                <dd>{{ $certificate->certificate_number }}</dd>
                            </div>

                            <div class="detail-item">
                                <dt>Nama Peserta</dt>
                                <dd>{{ $certificate->user?->full_name ?? '-' }}</dd>
                            </div>

                            <div class="detail-item">
                                <dt>Kursus</dt>
                                <dd>{{ $certificate->course?->course_name ?? '-' }}</dd>
                            </div>

                            <div class="detail-item">
                                <dt>Status Sertifikat</dt>
                                <dd>
                                    {{ $isValid ? 'Diterbitkan' : ucfirst($certificate->status) }}
                                </dd>
                            </div>

                            <div class="detail-item">
                                <dt>Tanggal Terbit</dt>
                                <dd>
                                    {{ $certificate->issued_at?->translatedFormat('d F Y') ?? '-' }}
                                </dd>
                            </div>

                            @if ($certificate->status === 'revoked')
                                <div class="detail-item full">
                                    <dt>Tanggal Dicabut</dt>
                                    <dd>
                                        {{ $certificate->revoked_at?->translatedFormat('d F Y') ?? '-' }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="footer-note">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 11v5"></path>
                                <path d="M12 8h.01"></path>
                            </svg>

                            <p>
                                Informasi pada halaman ini berasal dari sistem
                                verifikasi resmi HISSA Academy. Pastikan alamat
                                halaman yang dibuka berasal dari domain resmi.
                            </p>
                        </div>
                    </section>
                </main>
            @endif

            <p class="page-footer">
                &copy; {{ now()->year }} HISSA Academy. Semua hak dilindungi.
            </p>
        </div>
    </div>
</body>
</html>
```
