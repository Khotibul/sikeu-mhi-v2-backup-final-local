<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kwitansi Gabungan - {{ $siswa->nama_siswa ?? '-' }}</title>
    @includeIf('partials.favicon')

    @php
    /*
    KWITANSI MODEL SOBEK + ARSIP
    Ukuran kwitansi: 210mm x 110mm.
    Kertas print: A4 portrait.
    Bagian kanan adalah arsip internal lembaga.
    */

    $getValue = function ($item, array $keys, $default = null) {
    foreach ($keys as $key) {
    if (is_array($item) && array_key_exists($key, $item) && $item[$key] !== null && $item[$key] !== '') {
    return $item[$key];
    }

    if (is_object($item) && isset($item->$key) && $item->$key !== null && $item->$key !== '') {
    return $item->$key;
    }
    }

    return $default;
    };

    $itemsRaw = [];
    if (isset($items)) {
    $itemsRaw = $items;
    } elseif (isset($pembayaranItems)) {
    $itemsRaw = $pembayaranItems;
    } elseif (isset($pembayarans)) {
    $itemsRaw = $pembayarans;
    } elseif (isset($pembayaran)) {
    $itemsRaw = $pembayaran;
    } elseif (isset($riwayat)) {
    $itemsRaw = $riwayat;
    }

    if ($itemsRaw instanceof \Illuminate\Support\Collection) {
    $itemsCollection = $itemsRaw->values();
    } elseif (is_array($itemsRaw)) {
    $itemsCollection = collect($itemsRaw)->values();
    } elseif (!empty($itemsRaw)) {
    $itemsCollection = collect([$itemsRaw]);
    } else {
    $itemsCollection = collect();
    }

    $formatTanggal = function ($tanggal, $default = '-') {
    if (!$tanggal) {
    return $default;
    }

    try {
    return \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
    } catch (\Throwable $e) {
    return $default;
    }
    };

    $namaBulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
    ];

    $formatBulan = function ($bulan) use ($namaBulan) {
    if ($bulan === null || $bulan === '') {
    return '-';
    }

    if (is_numeric($bulan)) {
    return $namaBulan[(int) $bulan] ?? (string) $bulan;
    }

    return (string) $bulan;
    };

    if (!function_exists('mhiKwitansiSobekTerbilangFinal')) {
    function mhiKwitansiSobekTerbilangFinal($angka)
    {
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
        return mhiKwitansiSobekTerbilangFinal($angka - 10) . ' Belas' ;
        }

        if ($angka < 100) {
        return mhiKwitansiSobekTerbilangFinal((int) ($angka / 10)) . ' Puluh ' . mhiKwitansiSobekTerbilangFinal($angka % 10);
        }

        if ($angka < 200) {
        return 'Seratus ' . mhiKwitansiSobekTerbilangFinal($angka - 100);
        }

        if ($angka < 1000) {
        return mhiKwitansiSobekTerbilangFinal((int) ($angka / 100)) . ' Ratus ' . mhiKwitansiSobekTerbilangFinal($angka % 100);
        }

        if ($angka < 2000) {
        return 'Seribu ' . mhiKwitansiSobekTerbilangFinal($angka - 1000);
        }

        if ($angka < 1000000) {
        return mhiKwitansiSobekTerbilangFinal((int) ($angka / 1000)) . ' Ribu ' . mhiKwitansiSobekTerbilangFinal($angka % 1000);
        }

        if ($angka < 1000000000) {
        return mhiKwitansiSobekTerbilangFinal((int) ($angka / 1000000)) . ' Juta ' . mhiKwitansiSobekTerbilangFinal($angka % 1000000);
        }

        if ($angka < 1000000000000) {
        return mhiKwitansiSobekTerbilangFinal((int) ($angka / 1000000000)) . ' Miliar ' . mhiKwitansiSobekTerbilangFinal($angka % 1000000000);
        }

        return (string) $angka;
        }
        }

        $getNominal=function ($item) use ($getValue) {
        $terbayar=(int) $getValue($item, ['terbayar', 'dibayar' ], 0);
        $jumlah=(int) $getValue($item, ['jumlah_bayar', 'nominal' , 'nominal_bayar' , 'bayar' ], 0);

        return $terbayar> 0 ? $terbayar : $jumlah;
        };

        $jenisDefault = strtolower((string) ($jenis ?? $type ?? $jenisBayar ?? ''));

        $isDiniyah = function ($item = null) use ($getValue, $jenisDefault) {
        $raw = $jenisDefault;

        if ($item) {
        $raw = strtolower((string) $getValue($item, ['jenis_pembayaran', 'jenis', 'type', 'kategori'], $jenisDefault));
        }

        return str_contains($raw, 'pondok') || str_contains($raw, 'diniyah');
        };

        $getJenisLabel = function ($item = null) use ($isDiniyah) {
        return $isDiniyah($item) ? 'Biaya Pendidikan Diniyah' : 'Biaya Pendidikan Formal';
        };

        $getJenisRingkas = function ($item = null) use ($isDiniyah) {
        return $isDiniyah($item) ? 'Diniyah' : 'Formal';
        };

        $getStatus = function ($item) use ($getValue) {
        $status = strtoupper(trim((string) $getValue($item, ['status_bayar', 'status'], '')));
        $ket = strtolower((string) $getValue($item, ['keterangan', 'uraian'], ''));

        if ($status === '') {
        $status = (str_contains($ket, 'cicil') || str_contains($ket, 'nyicil') || str_contains($ket, 'angsuran')) ? 'CICILAN' : 'LUNAS';
        }

        if (in_array($status, ['CICIL', 'NYICIL', 'ANGSURAN'], true)) {
        return 'CICILAN';
        }

        return $status === 'CICILAN' ? 'CICILAN' : 'LUNAS';
        };

        $firstItem = $itemsCollection->first();

        $totalKwitansi = (int) ($total ?? $totalBayar ?? $totalNominal ?? $itemsCollection->sum(fn ($item) => $getNominal($item)));
        $terbilangKwitansi = trim($terbilangRupiah ?? $terbilang ?? (mhiKwitansiSobekTerbilangFinal($totalKwitansi) . ' Rupiah'));
        $jumlahItemKwitansi = $itemsCollection->count();

        $modeClass = '';
        if ($jumlahItemKwitansi >= 12) {
        $modeClass = 'ultra-compact';
        } elseif ($jumlahItemKwitansi >= 8) {
        $modeClass = 'compact';
        }

        $tanggalAcuan = $tanggalNormal ?? $tanggalKwitansi ?? $tanggalBayar ?? $getValue($firstItem, ['tgl_bayar', 'tanggal_bayar', 'tanggal', 'created_at'], now());
        $tanggalKwitansiText = $formatTanggal($tanggalAcuan, date('d-m-Y'));

        $idAcuan = $getValue($firstItem, ['id_bayar', 'id_bayar_diniyah', 'id', 'id_transaksi'], $siswa->id_siswa ?? 0);
        $prefixNomor = $jumlahItemKwitansi > 1 ? 'KWG' : 'KWP';
        $nomorCetak = $nomorKwitansi
        ?? $nomorBukti
        ?? ($prefixNomor . '-' . date('Ymd', strtotime($tanggalAcuan)) . '-' . str_pad((int) $idAcuan, 4, '0', STR_PAD_LEFT));

        $kelasFormal = $siswa->kelas_formal ?? '-';
        $kelasDiniyah = $siswa->kelas_diniyah ?? '-';

        $adaFormal = $itemsCollection->contains(fn ($item) => !$isDiniyah($item));
        $adaDiniyah = $itemsCollection->contains(fn ($item) => $isDiniyah($item));

        if (!empty($judul ?? null)) {
        $judulBayar = $judul;
        } elseif ($adaFormal && $adaDiniyah) {
        $judulBayar = 'Biaya Pendidikan Formal & Diniyah';
        } elseif ($adaDiniyah || $jenisDefault === 'pondok' || $jenisDefault === 'diniyah') {
        $judulBayar = 'Biaya Pendidikan Diniyah';
        } else {
        $judulBayar = 'Biaya Pendidikan Formal';
        }

        $statusKeseluruhan = $itemsCollection->contains(fn ($item) => $getStatus($item) === 'CICILAN') ? 'CICILAN' : 'LUNAS';

        $bulanTahunDibayar = $itemsCollection
        ->map(function ($item) use ($getValue, $formatBulan) {
        $bulan = $formatBulan($getValue($item, ['bulan_bayar', 'bulan', 'bulan_pembayaran'], '-'));
        $tahun = $getValue($item, ['tahun_bayar', 'tahun', 'tahun_pembayaran'], '-');

        return trim($bulan . ' ' . $tahun);
        })
        ->filter(fn ($text) => trim($text) !== '-' && trim($text) !== '')
        ->unique()
        ->values()
        ->implode(', ');
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
                background: #e5eaf0;
                font-family: Arial, Helvetica, sans-serif;
                color: #111827;
            }

            .toolbar {
                width: 210mm;
                max-width: calc(100% - 16px);
                margin: 7px auto;
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .btn {
                border: none;
                border-radius: 7px;
                padding: 7px 11px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .btn-print {
                background: #0f766e;
                color: #fff;
            }

            .btn-back {
                background: #e5e7eb;
                color: #111827;
            }

            .paper {
                width: 210mm;
                min-height: 297mm;
                background: #fff;
                margin: 0 auto;
                padding: 0;
            }

            .kwitansi {
                width: 210mm;
                height: 110mm;
                border: 1.5px solid #0f766e;
                background: #fff;
                position: relative;
                overflow: hidden;
                display: grid;
                grid-template-columns: 1fr 45mm;
            }

            .kwitansi.compact {
                grid-template-columns: 1fr 42mm;
            }

            .kwitansi.ultra-compact {
                grid-template-columns: 1fr 39mm;
            }

            .main-slip {
                position: relative;
                padding: 3.6mm 4mm 2.8mm 4.4mm;
                overflow: hidden;
                height: 110mm;
            }

            .archive-slip {
                position: relative;
                height: 110mm;
                border-left: 1.5px dashed #94a3b8;
                padding: 4mm 3mm 2.6mm;
                background: #fbfffe;
                overflow: hidden;
            }

            .tear-label {
                position: absolute;
                left: -7mm;
                top: 50%;
                transform: translateY(-50%) rotate(-90deg);
                transform-origin: center;
                font-size: 5.7px;
                font-weight: 900;
                color: #64748b;
                background: #fff;
                padding: 1mm 1.5mm;
                letter-spacing: .8px;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .watermark {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: .065;
                pointer-events: none;
            }

            .watermark img {
                width: 75mm;
                max-height: 75mm;
                object-fit: contain;
            }

            .main-content {
                position: relative;
                z-index: 1;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .kop {
                display: grid;
                grid-template-columns: 16mm 1fr 33mm;
                gap: 2.5mm;
                align-items: center;
                border-bottom: 1.6px solid #0f766e;
                padding-bottom: 2.2mm;
                margin-bottom: 2mm;
            }

            .logo-box img {
                width: 14mm;
                height: 14mm;
                object-fit: contain;
            }

            .kop-title h1,
            .kop-title h2,
            .kop-title p {
                margin: 0;
                line-height: 1.12;
                text-align: left;
            }

            .kop-title h1 {
                color: #0f766e;
                font-size: 9.2px;
                text-transform: uppercase;
                font-weight: 950;
            }

            .kop-title h2 {
                color: #e11d48;
                font-size: 9.6px;
                text-transform: uppercase;
                font-weight: 950;
            }

            .kop-title p {
                margin-top: .6mm;
                color: #475569;
                font-size: 5.6px;
                font-weight: 700;
            }

            .nomor-box {
                text-align: right;
                font-size: 5.9px;
                font-weight: 900;
                line-height: 1.4;
                color: #334155;
            }

            .nomor-box strong {
                display: block;
                color: #e11d48;
                font-size: 6.7px;
                word-break: break-word;
            }

            .title {
                text-align: center;
                margin: .6mm 0 1.6mm;
            }

            .title h3 {
                display: inline-block;
                margin: 0;
                color: #111827;
                font-size: 9px;
                font-weight: 950;
                text-transform: uppercase;
                border-bottom: 1px solid #111827;
                padding-bottom: .2mm;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 1.05fr .95fr;
                gap: 3mm;
                margin-bottom: 1.5mm;
            }

            .info-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 6.3px;
                line-height: 1.25;
            }

            .info-table td {
                padding: .35mm .4mm;
                vertical-align: top;
            }

            .info-table .label {
                width: 20mm;
                font-weight: 950;
                color: #0f172a;
                text-transform: uppercase;
            }

            .info-table .colon {
                width: 2mm;
                text-align: center;
            }

            .info-table .value {
                font-weight: 900;
            }

            .amount-pill {
                display: inline-block;
                border: 1px solid #94a3b8;
                border-radius: 4px;
                padding: .7mm 1.2mm;
                font-size: 7px;
                font-weight: 950;
                color: #0f172a;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                padding: .35mm 1.1mm;
                background: #ccfbf1;
                color: #0f766e;
                font-size: 5.5px;
                font-weight: 950;
                white-space: nowrap;
            }

            .status-badge.cicilan {
                background: #fef3c7;
                color: #b45309;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                font-size: 5.9px;
                line-height: 1.16;
            }

            .items-table th,
            .items-table td {
                border: 1px solid #8bd3cb;
                padding: .55mm .7mm;
                vertical-align: middle;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            .items-table th {
                background: #e6fffb;
                color: #0f766e;
                text-align: center;
                font-weight: 950;
                text-transform: uppercase;
            }

            .items-table .center {
                text-align: center;
            }

            .items-table .right {
                text-align: right;
            }

            .bottom-area {
                display: grid;
                grid-template-columns: 1fr 32mm;
                gap: 4mm;
                align-items: start;
                margin-top: 1.4mm;
                padding-top: 1mm;
            }

            .total-box {
                border: 1px dashed #0f766e;
                border-radius: 3px;
                padding: 1.6mm 2mm;
            }

            .total-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 6.8px;
                font-weight: 950;
                margin-bottom: .7mm;
            }

            .total-row strong {
                color: #e11d48;
                font-size: 8.3px;
            }

            .terbilang {
                font-size: 5.7px;
                font-weight: 900;
                line-height: 1.25;
            }

            .footer-note {
                margin-top: 1mm;
                color: #64748b;
                font-size: 4.8px;
                line-height: 1.25;
            }

            .signature {
                text-align: center;
                font-size: 5.7px;
                font-weight: 900;
                color: #0f172a;
                padding-top: .4mm;
            }

            .signature-space {
                height: 7mm;
            }

            .signature-name {
                font-size: 6px;
                font-weight: 950;
                text-decoration: underline;
                text-transform: uppercase;
                word-break: break-word;
            }

            .archive-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .archive-title {
                color: #0f766e;
                text-align: center;
                font-size: 8.5px;
                font-weight: 950;
                text-transform: uppercase;
                border-bottom: 1.4px solid #0f766e;
                padding-bottom: 2mm;
                margin-bottom: 2.2mm;
            }

            .archive-mini {
                width: 100%;
                border-collapse: collapse;
                font-size: 5.4px;
                line-height: 1.18;
            }

            .archive-mini td {
                border-bottom: 1px dotted #cbd5e1;
                padding: .7mm .25mm;
                vertical-align: top;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            .archive-mini td:first-child {
                width: 33%;
                font-weight: 950;
                text-transform: uppercase;
                color: #334155;
            }

            .archive-total {
                margin-top: 2mm;
                background: #0f766e;
                color: #fff;
                border-radius: 4px;
                text-align: center;
                padding: 2.6mm 1.4mm;
                font-size: 8px;
                font-weight: 950;
            }

            .archive-note {
                margin-top: 1.5mm;
                color: #64748b;
                text-align: center;
                font-size: 4.6px;
                line-height: 1.25;
            }

            .archive-ttd {
                margin-top: 5mm;
                padding-top: 1mm;
                text-align: center;
                font-size: 5.2px;
                font-weight: 900;
            }

            .archive-ttd .space {
                height: 6mm;
            }

            .archive-ttd strong {
                font-size: 5.3px;
                text-decoration: underline;
                text-transform: uppercase;
                word-break: break-word;
            }

            .compact .main-slip {
                padding: 3mm 3.5mm 2.6mm 3.8mm;
            }

            .compact .items-table {
                font-size: 5.1px;
                line-height: 1.08;
            }

            .compact .items-table th,
            .compact .items-table td {
                padding: .42mm .5mm;
            }

            .compact .info-table {
                font-size: 5.7px;
            }

            .compact .bottom-area {
                grid-template-columns: 1fr 28mm;
                gap: 2.5mm;
                padding-top: 1.4mm;
            }

            .compact .signature-space {
                height: 9mm;
            }

            .ultra-compact .main-slip {
                padding: 2.6mm 3.2mm 2.2mm 3.4mm;
            }

            .ultra-compact .kop {
                margin-bottom: 1.1mm;
                padding-bottom: 1.4mm;
            }

            .ultra-compact .title {
                margin: .35mm 0 1mm;
            }

            .ultra-compact .info-grid {
                margin-bottom: 1mm;
                gap: 2mm;
            }

            .ultra-compact .info-table {
                font-size: 5.15px;
                line-height: 1.1;
            }

            .ultra-compact .items-table {
                font-size: 4.45px;
                line-height: 1.02;
            }

            .ultra-compact .items-table th,
            .ultra-compact .items-table td {
                padding: .28mm .36mm;
            }

            .ultra-compact .bottom-area {
                grid-template-columns: 1fr 24mm;
                gap: 2mm;
                padding-top: .9mm;
            }

            .ultra-compact .total-box {
                padding: 1mm 1.3mm;
            }

            .ultra-compact .total-row {
                font-size: 5.7px;
                margin-bottom: .2mm;
            }

            .ultra-compact .total-row strong {
                font-size: 6.5px;
            }

            .ultra-compact .terbilang {
                font-size: 4.7px;
                line-height: 1.1;
            }

            .ultra-compact .footer-note {
                font-size: 4px;
            }

            .ultra-compact .signature {
                font-size: 4.8px;
            }

            .ultra-compact .signature-space {
                height: 6mm;
            }

            .ultra-compact .archive-mini {
                font-size: 4.8px;
            }

            .ultra-compact .archive-total {
                font-size: 6.7px;
                padding: 1.8mm 1mm;
            }

            .ultra-compact .archive-ttd .space {
                height: 6mm;
            }

            @media print {

                html,
                body {
                    background: #fff !important;
                    width: 210mm;
                }

                .toolbar {
                    display: none !important;
                }

                .paper {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0;
                    box-shadow: none;
                }

                .kwitansi {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
            }
        </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()" class="btn btn-print">🖨 Cetak Sekarang</button>
        <a href="javascript:history.back()" class="btn btn-back">Kembali</a>
    </div>

    <div class="paper">
        <div class="kwitansi {{ $modeClass }}">
            <div class="main-slip">
                <div class="watermark">
                    <img src="{{ asset('images/logo-mhi.png') }}" alt="Watermark Logo">
                </div>

                <div class="main-content">
                    <div class="kop">
                        <div class="logo-box">
                            <img src="{{ asset('images/logo-mhi.png') }}" alt="Logo Yayasan">
                        </div>

                        <div class="kop-title">
                            <h1>Yayasan Pendidikan Pesantren</h1>
                            <h2>Mamba'ul Khoiriyatil Islamiyah</h2>
                            <p>Jl. KH. Abdul Halim Rohman No. 01, Kedungsuko, Bangsalsari, Jember</p>
                        </div>

                        <div class="nomor-box">
                            <div>No. Kwitansi</div>
                            <strong>{{ $nomorCetak }}</strong>
                            <div>Tgl: {{ $tanggalKwitansiText }}</div>
                        </div>
                    </div>

                    <div class="title">
                        <h3>KWITANSI PEMBAYARAN GABUNGAN</h3>
                    </div>

                    <div class="info-grid">
                        <table class="info-table">
                            <tr>
                                <td class="label">Nama Santri</td>
                                <td class="colon">:</td>
                                <td class="value">{{ strtoupper($siswa->nama_siswa ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td class="label">NIS / NISN</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kelas Formal</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $kelasFormal ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kelas Diniyah</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $kelasDiniyah ?: '-' }}</td>
                            </tr>
                        </table>

                        <table class="info-table">
                            <tr>
                                <td class="label">Jenis Bayar</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $judulBayar }}</td>
                            </tr>
                            <tr>
                                <td class="label">Bulan Bayar</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $bulanTahunDibayar ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Diterima</td>
                                <td class="colon">:</td>
                                <td><span class="amount-pill">Rp {{ number_format($totalKwitansi, 0, ',', '.') }}</span></td>
                            </tr>
                            <tr>
                                <td class="label">Status</td>
                                <td class="colon">:</td>
                                <td>
                                    <span class="status-badge {{ $statusKeseluruhan === 'CICILAN' ? 'cicilan' : '' }}">
                                        {{ $statusKeseluruhan }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width:5%;">No</th>
                                <th style="width:29%;">Keterangan</th>
                                <th style="width:13%;">Bulan</th>
                                <th style="width:10%;">Tahun</th>
                                <th style="width:14%;">Tanggal</th>
                                <th style="width:17%;">Nominal</th>
                                <th style="width:12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itemsCollection as $index => $item)
                            @php
                            $labelItem = $getJenisLabel($item);
                            $bulanItem = $formatBulan($getValue($item, ['bulan_bayar', 'bulan', 'bulan_pembayaran'], '-'));
                            $tahunItem = $getValue($item, ['tahun_bayar', 'tahun', 'tahun_pembayaran'], '-');
                            $nominalItem = $getNominal($item);
                            $statusItem = $getStatus($item);
                            $tanggalItem = $formatTanggal($getValue($item, ['tgl_bayar', 'tanggal_bayar', 'tanggal'], $tanggalAcuan), '-');
                            $keteranganItem = $getValue($item, ['keterangan', 'uraian'], $labelItem);
                            @endphp

                            <tr>
                                <td class="center">{{ $index + 1 }}</td>
                                <td>{{ $labelItem }}</td>
                                <td class="center">{{ $bulanItem }}</td>
                                <td class="center">{{ $tahunItem }}</td>
                                <td class="center">{{ $tanggalItem }}</td>
                                <td class="right">Rp {{ number_format($nominalItem, 0, ',', '.') }}</td>
                                <td class="center">
                                    <span class="status-badge {{ $statusItem === 'CICILAN' ? 'cicilan' : '' }}">
                                        {{ $statusItem }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="center">Tidak ada data pembayaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="bottom-area">
                        <div>
                            <div class="total-box">
                                <div class="total-row">
                                    <span>Total Diterima</span>
                                    <strong>Rp {{ number_format($totalKwitansi, 0, ',', '.') }}</strong>
                                </div>
                                <div class="terbilang">
                                    <strong>Terbilang:</strong> {{ ucwords(trim($terbilangKwitansi)) }}
                                </div>
                            </div>

                            <div class="footer-note">
                                Catatan: Kwitansi ini sah apabila telah ditandatangani oleh bendahara yayasan.
                                Dicetak {{ now()->format('d-m-Y H:i') }} WIB • SIKEU MHI V2
                            </div>
                        </div>

                        <div class="signature">
                            Bendahara Yayasan
                            <div class="signature-space"></div>
                            <div class="signature-name">AG. AHMAD HULQI KHOIR</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="archive-slip">
                <div class="tear-label">SOBEK DI SINI</div>
                <div class="archive-content">
                    <div class="archive-title">Arsip</div>

                    <table class="archive-mini">
                        <tr>
                            <td>No.</td>
                            <td>{{ $nomorCetak }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>{{ $tanggalKwitansiText }}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>{{ strtoupper($siswa->nama_siswa ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Jenis</td>
                            <td>{{ $judulBayar }}</td>
                        </tr>
                        <tr>
                            <td>Bulan</td>
                            <td>{{ $bulanTahunDibayar ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Item</td>
                            <td>{{ $jumlahItemKwitansi }} pembayaran</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{{ $statusKeseluruhan }}</td>
                        </tr>
                        <tr>
                            <td>Petugas</td>
                            <td>{{ session('admin_nama') ?? 'Admin' }}</td>
                        </tr>
                    </table>

                    <div class="archive-total">
                        Rp {{ number_format($totalKwitansi, 0, ',', '.') }}
                    </div>

                    <div class="archive-note">
                        Simpan bagian ini sebagai arsip internal lembaga.
                    </div>

                    <div class="archive-ttd">
                        Bendahara
                        <div class="space"></div>
                        <strong>AG. AHMAD HULQI KHOIR</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>