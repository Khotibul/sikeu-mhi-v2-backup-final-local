<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Setor Pemasukan Lain - SIKEU MHI</title>
    @includeIf('partials.favicon')

    @php
    if (!function_exists('sikeuBebasTerbilang')) {
    function sikeuBebasTerbilang($angka)
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
        return sikeuBebasTerbilang($angka - 10) . ' Belas' ;
        }

        if ($angka < 100) {
        return sikeuBebasTerbilang((int) ($angka / 10)) . ' Puluh ' . sikeuBebasTerbilang($angka % 10);
        }

        if ($angka < 200) {
        return 'Seratus ' . sikeuBebasTerbilang($angka - 100);
        }

        if ($angka < 1000) {
        return sikeuBebasTerbilang((int) ($angka / 100)) . ' Ratus ' . sikeuBebasTerbilang($angka % 100);
        }

        if ($angka < 2000) {
        return 'Seribu ' . sikeuBebasTerbilang($angka - 1000);
        }

        if ($angka < 1000000) {
        return sikeuBebasTerbilang((int) ($angka / 1000)) . ' Ribu ' . sikeuBebasTerbilang($angka % 1000);
        }

        if ($angka < 1000000000) {
        return sikeuBebasTerbilang((int) ($angka / 1000000)) . ' Juta ' . sikeuBebasTerbilang($angka % 1000000);
        }

        if ($angka < 1000000000000) {
        return sikeuBebasTerbilang((int) ($angka / 1000000000)) . ' Miliar ' . sikeuBebasTerbilang($angka % 1000000000);
        }

        return (string) $angka;
        }
        }

        $tanggalRaw=$item->tanggal
        ?? $item->tgl_masuk
        ?? $item->tgl_setor
        ?? $item->created_at
        ?? now();

        try {
        $tanggalSetor = \Carbon\Carbon::parse($tanggalRaw)->format('d-m-Y');
        $tanggalNomor = \Carbon\Carbon::parse($tanggalRaw)->format('Ymd');
        } catch (\Throwable $e) {
        $tanggalSetor = date('d-m-Y');
        $tanggalNomor = date('Ymd');
        }

        $idMasuk = $item->id_masuk
        ?? $item->id_pemasukan
        ?? $item->id
        ?? 0;

        $nomorBukti = $item->no_bukti
        ?? $item->kode_bukti
        ?? ('BS-' . $tanggalNomor . '-' . str_pad((string) $idMasuk, 4, '0', STR_PAD_LEFT));

        $namaPenyetor = $item->nama_penyetor
        ?? $item->penyetor
        ?? $item->nama
        ?? '-';

        $uraian = $item->uraian
        ?? $item->keterangan
        ?? $item->keperluan
        ?? '-';

        $nominal = (int) (
        $item->nominal
        ?? $item->jumlah
        ?? $item->jumlah_bayar
        ?? 0
        );

        $terbilang = trim(preg_replace('/\s+/', ' ', sikeuBebasTerbilang($nominal))) . ' Rupiah';

        $petugas = $item->nama_admin
        ?? $item->petugas
        ?? session('admin_nama')
        ?? 'Petugas';

        $bendaharaYayasan = 'AG. AHMAD HULQI KHOIR';

        $logoUtama = asset('images/logo-mhi.png');
        @endphp

        <style>
            @page {
                size: A4 portrait;
                margin: 8mm 0 0 0;
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: #e8eef3;
                color: #0f172a;
                font-family: Arial, Helvetica, sans-serif;
            }

            body {
                padding: 8px 0 20px;
            }

            .print-toolbar {
                width: 210mm;
                margin: 0 auto 6px auto;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .print-toolbar button,
            .print-toolbar a {
                border: 0;
                border-radius: 7px;
                padding: 8px 13px;
                font-size: 12px;
                line-height: 1;
                font-weight: 800;
                cursor: pointer;
                text-decoration: none;
            }

            .print-toolbar button {
                background: #0f766e;
                color: #ffffff;
            }

            .print-toolbar a {
                background: #ffffff;
                color: #0f172a;
                border: 1px solid #cbd5e1;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #ffffff;
                padding-top: 0;
            }

            .receipt-sheet {
                width: 210mm;
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                margin: 0 auto;
                display: flex;
                overflow: hidden;
                background: #ffffff;
                border: 1px solid #0f766e;
                page-break-inside: avoid;
            }

            .receipt-main {
                width: 164mm;
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                position: relative;
                overflow: hidden;
                padding: 4mm 4.5mm 3.5mm 4.5mm;
            }

            .receipt-archive {
                width: 46mm;
                height: 110mm;
                min-height: 110mm;
                max-height: 110mm;
                position: relative;
                overflow: hidden;
                padding: 3.5mm 3mm;
                border-left: 1.8px dashed #94a3b8;
                background: #fbfffe;
            }

            .tear-label {
                position: absolute;
                left: -17mm;
                top: 52mm;
                transform: rotate(-90deg);
                transform-origin: center;
                font-size: 7px;
                line-height: 1;
                font-weight: 900;
                letter-spacing: .6px;
                color: #64748b;
                background: #fbfffe;
                padding: 1px 3px;
                white-space: nowrap;
            }

            .watermark {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                pointer-events: none;
                z-index: 0;
                opacity: .045;
            }

            .watermark img {
                width: 65mm;
                height: 65mm;
                object-fit: contain;
            }

            .content {
                position: relative;
                z-index: 2;
                height: 100%;
            }

            .header {
                display: grid;
                grid-template-columns: 1fr 42mm;
                gap: 8px;
                align-items: start;
                border-bottom: 1.3px solid #0f766e;
                padding-bottom: 2mm;
                margin-bottom: 2mm;
            }

            .header-left {
                display: flex;
                align-items: flex-start;
                gap: 7px;
            }

            .logo {
                width: 16.5mm;
                height: 16.5mm;
                object-fit: contain;
            }

            .school-title h1,
            .school-title h2,
            .school-title p {
                margin: 0;
            }

            .school-title h2 {
                font-size: 10px;
                line-height: 1.15;
                color: #0f766e;
                text-transform: uppercase;
                font-weight: 900;
            }

            .school-title h1 {
                font-size: 12px;
                line-height: 1.15;
                color: #e11d48;
                text-transform: uppercase;
                font-weight: 900;
            }

            .school-title p {
                margin-top: 1px;
                font-size: 6.5px;
                color: #475569;
                line-height: 1.25;
                font-weight: 700;
            }

            .number-box {
                text-align: right;
                font-size: 7.2px;
                line-height: 1.35;
                font-weight: 800;
            }

            .number-box .num {
                color: #e11d48;
                font-size: 8px;
                font-weight: 900;
                word-break: break-all;
            }

            .title {
                text-align: center;
                margin: 1mm 0 2mm;
                font-size: 12px;
                font-weight: 900;
                text-transform: uppercase;
                text-decoration: underline;
                color: #0f172a;
            }

            .subtitle {
                text-align: center;
                margin-top: -1mm;
                margin-bottom: 2mm;
                font-size: 7px;
                color: #475569;
                font-weight: 700;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4px 10px;
                margin-bottom: 2mm;
            }

            .info-row {
                display: grid;
                grid-template-columns: 28mm 3mm 1fr;
                gap: 0;
                align-items: start;
                font-size: 8px;
                line-height: 1.25;
            }

            .info-row strong {
                text-transform: uppercase;
                font-weight: 900;
            }

            .detail-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 7.3px;
                line-height: 1.2;
                margin-top: 1mm;
            }

            .detail-table th,
            .detail-table td {
                border: 1px solid #9ed6d0;
                padding: 2px 3px;
                vertical-align: top;
            }

            .detail-table th {
                background: #ecfdf5;
                color: #0f766e;
                text-transform: uppercase;
                font-weight: 900;
                text-align: center;
            }

            .detail-table .right {
                text-align: right;
            }

            .detail-table .center {
                text-align: center;
            }

            .summary {
                display: grid;
                grid-template-columns: 1fr 40mm;
                gap: 8px;
                align-items: stretch;
                margin-top: 2mm;
            }

            .terbilang-box {
                border: 1px dashed #0f766e;
                padding: 2mm 2.5mm;
                min-height: 13mm;
            }

            .terbilang-box .label {
                font-size: 7px;
                text-transform: uppercase;
                font-weight: 900;
                color: #0f766e;
                margin-bottom: 1mm;
            }

            .terbilang-box .text {
                font-size: 8px;
                font-weight: 900;
                line-height: 1.35;
            }

            .amount-box {
                background: #0f766e;
                color: #ffffff;
                border-radius: 5px;
                padding: 2mm 2.5mm;
                text-align: right;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .amount-box .label {
                font-size: 7px;
                font-weight: 900;
                text-transform: uppercase;
                margin-bottom: 1mm;
            }

            .amount-box .value {
                font-size: 13px;
                font-weight: 900;
            }

            .note {
                margin-top: 1.5mm;
                color: #475569;
                font-size: 6.4px;
                line-height: 1.3;
                max-width: 115mm;
            }

            .signature-main {
                position: absolute;
                right: 7mm;
                bottom: 9mm;
                width: 38mm;
                text-align: center;
                font-size: 7.4px;
                line-height: 1.3;
                font-weight: 800;
                z-index: 3;
            }

            .signature-main .space {
                height: 12mm;
            }

            .signature-main .name {
                font-size: 7.3px;
                font-weight: 900;
                text-transform: uppercase;
                text-decoration: underline;
            }

            .archive-title {
                text-align: center;
                color: #0f766e;
                font-size: 11px;
                line-height: 1;
                font-weight: 900;
                text-transform: uppercase;
                border-bottom: 1px solid #0f766e;
                padding-bottom: 2mm;
                margin-bottom: 2mm;
            }

            .archive-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 6.8px;
                line-height: 1.2;
            }

            .archive-table td {
                padding: 1px 0;
                border-bottom: 1px dotted #cbd5e1;
                vertical-align: top;
            }

            .archive-table .label {
                width: 13mm;
                font-weight: 900;
                text-transform: uppercase;
                color: #334155;
            }

            .archive-table .sep {
                width: 2mm;
            }

            .archive-amount {
                margin-top: 2.2mm;
                background: #0f766e;
                color: #ffffff;
                border-radius: 5px;
                padding: 5px 4px;
                text-align: center;
                font-size: 11px;
                line-height: 1.2;
                font-weight: 900;
                word-break: break-word;
            }

            .archive-note {
                margin-top: 2mm;
                font-size: 5.8px;
                line-height: 1.25;
                color: #64748b;
                text-align: center;
            }

            .signature-archive {
                position: absolute;
                left: 50%;
                bottom: 6.5mm;
                transform: translateX(-50%);
                width: 30mm;
                text-align: center;
                font-size: 6.6px;
                line-height: 1.3;
                font-weight: 800;
            }

            .signature-archive .space {
                height: 10mm;
            }

            .signature-archive .name {
                font-size: 6.2px;
                font-weight: 900;
                text-transform: uppercase;
                text-decoration: underline;
            }

            @media print {

                html,
                body {
                    background: #ffffff;
                }

                body {
                    padding: 0;
                    margin: 0;
                }

                .print-toolbar {
                    display: none !important;
                }

                .page {
                    width: 210mm;
                    min-height: auto;
                    padding: 0;
                    margin: 0 auto;
                    background: #ffffff;
                }

                .receipt-sheet {
                    margin: 0 auto;
                    box-shadow: none;
                    page-break-after: avoid;
                    page-break-before: avoid;
                    page-break-inside: avoid;
                }
            }
        </style>
</head>

<body>
    <div class="print-toolbar">
        <button onclick="window.print()">🖨 Cetak Bukti Setor</button>
        <a href="{{ url()->previous() }}">Kembali</a>
    </div>

    <div class="page">
        <div class="receipt-sheet">
            <div class="receipt-main">
                <div class="watermark">
                    <img src="{{ $logoUtama }}" alt="Watermark Logo">
                </div>

                <div class="content">
                    <div class="header">
                        <div class="header-left">
                            <img src="{{ $logoUtama }}" class="logo" alt="Logo">
                            <div class="school-title">
                                <h2>Yayasan Pendidikan Pesantren</h2>
                                <h1>Mamba'ul Khoiriyatil Islamiyah</h1>
                                <p>Jl. KH. Abdul Halim Rohman No. 01, Kedungsuko, Bangsalsari, Jember</p>
                            </div>
                        </div>

                        <div class="number-box">
                            <div>No. Bukti Setor</div>
                            <div class="num">{{ $nomorBukti }}</div>
                            <div>Tgl: {{ $tanggalSetor }}</div>
                        </div>
                    </div>

                    <div class="title">Bukti Setor Pemasukan Lain</div>
                    <div class="subtitle">Dokumen ini diberikan kepada penyetor sebagai bukti transaksi resmi.</div>

                    <div class="info-grid">
                        <div class="info-row">
                            <strong>Nama Penyetor</strong><span>:</span>
                            <span>{{ $namaPenyetor }}</span>
                        </div>
                        <div class="info-row">
                            <strong>Tanggal Setor</strong><span>:</span>
                            <span>{{ $tanggalSetor }}</span>
                        </div>
                        <div class="info-row">
                            <strong>Petugas</strong><span>:</span>
                            <span>{{ $petugas }}</span>
                        </div>
                        <div class="info-row">
                            <strong>Jenis Bukti</strong><span>:</span>
                            <span>Setoran Bebas / Pemasukan Lain</span>
                        </div>
                    </div>

                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th style="width: 8mm;">No</th>
                                <th>Keterangan Setoran</th>
                                <th style="width: 34mm;">Nominal</th>
                                <th style="width: 24mm;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="center">1</td>
                                <td>
                                    <strong>{{ $uraian }}</strong>
                                    <div style="font-size:6.4px;color:#64748b;margin-top:1px;">
                                        Penyetor: {{ $namaPenyetor }}
                                    </div>
                                </td>
                                <td class="right">
                                    <strong>Rp {{ number_format($nominal, 0, ',', '.') }}</strong>
                                </td>
                                <td class="center">
                                    <strong>DITERIMA</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="summary">
                        <div class="terbilang-box">
                            <div class="label">Terbilang</div>
                            <div class="text">{{ $terbilang }}</div>
                        </div>

                        <div class="amount-box">
                            <div class="label">Total Setoran</div>
                            <div class="value">Rp {{ number_format($nominal, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="note">
                        Catatan: Bukti setor ini sah sebagai tanda penerimaan dana oleh petugas.
                        Mohon disimpan dengan baik dan ditunjukkan apabila diperlukan untuk verifikasi data setoran.
                    </div>

                    <div class="signature-main">
                        <div>Petugas Penerima</div>
                        <div class="space"></div>
                        <div class="name">{{ strtoupper($petugas) }}</div>
                    </div>
                </div>
            </div>

            <div class="receipt-archive">
                <div class="tear-label">SOBEK DI SINI</div>

                <div class="archive-title">Arsip</div>

                <table class="archive-table">
                    <tr>
                        <td class="label">No</td>
                        <td class="sep">:</td>
                        <td>{{ $nomorBukti }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="sep">:</td>
                        <td>{{ $tanggalSetor }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penyetor</td>
                        <td class="sep">:</td>
                        <td>{{ $namaPenyetor }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis</td>
                        <td class="sep">:</td>
                        <td>Setoran Bebas</td>
                    </tr>
                    <tr>
                        <td class="label">Uraian</td>
                        <td class="sep">:</td>
                        <td>{{ $uraian }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="sep">:</td>
                        <td>DITERIMA</td>
                    </tr>
                    <tr>
                        <td class="label">Petugas</td>
                        <td class="sep">:</td>
                        <td>{{ $petugas }}</td>
                    </tr>
                </table>

                <div class="archive-amount">
                    Rp {{ number_format($nominal, 0, ',', '.') }}
                </div>

                <div class="archive-note">
                    Simpan bagian ini sebagai arsip internal lembaga.
                </div>

                <div class="signature-archive">
                    <div>Petugas</div>
                    <div class="space"></div>
                    <div class="name">{{ strtoupper($petugas) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>