<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $unit = $request->get('unit');
        $search = $request->get('search');

        $query = Pengeluaran::query()
            ->whereBetween('tgl_keluar', [$tanggalAwal, $tanggalAkhir]);

        if (!empty($unit)) {
            $query->where('unit', $unit);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('penerima', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%')
                    ->orWhere('uraian', 'like', '%' . $search . '%');
            });
        }

        $pengeluaran = $query
            ->orderByDesc('tgl_keluar')
            ->orderByDesc('id_keluar')
            ->get();

        $totalPengeluaran = $pengeluaran->sum('jumlah');

        $unitList = Pengeluaran::whereNotNull('unit')
            ->where('unit', '!=', '')
            ->select('unit')
            ->distinct()
            ->orderBy('unit')
            ->pluck('unit');

        return view('pengeluaran.index', compact(
            'pengeluaran',
            'totalPengeluaran',
            'tanggalAwal',
            'tanggalAkhir',
            'unit',
            'search',
            'unitList'
        ));
    }

    public function create()
    {
        return view('pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_keluar' => 'required|date',
            'penerima'   => 'required|string|max:100',
            'unit'       => 'required|string|max:50',
            'uraian'     => 'required|string',
            'jumlah'     => 'required|numeric|min:1',
            'bukti_foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $tglKeluar = $request->input('tgl_keluar');
        $penerima = trim((string) $request->input('penerima'));
        $unit = trim((string) $request->input('unit'));
        $uraian = trim((string) $request->input('uraian'));
        $jumlah = (int) $request->input('jumlah');

        /*
         * Anti dobel simpan.
         * Penyebab paling umum data dobel adalah tombol submit terpencet dua kali
         * saat upload bukti foto agak lama. Kunci ini aktif 30 detik.
         */
        $token = (string) $request->input('_pengeluaran_token', '');
        $fingerprint = implode('|', [
            session('admin_id') ?? 'guest',
            $token ?: 'no-token',
            $tglKeluar,
            strtolower($penerima),
            strtolower($unit),
            strtolower($uraian),
            $jumlah,
        ]);

        $submitKey = 'pengeluaran:submit:' . sha1($fingerprint);

        if (!Cache::add($submitKey, true, 30)) {
            return redirect()
                ->route('pengeluaran.index')
                ->with('success', 'Pengeluaran sudah diproses. Sistem mencegah data tersimpan dua kali.');
        }

        try {
            DB::transaction(function () use ($request, $tglKeluar, $penerima, $unit, $uraian, $jumlah) {
                $namaFoto = null;

                if ($request->hasFile('bukti_foto')) {
                    $namaFoto = $this->uploadBuktiFoto($request->file('bukti_foto'));
                }

                Pengeluaran::create([
                    'tgl_keluar' => $tglKeluar,
                    'penerima'   => $penerima,
                    'unit'       => $unit,
                    'uraian'     => $uraian,
                    'jumlah'     => $jumlah,
                    'bukti_foto' => $namaFoto,
                ]);
            });

            return redirect()
                ->route('pengeluaran.index')
                ->with('success', 'Data pengeluaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Cache::forget($submitKey);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengeluaran: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $request->validate([
            'tgl_keluar' => 'required|date',
            'penerima'   => 'required|string|max:100',
            'unit'       => 'required|string|max:50',
            'uraian'     => 'required|string',
            'jumlah'     => 'required|numeric|min:0',
            'bukti_foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $namaFoto = $pengeluaran->bukti_foto;

        if ($request->hasFile('bukti_foto')) {
            $this->hapusBuktiFoto($pengeluaran->bukti_foto);
            $namaFoto = $this->uploadBuktiFoto($request->file('bukti_foto'));
        }

        $pengeluaran->update([
            'tgl_keluar' => $request->tgl_keluar,
            'penerima'   => $request->penerima,
            'unit'       => $request->unit,
            'uraian'     => $request->uraian,
            'jumlah'     => (int) $request->jumlah,
            'bukti_foto' => $namaFoto,
        ]);

        return redirect()
            ->route('pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $this->hapusBuktiFoto($pengeluaran->bukti_foto);

        $pengeluaran->delete();

        return redirect()
            ->route('pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    public function cetak($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $nomorBukti = $this->formatNomorBukti($pengeluaran);
        $terbilang = ucwords(trim($this->terbilang((int) $pengeluaran->jumlah))) . ' Rupiah';
        $tanggalCetak = Carbon::parse($pengeluaran->tgl_keluar)->translatedFormat('d F Y');

        return view('pengeluaran.cetak', compact(
            'pengeluaran',
            'nomorBukti',
            'terbilang',
            'tanggalCetak'
        ));
    }

    private function formatNomorBukti(Pengeluaran $pengeluaran): string
    {
        $tgl = Carbon::parse($pengeluaran->tgl_keluar);

        return 'OUT/' . $tgl->format('Y') . '/' . $tgl->format('m') . '/' . str_pad($pengeluaran->id_keluar, 4, '0', STR_PAD_LEFT);
    }

    private function uploadBuktiFoto($file): string
    {
        $folderTujuan = public_path('uploads/pengeluaran');

        if (!File::exists($folderTujuan)) {
            File::makeDirectory($folderTujuan, 0755, true);
        }

        $namaFile = 'bukti-' . date('YmdHis') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($folderTujuan, $namaFile);

        return $namaFile;
    }

    private function hapusBuktiFoto(?string $namaFile): void
    {
        if (empty($namaFile)) {
            return;
        }

        $path = public_path('uploads/pengeluaran/' . $namaFile);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function penyebut($nilai)
    {
        $nilai = abs((int) $nilai);
        $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $temp = "";

        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } elseif ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } elseif ($nilai < 100) {
            $temp = $this->penyebut(intval($nilai / 10)) . " puluh" . $this->penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            $temp = $this->penyebut(intval($nilai / 100)) . " ratus" . $this->penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $temp = $this->penyebut(intval($nilai / 1000)) . " ribu" . $this->penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $temp = $this->penyebut(intval($nilai / 1000000)) . " juta" . $this->penyebut($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $temp = $this->penyebut(intval($nilai / 1000000000)) . " miliar" . $this->penyebut(fmod($nilai, 1000000000));
        }

        return $temp;
    }

    private function terbilang($nilai)
    {
        if ($nilai < 0) {
            return "minus " . trim($this->penyebut($nilai));
        }

        return trim($this->penyebut($nilai));
    }
}