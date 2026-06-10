<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PPDB YPP MHI</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --tosca: #0f9f92;
            --tosca-dark: #0f766e;
            --tosca-soft: #e7faf7;
            --pink: #e3456d;
            --pink-dark: #be185d;
            --pink-soft: #ffe7ee;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f4f8fb;
            --shadow: 0 20px 50px rgba(15, 23, 42, .10);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 159, 146, .14), transparent 34%),
                radial-gradient(circle at top right, rgba(227, 69, 109, .12), transparent 32%),
                var(--bg);
            color: var(--text);
        }

        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 28px 16px;
        }

        .success-card {
            width: min(760px, 100%);
            background: rgba(255, 255, 255, .97);
            border: 1px solid var(--border);
            border-radius: 34px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .head {
            background: linear-gradient(135deg, var(--tosca-dark), var(--tosca));
            color: white;
            text-align: center;
            padding: 34px 22px 30px;
            position: relative;
            overflow: hidden;
        }

        .head::before,
        .head::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
        }

        .head::before {
            width: 190px;
            height: 190px;
            left: -74px;
            top: -78px;
        }

        .head::after {
            width: 170px;
            height: 170px;
            right: -64px;
            bottom: -70px;
        }

        .head-content {
            position: relative;
            z-index: 2;
        }

        .logo-wrap {
            width: 84px;
            height: 84px;
            border-radius: 28px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 18px 38px rgba(0, 0, 0, .16);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            margin: 0 auto 14px;
        }

        .logo-wrap img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .yayasan-name {
            margin: 0;
            font-size: 13px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .pesantren-name {
            margin: 6px 0 16px;
            font-size: 21px;
            font-weight: 950;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .check {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .20);
            border: 1px solid rgba(255, 255, 255, .34);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 34px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .head p {
            margin: 0;
            font-size: 14px;
            line-height: 1.75;
            opacity: .94;
        }

        .body {
            padding: 26px;
        }

        .number-box {
            border: 1px dashed rgba(15, 118, 110, .38);
            background: var(--tosca-soft);
            border-radius: 24px;
            padding: 20px;
            text-align: center;
            margin-bottom: 18px;
        }

        .number-box span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .number-box strong {
            color: var(--tosca-dark);
            font-size: 28px;
            font-weight: 950;
            letter-spacing: .02em;
            word-break: break-word;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-item {
            border: 1px solid var(--border);
            background: #ffffff;
            border-radius: 18px;
            padding: 14px;
        }

        .info-item span {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 850;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-item strong {
            color: var(--text);
            font-size: 14px;
            font-weight: 900;
        }

        .next-step {
            border-radius: 22px;
            background: #fff7fb;
            border: 1px solid #ffd1df;
            padding: 16px;
            color: #8a1241;
            font-size: 13px;
            line-height: 1.8;
            font-weight: 650;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 22px;
        }

        .btn {
            min-height: 48px;
            border-radius: 16px;
            padding: 0 18px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            box-shadow: 0 14px 24px rgba(15, 118, 110, .18);
        }

        .btn-light {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid var(--border);
        }

        @media(max-width: 640px) {
            .success-card {
                border-radius: 28px;
            }

            .head {
                padding: 28px 18px 24px;
            }

            .logo-wrap {
                width: 74px;
                height: 74px;
                border-radius: 24px;
            }

            .logo-wrap img {
                width: 56px;
                height: 56px;
            }

            .pesantren-name {
                font-size: 17px;
            }

            h1 {
                font-size: 24px;
            }

            .body {
                padding: 18px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .number-box strong {
                font-size: 22px;
            }

            .actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .btn {
                width: 100%;
            }
        }

        @media print {
            body {
                background: white;
            }

            .page {
                display: block;
                padding: 0;
            }

            .success-card {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="success-card">
            <div class="head">
                <div class="head-content">
                    <div class="logo-wrap">
                        <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo YPP MHI">
                    </div>

                    <p class="yayasan-name">Yayasan Pendidikan Pesantren</p>
                    <div class="pesantren-name">Mamba'ul Khoiriyatil Islamiyah</div>

                    <div class="check">✓</div>
                    <h1>Pendaftaran Berhasil</h1>
                    <p>
                        Terima kasih. Formulir PPDB online sudah masuk ke sistem dan akan diverifikasi oleh panitia.
                    </p>
                </div>
            </div>

            <div class="body">
                <div class="number-box">
                    <span>Nomor Pendaftaran</span>
                    <strong>{{ $pendaftar->no_daftar ?? '-' }}</strong>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span>Nama Calon Santri</span>
                        <strong>{{ $pendaftar->nama_lengkap ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Tanggal Daftar</span>
                        <strong>
                            @if (!empty($pendaftar->tgl_daftar))
                                {{ \Carbon\Carbon::parse($pendaftar->tgl_daftar)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Jenjang</span>
                        <strong>{{ $pendaftar->jenjang_sekolah ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Status Seleksi</span>
                        <strong>{{ $pendaftar->status_seleksi ?? 'Pending' }}</strong>
                    </div>
                </div>

                <div class="next-step">
                    <strong>Langkah berikutnya:</strong><br>
                    Simpan nomor pendaftaran ini. Panitia PPDB akan menghubungi wali santri melalui nomor HP yang telah
                    diisi
                    untuk proses verifikasi data dan informasi lanjutan.
                </div>

                <div class="actions">
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        🖨 Cetak Bukti
                    </button>

                    <a href="{{ route('ppdb-online.form') }}" class="btn btn-light">
                        Isi Formulir Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
