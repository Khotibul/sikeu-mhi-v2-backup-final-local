<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DataKelas;
use App\Models\DataKelasDiniyah;
use App\Models\Pembayaran;
use App\Models\PembayaranDiniyah;
use Illuminate\Http\Request;

class TunggakanController extends Controller
{
    private array $bulanList = [
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
    ];

    public function index(Request $request)
    {
        $tahunAjaranList = $this->tahunAjaranList();
        $kelasFormalList = DataKelas::orderBy('nama_kelas')->get();
        $kelasDiniyahList = DataKelasDiniyah::orderBy('nama_kelas')->get();

        $tahunAjaran = $request->get('tahun_ajaran', '2025/2026');
        $sampaiBulan = $request->get('sampai_bulan');
        $kelasFormalFilter = $request->get('kelas_formal');
        $kelasDiniyahFilter = $request->get('kelas_diniyah');
        $search = $request->get('search');

        $isProcessed = $request->has('proses');

        $laporan = [];
        $bulanDicek = [];
        $totalFormal = 0;
        $totalPondok = 0;
        $grandTotal = 0;
        $jumlahSantriMenunggak = 0;

        if ($isProcessed) {
            if (empty($sampaiBulan)) {
                return back()->with('error', 'Pilih sampai bulan terlebih dahulu.');
            }

            if (empty($kelasFormalFilter) && empty($kelasDiniyahFilter)) {
                return back()->with('error', 'Pilih minimal kelas formal atau kelas diniyah terlebih dahulu.');
            }

            $bulanDicek = $this->bulanSampai($sampaiBulan);

            $query = Siswa::query()
                ->where(function ($q) {
                    $q->where('status_aktif', 'Aktif')
                        ->orWhereNull('status_aktif')
                        ->orWhere('status_aktif', '');
                });

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_siswa', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%')
                        ->orWhere('nisn', 'like', '%' . $search . '%')
                        ->orWhere('nama_wali', 'like', '%' . $search . '%')
                        ->orWhere('nama_ibu', 'like', '%' . $search . '%');
                });
            }

            if (!empty($kelasFormalFilter)) {
                $query->where('kelas_formal', $kelasFormalFilter);
            }

            if (!empty($kelasDiniyahFilter)) {
                $query->where('kelas_diniyah', $kelasDiniyahFilter);
            }

            $siswaList = $query->orderBy('nama_siswa')->get();

            foreach ($siswaList as $siswa) {
                $tunggakanFormal = 0;
                $tunggakanPondok = 0;
                $bulanFormal = [];
                $bulanPondok = [];

                if (!empty($kelasFormalFilter) && $this->punyaFormal($siswa)) {
                    $hasilFormal = $this->hitungTunggakanFormalDariRiwayat(
                        $siswa,
                        $tahunAjaran,
                        $bulanDicek
                    );

                    $tunggakanFormal = $hasilFormal['total'];
                    $bulanFormal = $hasilFormal['bulan'];
                }

                if (!empty($kelasDiniyahFilter) && $this->punyaDiniyah($siswa)) {
                    $hasilPondok = $this->hitungTunggakanDiniyahDariRiwayat(
                        $siswa,
                        $tahunAjaran,
                        $bulanDicek
                    );

                    $tunggakanPondok = $hasilPondok['total'];
                    $bulanPondok = $hasilPondok['bulan'];
                }

                $totalSantri = $tunggakanFormal + $tunggakanPondok;

                if ($totalSantri > 0) {
                    $jumlahSantriMenunggak++;

                    $laporan[] = [
                        'siswa' => $siswa,
                        'formal' => $tunggakanFormal,
                        'pondok' => $tunggakanPondok,
                        'total' => $totalSantri,
                        'bulan_formal' => $bulanFormal,
                        'bulan_pondok' => $bulanPondok,
                    ];

                    $totalFormal += $tunggakanFormal;
                    $totalPondok += $tunggakanPondok;
                }
            }

            $grandTotal = $totalFormal + $totalPondok;
        }

        return view('tunggakan.index', compact(
            'tahunAjaranList',
            'kelasFormalList',
            'kelasDiniyahList',
            'tahunAjaran',
            'sampaiBulan',
            'kelasFormalFilter',
            'kelasDiniyahFilter',
            'search',
            'isProcessed',
            'bulanDicek',
            'laporan',
            'totalFormal',
            'totalPondok',
            'grandTotal',
            'jumlahSantriMenunggak'
        ));
    }

    private function hitungTunggakanFormalDariRiwayat(Siswa $siswa, string $tahunAjaran, array $bulanDicek): array
    {
        $nominalFormal = $this->hitungNominalFormal($siswa);

        $totalTunggakan = 0;
        $bulanTunggakan = [];

        foreach ($bulanDicek as $bulan) {
            $tahunBayar = $this->tahunBayarDariBulan($bulan, $tahunAjaran);

            $transaksi = $this->cariPembayaranFormalLegacy(
                $siswa->id_siswa,
                $bulan,
                $tahunBayar,
                $tahunAjaran
            );

            $tagihan = $transaksi
                ? (int) ($transaksi->jumlah_bayar ?: $nominalFormal)
                : $nominalFormal;

            $terbayar = $this->ambilTerbayarLegacy($transaksi);

            $sisa = max($tagihan - $terbayar, 0);

            if ($sisa > 0) {
                $totalTunggakan += $sisa;

                if ($terbayar > 0) {
                    $bulanTunggakan[] = $bulan . ' (Cicil)';
                } else {
                    $bulanTunggakan[] = $bulan;
                }
            }
        }

        return [
            'total' => $totalTunggakan,
            'bulan' => $bulanTunggakan,
        ];
    }

    private function hitungTunggakanDiniyahDariRiwayat(Siswa $siswa, string $tahunAjaran, array $bulanDicek): array
    {
        $nominalDiniyah = $this->hitungNominalDiniyah($siswa);

        $totalTunggakan = 0;
        $bulanTunggakan = [];

        foreach ($bulanDicek as $bulan) {
            $tahunBayar = $this->tahunBayarDariBulan($bulan, $tahunAjaran);

            $transaksi = $this->cariPembayaranDiniyahLegacy(
                $siswa->id_siswa,
                $bulan,
                $tahunBayar,
                $tahunAjaran
            );

            $tagihan = $transaksi
                ? (int) ($transaksi->jumlah_bayar ?: $nominalDiniyah)
                : $nominalDiniyah;

            $terbayar = $this->ambilTerbayarLegacy($transaksi);

            $sisa = max($tagihan - $terbayar, 0);

            if ($sisa > 0) {
                $totalTunggakan += $sisa;

                if ($terbayar > 0) {
                    $bulanTunggakan[] = $bulan . ' (Cicil)';
                } else {
                    $bulanTunggakan[] = $bulan;
                }
            }
        }

        return [
            'total' => $totalTunggakan,
            'bulan' => $bulanTunggakan,
        ];
    }

    private function cariPembayaranFormalLegacy($idSiswa, string $bulan, string $tahunBayar, string $tahunAjaran)
    {
        return Pembayaran::where('id_siswa', $idSiswa)
            ->whereIn('bulan_bayar', $this->bulanVariants($bulan))
            ->where(function ($query) use ($tahunBayar) {
                $query->where('tahun_bayar', $tahunBayar)
                    ->orWhereNull('tahun_bayar')
                    ->orWhere('tahun_bayar', '');
            })
            ->where(function ($query) use ($tahunAjaran) {
                $query->whereIn('tahun_ajaran', $this->tahunAjaranVariants($tahunAjaran))
                    ->orWhereNull('tahun_ajaran')
                    ->orWhere('tahun_ajaran', '');
            })
            ->orderByDesc('tgl_bayar')
            ->orderByDesc('id_bayar')
            ->first();
    }

    private function cariPembayaranDiniyahLegacy($idSiswa, string $bulan, string $tahunBayar, string $tahunAjaran)
    {
        return PembayaranDiniyah::where('id_siswa', $idSiswa)
            ->whereIn('bulan_bayar', $this->bulanVariants($bulan))
            ->where(function ($query) use ($tahunBayar) {
                $query->where('tahun_bayar', $tahunBayar)
                    ->orWhereNull('tahun_bayar')
                    ->orWhere('tahun_bayar', '');
            })
            ->where(function ($query) use ($tahunAjaran) {
                $query->whereIn('tahun_ajaran', $this->tahunAjaranVariants($tahunAjaran))
                    ->orWhereNull('tahun_ajaran')
                    ->orWhere('tahun_ajaran', '');
            })
            ->orderByDesc('tgl_bayar')
            ->orderByDesc('id_bayar_diniyah')
            ->first();
    }

    private function tahunAjaranVariants(string $tahunAjaran): array
    {
        $variants = [
            $tahunAjaran,
            str_replace('/', '-', $tahunAjaran),
        ];

        if (str_contains($tahunAjaran, '/')) {
            [$tahunAwal, $tahunAkhir] = explode('/', $tahunAjaran);

            $variants[] = $tahunAwal;
            $variants[] = $tahunAkhir;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private function bulanVariants(string $bulan): array
    {
        $bulan = trim($bulan);

        return array_values(array_unique(array_filter([
            $bulan,
            strtolower($bulan),
            strtoupper($bulan),
            ucfirst(strtolower($bulan)),
        ])));
    }

    private function bulanSampai(string $sampaiBulan): array
    {
        $index = array_search($sampaiBulan, $this->bulanList);

        if ($index === false) {
            return [];
        }

        return array_slice($this->bulanList, 0, $index + 1);
    }

    private function punyaFormal(Siswa $siswa): bool
    {
        $kelas = strtolower(trim($siswa->kelas_formal ?? ''));

        return !empty($kelas) && !in_array($kelas, [
            '-',
            'non formal',
            'nonformal',
            'tidak formal',
            'tanpa formal',
            'tidak sekolah',
        ]);
    }

    private function punyaDiniyah(Siswa $siswa): bool
    {
        $kelas = strtolower(trim($siswa->kelas_diniyah ?? ''));

        return !empty($kelas) && !in_array($kelas, [
            '-',
            'non diniyah',
            'nondiniyah',
            'tidak diniyah',
            'tanpa diniyah',
            'tidak pondok',
        ]);
    }

    private function hitungNominalFormal(Siswa $siswa): int
    {
        if ((int) ($siswa->potongan_formal ?? 0) > 0) {
            return (int) $siswa->potongan_formal;
        }

        $kelas = DataKelas::where('nama_kelas', $siswa->kelas_formal)->first();

        return $kelas ? (int) $kelas->nominal_spp : 0;
    }

    private function hitungNominalDiniyah(Siswa $siswa): int
    {
        if ((int) ($siswa->potongan_diniyah ?? 0) > 0) {
            return (int) $siswa->potongan_diniyah;
        }

        $kelas = DataKelasDiniyah::where('nama_kelas', $siswa->kelas_diniyah)->first();

        return $kelas ? (int) $kelas->nominal_spp : 0;
    }

    private function tahunBayarDariBulan(string $bulan, string $tahunAjaran): string
    {
        [$tahunAwal, $tahunAkhir] = explode('/', $tahunAjaran);

        $semesterAwal = [
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        return in_array($bulan, $semesterAwal) ? $tahunAwal : $tahunAkhir;
    }

    private function tahunAjaranList(): array
    {
        return [
            '2023/2024',
            '2024/2025',
            '2025/2026',
            '2026/2027',
            '2027/2028',
        ];
    }

    private function ambilTerbayarLegacy($bayar): int
    {
        if (!$bayar) {
            return 0;
        }

        $terbayar = (int) ($bayar->terbayar ?? 0);
        $jumlahBayar = (int) ($bayar->jumlah_bayar ?? 0);

        $statusBayar = strtolower(trim($bayar->status_bayar ?? ''));
        $keterangan = strtolower(trim($bayar->keterangan ?? ''));

        if ($terbayar <= 0 && (
            $statusBayar === 'lunas' ||
            $keterangan === 'lunas' ||
            str_contains($keterangan, 'lunas')
        )) {
            return $jumlahBayar;
        }

        return $terbayar;
    }
}