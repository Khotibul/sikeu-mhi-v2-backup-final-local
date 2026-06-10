@extends('layouts.app')

@section('title', 'Data Santri - SIKEU MHI V2')
@section('page_title', 'Data Santri')
@section('page_subtitle', 'Kelola data santri, import, export, dan NIS otomatis.')

@section('content')
@php
    $namaKelas = function ($kelas) {
        return $kelas->nama_kelas
            ?? $kelas->nama_kelas_formal
            ?? $kelas->nama_kelas_diniyah
            ?? $kelas->kelas_formal
            ?? $kelas->kelas_diniyah
            ?? $kelas->nama
            ?? $kelas->kelas
            ?? '-';
    };
@endphp

<style>
    .siswa-card{background:#fff;border:1px solid rgba(15,118,110,.14);border-radius:24px;padding:20px;box-shadow:0 18px 55px rgba(15,23,42,.08);margin-bottom:18px}
    .siswa-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
    .siswa-title{margin:0;color:var(--tosca-dark,#0f766e);font-size:22px;font-weight:950}
    .siswa-muted{margin:5px 0 0;color:var(--muted,#64748b);font-size:13px}
    .siswa-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    .siswa-btn{border:none;border-radius:14px;padding:10px 14px;font-weight:900;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap}
    .siswa-btn-primary{background:linear-gradient(135deg,#0f9f8f,#087c73);color:#fff!important;box-shadow:0 12px 25px rgba(15,118,110,.2)}
    .siswa-btn-light{background:#f1f5f9;color:#334155!important;border:1px solid #dbe5ec}
    .siswa-btn-danger{background:linear-gradient(135deg,#f43f5e,#be123c);color:#fff!important}
    .siswa-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
    .siswa-stat{background:linear-gradient(135deg,#fff,#f8fffd);border:1px solid #dbeafe;border-radius:20px;padding:16px}
    .siswa-stat span{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em}
    .siswa-stat strong{display:block;margin-top:8px;color:#0f766e;font-size:26px;font-weight:950}
    .siswa-filter{display:grid;grid-template-columns:1.6fr repeat(4,1fr) auto;gap:10px;align-items:end}
    .siswa-field label{display:block;margin-bottom:6px;color:#475569;font-size:11px;font-weight:900;text-transform:uppercase}
    .siswa-control{width:100%;min-height:42px;border:1px solid #d5e1e8;border-radius:14px;padding:9px 12px;outline:none;font-weight:750;color:#0f172a;background:#fff}
    .siswa-control:focus{border-color:#0f9f8f;box-shadow:0 0 0 4px rgba(15,159,143,.12)}
    .siswa-import-form{margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .siswa-table-wrap{overflow-x:auto;border:1px solid #dbe5ec;border-radius:20px}
    .siswa-table{width:100%;min-width:1180px;border-collapse:collapse;background:#fff}
    .siswa-table th{background:#0f3d5c;color:#fff;padding:12px;font-size:11px;text-transform:uppercase;letter-spacing:.04em;text-align:left;white-space:nowrap}
    .siswa-table td{padding:12px;border-bottom:1px solid #eef2f7;vertical-align:middle;font-size:13px;color:#334155}
    .siswa-table tr:hover td{background:#f8fffd}
    .siswa-name{color:#0f172a;font-weight:950}
    .siswa-badge{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:950;text-transform:uppercase;background:#ccfbf1;color:#0f766e;margin:2px}
    .siswa-badge-pink{background:#fce7f3;color:#be185d}
    .siswa-nominal-list{display:flex;flex-direction:column;gap:5px;min-width:170px}
    .siswa-nominal-default{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:950;text-transform:uppercase;background:#f1f5f9;color:#64748b;margin:2px}
    .siswa-empty{text-align:center;padding:45px;color:#64748b;font-weight:850}
    .siswa-alert{border-radius:18px;padding:13px 16px;margin-bottom:14px;font-weight:800}
    .siswa-alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
    .siswa-alert-error{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3}
    .siswa-pagination{
        margin-top:18px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        border:1px solid #dbe5ec;
        border-radius:18px;
        padding:12px;
        background:#f8fffd;
    }
    .siswa-pagination-info{
        color:#64748b;
        font-size:12px;
        font-weight:850;
    }
    .siswa-pagination-links{
        display:flex;
        gap:6px;
        flex-wrap:wrap;
        align-items:center;
    }
    .siswa-page-link,
    .siswa-page-disabled,
    .siswa-page-active{
        min-width:34px;
        height:34px;
        padding:0 10px;
        border-radius:12px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:12px;
        font-weight:950;
        text-decoration:none;
        border:1px solid #dbe5ec;
    }
    .siswa-page-link{
        background:#ffffff;
        color:#0f766e!important;
    }
    .siswa-page-link:hover{
        background:#ccfbf1;
        border-color:#99f6e4;
    }
    .siswa-page-active{
        background:linear-gradient(135deg,#0f9f8f,#087c73);
        color:#ffffff;
        border-color:#0f9f8f;
    }
    .siswa-page-disabled{
        background:#eef2f7;
        color:#94a3b8;
        cursor:not-allowed;
    }
    .siswa-pagination svg{
        width:16px!important;
        height:16px!important;
        max-width:16px!important;
        max-height:16px!important;
    }
    @media(max-width:1100px){.siswa-grid{grid-template-columns:repeat(2,1fr)}.siswa-filter{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:650px){.siswa-grid,.siswa-filter{grid-template-columns:1fr}.siswa-btn,.siswa-control{width:100%;justify-content:center}}
</style>

@if(session('success'))<div class="siswa-alert siswa-alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="siswa-alert siswa-alert-error">⚠️ {{ session('error') }}</div>@endif
@if($errors->any())<div class="siswa-alert siswa-alert-error">⚠️ {{ $errors->first() }}</div>@endif

<div class="siswa-card">
    <div class="siswa-grid">
        <div class="siswa-stat"><span>Total Santri</span><strong>{{ number_format($totalSantri ?? 0, 0, ',', '.') }}</strong></div>
        <div class="siswa-stat"><span>Santri Aktif</span><strong>{{ number_format($totalAktif ?? 0, 0, ',', '.') }}</strong></div>
        <div class="siswa-stat"><span>Data Tampil</span><strong>{{ number_format(($siswas ?? collect())->total(), 0, ',', '.') }}</strong></div>
        <div class="siswa-stat"><span>Halaman</span><strong>{{ ($siswas ?? null)?->currentPage() ?? 1 }}</strong></div>
    </div>
</div>

<div class="siswa-card">
    <div class="siswa-toolbar">
        <div>
            <h3 class="siswa-title">Import & Export Data Santri</h3>
            <p class="siswa-muted">Download template Excel, isi data, lalu Save As menjadi CSV sebelum import. NIS dibuat otomatis oleh sistem.</p>
        </div>
        <div class="siswa-actions">
            <a href="{{ route('siswa.template-import') }}" class="siswa-btn siswa-btn-light">⬇ Template Excel</a>
            <a href="{{ route('siswa.export', request()->query()) }}" class="siswa-btn siswa-btn-primary">📤 Export CSV</a>
            <a href="{{ route('siswa.create') }}" class="siswa-btn siswa-btn-primary">+ Tambah Santri</a>
        </div>
    </div>

    <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="siswa-import-form" data-confirm="Import data santri dari file ini? NIS akan dibuat otomatis oleh sistem.">
        @csrf
        <input type="file" name="file_import" accept=".csv,.txt" required class="siswa-control" style="max-width:380px;">
        <button type="submit" class="siswa-btn siswa-btn-primary">📥 Import CSV</button>
    </form>
</div>

<div class="siswa-card">
    <form action="{{ route('siswa.index') }}" method="GET" class="siswa-filter">
        <div class="siswa-field">
            <label>Kata Kunci</label>
            <input type="text" name="search" value="{{ request('search') }}" class="siswa-control" placeholder="Nama, NIS, NISN, wali, nomor HP...">
        </div>
        <div class="siswa-field">
            <label>Kelas Formal</label>
            <select name="kelas_formal" class="siswa-control">
                <option value="">Semua</option>
                @foreach(($kelasFormal ?? collect()) as $kelas)
                    @php $nama = $namaKelas($kelas); @endphp
                    <option value="{{ $nama }}" {{ request('kelas_formal') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="siswa-field">
            <label>Kelas Diniyah</label>
            <select name="kelas_diniyah" class="siswa-control">
                <option value="">Semua</option>
                @foreach(($kelasDiniyah ?? collect()) as $kelas)
                    @php $nama = $namaKelas($kelas); @endphp
                    <option value="{{ $nama }}" {{ request('kelas_diniyah') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="siswa-field">
            <label>Status Mukim</label>
            <select name="status_mukim" class="siswa-control">
                <option value="">Semua</option>
                @foreach(['Mukim','Pulang Pergi','Asrama','Non Mukim'] as $status)
                    <option value="{{ $status }}" {{ request('status_mukim') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="siswa-field">
            <label>Status Aktif</label>
            <select name="status_aktif" class="siswa-control">
                <option value="">Semua</option>
                @foreach(['Aktif','Nonaktif','Alumni'] as $status)
                    <option value="{{ $status }}" {{ request('status_aktif') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="siswa-actions">
            <button type="submit" class="siswa-btn siswa-btn-primary">🔍 Cari</button>
            <a href="{{ route('siswa.index') }}" class="siswa-btn siswa-btn-light">Reset</a>
        </div>
    </form>
</div>

<div class="siswa-card">
    <div class="siswa-toolbar" style="margin-bottom:14px;">
        <div>
            <h3 class="siswa-title">Daftar Santri</h3>
            <p class="siswa-muted">NIS dikunci dan dibuat otomatis saat tambah/import santri.</p>
        </div>
    </div>

    <div class="siswa-table-wrap">
        <table class="siswa-table">
            <thead>
                <tr>
                    <th>No</th><th>NIS</th><th>Nama Santri</th><th>NISN</th><th>Kelas</th><th>Wali / Ibu</th><th>No HP</th><th>Status</th><th>Nominal Khusus</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($siswas ?? collect()) as $index => $siswa)
                    <tr>
                        <td>{{ ($siswas->firstItem() ?? 1) + $index }}</td>
                        <td><strong>{{ $siswa->nis ?? '-' }}</strong></td>
                        <td>
                            <div class="siswa-name">{{ $siswa->nama_siswa ?? '-' }}</div>
                            <small>{{ $siswa->jk ?? '-' }} · {{ $siswa->tempat_lahir ?? '-' }}{{ !empty($siswa->tgl_lahir) ? ', ' . \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d-m-Y') : '' }}</small>
                        </td>
                        <td>{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            <span class="siswa-badge">{{ $siswa->kelas_formal ?? '-' }}</span>
                            <span class="siswa-badge siswa-badge-pink">{{ $siswa->kelas_diniyah ?? '-' }}</span>
                        </td>
                        <td><strong>{{ $siswa->nama_wali ?? '-' }}</strong><br><small>Ibu: {{ $siswa->nama_ibu ?? '-' }}</small></td>
                        <td>{{ $siswa->no_hp ?? '-' }}</td>
                        <td>
                            <span class="siswa-badge">{{ $siswa->status_mukim ?? '-' }}</span>
                            <span class="siswa-badge siswa-badge-pink">{{ $siswa->status_aktif ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="siswa-nominal-list">
                                @if((int)($siswa->potongan_formal ?? 0) > 0)
                                    <span class="siswa-badge">
                                        Formal: Rp {{ number_format((int) $siswa->potongan_formal, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="siswa-nominal-default">Formal: Default</span>
                                @endif

                                @if((int)($siswa->potongan_diniyah ?? 0) > 0)
                                    <span class="siswa-badge siswa-badge-pink">
                                        Pondok: Rp {{ number_format((int) $siswa->potongan_diniyah, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="siswa-nominal-default">Pondok: Default</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="siswa-actions">
                                <a href="{{ route('siswa.edit', $siswa->id_siswa) }}" class="siswa-btn siswa-btn-light">✏️</a>
                                <form action="{{ route('siswa.destroy', $siswa->id_siswa) }}" method="POST" data-confirm="Hapus data santri {{ $siswa->nama_siswa ?? '' }}?" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="siswa-btn siswa-btn-danger">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="siswa-empty">Belum ada data santri sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists(($siswas ?? null), 'lastPage') && $siswas->lastPage() > 1)
        @php
            $paginator = $siswas->appends(request()->query());
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $startPage = max(1, $currentPage - 2);
            $endPage = min($lastPage, $currentPage + 2);
        @endphp

        <div class="siswa-pagination">
            <div class="siswa-pagination-info">
                Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }}
                dari {{ number_format($paginator->total(), 0, ',', '.') }} data
            </div>

            <div class="siswa-pagination-links">
                @if($paginator->onFirstPage())
                    <span class="siswa-page-disabled">‹</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="siswa-page-link">‹</a>
                @endif

                @if($startPage > 1)
                    <a href="{{ $paginator->url(1) }}" class="siswa-page-link">1</a>
                    @if($startPage > 2)
                        <span class="siswa-page-disabled">...</span>
                    @endif
                @endif

                @for($page = $startPage; $page <= $endPage; $page++)
                    @if($page == $currentPage)
                        <span class="siswa-page-active">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="siswa-page-link">{{ $page }}</a>
                    @endif
                @endfor

                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="siswa-page-disabled">...</span>
                    @endif
                    <a href="{{ $paginator->url($lastPage) }}" class="siswa-page-link">{{ $lastPage }}</a>
                @endif

                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="siswa-page-link">›</a>
                @else
                    <span class="siswa-page-disabled">›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
