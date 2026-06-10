<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengeluaran Kas #{{ $pengeluaran->id_keluar }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #eef2f7;
            color: #111827;
        }

        .toolbar {
            width: 210mm;
            margin: 6px auto 0;
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            padding-left: 4mm;
        }

        .btn {
            border: none;
            border-radius: 9px;
            padding: 9px 14px;
            cursor: pointer;
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #0f766e;
            color: #ffffff;
        }

        .btn-back {
            background: #e5e7eb;
            color: #111827;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 2mm 2mm 0 2mm;
            position: relative;
        }

        .slip {
            width: 100%;
            border: 1.5px dashed #bfc8d4;
            padding: 7px 8px;
            position: relative;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.045;
            z-index: 0;
        }

        .watermark img {
            width: 210px;
            height: 210px;
            object-fit: contain;
        }

        .content {
            position: relative;
            z-index: 2;
        }

        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 7px;
            margin-bottom: 9px;
            display: grid;
            grid-template-columns: 64px 1fr 155px;
            gap: 9px;
            align-items: start;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-wrap img {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .yayasan h1 {
            margin: 0;
            font-size: 15px;
            line-height: 1.22;
            text-transform: uppercase;
            color: #111827;
            font-weight: 900;
        }

        .yayasan p {
            margin: 3px 0 0;
            font-size: 10.5px;
            color: #4b5563;
        }

        .meta-top {
            text-align: right;
            font-size: 10.5px;
            line-height: 1.45;
        }

        .meta-top strong {
            color: #e11d48;
        }

        .title {
            text-align: center;
            margin: 8px 0 11px;
        }

        .title h2 {
            margin: 0;
            font-size: 14.5px;
            text-transform: uppercase;
            color: #111827;
            text-decoration: underline;
            letter-spacing: .03em;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 13px;
            font-size: 12px;
        }

        .info-table td {
            padding: 3.5px 2px;
            vertical-align: top;
        }

        .info-table td.label {
            width: 122px;
            color: #374151;
            text-transform: uppercase;
            font-size: 10.5px;
            font-weight: 700;
        }

        .info-table td.colon {
            width: 10px;
        }

        .value-text {
            font-weight: 800;
            color: #111827;
        }

        .amount-box {
            display: inline-block;
            padding: 6px 12px;
            border: 1.3px solid #0f766e;
            background: #f8fffd;
            border-radius: 4px;
            font-weight: 900;
            color: #111827;
        }

        .spell-box {
            display: inline-block;
            background: #f3f4f6;
            padding: 5px 10px;
            border-radius: 4px;
            font-style: italic;
            font-size: 11px;
            color: #111827;
            font-weight: 700;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 10.5px;
        }

        .sign-table th,
        .sign-table td {
            border: 1px solid #374151;
            text-align: center;
            padding: 5px 4px;
            vertical-align: bottom;
        }

        .sign-table th {
            background: #f3f4f6;
            text-transform: uppercase;
            font-size: 9.5px;
            font-weight: 900;
        }

        .sign-space {
            height: 50px;
            font-weight: 800;
        }

        .text-left {
            text-align: left !important;
            line-height: 1.7;
            font-weight: 700;
        }

        .footer-note {
            margin-top: 8px;
            font-size: 9.5px;
            color: #6b7280;
        }

        @media print {
            html,
            body {
                width: 210mm;
                height: 297mm;
                background: #ffffff;
                margin: 0;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .paper {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 2mm 2mm 0 2mm;
                box-shadow: none;
            }

            .slip {
                width: 100%;
                padding: 7px 8px;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()" class="btn btn-print">
            🖨 Cetak Sekarang
        </button>

        <a href="{{ route('pengeluaran.index') }}" class="btn btn-back">
            Kembali
        </a>
    </div>

    <div class="paper">
        <div class="slip">
            <div class="watermark">
                <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo">
            </div>

            <div class="content">
                <div class="header">
                    <div class="logo-wrap">
                        <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo Yayasan">
                    </div>

                    <div class="yayasan">
                        <h1>Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah</h1>
                        <p>Jl. KH. Abdul Hamid Rohman No. 01, Kedungsuko, Bangsalsari, Jember</p>
                    </div>

                    <div class="meta-top">
                        <div>No: <strong>{{ $nomorBukti }}</strong></div>
                        <div>Tgl: {{ \Carbon\Carbon::parse($pengeluaran->tgl_keluar)->format('d-m-Y') }}</div>
                    </div>
                </div>

                <div class="title">
                    <h2>Bukti Pengeluaran Kas</h2>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label">Dibayarkan ke</td>
                        <td class="colon">:</td>
                        <td class="value-text">
                            {{ strtoupper($pengeluaran->penerima) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Unit / Bagian</td>
                        <td class="colon">:</td>
                        <td class="value-text">
                            {{ strtoupper($pengeluaran->unit) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Jumlah Uang</td>
                        <td class="colon">:</td>
                        <td>
                            <span class="amount-box">
                                Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }},-
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Terbilang</td>
                        <td class="colon">:</td>
                        <td>
                            <span class="spell-box">
                                # {{ $terbilang }} #
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Keterangan</td>
                        <td class="colon">:</td>
                        <td class="value-text">
                            {{ strtoupper($pengeluaran->uraian) }}
                        </td>
                    </tr>
                </table>

                <table class="sign-table">
                    <tr>
                        <th>Disetujui Oleh</th>
                        <th>Diterima Oleh</th>
                        <th>Dibukukan Oleh</th>
                        <th>Cek Akun / Validasi</th>
                    </tr>

                    <tr>
                        <td class="sign-space">
                            ( Ag. Ahmad Hulqi Khoir )
                        </td>

                        <td class="sign-space">
                            ( {{ strtoupper($pengeluaran->penerima) }} )
                        </td>

                        <td class="sign-space">
                            ( Teller / Admin )
                        </td>

                        <td class="text-left">
                            DEBET : ............................<br><br>
                            KREDIT : ...........................
                        </td>
                    </tr>
                </table>

                <div class="footer-note">
                    Dicetak pada {{ now()->format('d-m-Y H:i') }} WIB • SIKEU MHI V2
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>