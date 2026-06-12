<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\PembayaranDiniyah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Support\AdminUnitScope;

class PembayaranSppController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $hasilSiswa = collect();

        if (!empty($search)) {
            $query = Siswa::query();

            AdminUnitScope::applyToSiswaQuery($query);

            $hasilSiswa = $query
                ->where(function ($query) use ($search) {
                    $query->where('nama_siswa', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%')
                        ->orWhere('nisn', 'like', '%' . $search . '%')
                        ->orWhere('nama_wali', 'like', '%' . $search . '%')
                        ->orWhere('nama_ibu', 'like', '%' . $search . '%')
                        ->orWhere('kelas_formal', 'like', '%' . $search . '%')
                        ->orWhere('kelas_diniyah', 'like', '%' . $search . '%')
                        ->orWhere('status_mukim', 'like', '%' . $search . '%');
                })
                ->orderBy('nama_siswa')
                ->limit(80)
                ->get();
        }

        return view('pembayaran-spp.index', compact(
            'search',
            'hasilSiswa'
        ));
    }

    public function pilihSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        if (!AdminUnitScope::bolehAksesSiswa($siswa)) {
            abort(403, 'Santri ini tidak termasuk unit admin yang sedang login.');
        }

        $tahunAjaran = $request->get('tahun_ajaran', $this->tahunAjaranAktif());

        $tahunRiwayat = $request->get('tahun_riwayat', 'semua');
        $jenisRiwayat = $request->get('jenis_riwayat', 'semua');
        $searchRiwayat = $request->get('search_riwayat');

        $tahunAjaranList = $this->tahunAjaranList();
        $tahunAjaranRiwayatList = $this->tahunAjaranRiwayatList($siswa->id_siswa, $tahunAjaranList);

        $nominalFormal = $this->ambilNominalFormal($siswa);
        $nominalPondok = $this->ambilNominalPondok($siswa);

        $tampilFormal = $this->tampilFormal($siswa) && $nominalFormal > 0;
        $tampilPondok = $this->tampilPondok($siswa) && $nominalPondok > 0;

        $bulanFormal = $tampilFormal
            ? $this->buatDataBulanFormal($siswa, $tahunAjaran, $nominalFormal)
            : collect();

        $bulanPondok = $tampilPondok
            ? $this->buatDataBulanPondok($siswa, $tahunAjaran, $nominalPondok)
            : collect();

        $riwayatFormal = collect();
        $riwayatPondok = collect();

        if ($jenisRiwayat === 'semua' || $jenisRiwayat === 'formal') {
            $riwayatFormal = $this->queryRiwayatFormal($siswa->id_siswa, $tahunRiwayat, $searchRiwayat)
                ->orderByDesc('tgl_bayar')
                ->orderByDesc('id_bayar')
                ->get();
        }

        if ($tampilPondok && ($jenisRiwayat === 'semua' || $jenisRiwayat === 'pondok')) {
            $riwayatPondok = $this->queryRiwayatPondok($siswa->id_siswa, $tahunRiwayat, $searchRiwayat)
                ->orderByDesc('tgl_bayar')
                ->orderByDesc('id_bayar_diniyah')
                ->get();
        }

        return view('pembayaran-spp.pilih-siswa', compact(
            'siswa',
            'tahunAjaran',
            'tahunRiwayat',
            'jenisRiwayat',
            'searchRiwayat',
            'tahunAjaranList',
            'tahunAjaranRiwayatList',
            'nominalFormal',
            'nominalPondok',
            'tampilFormal',
            'tampilPondok',
            'bulanFormal',
            'bulanPondok',
            'riwayatFormal',
            'riwayatPondok'
        ));
    }

    public function bayarFormal(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'tahun_ajaran' => 'required|string',
            'tgl_bayar' => 'required|date',
            'bulan' => 'required|array|min:1',
            'nominal' => 'required|array',
            'tahun_bayar' => 'required|array',
        ]);

        $nominalFormal = $this->ambilNominalFormal($siswa);

        if ($nominalFormal <= 0) {
            return back()->with('error', 'Nominal formal belum diset. Cek data kelas formal atau nominal santri.');
        }

        $jumlahTersimpan = 0;

        foreach ($request->bulan as $bulan) {
            $nominalBayar = (int) ($request->nominal[$bulan] ?? 0);
            $tahunBayar = $request->tahun_bayar[$bulan] ?? null;

            if ($nominalBayar <= 0 || empty($tahunBayar)) {
                continue;
            }

            $sudahTerbayar = $this->totalTerbayarFormal(
                $siswa->id_siswa,
                $bulan,
                $tahunBayar,
                $request->tahun_ajaran
            );

            $sisa = max($nominalFormal - $sudahTerbayar, 0);

            if ($sisa <= 0) {
                continue;
            }

            if ($nominalBayar > $sisa) {
                return back()->with('error', 'Nominal pembayaran formal bulan ' . $bulan . ' melebihi sisa tagihan. Sisa Rp ' . number_format($sisa, 0, ',', '.'));
            }

            $totalSetelahBayar = $sudahTerbayar + $nominalBayar;
            $status = $totalSetelahBayar >= $nominalFormal ? 'LUNAS' : 'CICIL';

            $this->insertPembayaranFormal([
                'id_siswa' => $siswa->id_siswa,
                'tgl_bayar' => $request->tgl_bayar,
                'bulan_bayar' => $bulan,
                'tahun_bayar' => $tahunBayar,
                'semester' => $this->semesterBulan($bulan),
                'jumlah_bayar' => $nominalFormal,
                'terbayar' => $nominalBayar,
                'status_bayar' => $status,
                'keterangan' => $status,
                'id_admin' => session('admin_id') ?? 0,
                'tahun_ajaran' => $request->tahun_ajaran,
            ]);

            $jumlahTersimpan++;
        }

        if ($jumlahTersimpan <= 0) {
            return back()->with('error', 'Tidak ada bulan formal yang berhasil dibayar. Pastikan bulan sudah dicentang dan nominal lebih dari 0.');
        }

        return redirect()
            ->route('pembayaran-spp.siswa', [
                'id' => $siswa->id_siswa,
                'tahun_ajaran' => $request->tahun_ajaran,
                'tahun_riwayat' => $request->tahun_ajaran,
                'jenis_riwayat' => 'formal',
            ])
            ->with('success', $jumlahTersimpan . ' pembayaran formal berhasil disimpan.');
    }

    public function bayarPondok(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'tahun_ajaran' => 'required|string',
            'tgl_bayar' => 'required|date',
            'bulan' => 'required|array|min:1',
            'nominal' => 'required|array',
            'tahun_bayar' => 'required|array',
        ]);

        $nominalPondok = $this->ambilNominalPondok($siswa);

        if ($nominalPondok <= 0) {
            return back()->with('error', 'Nominal pondok/diniyah belum diset. Cek data kelas diniyah atau nominal SPP khusus santri.');
        }

        $jumlahTersimpan = 0;

        foreach ($request->bulan as $bulan) {
            $nominalBayar = (int) ($request->nominal[$bulan] ?? 0);
            $tahunBayar = $request->tahun_bayar[$bulan] ?? null;

            if ($nominalBayar <= 0 || empty($tahunBayar)) {
                continue;
            }

            $sudahTerbayar = $this->totalTerbayarPondok(
                $siswa->id_siswa,
                $bulan,
                $tahunBayar,
                $request->tahun_ajaran
            );

            $sisa = max($nominalPondok - $sudahTerbayar, 0);

            if ($sisa <= 0) {
                continue;
            }

            if ($nominalBayar > $sisa) {
                return back()->with('error', 'Nominal pembayaran pondok/diniyah bulan ' . $bulan . ' melebihi sisa tagihan. Sisa Rp ' . number_format($sisa, 0, ',', '.'));
            }

            $totalSetelahBayar = $sudahTerbayar + $nominalBayar;
            $status = $totalSetelahBayar >= $nominalPondok ? 'LUNAS' : 'CICIL';

            $this->insertPembayaranPondok([
                'id_admin' => session('admin_id') ?? 0,
                'id_siswa' => $siswa->id_siswa,
                'tgl_bayar' => $request->tgl_bayar,
                'bulan_bayar' => $bulan,
                'tahun_bayar' => $tahunBayar,
                'jumlah_bayar' => $nominalPondok,
                'terbayar' => $nominalBayar,
                'keterangan' => $status,
                'tahun_ajaran' => $request->tahun_ajaran,
            ]);

            $jumlahTersimpan++;
        }

        if ($jumlahTersimpan <= 0) {
            return back()->with('error', 'Tidak ada bulan pondok/diniyah yang berhasil dibayar. Pastikan bulan sudah dicentang dan nominal lebih dari 0.');
        }

        return redirect()
            ->route('pembayaran-spp.siswa', [
                'id' => $siswa->id_siswa,
                'tahun_ajaran' => $request->tahun_ajaran,
                'tahun_riwayat' => $request->tahun_ajaran,
                'jenis_riwayat' => 'pondok',
            ])
            ->with('success', $jumlahTersimpan . ' pembayaran pondok/diniyah berhasil disimpan.');
    }

    public function hapusFormal($id)
    {
        $pembayaran = Pembayaran::where('id_bayar', $id)->firstOrFail();

        $idSiswa = $pembayaran->id_siswa;
        $tahunAjaran = $pembayaran->tahun_ajaran ?: $this->tahunAjaranAktif();

        $pembayaran->delete();

        return redirect()
            ->route('pembayaran-spp.siswa', [
                'id' => $idSiswa,
                'tahun_ajaran' => $tahunAjaran,
                'tahun_riwayat' => $tahunAjaran,
                'jenis_riwayat' => 'formal',
            ])
            ->with('success', 'Riwayat pembayaran formal berhasil dihapus.');
    }

    public function hapusPondok($id)
    {
        $pembayaran = PembayaranDiniyah::where('id_bayar_diniyah', $id)->firstOrFail();

        $idSiswa = $pembayaran->id_siswa;
        $tahunAjaran = $pembayaran->tahun_ajaran ?: $this->tahunAjaranAktif();

        $pembayaran->delete();

        return redirect()
            ->route('pembayaran-spp.siswa', [
                'id' => $idSiswa,
                'tahun_ajaran' => $tahunAjaran,
                'tahun_riwayat' => $tahunAjaran,
                'jenis_riwayat' => 'pondok',
            ])
            ->with('success', 'Riwayat pembayaran pondok/diniyah berhasil dihapus.');
    }

    public function kwitansi(Request $request)
    {
        $jenis = $request->get('jenis');

        $ids = collect(explode(',', $request->get('ids')))
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            abort(404);
        }

        if ($jenis === 'formal') {
            $items = Pembayaran::leftJoin('siswa', 'pembayaran.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah',
                    'siswa.status_mukim'
                )
                ->whereIn('pembayaran.id_bayar', $ids)
                ->orderBy('pembayaran.tahun_bayar')
                ->orderBy('pembayaran.id_bayar')
                ->get();

            if ($items->isEmpty()) {
                abort(404);
            }

            $siswa = $items->first();
            $total = $items->sum(fn($item) => $this->ambilNominalLegacy($item));
            $nomorKwitansi = 'KWF-' . now()->format('Ymd') . '-' . str_pad($ids->first(), 5, '0', STR_PAD_LEFT);
            $terbilang = ucwords($this->terbilang($total)) . ' Rupiah';
            $judul = 'Biaya Pendidikan Formal';

            return view('pembayaran-spp.kwitansi', compact(
                'jenis',
                'items',
                'siswa',
                'total',
                'nomorKwitansi',
                'terbilang',
                'judul'
            ));
        }

        if ($jenis === 'pondok') {
            $items = PembayaranDiniyah::leftJoin('siswa', 'pembayaran_diniyah.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran_diniyah.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah',
                    'siswa.status_mukim'
                )
                ->whereIn('pembayaran_diniyah.id_bayar_diniyah', $ids)
                ->orderBy('pembayaran_diniyah.tahun_bayar')
                ->orderBy('pembayaran_diniyah.id_bayar_diniyah')
                ->get();

            if ($items->isEmpty()) {
                abort(404);
            }

            $siswa = $items->first();
            $total = $items->sum(fn($item) => $this->ambilNominalLegacy($item));
            $nomorKwitansi = 'KWP-' . now()->format('Ymd') . '-' . str_pad($ids->first(), 5, '0', STR_PAD_LEFT);
            $terbilang = ucwords($this->terbilang($total)) . ' Rupiah';
            $judul = 'Biaya Pendidikan Pondok/Diniyah';

            return view('pembayaran-spp.kwitansi', compact(
                'jenis',
                'items',
                'siswa',
                'total',
                'nomorKwitansi',
                'terbilang',
                'judul'
            ));
        }

        abort(404);
    }

    public function kwitansiFormal($id)
    {
        return redirect()->route('pembayaran-spp.kwitansi', [
            'jenis' => 'formal',
            'ids' => $id,
        ]);
    }

    public function kwitansiPondok($id)
    {
        return redirect()->route('pembayaran-spp.kwitansi', [
            'jenis' => 'pondok',
            'ids' => $id,
        ]);
    }
    public function kwitansiGabungan(Request $request)
    {
        $formalIds = $request->formal_ids ?? [];
        $pondokIds = $request->pondok_ids ?? [];

        if (empty($formalIds) && empty($pondokIds)) {
            return back()->with('error', 'Pilih minimal satu transaksi untuk cetak kwitansi gabungan.');
        }

        $formal = collect();

        if (!empty($formalIds)) {
            $formal = Pembayaran::leftJoin('siswa', 'pembayaran.id_siswa', '=', 'siswa.id_siswa')
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
                ->whereIn('pembayaran.id_bayar', $formalIds)
                ->get();
        }

        $pondok = collect();

        if (!empty($pondokIds)) {
            $pondok = PembayaranDiniyah::leftJoin('siswa', 'pembayaran_diniyah.id_siswa', '=', 'siswa.id_siswa')
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
                ->whereIn('pembayaran_diniyah.id_bayar_diniyah', $pondokIds)
                ->get();
        }

        $items = collect();

        foreach ($formal as $item) {
            $statusBayar = $this->statusPembayaranItem($item, 'formal', $item);

            $items->push([
                'jenis' => 'Biaya Pendidikan Formal',
                'nama_siswa' => $item->nama_siswa,
                'nis' => $item->nis,
                'nisn' => $item->nisn,
                'kelas_formal' => $item->kelas_formal,
                'kelas_diniyah' => $item->kelas_diniyah,
                'status_mukim' => $item->status_mukim,
                'bulan_bayar' => $item->bulan_bayar,
                'tahun_bayar' => $item->tahun_bayar,
                'tahun_ajaran' => $item->tahun_ajaran,
                'tgl_bayar' => $item->tgl_bayar,
                'nominal' => $this->ambilNominalLegacy($item),
                'status' => $statusBayar,
                'status_bayar' => $statusBayar,
                'keterangan' => $item->keterangan ?: 'Pembayaran SPP Formal',
            ]);
        }

        foreach ($pondok as $item) {
            $statusBayar = $this->statusPembayaranItem($item, 'diniyah', $item);

            $items->push([
                'jenis' => 'Biaya Pendidikan Pondok/Diniyah',
                'nama_siswa' => $item->nama_siswa,
                'nis' => $item->nis,
                'nisn' => $item->nisn,
                'kelas_formal' => $item->kelas_formal,
                'kelas_diniyah' => $item->kelas_diniyah,
                'status_mukim' => $item->status_mukim,
                'bulan_bayar' => $item->bulan_bayar,
                'tahun_bayar' => $item->tahun_bayar,
                'tahun_ajaran' => $item->tahun_ajaran,
                'tgl_bayar' => $item->tgl_bayar,
                'nominal' => $this->ambilNominalLegacy($item),
                'status' => $statusBayar,
                'status_bayar' => $statusBayar,
                'keterangan' => $item->keterangan ?: 'Pembayaran Pondok/Diniyah',
            ]);
        }

        if ($items->isEmpty()) {
            return back()->with('error', 'Transaksi yang dipilih tidak ditemukan.');
        }

        $items = $items
            ->sortBy([
                ['tgl_bayar', 'asc'],
                ['tahun_bayar', 'asc'],
                ['bulan_bayar', 'asc'],
            ])
            ->values();

        $siswaData = $items->first();

        $siswa = (object) [
            'nama_siswa' => $siswaData['nama_siswa'] ?? '-',
            'nis' => $siswaData['nis'] ?? '-',
            'nisn' => $siswaData['nisn'] ?? '-',
            'kelas_formal' => $siswaData['kelas_formal'] ?? '-',
            'kelas_diniyah' => $siswaData['kelas_diniyah'] ?? '-',
            'status_mukim' => $siswaData['status_mukim'] ?? '-',
        ];

        $total = $items->sum('nominal');
        $nomorKwitansi = 'KWG-' . now()->format('Ymd-His');
        $terbilang = ucwords($this->terbilang($total)) . ' Rupiah';

        return view('pembayaran-spp.kwitansi-gabungan', compact(
            'items',
            'siswa',
            'total',
            'nomorKwitansi',
            'terbilang'
        ));
    }

    private function queryRiwayatFormal($idSiswa, string $tahunRiwayat, ?string $searchRiwayat)
    {
        $query = Pembayaran::where('id_siswa', $idSiswa);

        if ($tahunRiwayat !== 'semua') {
            $tahun = $this->pecahTahunAjaran($tahunRiwayat);

            $query->where(function ($q) use ($tahunRiwayat, $tahun) {
                $q->where('tahun_ajaran', $tahunRiwayat)
                    ->orWhere(function ($legacyAwal) use ($tahun) {
                        $legacyAwal->where('tahun_ajaran', $tahun['awal'])
                            ->where('tahun_bayar', $tahun['awal']);
                    })
                    ->orWhere(function ($legacyAkhir) use ($tahun) {
                        $legacyAkhir->where('tahun_ajaran', $tahun['akhir'])
                            ->where('tahun_bayar', $tahun['akhir']);
                    })
                    ->orWhere(function ($legacyKosong) use ($tahun) {
                        $legacyKosong->where(function ($empty) {
                            $empty->whereNull('tahun_ajaran')
                                ->orWhere('tahun_ajaran', '');
                        })
                            ->whereIn('tahun_bayar', array_filter([
                                $tahun['awal'],
                                $tahun['akhir'],
                            ]));
                    });
            });
        }

        if (!empty($searchRiwayat)) {
            $query->where(function ($q) use ($searchRiwayat) {
                $q->where('bulan_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('tahun_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('tahun_ajaran', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('keterangan', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('status_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('jumlah_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('terbayar', 'like', '%' . $searchRiwayat . '%');
            });
        }

        return $query;
    }

    private function queryRiwayatPondok($idSiswa, string $tahunRiwayat, ?string $searchRiwayat)
    {
        $query = PembayaranDiniyah::where('id_siswa', $idSiswa);

        if ($tahunRiwayat !== 'semua') {
            $tahun = $this->pecahTahunAjaran($tahunRiwayat);

            $query->where(function ($q) use ($tahunRiwayat, $tahun) {
                $q->where('tahun_ajaran', $tahunRiwayat)
                    ->orWhere(function ($legacyAwal) use ($tahun) {
                        $legacyAwal->where('tahun_ajaran', $tahun['awal'])
                            ->where('tahun_bayar', $tahun['awal']);
                    })
                    ->orWhere(function ($legacyAkhir) use ($tahun) {
                        $legacyAkhir->where('tahun_ajaran', $tahun['akhir'])
                            ->where('tahun_bayar', $tahun['akhir']);
                    })
                    ->orWhere(function ($legacyKosong) use ($tahun) {
                        $legacyKosong->where(function ($empty) {
                            $empty->whereNull('tahun_ajaran')
                                ->orWhere('tahun_ajaran', '');
                        })
                            ->whereIn('tahun_bayar', array_filter([
                                $tahun['awal'],
                                $tahun['akhir'],
                            ]));
                    });
            });
        }

        if (!empty($searchRiwayat)) {
            $query->where(function ($q) use ($searchRiwayat) {
                $q->where('bulan_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('tahun_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('tahun_ajaran', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('keterangan', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('jumlah_bayar', 'like', '%' . $searchRiwayat . '%')
                    ->orWhere('terbayar', 'like', '%' . $searchRiwayat . '%');
            });
        }

        return $query;
    }

    private function buatDataBulanFormal($siswa, string $tahunAjaran, int $nominal): array
    {
        return collect($this->bulanAjaran($tahunAjaran))
            ->map(function ($item) use ($siswa, $tahunAjaran, $nominal) {
                $terbayar = $this->totalTerbayarFormal(
                    $siswa->id_siswa,
                    $item['bulan'],
                    $item['tahun'],
                    $tahunAjaran
                );

                $sisa = max($nominal - $terbayar, 0);

                return [
                    'bulan' => $item['bulan'],
                    'tahun_bayar' => $item['tahun'],
                    'tagihan' => $nominal,
                    'terbayar' => $terbayar,
                    'sisa' => $sisa,
                    'status' => $this->statusTagihan($nominal, $terbayar),
                ];
            })
            ->toArray();
    }

    private function buatDataBulanPondok($siswa, string $tahunAjaran, int $nominal): array
    {
        return collect($this->bulanAjaran($tahunAjaran))
            ->map(function ($item) use ($siswa, $tahunAjaran, $nominal) {
                $terbayar = $this->totalTerbayarPondok(
                    $siswa->id_siswa,
                    $item['bulan'],
                    $item['tahun'],
                    $tahunAjaran
                );

                $sisa = max($nominal - $terbayar, 0);

                return [
                    'bulan' => $item['bulan'],
                    'tahun_bayar' => $item['tahun'],
                    'tagihan' => $nominal,
                    'terbayar' => $terbayar,
                    'sisa' => $sisa,
                    'status' => $this->statusTagihan($nominal, $terbayar),
                ];
            })
            ->toArray();
    }

    private function totalTerbayarFormal($idSiswa, $bulan, $tahunBayar, $tahunAjaran): int
    {
        // NOTE:
        // Jangan pakai pilihSatuTransaksiBulanan di sini.
        // Karena halaman ini menampilkan sisa per-bulan (akumulasi cicilan).
        // Jika hanya 1 transaksi yang dihitung, status di UI bisa tidak update.
        $query = Pembayaran::where('id_siswa', $idSiswa)
            ->where('bulan_bayar', $bulan)
            ->where('tahun_bayar', $tahunBayar);

        $this->filterTahunAjaranFleksibel($query, 'pembayaran', $tahunAjaran);

        $items = $query->get();

        return (int) $items->sum(fn($item) => $this->ambilNominalLegacy($item));
    }


    private function totalTerbayarPondok($idSiswa, $bulan, $tahunBayar, $tahunAjaran): int
    {
        // NOTE:
        // Jangan pakai pilihSatuTransaksiBulanan di sini.
        // Karena halaman ini menampilkan sisa per-bulan (akumulasi cicilan).
        // Jika hanya 1 transaksi yang dihitung, status di UI bisa tidak update.
        $query = PembayaranDiniyah::where('id_siswa', $idSiswa)
            ->where('bulan_bayar', $bulan)
            ->where('tahun_bayar', $tahunBayar);

        $this->filterTahunAjaranFleksibel($query, 'pembayaran_diniyah', $tahunAjaran);

        $items = $query->get();

        return (int) $items->sum(fn($item) => $this->ambilNominalLegacy($item));
    }


    private function filterTahunAjaranFleksibel($query, string $table, string $tahunAjaran): void
    {
        $tahun = $this->pecahTahunAjaran($tahunAjaran);

        $query->where(function ($q) use ($table, $tahunAjaran, $tahun) {
            if (Schema::hasColumn($table, 'tahun_ajaran')) {
                $q->where('tahun_ajaran', $tahunAjaran)
                    ->orWhere('tahun_ajaran', $tahun['awal'])
                    ->orWhere('tahun_ajaran', $tahun['akhir'])
                    ->orWhereNull('tahun_ajaran')
                    ->orWhere('tahun_ajaran', '');
            }
        });
    }

    private function pecahTahunAjaran(string $tahunAjaran): array
    {
        $parts = explode('/', $tahunAjaran);

        return [
            'awal' => $parts[0] ?? null,
            'akhir' => $parts[1] ?? null,
        ];
    }
    private function statusTagihan(int $tagihan, int $terbayar): string
    {
        if ($tagihan <= 0 || $terbayar <= 0) {
            return 'BELUM';
        }

        if ($terbayar >= $tagihan) {
            return 'LUNAS';
        }

        return 'CICILAN';
    }

    private function tahunAjaranAktif(): string
    {
        $tahun = (int) date('Y');
        $bulan = (int) date('n');

        if ($bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        }

        return ($tahun - 1) . '/' . $tahun;
    }

    private function tahunAjaranList(): array
    {
        $aktif = $this->tahunAjaranAktif();
        $tahunAwal = (int) substr($aktif, 0, 4);

        return [
            ($tahunAwal - 3) . '/' . ($tahunAwal - 2),
            ($tahunAwal - 2) . '/' . ($tahunAwal - 1),
            ($tahunAwal - 1) . '/' . $tahunAwal,
            $tahunAwal . '/' . ($tahunAwal + 1),
            ($tahunAwal + 1) . '/' . ($tahunAwal + 2),
        ];
    }

    private function tahunAjaranRiwayatList($idSiswa, array $defaultList): array
    {
        $formal = collect();
        $pondok = collect();

        if (Schema::hasColumn('pembayaran', 'tahun_ajaran')) {
            $formal = Pembayaran::where('id_siswa', $idSiswa)
                ->whereNotNull('tahun_ajaran')
                ->where('tahun_ajaran', '!=', '')
                ->distinct()
                ->pluck('tahun_ajaran');
        }

        if (Schema::hasColumn('pembayaran_diniyah', 'tahun_ajaran')) {
            $pondok = PembayaranDiniyah::where('id_siswa', $idSiswa)
                ->whereNotNull('tahun_ajaran')
                ->where('tahun_ajaran', '!=', '')
                ->distinct()
                ->pluck('tahun_ajaran');
        }

        return collect($defaultList)
            ->merge($formal)
            ->merge($pondok)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();
    }

    private function bulanAjaran(string $tahunAjaran): array
    {
        [$tahunAwal, $tahunAkhir] = explode('/', $tahunAjaran);

        return [
            ['bulan' => 'Juli', 'tahun' => $tahunAwal],
            ['bulan' => 'Agustus', 'tahun' => $tahunAwal],
            ['bulan' => 'September', 'tahun' => $tahunAwal],
            ['bulan' => 'Oktober', 'tahun' => $tahunAwal],
            ['bulan' => 'November', 'tahun' => $tahunAwal],
            ['bulan' => 'Desember', 'tahun' => $tahunAwal],
            ['bulan' => 'Januari', 'tahun' => $tahunAkhir],
            ['bulan' => 'Februari', 'tahun' => $tahunAkhir],
            ['bulan' => 'Maret', 'tahun' => $tahunAkhir],
            ['bulan' => 'April', 'tahun' => $tahunAkhir],
            ['bulan' => 'Mei', 'tahun' => $tahunAkhir],
            ['bulan' => 'Juni', 'tahun' => $tahunAkhir],
        ];
    }

    private function semesterBulan(string $bulan): string
    {
        return in_array($bulan, [
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ]) ? 'Ganjil' : 'Genap';
    }

    private function tampilFormal($siswa): bool
    {
        $kelas = strtolower(trim($siswa->kelas_formal ?? ''));

        return !($kelas === '' || $kelas === '-' || str_contains($kelas, 'non formal'));
    }

    private function tampilPondok($siswa): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Santri tanpa diniyah/pondok tidak boleh menampilkan tabel pondok
        |--------------------------------------------------------------------------
        | Kasus di data lama biasanya kelas_diniyah bernilai "-", kosong, atau
        | "tidak diniyah". Jika begitu, tabel Pondok/Diniyah disembunyikan.
        |
        | Selain itu, di pilihSiswa() tabel Pondok/Diniyah juga akan disembunyikan
        | jika nominal pondok = 0, supaya tidak muncul tagihan kosong.
        */
        $kelas = strtolower(trim((string) ($siswa->kelas_diniyah ?? '')));

        $nilaiKosong = [
            '',
            '-',
            '0',
            'null',
            'none',
            'tidak',
            'tidak diniyah',
            'non diniyah',
            'non-diniyah',
            'tanpa diniyah',
            'tidak ada',
        ];

        if (in_array($kelas, $nilaiKosong, true)) {
            return false;
        }

        return true;
    }

    private function ambilNominalFormal($siswa): int
    {
        /*
        |--------------------------------------------------------------------------
        | Nominal SPP Formal Khusus
        |--------------------------------------------------------------------------
        | Kolom potongan_formal sekarang dipakai sebagai NOMINAL SPP KHUSUS
        | per santri, bukan pengurang.
        |
        | Contoh:
        | potongan_formal = 120000
        | Maka tagihan formal santri tersebut setiap bulan = Rp 120.000.
        |
        | Jika kosong / 0, sistem memakai nominal default dari data kelas.
        */
        foreach (
            [
                'potongan_formal',
                'nominal_spp_formal',
                'spp_formal',
                'biaya_formal',
                'potongan_spp_formal',
            ] as $kolom
        ) {
            if (isset($siswa->{$kolom}) && (int) $siswa->{$kolom} > 0) {
                return (int) $siswa->{$kolom};
            }
        }

        return $this->ambilNominalKelas(
            'data_kelas',
            $siswa->kelas_formal ?? '',
            ['nama_kelas', 'kelas', 'kelas_formal', 'nama'],
            ['nominal_spp', 'nominal', 'spp', 'biaya', 'biaya_spp']
        );
    }

    private function ambilNominalPondok($siswa): int
    {
        /*
        |--------------------------------------------------------------------------
        | Nominal SPP Pondok/Diniyah Khusus
        |--------------------------------------------------------------------------
        | Kolom potongan_diniyah sekarang dipakai sebagai NOMINAL SPP KHUSUS
        | per santri, bukan pengurang.
        |
        | Jika potongan_diniyah > 0, sistem memakai nominal itu sebagai
        | tagihan pondok/diniyah bulanan. Jika 0, memakai nominal default kelas.
        */
        foreach (
            [
                'potongan_diniyah',
                'potongan_spp_diniyah',
                'nominal_spp_diniyah',
                'spp_diniyah',
                'biaya_diniyah',
                'biaya_pondok',
            ] as $kolom
        ) {
            if (isset($siswa->{$kolom}) && (int) $siswa->{$kolom} > 0) {
                return (int) $siswa->{$kolom};
            }
        }

        return $this->ambilNominalKelas(
            'data_kelas_diniyah',
            $siswa->kelas_diniyah ?? '',
            ['nama_kelas_diniyah', 'kelas_diniyah', 'nama_kelas', 'kelas', 'nama'],
            ['nominal_spp', 'nominal', 'spp', 'biaya', 'biaya_diniyah', 'nominal_diniyah']
        );
    }

    private function ambilNominalKelas(string $table, string $kelas, array $kolomNama, array $kolomNominal): int
    {
        if (empty($kelas) || !Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);

        $query->where(function ($q) use ($table, $kelas, $kolomNama) {
            foreach ($kolomNama as $kolom) {
                if (Schema::hasColumn($table, $kolom)) {
                    $q->orWhere($kolom, $kelas);
                }
            }
        });

        $row = $query->first();

        if (!$row) {
            return 0;
        }

        foreach ($kolomNominal as $kolom) {
            if (isset($row->{$kolom}) && (int) $row->{$kolom} > 0) {
                return (int) $row->{$kolom};
            }
        }

        return 0;
    }

    private function ambilNominalLegacy($item): int
    {
        $terbayar = (int) ($item->terbayar ?? 0);
        $jumlahBayar = (int) ($item->jumlah_bayar ?? 0);

        if ($terbayar > 0) {
            return $terbayar;
        }

        if ($jumlahBayar > 0) {
            return $jumlahBayar;
        }

        return 0;
    }

    private function insertPembayaranFormal(array $data): int
    {
        return $this->insertFlexible('pembayaran', $data);
    }

    private function insertPembayaranPondok(array $data): int
    {
        return $this->insertFlexible('pembayaran_diniyah', $data);
    }

    private function insertFlexible(string $table, array $data): int
    {
        $columns = Schema::getColumnListing($table);

        $filtered = collect($data)
            ->only($columns)
            ->toArray();

        return DB::table($table)->insertGetId($filtered);
    }


    private function normalisasiStatusPembayaran(?string $status): string
    {
        $status = strtoupper(trim((string) $status));

        if (in_array($status, ['LUNAS', 'PELUNASAN'], true)) {
            return 'LUNAS';
        }

        if (in_array($status, ['CICIL', 'CICILAN', 'NYICIL', 'ANGSURAN'], true)) {
            return 'CICILAN';
        }

        return $status === '' ? 'LUNAS' : $status;
    }

    private function statusPembayaran(int $tagihan, int $totalTerbayar): string
    {
        if ($tagihan <= 0) {
            return 'LUNAS';
        }

        return $totalTerbayar >= $tagihan ? 'LUNAS' : 'CICILAN';
    }

    private function totalTerbayarJenisBulan($idSiswa, string $jenis, string $bulan, string $tahun, ?string $tahunAjaran = null): int
    {
        $table = $this->tabelPembayaranByJenis($jenis);

        if (!Schema::hasTable($table)) {
            return 0;
        }

        $columns = Schema::getColumnListing($table);

        $query = DB::table($table);

        if (in_array('id_siswa', $columns, true)) {
            $query->where('id_siswa', $idSiswa);
        }

        if (in_array('bulan_bayar', $columns, true)) {
            $query->where('bulan_bayar', $bulan);
        }

        if (in_array('tahun_bayar', $columns, true)) {
            $query->where('tahun_bayar', $tahun);
        }

        if ($tahunAjaran && in_array('tahun_ajaran', $columns, true)) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        return (int) $query->get()->sum(function ($row) {
            return $this->ambilNominalLegacy($row);
        });
    }

    private function semesterDariBulan(?string $bulan): string
    {
        $bulan = strtolower(trim((string) $bulan));

        $semester1 = [
            'juli',
            'agustus',
            'september',
            'oktober',
            'november',
            'desember',
        ];

        return in_array($bulan, $semester1, true) ? '1' : '2';
    }

    private function statusPembayaranItem($item, string $jenis, $siswa = null): string
    {
        $statusDb = $item->status_bayar ?? $item->status ?? null;

        if ($statusDb) {
            return $this->normalisasiStatusPembayaran($statusDb);
        }

        $keterangan = strtolower((string) ($item->keterangan ?? ''));

        if (str_contains($keterangan, 'cicil') || str_contains($keterangan, 'angsuran') || str_contains($keterangan, 'nyicil')) {
            return 'CICILAN';
        }

        if (str_contains($keterangan, 'lunas') || str_contains($keterangan, 'pelunasan')) {
            return 'LUNAS';
        }

        if ($siswa) {
            $tagihan = $jenis === 'formal'
                ? (int) $this->ambilNominalFormal($siswa)
                : (int) $this->ambilNominalPondok($siswa);

            $nominal = $this->ambilNominalLegacy($item);

            return $this->statusPembayaran($tagihan, $nominal);
        }

        return 'LUNAS';
    }

    private function labelStatusPembayaran(string $status): string
    {
        return $this->normalisasiStatusPembayaran($status) === 'LUNAS' ? 'LUNAS' : 'CICILAN';
    }


    private function penyebut($nilai)
    {
        $nilai = abs((int) $nilai);

        $huruf = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($nilai < 12) {
            return ' ' . $huruf[$nilai];
        }

        if ($nilai < 20) {
            return $this->penyebut($nilai - 10) . ' belas';
        }

        if ($nilai < 100) {
            return $this->penyebut((int) ($nilai / 10)) . ' puluh' . $this->penyebut($nilai % 10);
        }

        if ($nilai < 200) {
            return ' seratus' . $this->penyebut($nilai - 100);
        }

        if ($nilai < 1000) {
            return $this->penyebut((int) ($nilai / 100)) . ' ratus' . $this->penyebut($nilai % 100);
        }

        if ($nilai < 2000) {
            return ' seribu' . $this->penyebut($nilai - 1000);
        }

        if ($nilai < 1000000) {
            return $this->penyebut((int) ($nilai / 1000)) . ' ribu' . $this->penyebut($nilai % 1000);
        }

        if ($nilai < 1000000000) {
            return $this->penyebut((int) ($nilai / 1000000)) . ' juta' . $this->penyebut($nilai % 1000000);
        }

        if ($nilai < 1000000000000) {
            return $this->penyebut((int) ($nilai / 1000000000)) . ' miliar' . $this->penyebut($nilai % 1000000000);
        }

        return '';
    }

    private function terbilang($nilai)
    {
        return trim($this->penyebut($nilai));
    }
    public function cetakGabunganTanggal(Request $request, $id)
    {
        $tanggal = $request->query('tanggal');

        if (!$tanggal) {
            return back()->with('error', 'Pilih tanggal pembayaran terlebih dahulu.');
        }

        try {
            $tanggalNormal = Carbon::parse($tanggal)->toDateString();
        } catch (\Throwable $e) {
            return back()->with('error', 'Format tanggal pembayaran tidak valid.');
        }

        $siswa = Siswa::findOrFail($id);

        if (!AdminUnitScope::bolehAksesSiswa($siswa)) {
            abort(403, 'Santri ini tidak termasuk unit admin yang sedang login.');
        }

        $items = collect();

        if (Schema::hasTable('pembayaran')) {
            $formal = DB::table('pembayaran')
                ->where('id_siswa', $id)
                ->whereDate('tgl_bayar', $tanggalNormal)
                ->get();

            foreach ($formal as $item) {
                $statusBayar = $this->statusPembayaranItem($item, 'formal', $siswa);

                $items->push((object) [
                    'jenis_pembayaran' => 'Formal',
                    'keterangan' => $item->keterangan ?: 'Pembayaran SPP Formal',
                    'bulan_bayar' => $item->bulan_bayar ?? '-',
                    'tahun_bayar' => $item->tahun_bayar ?? '-',
                    'tahun_ajaran' => $item->tahun_ajaran ?? null,
                    'tgl_bayar' => $item->tgl_bayar ?? $tanggalNormal,
                    'jumlah_bayar' => $this->ambilNominalLegacy($item),
                    'status_bayar' => $statusBayar,
                    'status' => $statusBayar,
                ]);
            }
        }

        if (Schema::hasTable('pembayaran_diniyah') && $this->tampilPondok($siswa)) {
            $pondok = DB::table('pembayaran_diniyah')
                ->where('id_siswa', $id)
                ->whereDate('tgl_bayar', $tanggalNormal)
                ->get();

            foreach ($pondok as $item) {
                $statusBayar = $this->statusPembayaranItem($item, 'diniyah', $siswa);

                $items->push((object) [
                    'jenis_pembayaran' => 'Pondok/Diniyah',
                    'keterangan' => $item->keterangan ?: 'Pembayaran Pondok/Diniyah',
                    'bulan_bayar' => $item->bulan_bayar ?? '-',
                    'tahun_bayar' => $item->tahun_bayar ?? '-',
                    'tahun_ajaran' => $item->tahun_ajaran ?? null,
                    'tgl_bayar' => $item->tgl_bayar ?? $tanggalNormal,
                    'jumlah_bayar' => $this->ambilNominalLegacy($item),
                    'status_bayar' => $statusBayar,
                    'status' => $statusBayar,
                ]);
            }
        }

        $items = $items
            ->filter(function ($item) {
                return (int) ($item->jumlah_bayar ?? 0) > 0;
            })
            ->sortBy([
                ['jenis_pembayaran', 'asc'],
                ['tahun_bayar', 'asc'],
                ['bulan_bayar', 'asc'],
            ])
            ->values();

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada transaksi pada tanggal tersebut.');
        }

        $total = $items->sum(function ($item) {
            return (int) ($item->jumlah_bayar ?? 0);
        });

        $admin = DB::table('admin')
            ->where('id_admin', session('admin_id'))
            ->first();

        return view('pembayaran-spp.kwitansi-gabungan-tanggal', compact(
            'siswa',
            'items',
            'total',
            'tanggalNormal',
            'admin'
        ));
    }
    public function bayarGabungan(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        if (!AdminUnitScope::bolehAksesSiswa($siswa)) {
            abort(403, 'Santri ini tidak termasuk unit admin yang sedang login.');
        }

        $bolehBayarPondok = $this->tampilPondok($siswa) && $this->ambilNominalPondok($siswa) > 0;

        $items = $request->input('items', []);

        if (!is_array($items) || empty($items)) {
            return back()->with('error', 'Tidak ada data pembayaran yang dikirim.');
        }

        if ($request->filled('bayar_satuan')) {
            $key = $request->input('bayar_satuan');

            if (!isset($items[$key])) {
                return back()->with('error', 'Data bulan yang akan dibayar tidak ditemukan.');
            }

            $selectedItems = [$items[$key]];
        } else {
            $selectedItems = collect($items)
                ->filter(function ($item) {
                    return isset($item['checked']) && (string) $item['checked'] === '1';
                })
                ->values()
                ->toArray();
        }

        if (empty($selectedItems)) {
            return back()->with('error', 'Silakan pilih minimal satu bulan pembayaran.');
        }

        $tglBayar = $request->input('tgl_bayar', now()->toDateString());
        $tahunAjaran = $request->input('tahun_ajaran', $this->tahunAjaranAktif());

        try {
            $tglBayar = Carbon::parse($tglBayar)->toDateString();
        } catch (\Throwable $e) {
            return back()->with('error', 'Tanggal bayar tidak valid.');
        }

        DB::beginTransaction();

        try {
            $berhasil = 0;
            $dilewati = 0;
            $total = 0;

            foreach ($selectedItems as $item) {
                $jenis = strtolower(trim($item['jenis'] ?? ''));
                $bulan = trim((string) ($item['bulan'] ?? ''));
                $tahun = trim((string) ($item['tahun'] ?? ''));

                $nominalInput = $item['nominal_input'] ?? ($item['nominal'] ?? 0);
                $nominal = (int) preg_replace('/[^0-9]/', '', (string) $nominalInput);

                $tagihanInput = $item['tagihan'] ?? 0;
                $tagihan = (int) preg_replace('/[^0-9]/', '', (string) $tagihanInput);

                if (!in_array($jenis, ['formal', 'diniyah', 'pondok'], true)) {
                    $dilewati++;
                    continue;
                }

                if ($bulan === '' || $tahun === '' || $nominal <= 0) {
                    $dilewati++;
                    continue;
                }

                $jenisNormal = $jenis === 'pondok' ? 'diniyah' : $jenis;

                if ($jenisNormal === 'diniyah' && !$bolehBayarPondok) {
                    $dilewati++;
                    continue;
                }

                if ($tagihan <= 0) {
                    $tagihan = $jenisNormal === 'formal'
                        ? (int) $this->ambilNominalFormal($siswa)
                        : (int) $this->ambilNominalPondok($siswa);
                }

                if ($tagihan <= 0) {
                    $dilewati++;
                    continue;
                }

                $totalSebelumnya = $this->totalTerbayarJenisBulan(
                    $id,
                    $jenisNormal,
                    $bulan,
                    $tahun,
                    $tahunAjaran
                );

                $sisaSebelum = max($tagihan - $totalSebelumnya, 0);

                if ($sisaSebelum <= 0) {
                    $dilewati++;
                    continue;
                }

                /*
                 * Nominal dibatasi maksimal sisa tagihan.
                 * Jadi kalau input lebih besar, sistem tidak membuat kelebihan bayar.
                 */
                $nominal = min($nominal, $sisaSebelum);

                $totalSetelah = $totalSebelumnya + $nominal;
                $statusBayar = $this->statusPembayaran($tagihan, $totalSetelah);

                $this->insertPembayaranBulanan($id, [
                    'jenis' => $jenisNormal,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nominal' => $nominal,
                    'tagihan' => $tagihan,
                    'status_bayar' => $statusBayar,
                    'tgl_bayar' => $tglBayar,
                    'tahun_ajaran' => $tahunAjaran,
                    'semester' => $this->semesterDariBulan($bulan),
                    'keterangan' => $statusBayar === 'LUNAS'
                        ? ('Lunas - Pembayaran ' . ($jenisNormal === 'formal' ? 'SPP Formal' : 'Pondok/Diniyah') . ' ' . $bulan . ' ' . $tahun)
                        : ('Cicilan - Pembayaran ' . ($jenisNormal === 'formal' ? 'SPP Formal' : 'Pondok/Diniyah') . ' ' . $bulan . ' ' . $tahun),
                ]);

                $berhasil++;
                $total += $nominal;
            }

            DB::commit();

            if ($berhasil <= 0) {
                return back()->with('error', 'Tidak ada pembayaran baru yang disimpan. Kemungkinan bulan yang dipilih sudah lunas atau nominal tidak valid.');
            }

            return back()->with(
                'success',
                'Pembayaran berhasil disimpan. Berhasil: ' . $berhasil .
                    ' item. Dilewati: ' . $dilewati .
                    '. Total Rp ' . number_format($total, 0, ',', '.')
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    private function tabelPembayaranByJenis(string $jenis): string
    {
        return $jenis === 'formal' ? 'pembayaran' : 'pembayaran_diniyah';
    }

    private function sudahLunasBulanan($idSiswa, string $jenis, string $bulan, string $tahun): bool
    {
        $table = $this->tabelPembayaranByJenis($jenis);

        if (!Schema::hasTable($table)) {
            return false;
        }

        $columns = Schema::getColumnListing($table);

        $query = DB::table($table);

        if (in_array('id_siswa', $columns, true)) {
            $query->where('id_siswa', $idSiswa);
        } elseif (in_array('id_santri', $columns, true)) {
            $query->where('id_santri', $idSiswa);
        }

        $this->whereKolomBulanTahun($query, $columns, $bulan, $tahun);

        if (in_array('status', $columns, true)) {
            $query->where(function ($q) {
                $q->where('status', 'Lunas')
                    ->orWhere('status', 'lunas')
                    ->orWhere('status', 'LUNAS');
            });
        }

        return $query->exists();
    }

    private function whereKolomBulanTahun($query, array $columns, string $bulan, string $tahun): void
    {
        if (in_array('bulan', $columns, true)) {
            $query->where('bulan', $bulan);
        } elseif (in_array('bulan_bayar', $columns, true)) {
            $query->where('bulan_bayar', $bulan);
        } elseif (in_array('nama_bulan', $columns, true)) {
            $query->where('nama_bulan', $bulan);
        }

        if (in_array('tahun', $columns, true)) {
            $query->where('tahun', $tahun);
        } elseif (in_array('tahun_bayar', $columns, true)) {
            $query->where('tahun_bayar', $tahun);
        }
    }
    private function insertPembayaranBulanan($idSiswa, array $item): void
    {
        $jenis = strtolower($item['jenis']);
        $table = $this->tabelPembayaranByJenis($jenis);

        if (!Schema::hasTable($table)) {
            throw new \RuntimeException("Tabel {$table} tidak ditemukan.");
        }

        $columns = Schema::getColumnListing($table);
        $now = now();

        $nominal = (int) ($item['nominal'] ?? 0);
        $tagihan = (int) ($item['tagihan'] ?? 0);
        $statusBayar = $this->normalisasiStatusPembayaran($item['status_bayar'] ?? 'LUNAS');

        $labelJenis = $jenis === 'formal' ? 'Formal' : 'Pondok/Diniyah';
        $tglBayar = $item['tgl_bayar'] ?? $now->toDateString();
        $tahunAjaran = $item['tahun_ajaran'] ?? $this->tahunAjaranAktif();
        $semester = $item['semester'] ?? $this->semesterDariBulan($item['bulan'] ?? '');

        $keterangan = $item['keterangan']
            ?: (($statusBayar === 'LUNAS' ? 'Lunas' : 'Cicilan') . ' - Pembayaran ' . $labelJenis . ' ' . $item['bulan'] . ' ' . $item['tahun']);

        /*
         * Disesuaikan dengan tabel lama:
         * pembayaran:
         * id_bayar, id_siswa, tgl_bayar, bulan_bayar, tahun_bayar, semester,
         * jumlah_bayar, terbayar, status_bayar, keterangan, id_admin, tahun_ajaran
         *
         * pembayaran_diniyah:
         * id_bayar_diniyah, id_admin, id_siswa, tgl_bayar, bulan_bayar, tahun_bayar,
         * jumlah_bayar, terbayar, keterangan, tahun_ajaran
         *
         * Tidak memakai kolom tagihan karena di tabel tidak ada.
         */
        $candidate = [
            'id_siswa' => $idSiswa,
            'id_santri' => $idSiswa,

            'tanggal' => $tglBayar,
            'tgl_bayar' => $tglBayar,
            'tanggal_bayar' => $tglBayar,

            'bulan' => $item['bulan'],
            'bulan_bayar' => $item['bulan'],
            'nama_bulan' => $item['bulan'],

            'tahun' => $item['tahun'],
            'tahun_bayar' => $item['tahun'],

            'semester' => $semester,
            'tahun_ajaran' => $tahunAjaran,

            'jenis' => $labelJenis,
            'jenis_pembayaran' => $labelJenis,

            'keterangan' => $keterangan,
            'uraian' => $keterangan,

            'nominal' => $nominal,
            'jumlah' => $nominal,
            'jumlah_bayar' => $tagihan,
            'bayar' => $nominal,
            'terbayar' => $nominal,

            'status' => $statusBayar,
            'status_bayar' => $statusBayar,

            'id_admin' => session('admin_id') ?: 1,
            'admin_id' => session('admin_id') ?: 1,

            'created_at' => $now,
            'updated_at' => $now,
        ];

        $data = collect($candidate)->only($columns)->toArray();

        DB::table($table)->insert($data);

        $this->insertRiwayatPembayaranBulanan($idSiswa, $jenis, array_merge($item, [
            'nominal' => $nominal,
            'status_bayar' => $statusBayar,
            'keterangan' => $keterangan,
            'tgl_bayar' => $tglBayar,
            'tahun_ajaran' => $tahunAjaran,
        ]));
    }

    private function insertRiwayatPembayaranBulanan($idSiswa, string $jenis, array $item): void
    {
        if (!Schema::hasTable('riwayat_transaksi')) {
            return;
        }

        $columns = Schema::getColumnListing('riwayat_transaksi');
        $now = now();

        $labelJenis = $jenis === 'formal' ? 'Formal' : 'Pondok/Diniyah';
        $keterangan = 'Pembayaran ' . $labelJenis . ' ' . $item['bulan'] . ' ' . $item['tahun'];

        $candidate = [
            'id_siswa' => $idSiswa,
            'id_santri' => $idSiswa,

            'tanggal' => $now->toDateString(),
            'tgl_transaksi' => $now->toDateString(),
            'tanggal_transaksi' => $now->toDateString(),

            'bulan' => $item['bulan'],
            'bulan_bayar' => $item['bulan'],

            'tahun' => $item['tahun'],
            'tahun_bayar' => $item['tahun'],

            'jenis' => $jenis,
            'jenis_transaksi' => 'pemasukan',
            'kategori' => $labelJenis,

            'keterangan' => $keterangan,
            'uraian' => $keterangan,

            'nominal' => (int) $item['nominal'],
            'jumlah' => (int) $item['nominal'],

            'status' => $item['status_bayar'] ?? 'Lunas',

            'id_admin' => session('admin_id'),
            'admin_id' => session('admin_id'),

            'created_at' => $now,
            'updated_at' => $now,
        ];

        $data = collect($candidate)->only($columns)->toArray();

        DB::table('riwayat_transaksi')->insert($data);
    }
}
