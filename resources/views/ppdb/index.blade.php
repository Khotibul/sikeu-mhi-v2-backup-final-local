@extends('layouts.app')

@section('title', 'Data PPDB - SIKEU MHI V2')
@section('page_title', 'Data PPDB')
@section('page_subtitle', 'Kelola data pendaftaran calon santri baru.')

@section('content')
    <style>
        .ppdb-hero {
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            border-radius: 30px;
            padding: 28px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden
        }

        .ppdb-hero:after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            right: -65px;
            top: -80px
        }

        .ppdb-hero h2 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 950;
            position: relative;
            z-index: 1
        }

        .ppdb-hero p {
            margin: 0;
            line-height: 1.7;
            font-weight: 700;
            position: relative;
            z-index: 1
        }

        .ppdb-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 18px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden
        }

        .stat-card:after {
            content: "";
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 999px;
            background: var(--tosca-soft);
            right: -30px;
            top: -34px
        }

        .stat-card.pink:after {
            background: var(--pink-soft)
        }

        .stat-card.warn:after {
            background: var(--warning-soft)
        }

        .stat-card span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            margin-bottom: 8px;
            position: relative;
            z-index: 1
        }

        .stat-card strong {
            display: block;
            color: var(--tosca-dark);
            font-size: 28px;
            font-weight: 950;
            position: relative;
            z-index: 1
        }

        .stat-card.pink strong {
            color: var(--pink-dark)
        }

        .stat-card.warn strong {
            color: #b45309
        }

        .ppdb-panel {
            background: white;
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 22px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden
        }

        .ppdb-panel:after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: url('{{ asset('images/logo-mhi.png') }}') center/contain no-repeat;
            opacity: .028;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none
        }

        .panel-content {
            position: relative;
            z-index: 1
        }

        .ppdb-toolbar {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 16px
        }

        .filter-ppdb {
            display: grid;
            grid-template-columns: 1.4fr .65fr .65fr .65fr auto auto;
            gap: 10px;
            align-items: end
        }

        .field {
            display: grid;
            gap: 7px
        }

        .field label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 950;
            text-transform: uppercase
        }

        .control {
            width: 100%;
            height: 46px;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0 14px;
            font-weight: 800;
            outline: none;
            background: white;
            color: var(--text)
        }

        textarea.control {
            height: 90px;
            padding: 12px 14px;
            resize: vertical;
            line-height: 1.5
        }

        .control:focus {
            border-color: var(--tosca);
            box-shadow: 0 0 0 4px rgba(18, 169, 154, .1)
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #d7e1e7;
            border-radius: 20px
        }

        .ppdb-table {
            width: 100%;
            min-width: 1120px;
            border-collapse: collapse;
            background: white;
            font-size: 14px
        }

        .ppdb-table th,
        .ppdb-table td {
            border: 1px solid #d7e1e7;
            padding: 11px 10px;
            text-align: left;
            vertical-align: middle
        }

        .ppdb-table th {
            background: #e7f9f6;
            color: var(--tosca-dark);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase
        }

        .student {
            display: flex;
            gap: 11px;
            align-items: center
        }

        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--tosca-dark);
            font-weight: 950;
            flex: 0 0 auto
        }

        .student strong {
            display: block;
            text-transform: uppercase;
            font-weight: 950
        }

        .student small {
            color: var(--muted);
            font-weight: 800;
            line-height: 1.5
        }

        .badge {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap
        }

        .b-pending {
            background: var(--warning-soft);
            color: #b45309
        }

        .b-diterima {
            background: var(--tosca-soft);
            color: var(--tosca-dark)
        }

        .b-ditolak {
            background: var(--pink-soft);
            color: var(--pink-dark)
        }

        .b-soft {
            background: #f1f5f9;
            color: #334155
        }

        .btn-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white !important;
            border: none;
            padding: 10px 16px;
            border-radius: 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-success:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--tosca), var(--tosca-dark, #0f766e));
            color: white !important;
            border: none;
            padding: 10px 16px;
            border-radius: 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-light {
            background: #f1f5f9;
            color: #334155 !important;
            border: 1px solid var(--border);
            padding: 10px 16px;
            border-radius: 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .aksi {
            display: flex;
            gap: 7px;
            flex-wrap: wrap
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            font-weight: 950
        }

        .edit {
            background: #eefbf8;
            color: var(--tosca-dark)
        }

        .print {
            background: linear-gradient(135deg, var(--tosca), #087c73);
            color: white !important
        }

        .accept {
            background: linear-gradient(135deg, #22c55e, #15803d);
            color: white
        }

        .delete {
            background: linear-gradient(135deg, #ef476f, #e11d48);
            color: white
        }

        .modal-ppdb {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px
        }

        .modal-ppdb.show {
            display: flex
        }

        .modal-box {
            width: 980px;
            max-width: 100%;
            max-height: 92vh;
            overflow-y: auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .28)
        }

        .modal-head {
            padding: 22px 24px;
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            border-radius: 28px 28px 0 0;
            display: flex;
            justify-content: space-between;
            gap: 12px
        }

        .modal-head h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 950
        }

        .modal-head p {
            margin: 5px 0 0
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            border: 0;
            background: rgba(255, 255, 255, .22);
            color: white;
            font-size: 18px;
            cursor: pointer
        }

        .modal-body {
            padding: 22px 24px
        }

        .form-modal {
            display: grid;
            gap: 14px
        }

        .grid2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px
        }

        .grid3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px
        }

        .section-title {
            margin: 5px 0;
            color: var(--tosca-dark);
            font-size: 16px;
            font-weight: 950;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 8px
        }

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 8px
        }

        .empty {
            text-align: center;
            padding: 42px 15px;
            color: var(--muted)
        }

        .empty h3 {
            color: var(--tosca-dark);
            margin: 0 0 8px;
            font-weight: 950
        }

        .berkas-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .62);
            backdrop-filter: blur(4px);
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px
        }

        .berkas-modal.show {
            display: flex
        }

        .berkas-box {
            width: 1040px;
            max-width: 100%;
            max-height: 92vh;
            overflow-y: auto;
            background: white;
            border-radius: 28px;
            border: 1px solid rgba(15, 118, 110, .18);
            box-shadow: 0 30px 80px rgba(15, 23, 42, .30)
        }

        .berkas-head {
            padding: 22px 24px;
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            border-radius: 28px 28px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px
        }

        .berkas-head h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 950
        }

        .berkas-head p {
            margin: 5px 0 0;
            font-size: 13px;
            opacity: .94;
            font-weight: 750;
            line-height: 1.5
        }

        .berkas-close {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 14px;
            background: rgba(255, 255, 255, .22);
            color: white;
            font-size: 20px;
            font-weight: 950;
            cursor: pointer
        }

        .berkas-body {
            padding: 22px
        }

        .berkas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px
        }

        .berkas-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 16px;
            background: #fbfffe;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .04)
        }

        .berkas-card h4 {
            margin: 0 0 12px;
            color: var(--tosca-dark);
            font-size: 15px;
            font-weight: 950;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .berkas-preview {
            width: 100%;
            height: 260px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #64748b;
            font-weight: 850;
            text-align: center
        }

        .berkas-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: white
        }

        .berkas-empty {
            color: #94a3b8;
            font-weight: 850;
            line-height: 1.6
        }

        .berkas-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .berkas-btn {
            border: none;
            border-radius: 13px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--tosca), #087c73);
            color: #fff !important
        }

        .berkas-btn.light {
            background: #f1f5f9;
            color: #334155 !important;
            border: 1px solid var(--border)
        }

        .berkas-count {
            position: absolute;
            right: -3px;
            top: -3px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #e11d48;
            color: white;
            font-size: 10px;
            font-weight: 950;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white
        }

        /* FIX PAGINATION */
        .pagination-wrapper {
            margin-top: 25px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .pagination-wrapper nav svg {
            width: 20px !important;
            height: 20px !important;
            max-width: 20px !important;
        }

        .pagination-wrapper .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            gap: 6px;
            margin: 0;
            justify-content: flex-end;
            align-items: center;
        }

        .pagination-wrapper .page-item .page-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            background-color: #fff;
            border: 1px solid #d7e1e7;
            border-radius: 12px;
            color: #334155;
            font-weight: 800;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, var(--tosca), #087c73);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.2);
        }

        .pagination-wrapper .page-item:not(.active):not(.disabled) .page-link:hover {
            border-color: var(--tosca);
            color: var(--tosca-dark);
            background: #f0fdfa;
            transform: translateY(-2px);
        }

        .pagination-wrapper .page-item.disabled .page-link {
            opacity: 0.5;
            background: #f8fafc;
            cursor: not-allowed;
        }

        .pagination-wrapper nav>div>div {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pagination-wrapper nav span[aria-current="page"]>span {
            background: linear-gradient(135deg, var(--tosca), #087c73) !important;
            color: white !important;
            border-radius: 12px;
        }

        .pagination-wrapper nav a {
            border-radius: 12px !important;
        }

        @media(max-width:1000px) {

            .ppdb-stats,
            .ppdb-toolbar,
            .filter-ppdb,
            .grid2,
            .grid3,
            .berkas-grid {
                grid-template-columns: 1fr
            }

            .modal-foot {
                flex-direction: column
            }

            .modal-foot .btn,
            .modal-foot .btn-primary,
            .modal-foot .btn-light {
                width: 100%
            }

            .berkas-preview {
                height: 220px
            }
        }
    </style>

    @php
        $ppdbs = $ppdbs ?? ($dataPpdb ?? ($pendaftar ?? collect()));
        $search = $search ?? request('search');
        $tahunAjaran = $tahunAjaran ?? request('tahun_ajaran', 'semua');
        $statusSeleksi = $statusSeleksi ?? request('status_seleksi', 'semua');
        $unit = $unit ?? request('unit', 'semua');
        $listTahunAjaran = $listTahunAjaran ?? ($tahunAjaranList ?? ['2025/2026', '2026/2027']);
        $statusOptions = ['Pending', 'Diterima', 'Ditolak'];
        $itemsCount = method_exists($ppdbs, 'items') ? collect($ppdbs->items()) : collect($ppdbs);
        $totalPendaftar = $totalPendaftar ?? $itemsCount->count();
        $totalPending = $totalPending ?? $itemsCount->where('status_seleksi', 'Pending')->count();
        $totalDiterima = $totalDiterima ?? $itemsCount->where('status_seleksi', 'Diterima')->count();
        $totalDitolak = $totalDitolak ?? $itemsCount->where('status_seleksi', 'Ditolak')->count();

        $statusClass = function ($status) {
            $status = strtolower((string) $status);
            if ($status === 'diterima') {
                return 'b-diterima';
            }
            if ($status === 'ditolak') {
                return 'b-ditolak';
            }
            return 'b-pending';
        };

        // BYPASS: Fungsi baru untuk tembak langsung ke file fisik pakai URL Web!
        $fileUrl = function ($path) {
            if (!$path) {
                return null;
            }
            $cleanPath = preg_replace('#^(public|storage)/#', '', ltrim((string) $path, '/'));
            return asset($cleanPath);
        };

        $isImageFile = function ($path) {
            return $path && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', (string) $path);
        };

        $isPdfFile = function ($path) {
            return $path && preg_match('/\.pdf$/i', (string) $path);
        };

        $countBerkas = function ($item) {
            return collect([
                $item->file_kk ?? null,
                $item->file_ktp ?? null,
                $item->file_foto ?? null,
                $item->file_ijazah ?? null,
            ])
                ->filter()
                ->count();
        };
    @endphp

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data:</strong>
            <ul style="margin:8px 0 0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ppdb-hero">
        <h2>Data PPDB Santri</h2>
        <p>Kelola data pendaftaran calon santri baru, status seleksi, cetak bukti pendaftaran, dan pindahkan calon santri
            yang diterima ke data santri aktif.</p>
    </div>

    <div class="ppdb-stats">
        <div class="stat-card"><span>Total Pendaftar</span><strong>{{ number_format($totalPendaftar, 0, ',', '.') }}</strong>
        </div>
        <div class="stat-card warn"><span>Pending</span><strong>{{ number_format($totalPending, 0, ',', '.') }}</strong>
        </div>
        <div class="stat-card"><span>Diterima</span><strong>{{ number_format($totalDiterima, 0, ',', '.') }}</strong></div>
        <div class="stat-card pink"><span>Ditolak</span><strong>{{ number_format($totalDitolak, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="ppdb-panel">
        <div class="panel-content">
            <div class="ppdb-toolbar">
                <form action="{{ route('ppdb.index') }}" method="GET" class="filter-ppdb">
                    <div class="field">
                        <label>Cari Pendaftar</label>
                        <input type="text" name="search" class="control" value="{{ $search }}"
                            placeholder="Nama, no daftar, NISN...">
                    </div>
                    <div class="field">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="control">
                            <option value="semua"
                                {{ strtolower($tahunAjaran) === 'semua' || $tahunAjaran === '' ? 'selected' : '' }}>Semua
                            </option>
                            @foreach ($listTahunAjaran as $ta)
                                <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                                    {{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Unit / Jenjang</label>
                        <select name="unit" class="control">
                            <option value="semua" {{ strtolower($unit) === 'semua' || $unit === '' ? 'selected' : '' }}>
                                Semua</option>
                            @foreach ($unitOptions ?? ['MTS' => 'MTs', 'SMP' => 'SMP', 'MA' => 'MA', 'SMK' => 'SMK'] as $unitValue => $unitLabel)
                                <option value="{{ $unitValue }}"
                                    {{ strtoupper($unit) == strtoupper($unitValue) ? 'selected' : '' }}>
                                    {{ $unitLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status_seleksi" class="control">
                            <option value="semua"
                                {{ strtolower($statusSeleksi) === 'semua' || $statusSeleksi === '' ? 'selected' : '' }}>
                                Semua</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}"
                                    {{ strtolower($statusSeleksi) == strtolower($statusOption) ? 'selected' : '' }}>
                                    {{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">🔎 Tampilkan</button>
                    <a href="{{ route('ppdb.index') }}" class="btn-light">Reset</a>
                </form>

                <div class="btn-wrapper">
                    <div style="position:relative; display:inline-block;">
                        <button type="button" class="btn-primary" id="exportBtn" onclick="toggleExportMenu()"
                            style="background: linear-gradient(135deg, #10b981, #059669);">⬇ Export Excel</button>
                        <div class="export-menu" id="exportMenu"
                            style="position:absolute; right:0; top:100%; margin-top:8px; background:white; border:1px solid var(--border); border-radius:12px; box-shadow:0 10px 25px rgba(15,23,42,.12); min-width:160px; z-index:100; display:none;">
                            <a href="{{ route('ppdb.export',request()->merge(['format' => 'csv'])->query()) }}"
                                style="display:block; padding:12px 16px; text-decoration:none; color:var(--text); border-bottom:1px solid var(--border); font-weight:800;"
                                onmouseover="this.style.background='#f5f5f5'"
                                onmouseout="this.style.background='transparent'">📄 Download CSV</a>
                            <a href="{{ route('ppdb.export',request()->merge(['format' => 'xlsx'])->query()) }}"
                                style="display:block; padding:12px 16px; text-decoration:none; color:var(--text); font-weight:800;"
                                onmouseover="this.style.background='#f5f5f5'"
                                onmouseout="this.style.background='transparent'">📊 Download Excel</a>
                        </div>
                    </div>
                    <button type="button" class="btn-primary" onclick="openPpdbModal('modalTambahPpdb')">
                        + Tambah Pendaftar
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="ppdb-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Identitas Pendaftar</th>
                            <th>Tanggal</th>
                            <th>Jenjang</th>
                            <th>Pondok</th>
                            <th>Orang Tua</th>
                            <th>Status</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ppdbs as $index => $item)
                            @php
                                $status = $item->status_seleksi ?? 'Pending';
                                $modalId = 'modalEditPpdb' . $item->id_daftar;
                                $jumlahBerkas = $countBerkas($item);
                            @endphp
                            <tr>
                                <td>{{ method_exists($ppdbs, 'firstItem') ? $ppdbs->firstItem() + $index : $index + 1 }}
                                </td>
                                <td>
                                    <div class="student">
                                        <div class="avatar">{{ strtoupper(substr($item->nama_lengkap ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $item->nama_lengkap ?? '-' }}</strong>
                                            <small>
                                                No: {{ $item->no_daftar ?? '-' }} | NISN: {{ $item->nisn ?? '-' }}<br>
                                                JK: {{ $item->jk ?? '-' }} |
                                                TTL: {{ $item->tempat_lahir ?? '-' }},
                                                {{ !empty($item->tgl_lahir) ? \Carbon\Carbon::parse($item->tgl_lahir)->format('d-m-Y') : '-' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ !empty($item->tgl_daftar) ? \Carbon\Carbon::parse($item->tgl_daftar)->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $item->jenjang_sekolah ?? '-' }}</strong><br>
                                    <small style="color:var(--muted);font-weight:800">
                                        Jurusan: {{ $item->jurusan ?? '-' }}<br>
                                        Diniyah: {{ $item->kelas_diniyah ?? '-' }}
                                    </small>
                                </td>
                                <td><span
                                        class="badge b-soft">{{ ($item->status_pondok ?? 'Ya') === 'Ya' ? 'Pondok' : 'Tidak Pondok' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $item->nama_ayah ?? '-' }}</strong><br>
                                    <small style="color:var(--muted);font-weight:800">
                                        Ibu: {{ $item->nama_ibu ?? '-' }}<br>
                                        HP: {{ $item->no_hp_ortu ?? '-' }}
                                    </small>
                                </td>
                                <td><span class="badge {{ $statusClass($status) }}">{{ $status }}</span></td>
                                <td>{{ $item->tahun_ajaran ?? '-' }}</td>
                                <td>
                                    <div class="aksi">
                                        <button type="button" class="icon-btn edit" title="Edit"
                                            onclick="openPpdbModal('{{ $modalId }}')">✏️</button>
                                        <a href="{{ route('ppdb.cetak', $item->id_daftar) }}" target="_blank"
                                            class="icon-btn print" title="Cetak">🖨</a>
                                        <button type="button" class="icon-btn print" title="Lihat Berkas"
                                            onclick="openBerkasModal('modalBerkas{{ $item->id_daftar }}')"
                                            style="position:relative;">
                                            📁
                                            @if ($jumlahBerkas > 0)
                                                <span class="berkas-count">{{ $jumlahBerkas }}</span>
                                            @endif
                                        </button>

                                        @if (($item->status_seleksi ?? 'Pending') === 'Diterima')
                                            <form action="{{ route('ppdb.terima-santri', $item->id_daftar) }}"
                                                method="POST"
                                                data-confirm="Pindahkan {{ $item->nama_lengkap }} ke Data Santri?">
                                                @csrf
                                                <button type="submit" class="icon-btn accept"
                                                    title="Jadikan Santri">✅</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('ppdb.destroy', $item->id_daftar) }}" method="POST"
                                            data-confirm="Hapus data PPDB {{ $item->nama_lengkap }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn delete" title="Hapus">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty">
                                        <h3>Belum ada data PPDB</h3>
                                        <p>Data pendaftar akan tampil setelah calon santri ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($ppdbs, 'links'))
                <div class="pagination-wrapper">
                    {{ $ppdbs->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal-ppdb" id="modalTambahPpdb">
        <div class="modal-box">
            <div class="modal-head">
                <div>
                    <h3>Tambah Pendaftar PPDB</h3>
                    <p>Input data calon santri sesuai formulir pendaftaran.</p>
                </div>
                <button type="button" class="modal-close" onclick="closePpdbModal('modalTambahPpdb')">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data"
                    class="form-modal" data-confirm="Simpan data pendaftar baru?">
                    @csrf
                    @include('ppdb._form', ['item' => null, 'statusOptions' => $statusOptions])
                    <div class="modal-foot">
                        <button type="button" class="btn-light"
                            onclick="closePpdbModal('modalTambahPpdb')">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Pendaftar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    @foreach ($ppdbs as $item)
        @php $modalId = 'modalEditPpdb' . $item->id_daftar; @endphp
        <div class="modal-ppdb" id="{{ $modalId }}">
            <div class="modal-box">
                <div class="modal-head">
                    <div>
                        <h3>Edit Data PPDB</h3>
                        <p>Perbarui data pendaftar {{ $item->nama_lengkap }}.</p>
                    </div>
                    <button type="button" class="modal-close"
                        onclick="closePpdbModal('{{ $modalId }}')">×</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ppdb.update', $item->id_daftar) }}" method="POST"
                        enctype="multipart/form-data" class="form-modal" data-confirm="Simpan perubahan data PPDB?">
                        @csrf
                        @method('PUT')
                        @include('ppdb._form', ['item' => $item, 'statusOptions' => $statusOptions])
                        <div class="modal-foot">
                            <button type="button" class="btn-light"
                                onclick="closePpdbModal('{{ $modalId }}')">Batal</button>
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Modal Berkas: Menggunakan Bypass URL (Natively via Browser) --}}
    @foreach ($ppdbs as $item)
        @php
            $berkasModalId = 'modalBerkas' . $item->id_daftar;
            $berkasList = [
                'Kartu Keluarga' => ['path' => $item->file_kk ?? null, 'icon' => '👨‍👩‍👧‍👦'],
                'KTP Orang Tua' => ['path' => $item->file_ktp ?? null, 'icon' => '🪪'],
                'Foto Santri' => ['path' => $item->file_foto ?? null, 'icon' => '🧑'],
                'Ijazah / SKL' => ['path' => $item->file_ijazah ?? null, 'icon' => '📄'],
            ];
        @endphp

        <div class="berkas-modal" id="{{ $berkasModalId }}">
            <div class="berkas-box">
                <div class="berkas-head">
                    <div>
                        <h3>Berkas Pendaftaran</h3>
                        <p>{{ $item->nama_lengkap ?? '-' }} · No. Daftar: {{ $item->no_daftar ?? '-' }}</p>
                    </div>
                    <button type="button" class="berkas-close"
                        onclick="closeBerkasModal('{{ $berkasModalId }}')">×</button>
                </div>

                <div class="berkas-body">
                    <div class="berkas-grid">
                        @foreach ($berkasList as $label => $berkas)
                            @php
                                $path = $berkas['path'] ?? null;
                                $url = $fileUrl($path);
                                $icon = $berkas['icon'];
                            @endphp

                            <div class="berkas-card">
                                <h4><span>{{ $icon }}</span> <span>{{ $label }}</span></h4>

                                <div class="berkas-preview">
                                    @if ($url && $isImageFile($path))
                                        <img src="{{ $url }}" alt="{{ $label }}"
                                            onerror="this.outerHTML='<div class=\'berkas-empty\' style=\'color:#ef476f;\'>⚠ File fisik terhapus/hilang di server</div>'">
                                    @elseif($url && $isPdfFile($path))
                                        <div>
                                            <div style="font-size:46px;margin-bottom:8px;">📄</div>
                                            <div>File PDF tersedia</div>
                                            <small style="display:block;margin-top:6px;color:#94a3b8;">Silakan download
                                                atau buka.</small>
                                        </div>
                                    @elseif($url)
                                        <div>
                                            <div style="font-size:46px;margin-bottom:8px;">📎</div>
                                            <div>File tersedia</div>
                                        </div>
                                    @else
                                        <div class="berkas-empty">Belum upload berkas</div>
                                    @endif
                                </div>

                                <div class="berkas-actions">
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" class="berkas-btn">👁 Buka</a>
                                        <a href="{{ $url }}" download class="berkas-btn light">⬇ Download</a>
                                    @else
                                        <span class="berkas-empty">Tidak ada file</span>
                                    @endif
                                </div>

                                @if ($path)
                                    <small
                                        style="display:block;margin-top:10px;color:#64748b;font-weight:750;word-break:break-all;">
                                        {{ $path }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function openPpdbModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('show');
        }

        function closePpdbModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('show');
        }

        function openBerkasModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('show');
        }

        function closeBerkasModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('show');
        }

        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            if (menu) {
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            }
        }

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-ppdb') || event.target.classList.contains('berkas-modal')) {
                event.target.classList.remove('show');
            }

            const exportMenu = document.getElementById('exportMenu');
            const exportBtn = document.getElementById('exportBtn');
            if (exportMenu && event.target !== exportBtn && !event.target.closest('.export-menu') && !event.target
                .closest('#exportBtn')) {
                exportMenu.style.display = 'none';
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-ppdb.show, .berkas-modal.show').forEach(function(modal) {
                    modal.classList.remove('show');
                });
                const exportMenu = document.getElementById('exportMenu');
                if (exportMenu) {
                    exportMenu.style.display = 'none';
                }
            }
        });
    </script>
@endsection
