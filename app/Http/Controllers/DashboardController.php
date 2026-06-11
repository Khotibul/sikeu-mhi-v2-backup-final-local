<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DataKelas;
use App\Models\DataKelasDiniyah;
use App\Models\Pembayaran;
use App\Models\PembayaranDiniyah;
use App\Models\PembayaranPangkal;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        // Ambil Data Pengeluaran
        $totalPengeluaranBulanIni = (int) Pengeluaran::whereBetween('tgl_keluar', [
            $awalBulan->format('Y-m-d'),
            $akhirBulan->format('Y-m-d'),
        ])->sum('jumlah');

        $totalPengeluaranHariIni = (int) Pengeluaran::where('tgl_keluar', $today->format('Y-m-d'))->sum('jumlah');

        $diagram = $this->buatDataDiagram([
            'SPP Formal' => $pemasukanFormalBulanIni,
            'Syahriyah Pondok' => $pemasukanPondokBulanIni,
            'Infaq/Lainnya' => $pemasukanLainSantriBulanIni,
        ]);

        $transaksiTerakhir = $this->ambilTransaksiTerakhir();
        $prayerTimes = $this->fetchPrayerTimes();
        $dailyAgenda = $this->dailyAgenda();

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
            'totalPengeluaranBulanIni',
            'totalPengeluaranHariIni',
            'diagram',
            'transaksiTerakhir',
            'prayerTimes',
            'dailyAgenda'
        ));
    }

    /**
     * Return dashboard metrics as JSON for realtime frontend updates.
     */
    public function data()
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

        $totalPengeluaranBulanIni = (int) Pengeluaran::whereBetween('tgl_keluar', [
            $awalBulan->format('Y-m-d'),
            $akhirBulan->format('Y-m-d'),
        ])->sum('jumlah');

        $totalPengeluaranHariIni = (int) Pengeluaran::where('tgl_keluar', $today->format('Y-m-d'))->sum('jumlah');

        $diagram = $this->buatDataDiagram([
            'SPP Formal' => $pemasukanFormalBulanIni,
            'Syahriyah Pondok' => $pemasukanPondokBulanIni,
            'Infaq/Lainnya' => $pemasukanLainSantriBulanIni,
        ]);

        $transaksiTerakhir = $this->ambilTransaksiTerakhir();
        $prayerTimes = $this->fetchPrayerTimes();
        $dailyAgenda = $this->dailyAgenda();
        $saldo = $totalPemasukanBulanIni - $totalPengeluaranBulanIni;
        $saldoRatio = ($totalPemasukanBulanIni > 0)
            ? round(($saldo / $totalPemasukanBulanIni) * 100, 1)
            : 0;

        return response()->json([
            'totalSantri' => $totalSantri,
            'santriAktif' => $santriAktif,
            'totalKelasFormal' => $totalKelasFormal,
            'totalKelasDiniyah' => $totalKelasDiniyah,
            'totalKelas' => $totalKelasFormal + $totalKelasDiniyah,
            'totalPemasukanBulanIni' => $totalPemasukanBulanIni,
            'totalPemasukanHariIni' => $totalPemasukanHariIni,
            'totalPengeluaranBulanIni' => $totalPengeluaranBulanIni,
            'totalPengeluaranHariIni' => $totalPengeluaranHariIni,
            'diagram' => $diagram,
            'transaksiTerakhir' => $transaksiTerakhir,
            'prayerTimes' => $prayerTimes,
            'dailyAgenda' => $dailyAgenda,
            'saldoBersih' => $saldo,
            'saldoRatio' => $saldoRatio,
            'isPositiveSaldo' => $saldo >= 0,
        ]);
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
                DB::raw("'SPP Pendidikan Formal' as jenis")
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
                DB::raw("'Syahriyah Pondok/Diniyah' as jenis")
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
                DB::raw("'Infaq / Pembayaran Lain' as jenis")
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
                    'badge' => 'Infaq',
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

    private function fetchPrayerTimes(): array
    {
        $city = env('PRAYER_CITY', 'Bangsalsari');
        $country = env('PRAYER_COUNTRY', 'Indonesia');
        $method = env('PRAYER_METHOD', 4);

        try {
            $response = Http::timeout(6)->get('https://api.aladhan.com/v1/timingsByCity', [
                'city' => $city,
                'country' => $country,
                'method' => $method,
                'school' => 1,
            ]);

            if ($response->ok() && isset($response['data']['timings'])) {
                $timings = $response['data']['timings'];
                $dateLabel = $response['data']['date']['gregorian']['date'] ?? Carbon::today()->format('d/m/Y');

                return [
                    'source' => 'Aladhan API',
                    'city' => $city,
                    'country' => $country,
                    'date' => $dateLabel,
                    'timings' => [
                        'Fajr' => $timings['Fajr'] ?? '-',
                        'Dhuhr' => $timings['Dhuhr'] ?? '-',
                        'Asr' => $timings['Asr'] ?? '-',
                        'Maghrib' => $timings['Maghrib'] ?? '-',
                        'Isha' => $timings['Isha'] ?? '-',
                        'Sunrise' => $timings['Sunrise'] ?? '-',
                    ],
                ];
            }
        } catch (\Throwable $e) {
            // API gagal, gunakan fallback lokal sederhana.
        }

        return [
            'source' => 'Fallback Lokal',
            'city' => $city,
            'country' => $country,
            'date' => Carbon::today()->format('d/m/Y'),
            'timings' => [
                'Fajr' => '04:45',
                'Dhuhr' => '11:55',
                'Asr' => '15:10',
                'Maghrib' => '17:45',
                'Isha' => '19:05',
                'Sunrise' => '05:55',
            ],
        ];
    }

    private function dailyAgenda(): array
    {
        $weekday = Carbon::today()->dayOfWeekIso;
        $shared = [
            ['time' => '05:00', 'title' => 'Bangun & Shalat Subuh berjamaah'],
            ['time' => '06:30', 'title' => 'Kajian tauhid dan halaqah'],
            ['time' => '09:00', 'title' => 'Belajar kitab & praktik pesantren'],
            ['time' => '12:00', 'title' => 'Shalat Dzuhur & istirahat makan'],
            ['time' => '14:30', 'title' => 'Pembinaan santri & kegiatan kelas'],
            ['time' => '16:00', 'title' => 'Shalat Ashar berjamaah'],
            ['time' => '18:00', 'title' => 'Shalat Maghrib & muhasabah harian'],
            ['time' => '19:30', 'title' => 'Pengajian malam / tahfidz'],
        ];

        if ($weekday === 7) {
            return [
                ['time' => '06:00', 'title' => 'Shalat Subuh spontan dan mujahadah'],
                ['time' => '08:00', 'title' => 'Kajian umum dan evaluasi pekanan'],
                ['time' => '10:00', 'title' => 'Waktu bebas santri dan konseling'],
                ['time' => '14:00', 'title' => 'Persiapan pekan berikutnya'],
                ['time' => '16:30', 'title' => 'Shalat Ashar berjamaah'],
                ['time' => '18:00', 'title' => 'Shalat Maghrib & doa bersama'],
            ];
        }

        return $shared;
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
            'SPP Formal' => '#12a99a',
            'Syahriyah Pondok' => '#e3456d',
            'Infaq/Lainnya' => '#f59e0b',
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
