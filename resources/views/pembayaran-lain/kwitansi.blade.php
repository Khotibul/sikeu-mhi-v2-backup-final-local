<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran Lain</title>

    @php
    $row = $pembayaran ?? $item ?? $data ?? $transaksi ?? null;
    $siswaRow = $siswa ?? $santri ?? $row;

    $get = function ($source, array $keys, $default = '-') {
    foreach ($keys as $key) {
    if (is_object($source) && isset($source->{$key}) && $source->{$key} !== null && $source->{$key} !== '') {
    return $source->{$key};
    }

    if (is_array($source) && isset($source[$key]) && $source[$key] !== null && $source[$key] !== '') {
    return $source[$key];
    }
    }

    return $default;
    };

    $toNumber = function ($value) {
    if (is_numeric($value)) {
    return (int) $value;
    }

    $clean = preg_replace('/[^0-9]/', '', (string) $value);

    return (int) ($clean ?: 0);
    };

    $terbilangFn = function ($angka) use (&$terbilangFn) {
    $angka = abs((int) $angka);

    $huruf = [
    '',
    'Satu',
    'Dua',
    'Tiga',
    'Empat',
    'Lima',
    'Enam',
    'Tujuh',
    'Delapan',
    'Sembilan',
    'Sepuluh',
    'Sebelas',
    ];

    if ($angka < 12) {
        return $huruf[$angka];
        }

        if ($angka < 20) {
        return $terbilangFn($angka - 10) . ' Belas' ;
        }

        if ($angka < 100) {
        return $terbilangFn((int) ($angka / 10)) . ' Puluh ' . $terbilangFn($angka % 10);
        }

        if ($angka < 200) {
        return 'Seratus ' . $terbilangFn($angka - 100);
        }

        if ($angka < 1000) {
        return $terbilangFn((int) ($angka / 100)) . ' Ratus ' . $terbilangFn($angka % 100);
        }

        if ($angka < 2000) {
        return 'Seribu ' . $terbilangFn($angka - 1000);
        }

        if ($angka < 1000000) {
        return $terbilangFn((int) ($angka / 1000)) . ' Ribu ' . $terbilangFn($angka % 1000);
        }

        if ($angka < 1000000000) {
        return $terbilangFn((int) ($angka / 1000000)) . ' Juta ' . $terbilangFn($angka % 1000000);
        }

        if ($angka < 1000000000000) {
        return $terbilangFn((int) ($angka / 1000000000)) . ' Miliar ' . $terbilangFn($angka % 1000000000);
        }

        return (string) $angka;
        };

        $namaSantri=$get($siswaRow, ['nama_siswa', 'nama_santri' , 'nama_lengkap' , 'nama' ], '-' );
        $nis=$get($siswaRow, ['nis', 'nis_siswa' ], '-' );
        $nisn=$get($siswaRow, ['nisn'], '-' );
        $kelasFormal=$get($siswaRow, ['kelas_formal', 'kelas' , 'nama_kelas' ], '-' );
        $kelasDiniyah=$get($siswaRow, ['kelas_diniyah', 'kelas_pondok' ], '-' );

        $tanggalRaw=$get($row, ['tgl_bayar', 'tanggal_bayar' , 'tanggal' , 'created_at' ], now()->toDateString());

        try {
        $tanggalCetak = \Carbon\Carbon::parse($tanggalRaw)->format('d-m-Y');
        $tanggalKode = \Carbon\Carbon::parse($tanggalRaw)->format('Ymd');
        } catch (\Throwable $e) {
        $tanggalCetak = date('d-m-Y');
        $tanggalKode = date('Ymd');
        }

        $idKwitansi = $get($row, ['id_pangkal', 'id_pembayaran_lain', 'id_bayar_lain', 'id_bayar', 'id_lain', 'id'], '0000');

        $nomorKwitansi = $get(
        $row,
        ['no_kwitansi', 'nomor_kwitansi', 'no_bukti', 'kode_bukti'],
        'KPL-' . $tanggalKode . '-' . str_pad((string) $idKwitansi, 4, '0', STR_PAD_LEFT)
        );

        $nominal = $toNumber($get($row, [
        'total_bayar',
        'jumlah_bayar',
        'nominal_bayar',
        'nominal',
        'jumlah',
        'bayar',
        'terbayar',
        'total',
        ], 0));

        /*
        * Redaksi jenis harus sama dengan yang tampil di halaman web.
        * Untuk tagihan tetap, kolom paling penting biasanya: jenis_tagihan.
        */
        $jenisBayar = $get($row, [
        'jenis_label',
        'jenis_tagihan',
        'nama_jenis_pembayaran',
        'nama_pembayaran',
        'nama_jenis',
        'jenis_nama',
        'nama_tagihan',
        'jenis_pembayaran',
        'jenis',
        'nama_biaya',
        ], 'Pembayaran Lain');

        $jenisBayar = trim((string) $jenisBayar);

        if ($jenisBayar === '' || strtoupper($jenisBayar) === 'PEMBAYARAN LAIN TETAP') {
        $jenisBayar = trim((string) $get($row, ['jenis_tagihan', 'uraian', 'keterangan_jenis', 'nama_biaya'], 'Pembayaran Lain'));
        }

        $jenisBayar = strtoupper($jenisBayar ?: 'Pembayaran Lain');

        $periode = $get($row, ['periode', 'bulan_tahun', 'bulan_bayar', 'bulan'], '-');
        $tahun = $get($row, ['tahun_bayar', 'tahun'], '');
        $periodeTampil = trim($periode . ' ' . $tahun);
        $periodeTampil = $periodeTampil !== '' ? $periodeTampil : '-';

        $keterangan = $get($row, ['keterangan', 'catatan'], '-');
        $statusRaw = $get($row, ['status_bayar', 'status', 'status_pembayaran'], null);

        if (!$statusRaw || $statusRaw === '-') {
        $ketUpper = strtoupper(trim((string) $keterangan));
        $statusRaw = in_array($ketUpper, ['LUNAS', 'CICILAN', 'CICIL', 'BELUM'], true) ? $ketUpper : 'LUNAS';
        }

        $status = strtoupper((string) $statusRaw);
        $petugas = $get($row, ['nama_admin', 'admin_nama', 'petugas'], session('admin_nama') ?? 'Admin');
        $bendahara = 'AG. AHMAD HULQI KHOIR';
        $terbilang = trim(preg_replace('/\s+/', ' ', $terbilangFn($nominal))) . ' Rupiah';

        $logoCandidates = [
        'images/logo-mhi.png',
        'img/logo-mhi.png',
        'images/logo.png',
        'img/logo.png',
        'logo-mhi.png',
        'logo.png',
        'assets/images/logo.png',
        'assets/img/logo.png',
        'storage/logo-mhi.png',
        'storage/logo.png',
        ];

        $logoSrc = null;

        foreach ($logoCandidates as $candidate) {
        try {
        if (function_exists('public_path') && file_exists(public_path($candidate))) {
        $logoSrc = asset($candidate);
        break;
        }
        } catch (\Throwable $e) {
        // Abaikan jika public_path tidak tersedia.
        }
        }
        @endphp

        <style>
            @page {
                size: A4 portrait;
                margin: 0;
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: #e8eef3;
                font-family: Arial, Helvetica, sans-serif;
                color: #0f172a;
            }

            body {
                padding: 0 0 24px;
            }

            .toolbar {
                width: 210mm;
                margin: 0 auto 6px;
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .btn {
                display: inline-block;
                border: 0;
                border-radius: 7px;
                padding: 8px 14px;
                font-size: 12px;
                font-weight: 800;
                text-decoration: none;
                cursor: pointer;
            }

            .btn-print {
                background: #0f8a7b;
                color: #fff;
            }

            .btn-back {
                background: #fff;
                color: #0f172a;
                border: 1px solid #cfd8e3;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
            }

            .kwitansi {
                width: 210mm;
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                margin: 0 auto;
                background: #fff;
                border: 1px solid #0f8a7b;
                display: grid;
                grid-template-columns: 164mm 46mm;
                overflow: hidden;
                page-break-inside: avoid;
            }

            .main {
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                padding: 4mm 4mm 3mm;
                position: relative;
                overflow: hidden;
            }

            .arsip {
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                padding: 4mm 3mm 3mm;
                border-left: 1.5px dashed #7c91a0;
                position: relative;
                overflow: hidden;
                background: #fbfffe;
            }

            .sobek {
                position: absolute;
                top: 50%;
                left: -16mm;
                transform: rotate(-90deg);
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .7px;
                color: #64748b;
                white-space: nowrap;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 8px;
                border-bottom: 1.4px solid #0f8a7b;
                padding-bottom: 2mm;
                margin-bottom: 2mm;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 8px;
                min-height: 18mm;
            }

            .logo {
                width: 18mm;
                height: 18mm;
                object-fit: contain;
                flex: 0 0 18mm;
            }

            .logo-fallback {
                width: 18mm;
                height: 18mm;
                border: 1.4px solid #0f8a7b;
                border-radius: 50%;
                color: #0f766e;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: 900;
                flex: 0 0 18mm;
            }

            .brand-text h1 {
                margin: 0;
                font-size: 11px;
                line-height: 1.15;
                color: #0f766e;
                text-transform: uppercase;
                font-weight: 900;
            }

            .brand-text h2 {
                margin: 0;
                font-size: 10px;
                line-height: 1.15;
                color: #e11d48;
                text-transform: uppercase;
                font-weight: 900;
            }

            .brand-text p {
                margin: 1px 0 0;
                font-size: 6.8px;
                line-height: 1.25;
                color: #334155;
                font-weight: 700;
            }

            .number-box {
                text-align: right;
                font-size: 7.4px;
                line-height: 1.35;
                font-weight: 900;
                min-width: 33mm;
            }

            .number-box .red {
                color: #e11d48;
            }

            .title {
                text-align: center;
                font-size: 12.5px;
                font-weight: 900;
                text-transform: uppercase;
                text-decoration: underline;
                margin: 1mm 0 2.3mm;
            }

            .meta {
                display: grid;
                grid-template-columns: 1fr 1fr;
                column-gap: 11mm;
                margin-bottom: 2mm;
            }

            .meta table {
                width: 100%;
                border-collapse: collapse;
                font-size: 7.7px;
                line-height: 1.35;
            }

            .meta td {
                padding: .7px 0;
                vertical-align: top;
            }

            .meta .label {
                width: 27mm;
                font-weight: 900;
                text-transform: uppercase;
            }

            .meta .sep {
                width: 3mm;
                text-align: center;
            }

            .items {
                width: 100%;
                border-collapse: collapse;
                font-size: 6.8px;
                line-height: 1.18;
                margin-top: 1mm;
            }

            .items th,
            .items td {
                border: 1px solid #8edbd2;
                padding: 1.3px 2px;
                text-align: center;
                vertical-align: middle;
            }

            .items th {
                background: #e7faf7;
                color: #075e56;
                font-weight: 900;
                text-transform: uppercase;
            }

            .items .left {
                text-align: left;
            }

            .bottom-area {
                margin-top: 2.2mm;
                display: grid;
                grid-template-columns: 118mm 36mm;
                column-gap: 5mm;
                align-items: start;
            }

            .total-box {
                border: 1.2px dashed #0f8a7b;
                padding: 3mm 3mm;
                min-height: 18mm;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 8.2px;
                font-weight: 900;
                margin-bottom: 2mm;
            }

            .total-row .amount {
                color: #e11d48;
                font-size: 14px;
                font-weight: 900;
            }

            .terbilang {
                font-size: 7.5px;
                font-weight: 900;
                line-height: 1.25;
            }

            .main-sign {
                text-align: center;
                font-size: 7.7px;
                font-weight: 900;
                padding-top: 1mm;
            }

            .main-sign .space {
                height: 8mm;
            }

            .main-sign .name {
                font-size: 7.5px;
                text-decoration: underline;
                text-transform: uppercase;
                font-weight: 900;
            }

            .note {
                margin-top: 1mm;
                font-size: 5.8px;
                color: #64748b;
                line-height: 1.2;
            }

            .arsip-title {
                text-align: center;
                color: #0f766e;
                font-size: 10.5px;
                font-weight: 900;
                text-transform: uppercase;
                border-bottom: 1.4px solid #0f8a7b;
                padding-bottom: 1.2mm;
                margin-bottom: 2.5mm;
            }

            .arsip table {
                width: 100%;
                border-collapse: collapse;
                font-size: 6.8px;
                line-height: 1.22;
            }

            .arsip td {
                padding: .75px 0;
                border-bottom: 1px dotted #cbd5e1;
                vertical-align: top;
            }

            .arsip .label {
                width: 13mm;
                font-weight: 900;
                text-transform: uppercase;
            }

            .arsip .sep {
                width: 2mm;
                text-align: center;
            }

            .arsip-total {
                margin-top: 2.5mm;
                background: #0f8a7b;
                color: #fff;
                border-radius: 5px;
                padding: 5px 4px;
                text-align: center;
                font-size: 11px;
                font-weight: 900;
            }

            .arsip-note {
                margin-top: 2mm;
                font-size: 5.7px;
                line-height: 1.2;
                color: #64748b;
                text-align: center;
            }

            .arsip-sign {
                position: absolute;
                left: 3mm;
                right: 3mm;
                bottom: 16mm;
                /* dinaikkan agar tidak terlalu mepet bawah */
                text-align: center;
                font-size: 6.8px;
                font-weight: 900;
            }

            .arsip-sign .space {
                height: 6mm;
                /* dipadatkan supaya tetap muat di tinggi 110mm */
            }

            .arsip-sign .name {
                font-size: 6.6px;
                text-decoration: underline;
                text-transform: uppercase;
                font-weight: 900;
            }

            @media print {

                html,
                body {
                    background: #fff;
                }

                body {
                    padding: 0;
                }

                .toolbar {
                    display: none !important;
                }

                .page {
                    width: 210mm;
                    min-height: auto;
                    margin: 0 auto;
                    padding: 0 !important;
                }

                .kwitansi {
                    margin: 0 auto !important;
                    transform: translateY(0);
                }
            }
        </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" class="btn btn-print" onclick="window.print()">🖨 Cetak Sekarang</button>
        <a href="javascript:history.back()" class="btn btn-back">Kembali</a>
    </div>

    <div class="page">
        <div class="kwitansi">
            <section class="main">
                <div class="header">
                    <div class="brand">
                        @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo" alt="Logo Yayasan">
                        @else
                        <div class="logo-fallback">MHI</div>
                        @endif

                        <div class="brand-text">
                            <h1>Yayasan Pendidikan Pesantren</h1>
                            <h2>Mamba'ul Khoiriyatil Islamiyah</h2>
                            <p>Jl. KH. Abdul Halim Rohman No. 01, Kedungsuko, Bangsalsari, Jember</p>
                        </div>
                    </div>

                    <div class="number-box">
                        <div>No. Kwitansi</div>
                        <div class="red">{{ $nomorKwitansi }}</div>
                        <div>Tgl: {{ $tanggalCetak }}</div>
                    </div>
                </div>

                <div class="title">Kwitansi Pembayaran Lain</div>

                <div class="meta">
                    <table>
                        <tr>
                            <td class="label">Nama Santri</td>
                            <td class="sep">:</td>
                            <td>{{ $namaSantri }}</td>
                        </tr>
                        <tr>
                            <td class="label">NIS / NISN</td>
                            <td class="sep">:</td>
                            <td>{{ $nis }} / {{ $nisn }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas Formal</td>
                            <td class="sep">:</td>
                            <td>{{ $kelasFormal }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas Diniyah</td>
                            <td class="sep">:</td>
                            <td>{{ $kelasDiniyah }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="label">Jenis Bayar</td>
                            <td class="sep">:</td>
                            <td>{{ $jenisBayar }}</td>
                        </tr>
                        <tr>
                            <td class="label">Periode</td>
                            <td class="sep">:</td>
                            <td>{{ $periodeTampil }}</td>
                        </tr>
                        <tr>
                            <td class="label">Diterima</td>
                            <td class="sep">:</td>
                            <td>Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="sep">:</td>
                            <td>{{ $status }}</td>
                        </tr>
                    </table>
                </div>

                <table class="items">
                    <thead>
                        <tr>
                            <th style="width:5%;">No</th>
                            <th style="width:29%;">Jenis Pembayaran</th>
                            <th style="width:29%;">Keterangan</th>
                            <th style="width:12%;">Periode</th>
                            <th style="width:12%;">Tanggal</th>
                            <th style="width:13%;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="left">{{ $jenisBayar }}</td>
                            <td class="left">{{ $keterangan }}</td>
                            <td>{{ $periodeTampil }}</td>
                            <td>{{ $tanggalCetak }}</td>
                            <td>Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="bottom-area">
                    <div>
                        <div class="total-box">
                            <div class="total-row">
                                <span>Total Diterima</span>
                                <span class="amount">Rp {{ number_format($nominal, 0, ',', '.') }}</span>
                            </div>
                            <div class="terbilang">Terbilang: {{ $terbilang }}</div>
                        </div>

                        <div class="note">
                            Catatan: Kwitansi ini sah apabila telah ditandatangani oleh bendahara yayasan dan disimpan sebagai bukti pembayaran resmi.
                        </div>
                    </div>

                    <div class="main-sign">
                        <div>Bendahara Yayasan</div>
                        <div class="space"></div>
                        <div class="name">{{ $bendahara }}</div>
                    </div>
                </div>
            </section>

            <aside class="arsip">
                <div class="sobek">SOBEK DI SINI</div>

                <div class="arsip-title">Arsip</div>

                <table>
                    <tr>
                        <td class="label">No</td>
                        <td class="sep">:</td>
                        <td>{{ $nomorKwitansi }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="sep">:</td>
                        <td>{{ $tanggalCetak }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="sep">:</td>
                        <td>{{ $namaSantri }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIS</td>
                        <td class="sep">:</td>
                        <td>{{ $nis }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis</td>
                        <td class="sep">:</td>
                        <td>{{ $jenisBayar }}</td>
                    </tr>
                    <tr>
                        <td class="label">Periode</td>
                        <td class="sep">:</td>
                        <td>{{ $periodeTampil }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="sep">:</td>
                        <td>{{ $status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Petugas</td>
                        <td class="sep">:</td>
                        <td>{{ $petugas }}</td>
                    </tr>
                </table>

                <div class="arsip-total">
                    Rp {{ number_format($nominal, 0, ',', '.') }}
                </div>

                <div class="arsip-note">
                    Simpan bagian ini sebagai arsip internal lembaga.
                </div>

                <div class="arsip-sign">
                    <div>Bendahara</div>
                    <div class="space"></div>
                    <div class="name">{{ $bendahara }}</div>
                </div>
            </aside>
        </div>
    </div>
</body>

</html>