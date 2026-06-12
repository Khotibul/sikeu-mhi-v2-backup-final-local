<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPemasukanController extends Controller
{
    private array $cacheNominalFormal = [];
    private array $cacheNominalDiniyah = [];
    private bool $nominalFormalLoaded = false;
    private bool $nominalDiniyahLoaded = false;

    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_awal')
            ?? $request->get('tgl_awal')
            ?? now()->format('Y-m-01');

        $tanggalSelesai = $request->get('tanggal_akhir')
            ?? $request->get('tgl_akhir')
            ?? now()->format('Y-m-d');

        $jenis = $request->get('jenis', 'semua');
        $kelas = trim((string) $request->get('kelas', ''));
        $search = trim((string) $request->get('search', ''));

        $tanggalMulai = Carbon::parse($tanggalMulai)->format('Y-m-d');
        $tanggalSelesai = Carbon::parse($tanggalSelesai)->format('Y-m-d');

        $formal = collect();
        $pondok = collect();
        $lain = collect();

        if ($jenis === 'semua' || $jenis === 'formal') {
            $queryFormal = DB::table('pembayaran')
                ->leftJoin('siswa', 'pembayaran.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah',
                    'siswa.status_mukim',
                    'siswa.potongan_formal',
                    'siswa.potongan_diniyah'
                )
                ->whereBetween('pembayaran.tgl_bayar', [$tanggalMulai, $tanggalSelesai]);

            $this->applySiswaFilter($queryFormal, $kelas, $search);
            $this->applyUnitScopeIfExists($queryFormal);

            $formal = $queryFormal
                ->orderBy('pembayaran.tgl_bayar')
                ->orderBy('pembayaran.id_bayar')
                ->get()
                ->map(function ($item) {
                    $nominal = $this->nominalLaporanFormal($item);

                    $statusLunas = $this->statusPembayaranLaporan($item, $nominal, $this->nominalBulananFormal($item));

                    return (object) [
                        'id' => $item->id_bayar ?? null,
                        'id_siswa' => $item->id_siswa ?? null,
                        'tanggal' => $item->tgl_bayar ?? null,
                        'waktu' => $this->ambilWaktu($item->tgl_bayar ?? null, $item->created_at ?? null),
                        'nama_siswa' => $item->nama_siswa ?? '-',
                        'nis' => $item->nis ?? '-',
                        'nisn' => $item->nisn ?? '-',
                        'kelas_formal' => $item->kelas_formal ?? '-',
                        'kelas_diniyah' => $item->kelas_diniyah ?? '-',
                        'kelas_group' => $this->kelasGroup($item->kelas_formal ?? null, 'SEKOLAH FORMAL'),
                        'jenis' => 'Formal',
                        'keterangan_transaksi' => 'Biaya Pendidikan Formal',
                        'bulan_bayar' => $item->bulan_bayar ?? null,
                        'tahun_bayar' => $item->tahun_bayar ?? null,
                        'tahun_ajaran' => $item->tahun_ajaran ?? null,
                        'nominal' => $nominal,
                        'status_lunas' => $statusLunas,
                        'status_waktu' => $this->statusWaktuPembayaran(
                            $item->bulan_bayar ?? null,
                            $item->tahun_bayar ?? null,
                            $item->tgl_bayar ?? null
                        ),
                    ];
                });
        }

        if ($jenis === 'semua' || $jenis === 'pondok') {
            $queryPondok = DB::table('pembayaran_diniyah')
                ->leftJoin('siswa', 'pembayaran_diniyah.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran_diniyah.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah',
                    'siswa.status_mukim',
                    'siswa.potongan_formal',
                    'siswa.potongan_diniyah'
                )
                ->whereBetween('pembayaran_diniyah.tgl_bayar', [$tanggalMulai, $tanggalSelesai]);

            $this->applySiswaFilter($queryPondok, $kelas, $search);
            $this->applyUnitScopeIfExists($queryPondok);

            $pondok = $queryPondok
                ->orderBy('pembayaran_diniyah.tgl_bayar')
                ->orderBy('pembayaran_diniyah.id_bayar_diniyah')
                ->get()
                ->map(function ($item) {
                    $nominal = $this->nominalLaporanDiniyah($item);

                    $statusLunas = $this->statusPembayaranLaporan($item, $nominal, $this->nominalBulananDiniyah($item));

                    return (object) [
                        'id' => $item->id_bayar_diniyah ?? null,
                        'id_siswa' => $item->id_siswa ?? null,
                        'tanggal' => $item->tgl_bayar ?? null,
                        'waktu' => $this->ambilWaktu($item->tgl_bayar ?? null, $item->created_at ?? null),
                        'nama_siswa' => $item->nama_siswa ?? '-',
                        'nis' => $item->nis ?? '-',
                        'nisn' => $item->nisn ?? '-',
                        'kelas_formal' => $item->kelas_formal ?? '-',
                        'kelas_diniyah' => $item->kelas_diniyah ?? '-',
                        'kelas_group' => $this->kelasGroup($item->kelas_diniyah ?? null, 'MADRASAH DINIYAH'),
                        'jenis' => 'Pondok/Diniyah',
                        'keterangan_transaksi' => 'Biaya Pendidikan Pondok',
                        'bulan_bayar' => $item->bulan_bayar ?? null,
                        'tahun_bayar' => $item->tahun_bayar ?? null,
                        'tahun_ajaran' => $item->tahun_ajaran ?? null,
                        'nominal' => $nominal,
                        'status_lunas' => $statusLunas,
                        'status_waktu' => $this->statusWaktuPembayaran(
                            $item->bulan_bayar ?? null,
                            $item->tahun_bayar ?? null,
                            $item->tgl_bayar ?? null
                        ),
                    ];
                });
        }

        if ($jenis === 'semua' || $jenis === 'lain') {
            $queryLain = DB::table('pembayaran_pangkal')
                ->leftJoin('siswa', 'pembayaran_pangkal.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran_pangkal.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah',
                    'siswa.status_mukim'
                )
                ->whereBetween('pembayaran_pangkal.tgl_bayar', [$tanggalMulai, $tanggalSelesai]);

            if ($search !== '') {
                $queryLain->where(function ($q) use ($search) {
                    $q->where('siswa.nama_siswa', 'like', '%' . $search . '%')
                        ->orWhere('siswa.nis', 'like', '%' . $search . '%')
                        ->orWhere('siswa.nisn', 'like', '%' . $search . '%')
                        ->orWhere('pembayaran_pangkal.jenis_tagihan', 'like', '%' . $search . '%')
                        ->orWhere('pembayaran_pangkal.keterangan', 'like', '%' . $search . '%');
                });
            }

            if ($kelas !== '') {
                $queryLain->where(function ($q) use ($kelas) {
                    $q->where('siswa.kelas_formal', 'like', '%' . $kelas . '%')
                        ->orWhere('siswa.kelas_diniyah', 'like', '%' . $kelas . '%');
                });
            }

            $this->applyUnitScopeIfExists($queryLain);

            $lain = $queryLain
                ->orderBy('pembayaran_pangkal.tgl_bayar')
                ->orderBy('pembayaran_pangkal.id_pangkal')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id' => $item->id_pangkal ?? null,
                        'id_siswa' => $item->id_siswa ?? null,
                        'tanggal' => $item->tgl_bayar ?? null,
                        'waktu' => $this->ambilWaktu($item->tgl_bayar ?? null, $item->created_at ?? null),
                        'nama_siswa' => $item->nama_siswa ?? '-',
                        'nis' => $item->nis ?? '-',
                        'nisn' => $item->nisn ?? '-',
                        'kelas_formal' => $item->kelas_formal ?? '-',
                        'kelas_diniyah' => $item->kelas_diniyah ?? '-',
                        'kelas_group' => 'LAINNYA (LAIN-LAIN)',
                        'jenis' => 'Pembayaran Lain',
                        'keterangan_transaksi' => $item->jenis_tagihan ?? 'Pembayaran Lain',
                        'bulan_bayar' => null,
                        'tahun_bayar' => null,
                        'tahun_ajaran' => null,
                        'nominal' => (int) ($item->nominal_bayar ?? 0),
                        'status_lunas' => 'LUNAS',
                        'status_waktu' => 'LANCAR',
                    ];
                });
        }

        $semuaTransaksi = $formal
            ->merge($pondok)
            ->merge($lain)
            ->sortBy(function ($item) {
                return ($item->kelas_group ?? '') . '|' . ($item->tanggal ?? '') . '|' . ($item->nama_siswa ?? '') . '|' . ($item->bulan_bayar ?? '');
            })
            ->values();

        $rekapNominalGlobal = $semuaTransaksi
            ->filter(fn($item) => (int) ($item->nominal ?? 0) > 0)
            ->groupBy(fn($item) => (int) $item->nominal)
            ->map(function ($items, $nominal) {
                $jumlahOrang = $items
                    ->map(function ($item) {
                        return $item->id_siswa
                            ?? $item->nis
                            ?? $item->nisn
                            ?? $item->nama_siswa
                            ?? null;
                    })
                    ->filter()
                    ->unique()
                    ->count();

                return (object) [
                    'nominal' => (int) $nominal,
                    'jumlah_orang' => $jumlahOrang,
                    'jumlah_transaksi' => $items->count(),
                    'total' => $items->sum('nominal'),
                    'lancar' => $items->where('status_waktu', 'LANCAR')->count(),
                    'tunggakan' => $items->where('status_waktu', 'TUNGGAKAN')->count(),
                ];
            })
            ->sortKeys()
            ->values();

        $rekapPerKelas = $semuaTransaksi
            ->groupBy('kelas_group')
            ->map(function ($items, $kelasGroup) {
                return (object) [
                    'kelas' => $kelasGroup,
                    'jumlah_orang' => $items
                        ->map(fn($item) => $item->id_siswa ?? $item->nis ?? $item->nama_siswa ?? null)
                        ->filter()
                        ->unique()
                        ->count(),
                    'jumlah_transaksi' => $items->count(),
                    'total' => $items->sum('nominal'),
                ];
            })
            ->values();

        $rincianPerKelas = $semuaTransaksi->groupBy('kelas_group');

        $totalFormal = $formal->sum('nominal');
        $totalPondok = $pondok->sum('nominal');
        $totalLain = $lain->sum('nominal');
        $totalPemasukan = $semuaTransaksi->sum('nominal');
        $jumlahTransaksi = $semuaTransaksi->count();
        $jumlahSantriBayar = $semuaTransaksi
            ->map(fn($item) => $item->id_siswa ?? $item->nis ?? $item->nisn ?? $item->nama_siswa ?? null)
            ->filter()
            ->unique()
            ->count();

        return view('laporan-pemasukan.index', compact(
            'tanggalMulai',
            'tanggalSelesai',
            'jenis',
            'kelas',
            'search',
            'formal',
            'pondok',
            'lain',
            'semuaTransaksi',
            'rekapNominalGlobal',
            'rekapPerKelas',
            'rincianPerKelas',
            'totalFormal',
            'totalPondok',
            'totalLain',
            'totalPemasukan',
            'jumlahTransaksi',
            'jumlahSantriBayar'
        ));
    }

    private function applySiswaFilter($query, string $kelas = '', string $search = ''): void
    {
        if ($kelas !== '') {
            $query->where(function ($q) use ($kelas) {
                $q->where('siswa.kelas_formal', 'like', '%' . $kelas . '%')
                    ->orWhere('siswa.kelas_diniyah', 'like', '%' . $kelas . '%');
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_siswa', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nis', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nisn', 'like', '%' . $search . '%')
                    ->orWhere('siswa.kelas_formal', 'like', '%' . $search . '%')
                    ->orWhere('siswa.kelas_diniyah', 'like', '%' . $search . '%');
            });
        }
    }

    private function applyUnitScopeIfExists($query): void
    {
        if (class_exists(\App\Support\AdminUnitScope::class)) {
            \App\Support\AdminUnitScope::applyToSiswaQuery($query, 'siswa');
        }
    }

    private function ambilNominalPembayaran($item): int
    {
        $terbayar = (int) ($item->terbayar ?? 0);

        if ($terbayar > 0) {
            return $terbayar;
        }

        return (int) ($item->jumlah_bayar ?? $item->nominal_bayar ?? 0);
    }


    private function statusPembayaranLaporan($item, int $nominalBayar, int $tagihan): string
    {
        $statusDb = strtoupper(trim((string) ($item->status_bayar ?? $item->status ?? '')));

        if (in_array($statusDb, ['LUNAS', 'PELUNASAN'], true)) {
            return 'LUNAS';
        }

        if (in_array($statusDb, ['CICIL', 'CICILAN', 'NYICIL', 'ANGSURAN'], true)) {
            return 'CICILAN';
        }

        $keterangan = strtolower((string) ($item->keterangan ?? ''));

        if (str_contains($keterangan, 'cicil') || str_contains($keterangan, 'angsuran') || str_contains($keterangan, 'nyicil')) {
            return 'CICILAN';
        }

        if (str_contains($keterangan, 'lunas') || str_contains($keterangan, 'pelunasan')) {
            return 'LUNAS';
        }

        if ($tagihan > 0 && $nominalBayar > 0 && $nominalBayar < $tagihan) {
            return 'CICILAN';
        }

        return 'LUNAS';
    }

    private function nominalLaporanFormal($item): int
    {
        $nominalDariRow = $this->ambilNominalPembayaran($item);
        $nominalBulanan = $this->nominalBulananFormal($item);

        if ($nominalBulanan > 0 && $nominalDariRow > $nominalBulanan) {
            return $nominalBulanan;
        }

        return $nominalDariRow;
    }

    private function nominalLaporanDiniyah($item): int
    {
        $nominalDariRow = $this->ambilNominalPembayaran($item);
        $nominalBulanan = $this->nominalBulananDiniyah($item);

        if ($nominalBulanan > 0 && $nominalDariRow > $nominalBulanan) {
            return $nominalBulanan;
        }

        return $nominalDariRow;
    }

    private function nominalBulananFormal($item): int
    {
        /*
         * Di sistem sekarang, kolom potongan_formal dipakai sebagai
         * Nominal SPP Formal Khusus per bulan.
         */
        $nominalKhusus = (int) ($item->potongan_formal ?? 0);

        if ($nominalKhusus > 0) {
            return $nominalKhusus;
        }

        return $this->nominalDefaultFormal($item->kelas_formal ?? null);
    }

    private function nominalBulananDiniyah($item): int
    {
        /*
         * Di sistem sekarang, kolom potongan_diniyah dipakai sebagai
         * Nominal SPP Pondok/Diniyah Khusus per bulan.
         */
        $nominalKhusus = (int) ($item->potongan_diniyah ?? 0);

        if ($nominalKhusus > 0) {
            return $nominalKhusus;
        }

        return $this->nominalDefaultDiniyah($item->kelas_diniyah ?? null);
    }

    private function nominalDefaultFormal(?string $kelas): int
    {
        $this->loadNominalFormal();

        $key = $this->normalKey($kelas);

        return (int) ($this->cacheNominalFormal[$key] ?? 0);
    }

    private function nominalDefaultDiniyah(?string $kelas): int
    {
        $this->loadNominalDiniyah();

        $key = $this->normalKey($kelas);

        return (int) ($this->cacheNominalDiniyah[$key] ?? 0);
    }

    private function loadNominalFormal(): void
    {
        if ($this->nominalFormalLoaded) {
            return;
        }

        $this->nominalFormalLoaded = true;

        try {
            DB::table('data_kelas')
                ->select('nama_kelas', 'nominal_spp')
                ->get()
                ->each(function ($item) {
                    $this->cacheNominalFormal[$this->normalKey($item->nama_kelas ?? '')] = (int) ($item->nominal_spp ?? 0);
                });
        } catch (\Throwable $e) {
            $this->cacheNominalFormal = [];
        }
    }

    private function loadNominalDiniyah(): void
    {
        if ($this->nominalDiniyahLoaded) {
            return;
        }

        $this->nominalDiniyahLoaded = true;

        try {
            DB::table('data_kelas_diniyah')
                ->select('nama_kelas', 'nominal_spp')
                ->get()
                ->each(function ($item) {
                    $this->cacheNominalDiniyah[$this->normalKey($item->nama_kelas ?? '')] = (int) ($item->nominal_spp ?? 0);
                });
        } catch (\Throwable $e) {
            $this->cacheNominalDiniyah = [];
        }
    }

    private function normalKey(?string $value): string
    {
        $value = strtoupper(trim((string) $value));
        $value = preg_replace('/\s+/', ' ', $value);

        return $value ?: '-';
    }

    private function kelasGroup(?string $kelas, string $kategori): string
    {
        $kelas = trim((string) $kelas);

        if ($kelas === '' || $kelas === '-') {
            return 'LAINNYA (LAIN-LAIN)';
        }

        return strtoupper($kelas) . ' (' . $kategori . ')';
    }

    private function ambilWaktu($tanggal, $createdAt = null): string
    {
        try {
            if (!empty($createdAt)) {
                return Carbon::parse($createdAt)->format('d/m H:i');
            }

            if (!empty($tanggal)) {
                return Carbon::parse($tanggal)->format('d/m');
            }
        } catch (\Throwable $e) {
            return '-';
        }

        return '-';
    }

    private function statusWaktuPembayaran($bulanBayar, $tahunBayar, $tglBayar): string
    {
        if (empty($bulanBayar) || empty($tahunBayar) || empty($tglBayar)) {
            return 'LANCAR';
        }

        $bulanKeAngka = $this->bulanKeAngka($bulanBayar);

        if (!$bulanKeAngka) {
            return 'LANCAR';
        }

        try {
            $tanggalBayar = Carbon::parse($tglBayar);
        } catch (\Throwable $e) {
            return 'LANCAR';
        }

        $periodeBayar = ((int) $tahunBayar * 100) + (int) $bulanKeAngka;
        $periodeTransaksi = ((int) $tanggalBayar->format('Y') * 100) + (int) $tanggalBayar->format('m');

        return $periodeBayar < $periodeTransaksi ? 'TUNGGAKAN' : 'LANCAR';
    }

    private function bulanKeAngka($bulan): ?int
    {
        $bulan = strtolower(trim((string) $bulan));

        $map = [
            'januari' => 1,
            'jan' => 1,
            'februari' => 2,
            'feb' => 2,
            'maret' => 3,
            'mar' => 3,
            'april' => 4,
            'apr' => 4,
            'mei' => 5,
            'may' => 5,
            'juni' => 6,
            'jun' => 6,
            'juli' => 7,
            'jul' => 7,
            'agustus' => 8,
            'agu' => 8,
            'aug' => 8,
            'september' => 9,
            'sep' => 9,
            'oktober' => 10,
            'okt' => 10,
            'oct' => 10,
            'november' => 11,
            'nov' => 11,
            'desember' => 12,
            'des' => 12,
            'dec' => 12,
        ];

        /*
         * Fix khusus: array di atas harus tetap punya key november.
         */
        $map['november'] = 11;

        if (isset($map[$bulan])) {
            return $map[$bulan];
        }

        if (is_numeric($bulan)) {
            $angka = (int) $bulan;
            return $angka >= 1 && $angka <= 12 ? $angka : null;
        }

        return null;
    }
}
