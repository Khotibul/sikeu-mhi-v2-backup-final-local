@extends('layouts.app')

@section('title', 'Pembayaran Santri - ' . $siswa->nama_siswa)

@section('page_title', 'Pembayaran Santri')

@section('page_subtitle', 'Bayar biaya pendidikan formal dan pondok/diniyah. Bisa pilih banyak bulan sekaligus.')

@section('content')
<style>
    .pay-hero {
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: white;
        border-radius: 30px;
        padding: 28px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .pay-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        right: -70px;
        top: -80px;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: 80px 1fr 260px;
        gap: 18px;
        align-items: center;
    }

    .hero-avatar {
        width: 80px;
        height: 80px;
        border-radius: 26px;
        background: rgba(255, 255, 255, .22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        font-weight: 950;
    }

    .hero-name {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .hero-meta {
        line-height: 1.7;
        opacity: .95;
        font-size: 13px;
    }

    .year-box {
        background: rgba(255, 255, 255, .18);
        border: 1px solid rgba(255, 255, 255, .30);
        border-radius: 22px;
        padding: 16px;
    }

    .year-box label {
        display: block;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .year-box select {
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: 12px;
        font-weight: 900;
        color: var(--tosca-dark);
        outline: none;
    }

    .summary-grid {
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
        right: -30px;
        top: -34px;
    }

    .summary-card.pink::after {
        background: var(--pink-soft);
    }

    .summary-card span {
        display: block;
        position: relative;
        z-index: 1;
        color: var(--muted);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .summary-card strong {
        display: block;
        position: relative;
        z-index: 1;
        color: var(--tosca-dark);
        font-size: 24px;
        font-weight: 950;
    }

    .summary-card.pink strong {
        color: var(--pink-dark);
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 20px;
    }

    .payment-panel,
    .history-panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .payment-panel::after,
    .history-panel::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .03;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .panel-content {
        position: relative;
        z-index: 1;
    }

    .panel-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .panel-title h3 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 20px;
        font-weight: 950;
    }

    .panel-title small {
        color: var(--muted);
        font-weight: 800;
    }

    .pay-toolbar {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .pay-date {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
    }

    .pay-date input {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 9px 10px;
        outline: none;
    }

    .month-list {
        display: grid;
        gap: 10px;
    }

    .month-row {
        border: 1px solid #d7e1e7;
        border-radius: 18px;
        padding: 12px;
        display: grid;
        grid-template-columns: 34px 1fr 150px 86px;
        gap: 12px;
        align-items: center;
        background: #fbfffe;
        border-left: 5px solid var(--pink);
    }

    .month-row.lunas {
        border-left-color: var(--tosca);
        background: #f7fffd;
    }

    .month-row.cicil {
        border-left-color: #f59e0b;
        background: #fffdf5;
    }

    .check-bulan {
        width: 18px;
        height: 18px;
        accent-color: var(--tosca);
    }

    .month-name {
        color: var(--text);
        font-weight: 950;
        margin-bottom: 5px;
    }

    .month-detail {
        color: var(--muted);
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
        font-size: 12px;
        line-height: 1.5;
    }

    .pill {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .pill-belum {
        background: var(--pink-soft);
        color: var(--pink-dark);
    }

    .pill-cicil {
        background: #fff7d6;
        color: #a16207;
    }

    .pill-lunas {
        background: var(--tosca-soft);
        color: var(--tosca-dark);
    }

    .amount-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 13px;
        padding: 11px 12px;
        text-align: right;
        font-weight: 950;
        color: var(--tosca-dark);
        outline: none;
        background: white;
    }

    .amount-input:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
    }

    .pay-submit {
        margin-top: 16px;
        width: 100%;
        border: none;
        border-radius: 18px;
        padding: 14px;
        background: linear-gradient(135deg, var(--tosca), #087c73);
        color: white;
        font-size: 14px;
        font-weight: 950;
        cursor: pointer;
        box-shadow: 0 14px 24px rgba(15, 118, 110, .16);
    }

    .history-filter {
        background: #fbfffe;
        border: 1px solid #d7e1e7;
        border-radius: 20px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .history-filter-grid {
        display: grid;
        grid-template-columns: 1fr 180px 180px auto;
        gap: 10px;
        align-items: end;
    }

    .history-filter label {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .history-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .history-table-wrap {
        overflow-x: auto;
        border: 1px solid #d7e1e7;
        border-radius: 18px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1050px;
        background: rgba(255, 255, 255, .98);
        font-size: 14px;
    }

    .history-table th,
    .history-table td {
        border: 1px solid #d7e1e7;
        padding: 11px 10px;
        text-align: left;
        vertical-align: middle;
    }

    .history-table th {
        background: #e7f9f6;
        color: var(--tosca-dark);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .history-table td.number {
        text-align: right;
        color: var(--tosca-dark);
        font-weight: 950;
        white-space: nowrap;
    }

    .kind-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 950;
        background: var(--tosca-soft);
        color: var(--tosca-dark);
    }

    .kind-badge.pondok {
        background: var(--pink-soft);
        color: var(--pink-dark);
    }


    .status-pay-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-pay-badge.lunas {
        background: #ccfbf1;
        color: #0f766e;
    }

    .status-pay-badge.cicilan {
        background: #fef3c7;
        color: #b45309;
    }


    .action-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 13px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 15px;
        cursor: pointer;
        font-weight: 950;
    }

    .icon-print {
        background: linear-gradient(135deg, var(--tosca), #087c73);
        color: white;
    }

    .icon-delete {
        background: linear-gradient(135deg, #ef476f, #e11d48);
        color: white;
    }

    .check-history {
        width: 17px;
        height: 17px;
        accent-color: var(--tosca);
    }

    .empty-box {
        text-align: center;
        padding: 34px;
        color: var(--muted);
    }

    .empty-box h3 {
        color: var(--tosca-dark);
        margin: 0 0 8px;
    }

    .icon-wa {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #ffffff !important;
    }

    .icon-wa-disabled {
        background: #e2e8f0;
        color: #94a3b8 !important;
        cursor: not-allowed;
    }


    .pay-single {
        border: none;
        border-radius: 14px;
        padding: 11px 12px;
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: #ffffff;
        font-size: 12px;
        font-weight: 950;
        cursor: pointer;
        box-shadow: 0 10px 18px rgba(15, 118, 110, .14);
        white-space: nowrap;
    }

    .pay-single:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }

    .pay-gabungan-box {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 26px;
        padding: 18px 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .pay-gabungan-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .pay-gabungan-info {
        color: var(--muted);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.6;
    }

    .pay-gabungan-info strong {
        color: var(--tosca-dark);
        font-weight: 950;
    }

    .pay-gabungan-date {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 950;
        color: var(--muted);
        text-transform: uppercase;
    }

    .pay-gabungan-date input {
        border: 1px solid var(--border);
        border-radius: 13px;
        padding: 10px 12px;
        outline: none;
        font-weight: 900;
        color: var(--text);
    }

    .pay-submit-gabungan {
        border: none;
        border-radius: 18px;
        padding: 13px 18px;
        background: linear-gradient(135deg, var(--tosca), #087c73);
        color: white;
        font-size: 14px;
        font-weight: 950;
        cursor: pointer;
        box-shadow: 0 14px 24px rgba(15, 118, 110, .16);
        min-width: 230px;
    }

    .pay-submit-gabungan:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }

    @media(max-width: 1200px) {

        .hero-content,
        .summary-grid,
        .payment-grid,
        .history-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 700px) {
        .month-row {
            grid-template-columns: 34px 1fr;
        }

        .amount-input {
            grid-column: 2;
        }

        .pay-single {
            grid-column: 2;
            width: 100%;
        }
    }

    /* === UI pembayaran model lama + improve === */
    .legacy-hint {
        font-size: 12px;
        color: var(--muted);
        font-weight: 750;
        line-height: 1.5;
    }

    .month-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .month-row.legacy-pay-row {
        display: grid;
        grid-template-columns: 34px minmax(170px, 1fr) 145px 190px;
        gap: 10px;
        align-items: center;
        padding: 13px 14px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #dbeafe;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .05);
    }

    .month-row.legacy-pay-row.belum {
        border-left: 5px solid #ec4899;
        background: linear-gradient(90deg, #fff7fb, #ffffff);
    }

    .month-row.legacy-pay-row.cicilan,
    .month-row.legacy-pay-row.cicil {
        border-left: 5px solid #f59e0b;
        background: linear-gradient(90deg, #fffaf0, #ffffff);
    }

    .month-row.legacy-pay-row.lunas {
        border-left: 5px solid #0f766e;
        background: linear-gradient(90deg, #f0fdfa, #ffffff);
    }

    .month-check-cell {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .month-info-main {
        min-width: 0;
    }

    .month-name {
        font-size: 14px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 5px;
    }

    .month-detail {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        font-size: 11px;
        color: var(--muted);
        line-height: 1.4;
    }

    .month-money {
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 11px;
        color: #64748b;
        line-height: 1.3;
    }

    .month-money strong {
        color: #0f172a;
        font-size: 13px;
        font-weight: 950;
    }

    .month-action-cell {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
        flex-wrap: wrap;
    }

    .month-action-cell .amount-input {
        width: 105px;
        min-height: 36px;
        text-align: right;
        font-size: 12px;
        font-weight: 900;
        border-radius: 12px;
    }

    .legacy-btn {
        border: none;
        border-radius: 12px;
        padding: 9px 11px;
        min-height: 36px;
        font-size: 11px;
        font-weight: 950;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        white-space: nowrap;
        transition: .18s ease;
    }

    .legacy-btn-bayar {
        background: linear-gradient(135deg, #0f9f8f, #087c73);
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(15, 118, 110, .22);
    }

    .legacy-btn-cetak {
        background: #e0f2fe;
        color: #0369a1 !important;
    }

    .legacy-btn-wa {
        background: #dcfce7;
        color: #15803d !important;
    }

    .legacy-btn-wa.disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .legacy-btn-hapus {
        background: #ffe4e6;
        color: #be123c !important;
    }

    .legacy-btn:disabled,
    .legacy-btn[disabled] {
        opacity: .45;
        cursor: not-allowed;
        box-shadow: none;
    }

    .legacy-paid-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: wrap;
    }

    .legacy-pay-extra {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pay-submit-gabungan {
        min-width: 190px;
    }

    .pay-gabungan-box {
        position: sticky;
        top: 10px;
        z-index: 5;
    }

    @media(max-width: 1100px) {
        .month-row.legacy-pay-row {
            grid-template-columns: 34px 1fr;
        }

        .month-money,
        .month-action-cell {
            grid-column: 2;
            align-items: flex-start;
            justify-content: flex-start;
        }
    }

    @media(max-width: 700px) {
        .month-row.legacy-pay-row {
            padding: 12px;
        }

        .month-action-cell .amount-input,
        .legacy-btn {
            width: 100%;
        }

        .legacy-paid-actions,
        .legacy-pay-extra {
            width: 100%;
        }
    }
</style>

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

@php
$totalTagihanFormal = collect($bulanFormal ?? [])->sum('tagihan');
$totalTerbayarFormal = collect($bulanFormal ?? [])->sum('terbayar');
$totalSisaFormal = collect($bulanFormal ?? [])->sum('sisa');

$kelasDiniyahRaw = trim((string) ($siswa->kelas_diniyah ?? ''));
$kelasDiniyahNormal = strtolower($kelasDiniyahRaw);

/*
Santri dianggap TIDAK DINIYAH kalau kelas diniyah kosong / tanda "-" / variasi "tidak".
Kalau kondisi ini benar, panel/tabel Pondok/Diniyah disembunyikan total.
*/
$punyaKelasDiniyah = !in_array($kelasDiniyahNormal, [
'',
'-',
'--',
'0',
'null',
'tidak',
'tidak diniyah',
'non diniyah',
'tanpa diniyah',
'tidak ada',
'none',
], true);

$totalTagihanPondokAwal = collect($bulanPondok ?? [])->sum('tagihan');
$totalTerbayarPondokAwal = collect($bulanPondok ?? [])->sum('terbayar');
$totalSisaPondokAwal = collect($bulanPondok ?? [])->sum('sisa');

$bolehBayarPondok = ($tampilPondok ?? false)
&& $punyaKelasDiniyah
&& (int) ($nominalPondok ?? 0) > 0
&& (int) $totalTagihanPondokAwal > 0;

$totalTagihanPondok = $bolehBayarPondok ? $totalTagihanPondokAwal : 0;
$totalTerbayarPondok = $bolehBayarPondok ? $totalTerbayarPondokAwal : 0;
$totalSisaPondok = $bolehBayarPondok ? $totalSisaPondokAwal : 0;

$totalTagihan = $totalTagihanFormal + $totalTagihanPondok;
$totalTerbayar = $totalTerbayarFormal + $totalTerbayarPondok;
$totalSisa = $totalSisaFormal + $totalSisaPondok;
@endphp

@php
$formatNoWa = function ($nomor) {
$nomor = preg_replace('/[^0-9]/', '', (string) $nomor);

if ($nomor === '') {
return null;
}

if (substr($nomor, 0, 1) === '0') {
$nomor = '62' . substr($nomor, 1);
}

if (substr($nomor, 0, 2) !== '62') {
$nomor = '62' . $nomor;
}

return strlen($nomor) >= 10 ? $nomor : null;
};

$buatLinkWaKwitansi = function ($nomor, $namaSiswa, $jenis, $bulan, $tahun, $nominal, $urlKwitansi) use (
$formatNoWa,
) {
$nomorWa = $formatNoWa($nomor);

if (!$nomorWa) {
return null;
}

$pesan =
"Assalamu'alaikum Wr. Wb.\n\n" .
"Yth. Wali Santri dari {$namaSiswa},\n" .
"Berikut kami kirimkan bukti pembayaran:\n\n" .
"Jenis: {$jenis}\n" .
"Bulan: {$bulan} {$tahun}\n" .
'Nominal: Rp ' .
number_format((int) $nominal, 0, ',', '.') .
"\n\n" .
"Link kwitansi:\n{$urlKwitansi}\n\n" .
"Terima kasih.\n" .
"Bendahara YPP Mamba'ul Khoiriyatil Islamiyah";

return 'https://wa.me/' . $nomorWa . '?text=' . rawurlencode($pesan);
};

$keyBulanTahun = function ($bulan, $tahun) {
return strtolower(trim((string) $bulan)) . '|' . trim((string) $tahun);
};

$normalStatusBayar = function ($status, $keterangan = '') {
$status = strtoupper(trim((string) $status));
$ket = strtolower((string) $keterangan);

if ($status === '') {
if (str_contains($ket, 'cicil') || str_contains($ket, 'angsuran') || str_contains($ket, 'nyicil')) {
return 'CICILAN';
}

return 'LUNAS';
}

return in_array($status, ['CICIL', 'CICILAN', 'NYICIL', 'ANGSURAN'], true) ? 'CICILAN' : 'LUNAS';
};

$riwayatFormalBulanMap = collect($riwayatFormal ?? [])
->sortByDesc(function ($item) {
return strtotime((string) ($item->tgl_bayar ?? '')) ?: 0;
})
->groupBy(function ($item) use ($keyBulanTahun) {
return $keyBulanTahun($item->bulan_bayar ?? '', $item->tahun_bayar ?? '');
})
->map(function ($items) {
return $items->first();
});

$riwayatPondokBulanMap = collect($riwayatPondok ?? [])
->sortByDesc(function ($item) {
return strtotime((string) ($item->tgl_bayar ?? '')) ?: 0;
})
->groupBy(function ($item) use ($keyBulanTahun) {
return $keyBulanTahun($item->bulan_bayar ?? '', $item->tahun_bayar ?? '');
})
->map(function ($items) {
return $items->first();
});
@endphp

<a href="{{ route('pembayaran-spp.index') }}" class="btn btn-light" style="margin-bottom:16px;">
    ← Kembali Cari Santri
</a>

<div class="pay-hero">
    <div class="hero-content">
        <div class="hero-avatar">
            {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
        </div>

        <div>
            <h2 class="hero-name">{{ $siswa->nama_siswa }}</h2>
            <div class="hero-meta">
                NIS: {{ $siswa->nis ?: '-' }} |
                NISN: {{ $siswa->nisn ?: '-' }}<br>
                Kelas Formal: {{ $siswa->kelas_formal ?: '-' }} |
                Kelas Diniyah: {{ $siswa->kelas_diniyah ?: '-' }} |
                Status: {{ $siswa->status_mukim ?: '-' }}
            </div>
        </div>

        <div class="year-box">
            <form action="{{ route('pembayaran-spp.siswa', $siswa->id_siswa) }}" method="GET">
                <label>Tahun Ajaran Tagihan</label>
                <select name="tahun_ajaran" onchange="this.form.submit()">
                    @foreach ($tahunAjaranList as $item)
                    <option value="{{ $item }}" {{ $tahunAjaran == $item ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <span>Total Tagihan</span>
        <strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card">
        <span>Total Terbayar</span>
        <strong>Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Total Sisa</span>
        <strong>Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong>
    </div>

    <div class="summary-card pink">
        <span>Nominal Bulanan</span>
        <strong style="font-size:17px; line-height:1.6;">
            Formal: Rp {{ number_format($nominalFormal, 0, ',', '.') }}<br>
            Pondok:
            @if($bolehBayarPondok)
            Rp {{ number_format($nominalPondok, 0, ',', '.') }}
            @else
            Tidak Diniyah
            @endif
        </strong>
    </div>
</div>

<form id="formBayarGabungan"
    action="{{ route('pembayaran-spp.siswa.bayar-gabungan', $siswa->id_siswa) }}"
    method="POST"
    data-confirm="Simpan pembayaran yang dipilih?">
    @csrf

    <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

    <div class="pay-gabungan-box">
        <div class="pay-gabungan-left">
            <label class="pay-gabungan-date">
                Tanggal Bayar
                <input type="date" name="tgl_bayar" value="{{ date('Y-m-d') }}" required>
            </label>

            <div class="pay-gabungan-info" id="infoBayarGabungan">
                Belum ada bulan dipilih untuk bayar gabungan.
            </div>
        </div>

        <button type="submit" class="pay-submit-gabungan" id="btnBayarGabungan" disabled>
            Bayar Gabungan
        </button>
    </div>

    <div class="payment-grid">
        @if ($tampilFormal)
        <div class="payment-panel">
            <div class="panel-content">
                <div class="panel-title">
                    <div>
                        <h3>🏫 Biaya Pendidikan Formal</h3>
                        <small>{{ $tahunAjaran }}</small>
                    </div>
                </div>

                <div class="pay-toolbar">
                    <div class="pay-date">
                        Tombol <strong>Bayar</strong> per bulan seperti web lama. Centang untuk bayar gabungan.
                    </div>

                    <button type="button" class="btn btn-light js-check-all" data-target="formal">
                        Centang Semua Belum Lunas
                    </button>
                </div>

                <div class="month-list">
                    @foreach ($bulanFormal as $bulan)
                    @php
                    $nominalSisaFormal = (int) ($bulan['sisa'] ?? 0);
                    $nominalTagihanFormal = (int) ($bulan['tagihan'] ?? 0);
                    $nominalTerbayarFormal = (int) ($bulan['terbayar'] ?? 0);
                    $bulanKeyFormal = $keyBulanTahun($bulan['bulan'] ?? '', $bulan['tahun_bayar'] ?? '');
                    $riwayatBulanFormal = $riwayatFormalBulanMap->get($bulanKeyFormal);

                    $adaPembayaranFormal = $riwayatBulanFormal || $nominalTerbayarFormal > 0;
                    $isLunas = ($nominalSisaFormal <= 0 && $nominalTerbayarFormal > 0) || ($nominalTagihanFormal <= 0);

                        if ($isLunas) {
                        $status='LUNAS' ;
                        } elseif ($adaPembayaranFormal) {
                        $status='CICILAN' ;
                        } else {
                        $status='BELUM' ;
                        }

                        $rowClass=strtolower($status);
                        $key='formal_' . $loop->iteration;
                        $bolehBayarSisa = !$isLunas && $nominalSisaFormal > 0 && $nominalTagihanFormal > 0;

                        $nominalCetakFormal = $riwayatBulanFormal
                        ? (($riwayatBulanFormal->terbayar ?? 0) > 0 ? $riwayatBulanFormal->terbayar : ($riwayatBulanFormal->jumlah_bayar ?? 0))
                        : $nominalTerbayarFormal;

                        $linkWaFormalBulan = null;

                        if ($riwayatBulanFormal) {
                        $linkWaFormalBulan = $buatLinkWaKwitansi(
                        $siswa->no_hp ?? null,
                        $siswa->nama_siswa ?? '-',
                        'Pembayaran SPP Formal',
                        $riwayatBulanFormal->bulan_bayar ?? ($bulan['bulan'] ?? '-'),
                        $riwayatBulanFormal->tahun_bayar ?? ($bulan['tahun_bayar'] ?? '-'),
                        $nominalCetakFormal,
                        route('pembayaran-spp.kwitansi-formal', $riwayatBulanFormal->id_bayar),
                        );
                        }
                        @endphp

                        <div class="month-row legacy-pay-row {{ $rowClass }}">
                            <div class="month-check-cell">
                                <input type="checkbox"
                                    name="items[{{ $key }}][checked]"
                                    value="1"
                                    class="check-bulan check-formal js-bayar-check"
                                    data-nominal="{{ $nominalSisaFormal }}"
                                    {{ !$bolehBayarSisa ? 'disabled' : '' }}>
                            </div>

                            <div class="month-info-main">
                                <div class="month-name">
                                    {{ $bulan['bulan'] }} {{ $bulan['tahun_bayar'] }}
                                </div>

                                <div class="month-detail">
                                    <span class="pill pill-{{ strtolower($status) }}">{{ $status }}</span>
                                    <span>Formal</span>
                                    @if($adaPembayaranFormal && !$isLunas)
                                    <span>Masih bisa dibayar sisa</span>
                                    @endif
                                </div>
                            </div>

                            <div class="month-money">
                                <span>Tagihan: <strong>Rp {{ number_format($nominalTagihanFormal, 0, ',', '.') }}</strong></span>
                                <span>Terbayar: Rp {{ number_format($nominalTerbayarFormal, 0, ',', '.') }}</span>
                                <span>Sisa: Rp {{ number_format($nominalSisaFormal, 0, ',', '.') }}</span>
                            </div>

                            <div class="month-action-cell">
                                <input type="hidden" name="items[{{ $key }}][jenis]" value="formal">
                                <input type="hidden" name="items[{{ $key }}][bulan]" value="{{ $bulan['bulan'] }}">
                                <input type="hidden" name="items[{{ $key }}][tahun]" value="{{ $bulan['tahun_bayar'] }}">
                                <input type="hidden" name="items[{{ $key }}][tagihan]" value="{{ $nominalTagihanFormal }}">
                                <input type="hidden" name="items[{{ $key }}][keterangan]"
                                    value="Pembayaran SPP Formal {{ $bulan['bulan'] }} {{ $bulan['tahun_bayar'] }}">

                                @if ($riwayatBulanFormal)
                                <div class="legacy-paid-actions">
                                    <a href="{{ route('pembayaran-spp.kwitansi-formal', $riwayatBulanFormal->id_bayar) }}"
                                        target="_blank" class="legacy-btn legacy-btn-cetak" title="Cetak kwitansi">
                                        🖨 Cetak
                                    </a>

                                    @if ($linkWaFormalBulan)
                                    <a href="{{ $linkWaFormalBulan }}" target="_blank"
                                        class="legacy-btn legacy-btn-wa" title="Kirim WhatsApp">
                                        🟢 WA
                                    </a>
                                    @else
                                    <span class="legacy-btn legacy-btn-wa disabled"
                                        title="Nomor WhatsApp belum terisi">
                                        🟢 WA
                                    </span>
                                    @endif

                                    <button type="submit" form="hapusFormal{{ $riwayatBulanFormal->id_bayar }}"
                                        class="legacy-btn legacy-btn-hapus" title="Hapus pembayaran">
                                        🗑 Hapus
                                    </button>
                                </div>
                                @endif

                                @if ($bolehBayarSisa)
                                <div class="legacy-pay-extra">
                                    <input type="number"
                                        name="items[{{ $key }}][nominal]"
                                        value="{{ $nominalSisaFormal }}"
                                        min="1"
                                        max="{{ $nominalSisaFormal }}"
                                        class="amount-input js-bayar-nominal"
                                        data-key="{{ $key }}">

                                    <button type="submit"
                                        class="legacy-btn legacy-btn-bayar"
                                        name="bayar_satuan"
                                        value="{{ $key }}">
                                        {{ $adaPembayaranFormal ? 'Bayar Sisa' : 'Bayar' }}
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                </div>
            </div>
        </div>
        @endif

        @if ($bolehBayarPondok)
        <div class="payment-panel">
            <div class="panel-content">
                <div class="panel-title">
                    <div>
                        <h3>🕌 Biaya Pendidikan Pondok/Diniyah</h3>
                        <small>{{ $tahunAjaran }}</small>
                    </div>
                </div>

                <div class="pay-toolbar">
                    <div class="pay-date">
                        Tombol <strong>Bayar</strong> per bulan seperti web lama. Centang untuk bayar gabungan.
                    </div>

                    <button type="button" class="btn btn-light js-check-all" data-target="pondok">
                        Centang Semua Belum Lunas
                    </button>
                </div>

                <div class="month-list">
                    @foreach ($bulanPondok as $bulan)
                    @php
                    $nominalSisaPondok = (int) ($bulan['sisa'] ?? 0);
                    $nominalTagihanPondok = (int) ($bulan['tagihan'] ?? 0);
                    $nominalTerbayarPondok = (int) ($bulan['terbayar'] ?? 0);
                    $bulanKeyPondok = $keyBulanTahun($bulan['bulan'] ?? '', $bulan['tahun_bayar'] ?? '');
                    $riwayatBulanPondok = $riwayatPondokBulanMap->get($bulanKeyPondok);

                    $adaPembayaranPondok = $riwayatBulanPondok || $nominalTerbayarPondok > 0;
                    $isLunas = ($nominalSisaPondok <= 0 && $nominalTerbayarPondok > 0) || ($nominalTagihanPondok <= 0);

                        if ($isLunas) {
                        $status='LUNAS' ;
                        } elseif ($adaPembayaranPondok) {
                        $status='CICILAN' ;
                        } else {
                        $status='BELUM' ;
                        }

                        $rowClass=strtolower($status);
                        $key='pondok_' . $loop->iteration;
                        $bolehBayarSisa = !$isLunas && $nominalSisaPondok > 0 && $nominalTagihanPondok > 0;

                        $nominalCetakPondok = $riwayatBulanPondok
                        ? (($riwayatBulanPondok->terbayar ?? 0) > 0 ? $riwayatBulanPondok->terbayar : ($riwayatBulanPondok->jumlah_bayar ?? 0))
                        : $nominalTerbayarPondok;

                        $linkWaPondokBulan = null;

                        if ($riwayatBulanPondok) {
                        $linkWaPondokBulan = $buatLinkWaKwitansi(
                        $siswa->no_hp ?? null,
                        $siswa->nama_siswa ?? '-',
                        'Pembayaran Pondok/Diniyah',
                        $riwayatBulanPondok->bulan_bayar ?? ($bulan['bulan'] ?? '-'),
                        $riwayatBulanPondok->tahun_bayar ?? ($bulan['tahun_bayar'] ?? '-'),
                        $nominalCetakPondok,
                        route('pembayaran-spp.kwitansi-pondok', $riwayatBulanPondok->id_bayar_diniyah),
                        );
                        }
                        @endphp

                        <div class="month-row legacy-pay-row {{ $rowClass }}">
                            <div class="month-check-cell">
                                <input type="checkbox"
                                    name="items[{{ $key }}][checked]"
                                    value="1"
                                    class="check-bulan check-pondok js-bayar-check"
                                    data-nominal="{{ $nominalSisaPondok }}"
                                    {{ !$bolehBayarSisa ? 'disabled' : '' }}>
                            </div>

                            <div class="month-info-main">
                                <div class="month-name">
                                    {{ $bulan['bulan'] }} {{ $bulan['tahun_bayar'] }}
                                </div>

                                <div class="month-detail">
                                    <span class="pill pill-{{ strtolower($status) }}">{{ $status }}</span>
                                    <span>Pondok/Diniyah</span>
                                    @if($adaPembayaranPondok && !$isLunas)
                                    <span>Masih bisa dibayar sisa</span>
                                    @endif
                                </div>
                            </div>

                            <div class="month-money">
                                <span>Tagihan: <strong>Rp {{ number_format($nominalTagihanPondok, 0, ',', '.') }}</strong></span>
                                <span>Terbayar: Rp {{ number_format($nominalTerbayarPondok, 0, ',', '.') }}</span>
                                <span>Sisa: Rp {{ number_format($nominalSisaPondok, 0, ',', '.') }}</span>
                            </div>

                            <div class="month-action-cell">
                                <input type="hidden" name="items[{{ $key }}][jenis]" value="pondok">
                                <input type="hidden" name="items[{{ $key }}][bulan]" value="{{ $bulan['bulan'] }}">
                                <input type="hidden" name="items[{{ $key }}][tahun]" value="{{ $bulan['tahun_bayar'] }}">
                                <input type="hidden" name="items[{{ $key }}][tagihan]" value="{{ $nominalTagihanPondok }}">
                                <input type="hidden" name="items[{{ $key }}][keterangan]"
                                    value="Pembayaran Pondok/Diniyah {{ $bulan['bulan'] }} {{ $bulan['tahun_bayar'] }}">

                                @if ($riwayatBulanPondok)
                                <div class="legacy-paid-actions">
                                    <a href="{{ route('pembayaran-spp.kwitansi-pondok', $riwayatBulanPondok->id_bayar_diniyah) }}"
                                        target="_blank" class="legacy-btn legacy-btn-cetak" title="Cetak kwitansi">
                                        🖨 Cetak
                                    </a>

                                    @if ($linkWaPondokBulan)
                                    <a href="{{ $linkWaPondokBulan }}" target="_blank"
                                        class="legacy-btn legacy-btn-wa" title="Kirim WhatsApp">
                                        🟢 WA
                                    </a>
                                    @else
                                    <span class="legacy-btn legacy-btn-wa disabled"
                                        title="Nomor WhatsApp belum terisi">
                                        🟢 WA
                                    </span>
                                    @endif

                                    <button type="submit" form="hapusPondok{{ $riwayatBulanPondok->id_bayar_diniyah }}"
                                        class="legacy-btn legacy-btn-hapus" title="Hapus pembayaran">
                                        🗑 Hapus
                                    </button>
                                </div>
                                @endif

                                @if ($bolehBayarSisa)
                                <div class="legacy-pay-extra">
                                    <input type="number"
                                        name="items[{{ $key }}][nominal]"
                                        value="{{ $nominalSisaPondok }}"
                                        min="1"
                                        max="{{ $nominalSisaPondok }}"
                                        class="amount-input js-bayar-nominal"
                                        data-key="{{ $key }}">

                                    <button type="submit"
                                        class="legacy-btn legacy-btn-bayar"
                                        name="bayar_satuan"
                                        value="{{ $key }}">
                                        {{ $adaPembayaranPondok ? 'Bayar Sisa' : 'Bayar' }}
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</form>

<div class="history-panel">
    <div class="panel-content">
        <form action="{{ route('pembayaran-spp.siswa', $siswa->id_siswa) }}" method="GET" class="history-filter">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

            <div class="history-filter-grid">
                <div>
                    <label>Cari Riwayat</label>
                    <input type="text" name="search_riwayat" value="{{ $searchRiwayat }}" class="form-control"
                        placeholder="Bulan, nominal, keterangan...">
                </div>

                <div>
                    <label>Tahun Riwayat</label>
                    <select name="tahun_riwayat" class="form-control">
                        <option value="semua" {{ $tahunRiwayat === 'semua' ? 'selected' : '' }}>
                            Semua Tahun
                        </option>

                        @foreach ($tahunAjaranRiwayatList as $item)
                        <option value="{{ $item }}" {{ $tahunRiwayat === $item ? 'selected' : '' }}>
                            {{ $item }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Jenis Riwayat</label>
                    <select name="jenis_riwayat" class="form-control">
                        <option value="semua" {{ $jenisRiwayat === 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="formal" {{ $jenisRiwayat === 'formal' ? 'selected' : '' }}>Formal</option>
                        <option value="pondok" {{ $jenisRiwayat === 'pondok' ? 'selected' : '' }}>Pondok</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    🔎 Tampilkan
                </button>
            </div>
        </form>

        @php
        /*
        Tombol cetak gabungan PER TANGGAL.
        Formal + Pondok/Diniyah yang dibayar di tanggal yang sama akan dikelompokkan jadi 1 kwitansi.
        */
        $tanggalGabunganFormal = collect($riwayatFormal ?? [])->map(function ($item) {
        $nominal = (int) (($item->terbayar ?? 0) > 0 ? $item->terbayar : ($item->jumlah_bayar ?? 0));

        return [
        'tanggal' => !empty($item->tgl_bayar) ? \Carbon\Carbon::parse($item->tgl_bayar)->toDateString() : null,
        'nominal' => $nominal,
        ];
        });

        $tanggalGabunganPondok = collect($riwayatPondok ?? [])->map(function ($item) {
        $nominal = (int) (($item->terbayar ?? 0) > 0 ? $item->terbayar : ($item->jumlah_bayar ?? 0));

        return [
        'tanggal' => !empty($item->tgl_bayar) ? \Carbon\Carbon::parse($item->tgl_bayar)->toDateString() : null,
        'nominal' => $nominal,
        ];
        });

        $tanggalGabungan = $tanggalGabunganFormal
        ->merge($tanggalGabunganPondok)
        ->filter(fn ($item) => !empty($item['tanggal']))
        ->groupBy('tanggal')
        ->map(function ($group, $tanggal) {
        return [
        'tanggal' => $tanggal,
        'jumlah' => $group->count(),
        'total' => $group->sum('nominal'),
        ];
        })
        ->sortByDesc('tanggal')
        ->values();
        @endphp

        @if($tanggalGabungan->count() > 0)
        <div style="background:#fff;border:1px solid #dbe5ec;border-radius:22px;padding:18px;margin-bottom:18px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <div>
                    <h3 style="margin:0;color:var(--tosca-dark);font-size:20px;font-weight:950;">
                        Cetak Gabungan per Tanggal
                    </h3>
                    <p style="margin:5px 0 0;color:var(--muted);font-size:13px;font-weight:700;">
                        Pilih tanggal pembayaran. Kwitansi akan berisi formal dan pondok/diniyah pada tanggal itu saja.
                    </p>
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;">
                @foreach($tanggalGabungan as $group)
                <a href="{{ route('pembayaran-spp.siswa.cetak-gabungan-tanggal', [
                                    'id' => $siswa->id_siswa,
                                    'tanggal' => $group['tanggal'],
                                ]) }}"
                    target="_blank"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 13px;border-radius:14px;background:#f8fffd;border:1px solid #99f6e4;color:#0f766e;font-weight:900;text-decoration:none;font-size:12px;">
                    🖨 {{ \Carbon\Carbon::parse($group['tanggal'])->format('d-m-Y') }}
                    <span style="background:#ccfbf1;border-radius:999px;padding:3px 8px;">
                        {{ $group['jumlah'] }} item
                    </span>
                    <span>
                        Rp {{ number_format($group['total'], 0, ',', '.') }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- FORM CETAK GABUNGAN KHUSUS UNTUK CHECKBOX SAJA --}}
        <form id="formCetakGabungan" action="{{ route('pembayaran-spp.kwitansi-gabungan') }}" method="POST"
            target="_blank">
            @csrf

            <div class="history-top">
                <div>
                    <h3 style="margin:0; color:var(--tosca-dark); font-size:21px; font-weight:950;">
                        Riwayat Pembayaran
                    </h3>
                    <p style="margin:5px 0 0; color:var(--muted);">
                        Centang beberapa transaksi untuk cetak kwitansi gabungan.
                    </p>
                </div>

                <button type="submit" class="btn btn-primary">
                    🖨 Cetak Gabungan
                </button>
            </div>

            <div class="history-table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th width="45">Pilih</th>
                            <th width="55">No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Bulan</th>
                            <th>Tahun Ajaran</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th width="130">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = 1; @endphp

                        @foreach ($riwayatFormal as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="formal_ids[]" value="{{ $item->id_bayar }}"
                                    class="check-history">
                            </td>

                            <td>{{ $no++ }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y') }}
                            </td>

                            <td>
                                <span class="kind-badge">Formal</span>
                            </td>

                            <td>
                                {{ $item->bulan_bayar }} {{ $item->tahun_bayar }}
                            </td>

                            <td>
                                {{ $item->tahun_ajaran ?: '-' }}
                            </td>

                            <td class="number">
                                Rp
                                {{ number_format(($item->terbayar ?? 0) > 0 ? $item->terbayar : $item->jumlah_bayar, 0, ',', '.') }}
                            </td>

                            <td>
                                @php
                                $statusRiwayat = strtoupper(trim($item->status_bayar ?? ''));
                                $ketRiwayat = strtolower((string) ($item->keterangan ?? ''));

                                if ($statusRiwayat === '') {
                                if (str_contains($ketRiwayat, 'cicil') || str_contains($ketRiwayat, 'angsuran') || str_contains($ketRiwayat, 'nyicil')) {
                                $statusRiwayat = 'CICILAN';
                                } else {
                                $statusRiwayat = 'LUNAS';
                                }
                                }

                                $statusRiwayat = in_array($statusRiwayat, ['CICIL', 'CICILAN', 'NYICIL', 'ANGSURAN'], true)
                                ? 'CICILAN'
                                : 'LUNAS';
                                @endphp

                                <span class="status-pay-badge {{ $statusRiwayat === 'LUNAS' ? 'lunas' : 'cicilan' }}">
                                    {{ $statusRiwayat }}
                                </span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('pembayaran-spp.kwitansi-formal', $item->id_bayar) }}"
                                        target="_blank" class="icon-btn icon-print" title="Cetak">
                                        🖨
                                    </a>
                                    @php
                                    $nominalFormalWa =
                                    ($item->terbayar ?? 0) > 0 ? $item->terbayar : $item->jumlah_bayar;

                                    $linkWaFormal = $buatLinkWaKwitansi(
                                    $siswa->no_hp ?? null,
                                    $siswa->nama_siswa ?? '-',
                                    'Pembayaran SPP Formal',
                                    $item->bulan_bayar ?? '-',
                                    $item->tahun_bayar ?? '-',
                                    $nominalFormalWa,
                                    route('pembayaran-spp.kwitansi-formal', $item->id_bayar),
                                    );
                                    @endphp

                                    @if ($linkWaFormal)
                                    <a href="{{ $linkWaFormal }}" target="_blank" class="icon-btn icon-wa"
                                        title="Kirim Kwitansi ke WhatsApp">
                                        🟢
                                    </a>
                                    @else
                                    <span class="icon-btn icon-wa-disabled"
                                        title="Nomor WhatsApp belum terisi">
                                        🟢
                                    </span>
                                    @endif

                                    <button type="submit" form="hapusFormal{{ $item->id_bayar }}"
                                        class="icon-btn icon-delete" title="Hapus">
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @foreach ($riwayatPondok as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="pondok_ids[]" value="{{ $item->id_bayar_diniyah }}"
                                    class="check-history">
                            </td>

                            <td>{{ $no++ }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y') }}
                            </td>

                            <td>
                                <span class="kind-badge pondok">Pondok</span>
                            </td>

                            <td>
                                {{ $item->bulan_bayar }} {{ $item->tahun_bayar }}
                            </td>

                            <td>
                                {{ $item->tahun_ajaran ?: '-' }}
                            </td>

                            <td class="number">
                                Rp
                                {{ number_format(($item->terbayar ?? 0) > 0 ? $item->terbayar : $item->jumlah_bayar, 0, ',', '.') }}
                            </td>

                            <td>
                                @php
                                $ketRiwayat = strtolower((string) ($item->keterangan ?? ''));

                                if (str_contains($ketRiwayat, 'cicil') || str_contains($ketRiwayat, 'angsuran') || str_contains($ketRiwayat, 'nyicil')) {
                                $statusRiwayat = 'CICILAN';
                                } else {
                                $statusRiwayat = 'LUNAS';
                                }
                                @endphp

                                <span class="status-pay-badge {{ $statusRiwayat === 'LUNAS' ? 'lunas' : 'cicilan' }}">
                                    {{ $statusRiwayat }}
                                </span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('pembayaran-spp.kwitansi-pondok', $item->id_bayar_diniyah) }}"
                                        target="_blank" class="icon-btn icon-print" title="Cetak">
                                        🖨
                                    </a>
                                    @php
                                    $nominalPondokWa =
                                    ($item->terbayar ?? 0) > 0 ? $item->terbayar : $item->jumlah_bayar;

                                    $linkWaPondok = $buatLinkWaKwitansi(
                                    $siswa->no_hp ?? null,
                                    $siswa->nama_siswa ?? '-',
                                    'Pembayaran Pondok/Diniyah',
                                    $item->bulan_bayar ?? '-',
                                    $item->tahun_bayar ?? '-',
                                    $nominalPondokWa,
                                    route('pembayaran-spp.kwitansi-pondok', $item->id_bayar_diniyah),
                                    );
                                    @endphp

                                    @if ($linkWaPondok)
                                    <a href="{{ $linkWaPondok }}" target="_blank" class="icon-btn icon-wa"
                                        title="Kirim Kwitansi ke WhatsApp">
                                        🟢
                                    </a>
                                    @else
                                    <span class="icon-btn icon-wa-disabled"
                                        title="Nomor WhatsApp belum terisi">
                                        🟢
                                    </span>
                                    @endif

                                    <button type="submit" form="hapusPondok{{ $item->id_bayar_diniyah }}"
                                        class="icon-btn icon-delete" title="Hapus">
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if ($riwayatFormal->count() === 0 && $riwayatPondok->count() === 0)
                        <tr>
                            <td colspan="9">
                                <div class="empty-box">
                                    <h3>Belum ada riwayat pembayaran</h3>
                                    <p>Riwayat akan muncul setelah pembayaran disimpan atau filter riwayat diubah.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </form>

        {{-- FORM HAPUS DILETAKKAN DI LUAR FORM CETAK GABUNGAN --}}
        @foreach ($riwayatFormal as $item)
        <form id="hapusFormal{{ $item->id_bayar }}"
            action="{{ route('pembayaran-spp.hapus-formal', $item->id_bayar) }}" method="POST"
            data-confirm="Hapus riwayat pembayaran formal ini?" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endforeach

        @foreach ($riwayatPondok as $item)
        <form id="hapusPondok{{ $item->id_bayar_diniyah }}"
            action="{{ route('pembayaran-spp.hapus-pondok', $item->id_bayar_diniyah) }}" method="POST"
            data-confirm="Hapus riwayat pembayaran pondok/diniyah ini?" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endforeach
    </div>
</div>

<script>
    function formatRupiahPembayaran(angka) {
        return new Intl.NumberFormat('id-ID').format(parseInt(angka || 0));
    }

    function updateBayarGabungan() {
        const checks = document.querySelectorAll('.js-bayar-check:not(:disabled)');
        const btnGabungan = document.getElementById('btnBayarGabungan');
        const infoGabungan = document.getElementById('infoBayarGabungan');

        let totalItem = 0;
        let totalNominal = 0;

        checks.forEach(function(check) {
            if (!check.checked) {
                return;
            }

            const row = check.closest('.month-row');
            const nominalInput = row ? row.querySelector('.js-bayar-nominal') : null;
            const nominal = nominalInput ? parseInt(nominalInput.value || 0) : parseInt(check.dataset.nominal || 0);

            totalItem++;
            totalNominal += nominal;
        });

        if (btnGabungan && infoGabungan) {
            if (totalItem > 0) {
                btnGabungan.disabled = false;
                btnGabungan.textContent = 'Bayar ' + totalItem + ' Bulan Terpilih';
                infoGabungan.innerHTML = 'Terpilih <strong>' + totalItem + '</strong> item. Total pembayaran <strong>Rp ' + formatRupiahPembayaran(totalNominal) + '</strong>.';
            } else {
                btnGabungan.disabled = true;
                btnGabungan.textContent = 'Bayar Gabungan';
                infoGabungan.textContent = 'Belum ada bulan dipilih untuk bayar gabungan.';
            }
        }
    }

    document.querySelectorAll('.js-check-all').forEach(function(button) {
        button.addEventListener('click', function() {
            const target = this.dataset.target;
            const checks = document.querySelectorAll('.check-' + target + ':not(:disabled)');

            const semuaSudahDicentang = Array.from(checks).every(function(check) {
                return check.checked;
            });

            checks.forEach(function(check) {
                check.checked = !semuaSudahDicentang;
            });

            this.textContent = semuaSudahDicentang ?
                'Centang Semua Belum Lunas' :
                'Batalkan Pilihan';

            updateBayarGabungan();
        });
    });

    document.querySelectorAll('.js-bayar-check').forEach(function(check) {
        check.addEventListener('change', updateBayarGabungan);
    });

    document.querySelectorAll('.js-bayar-nominal').forEach(function(input) {
        input.addEventListener('input', updateBayarGabungan);
    });

    updateBayarGabungan();
</script>
<script>
    const formCetakGabungan = document.getElementById('formCetakGabungan');

    if (formCetakGabungan) {
        formCetakGabungan.addEventListener('submit', function(event) {
            const checked = formCetakGabungan.querySelectorAll('input[type="checkbox"]:checked');

            if (checked.length === 0) {
                event.preventDefault();

                if (window.mhiConfirm) {
                    window.mhiConfirm({
                        title: 'Belum Ada Transaksi Dipilih',
                        message: 'Centang minimal satu riwayat pembayaran untuk mencetak kwitansi gabungan.',
                        icon: '🖨',
                        okText: 'Mengerti',
                        cancelText: 'Tutup'
                    });
                } else {
                    alert('Centang minimal satu riwayat pembayaran.');
                }
            }
        });
    }
</script>
@endsection