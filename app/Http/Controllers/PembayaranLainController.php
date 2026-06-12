<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\JenisPembayaran;
use App\Models\PembayaranPangkal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\AdminUnitScope;

class PembayaranLainController extends Controller
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

        return view('pembayaran-lain.index', compact(
            'search',
            'hasilSiswa'
        ));
    }

    public function siswa($id)
    {
        $siswa = Siswa::findOrFail($id);
        if (!AdminUnitScope::bolehAksesSiswa($siswa)) {
            abort(403, 'Santri ini tidak termasuk unit admin yang sedang login.');
        }

        $jenisPembayaran = JenisPembayaran::orderBy('nama_jenis')->get();

        $riwayat = PembayaranPangkal::where('id_siswa', $siswa->id_siswa)
            ->orderByDesc('tgl_bayar')
            ->orderByDesc('id_pangkal')
            ->get();

        $rekapTagihan = $jenisPembayaran->map(function ($jenis) use ($siswa) {
            $totalTerbayar = PembayaranPangkal::where('id_siswa', $siswa->id_siswa)
                ->where('jenis_tagihan', $jenis->nama_jenis)
                ->sum('nominal_bayar');

            $nominalTagihan = (int) $jenis->nominal_standar;
            $sisa = max($nominalTagihan - $totalTerbayar, 0);

            if ($totalTerbayar <= 0) {
                $status = 'BELUM';
            } elseif ($sisa > 0) {
                $status = 'CICIL';
            } else {
                $status = 'LUNAS';
            }

            return [
                'id_jenis' => $jenis->id_jenis,
                'nama_jenis' => $jenis->nama_jenis,
                'nominal_standar' => $nominalTagihan,
                'terbayar' => $totalTerbayar,
                'sisa' => $sisa,
                'status' => $status,
            ];
        });

        $totalTagihan = $rekapTagihan->sum('nominal_standar');
        $totalTerbayar = $rekapTagihan->sum('terbayar');
        $totalSisa = $rekapTagihan->sum('sisa');

        return view('pembayaran-lain.siswa', compact(
            'siswa',
            'jenisPembayaran',
            'riwayat',
            'rekapTagihan',
            'totalTagihan',
            'totalTerbayar',
            'totalSisa'
        ));
    }

    public function bayar(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'tgl_bayar' => 'required|date',
            'nominal_bayar' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'id_jenis' => 'nullable|exists:jenis_pembayaran,id_jenis',
            'jenis_tagihan' => 'nullable|string|max:100',
        ]);

        if ($request->filled('id_jenis')) {
            $jenis = JenisPembayaran::findOrFail($request->id_jenis);
        } elseif ($request->filled('jenis_tagihan')) {
            $jenis = JenisPembayaran::where('nama_jenis', $request->jenis_tagihan)->first();

            if (!$jenis) {
                return back()
                    ->withInput()
                    ->with('error', 'Jenis pembayaran tidak ditemukan. Cek data jenis pembayaran.');
            }
        } else {
            return back()
                ->withInput()
                ->with('error', 'Jenis pembayaran wajib dipilih.');
        }

        $nominalTagihan = (int) ($jenis->nominal_standar ?? 0);
        $nominalBayar = (int) $request->nominal_bayar;

        $sudahTerbayar = PembayaranPangkal::where('id_siswa', $siswa->id_siswa)
            ->where('jenis_tagihan', $jenis->nama_jenis)
            ->sum('nominal_bayar');

        $keteranganOtomatis = 'LUNAS';

        if ($nominalTagihan > 0) {
            $sisa = max($nominalTagihan - $sudahTerbayar, 0);

            if ($sisa <= 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Tagihan ini sudah lunas.');
            }

            if ($nominalBayar > $sisa) {
                return back()
                    ->withInput()
                    ->with('error', 'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan Rp ' . number_format($sisa, 0, ',', '.'));
            }

            if (($sudahTerbayar + $nominalBayar) < $nominalTagihan) {
                $keteranganOtomatis = 'CICILAN';
            }
        }

        PembayaranPangkal::insert([
            'id_siswa' => $siswa->id_siswa,
            'tgl_bayar' => $request->tgl_bayar,
            'jenis_tagihan' => $jenis->nama_jenis,
            'nominal_bayar' => $nominalBayar,
            'keterangan' => $request->keterangan ?: $keteranganOtomatis,
            'id_admin' => session('admin_id') ?? 0,
        ]);

        return redirect()
            ->route('pembayaran-lain.siswa', $siswa->id_siswa)
            ->with('success', 'Pembayaran berhasil disimpan. Klik ikon print pada riwayat untuk mencetak kwitansi.');
    }

    public function hapus($id)
    {
        $pembayaran = PembayaranPangkal::findOrFail($id);
        $idSiswa = $pembayaran->id_siswa;

        $pembayaran->delete();

        return redirect()
            ->route('pembayaran-lain.siswa', $idSiswa)
            ->with('success', 'Riwayat pembayaran berhasil dihapus.');
    }

    public function kwitansi($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Penting
        |--------------------------------------------------------------------------
        | URL /pembayaran-lain/kwitansi/{id} adalah kwitansi untuk pembayaran lain
        | tagihan tetap. Tabel utama yang dipakai sistem ini adalah pembayaran_pangkal.
        | Table lain tetap dibuat fallback supaya aman jika database lama berbeda.
        */

        $paymentTables = [
            'pembayaran_pangkal',
            'pembayaran_lain',
            'pembayaran_lain_santri',
        ];

        $chosen = null;

        foreach ($paymentTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $cols = Schema::getColumnListing($table);

            $pk = collect([
                'id_pangkal',
                'id_pembayaran_lain',
                'id_bayar_lain',
                'id_bayar',
                'id_lain',
                'id',
            ])->first(fn($col) => in_array($col, $cols, true));

            if (!$pk) {
                continue;
            }

            if (DB::table($table)->where($pk, $id)->exists()) {
                $chosen = [
                    'table' => $table,
                    'pk' => $pk,
                    'cols' => $cols,
                ];
                break;
            }
        }

        if (!$chosen) {
            abort(404, 'Data pembayaran lain tidak ditemukan.');
        }

        $paymentTable = $chosen['table'];
        $paymentPk = $chosen['pk'];
        $paymentCols = $chosen['cols'];

        $query = DB::table($paymentTable . ' as pl');
        $select = ['pl.*'];

        /*
        |--------------------------------------------------------------------------
        | Join siswa
        |--------------------------------------------------------------------------
        */

        $siswaFk = collect([
            'id_siswa',
            'siswa_id',
        ])->first(fn($col) => in_array($col, $paymentCols, true));

        if ($siswaFk && Schema::hasTable('siswa')) {
            $siswaCols = Schema::getColumnListing('siswa');

            if (in_array('id_siswa', $siswaCols, true)) {
                $query->leftJoin('siswa as s', 's.id_siswa', '=', 'pl.' . $siswaFk);

                foreach (
                    [
                        'nama_siswa',
                        'nama',
                        'nama_santri',
                        'nama_lengkap',
                        'nis',
                        'nisn',
                        'kelas_formal',
                        'kelas_diniyah',
                        'status_mukim',
                    ] as $col
                ) {
                    if (in_array($col, $siswaCols, true)) {
                        $select[] = 's.' . $col;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Nama jenis pembayaran
        |--------------------------------------------------------------------------
        | Utamakan pl.jenis_tagihan karena di halaman web riwayat jenis tampil dari
        | kolom ini, misalnya "DU SANTRI REGULER".
        */

        $jenisFk = collect([
            'id_jenis',
            'id_jenis_pembayaran',
            'jenis_id',
            'id_tagihan',
            'id_biaya',
        ])->first(fn($col) => in_array($col, $paymentCols, true));

        $paymentNameCols = collect([
            'jenis_tagihan',
            'nama_pembayaran',
            'nama_jenis',
            'jenis_pembayaran',
            'jenis',
            'nama_tagihan',
            'nama_biaya',
            'uraian',
        ])->filter(fn($col) => in_array($col, $paymentCols, true))
            ->map(fn($col) => 'pl.' . $col)
            ->values()
            ->all();

        $jenisJoined = false;

        if ($jenisFk) {
            foreach (['jenis_pembayaran', 'data_jenis_pembayaran', 'jenis_pembayaran_lain'] as $jenisTable) {
                if (!Schema::hasTable($jenisTable)) {
                    continue;
                }

                $jenisCols = Schema::getColumnListing($jenisTable);

                $jenisPk = collect([
                    'id_jenis',
                    'id_jenis_pembayaran',
                    'id',
                    'id_biaya',
                    'id_tagihan',
                ])->first(fn($col) => in_array($col, $jenisCols, true));

                if (!$jenisPk) {
                    continue;
                }

                $query->leftJoin($jenisTable . ' as jp', 'jp.' . $jenisPk, '=', 'pl.' . $jenisFk);

                $jenisNameCols = collect([
                    'nama_jenis',
                    'nama_pembayaran',
                    'nama_jenis_pembayaran',
                    'jenis_pembayaran',
                    'nama',
                    'judul',
                    'uraian',
                ])->filter(fn($col) => in_array($col, $jenisCols, true))
                    ->map(fn($col) => 'jp.' . $col)
                    ->values()
                    ->all();

                $coalesce = array_merge($paymentNameCols, $jenisNameCols, ["'Pembayaran Lain'"]);
                $select[] = DB::raw('COALESCE(' . implode(', ', $coalesce) . ') as jenis_label');

                $jenisJoined = true;
                break;
            }
        }

        if (!$jenisJoined) {
            $coalesce = array_merge($paymentNameCols, ["'Pembayaran Lain'"]);
            $select[] = DB::raw('COALESCE(' . implode(', ', $coalesce) . ') as jenis_label');
        }

        /*
        |--------------------------------------------------------------------------
        | Join admin
        |--------------------------------------------------------------------------
        */

        $adminFk = collect([
            'id_admin',
            'admin_id',
        ])->first(fn($col) => in_array($col, $paymentCols, true));

        if ($adminFk && Schema::hasTable('admin')) {
            $adminCols = Schema::getColumnListing('admin');

            if (in_array('id_admin', $adminCols, true)) {
                $query->leftJoin('admin as a', 'a.id_admin', '=', 'pl.' . $adminFk);

                $adminNameCols = collect([
                    'nama_lengkap',
                    'nama',
                    'username',
                ])->filter(fn($col) => in_array($col, $adminCols, true))
                    ->map(fn($col) => 'a.' . $col)
                    ->values()
                    ->all();

                if (!empty($adminNameCols)) {
                    $select[] = DB::raw('COALESCE(' . implode(', ', $adminNameCols) . ", 'Admin') as nama_admin");
                }
            }
        }

        $pembayaran = $query
            ->select($select)
            ->where('pl.' . $paymentPk, $id)
            ->first();

        if (!$pembayaran) {
            abort(404, 'Data pembayaran lain tidak ditemukan.');
        }

        return view('pembayaran-lain.kwitansi', compact('pembayaran'));
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
            return $this->penyebut(intval($nilai / 10)) . ' puluh' . $this->penyebut($nilai % 10);
        }

        if ($nilai < 200) {
            return ' seratus' . $this->penyebut($nilai - 100);
        }

        if ($nilai < 1000) {
            return $this->penyebut(intval($nilai / 100)) . ' ratus' . $this->penyebut($nilai % 100);
        }

        if ($nilai < 2000) {
            return ' seribu' . $this->penyebut($nilai - 1000);
        }

        if ($nilai < 1000000) {
            return $this->penyebut(intval($nilai / 1000)) . ' ribu' . $this->penyebut($nilai % 1000);
        }

        if ($nilai < 1000000000) {
            return $this->penyebut(intval($nilai / 1000000)) . ' juta' . $this->penyebut($nilai % 1000000);
        }

        if ($nilai < 1000000000000) {
            return $this->penyebut(intval($nilai / 1000000000)) . ' miliar' . $this->penyebut(fmod($nilai, 1000000000));
        }

        return '';
    }

    private function terbilang($nilai)
    {
        if ($nilai < 0) {
            return 'minus ' . trim($this->penyebut($nilai));
        }

        return trim($this->penyebut($nilai));
    }
}
