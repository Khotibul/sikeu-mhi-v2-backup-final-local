<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DataKelas;
use App\Models\DataKelasDiniyah;
use App\Models\Pembayaran;
use App\Models\PembayaranDiniyah;
use App\Models\PembayaranPangkal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        $totalSantri = Siswa::count();

        $santriAktif = Siswa::where(function ($query) {
                $query->where('status_aktif', 'Aktif')
                    ->orWhereNull('status_aktif')
                    ->orWhere('status_aktif', '');
            })
            ->count();

        $totalKelasFormal = DataKelas::count();
        $totalKelasDiniyah = DataKelasDiniyah::count();

        $pemasukanFormalBulanIni = $this->totalPembayaranFormal($awalBulan, $akhirBulan);
        $pemasukanPondokBulanIni = $this->totalPembayaranDiniyah($awalBulan, $akhirBulan);
        $pemasukanLainSantriBulanIni = $this->totalPembayaranLainSantri($awalBulan, $akhirBulan);

        $totalPemasukanBulanIni = $pemasukanFormalBulanIni
            + $pemasukanPondokBulanIni
            + $pemasukanLainSantriBulanIni;

        $pemasukanFormalHariIni = $this->totalPembayaranFormal($today, $today);
        $pemasukanPondokHariIni = $this->totalPembayaranDiniyah($today, $today);
        $pemasukanLainSantriHariIni = $this->totalPembayaranLainSantri($today, $today);

        $totalPemasukanHariIni = $pemasukanFormalHariIni
            + $pemasukanPondokHariIni
            + $pemasukanLainSantriHariIni;

        $diagram = $this->buatDataDiagram([
            'Formal' => $pemasukanFormalBulanIni,
            'Pondok/Diniyah' => $pemasukanPondokBulanIni,
            'Pembayaran Lain' => $pemasukanLainSantriBulanIni,
        ]);

        $transaksiTerakhir = $this->ambilTransaksiTerakhir();

        return view('dashboard', compact(
            'totalSantri',
            'santriAktif',
            'totalKelasFormal',
            'totalKelasDiniyah',
            'pemasukanFormalBulanIni',
            'pemasukanPondokBulanIni',
            'pemasukanLainSantriBulanIni',
            'totalPemasukanBulanIni',
            'pemasukanFormalHariIni',
            'pemasukanPondokHariIni',
            'pemasukanLainSantriHariIni',
            'totalPemasukanHariIni',
            'diagram',
            'transaksiTerakhir'
        ));
    }

    private function totalPembayaranFormal(Carbon $start, Carbon $end): int
    {
        return (int) Pembayaran::whereBetween('tgl_bayar', [
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
            ])
            ->get()
            ->sum(function ($item) {
                return $this->ambilNominalLegacy($item);
            });
    }

    private function totalPembayaranDiniyah(Carbon $start, Carbon $end): int
    {
        return (int) PembayaranDiniyah::whereBetween('tgl_bayar', [
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
            ])
            ->get()
            ->sum(function ($item) {
                return $this->ambilNominalLegacy($item);
            });
    }

    private function totalPembayaranLainSantri(Carbon $start, Carbon $end): int
    {
        return (int) PembayaranPangkal::whereBetween('tgl_bayar', [
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
            ])
            ->sum('nominal_bayar');
    }

    private function ambilTransaksiTerakhir()
    {
        $formal = Pembayaran::leftJoin('siswa', 'pembayaran.id_siswa', '=', 'siswa.id_siswa')
            ->select(
                'pembayaran.id_bayar as id',
                'pembayaran.tgl_bayar',
                'pembayaran.bulan_bayar',
                'pembayaran.tahun_bayar',
                'pembayaran.jumlah_bayar',
                'pembayaran.terbayar',
                'pembayaran.status_bayar',
                'pembayaran.keterangan',
                'siswa.nama_siswa',
                DB::raw("'Biaya Pendidikan Formal' as jenis")
            )
            ->orderByDesc('pembayaran.tgl_bayar')
            ->orderByDesc('pembayaran.id_bayar')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tgl_bayar,
                    'nama' => $item->nama_siswa ?: '-',
                    'jenis' => $item->jenis,
                    'periode' => trim(($item->bulan_bayar ?: '-') . ' ' . ($item->tahun_bayar ?: '')),
                    'nominal' => $this->ambilNominalLegacy($item),
                    'badge' => 'Formal',
                ];
            });

        $pondok = PembayaranDiniyah::leftJoin('siswa', 'pembayaran_diniyah.id_siswa', '=', 'siswa.id_siswa')
            ->select(
                'pembayaran_diniyah.id_bayar_diniyah as id',
                'pembayaran_diniyah.tgl_bayar',
                'pembayaran_diniyah.bulan_bayar',
                'pembayaran_diniyah.tahun_bayar',
                'pembayaran_diniyah.jumlah_bayar',
                'pembayaran_diniyah.terbayar',
                'pembayaran_diniyah.keterangan',
                'siswa.nama_siswa',
                DB::raw("'Biaya Pendidikan Pondok/Diniyah' as jenis")
            )
            ->orderByDesc('pembayaran_diniyah.tgl_bayar')
            ->orderByDesc('pembayaran_diniyah.id_bayar_diniyah')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tgl_bayar,
                    'nama' => $item->nama_siswa ?: '-',
                    'jenis' => $item->jenis,
                    'periode' => trim(($item->bulan_bayar ?: '-') . ' ' . ($item->tahun_bayar ?: '')),
                    'nominal' => $this->ambilNominalLegacy($item),
                    'badge' => 'Pondok',
                ];
            });

        $lain = PembayaranPangkal::leftJoin('siswa', 'pembayaran_pangkal.id_siswa', '=', 'siswa.id_siswa')
            ->select(
                'pembayaran_pangkal.id_pangkal as id',
                'pembayaran_pangkal.tgl_bayar',
                'pembayaran_pangkal.jenis_tagihan',
                'pembayaran_pangkal.nominal_bayar',
                'pembayaran_pangkal.keterangan',
                'siswa.nama_siswa',
                DB::raw("'Pembayaran Lain Santri' as jenis")
            )
            ->orderByDesc('pembayaran_pangkal.tgl_bayar')
            ->orderByDesc('pembayaran_pangkal.id_pangkal')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tgl_bayar,
                    'nama' => $item->nama_siswa ?: '-',
                    'jenis' => $item->jenis,
                    'periode' => $item->jenis_tagihan ?: '-',
                    'nominal' => (int) $item->nominal_bayar,
                    'badge' => 'Lain',
                ];
            });

        return $formal
            ->merge($pondok)
            ->merge($lain)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();
    }

    private function ambilNominalLegacy($item): int
    {
        $terbayar = (int) ($item->terbayar ?? 0);
        $jumlahBayar = (int) ($item->jumlah_bayar ?? 0);

        $statusBayar = strtolower(trim($item->status_bayar ?? ''));
        $keterangan = strtolower(trim($item->keterangan ?? ''));

        if ($terbayar > 0) {
            return $terbayar;
        }

        if (
            $statusBayar === 'lunas' ||
            $keterangan === 'lunas' ||
            str_contains($keterangan, 'lunas')
        ) {
            return $jumlahBayar;
        }

        return 0;
    }

    private function buatDataDiagram(array $data): array
    {
        $total = array_sum($data);

        if ($total <= 0) {
            return [
                'total' => 0,
                'items' => [
                    [
                        'label' => 'Belum Ada Data',
                        'nominal' => 0,
                        'persen' => 100,
                        'color' => '#e5e7eb',
                    ],
                ],
                'gradient' => '#e5e7eb 0deg 360deg',
            ];
        }

        $colors = [
            'Formal' => '#12a99a',
            'Pondok/Diniyah' => '#e3456d',
            'Pembayaran Lain' => '#f59e0b',
        ];

        $items = [];
        $currentDegree = 0;
        $gradientParts = [];

        foreach ($data as $label => $nominal) {
            $persen = round(($nominal / $total) * 100, 1);
            $degree = ($nominal / $total) * 360;
            $start = $currentDegree;
            $end = $currentDegree + $degree;
            $color = $colors[$label] ?? '#94a3b8';

            if ($nominal > 0) {
                $gradientParts[] = "{$color} {$start}deg {$end}deg";
            }

            $items[] = [
                'label' => $label,
                'nominal' => $nominal,
                'persen' => $persen,
                'color' => $color,
            ];

            $currentDegree = $end;
        }

        return [
            'total' => $total,
            'items' => $items,
            'gradient' => implode(', ', $gradientParts),
        ];
    }
}