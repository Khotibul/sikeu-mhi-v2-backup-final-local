@extends('layouts.app')

@section('title', 'Catatan Pengeluaran - SIKEU MHI V2')

@section('page_title', 'Catatan Pengeluaran')

@section('page_subtitle', 'Kelola catatan pengeluaran yayasan sesuai database lama.')

@section('content')
<style>
    .expense-filter {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 155px 155px 170px auto auto;
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
        letter-spacing: .03em;
    }

    .filter-control,
    .modal-control {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 13px;
        font-size: 14px;
        outline: none;
        background: #fff;
        color: var(--text);
    }

    .filter-control:focus,
    .modal-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
    }

    textarea.modal-control {
        resize: vertical;
        min-height: 100px;
    }

    .expense-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 999px;
        background: var(--pink-soft);
        right: -30px;
        top: -34px;
        opacity: .8;
    }

    .summary-card.tosca::after {
        background: var(--tosca-soft);
    }

    .summary-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .summary-card strong {
        display: block;
        color: var(--pink-dark);
        font-size: 30px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .summary-card.tosca strong {
        color: var(--tosca-dark);
    }

    .expense-report {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .expense-report::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .035;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .expense-content {
        position: relative;
        z-index: 1;
    }

    .report-header {
        text-align: center;
        margin-bottom: 22px;
        border-bottom: 3px double var(--tosca);
        padding-bottom: 14px;
    }

    .report-header h2 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 18px;
        text-transform: uppercase;
        font-weight: 900;
    }

    .report-header h1 {
        margin: 4px 0;
        color: var(--pink-dark);
        font-size: 24px;
        text-transform: uppercase;
        font-weight: 950;
    }

    .report-header p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .expense-table-wrap {
        overflow-x: auto;
        border-radius: 18px;
        border: 1px solid #d7e1e7;
        background: rgba(255,255,255,.96);
    }

    .expense-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: rgba(255,255,255,.96);
    }

    .expense-table th,
    .expense-table td {
        border: 1px solid #d7e1e7;
        padding: 12px 10px;
        text-align: left;
        vertical-align: middle;
    }

    .expense-table th {
        color: var(--tosca-dark);
        font-size: 12px;
        text-transform: uppercase;
        background: #e7f9f6;
        letter-spacing: .03em;
        font-weight: 900;
        white-space: nowrap;
    }

    .expense-table tbody tr:nth-child(even) {
        background: #fcfefe;
    }

    .expense-table tbody tr:hover {
        background: #f7fbfb;
    }

    .expense-table td {
        font-size: 14px;
        color: #1f2937;
    }

    .expense-table td.number {
        text-align: right;
        font-weight: 800;
        white-space: nowrap;
        font-size: 14px;
        color: var(--tosca-dark);
        min-width: 150px;
    }

    .expense-table td.penerima-col strong {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
    }

    .unit-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        background: #ffe6ef;
        color: var(--pink-dark);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .bukti-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #e6fbf8;
        color: var(--tosca-dark);
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
    }

    .no-bukti {
        color: var(--muted);
        font-size: 13px;
    }

    .action-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .mini-btn {
        border: none;
        border-radius: 12px;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        transition: .15s ease-in-out;
        line-height: 1;
        box-shadow: 0 4px 10px rgba(15, 23, 42, .06);
    }

    .mini-btn:hover {
        transform: translateY(-1px);
    }

    .mini-btn .icon {
        font-size: 14px;
        line-height: 1;
    }

    .mini-btn.print {
        background: linear-gradient(135deg, var(--tosca), #0f9f95);
        color: #fff;
    }

    .mini-btn.edit {
        background: #f3f4f6;
        color: #374151;
    }

    .mini-btn.delete {
        background: linear-gradient(135deg, #ef476f, #e11d48);
        color: #fff;
    }

    .empty-state-table {
        text-align: center;
        padding: 34px 16px;
        color: var(--muted);
    }

    .empty-state-table h3 {
        margin: 0 0 8px;
        color: var(--tosca-dark);
        font-size: 20px;
    }

    .report-footer {
        display: grid;
        grid-template-columns: 1fr 240px;
        gap: 20px;
        margin-top: 30px;
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

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .55);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-card {
        width: 100%;
        max-width: 760px;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(15, 23, 42, .25);
        overflow: hidden;
        animation: modalPop .18s ease;
    }

    @keyframes modalPop {
        from {
            transform: translateY(16px) scale(.98);
            opacity: .6;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 950;
    }

    .modal-header p {
        margin: 4px 0 0;
        opacity: .9;
        font-size: 13px;
    }

    .modal-close {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: none;
        background: rgba(255,255,255,.20);
        color: white;
        font-size: 22px;
        cursor: pointer;
        font-weight: 900;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .modal-group {
        display: grid;
        gap: 7px;
    }

    .modal-group.full {
        grid-column: 1 / -1;
    }

    .modal-group label {
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        text-transform: uppercase;
    }

    .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .old-file-box {
        margin-top: 8px;
        font-size: 12px;
        color: var(--muted);
    }

    .btn[disabled],
    .mini-btn[disabled] {
        opacity: .68;
        cursor: wait !important;
        transform: none !important;
        pointer-events: none;
    }

    @media print {
        body {
            background: white;
        }

        .sidebar,
        .topbar,
        .expense-filter,
        .no-print,
        .main-header,
        .modal-overlay {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .expense-summary {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .summary-card {
            box-shadow: none;
            padding: 10px;
            border-radius: 12px;
        }

        .summary-card span {
            font-size: 10px;
        }

        .summary-card strong {
            font-size: 16px;
        }

        .expense-report {
            box-shadow: none;
            border: none;
            border-radius: 0;
            padding: 12px;
        }

        .report-header h2 {
            font-size: 15px;
        }

        .report-header h1 {
            font-size: 18px;
        }

        .report-header p {
            font-size: 11px;
        }

        .expense-table {
            font-size: 11px;
        }

        .expense-table th {
            font-size: 9px;
        }

        .expense-table td.number {
            font-size: 11px;
        }

        .expense-table th,
        .expense-table td {
            padding: 6px 6px;
        }

        .report-footer {
            font-size: 11px;
            margin-top: 18px;
        }

        .signature-space {
            height: 42px;
        }

        @page {
            size: A4 portrait;
            margin: 1cm;
        }
    }

    @media(max-width: 1100px) {
        .filter-grid,
        .expense-summary,
        .report-footer,
        .modal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $unitOptions = [
        'SMP',
        'MTS',
        'SMK',
        'MA',
        'SPM ULYA',
        "MA'HAD ALY",
        'MADIN NUHA',
        'YAYASAN',
        'PONDOK PA',
        'PONDOK PI',
    ];
@endphp

@if(session('success'))
    <div class="alert alert-success no-print">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger no-print">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger no-print">
        <strong>Periksa kembali data:</strong>
        <ul style="margin:8px 0 0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="expense-filter no-print">
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="filter-grid">
        <div class="filter-group">
            <label>Cari Pengeluaran</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="filter-control"
                placeholder="Penerima / unit / uraian..."
            >
        </div>

        <div class="filter-group">
            <label>Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="filter-control">
        </div>

        <div class="filter-group">
            <label>Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="filter-control">
        </div>

        <div class="filter-group">
            <label>Unit</label>
            <select name="unit" class="filter-control">
                <option value="">Semua</option>
                @foreach($unitOptions as $option)
                    <option value="{{ $option }}" {{ $unit == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach

                @foreach($unitList as $item)
                    @if(!in_array($item, $unitOptions))
                        <option value="{{ $item }}" {{ $unit == $item ? 'selected' : '' }}>
                            {{ $item }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>

        <button type="button" class="btn btn-danger js-open-create">
            + Tambah
        </button>
    </form>
</div>

<div class="expense-summary">
    <div class="summary-card">
        <span>Total Pengeluaran</span>
        <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card tosca">
        <span>Jumlah Transaksi</span>
        <strong>{{ number_format($pengeluaran->count(), 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card tosca">
        <span>Periode Laporan</span>
        <strong style="font-size:20px;">
            {{ \Carbon\Carbon::parse($tanggalAwal)->format('d-m-Y') }}
            -
            {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d-m-Y') }}
        </strong>
    </div>
</div>

<div class="no-print" style="display:flex; justify-content:flex-end; gap:10px; margin-bottom:16px;">
    <button type="button" onclick="window.print()" class="btn btn-primary">
        🖨 Cetak Laporan
    </button>
</div>

<div class="expense-report">
    <div class="expense-content">
        <div class="report-header">
            <h2>Yayasan Pendidikan Pesantren</h2>
            <h1>Mamba'ul Khoiriyatil Islamiyah</h1>
            <p>Laporan Catatan Pengeluaran</p>
        </div>

        <div class="expense-table-wrap">
            <table class="expense-table">
                <thead>
                    <tr>
                        <th width="55">No</th>
                        <th width="115">Tanggal</th>
                        <th width="220">Penerima</th>
                        <th width="140">Unit</th>
                        <th>Uraian</th>
                        <th width="170">Jumlah</th>
                        <th class="no-print" width="85">Bukti</th>
                        <th class="no-print" width="240">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pengeluaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}</td>

                            <td class="penerima-col">
                                <strong>{{ $item->penerima }}</strong>
                            </td>

                            <td>
                                <span class="unit-badge">
                                    {{ $item->unit ?: 'Umum' }}
                                </span>
                            </td>

                            <td>{{ $item->uraian ?: '-' }}</td>

                            <td class="number">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>

                            <td class="no-print">
                                @if($item->bukti_foto)
                                    <a
                                        href="{{ asset('uploads/pengeluaran/' . $item->bukti_foto) }}"
                                        target="_blank"
                                        class="bukti-link"
                                    >
                                        📎 Lihat
                                    </a>
                                @else
                                    <span class="no-bukti">-</span>
                                @endif
                            </td>

                            <td class="no-print">
                                <div class="action-group">
                                    <a
                                        href="{{ route('pengeluaran.cetak', $item->id_keluar) }}"
                                        target="_blank"
                                        class="mini-btn print"
                                        title="Cetak"
                                    >
                                        <span class="icon">🖨️</span>
                                        <span>Cetak</span>
                                    </a>

                                    <button
                                        type="button"
                                        class="mini-btn edit js-open-edit"
                                        data-id="{{ $item->id_keluar }}"
                                        title="Edit"
                                    >
                                        <span class="icon">✏️</span>
                                        <span>Edit</span>
                                    </button>

                                    <form
                                        action="{{ route('pengeluaran.destroy', $item->id_keluar) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data pengeluaran ini?')"
                                        style="display:inline;"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="mini-btn delete" title="Hapus">
                                            <span class="icon">🗑️</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-table">
                                    <h3>Belum ada data pengeluaran</h3>
                                    <p>Data pengeluaran belum ditemukan pada filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($pengeluaran->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right;">Total</th>
                            <th style="text-align:right;">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </th>
                            <th class="no-print"></th>
                            <th class="no-print"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="report-footer">
            <div>
                <strong>Catatan:</strong><br>
                Laporan ini berisi pengeluaran yang tercatat pada sistem sesuai periode yang dipilih.
                Bukti keluar per transaksi dapat dicetak melalui tombol <strong>Cetak</strong>.
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

<div class="modal-overlay no-print" id="createModal">
    <div class="modal-card">
        <form
            action="{{ route('pengeluaran.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="js-pengeluaran-submit"
        >
            @csrf
            <input type="hidden" name="_pengeluaran_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

            <div class="modal-header">
                <div>
                    <h3>Tambah Pengeluaran</h3>
                    <p>Input catatan pengeluaran kas yayasan.</p>
                </div>

                <button type="button" class="modal-close js-close-modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="modal-grid">
                    <div class="modal-group">
                        <label>Tanggal Keluar</label>
                        <input type="date" name="tgl_keluar" value="{{ date('Y-m-d') }}" class="modal-control" required>
                    </div>

                    <div class="modal-group">
                        <label>Penerima</label>
                        <input type="text" name="penerima" class="modal-control" placeholder="Nama penerima" required>
                    </div>

                    <div class="modal-group">
                        <label>Unit</label>
                        <select name="unit" class="modal-control" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($unitOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="modal-control" placeholder="0" min="0" required>
                    </div>

                    <div class="modal-group full">
                        <label>Uraian</label>
                        <textarea name="uraian" class="modal-control" placeholder="Tuliskan uraian pengeluaran..." required></textarea>
                    </div>

                    <div class="modal-group full">
                        <label>Bukti Foto</label>
                        <input type="file" name="bukti_foto" class="modal-control" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light js-close-modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

@foreach($pengeluaran as $item)
    <div class="modal-overlay no-print" id="editModal-{{ $item->id_keluar }}">
        <div class="modal-card">
            <form
                action="{{ route('pengeluaran.update', $item->id_keluar) }}"
                method="POST"
                enctype="multipart/form-data"
                class="js-pengeluaran-submit"
            >
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h3>Edit Pengeluaran</h3>
                        <p>Perbarui catatan pengeluaran kas.</p>
                    </div>

                    <button type="button" class="modal-close js-close-modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="modal-grid">
                        <div class="modal-group">
                            <label>Tanggal Keluar</label>
                            <input
                                type="date"
                                name="tgl_keluar"
                                value="{{ $item->tgl_keluar }}"
                                class="modal-control"
                                required
                            >
                        </div>

                        <div class="modal-group">
                            <label>Penerima</label>
                            <input
                                type="text"
                                name="penerima"
                                value="{{ $item->penerima }}"
                                class="modal-control"
                                required
                            >
                        </div>

                        <div class="modal-group">
                            <label>Unit</label>
                            <select name="unit" class="modal-control" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($unitOptions as $option)
                                    <option value="{{ $option }}" {{ $item->unit == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach

                                @if($item->unit && !in_array($item->unit, $unitOptions))
                                    <option value="{{ $item->unit }}" selected>
                                        {{ $item->unit }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="modal-group">
                            <label>Jumlah</label>
                            <input
                                type="number"
                                name="jumlah"
                                value="{{ $item->jumlah }}"
                                class="modal-control"
                                min="0"
                                required
                            >
                        </div>

                        <div class="modal-group full">
                            <label>Uraian</label>
                            <textarea name="uraian" class="modal-control" required>{{ $item->uraian }}</textarea>
                        </div>

                        <div class="modal-group full">
                            <label>Bukti Foto</label>
                            <input type="file" name="bukti_foto" class="modal-control" accept="image/*">

                            @if($item->bukti_foto)
                                <div class="old-file-box">
                                    Bukti lama:
                                    <a href="{{ asset('uploads/pengeluaran/' . $item->bukti_foto) }}" target="_blank">
                                        lihat bukti
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light js-close-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script>

    document.querySelectorAll('.js-pengeluaran-submit').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.submitting === '1') {
                event.preventDefault();
                return false;
            }

            form.dataset.submitting = '1';

            form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                button.disabled = true;
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = '⏳ Menyimpan...';
            });
        });
    });

    const createModal = document.getElementById('createModal');

    document.querySelectorAll('.js-open-create').forEach(function (button) {
        button.addEventListener('click', function () {
            createModal.classList.add('show');
        });
    });

    document.querySelectorAll('.js-open-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const modal = document.getElementById('editModal-' + id);

            if (modal) {
                modal.classList.add('show');
            }
        });
    });

    document.querySelectorAll('.js-close-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            this.closest('.modal-overlay').classList.remove('show');
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                overlay.classList.remove('show');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.show').forEach(function (modal) {
                modal.classList.remove('show');
            });
        }
    });
</script>
@endsection