@extends('layouts.app')

@section('title', 'Laporan Tunggakan - SIKEU MHI V2')

@section('page_title', 'Laporan Tunggakan')

@section('page_subtitle', 'Rekap tunggakan biaya pendidikan formal dan pondok berdasarkan filter kelas dan bulan.')

@section('content')
<style>
    .report-filter {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 160px 160px 180px 180px auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group {
        display: grid;
        gap: 7px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        text-transform: uppercase;
    }

    .filter-control {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 13px;
        font-size: 14px;
        outline: none;
        background: white;
        color: var(--text);
    }

    .filter-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18,169,154,.10);
    }

    .print-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 16px;
    }

    .report-paper {
        background: white;
        border-radius: 22px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-soft);
        padding: 26px;
        position: relative;
        overflow: hidden;
    }

    .report-paper::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .04;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .report-content {
        position: relative;
        z-index: 1;
    }

    .report-header {
        text-align: center;
        border-bottom: 3px double var(--tosca);
        padding-bottom: 14px;
        margin-bottom: 18px;
    }

    .report-header h2 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .report-header h1 {
        margin: 4px 0;
        color: var(--pink-dark);
        font-size: 22px;
        text-transform: uppercase;
        letter-spacing: .02em;
    }

    .report-header p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
    }

    .report-title {
        text-align: center;
        margin: 18px 0;
    }

    .report-title h3 {
        margin: 0;
        font-size: 20px;
        text-decoration: underline;
        color: var(--text);
    }

    .report-meta {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .meta-box {
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 12px 14px;
        background: #fbfffe;
    }

    .meta-box span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .meta-box strong {
        color: var(--tosca-dark);
        font-size: 15px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-box {
        border-radius: 20px;
        padding: 18px;
        background: linear-gradient(135deg, var(--tosca-soft), white);
        border: 1px solid rgba(18,169,154,.18);
    }

    .summary-box.pink {
        background: linear-gradient(135deg, var(--pink-soft), white);
        border-color: rgba(227,69,109,.18);
    }

    .summary-box span {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .summary-box strong {
        display: block;
        font-size: 24px;
        color: var(--tosca-dark);
        font-weight: 950;
    }

    .summary-box.pink strong {
        color: var(--pink-dark);
    }

    .simple-report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 13px;
        background: rgba(255,255,255,.96);
    }

    .simple-report-table th,
    .simple-report-table td {
        border: 1px solid #d1d5db;
        padding: 9px 10px;
        text-align: left;
        vertical-align: top;
    }

    .simple-report-table th {
        background: #e3faf7;
        color: var(--tosca-dark);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .03em;
    }

    .simple-report-table td.number {
        text-align: right;
        white-space: nowrap;
        font-weight: 800;
    }

    .student-name-report {
        font-weight: 900;
        color: var(--text);
    }

    .student-small-report {
        color: #6b7280;
        font-size: 12px;
        margin-top: 3px;
    }

    .month-list-text {
        font-size: 12px;
        line-height: 1.45;
        color: var(--text);
    }

    .report-footer {
        display: grid;
        grid-template-columns: 1fr 240px;
        gap: 20px;
        margin-top: 26px;
        font-size: 13px;
    }

    .signature {
        text-align: center;
    }

    .signature-space {
        height: 58px;
    }

    .signature strong {
        text-decoration: underline;
    }

    .empty-report {
        text-align: center;
        padding: 44px 20px;
    }

    .empty-report h3 {
        color: var(--tosca-dark);
        margin: 0 0 10px;
        font-size: 22px;
    }

    .empty-report p {
        color: var(--muted);
        margin: 0;
        line-height: 1.7;
    }

    .initial-state {
        text-align: center;
        padding: 46px 22px;
    }

    .initial-icon {
        width: 82px;
        height: 82px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 18px;
    }

    .initial-state h3 {
        color: var(--tosca-dark);
        margin: 0 0 10px;
        font-size: 22px;
    }

    .initial-state p {
        color: var(--muted);
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    @media print {
        body {
            background: white;
        }

        .sidebar,
        .topbar,
        .print-actions,
        .report-filter {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .report-paper {
            box-shadow: none;
            border-radius: 0;
            border: none;
            padding: 12px;
        }

        .report-header h2 {
            font-size: 15px;
        }

        .report-header h1 {
            font-size: 18px;
        }

        .report-title h3 {
            font-size: 17px;
        }

        .report-meta {
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .meta-box {
            padding: 8px;
        }

        .meta-box span {
            font-size: 10px;
        }

        .meta-box strong {
            font-size: 12px;
        }

        .summary-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .summary-box {
            padding: 10px;
        }

        .summary-box span {
            font-size: 10px;
        }

        .summary-box strong {
            font-size: 16px;
        }

        .simple-report-table {
            font-size: 10px;
        }

        .simple-report-table th {
            font-size: 9px;
        }

        .simple-report-table th,
        .simple-report-table td {
            padding: 5px 6px;
        }

        .student-small-report,
        .month-list-text {
            font-size: 9px;
        }

        .report-footer {
            font-size: 11px;
            margin-top: 16px;
        }

        .signature-space {
            height: 42px;
        }

        @page {
            size: A4 portrait;
            margin: 1cm;
        }
    }

    @media(max-width: 1200px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .report-meta,
        .summary-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 800px) {
        .filter-grid,
        .report-meta,
        .summary-grid,
        .report-footer {
            grid-template-columns: 1fr;
        }

        .print-actions {
            justify-content: stretch;
        }

        .print-actions .btn {
            width: 100%;
        }
    }
</style>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="report-filter">
    <form action="{{ route('tunggakan.index') }}" method="GET" class="filter-grid">
        <input type="hidden" name="proses" value="1">

        <div class="filter-group">
            <label>Cari Santri</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="filter-control"
                placeholder="Nama, NIS, wali..."
            >
        </div>

        <div class="filter-group">
            <label>Tahun Ajaran</label>
            <select name="tahun_ajaran" class="filter-control" required>
                @foreach($tahunAjaranList as $item)
                    <option value="{{ $item }}" {{ $tahunAjaran == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Sampai Bulan</label>
            <select name="sampai_bulan" class="filter-control" required>
                <option value="">-- Pilih Bulan --</option>
                @foreach(['Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni'] as $bulan)
                    <option value="{{ $bulan }}" {{ $sampaiBulan == $bulan ? 'selected' : '' }}>
                        {{ $bulan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Kelas Formal</label>
            <select name="kelas_formal" class="filter-control">
                <option value="">-- Tidak Dicek --</option>
                @foreach($kelasFormalList as $kelas)
                    <option value="{{ $kelas->nama_kelas }}" {{ $kelasFormalFilter == $kelas->nama_kelas ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Kelas Diniyah</label>
            <select name="kelas_diniyah" class="filter-control">
                <option value="">-- Tidak Dicek --</option>
                @foreach($kelasDiniyahList as $kelas)
                    <option value="{{ $kelas->nama_kelas }}" {{ $kelasDiniyahFilter == $kelas->nama_kelas ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>
    </form>
</div>

@if(!$isProcessed)
    <div class="report-paper">
        <div class="report-content initial-state">
            <div class="initial-icon">📄</div>

            <h3>Silakan pilih filter laporan terlebih dahulu</h3>

            <p>
                Pilih <strong>tahun ajaran</strong>, <strong>sampai bulan</strong>,
                dan minimal salah satu dari <strong>kelas formal</strong> atau
                <strong>kelas diniyah</strong>. Setelah itu klik tombol
                <strong>Tampilkan</strong> untuk memproses laporan tunggakan.
            </p>
        </div>
    </div>
@endif

@if($isProcessed)
    <div class="print-actions">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            🖨 Cetak Laporan
        </button>
    </div>

    <div class="report-paper">
        <div class="report-content">
            <div class="report-header">
                <h2>Yayasan Pendidikan Pesantren</h2>
                <h1>Mamba'ul Khoiriyatil Islamiyah</h1>
                <p>Laporan Administrasi Keuangan Santri</p>
            </div>

            <div class="report-title">
                <h3>Laporan Tunggakan Biaya Pendidikan</h3>
            </div>

            <div class="report-meta">
                <div class="meta-box">
                    <span>Tahun Ajaran</span>
                    <strong>{{ $tahunAjaran }}</strong>
                </div>

                <div class="meta-box">
                    <span>Periode Sampai</span>
                    <strong>{{ $sampaiBulan }}</strong>
                </div>

                <div class="meta-box">
                    <span>Kelas Formal</span>
                    <strong>{{ $kelasFormalFilter ?: 'Tidak Dicek' }}</strong>
                </div>

                <div class="meta-box">
                    <span>Kelas Diniyah</span>
                    <strong>{{ $kelasDiniyahFilter ?: 'Tidak Dicek' }}</strong>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-box">
                    <span>Santri Menunggak</span>
                    <strong>{{ $jumlahSantriMenunggak }}</strong>
                </div>

                <div class="summary-box">
                    <span>Tunggakan Formal</span>
                    <strong>Rp {{ number_format($totalFormal, 0, ',', '.') }}</strong>
                </div>

                <div class="summary-box">
                    <span>Tunggakan Pondok</span>
                    <strong>Rp {{ number_format($totalPondok, 0, ',', '.') }}</strong>
                </div>

                <div class="summary-box pink">
                    <span>Total Tunggakan</span>
                    <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                </div>
            </div>

            <table class="simple-report-table">
                <thead>
                    <tr>
                        <th width="45">No</th>
                        <th>Nama Santri</th>
                        <th>Kelas Formal</th>
                        <th>Kelas Diniyah</th>
                        <th>Bulan Formal</th>
                        <th>Bulan Pondok</th>
                        <th>Tunggakan Formal</th>
                        <th>Tunggakan Pondok</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <div class="student-name-report">
                                    {{ $item['siswa']->nama_siswa }}
                                </div>

                                <div class="student-small-report">
                                    NIS: {{ $item['siswa']->nis ?: '-' }}
                                </div>
                            </td>

                            <td>{{ $item['siswa']->kelas_formal ?: '-' }}</td>

                            <td>{{ $item['siswa']->kelas_diniyah ?: '-' }}</td>

                            <td>
                                <div class="month-list-text">
                                    {{ count($item['bulan_formal']) ? implode(', ', $item['bulan_formal']) : '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="month-list-text">
                                    {{ count($item['bulan_pondok']) ? implode(', ', $item['bulan_pondok']) : '-' }}
                                </div>
                            </td>

                            <td class="number">
                                Rp {{ number_format($item['formal'], 0, ',', '.') }}
                            </td>

                            <td class="number">
                                Rp {{ number_format($item['pondok'], 0, ',', '.') }}
                            </td>

                            <td class="number">
                                Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-report">
                                    <h3>Tidak ada tunggakan 🎉</h3>
                                    <p>
                                        Untuk filter yang dipilih, tidak ditemukan data tunggakan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($laporan) > 0)
                    <tfoot>
                        <tr>
                            <th colspan="6" style="text-align:right;">Total</th>
                            <th style="text-align:right;">
                                Rp {{ number_format($totalFormal, 0, ',', '.') }}
                            </th>
                            <th style="text-align:right;">
                                Rp {{ number_format($totalPondok, 0, ',', '.') }}
                            </th>
                            <th style="text-align:right;">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>

            <div class="report-footer">
                <div>
                    <strong>Catatan:</strong><br>
                    Laporan ini dihitung berdasarkan data pembayaran yang tercatat pada sistem.
                    Untuk pembayaran formal, sumber data diambil dari tabel <strong>pembayaran</strong>.
                    Untuk pembayaran diniyah/pondok, sumber data diambil dari tabel
                    <strong>pembayaran_diniyah</strong>.
                </div>

                <div class="signature">
                    Jember, {{ date('d-m-Y') }}<br>
                    Bendahara Yayasan
                    <div class="signature-space"></div>
                    <strong>Ag. Ahmad Hulqi Khoir</strong>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection