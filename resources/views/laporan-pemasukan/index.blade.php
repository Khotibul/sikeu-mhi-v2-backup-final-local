@extends('layouts.app')

@section('title', 'Laporan Pemasukan - SIKEU MHI V2')

@section('page_title', 'Laporan Pemasukan')

@section('page_subtitle', 'Rekap nominal global, transaksi per kelas, dan rincian pembayaran.')

@section('content')
<style>
    .laporan-hero {
        background: linear-gradient(135deg, var(--tosca), var(--pink));
        color: white;
        border-radius: 30px;
        padding: 28px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .laporan-hero::after {
        content: "";
        position: absolute;
        width: 210px;
        height: 210px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        right: -65px;
        top: -75px;
    }

    .laporan-hero h2 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .laporan-hero p {
        margin: 0;
        line-height: 1.7;
        max-width: 900px;
        font-weight: 700;
        opacity: .95;
        position: relative;
        z-index: 1;
    }

    .filter-card,
    .summary-card,
    .report-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 26px;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .filter-card {
        padding: 18px;
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1.5fr auto auto;
        gap: 12px;
        align-items: end;
    }

    .field-group {
        display: grid;
        gap: 7px;
    }

    .field-group label {
        color: var(--muted);
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .field-control {
        width: 100%;
        height: 44px;
        border: 1px solid var(--border);
        border-radius: 15px;
        padding: 0 12px;
        font-weight: 800;
        outline: none;
        color: var(--text);
        background: white;
    }

    .field-control:focus {
        border-color: var(--tosca);
        box-shadow: 0 0 0 4px rgba(18,169,154,.10);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-card {
        padding: 18px;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        right: -30px;
        top: -35px;
        border-radius: 999px;
        background: var(--tosca-soft);
    }

    .summary-card.pink::after {
        background: var(--pink-soft);
    }

    .summary-card span {
        display: block;
        color: var(--muted);
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        margin-bottom: 7px;
        position: relative;
        z-index: 1;
    }

    .summary-card strong {
        display: block;
        color: var(--tosca-dark);
        font-size: 22px;
        font-weight: 950;
        position: relative;
        z-index: 1;
    }

    .summary-card.pink strong {
        color: var(--pink-dark);
    }

    .report-card {
        padding: 22px;
        margin-bottom: 18px;
    }

    .report-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .report-title h3 {
        margin: 0;
        color: var(--tosca-dark);
        font-size: 22px;
        font-weight: 950;
    }

    .report-title p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    .table-wrap {
        overflow-x: auto;
        border: 1px solid #d7e1e7;
        border-radius: 18px;
    }

    .report-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        background: white;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #d7e1e7;
        padding: 10px 11px;
        vertical-align: middle;
        font-size: 13px;
    }

    .report-table th {
        background: #0f2f4a;
        color: white;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .report-table tfoot th {
        background: #e7f9f6;
        color: var(--tosca-dark);
        font-size: 13px;
    }

    .nominal-big {
        font-size: 18px;
        font-weight: 950;
        color: var(--tosca-dark);
        white-space: nowrap;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 950;
        line-height: 1;
        text-transform: uppercase;
    }

    .badge-tosca {
        background: var(--tosca-soft);
        color: var(--tosca-dark);
        border: 1px solid rgba(18,169,154,.18);
    }

    .badge-pink {
        background: var(--pink-soft);
        color: var(--pink-dark);
        border: 1px solid rgba(227,69,109,.18);
    }

    .badge-blue {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid rgba(3,105,161,.15);
    }

    .badge-yellow {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid rgba(180,83,9,.18);
    }

    .kelas-title {
        margin: 18px 0 0;
        background: #e2e8f0;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
    }

    .detail-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        background: white;
        margin-bottom: 16px;
    }

    .detail-table th,
    .detail-table td {
        border: 1px solid #d7e1e7;
        padding: 9px 10px;
        font-size: 12px;
    }

    .detail-table th {
        background: #0f2f4a;
        color: white;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 950;
    }

    .print-area {
        position: relative;
    }

    @media(max-width: 1200px) {
        .filter-grid,
        .summary-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 700px) {
        .filter-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .laporan-hero h2 {
            font-size: 24px;
        }
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            background: white !important;
        }

        .sidebar,
        .topbar,
        .laporan-hero,
        .filter-card,
        .no-print {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .content-wrap {
            padding: 0 !important;
        }

        .report-card,
        .summary-card {
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .summary-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 8px !important;
        }

        .report-table th,
        .detail-table th {
            background: #0f2f4a !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

@php
    $tanggalMulai = $tanggalMulai ?? request('tgl_awal', date('Y-m-01'));
    $tanggalSelesai = $tanggalSelesai ?? request('tgl_akhir', date('Y-m-d'));
    $jenis = $jenis ?? request('jenis', 'semua');
    $kelas = $kelas ?? request('kelas', '');
    $search = $search ?? request('search', '');

    $rekapNominalGlobal = $rekapNominalGlobal ?? collect();
    $rekapPerKelas = $rekapPerKelas ?? collect();
    $rincianPerKelas = $rincianPerKelas ?? collect();

    $totalPemasukan = $totalPemasukan ?? 0;
    $totalFormal = $totalFormal ?? 0;
    $totalPondok = $totalPondok ?? 0;
    $totalLain = $totalLain ?? 0;
    $jumlahTransaksi = $jumlahTransaksi ?? 0;
    $jumlahSantriBayar = $jumlahSantriBayar ?? 0;
@endphp

<div class="laporan-hero no-print">
    <h2>Laporan Pemasukan</h2>
    <p>
        Rekap pemasukan berdasarkan nominal pembayaran global, jumlah transaksi, status pembayaran,
        dan rincian pembayaran per kelas.
    </p>
</div>

<div class="filter-card no-print">
    <form action="{{ route('laporan-pemasukan.index') }}" method="GET">
        <div class="filter-grid">
            <div class="field-group">
                <label>Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="field-control" value="{{ $tanggalMulai }}">
            </div>

            <div class="field-group">
                <label>Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="field-control" value="{{ $tanggalSelesai }}">
            </div>

            <div class="field-group">
                <label>Jenis</label>
                <select name="jenis" class="field-control">
                    <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="formal" {{ $jenis === 'formal' ? 'selected' : '' }}>Formal</option>
                    <option value="pondok" {{ $jenis === 'pondok' ? 'selected' : '' }}>Pondok/Diniyah</option>
                    <option value="lain" {{ $jenis === 'lain' ? 'selected' : '' }}>Pembayaran Lain</option>
                </select>
            </div>

            <div class="field-group">
                <label>Kelas</label>
                <input type="text" name="kelas" class="field-control" value="{{ $kelas }}" placeholder="Contoh: 9 MTS">
            </div>

            <div class="field-group">
                <label>Pencarian</label>
                <input type="text" name="search" class="field-control" value="{{ $search }}" placeholder="Nama / NIS / NISN">
            </div>

            <button type="submit" class="btn btn-primary">
                Tampilkan
            </button>

            <button type="button" class="btn btn-light" onclick="window.print()">
                🖨 Cetak
            </button>
        </div>
    </form>
</div>

<div class="print-area">
    <div class="summary-grid">
        <div class="summary-card">
            <span>Total Pemasukan</span>
            <strong>Rp {{ number_format((int) $totalPemasukan, 0, ',', '.') }}</strong>
        </div>

        <div class="summary-card pink">
            <span>Formal</span>
            <strong>Rp {{ number_format((int) $totalFormal, 0, ',', '.') }}</strong>
        </div>

        <div class="summary-card">
            <span>Pondok/Diniyah</span>
            <strong>Rp {{ number_format((int) $totalPondok, 0, ',', '.') }}</strong>
        </div>

        <div class="summary-card pink">
            <span>Pembayaran Lain</span>
            <strong>Rp {{ number_format((int) $totalLain, 0, ',', '.') }}</strong>
        </div>

        <div class="summary-card">
            <span>Santri Bayar</span>
            <strong>{{ number_format((int) $jumlahSantriBayar, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="report-card">
        <div class="report-title">
            <div>
                <h3>I. Rekap Nominal Pembayaran Global</h3>
                <p>
                    Periode {{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }}
                    sampai {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }}.
                </p>
            </div>

            <div>
                <span class="badge-soft badge-blue">
                    {{ number_format((int) $jumlahTransaksi, 0, ',', '.') }} Transaksi
                </span>
            </div>
        </div>

        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th width="55">No</th>
                        <th>Nominal Pembayaran</th>
                        <th style="text-align:center;">Jumlah Transaksi</th>
                        <th style="text-align:center;">Lancar</th>
                        <th style="text-align:center;">Tunggakan</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rekapNominalGlobal as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <span class="nominal-big">
                                    Rp {{ number_format((int) $item->nominal, 0, ',', '.') }}
                                </span>
                            </td>

                            <td style="text-align:center;">
                                <span class="badge-soft badge-blue">
                                    {{ number_format((int) $item->jumlah_transaksi, 0, ',', '.') }} Transaksi
                                </span>
                            </td>

                            <td style="text-align:center;">
                                <span class="badge-soft badge-tosca">
                                    {{ number_format((int) $item->lancar, 0, ',', '.') }}
                                </span>
                            </td>

                            <td style="text-align:center;">
                                @if((int) $item->tunggakan > 0)
                                    <span class="badge-soft badge-pink">
                                        {{ number_format((int) $item->tunggakan, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge-soft badge-tosca">0</span>
                                @endif
                            </td>

                            <td style="text-align:right; font-weight:950; color:var(--tosca-dark); white-space:nowrap;">
                                Rp {{ number_format((int) $item->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:28px; color:var(--muted);">
                                Belum ada data pembayaran sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right;">Total Global</th>
                        <th style="text-align:center;">{{ number_format((int) $jumlahTransaksi, 0, ',', '.') }} Transaksi</th>
                        <th colspan="2"></th>
                        <th style="text-align:right;">
                            Rp {{ number_format((int) $totalPemasukan, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="report-card">
        <div class="report-title">
            <div>
                <h3>II. Rekap Ringkas Per Kelas</h3>
                <p>Total pembayaran per kelompok kelas.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th width="55">No</th>
                        <th>Kelas / Kelompok</th>
                        <th style="text-align:center;">Jumlah Transaksi</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rekapPerKelas as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->kelas }}</strong></td>
                            <td style="text-align:center;">{{ number_format((int) $item->jumlah_transaksi, 0, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:950;">
                                Rp {{ number_format((int) $item->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">
                                Belum ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-card">
        <div class="report-title">
            <div>
                <h3>III. Rincian Data Transaksi</h3>
                <p>Daftar santri yang membayar, dikelompokkan berdasarkan kelas.</p>
            </div>
        </div>

        @forelse($rincianPerKelas as $kelasGroup => $items)
            <div class="kelas-title">
                Kelas: {{ $kelasGroup }}
            </div>

            <div class="table-wrap" style="border-radius:0 0 16px 16px; margin-bottom:16px;">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th width="45">No</th>
                            <th width="95">Waktu</th>
                            <th>Nama Santri</th>
                            <th>Keterangan Transaksi</th>
                            <th width="115">Status</th>
                            <th width="130" style="text-align:right;">Nominal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td style="text-align:center;">{{ $index + 1 }}</td>

                                <td style="text-align:center;">
                                    {{ $item->waktu ?? '-' }}
                                </td>

                                <td>
                                    <strong>{{ $item->nama_siswa ?? '-' }}</strong><br>
                                    <small style="color:var(--muted);">
                                        NIS: {{ $item->nis ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $item->keterangan_transaksi ?? '-' }}

                                    @if(!empty($item->bulan_bayar) || !empty($item->tahun_bayar))
                                        <br>
                                        <small style="color:var(--muted);">
                                            {{ $item->bulan_bayar ?? '-' }} {{ $item->tahun_bayar ?? '' }}
                                        </small>
                                    @endif
                                </td>

                                <td style="text-align:center;">
                                    @php
                                        $statusLunas = strtoupper($item->status_lunas ?? 'LUNAS');
                                        $statusClass = $statusLunas === 'CICILAN' ? 'badge-yellow' : 'badge-tosca';
                                    @endphp

                                    <span class="badge-soft {{ $statusClass }}">
                                        {{ $statusLunas }}
                                    </span>

                                    <br>

                                    @if(($item->status_waktu ?? 'LANCAR') === 'TUNGGAKAN')
                                        <span class="badge-soft badge-pink" style="margin-top:4px;">
                                            TUNGGAKAN
                                        </span>
                                    @else
                                        <span class="badge-soft badge-blue" style="margin-top:4px;">
                                            LANCAR
                                        </span>
                                    @endif
                                </td>

                                <td style="text-align:right; font-weight:950; white-space:nowrap;">
                                    Rp {{ number_format((int) ($item->nominal ?? 0), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right;">Total Kelas</th>
                            <th style="text-align:right;">
                                Rp {{ number_format((int) $items->sum('nominal'), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @empty
            <div style="text-align:center; padding:32px; color:var(--muted);">
                Belum ada rincian transaksi sesuai filter.
            </div>
        @endforelse
    </div>
</div>
@endsection
