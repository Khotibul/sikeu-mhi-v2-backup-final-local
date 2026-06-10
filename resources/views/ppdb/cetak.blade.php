<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran PPDB - {{ $pendaftar->nama_lengkap ?? '-' }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #e5e7eb;
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
        }

        .screen-action {
            padding: 12px;
            display: flex;
            gap: 8px;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 9px 13px;
            background: #0f766e;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-light {
            background: #334155;
        }

        .paper {
            width: 194mm;
            min-height: 281mm;
            margin: 0 auto 20px;
            background: #ffffff;
            padding: 8mm;
        }

        .bukti {
            border: 2px solid #0f8f83;
            padding: 5mm;
            min-height: 128mm;
            position: relative;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: .045;
            pointer-events: none;
            z-index: 0;
        }

        .watermark img {
            width: 86mm;
            height: 86mm;
            object-fit: contain;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .header {
            display: grid;
            grid-template-columns: 22mm 1fr 38mm;
            gap: 4mm;
            align-items: center;
            border-bottom: 2px solid #0f8f83;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .logo {
            width: 20mm;
            height: 20mm;
            object-fit: contain;
        }

        .school {
            text-align: center;
            line-height: 1.25;
        }

        .school .yayasan {
            font-size: 12px;
            font-weight: 900;
            color: #0f8f83;
            text-transform: uppercase;
        }

        .school .nama {
            font-size: 15px;
            font-weight: 950;
            color: #e3456d;
            text-transform: uppercase;
        }

        .school .alamat {
            font-size: 9px;
            color: #334155;
            font-weight: 700;
        }

        .nomor-box {
            border: 1.5px solid #0f8f83;
            border-radius: 8px;
            padding: 6px;
            text-align: center;
            font-size: 9px;
            line-height: 1.35;
        }

        .nomor-box strong {
            display: block;
            color: #e3456d;
            font-size: 11px;
            margin-top: 2px;
        }

        .title {
            text-align: center;
            margin: 4mm 0 3mm;
            font-size: 13px;
            font-weight: 950;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .status-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4mm;
            align-items: center;
            margin-bottom: 3mm;
        }

        .status-note {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
            font-weight: 700;
        }

        .status-badge {
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            border: 1px solid #0f8f83;
            color: #0f766e;
            background: #e7f9f6;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-badge.ditolak {
            color: #be185d;
            background: #ffe4ec;
            border-color: #e3456d;
        }

        .status-badge.pending {
            color: #92400e;
            background: #fff7d6;
            border-color: #f59e0b;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3mm;
        }

        .box {
            border: 1px solid #94a3b8;
            border-radius: 8px;
            overflow: hidden;
            background: rgba(255, 255, 255, .88);
        }

        .box-title {
            background: #e7f9f6;
            color: #0f766e;
            padding: 6px 8px;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            border-bottom: 1px solid #94a3b8;
        }

        .box-body {
            padding: 7px 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
            line-height: 1.35;
        }

        .info-table td:first-child {
            width: 34%;
            color: #475569;
            font-weight: 800;
        }

        .info-table td:nth-child(2) {
            width: 8px;
            text-align: center;
            color: #64748b;
        }

        .info-table td:last-child {
            font-weight: 900;
            color: #0f172a;
        }

        .full {
            grid-column: 1 / -1;
        }

        .file-list {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }

        .file-item {
            border: 1px dashed #94a3b8;
            border-radius: 7px;
            padding: 6px;
            font-size: 9px;
            line-height: 1.3;
        }

        .file-item strong {
            display: block;
            color: #0f766e;
            margin-bottom: 2px;
        }

        .note {
            margin-top: 3mm;
            border: 1px dashed #0f8f83;
            border-radius: 8px;
            padding: 7px 8px;
            font-size: 9.5px;
            line-height: 1.55;
            color: #334155;
            background: #fbfffe;
        }

        .signature {
            display: grid;
            grid-template-columns: 1fr 48mm;
            gap: 8mm;
            margin-top: 6mm;
            align-items: end;
        }

        .rules {
            font-size: 9px;
            line-height: 1.5;
            color: #475569;
        }

        .sign-box {
            text-align: center;
            font-size: 10px;
            line-height: 1.4;
            color: #0f172a;
        }

        .sign-space {
            height: 22mm;
        }

        .sign-name {
            display: inline-block;
            border-top: 1px solid #0f172a;
            padding-top: 3px;
            font-weight: 950;
            min-width: 38mm;
        }

        .footer {
            margin-top: 4mm;
            border-top: 1px solid #0f8f83;
            padding-top: 2mm;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 8.5px;
            color: #64748b;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .screen-action {
                display: none !important;
            }

            .paper {
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }

            .bukti {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    @php
        $status = $pendaftar->status_seleksi ?? 'Pending';
        $statusClass =
            strtolower($status) === 'ditolak' ? 'ditolak' : (strtolower($status) === 'pending' ? 'pending' : '');
        $jk = ($pendaftar->jk ?? '') === 'L' ? 'Laki-laki' : (($pendaftar->jk ?? '') === 'P' ? 'Perempuan' : '-');
        $tglDaftar = !empty($pendaftar->tgl_daftar)
            ? \Carbon\Carbon::parse($pendaftar->tgl_daftar)->format('d-m-Y')
            : '-';
        $tglLahir = !empty($pendaftar->tgl_lahir) ? \Carbon\Carbon::parse($pendaftar->tgl_lahir)->format('d-m-Y') : '-';
        $noDaftar = $pendaftar->no_daftar ?? 'PPDB-' . str_pad($pendaftar->id_daftar ?? 0, 5, '0', STR_PAD_LEFT);

        $dokumen = [
            'KK' => $pendaftar->file_kk ?? null,
            'KTP Wali' => $pendaftar->file_ktp ?? null,
            'Foto' => $pendaftar->file_foto ?? null,
            'Ijazah' => $pendaftar->file_ijazah ?? null,
        ];
    @endphp

    <div class="screen-action">
        <button type="button" class="btn" onclick="window.print()">🖨 Cetak Bukti</button>
        <a href="{{ route('ppdb.index') }}" class="btn btn-light">← Kembali</a>
    </div>

    <div class="paper">
        <div class="bukti">
            <div class="watermark">
                <img src="{{ asset('images/logo-mhi.png') }}" alt="Watermark">
            </div>

            <div class="content">
                <div class="header">
                    <div>
                        <img class="logo" src="{{ asset('images/logo-mhi.png') }}" alt="Logo">
                    </div>

                    <div class="school">
                        <div class="yayasan">Yayasan Pendidikan Pesantren</div>
                        <div class="nama">Mamba'ul Khoiriyatil Islamiyah</div>
                        <div class="alamat">
                            Jl. KH. Abdul Halim Rohman No. 01, Kedungsuko, Bangsalsari, Jember
                        </div>
                    </div>

                    <div class="nomor-box">
                        No. Pendaftaran
                        <strong>{{ $noDaftar }}</strong>
                        <div>Tgl: {{ $tglDaftar }}</div>
                    </div>
                </div>

                <div class="title">Bukti Pendaftaran PPDB Santri Baru</div>

                <div class="status-row">
                    <div class="status-note">
                        Bukti ini diterbitkan sebagai tanda bahwa calon santri telah terdaftar dalam sistem PPDB
                        Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah.
                    </div>

                    <div class="status-badge {{ $statusClass }}">
                        {{ $status }}
                    </div>
                </div>

                <div class="grid">
                    <div class="box">
                        <div class="box-title">Identitas Calon Santri</div>
                        <div class="box-body">
                            <table class="info-table">
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>NISN</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->nisn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>:</td>
                                    <td>{{ $jk }}</td>
                                </tr>
                                <tr>
                                    <td>Tempat, Tgl Lahir</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->tempat_lahir ?? '-' }}, {{ $tglLahir }}</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-title">Data Orang Tua / Wali</div>
                        <div class="box-body">
                            <table class="info-table">
                                <tr>
                                    <td>Nama Ayah</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->nama_ayah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Nama Ibu</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->nama_ibu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>No. HP Ortu</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->no_hp_ortu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Asal Sekolah</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->asal_sekolah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Tahun Ajaran</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->tahun_ajaran ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-title">Pilihan Pendidikan</div>
                        <div class="box-body">
                            <table class="info-table">
                                <tr>
                                    <td>Jenjang Sekolah</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->jenjang_sekolah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Jurusan</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->jurusan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Kelas Diniyah</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->kelas_diniyah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Status Pondok</td>
                                    <td>:</td>
                                    <td>{{ $pendaftar->status_pondok ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-title">Status Berkas</div>
                        <div class="box-body">
                            <div class="file-list">
                                @foreach ($dokumen as $nama => $file)
                                    <div class="file-item">
                                        <strong>{{ $nama }}</strong>
                                        {{ !empty($file) ? 'Ada' : 'Belum Ada' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="note full">
                        <strong>Catatan:</strong>
                        Bukti pendaftaran ini bukan bukti kelulusan seleksi akhir. Calon santri dinyatakan diterima
                        setelah mendapatkan keputusan resmi dari panitia PPDB dan menyelesaikan ketentuan administrasi
                        yang berlaku.
                    </div>
                </div>

                <div class="signature">
                    <div class="rules">
                        <strong>Ketentuan:</strong><br>
                        1. Simpan bukti pendaftaran ini sebagai arsip wali santri.<br>
                        2. Bawa bukti ini saat proses verifikasi atau daftar ulang.<br>
                        3. Data yang tidak sesuai dapat diperbaiki melalui panitia PPDB.
                    </div>

                    <div class="sign-box">
                        Jember, {{ now()->format('d-m-Y') }}<br>
                        Panitia PPDB
                        <div class="sign-space"></div>
                        <span class="sign-name">Admin PPDB</span>
                    </div>
                </div>

                <div class="footer">
                    <span>Dicetak pada {{ now()->format('d-m-Y H:i') }} WIB - SIKEU MHI V2</span>
                    <span>{{ $noDaftar }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
