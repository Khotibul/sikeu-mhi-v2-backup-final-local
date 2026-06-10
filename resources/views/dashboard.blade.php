@extends('layouts.app')

@section('title', 'Dashboard - SIKEU MHI V2')

@section('page_title', 'Dashboard Admin')

@section('page_subtitle', "Yayasan Pendidikan Pesantren Mamba'ul Khoiriyatil Islamiyah")

@section('content')
    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            border-radius: 28px;
            padding: 34px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .13);
            right: -55px;
            top: -75px;
        }

        .dashboard-hero h2 {
            margin: 0 0 10px;
            font-size: 32px;
            font-weight: 950;
            position: relative;
            z-index: 1;
        }

        .dashboard-hero p {
            margin: 0;
            max-width: 850px;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat-card,
        .finance-card,
        .panel-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 22px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 999px;
            background: var(--tosca-soft);
            right: -28px;
            top: -30px;
        }

        .stat-card.pink::after {
            background: var(--pink-soft);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 950;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .stat-value {
            font-size: 34px;
            font-weight: 950;
            color: var(--tosca-dark);
            position: relative;
            z-index: 1;
        }

        .stat-card.pink .stat-value {
            color: var(--pink-dark);
        }

        .stat-note {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            position: relative;
            z-index: 1;
        }

        .finance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }

        .finance-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 9px;
        }

        .finance-total {
            color: var(--tosca-dark);
            font-size: 38px;
            font-weight: 950;
            margin-bottom: 14px;
        }

        .income-breakdown {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .income-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 13px;
            border-radius: 16px;
            background: #fbfffe;
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 850;
        }

        .income-row span {
            color: var(--muted);
        }

        .income-row strong {
            color: var(--tosca-dark);
            white-space: nowrap;
        }

        .today-box {
            margin-top: 16px;
            padding: 17px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--pink-soft), #ffffff);
            border: 1px solid rgba(227, 69, 109, .18);
        }

        .today-box span {
            display: block;
            font-size: 12px;
            color: var(--muted);
            font-weight: 900;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .today-box strong {
            color: var(--pink-dark);
            font-size: 28px;
            font-weight: 950;
        }

        .pie-wrap {
            display: grid;
            grid-template-columns: 190px 1fr;
            gap: 22px;
            align-items: center;
        }

        .pie-chart {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: conic-gradient({{ $diagram['gradient'] }});
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .08);
        }

        .pie-chart::after {
            content: "";
            position: absolute;
            inset: 38px;
            border-radius: 50%;
            background: white;
            box-shadow: inset 0 0 0 1px var(--border);
        }

        .pie-center {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .pie-center strong {
            display: block;
            font-size: 16px;
            color: var(--tosca-dark);
            font-weight: 950;
        }

        .pie-center span {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-top: 3px;
        }

        .legend-list {
            display: grid;
            gap: 12px;
        }

        .legend-item {
            display: grid;
            grid-template-columns: 14px 1fr auto;
            gap: 10px;
            align-items: center;
            font-size: 13px;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
        }

        .legend-name {
            color: var(--text);
            font-weight: 850;
        }

        .legend-value {
            text-align: right;
            color: var(--muted);
            font-weight: 800;
        }

        .dashboard-bottom {
            display: grid;
            grid-template-columns: 1.3fr .7fr;
            gap: 18px;
        }

        .panel-title {
            margin: 0 0 18px;
            color: var(--tosca-dark);
            font-size: 18px;
            font-weight: 950;
        }

        .transaction-list {
            display: grid;
            gap: 12px;
        }

        .transaction-item {
            display: grid;
            grid-template-columns: 46px 1fr auto;
            gap: 13px;
            align-items: center;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #fbfffe;
        }

        .trx-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: var(--tosca-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .transaction-item h4 {
            margin: 0 0 4px;
            color: var(--text);
            font-size: 14px;
        }

        .transaction-item p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .trx-amount {
            text-align: right;
        }

        .trx-amount strong {
            color: var(--tosca-dark);
            font-size: 15px;
            font-weight: 950;
            white-space: nowrap;
        }

        .trx-badge {
            display: inline-flex;
            margin-top: 5px;
            padding: 4px 8px;
            border-radius: 999px;
            background: var(--pink-soft);
            color: var(--pink-dark);
            font-size: 10px;
            font-weight: 900;
        }

        .quick-menu {
            display: grid;
            gap: 12px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 18px;
            text-decoration: none;
            color: var(--text);
            background: #fbfffe;
            transition: .2s ease;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
        }

        .quick-link span {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--tosca-soft);
            font-size: 18px;
        }

        .quick-link strong {
            display: block;
            font-size: 14px;
            color: var(--tosca-dark);
        }

        .quick-link small {
            display: block;
            color: var(--muted);
            margin-top: 2px;
        }

        @media(max-width: 1200px) {

            .stats-grid,
            .finance-grid,
            .dashboard-bottom {
                grid-template-columns: 1fr;
            }

            .pie-wrap {
                grid-template-columns: 1fr;
                justify-items: center;
            }
        }

        @media(max-width: 700px) {
            .transaction-item {
                grid-template-columns: 46px 1fr;
            }

            .trx-amount {
                grid-column: 2;
                text-align: left;
            }
        }
    </style>

    <section class="dashboard-hero">
        <h2>Assalamu’alaikum, {{ session('admin_nama') ?? 'Admin' }}</h2>
        <p>
            Selamat datang di SIKEU MHI V2. Dashboard ini menampilkan ringkasan data santri,
            pemasukan bulan ini, pemasukan hari ini, serta transaksi terbaru dari pembayaran formal,
            pondok/diniyah, dan pembayaran lain santri.
        </p>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Santri</div>
            <div class="stat-value">{{ number_format($totalSantri, 0, ',', '.') }}</div>
            <div class="stat-note">Seluruh data santri</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Santri Aktif</div>
            <div class="stat-value">{{ number_format($santriAktif, 0, ',', '.') }}</div>
            <div class="stat-note">Status aktif / belum diset</div>
        </div>

        <div class="stat-card pink">
            <div class="stat-label">Kelas Formal</div>
            <div class="stat-value">{{ number_format($totalKelasFormal, 0, ',', '.') }}</div>
            <div class="stat-note">Data kelas formal</div>
        </div>

        <div class="stat-card pink">
            <div class="stat-label">Kelas Diniyah</div>
            <div class="stat-value">{{ number_format($totalKelasDiniyah, 0, ',', '.') }}</div>
            <div class="stat-note">Data kelas pondok/diniyah</div>
        </div>
    </section>

    <section class="finance-grid">
        <div class="finance-card">
            <div class="finance-label">Total Pemasukan Bulan Ini</div>

            <div class="finance-total">
                Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
            </div>

            <div class="income-breakdown">
                <div class="income-row">
                    <span>Formal</span>
                    <strong>Rp {{ number_format($pemasukanFormalBulanIni, 0, ',', '.') }}</strong>
                </div>

                <div class="income-row">
                    <span>Pondok/Diniyah</span>
                    <strong>Rp {{ number_format($pemasukanPondokBulanIni, 0, ',', '.') }}</strong>
                </div>

                <div class="income-row">
                    <span>Pembayaran Lain</span>
                    <strong>Rp {{ number_format($pemasukanLainSantriBulanIni, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="today-box">
                <span>Pemasukan Hari Ini</span>
                <strong>Rp {{ number_format($totalPemasukanHariIni, 0, ',', '.') }}</strong>

                <div style="margin-top:10px; font-size:12px; color:var(--muted); line-height:1.6;">
                    Formal: Rp {{ number_format($pemasukanFormalHariIni, 0, ',', '.') }} |
                    Pondok: Rp {{ number_format($pemasukanPondokHariIni, 0, ',', '.') }} |
                    Lain: Rp {{ number_format($pemasukanLainSantriHariIni, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="finance-card">
            <div class="finance-label">Komposisi Pemasukan Bulan Ini</div>

            <div class="pie-wrap">
                <div class="pie-chart">
                    <div class="pie-center">
                        <div>
                            <strong>{{ count($diagram['items']) }}</strong>
                            <span>Sumber</span>
                        </div>
                    </div>
                </div>

                <div class="legend-list">
                    @foreach ($diagram['items'] as $item)
                        <div class="legend-item">
                            <div class="legend-dot" style="background: {{ $item['color'] ?? '#e5e7eb' }}"></div>

                            <div class="legend-name">
                                {{ $item['label'] }}
                                <div style="font-size:11px; color:var(--muted); margin-top:2px;">
                                    {{ $item['persen'] }}%
                                </div>
                            </div>

                            <div class="legend-value">
                                Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-bottom">
        <div class="panel-card">
            <h3 class="panel-title">Riwayat Transaksi Terakhir</h3>

            <div class="transaction-list">
                @forelse($transaksiTerakhir as $trx)
                    <div class="transaction-item">
                        <div class="trx-icon">
                            @if ($trx['badge'] === 'Formal')
                                🏫
                            @elseif($trx['badge'] === 'Pondok')
                                🕌
                            @else
                                🧾
                            @endif
                        </div>

                        <div>
                            <h4>{{ $trx['nama'] }}</h4>
                            <p>
                                {{ $trx['jenis'] }}
                                @if ($trx['periode'] && $trx['periode'] !== '-')
                                    • {{ $trx['periode'] }}
                                @endif
                                <br>
                                {{ \Carbon\Carbon::parse($trx['tanggal'])->format('d-m-Y') }}
                            </p>
                        </div>

                        <div class="trx-amount">
                            <strong>Rp {{ number_format($trx['nominal'], 0, ',', '.') }}</strong>
                            <br>
                            <span class="trx-badge">{{ $trx['badge'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="transaction-item">
                        <div class="trx-icon">📭</div>
                        <div>
                            <h4>Belum ada transaksi</h4>
                            <p>Transaksi pembayaran akan muncul di sini.</p>
                        </div>
                        <div class="trx-amount">
                            <strong>Rp 0</strong>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="panel-card">
            <h3 class="panel-title">Menu Cepat</h3>

            <div class="quick-menu">
                <a href="{{ route('pembayaran-spp.index') }}" class="quick-link">
                    <span>💳</span>
                    <div>
                        <strong>Pembayaran SPP</strong>
                        <small>Formal dan pondok/diniyah</small>
                    </div>
                </a>

                <a href="{{ route('pembayaran-lain.index') }}" class="quick-link">
                    <span>🧾</span>
                    <div>
                        <strong>Pembayaran Lain</strong>
                        <small>Daftar ulang, UKT, seragam</small>
                    </div>
                </a>

                <a href="{{ route('laporan-pemasukan.index') }}" class="quick-link">
                    <span>📈</span>
                    <div>
                        <strong>Laporan Pemasukan</strong>
                        <small>Rekap pemasukan global</small>
                    </div>
                </a>

                <a href="{{ route('tunggakan.index') }}" class="quick-link">
                    <span>🔎</span>
                    <div>
                        <strong>Cek Tunggakan</strong>
                        <small>Lihat laporan tunggakan</small>
                    </div>
                </a>

                <a href="{{ route('pengeluaran.index') }}" class="quick-link">
                    <span>📤</span>
                    <div>
                        <strong>Pengeluaran</strong>
                        <small>Catatan kas keluar</small>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
