@extends('layouts.app')

@section('title', 'Setoran Bebas - SIKEU MHI V2')
@section('page_title', 'Pembayaran Lain')
@section('page_subtitle', 'Kelola tagihan tetap dan setoran bebas seperti infaq, kitab satuan, donasi, dan pemasukan umum.')

@section('content')
@include('pembayaran-lain._tabs')

<style>
    .bebas-card{background:#fff;border:1px solid rgba(15,118,110,.14);border-radius:24px;padding:20px;box-shadow:0 18px 55px rgba(15,23,42,.08);margin-bottom:18px}
    .bebas-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
    .bebas-title{margin:0;color:#0f766e;font-size:22px;font-weight:950}
    .bebas-muted{margin:5px 0 0;color:#64748b;font-size:13px}
    .bebas-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    .bebas-btn{border:none;border-radius:14px;padding:10px 14px;font-weight:900;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap}
    .bebas-btn-primary{background:linear-gradient(135deg,#0f9f8f,#087c73);color:#fff!important;box-shadow:0 12px 25px rgba(15,118,110,.2)}
    .bebas-btn-light{background:#f1f5f9;color:#334155!important;border:1px solid #dbe5ec}
    .bebas-btn-danger{background:linear-gradient(135deg,#f43f5e,#be123c);color:#fff!important}
    .bebas-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
    .bebas-stat{background:linear-gradient(135deg,#fff,#f8fffd);border:1px solid #dbeafe;border-radius:20px;padding:16px}
    .bebas-stat span{color:#64748b;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em}
    .bebas-stat strong{display:block;margin-top:8px;color:#0f766e;font-size:26px;font-weight:950}
    .bebas-filter{display:grid;grid-template-columns:1.4fr 1fr 1fr auto;gap:10px;align-items:end}
    .bebas-field label{display:block;margin-bottom:6px;color:#475569;font-size:11px;font-weight:900;text-transform:uppercase}
    .bebas-control{width:100%;min-height:42px;border:1px solid #d5e1e8;border-radius:14px;padding:9px 12px;outline:none;font-weight:750;color:#0f172a;background:#fff}
    .bebas-table-wrap{overflow-x:auto;border:1px solid #dbe5ec;border-radius:20px}
    .bebas-table{width:100%;min-width:950px;border-collapse:collapse;background:#fff}
    .bebas-table th{background:#0f3d5c;color:#fff;padding:12px;font-size:11px;text-transform:uppercase;letter-spacing:.04em;text-align:left;white-space:nowrap}
    .bebas-table td{padding:12px;border-bottom:1px solid #eef2f7;vertical-align:middle;font-size:13px;color:#334155}
    .bebas-name{color:#0f172a;font-weight:950}
    .bebas-badge{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:950;text-transform:uppercase;background:#ccfbf1;color:#0f766e;margin:2px}
    .bebas-empty{text-align:center;padding:45px;color:#64748b;font-weight:850}
    .bebas-alert{border-radius:18px;padding:13px 16px;margin-bottom:14px;font-weight:800}
    .bebas-alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
    .bebas-alert-error{background:#ffe4e6;color:#be123c;border:1px solid #fecdd3}
    .bebas-pagination{margin-top:18px}
    .bebas-pagination svg{width:16px!important;height:16px!important}
    @media(max-width:900px){.bebas-grid,.bebas-filter{grid-template-columns:1fr}.bebas-btn,.bebas-control{width:100%;justify-content:center}}
</style>

@if(session('success'))<div class="bebas-alert bebas-alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="bebas-alert bebas-alert-error">⚠️ {{ session('error') }}</div>@endif
@if($errors->any())<div class="bebas-alert bebas-alert-error">⚠️ {{ $errors->first() }}</div>@endif

<div class="bebas-card">
    <div class="bebas-toolbar">
        <div>
            <h3 class="bebas-title">Setoran Bebas</h3>
            <p class="bebas-muted">Untuk infaq, kitab satuan, donasi, pembelian buku, dan pemasukan lain yang nominalnya bebas.</p>
        </div>

        <div class="bebas-actions">
            <a href="{{ route('pembayaran-lain.bebas.create') }}" class="bebas-btn bebas-btn-primary">+ Tambah Setoran</a>
        </div>
    </div>
</div>

<div class="bebas-card">
    <div class="bebas-grid">
        <div class="bebas-stat">
            <span>Total Transaksi</span>
            <strong>{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div class="bebas-stat">
            <span>Total Nominal</span>
            <strong>Rp {{ number_format($totalNominal ?? 0, 0, ',', '.') }}</strong>
        </div>
    </div>
</div>

<div class="bebas-card">
    <form action="{{ route('pembayaran-lain.bebas.index') }}" method="GET" class="bebas-filter">
        <div class="bebas-field">
            <label>Kata Kunci</label>
            <input type="text" name="search" value="{{ request('search') }}" class="bebas-control" placeholder="Nama penyetor, uraian, nominal...">
        </div>

        <div class="bebas-field">
            <label>Tanggal Awal</label>
            <input type="date" name="tgl_awal" value="{{ request('tgl_awal') }}" class="bebas-control">
        </div>

        <div class="bebas-field">
            <label>Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" value="{{ request('tgl_akhir') }}" class="bebas-control">
        </div>

        <div class="bebas-actions">
            <button type="submit" class="bebas-btn bebas-btn-primary">🔍 Cari</button>
            <a href="{{ route('pembayaran-lain.bebas.index') }}" class="bebas-btn bebas-btn-light">Reset</a>
        </div>
    </form>
</div>

<div class="bebas-card">
    <div class="bebas-table-wrap">
        <table class="bebas-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Penyetor</th>
                    <th>Uraian</th>
                    <th>Nominal</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($items ?? collect()) as $index => $item)
                    <tr>
                        <td>{{ ($items->firstItem() ?? 1) + $index }}</td>
                        <td>
                            <span class="bebas-badge">
                                {{ !empty($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="bebas-name">{{ $item->nama_penyetor ?? '-' }}</div>
                            <small>No Bukti: BS-{{ !empty($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Ym') : date('Ym') }}-{{ str_pad($item->id_masuk ?? 0, 4, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>{{ $item->uraian ?? '-' }}</td>
                        <td><strong>Rp {{ number_format((int)($item->nominal ?? 0), 0, ',', '.') }}</strong></td>
                        <td>{{ $item->nama_admin ?? $item->username_admin ?? '-' }}</td>
                        <td>
                            <div class="bebas-actions">
                                <a href="{{ route('pembayaran-lain.bebas.cetak', $item->id_masuk) }}" class="bebas-btn bebas-btn-primary" target="_blank">🖨</a>
                                <a href="{{ route('pembayaran-lain.bebas.edit', $item->id_masuk) }}" class="bebas-btn bebas-btn-light">✏️</a>
                                <form action="{{ route('pembayaran-lain.bebas.destroy', $item->id_masuk) }}" method="POST" data-confirm="Hapus setoran {{ $item->nama_penyetor ?? '' }}?" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bebas-btn bebas-btn-danger">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="bebas-empty">Belum ada data setoran bebas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists(($items ?? null), 'links'))
        <div class="bebas-pagination">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
