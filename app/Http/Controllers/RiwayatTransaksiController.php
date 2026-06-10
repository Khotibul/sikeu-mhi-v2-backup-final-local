<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PembayaranDiniyah;
use App\Models\PembayaranPangkal;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class RiwayatTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $jenis = $request->get('jenis', 'semua');
        $search = $request->get('search');

        $transaksi = collect();

        if ($jenis === 'semua' || $jenis === 'formal') {
            $transaksi = $transaksi->merge(
                $this->ambilFormal($tanggalAwal, $tanggalAkhir, $search)
            );
        }

        if ($jenis === 'semua' || $jenis === 'pondok') {
            $transaksi = $transaksi->merge(
                $this->ambilPondok($tanggalAwal, $tanggalAkhir, $search)
            );
        }

        if ($jenis === 'semua' || $jenis === 'lain') {
            $transaksi = $transaksi->merge(
                $this->ambilPembayaranLain($tanggalAwal, $tanggalAkhir, $search)
            );
        }

        if ($jenis === 'semua' || $jenis === 'pengeluaran') {
            $transaksi = $transaksi->merge(
                $this->ambilPengeluaran($tanggalAwal, $tanggalAkhir, $search)
            );
        }

        // Satukan baris yang benar-benar sama supaya riwayat/laporan layar tidak terlihat dobel.
        // Jika ada dobel, kode hapus baris itu akan berisi semua ID dobel sehingga ketika dihapus,
        // semua salinan dobel ikut bersih.
        [$transaksi, $jumlahDuplikatTerdeteksi] = $this->rapikanDuplikatTampilan($transaksi);

        $transaksi = $transaksi
            ->sortByDesc(function ($item) {
                return ($item['tanggal'] ?? '') . ' ' . str_pad((string) ($item['id'] ?? 0), 12, '0', STR_PAD_LEFT);
            })
            ->values();

        $totalPemasukan = $transaksi->sum('masuk');
        $totalPengeluaran = $transaksi->sum('keluar');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        return view('riwayat-transaksi.index', compact(
            'tanggalAwal',
            'tanggalAkhir',
            'jenis',
            'search',
            'transaksi',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoBersih',
            'jumlahDuplikatTerdeteksi'
        ));
    }

    private function ambilFormal(string $tanggalAwal, string $tanggalAkhir, ?string $search)
    {
        $query = Pembayaran::leftJoin('siswa', 'pembayaran.id_siswa', '=', 'siswa.id_siswa')
            ->select(
                'pembayaran.*',
                'siswa.nama_siswa',
                'siswa.nis',
                'siswa.kelas_formal'
            )
            ->whereBetween('pembayaran.tgl_bayar', [$tanggalAwal, $tanggalAkhir]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_siswa', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nis', 'like', '%' . $search . '%')
                    ->orWhere('siswa.kelas_formal', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran.bulan_bayar', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran.tahun_ajaran', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran.keterangan', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran.status_bayar', 'like', '%' . $search . '%');
            });
        }

        return $query
            ->get()
            ->map(function ($item) {
                $nominal = $this->ambilNominalLegacy($item);
                $status = strtoupper(trim($item->status_bayar ?? $item->keterangan ?? 'LUNAS')) ?: 'LUNAS';

                return [
                    'kode' => 'formal:' . $item->id_bayar,
                    'id' => (int) $item->id_bayar,
                    'tipe' => 'formal',
                    'tanggal' => $item->tgl_bayar,
                    'nama' => $item->nama_siswa ?: '-',
                    'subjek' => 'NIS: ' . ($item->nis ?: '-') . ' | Kelas: ' . ($item->kelas_formal ?: '-'),
                    'jenis' => 'Biaya Pendidikan Formal',
                    'keterangan' => trim(($item->bulan_bayar ?: '-') . ' ' . ($item->tahun_bayar ?: '') . ' | ' . ($item->tahun_ajaran ?: '-') . ' | ' . $status),
                    'masuk' => $nominal,
                    'keluar' => 0,
                    'cetak_url' => route('pembayaran-spp.kwitansi', [
                        'jenis' => 'formal',
                        'ids' => $item->id_bayar,
                    ]),
                    'duplikat_count' => 1,
                ];
            });
    }

    private function ambilPondok(string $tanggalAwal, string $tanggalAkhir, ?string $search)
    {
        $query = PembayaranDiniyah::leftJoin('siswa', 'pembayaran_diniyah.id_siswa', '=', 'siswa.id_siswa')
            ->select(
                'pembayaran_diniyah.*',
                'siswa.nama_siswa',
                'siswa.nis',
                'siswa.kelas_diniyah'
            )
            ->whereBetween('pembayaran_diniyah.tgl_bayar', [$tanggalAwal, $tanggalAkhir]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_siswa', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nis', 'like', '%' . $search . '%')
                    ->orWhere('siswa.kelas_diniyah', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran_diniyah.bulan_bayar', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran_diniyah.tahun_ajaran', 'like', '%' . $search . '%')
                    ->orWhere('pembayaran_diniyah.keterangan', 'like', '%' . $search . '%');
            });
        }

        return $query
            ->get()
            ->map(function ($item) {
                $nominal = $this->ambilNominalLegacy($item);
                $status = strtoupper(trim($item->status_bayar ?? $item->keterangan ?? 'LUNAS')) ?: 'LUNAS';

                return [
                    'kode' => 'pondok:' . $item->id_bayar_diniyah,
                    'id' => (int) $item->id_bayar_diniyah,
                    'tipe' => 'pondok',
                    'tanggal' => $item->tgl_bayar,
                    'nama' => $item->nama_siswa ?: '-',
                    'subjek' => 'NIS: ' . ($item->nis ?: '-') . ' | Kelas: ' . ($item->kelas_diniyah ?: '-'),
                    'jenis' => 'Biaya Pendidikan Diniyah',
                    'keterangan' => trim(($item->bulan_bayar ?: '-') . ' ' . ($item->tahun_bayar ?: '') . ' | ' . ($item->tahun_ajaran ?: '-') . ' | ' . $status),
                    'masuk' => $nominal,
                    'keluar' => 0,
                    'cetak_url' => route('pembayaran-spp.kwitansi', [
                        'jenis' => 'pondok',
                        'ids' => $item->id_bayar_diniyah,
                    ]),
                    'duplikat_count' => 1,
                ];
            });
    }

    private function ambilPembayaranLain(string $tanggalAwal, string $tanggalAkhir, ?string $search)
    {
        $transaksi = collect();

        if (Schema::hasTable('pembayaran_pangkal')) {
            $query = PembayaranPangkal::leftJoin('siswa', 'pembayaran_pangkal.id_siswa', '=', 'siswa.id_siswa')
                ->select(
                    'pembayaran_pangkal.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.kelas_formal',
                    'siswa.kelas_diniyah'
                )
                ->whereBetween('pembayaran_pangkal.tgl_bayar', [$tanggalAwal, $tanggalAkhir]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('siswa.nama_siswa', 'like', '%' . $search . '%')
                        ->orWhere('siswa.nis', 'like', '%' . $search . '%')
                        ->orWhere('pembayaran_pangkal.jenis_tagihan', 'like', '%' . $search . '%')
                        ->orWhere('pembayaran_pangkal.keterangan', 'like', '%' . $search . '%');
                });
            }

            $transaksi = $transaksi->merge(
                $query->get()->map(function ($item) {
                    return [
                        'kode' => 'lain:' . $item->id_pangkal,
                        'id' => (int) $item->id_pangkal,
                        'tipe' => 'lain',
                        'tanggal' => $item->tgl_bayar,
                        'nama' => $item->nama_siswa ?: '-',
                        'subjek' => 'NIS: ' . ($item->nis ?: '-') . ' | Formal: ' . ($item->kelas_formal ?: '-') . ' | Diniyah: ' . ($item->kelas_diniyah ?: '-'),
                        'jenis' => 'Pembayaran Lain Santri',
                        'keterangan' => ($item->jenis_tagihan ?: '-') . ' | ' . ($item->keterangan ?: '-'),
                        'masuk' => (int) ($item->nominal_bayar ?? 0),
                        'keluar' => 0,
                        'cetak_url' => Route::has('pembayaran-lain.kwitansi') ? route('pembayaran-lain.kwitansi', $item->id_pangkal) : '#',
                        'duplikat_count' => 1,
                    ];
                })
            );
        }

        // Setoran bebas / pemasukan lain umum.
        if (Schema::hasTable('pemasukan_lain')) {
            $query = DB::table('pemasukan_lain')
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_penyetor', 'like', '%' . $search . '%')
                        ->orWhere('uraian', 'like', '%' . $search . '%');
                });
            }

            $transaksi = $transaksi->merge(
                $query->get()->map(function ($item) {
                    return [
                        'kode' => 'setoran-bebas:' . $item->id_masuk,
                        'id' => (int) $item->id_masuk,
                        'tipe' => 'lain',
                        'tanggal' => $item->tanggal,
                        'nama' => $item->nama_penyetor ?: '-',
                        'subjek' => 'Setoran Bebas / Pemasukan Lain',
                        'jenis' => 'Setoran Bebas',
                        'keterangan' => $item->uraian ?: '-',
                        'masuk' => (int) ($item->nominal ?? 0),
                        'keluar' => 0,
                        'cetak_url' => Route::has('pembayaran-lain.bebas.cetak')
                            ? route('pembayaran-lain.bebas.cetak', $item->id_masuk)
                            : '#',
                        'duplikat_count' => 1,
                    ];
                })
            );
        }

        return $transaksi;
    }

    private function ambilPengeluaran(string $tanggalAwal, string $tanggalAkhir, ?string $search)
    {
        $query = Pengeluaran::query()
            ->whereBetween('tgl_keluar', [$tanggalAwal, $tanggalAkhir]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('penerima', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%')
                    ->orWhere('uraian', 'like', '%' . $search . '%');
            });
        }

        return $query
            ->get()
            ->map(function ($item) {
                return [
                    'kode' => 'pengeluaran:' . $item->id_keluar,
                    'id' => (int) $item->id_keluar,
                    'tipe' => 'pengeluaran',
                    'tanggal' => $item->tgl_keluar,
                    'nama' => $item->penerima ?: '-',
                    'subjek' => 'Unit: ' . ($item->unit ?: '-'),
                    'jenis' => 'Pengeluaran',
                    'keterangan' => $item->uraian ?: '-',
                    'masuk' => 0,
                    'keluar' => (int) ($item->jumlah ?? 0),
                    'cetak_url' => Route::has('pengeluaran.cetak') ? route('pengeluaran.cetak', $item->id_keluar) : '#',
                    'duplikat_count' => 1,
                ];
            });
    }

    private function rapikanDuplikatTampilan($transaksi): array
    {
        $jumlahDuplikatTerdeteksi = 0;

        $rapi = collect($transaksi)
            ->filter(function ($item) {
                return ((int) ($item['masuk'] ?? 0) > 0) || ((int) ($item['keluar'] ?? 0) > 0);
            })
            ->groupBy(function ($item) {
                // Kunci dibuat dari identitas transaksi, bukan ID.
                // Jadi transaksi yang sama persis dari double click akan muncul satu baris saja.
                return implode('|', [
                    strtolower((string) ($item['tipe'] ?? '')),
                    Carbon::parse($item['tanggal'] ?? now())->toDateString(),
                    strtolower(trim((string) ($item['nama'] ?? ''))),
                    strtolower(trim((string) ($item['subjek'] ?? ''))),
                    strtolower(trim((string) ($item['jenis'] ?? ''))),
                    strtolower(trim((string) ($item['keterangan'] ?? ''))),
                    (int) ($item['masuk'] ?? 0),
                    (int) ($item['keluar'] ?? 0),
                ]);
            })
            ->map(function ($group) use (&$jumlahDuplikatTerdeteksi) {
                $first = $group->first();
                $count = $group->count();

                if ($count > 1) {
                    $jumlahDuplikatTerdeteksi += ($count - 1);

                    $codes = $group->pluck('kode')->values()->all();
                    $first['kode'] = 'group:' . base64_encode(json_encode($codes));
                    $first['duplikat_count'] = $count;
                    $first['keterangan'] = $first['keterangan'] . ' | Duplikat disatukan: ' . $count . ' data';
                }

                return $first;
            })
            ->values();

        return [$rapi, $jumlahDuplikatTerdeteksi];
    }

    public function hapusSatuan(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
        ]);

        $jumlah = $this->hapusBerdasarkanKode($request->kode);

        if ($jumlah <= 0) {
            return back()->with('error', 'Transaksi tidak ditemukan atau sudah terhapus.');
        }

        return back()->with('success', $jumlah . ' transaksi berhasil dihapus.');
    }

    public function hapusBanyak(Request $request)
    {
        $request->validate([
            'kode_transaksi' => 'required|array',
            'kode_transaksi.*' => 'required|string',
        ]);

        $jumlahTerhapus = 0;

        foreach (array_unique($request->kode_transaksi) as $kode) {
            $jumlahTerhapus += $this->hapusBerdasarkanKode($kode);
        }

        if ($jumlahTerhapus <= 0) {
            return back()->with('error', 'Tidak ada transaksi yang berhasil dihapus.');
        }

        return back()->with('success', $jumlahTerhapus . ' transaksi berhasil dihapus.');
    }

    private function hapusBerdasarkanKode(string $kode): int
    {
        $parts = explode(':', $kode, 2);

        if (count($parts) !== 2) {
            return 0;
        }

        [$tipe, $id] = $parts;

        // Kode group dipakai kalau di riwayat ada transaksi sama persis yang disatukan.
        if ($tipe === 'group') {
            $decoded = json_decode(base64_decode($id), true);

            if (!is_array($decoded)) {
                return 0;
            }

            $jumlah = 0;

            foreach ($decoded as $childKode) {
                $jumlah += $this->hapusBerdasarkanKode((string) $childKode);
            }

            return $jumlah;
        }

        return $this->hapusKodeNormal($tipe, $id);
    }

    private function hapusKodeNormal(string $tipe, $id): int
    {
        $id = (int) $id;

        if ($id <= 0) {
            return 0;
        }

        if ($tipe === 'formal') {
            return Pembayaran::where('id_bayar', $id)->delete();
        }

        if ($tipe === 'pondok') {
            return PembayaranDiniyah::where('id_bayar_diniyah', $id)->delete();
        }

        if ($tipe === 'lain') {
            return PembayaranPangkal::where('id_pangkal', $id)->delete();
        }

        if ($tipe === 'setoran-bebas' && Schema::hasTable('pemasukan_lain')) {
            return DB::table('pemasukan_lain')
                ->where('id_masuk', $id)
                ->delete();
        }

        if ($tipe === 'pengeluaran') {
            $pengeluaran = Pengeluaran::where('id_keluar', $id)->first();

            if (!$pengeluaran) {
                return 0;
            }

            $this->hapusBuktiFoto($pengeluaran->bukti_foto ?? null);
            $pengeluaran->delete();

            return 1;
        }

        return 0;
    }

    private function hapusBuktiFoto(?string $namaFile): void
    {
        if (empty($namaFile)) {
            return;
        }

        $paths = [
            public_path('uploads/pengeluaran/' . $namaFile),
            public_path($namaFile),
            storage_path('app/public/' . $namaFile),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    private function ambilNominalLegacy($item): int
    {
        $terbayar = (int) ($item->terbayar ?? 0);
        $jumlahBayar = (int) ($item->jumlah_bayar ?? 0);
        $nominalBayar = (int) ($item->nominal_bayar ?? 0);
        $nominal = (int) ($item->nominal ?? 0);

        // Untuk data cicilan, biasanya nominal riil ada di kolom terbayar.
        if ($terbayar > 0) {
            return $terbayar;
        }

        // Untuk data lama/lunas, jumlah_bayar adalah nominal yang harus dihitung.
        if ($jumlahBayar > 0) {
            return $jumlahBayar;
        }

        if ($nominalBayar > 0) {
            return $nominalBayar;
        }

        return max($nominal, 0);
    }
}
