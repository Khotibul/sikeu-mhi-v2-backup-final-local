<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PpdbOnlineController extends Controller
{
    public function form()
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasDiniyahList = $this->ambilKelasDiniyahPpdb();

        return view('ppdb-online.form', compact(
            'tahunAjaran',
            'kelasDiniyahList'
        ));
    }

    public function submit(Request $request)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $request->validate([
            'ppdb_submit_token' => 'nullable|string|max:150',
            'tahun_ajaran' => 'nullable|string|max:20',

            'nama_lengkap' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:30',
            'jk' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tgl_lahir' => 'required|date',
            'alamat' => 'required|string',
            'asal_sekolah' => 'nullable|string|max:150',

            'nama_ayah' => 'required|string|max:150',
            'nama_ibu' => 'nullable|string|max:150',
            'no_hp_ortu' => 'required|string|max:30',

            'jenjang_sekolah' => 'required|in:MTS,SMP,SPM ULYA,MA,SMK',
            'jurusan' => 'nullable|string|max:20',
            'kelas_diniyah' => 'nullable|string|max:100',
            'status_pondok' => 'required|in:Mukim,Pulang Pergi,Ya,Tidak',

            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:30720',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:30720',
            'file_foto' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:30720',
            'file_ijazah' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:30720',
        ]);

        $jurusanSmkValid = ['TKJ', 'AK', 'BDP'];

        if ($request->jenjang_sekolah === 'SMK') {
            if (empty($request->jurusan)) {
                return back()
                    ->withInput()
                    ->with('error', 'Jurusan wajib dipilih untuk jenjang SMK.');
            }

            if (!in_array($request->jurusan, $jurusanSmkValid, true)) {
                return back()
                    ->withInput()
                    ->with('error', 'Jurusan SMK hanya boleh TKJ, AK, atau BDP.');
            }
        }

        if ($request->jenjang_sekolah !== 'SMK') {
            $request->merge(['jurusan' => null]);
        }

        if ($request->status_pondok === 'Mukim' && empty($request->kelas_diniyah)) {
            return back()
                ->withInput()
                ->with('error', 'Kelas diniyah wajib dipilih untuk santri mukim.');
        }

        $tahunAjaran = $request->tahun_ajaran ?: $this->tahunAjaranAktif();

        /*
         * Kunci anti input ganda.
         * Jika browser mengirim POST dua kali karena tombol dobel klik / koneksi lambat,
         * request kedua akan ditolak sebelum insert database.
         */
        $submitToken = trim((string) $request->input('ppdb_submit_token', ''));
        $fingerprint = sha1(implode('|', [
            strtolower(trim((string) $request->nama_lengkap)),
            strtolower(trim((string) $request->nama_ayah)),
            strtolower(trim((string) $request->nama_ibu)),
            preg_replace('/\D+/', '', (string) $request->no_hp_ortu),
            (string) $request->tgl_lahir,
            (string) $request->jk,
            (string) $request->jenjang_sekolah,
            (string) $request->jurusan,
            (string) $tahunAjaran,
            $submitToken,
        ]));

        $submitKey = 'ppdb_online_submit_' . $fingerprint;

        if (!Cache::add($submitKey, true, 90)) {
            $existing = $this->findExistingPendaftar($request, $tahunAjaran);

            if ($existing) {
                return redirect()
                    ->route('ppdb-online.sukses', $existing->id_daftar)
                    ->with('success', 'Formulir sudah diproses. Sistem mencegah data ganda.');
            }

            return back()
                ->withInput()
                ->with('error', 'Formulir sedang diproses. Mohon tunggu sebentar dan jangan klik tombol kirim berulang.');
        }

        $statusPondokInput = $request->status_pondok;
        $statusPondok = in_array($statusPondokInput, ['Mukim', 'Ya'], true) ? 'Ya' : 'Tidak';

        $kelasDiniyah = $statusPondok === 'Ya'
            ? ($request->kelas_diniyah ?: $this->defaultKelasDiniyah())
            : '-';

        $uploadedFiles = [];

        try {
            $id = DB::transaction(function () use ($request, $tahunAjaran, $statusPondok, $kelasDiniyah, &$uploadedFiles) {
                $existing = $this->findExistingPendaftar($request, $tahunAjaran);

                if ($existing) {
                    return (int) $existing->id_daftar;
                }

                $noDaftar = $this->generateNoDaftar(true);

                $data = [
                    'no_daftar' => $noDaftar,
                    'tgl_daftar' => now()->toDateString(),

                    'nama_lengkap' => $request->nama_lengkap,
                    'nisn' => $request->nisn,
                    'jk' => $request->jk,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'alamat' => $request->alamat,
                    'asal_sekolah' => $request->asal_sekolah,

                    'nama_ayah' => $request->nama_ayah,
                    'nama_ibu' => $request->nama_ibu,
                    'no_hp_ortu' => $request->no_hp_ortu,

                    'jenjang_sekolah' => $request->jenjang_sekolah,
                    'jurusan' => $request->jenjang_sekolah === 'SMK' ? $request->jurusan : null,
                    'kelas_diniyah' => $kelasDiniyah,
                    'status_pondok' => $statusPondok,

                    'status_seleksi' => 'Pending',
                    'tahun_ajaran' => $tahunAjaran,
                ];

                if (Schema::hasColumn('ppdb_daftar', 'created_at')) {
                    $data['created_at'] = now();
                }

                if (Schema::hasColumn('ppdb_daftar', 'updated_at')) {
                    $data['updated_at'] = now();
                }

                foreach (['file_kk', 'file_ktp', 'file_foto', 'file_ijazah'] as $field) {
                    if ($request->hasFile($field)) {
                        $data[$field] = $this->uploadFile($request, $field, $noDaftar);
                        $uploadedFiles[] = $data[$field];
                    }
                }

                return (int) DB::table('ppdb_daftar')->insertGetId($this->filterKolomPpdb($data));
            });

            return redirect()
                ->route('ppdb-online.sukses', $id)
                ->with('success', 'Formulir pendaftaran berhasil dikirim.');
        } catch (ValidationException $e) {
            Cache::forget($submitKey);
            $this->hapusFileUploadGagal($uploadedFiles);

            throw $e;
        } catch (\Throwable $e) {
            Cache::forget($submitKey);
            $this->hapusFileUploadGagal($uploadedFiles);

            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim formulir: ' . $e->getMessage());
        }
    }

    public function sukses($id)
    {
        $pendaftar = DB::table('ppdb_daftar')
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar) {
            abort(404);
        }

        return view('ppdb-online.sukses', compact('pendaftar'));
    }

    private function findExistingPendaftar(Request $request, string $tahunAjaran)
    {
        $nama = strtolower(trim((string) $request->nama_lengkap));
        $hp = preg_replace('/\D+/', '', (string) $request->no_hp_ortu);

        $query = DB::table('ppdb_daftar')
            ->whereRaw('LOWER(TRIM(nama_lengkap)) = ?', [$nama])
            ->where('tgl_lahir', $request->tgl_lahir)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($hp !== '') {
            $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(no_hp_ortu, ' ', ''), '-', ''), '+', ''), '.', '') = ?", [$hp]);
        }

        if ($request->filled('nisn')) {
            $query->where(function ($q) use ($request) {
                $q->where('nisn', $request->nisn)
                    ->orWhereNull('nisn')
                    ->orWhere('nisn', '');
            });
        }

        return $query
            ->orderByDesc('id_daftar')
            ->first();
    }

    private function generateNoDaftar(bool $forUpdate = false): string
    {
        $tahun = now()->format('Y');
        $bulan = now()->format('m');
        $prefix = 'PPDB-' . $tahun . $bulan . '-';

        $query = DB::table('ppdb_daftar')
            ->where('no_daftar', 'like', $prefix . '%')
            ->orderByDesc('id_daftar');

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        $last = $query->first();

        if (!$last || empty($last->no_daftar)) {
            $urut = 1;
        } else {
            $urut = ((int) Str::afterLast($last->no_daftar, '-')) + 1;
        }

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    private function uploadFile(Request $request, string $field, string $noDaftar): ?string
    {
        $file = $request->file($field);

        if (!$file) {
            return null;
        }

        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                $field => 'File gagal diupload. Silakan pilih file ulang.',
            ]);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $folder = 'ppdb/' . $noDaftar;

        File::ensureDirectoryExists(public_path($folder), 0755, true);

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $this->compressImageToMax2Mb($file, $folder, $field);
        }

        if ($extension === 'pdf') {
            if ($file->getSize() > 2 * 1024 * 1024) {
                throw ValidationException::withMessages([
                    $field => 'File PDF maksimal 2 MB. Untuk dokumen besar, mohon upload dalam bentuk foto JPG/PNG agar bisa dikompres otomatis.',
                ]);
            }

            $namaFile = $field . '-' . time() . '-' . Str::random(6) . '.pdf';
            $relativePath = $folder . '/' . $namaFile;

            $file->move(public_path($folder), $namaFile);

            return $relativePath;
        }

        throw ValidationException::withMessages([
            $field => 'Format file tidak didukung.',
        ]);
    }

    private function compressImageToMax2Mb($file, string $folder, string $field): string
    {
        if (!extension_loaded('gd')) {
            throw ValidationException::withMessages([
                $field => 'Ekstensi PHP GD belum aktif. Aktifkan GD agar gambar bisa dikompres otomatis.',
            ]);
        }

        $maxBytes = 2 * 1024 * 1024;
        $sourcePath = $file->getRealPath();
        $info = getimagesize($sourcePath);

        if (!$info) {
            throw ValidationException::withMessages([
                $field => 'File gambar tidak valid.',
            ]);
        }

        $mime = $info['mime'];
        $width = (int) $info[0];
        $height = (int) $info[1];

        if ($mime === 'image/jpeg') {
            $sourceImage = imagecreatefromjpeg($sourcePath);
        } elseif ($mime === 'image/png') {
            $sourceImage = imagecreatefrompng($sourcePath);
        } elseif ($mime === 'image/webp') {
            $sourceImage = imagecreatefromwebp($sourcePath);
        } else {
            throw ValidationException::withMessages([
                $field => 'Format gambar tidak didukung.',
            ]);
        }

        if (!$sourceImage) {
            throw ValidationException::withMessages([
                $field => 'Gambar gagal diproses.',
            ]);
        }

        $targetWidth = $width;
        $targetHeight = $height;
        $maxDimension = 1280;

        if ($targetWidth > $maxDimension || $targetHeight > $maxDimension) {
            if ($targetWidth >= $targetHeight) {
                $targetHeight = (int) round($targetHeight * ($maxDimension / $targetWidth));
                $targetWidth = $maxDimension;
            } else {
                $targetWidth = (int) round($targetWidth * ($maxDimension / $targetHeight));
                $targetHeight = $maxDimension;
            }
        }

        $namaFile = $field . '-' . time() . '-' . Str::random(6) . '.jpg';
        $relativePath = $folder . '/' . $namaFile;
        $fullPath = public_path($relativePath);

        $quality = 82;
        $fileSize = PHP_INT_MAX;

        do {
            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);

            imagecopyresampled(
                $canvas,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $width,
                $height
            );

            imagejpeg($canvas, $fullPath, $quality);
            imagedestroy($canvas);

            clearstatcache(true, $fullPath);
            $fileSize = is_file($fullPath) ? filesize($fullPath) : PHP_INT_MAX;

            if ($fileSize <= $maxBytes) {
                break;
            }

            if ($quality > 50) {
                $quality -= 8;
            } else {
                $targetWidth = (int) round($targetWidth * 0.88);
                $targetHeight = (int) round($targetHeight * 0.88);
            }
        } while ($fileSize > $maxBytes && $targetWidth > 700 && $targetHeight > 700);

        imagedestroy($sourceImage);

        if (!is_file($fullPath) || filesize($fullPath) > $maxBytes) {
            @unlink($fullPath);

            throw ValidationException::withMessages([
                $field => 'Gambar terlalu besar dan tidak berhasil dikompres di bawah 2 MB. Mohon upload foto dengan ukuran lebih ringan.',
            ]);
        }

        return $relativePath;
    }

    private function hapusFileUploadGagal(array $paths): void
    {
        foreach ($paths as $path) {
            $path = ltrim((string) $path, '/');

            if ($path !== '' && is_file(public_path($path))) {
                @unlink(public_path($path));
            }

            if ($path !== '' && is_file(storage_path('app/public/' . $path))) {
                @unlink(storage_path('app/public/' . $path));
            }
        }
    }

    private function ambilKelasDiniyahPpdb(): array
    {
        $fallback = ['IBTIDAIYAH INDUK PA'];

        if (!Schema::hasTable('data_kelas_diniyah')) {
            return $fallback;
        }

        $columns = Schema::getColumnListing('data_kelas_diniyah');

        $kolomNama = collect([
            'nama_kelas_diniyah',
            'kelas_diniyah',
            'nama_kelas',
            'kelas',
            'nama',
        ])->first(fn($kolom) => in_array($kolom, $columns, true));

        if (!$kolomNama) {
            return $fallback;
        }

        $queryIbtidaiyah = DB::table('data_kelas_diniyah')
            ->where($kolomNama, 'like', '%IBTIDAIYAH%')
            ->orderBy($kolomNama);

        $data = $queryIbtidaiyah
            ->pluck($kolomNama)
            ->filter()
            ->map(fn($item) => trim((string) $item))
            ->unique()
            ->values()
            ->toArray();

        if (!empty($data)) {
            return $data;
        }

        $data = DB::table('data_kelas_diniyah')
            ->orderBy($kolomNama)
            ->pluck($kolomNama)
            ->filter()
            ->map(fn($item) => trim((string) $item))
            ->unique()
            ->values()
            ->toArray();

        return !empty($data) ? $data : $fallback;
    }

    private function defaultKelasDiniyah(): string
    {
        $kelas = $this->ambilKelasDiniyahPpdb();

        return $kelas[0] ?? 'IBTIDAIYAH INDUK PA';
    }

    private function tahunAjaranAktif(): string
    {
        $tahun = (int) now()->format('Y');

        return $tahun . '/' . ($tahun + 1);
    }

    private function filterKolomPpdb(array $data): array
    {
        $columns = Schema::getColumnListing('ppdb_daftar');

        return collect($data)
            ->only($columns)
            ->toArray();
    }
}
