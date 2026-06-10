@extends('layouts.app')

@section('title', 'Riwayat Transaksi - SIKEU MHI V2')

@section('page_title', 'Riwayat Transaksi')

@section('page_subtitle', 'Monitoring semua transaksi pemasukan dan pengeluaran.')

@section('content')
<style>
    .trx-filter {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 160px 160px 190px auto;
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

    .trx-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 96px;
        height: 96px;
        border-radius: 999px;
        background: var(--tosca-soft);
        right: -32px;
        top: -34px;
    }

    .summary-card.pink::after {
        background: var(--pink-soft);
    }

    .summary-card span {
        display: block;
        font-size: 12px;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 900;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .summary-card strong {
        display: block;
        font-size: 24px;
        color: var(--tosca-dark);
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .summary-card.pink strong {
        color: var(--pink-dark);
    }

    .trx-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .trx-card::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .035;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .trx-content {
        position: relative;
        z-index: 1;
    }

    .trx-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 16px;
    }

    .trx-title {
        margin: 0;
        font-size: 20px;
        font-weight: 950;
        color: var(--tosca-dark);
    }

    .trx-table-wrap {
        overflow-x: auto;
        border: 1px solid #d7e1e7;
        border-radius: 18px;
    }

    .trx-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: rgba(255, 255, 255, .98);
    }

    .trx-table th,
    .trx-table td {
        border: 1px solid #d7e1e7;
        padding: 11px 10px;
        text-align: left;
        vertical-align: middle;
    }

    .trx-table th {
        background: #e7f9f6;
        color: var(--tosca-dark);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .trx-table td.number {
        text-align: right;
        white-space: nowrap;
        font-weight: 900;
    }

    .text-masuk {
        color: var(--tosca-dark);
    }

    .text-keluar {
        color: var(--pink-dark);
    }

    .trx-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        background: var(--tosca-soft);
        color: var(--tosca-dark);
        white-space: nowrap;
    }

    .trx-badge.pondok {
        background: var(--pink-soft);
        color: var(--pink-dark);
    }

    .trx-badge.lain {
        background: #fff7d6;
        color: #a16207;
    }

    .trx-badge.pengeluaran {
        background: #fee2e2;
        color: #b91c1c;
    }

    .trx-name {
        font-weight: 900;
        color: var(--text);
    }

    .trx-small {
        font-size: 12px;
        color: var(--muted);
        margin-top: 3px;
        line-height: 1.5;
    }

    .duplicate-note {
        display: inline-flex;
        margin-top: 6px;
        padding: 4px 8px;
        border-radius: 999px;
        background: #fff7d6;
        color: #a16207;
        font-size: 11px;
        font-weight: 900;
    }

    .action-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .icon-btn {
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        font-weight: 900;
    }

    .icon-print {
        background: linear-gradient(135deg, var(--tosca), #087c73);
        color: white;
    }

    .icon-delete {
        background: linear-gradient(135deg, #ef476f, #e11d48);
        color: white;
    }

    .check-box {
        width: 17px;
        height: 17px;
        accent-color: var(--tosca);
    }

    .empty-box {
        text-align: center;
        padding: 38px 16px;
        color: var(--muted);
    }

    .empty-box h3 {
        margin: 0 0 8px;
        color: var(--tosca-dark);
        font-size: 22px;
    }

    .warning-duplikat {
        background: #fff7d6;
        color: #92400e;
        border: 1px solid #fde68a;
        border-radius: 18px;
        padding: 13px 15px;
        margin-bottom: 18px;
        font-weight: 800;
        line-height: 1.5;
    }

    @media(max-width: 1200px) {

        .filter-grid,
        .trx-summary {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 800px) {

        .filter-grid,
        .trx-summary {
            grid-template-columns: 1fr;
        }

        .trx-top {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <strong>Periksa kembali:</strong>
    <ul style="margin:8px 0 0; padding-left:20px;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(($jumlahDuplikatTerdeteksi ?? 0) > 0)
<div class="warning-duplikat">
    ⚠️ Sistem menemukan {{ $jumlahDuplikatTerdeteksi }} transaksi dobel pada filter ini.
    Tampilan riwayat sudah disatukan supaya total tidak membengkak.
    Jika tombol hapus pada baris bertanda dobel dipakai, semua salinan transaksi dobel tersebut ikut dihapus.
</div>
@endif

<div class="trx-filter">
    <form action="{{ route('riwayat-transaksi.index') }}" method="GET" class="filter-grid">
        <div class="filter-group">
            <label>Cari Transaksi</label>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="filter-control"
                placeholder="Nama santri, NIS, tagihan, uraian...">
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
            <label>Jenis</label>
            <select name="jenis" class="filter-control">
                <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua</option>
                <option value="formal" {{ $jenis === 'formal' ? 'selected' : '' }}>Formal</option>
                <option value="pondok" {{ $jenis === 'pondok' ? 'selected' : '' }}>Diniyah</option>
                <option value="lain" {{ $jenis === 'lain' ? 'selected' : '' }}>Pembayaran Lain</option>
                <option value="pengeluaran" {{ $jenis === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>
    </form>
</div>

<div class="trx-summary">
    <div class="summary-card">
        <span>Total Transaksi</span>
        <strong>{{ number_format($transaksi->count(), 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card">
        <span>Total Pemasukan</span>
        <strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Total Pengeluaran</span>
        <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card {{ $saldoBersih < 0 ? 'pink' : '' }}">
        <span>Selisih</span>
        <strong>Rp {{ number_format($saldoBersih, 0, ',', '.') }}</strong>
    </div>
</div>

<div class="trx-card">
    <div class="trx-content">
        <form
            id="formHapusBanyak"
            action="{{ route('riwayat-transaksi.hapus-banyak') }}"
            method="POST">
            @csrf
            @method('DELETE')

            <div class="trx-top">
                <div>
                    <h3 class="trx-title">Data Riwayat Transaksi</h3>
                    <p style="margin:5px 0 0; color:var(--muted); font-size:13px;">
                        Pilih beberapa transaksi untuk hapus/batalkan sekaligus.
                    </p>
                </div>

                <button type="submit" class="btn btn-danger btn-submit-delete">
                    🗑 Hapus Terpilih
                </button>
            </div>

            <div class="trx-table-wrap">
                <table class="trx-table">
                    <thead>
                        <tr>
                            <th width="45">
                                <input type="checkbox" id="checkAll" class="check-box">
                            </th>
                            <th width="55">No</th>
                            <th>Tanggal</th>
                            <th>Nama / Sumber</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transaksi as $index => $item)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="kode_transaksi[]"
                                    value="{{ $item['kode'] }}"
                                    class="check-box row-check">
                            </td>

                            <td>{{ $index + 1 }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                            </td>

                            <td>
                                <div class="trx-name">{{ $item['nama'] }}</div>
                                <div class="trx-small">{{ $item['subjek'] }}</div>

                                @if(($item['duplikat_count'] ?? 1) > 1)
                                <span class="duplicate-note">
                                    Dobel x{{ $item['duplikat_count'] }} disatukan
                                </span>
                                @endif
                            </td>

                            <td>
                                <span class="trx-badge {{ $item['tipe'] }}">
                                    {{ $item['jenis'] }}
                                </span>
                            </td>

                            <td>{{ $item['keterangan'] }}</td>

                            <td class="number text-masuk">
                                @if($item['masuk'] > 0)
                                Rp {{ number_format($item['masuk'], 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>

                            <td class="number text-keluar">
                                @if($item['keluar'] > 0)
                                Rp {{ number_format($item['keluar'], 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>

                            <td>
                                <div class="action-group">
                                    @if(!empty($item['cetak_url']) && $item['cetak_url'] !== '#')
                                    <a
                                        href="{{ $item['cetak_url'] }}"
                                        target="_blank"
                                        class="icon-btn icon-print"
                                        title="Cetak">
                                        🖨
                                    </a>
                                    @endif

                                    <button
                                        type="button"
                                        class="icon-btn icon-delete btn-hapus-satuan"
                                        title="Hapus"
                                        data-kode="{{ $item['kode'] }}">
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-box">
                                    <h3>Belum ada transaksi</h3>
                                    <p>Tidak ditemukan transaksi pada filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    @if($transaksi->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="6" style="text-align:right;">Total</th>
                            <th style="text-align:right;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</th>
                            <th style="text-align:right;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </form>

        <form
            id="formHapusSatuan"
            action="{{ route('riwayat-transaksi.hapus-satuan') }}"
            method="POST"
            style="display:none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="kode" id="kodeHapusSatuan">
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const rowChecks = document.querySelectorAll('.row-check');
        const formHapusBanyak = document.getElementById('formHapusBanyak');
        const formHapusSatuan = document.getElementById('formHapusSatuan');
        const kodeHapusSatuan = document.getElementById('kodeHapusSatuan');

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowChecks.forEach(function(check) {
                    check.checked = checkAll.checked;
                });
            });
        }

        document.querySelectorAll('.btn-hapus-satuan').forEach(function(button) {
            button.addEventListener('click', function() {
                const kode = this.dataset.kode;

                if (!kode || !formHapusSatuan || !kodeHapusSatuan) {
                    alert('Kode transaksi tidak ditemukan.');
                    return;
                }

                const pesan = 'Hapus transaksi ini? Data akan mempengaruhi laporan, dashboard, tunggakan, dan status pembayaran.';
                if (!confirm(pesan)) {
                    return;
                }

                kodeHapusSatuan.value = kode;
                this.disabled = true;
                this.innerHTML = '⏳';
                formHapusSatuan.submit();
            });
        });

        if (formHapusBanyak) {
            formHapusBanyak.addEventListener('submit', function(e) {
                const checked = document.querySelectorAll('.row-check:checked');

                if (checked.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu transaksi yang ingin dihapus.');
                    return;
                }

                const pesan = 'PERINGATAN! ' + checked.length + ' transaksi yang dipilih akan dihapus permanen dan mempengaruhi dashboard, laporan, tunggakan, serta status pembayaran. Lanjutkan?';

                if (!confirm(pesan)) {
                    e.preventDefault();
                    return;
                }

                document.querySelectorAll('.btn-submit-delete').forEach(function(btn) {
                    btn.disabled = true;
                    btn.innerHTML = '⏳ Menghapus...';
                });
            });
        }
    });
</script>
@endsection