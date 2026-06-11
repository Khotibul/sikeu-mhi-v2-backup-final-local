<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PpdbController extends Controller
{
    private string $table = 'ppdb_daftar';

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $tahunAjaran = $request->get('tahun_ajaran', 'semua');
        $statusSeleksi = $request->get('status_seleksi', 'semua');
        $unit = strtoupper(trim((string) $request->get('unit', 'semua')));

        $query = DB::table($this->table);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_daftar', 'like', '%' . $search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%')
                    ->orWhere('asal_sekolah', 'like', '%' . $search . '%')
                    ->orWhere('nama_ayah', 'like', '%' . $search . '%')
                    ->orWhere('nama_ibu', 'like', '%' . $search . '%')
                    ->orWhere('no_hp_ortu', 'like', '%' . $search . '%')
                    ->orWhere('jenjang_sekolah', 'like', '%' . $search . '%')
                    ->orWhere('jurusan', 'like', '%' . $search . '%')
                    ->orWhere('kelas_diniyah', 'like', '%' . $search . '%');
            });
        }

        if ($tahunAjaran !== 'semua' && $tahunAjaran !== '') {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($statusSeleksi !== 'semua' && $statusSeleksi !== '') {
            $query->where('status_seleksi', $statusSeleksi);
        }

        if ($unit !== 'SEMUA' && $unit !== '') {
            // Accept exact match or values that start with the selected unit (e.g. "SMP - A")
            $query->whereRaw('UPPER(TRIM(jenjang_sekolah)) LIKE ?', [$unit . '%']);
        }

        $ppdbs = $query
            ->orderByDesc('tgl_daftar')
            ->orderByDesc('id_daftar')
            ->paginate(25)
            ->withQueryString();

        $summaryQuery = DB::table($this->table);

        if ($tahunAjaran !== 'semua' && $tahunAjaran !== '') {
            $summaryQuery->where('tahun_ajaran', $tahunAjaran);
        }

        if ($unit !== 'SEMUA' && $unit !== '') {
            $summaryQuery->whereRaw('UPPER(TRIM(jenjang_sekolah)) LIKE ?', [$unit . '%']);
        }

        $summaryRows = $summaryQuery->get();

        $totalPendaftar = $summaryRows->count();
        $totalPending = $summaryRows->where('status_seleksi', 'Pending')->count();
        $totalDiterima = $summaryRows->where('status_seleksi', 'Diterima')->count();
        $totalDitolak = $summaryRows->where('status_seleksi', 'Ditolak')->count();

        $tahunAjaranList = DB::table($this->table)
            ->select('tahun_ajaran')
            ->whereNotNull('tahun_ajaran')
            ->where('tahun_ajaran', '!=', '')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $unitOptions = [
            'MTS' => 'MTs',
            'SMP' => 'SMP',
            'MA' => 'MA',
            'SMK' => 'SMK',
        ];

        return view('ppdb.index', compact(
            'ppdbs',
            'search',
            'tahunAjaran',
            'statusSeleksi',
            'unit',
            'tahunAjaranList',
            'unitOptions',
            'totalPendaftar',
            'totalPending',
            'totalDiterima',
            'totalDitolak'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePpdb($request);

        if (empty($validated['no_daftar'])) {
            $validated['no_daftar'] = $this->generateNoDaftar($validated['tahun_ajaran'] ?? null);
        }

        $validated['tgl_daftar'] = $validated['tgl_daftar'] ?? now()->toDateString();
        $validated['status_seleksi'] = $validated['status_seleksi'] ?? 'Pending';

        foreach ($this->fileFields() as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $this->uploadPpdbFile($request, $field);
            }
        }

        $validated = $this->normalizeData($validated);
        $validated = $this->withTimestamps($validated, true);

        DB::table($this->table)->insert($this->filterColumns($this->table, $validated));

        return redirect()
            ->route('ppdb.index')
            ->with('success', 'Data pendaftaran PPDB berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $pendaftar = DB::table($this->table)
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar) {
            abort(404);
        }

        $validated = $this->validatePpdb($request, $id);

        if (empty($validated['no_daftar'])) {
            $validated['no_daftar'] = $pendaftar->no_daftar ?: $this->generateNoDaftar($validated['tahun_ajaran'] ?? $pendaftar->tahun_ajaran ?? null);
        }

        foreach ($this->fileFields() as $field) {
            if ($request->hasFile($field)) {
                $this->deletePpdbFile($pendaftar->{$field} ?? null);
                $validated[$field] = $this->uploadPpdbFile($request, $field);
            } else {
                unset($validated[$field]);
            }
        }

        $validated = $this->normalizeData($validated);
        $validated = $this->withTimestamps($validated, false);

        DB::table($this->table)
            ->where('id_daftar', $id)
            ->update($this->filterColumns($this->table, $validated));

        return redirect()
            ->route('ppdb.index', [
                'search' => $request->get('search'),
                'tahun_ajaran' => $request->get('filter_tahun_ajaran'),
                'status_seleksi' => $request->get('filter_status_seleksi'),
                'unit' => $request->get('filter_unit'),
            ])
            ->with('success', 'Data pendaftaran PPDB berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pendaftar = DB::table($this->table)
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar) {
            abort(404);
        }

        foreach ($this->fileFields() as $field) {
            $this->deletePpdbFile($pendaftar->{$field} ?? null);
        }

        DB::table($this->table)
            ->where('id_daftar', $id)
            ->delete();

        return redirect()
            ->route('ppdb.index')
            ->with('success', 'Data pendaftaran PPDB berhasil dihapus.');
    }

    public function cetak($id)
    {
        $pendaftar = DB::table($this->table)
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar) {
            abort(404);
        }

        return view('ppdb.cetak', compact('pendaftar'));
    }

    public function terimaSantri($id)
    {
        $pendaftar = DB::table($this->table)
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar) {
            abort(404);
        }

        if ($this->sudahMasukSiswa($pendaftar)) {
            DB::table($this->table)
                ->where('id_daftar', $id)
                ->update($this->filterColumns($this->table, $this->withTimestamps([
                    'status_seleksi' => 'Diterima',
                ], false)));

            return redirect()
                ->route('ppdb.index')
                ->with('error', 'Data ini sudah terdaftar di Data Santri.');
        }

        DB::beginTransaction();

        try {
            $idSiswa = DB::table('siswa')->insertGetId(
                $this->filterColumns('siswa', $this->dataSantriDariPpdb($pendaftar))
            );

            DB::table($this->table)
                ->where('id_daftar', $id)
                ->update($this->filterColumns($this->table, $this->withTimestamps([
                    'status_seleksi' => 'Diterima',
                ], false)));

            DB::commit();

            return redirect()
                ->route('siswa.index')
                ->with('success', 'Calon santri berhasil diterima dan dipindahkan ke Data Santri.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('ppdb.index')
                ->with('error', 'Gagal memindahkan ke Data Santri: ' . $e->getMessage());
        }
    }


    public function berkas(Request $request, $id, string $field)
    {
        if (!in_array($field, $this->fileFields(), true)) {
            abort(404);
        }

        $pendaftar = DB::table($this->table)
            ->where('id_daftar', $id)
            ->first();

        if (!$pendaftar || empty($pendaftar->{$field})) {
            abort(404);
        }

        $filePath = $this->resolvePpdbFilePath($pendaftar->{$field});

        if (!$filePath || !File::exists($filePath) || !File::isFile($filePath)) {
            abort(404, 'Berkas tidak ditemukan di server.');
        }

        $downloadName = basename($filePath);

        if ($request->boolean('download')) {
            return response()->download($filePath, $downloadName);
        }

        return response()->file($filePath);
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $tahunAjaran = $request->get('tahun_ajaran', 'semua');
        $statusSeleksi = $request->get('status_seleksi', 'semua');
        $unit = strtoupper(trim((string) $request->get('unit', 'semua')));
        $format = strtolower($request->get('format', 'csv')); // csv atau xlsx

        $query = DB::table($this->table);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_daftar', 'like', '%' . $search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%')
                    ->orWhere('asal_sekolah', 'like', '%' . $search . '%')
                    ->orWhere('nama_ayah', 'like', '%' . $search . '%')
                    ->orWhere('nama_ibu', 'like', '%' . $search . '%')
                    ->orWhere('no_hp_ortu', 'like', '%' . $search . '%')
                    ->orWhere('jenjang_sekolah', 'like', '%' . $search . '%')
                    ->orWhere('jurusan', 'like', '%' . $search . '%')
                    ->orWhere('kelas_diniyah', 'like', '%' . $search . '%');
            });
        }

        if ($tahunAjaran !== 'semua' && $tahunAjaran !== '') {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($statusSeleksi !== 'semua' && $statusSeleksi !== '') {
            $query->where('status_seleksi', $statusSeleksi);
        }

        if ($unit !== 'SEMUA' && $unit !== '') {
            $query->whereRaw('UPPER(TRIM(jenjang_sekolah)) LIKE ?', [$unit . '%']);
        }

        $data = $query
            ->orderByDesc('tgl_daftar')
            ->orderByDesc('id_daftar')
            ->get();

        if ($format === 'xlsx') {
            return $this->exportExcel($data);
        }

        return $this->exportCsv($data);
    }

    private function exportCsv($data)
    {
        $filename = 'ppdb-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $output = fopen('php://output', 'w');

            // Tambahkan BOM untuk UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($output, [
                'No',
                'No. Daftar',
                'Nama Lengkap',
                'NISN',
                'JK',
                'Tempat Lahir',
                'Tgl Lahir',
                'Alamat',
                'Asal Sekolah',
                'Nama Ayah',
                'Nama Ibu',
                'No HP Ortu',
                'Jenjang Sekolah',
                'Jurusan',
                'Kelas Diniyah',
                'Status Pondok',
                'Status Seleksi',
                'Tahun Ajaran',
                'Tgl Daftar',
            ]);

            // Data
            foreach ($data as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item->no_daftar ?? '',
                    $item->nama_lengkap ?? '',
                    $item->nisn ?? '',
                    $item->jk ?? '',
                    $item->tempat_lahir ?? '',
                    $item->tgl_lahir ?? '',
                    $item->alamat ?? '',
                    $item->asal_sekolah ?? '',
                    $item->nama_ayah ?? '',
                    $item->nama_ibu ?? '',
                    $item->no_hp_ortu ?? '',
                    $item->jenjang_sekolah ?? '',
                    $item->jurusan ?? '',
                    $item->kelas_diniyah ?? '',
                    $item->status_pondok ?? '',
                    $item->status_seleksi ?? '',
                    $item->tahun_ajaran ?? '',
                    $item->tgl_daftar ?? '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($data)
    {
        // Jika library maatwebsite/excel terinstall, gunakan itu
        // Untuk sekarang, export sebagai CSV dengan nama .xlsx
        // Atau gunakan implementasi sederhana dengan PhpSpreadsheet jika tersedia

        $filename = 'ppdb-' . now()->format('Ymd-His') . '.xlsx';

        // Try to use PhpOffice\PhpSpreadsheet if available
        try {
            if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                return $this->exportExcelWithPhpSpreadsheet($data, $filename);
            }
        } catch (\Throwable $e) {
            // Fallback ke CSV
        }

        // Fallback: export sebagai CSV
        return $this->exportCsvAsExcel($data, $filename);
    }

    private function exportExcelWithPhpSpreadsheet($data, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'No',
            'No. Daftar',
            'Nama Lengkap',
            'NISN',
            'JK',
            'Tempat Lahir',
            'Tgl Lahir',
            'Alamat',
            'Asal Sekolah',
            'Nama Ayah',
            'Nama Ibu',
            'No HP Ortu',
            'Jenjang Sekolah',
            'Jurusan',
            'Kelas Diniyah',
            'Status Pondok',
            'Status Seleksi',
            'Tahun Ajaran',
            'Tgl Daftar',
        ];

        // Write headers
        $sheet->fromArray($headers, null, 'A1');

        // Write data
        $rowNum = 2;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->no_daftar ?? '',
                $item->nama_lengkap ?? '',
                $item->nisn ?? '',
                $item->jk ?? '',
                $item->tempat_lahir ?? '',
                $item->tgl_lahir ?? '',
                $item->alamat ?? '',
                $item->asal_sekolah ?? '',
                $item->nama_ayah ?? '',
                $item->nama_ibu ?? '',
                $item->no_hp_ortu ?? '',
                $item->jenjang_sekolah ?? '',
                $item->jurusan ?? '',
                $item->kelas_diniyah ?? '',
                $item->status_pondok ?? '',
                $item->status_seleksi ?? '',
                $item->tahun_ajaran ?? '',
                $item->tgl_daftar ?? '',
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '12A99A']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:S1')->applyFromArray($headerStyle);

        // Auto width
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportCsvAsExcel($data, $filename)
    {
        // Export CSV dengan UTF-8 BOM agar terbuka dengan baik di Excel
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, [
                'No',
                'No. Daftar',
                'Nama Lengkap',
                'NISN',
                'JK',
                'Tempat Lahir',
                'Tgl Lahir',
                'Alamat',
                'Asal Sekolah',
                'Nama Ayah',
                'Nama Ibu',
                'No HP Ortu',
                'Jenjang Sekolah',
                'Jurusan',
                'Kelas Diniyah',
                'Status Pondok',
                'Status Seleksi',
                'Tahun Ajaran',
                'Tgl Daftar',
            ]);

            foreach ($data as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item->no_daftar ?? '',
                    $item->nama_lengkap ?? '',
                    $item->nisn ?? '',
                    $item->jk ?? '',
                    $item->tempat_lahir ?? '',
                    $item->tgl_lahir ?? '',
                    $item->alamat ?? '',
                    $item->asal_sekolah ?? '',
                    $item->nama_ayah ?? '',
                    $item->nama_ibu ?? '',
                    $item->no_hp_ortu ?? '',
                    $item->jenjang_sekolah ?? '',
                    $item->jurusan ?? '',
                    $item->kelas_diniyah ?? '',
                    $item->status_pondok ?? '',
                    $item->status_seleksi ?? '',
                    $item->tahun_ajaran ?? '',
                    $item->tgl_daftar ?? '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function validatePpdb(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'no_daftar' => 'nullable|string|max:20',
            'tgl_daftar' => 'nullable|date',
            'nama_lengkap' => 'required|string|max:100',
            'nisn' => 'nullable|string|max:20',
            'jk' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:50',
            'tgl_lahir' => 'required|date',
            'alamat' => 'required|string',
            'asal_sekolah' => 'nullable|string|max:100',
            'nama_ayah' => 'required|string|max:100',
            'nama_ibu' => 'nullable|string|max:100',
            'no_hp_ortu' => 'required|string|max:20',
            'jenjang_sekolah' => 'required|string|max:50',
            'jurusan' => 'nullable|string|max:50',
            'kelas_diniyah' => 'required|string|max:50',
            'status_pondok' => 'required|in:Ya,Tidak',
            'status_seleksi' => 'nullable|in:Pending,Diterima,Ditolak',
            'tahun_ajaran' => 'required|string|max:15',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'file_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'file_ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
    }

    private function normalizeData(array $data): array
    {
        $upperFields = ['jk', 'status_pondok', 'status_seleksi'];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        if (($data['status_seleksi'] ?? '') === '') {
            $data['status_seleksi'] = 'Pending';
        }

        if (($data['jenjang_sekolah'] ?? '') !== 'SMK') {
            // Jurusan hanya dipakai untuk SMK/MA. Data lama tetap aman di halaman admin,
            // tetapi pendaftaran online SMK dibatasi di controller PPDB online.
        }

        return $data;
    }

    private function generateNoDaftar(?string $tahunAjaran = null): string
    {
        $year = now()->format('Y');
        $prefix = 'PPDB-' . $year . '-';

        $last = DB::table($this->table)
            ->where('no_daftar', 'like', $prefix . '%')
            ->orderByDesc('id_daftar')
            ->value('no_daftar');

        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last, $match)) {
            $next = ((int) $match[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function fileFields(): array
    {
        return ['file_kk', 'file_ktp', 'file_foto', 'file_ijazah'];
    }

    private function uploadPpdbFile(Request $request, string $field): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);

        if (!$file->isValid()) {
            return null;
        }

        $folder = public_path('uploads/ppdb');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = $field . '-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $extension;

        $file->move($folder, $filename);

        return 'uploads/ppdb/' . $filename;
    }


    private function resolvePpdbFilePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = ltrim((string) $path, '/');
        $path = str_replace('\\', '/', $path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return null;
        }

        $cleanPath = $path;
        $cleanPath = preg_replace('#^public/#', '', $cleanPath);
        $cleanPath = preg_replace('#^storage/#', '', $cleanPath);

        $candidates = [
            public_path($path),
            public_path($cleanPath),
            storage_path('app/public/' . $path),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $path),
            storage_path('app/' . $cleanPath),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && File::exists($candidate) && File::isFile($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function deletePpdbFile(?string $path): void
    {
        $fullPath = $this->resolvePpdbFilePath($path);

        if ($fullPath && File::exists($fullPath) && File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function sudahMasukSiswa($pendaftar): bool
    {
        $query = DB::table('siswa');

        if (!empty($pendaftar->nisn) && Schema::hasColumn('siswa', 'nisn')) {
            $query->where('nisn', $pendaftar->nisn);
        } else {
            $query->where('nama_siswa', $pendaftar->nama_lengkap);
        }

        return $query->exists();
    }

    private function dataSantriDariPpdb($pendaftar): array
    {
        $nama = $pendaftar->nama_lengkap;
        $nis = $this->generateNisSantri();

        $statusMukim = ($pendaftar->status_pondok ?? 'Tidak') === 'Ya' ? 'mukim' : 'tidak mukim';

        $data = [
            'nis' => $nis,
            'nisn' => $pendaftar->nisn,
            'nama_siswa' => $nama,
            'nama_lengkap' => $nama,
            'jk' => $pendaftar->jk,
            'jenis_kelamin' => $pendaftar->jk === 'L' ? 'Laki-laki' : 'Perempuan',
            'tempat_lahir' => $pendaftar->tempat_lahir,
            'tgl_lahir' => $pendaftar->tgl_lahir,
            'tanggal_lahir' => $pendaftar->tgl_lahir,
            'alamat' => $pendaftar->alamat,
            'asal_sekolah' => $pendaftar->asal_sekolah,
            'nama_ayah' => $pendaftar->nama_ayah,
            'nama_ibu' => $pendaftar->nama_ibu,
            'nama_wali' => $pendaftar->nama_ayah ?: $pendaftar->nama_ibu,
            'no_hp' => $pendaftar->no_hp_ortu,
            'no_hp_wali' => $pendaftar->no_hp_ortu,
            'kelas_formal' => trim(($pendaftar->jenjang_sekolah ?? '') . ' ' . ($pendaftar->jurusan ?? '')),
            'kelas_diniyah' => $pendaftar->kelas_diniyah,
            'status_mukim' => $statusMukim,
            'status' => 'Aktif',
            'tahun_ajaran' => $pendaftar->tahun_ajaran,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $this->fillRequiredDefaults('siswa', $data);
    }

    private function generateNisSantri(): string
    {
        if (!Schema::hasColumn('siswa', 'nis')) {
            return '';
        }

        $last = DB::table('siswa')
            ->whereNotNull('nis')
            ->where('nis', 'regexp', '^[0-9]+$')
            ->orderByRaw('CAST(nis AS UNSIGNED) DESC')
            ->value('nis');

        if (!$last) {
            return '1001';
        }

        return (string) ((int) $last + 1);
    }

    private function fillRequiredDefaults(string $table, array $data): array
    {
        $columns = Schema::getColumnListing($table);

        foreach ($columns as $column) {
            if (array_key_exists($column, $data)) {
                continue;
            }

            if (in_array($column, ['created_at', 'updated_at'], true)) {
                $data[$column] = now();
                continue;
            }

            if (in_array($column, ['status'], true)) {
                $data[$column] = 'Aktif';
                continue;
            }
        }

        return $data;
    }

    private function filterColumns(string $table, array $data): array
    {
        $columns = Schema::getColumnListing($table);

        return collect($data)
            ->only($columns)
            ->toArray();
    }

    private function withTimestamps(array $data, bool $isCreate): array
    {
        if (Schema::hasColumn($this->table, 'updated_at')) {
            $data['updated_at'] = now();
        }

        if ($isCreate && Schema::hasColumn($this->table, 'created_at')) {
            $data['created_at'] = now();
        }

        return $data;
    }
}
