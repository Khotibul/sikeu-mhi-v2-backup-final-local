@extends('layouts.app')

@section('title', 'Pembayaran SPP - SIKEU MHI V2')

@section('page_title', 'Pembayaran SPP')

@section('page_subtitle', 'Cari santri terlebih dahulu, lalu pilih santri untuk melakukan pembayaran biaya pendidikan.')

@section('content')
<style>
    .spp-hero {
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: white;
        border-radius: 30px;
        padding: 30px;
        margin-bottom: 22px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .spp-hero::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        right: -70px;
        top: -85px;
    }

    .spp-hero h2 {
        margin: 0 0 10px;
        font-size: 32px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .spp-hero p {
        margin: 0;
        line-height: 1.8;
        max-width: 940px;
        position: relative;
        z-index: 1;
        opacity: .96;
        font-weight: 700;
    }

    .search-panel,
    .result-panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .search-panel::after,
    .result-panel::after {
        content: "";
        position: absolute;
        width: 290px;
        height: 290px;
        background: url("{{ asset('images/logo-mhi.png') }}") center/contain no-repeat;
        opacity: .028;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .panel-content {
        position: relative;
        z-index: 1;
    }

    .search-title {
        margin: 0 0 16px;
        color: var(--tosca-dark);
        font-size: 21px;
        font-weight: 950;
    }

    .search-form {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 12px;
        align-items: end;
    }

    .search-group {
        display: grid;
        gap: 8px;
    }

    .search-group label {
        font-size: 12px;
        color: var(--muted);
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .search-control {
        width: 100%;
        height: 48px;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 0 16px;
        outline: none;
        font-size: 14px;
        background: white;
        color: var(--text);
        font-weight: 800;
    }

    .search-control::placeholder {
        color: #94a3b8;
        font-weight: 700;
    }

    .search-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18,169,154,.10);
    }

    .summary-mini {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .mini-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .mini-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 999px;
        background: var(--tosca-soft);
        right: -30px;
        top: -32px;
    }

    .mini-card.pink::after {
        background: var(--pink-soft);
    }

    .mini-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 7px;
        position: relative;
        z-index: 1;
    }

    .mini-card strong {
        display: block;
        color: var(--tosca-dark);
        font-size: 26px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .mini-card.pink strong {
        color: var(--pink-dark);
    }

    .result-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .result-title h3 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 21px;
        font-weight: 950;
    }

    .result-title p {
        margin: 5px 0 0;
        color: var(--muted);
        line-height: 1.6;
    }

    .student-grid {
        display: grid;
        gap: 12px;
    }

    .student-item {
        border: 1px solid #d7e1e7;
        border-radius: 20px;
        padding: 15px;
        background: rgba(255,255,255,.94);
        display: grid;
        grid-template-columns: 58px 1fr auto;
        gap: 14px;
        align-items: center;
        transition: .18s ease;
    }

    .student-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px rgba(15,23,42,.08);
        border-color: rgba(18,169,154,.35);
    }

    .student-avatar {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        font-weight: 950;
        color: var(--tosca-dark);
    }

    .student-name {
        font-weight: 950;
        color: var(--text);
        text-transform: uppercase;
        margin-bottom: 5px;
        font-size: 15px;
    }

    .student-meta {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .badge-line {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .badge-tosca {
        background: var(--tosca-soft);
        color: var(--tosca-dark);
    }

    .badge-pink {
        background: var(--pink-soft);
        color: var(--pink-dark);
    }

    .empty-state {
        text-align: center;
        padding: 44px 18px;
        color: var(--muted);
    }

    .empty-icon {
        width: 82px;
        height: 82px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--tosca-soft), var(--pink-soft));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 16px;
    }

    .empty-state h3 {
        color: var(--tosca-dark);
        margin: 0 0 8px;
        font-size: 22px;
        font-weight: 950;
    }

    .empty-state p {
        margin: 0;
        line-height: 1.7;
    }

    @media(max-width: 900px) {
        .search-form,
        .summary-mini,
        .student-item {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="spp-hero">
    <h2>Pembayaran SPP Santri</h2>
    <p>
        Cari santri berdasarkan nama, NIS, NISN, wali, ibu, kelas formal, kelas diniyah, atau status mukim.
        Setelah memilih santri, sistem akan menampilkan tagihan bulanan yang sudah sinkron dengan riwayat pembayaran lama dan baru.
    </p>
</div>

<div class="search-panel">
    <div class="panel-content">
        <h3 class="search-title">Cari Santri</h3>

        <form action="{{ route('pembayaran-spp.index') }}" method="GET" class="search-form">
            <div class="search-group">
                <label>Kata Kunci</label>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="search-control"
                    placeholder="Ketik nama santri, NIS, NISN, wali, ibu, kelas formal, atau kelas diniyah..."
                    autofocus
                >
            </div>

            <button type="submit" class="btn btn-primary">
                🔎 Cari
            </button>

            <a href="{{ route('pembayaran-spp.index') }}" class="btn btn-light">
                Reset
            </a>
        </form>
    </div>
</div>

@if(!empty($search))
    <div class="summary-mini">
        <div class="mini-card">
            <span>Kata Kunci</span>
            <strong style="font-size:20px;">{{ $search }}</strong>
        </div>

        <div class="mini-card">
            <span>Hasil Ditemukan</span>
            <strong>{{ number_format($hasilSiswa->count(), 0, ',', '.') }}</strong>
        </div>

        <div class="mini-card pink">
            <span>Mode</span>
            <strong style="font-size:20px;">Pembayaran</strong>
        </div>
    </div>

    <div class="result-panel">
        <div class="panel-content">
            <div class="result-title">
                <div>
                    <h3>Hasil Pencarian</h3>
                    <p>Pilih santri yang akan melakukan pembayaran.</p>
                </div>
            </div>

            <div class="student-grid">
                @forelse($hasilSiswa as $siswa)
                    <div class="student-item">
                        <div class="student-avatar">
                            {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                        </div>

                        <div>
                            <div class="student-name">
                                {{ $siswa->nama_siswa }}
                            </div>

                            <div class="student-meta">
                                NIS: {{ $siswa->nis ?: '-' }} |
                                NISN: {{ $siswa->nisn ?: '-' }}<br>
                                Wali: {{ $siswa->nama_wali ?: '-' }} |
                                Ibu: {{ $siswa->nama_ibu ?: '-' }}
                            </div>

                            <div class="badge-line">
                                <span class="badge badge-tosca">
                                    Formal: {{ $siswa->kelas_formal ?: '-' }}
                                </span>

                                <span class="badge badge-pink">
                                    Diniyah: {{ $siswa->kelas_diniyah ?: '-' }}
                                </span>

                                <span class="badge badge-tosca">
                                    {{ $siswa->status_mukim ?: '-' }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('pembayaran-spp.siswa', $siswa->id_siswa) }}" class="btn btn-primary">
                            Pilih Santri
                        </a>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">🔎</div>
                        <h3>Santri tidak ditemukan</h3>
                        <p>
                            Coba gunakan kata kunci lain, misalnya nama pendek, NIS, kelas, nama wali, atau nama ibu.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@else
    <div class="result-panel">
        <div class="panel-content">
            <div class="empty-state">
                <div class="empty-icon">💳</div>
                <h3>Silakan cari santri terlebih dahulu</h3>
                <p>
                    Setelah santri dipilih, halaman pembayaran akan menampilkan tagihan formal dan pondok/diniyah
                    sesuai status santri.
                </p>
            </div>
        </div>
    </div>
@endif
@endsection