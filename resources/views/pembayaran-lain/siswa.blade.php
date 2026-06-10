@extends('layouts.app')

@section('title', 'Pembayaran Lain - ' . $siswa->nama_siswa)

@section('page_title', 'Pembayaran Lain Santri')

@section('page_subtitle',
    'Input pembayaran non-bulanan seperti daftar ulang, UKT, seragam, kitab, dan biaya khusus
    santri.')

@section('content')
    <style>
        .icon-wa {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff !important;
        }

        .icon-wa-disabled {
            background: #e2e8f0;
            color: #94a3b8 !important;
            cursor: not-allowed;
        }

        .lain-detail-hero {
            background: linear-gradient(135deg, var(--tosca), var(--pink));
            color: white;
            border-radius: 30px;
            padding: 28px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .lain-detail-hero::after {
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
            grid-template-columns: 80px 1fr auto;
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
            font-weight: 700;
        }

        .hero-action {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 20px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            width: 96px;
            height: 96px;
            border-radius: 999px;
            background: var(--tosca-soft);
            right: -30px;
            top: -34px;
        }

        .stat-card.pink::after {
            background: var(--pink-soft);
        }

        .stat-card span {
            display: block;
            position: relative;
            z-index: 1;
            color: var(--muted);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-card strong {
            display: block;
            position: relative;
            z-index: 1;
            color: var(--tosca-dark);
            font-size: 26px;
            font-weight: 950;
        }

        .stat-card.pink strong {
            color: var(--pink-dark);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 18px;
            align-items: start;
        }

        .payment-card,
        .history-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 22px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .payment-card::after,
        .history-card::after {
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

        .card-content {
            position: relative;
            z-index: 1;
        }

        .card-title {
            margin-bottom: 18px;
        }

        .card-title h3 {
            margin: 0;
            color: var(--tosca-dark);
            font-size: 21px;
            font-weight: 950;
        }

        .card-title p {
            margin: 5px 0 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 13px;
        }

        .form-stack {
            display: grid;
            gap: 14px;
        }

        .field-group {
            display: grid;
            gap: 7px;
        }

        .field-group label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .field-control {
            width: 100%;
            height: 48px;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0 14px;
            outline: none;
            font-size: 14px;
            background: white;
            color: var(--text);
            font-weight: 800;
        }

        textarea.field-control {
            height: 94px;
            padding: 12px 14px;
            resize: vertical;
            line-height: 1.5;
        }

        .field-control:focus {
            border-color: var(--tosca);
            box-shadow: 0 0 0 4px rgba(18, 169, 154, .10);
        }

        .nominal-box {
            background: linear-gradient(135deg, var(--tosca-soft), #ffffff);
            border: 1px solid rgba(18, 169, 154, .22);
            border-radius: 20px;
            padding: 16px;
        }

        .nominal-box span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .nominal-box strong {
            color: var(--tosca-dark);
            font-size: 25px;
            font-weight: 950;
        }

        .submit-btn {
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

        .history-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .history-search {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .history-search input {
            width: 260px;
            max-width: 100%;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0 13px;
            outline: none;
            font-weight: 800;
        }

        .history-table-wrap {
            overflow-x: auto;
            border: 1px solid #d7e1e7;
            border-radius: 18px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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
            font-size: 18px;
        }

        .kind-badge {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            background: var(--pink-soft);
            color: var(--pink-dark);
            text-transform: uppercase;
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
            color: white !important;
        }

        .icon-delete {
            background: linear-gradient(135deg, #ef476f, #e11d48);
            color: white;
        }

        .empty-box {
            text-align: center;
            padding: 42px 18px;
            color: var(--muted);
        }

        .empty-box h3 {
            color: var(--tosca-dark);
            margin: 0 0 8px;
            font-size: 22px;
            font-weight: 950;
        }

        @media(max-width: 1200px) {

            .hero-content,
            .stat-grid,
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $jenisList = $jenisPembayaran ?? ($jenis ?? ($dataJenis ?? collect()));

        if (isset($riwayat)) {
            $dataRiwayat = $riwayat;
        } elseif (isset($riwayatPembayaran)) {
            $dataRiwayat = $riwayatPembayaran;
        } elseif (isset($pembayaranLain)) {
            $dataRiwayat = $pembayaranLain;
        } elseif (isset($pembayaran)) {
            $dataRiwayat = $pembayaran;
        } else {
            $dataRiwayat = collect();
        }

        $totalTransaksi = $dataRiwayat->count();

        $totalNominal = $dataRiwayat->sum(function ($item) {
            return (int) ($item->nominal_bayar ?? ($item->jumlah_bayar ?? ($item->nominal ?? 0)));
        });

        $tanggalTerakhir =
            optional(
                $dataRiwayat
                    ->sortByDesc(function ($item) {
                        return $item->tgl_bayar ?? ($item->tanggal ?? ($item->created_at ?? null));
                    })
                    ->first(),
            )->tgl_bayar ?? null;
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

        $buatLinkWaKwitansiLain = function ($nomor, $namaSiswa, $jenis, $nominal, $urlKwitansi) use ($formatNoWa) {
            $nomorWa = $formatNoWa($nomor);

            if (!$nomorWa) {
                return null;
            }

            $pesan =
                "Assalamu'alaikum Wr. Wb.\n\n" .
                "Yth. Wali Santri dari {$namaSiswa},\n" .
                "Berikut kami kirimkan bukti pembayaran:\n\n" .
                "Jenis: {$jenis}\n" .
                'Nominal: Rp ' .
                number_format((int) $nominal, 0, ',', '.') .
                "\n\n" .
                "Link kwitansi:\n{$urlKwitansi}\n\n" .
                "Terima kasih.\n" .
                "Bendahara YPP Mamba'ul Khoiriyatil Islamiyah";

            return 'https://wa.me/' . $nomorWa . '?text=' . rawurlencode($pesan);
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

    <a href="{{ route('pembayaran-lain.index') }}" class="btn btn-light" style="margin-bottom:16px;">
        ← Kembali Cari Santri
    </a>

    <div class="lain-detail-hero">
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

            <div class="hero-action">
                <a href="{{ route('pembayaran-spp.siswa', $siswa->id_siswa) }}" class="btn btn-light">
                    💳 SPP
                </a>
            </div>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span>Total Transaksi</span>
            <strong>{{ number_format($totalTransaksi, 0, ',', '.') }}</strong>
        </div>

        <div class="stat-card pink">
            <span>Total Pembayaran</span>
            <strong>Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong>
        </div>

        <div class="stat-card">
            <span>Transaksi Terakhir</span>
            <strong style="font-size:20px;">
                @if ($tanggalTerakhir)
                    {{ \Carbon\Carbon::parse($tanggalTerakhir)->format('d-m-Y') }}
                @else
                    -
                @endif
            </strong>
        </div>
    </div>

    <div class="main-grid">
        <div class="payment-card">
            <div class="card-content">
                <div class="card-title">
                    <h3>🧾 Input Pembayaran</h3>
                    <p>Pilih jenis pembayaran, nominal bisa mengikuti standar atau diedit manual.</p>
                </div>

                <form action="{{ route('pembayaran-lain.bayar', $siswa->id_siswa) }}" method="POST" class="form-stack"
                    onsubmit="return confirm('Simpan pembayaran lain santri ini?')">
                    @csrf

                    <div class="field-group">
                        <label>Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" value="{{ date('Y-m-d') }}" class="field-control" required>
                    </div>

                    <div class="field-group">
                        <label>Jenis Pembayaran</label>
                        <select name="jenis_tagihan" id="jenis_tagihan" class="field-control" required>
                            <option value="">-- Pilih Jenis Pembayaran --</option>

                            @foreach ($jenisList as $jenis)
                                @php
                                    $namaJenis = $jenis->nama_jenis ?? ($jenis->jenis_tagihan ?? ($jenis->nama ?? '-'));
                                    $nominalStandar = (int) ($jenis->nominal_standar ?? ($jenis->nominal ?? 0));
                                @endphp

                                <option value="{{ $namaJenis }}" data-nominal="{{ $nominalStandar }}">
                                    {{ $namaJenis }}
                                    @if ($nominalStandar > 0)
                                        - Rp {{ number_format($nominalStandar, 0, ',', '.') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Nominal Bayar</label>
                        <input type="number" name="nominal_bayar" id="nominal_bayar" value="{{ old('nominal_bayar') }}"
                            min="1" class="field-control" placeholder="Masukkan nominal pembayaran" required>
                    </div>

                    <div class="nominal-box">
                        <span>Preview Nominal</span>
                        <strong id="preview_nominal">Rp 0</strong>
                    </div>

                    <div class="field-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="field-control" placeholder="Contoh: Pembayaran daftar ulang / UKT / seragam...">{{ old('keterangan') }}</textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        ✈ Simpan Pembayaran
                    </button>
                </form>
            </div>
        </div>

        <div class="history-card">
            <div class="card-content">
                <div class="history-toolbar">
                    <div class="card-title" style="margin-bottom:0;">
                        <h3>Riwayat Pembayaran Lain</h3>
                        <p>Daftar pembayaran lain santri ini.</p>
                    </div>

                    <div class="history-search">
                        <input type="text" id="searchRiwayat" placeholder="Cari riwayat...">
                    </div>
                </div>

                <div class="history-table-wrap">
                    <table class="history-table" id="tableRiwayat">
                        <thead>
                            <tr>
                                <th width="55">No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th width="115">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($dataRiwayat as $index => $item)
                                @php
                                    $idBayar = $item->id_pangkal ?? ($item->id_bayar_lain ?? ($item->id ?? null));
                                    $tanggal = $item->tgl_bayar ?? ($item->tanggal ?? ($item->created_at ?? null));
                                    $jenisTagihan =
                                        $item->jenis_tagihan ?? ($item->jenis_bayar ?? ($item->nama_jenis ?? '-'));
                                    $nominal =
                                        (int) ($item->nominal_bayar ?? ($item->jumlah_bayar ?? ($item->nominal ?? 0)));
                                    $keterangan = $item->keterangan ?? '-';
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        @if ($tanggal)
                                            {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        <span class="kind-badge">{{ $jenisTagihan }}</span>
                                    </td>

                                    <td>{{ $keterangan }}</td>

                                    <td class="number">
                                        Rp {{ number_format($nominal, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if ($idBayar)
                                            <div class="action-group">
                                                <a href="{{ route('pembayaran-lain.kwitansi', $idBayar) }}" target="_blank"
                                                    class="icon-btn icon-print" title="Cetak Kwitansi">
                                                    🖨
                                                </a>
                                                @php
                                                    $linkWaLain = $buatLinkWaKwitansiLain(
                                                        $siswa->no_hp ?? null,
                                                        $siswa->nama_siswa ?? '-',
                                                        $jenisTagihan ?? '-',
                                                        $nominal ?? 0,
                                                        route('pembayaran-lain.kwitansi', $idBayar),
                                                    );
                                                @endphp

                                                @if ($linkWaLain)
                                                    <a href="{{ $linkWaLain }}" target="_blank" class="icon-btn icon-wa"
                                                        title="Kirim Kwitansi ke WhatsApp">
                                                        🟢
                                                    </a>
                                                @else
                                                    <span class="icon-btn icon-wa-disabled"
                                                        title="Nomor WhatsApp belum terisi">
                                                        🟢
                                                    </span>
                                                @endif

                                                <form action="{{ route('pembayaran-lain.hapus', $idBayar) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Hapus riwayat pembayaran ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="icon-btn icon-delete" title="Hapus">
                                                        🗑
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-box">
                                            <h3>Belum ada riwayat pembayaran</h3>
                                            <p>Riwayat pembayaran lain akan muncul setelah transaksi disimpan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatRupiah(angka) {
            angka = parseInt(angka || 0);

            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        const jenisTagihan = document.getElementById('jenis_tagihan');
        const nominalBayar = document.getElementById('nominal_bayar');
        const previewNominal = document.getElementById('preview_nominal');

        function updatePreview() {
            previewNominal.textContent = formatRupiah(nominalBayar.value);
        }

        if (jenisTagihan) {
            jenisTagihan.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const nominal = selected.getAttribute('data-nominal');

                if (nominal && parseInt(nominal) > 0) {
                    nominalBayar.value = nominal;
                }

                updatePreview();
            });
        }

        if (nominalBayar) {
            nominalBayar.addEventListener('input', updatePreview);
            updatePreview();
        }

        const searchRiwayat = document.getElementById('searchRiwayat');
        const tableRiwayat = document.getElementById('tableRiwayat');

        if (searchRiwayat && tableRiwayat) {
            searchRiwayat.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const rows = tableRiwayat.querySelectorAll('tbody tr');

                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    </script>
@endsection
