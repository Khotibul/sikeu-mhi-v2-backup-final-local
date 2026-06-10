<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('siswa');

        $this->applyAdminUnitScope($query);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%')
                    ->orWhere('nama_wali', 'like', '%' . $search . '%')
                    ->orWhere('nama_ibu', 'like', '%' . $search . '%')
                    ->orWhere('no_hp', 'like', '%' . $search . '%');
            });
        }

        foreach (['kelas_formal', 'kelas_diniyah', 'status_mukim', 'status_aktif'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->{$filter});
            }
        }

        $siswas = $query
            ->orderByRaw("CAST(NULLIF(nis, '') AS UNSIGNED) ASC")
            ->orderBy('nama_siswa')
            ->paginate(20)
            ->appends($request->query());

        $kelasFormal = $this->ambilKelasFormal();
        $kelasDiniyah = $this->ambilKelasDiniyah();

        $totalSantri = DB::table('siswa')->count();
        $totalAktif = DB::table('siswa')->where('status_aktif', 'Aktif')->count();

        return view('siswa.index', compact(
            'siswas',
            'kelasFormal',
            'kelasDiniyah',
            'totalSantri',
            'totalAktif'
        ));
    }

    public function create()
    {
        $nisOtomatis = $this->previewNisOtomatis();
        $kelasFormal = $this->ambilKelasFormal();
        $kelasDiniyah = $this->ambilKelasDiniyah();

        return view('siswa.create', compact('nisOtomatis', 'kelasFormal', 'kelasDiniyah'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rulesSiswa());

        $draftData = $this->dataSiswaDariRequest($request);
        $submitKey = 'siswa_store_' . sha1(json_encode([
            'admin' => session('admin_id') ?? 'guest',
            'nama_siswa' => strtolower($draftData['nama_siswa'] ?? ''),
            'nisn' => strtolower($draftData['nisn'] ?? ''),
            'tgl_lahir' => $draftData['tgl_lahir'] ?? '',
            'nama_wali' => strtolower($draftData['nama_wali'] ?? ''),
            'nama_ibu' => strtolower($draftData['nama_ibu'] ?? ''),
            'no_hp' => preg_replace('/\D+/', '', (string) ($draftData['no_hp'] ?? '')),
            'tahun_ajaran' => $draftData['tahun_ajaran'] ?? '',
        ]));

        // Anti double submit: jika tombol terkirim 2 kali dalam waktu dekat,
        // request kedua tidak akan membuat data santri baru.
        if (!Cache::add($submitKey, true, 60)) {
            return redirect()->route('siswa.index')
                ->with('success', 'Data santri sedang/sudah diproses. Sistem mencegah input ganda.');
        }

        try {
            $result = DB::transaction(function () use ($request, $draftData) {
                $duplikat = $this->cariDuplikatSiswa($draftData);

                if ($duplikat) {
                    return [
                        'status' => 'duplikat',
                        'siswa' => $duplikat,
                    ];
                }

                $foto = null;

                if ($request->hasFile('foto')) {
                    $foto = $request->file('foto')->store('foto_siswa', 'public');
                }

                $data = $draftData;
                $data['nis'] = $this->generateNisOtomatis();

                if ($foto) {
                    $data['foto'] = $foto;
                }

                DB::table('siswa')->insert($this->filterKolomSiswa($data));

                return [
                    'status' => 'tersimpan',
                    'siswa' => null,
                ];
            });

            if (($result['status'] ?? null) === 'duplikat') {
                return redirect()->route('siswa.index')
                    ->with('error', 'Data santri tidak disimpan karena terdeteksi sudah ada. Sistem mencegah data ganda.');
            }

            return redirect()->route('siswa.index')
                ->with('success', 'Data santri berhasil ditambahkan. NIS dibuat otomatis oleh sistem.');
        } catch (\Throwable $e) {
            Cache::forget($submitKey);

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan santri: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $siswa = DB::table('siswa')->where('id_siswa', $id)->first();

        if (!$siswa) {
            abort(404);
        }

        $kelasFormal = $this->ambilKelasFormal();
        $kelasDiniyah = $this->ambilKelasDiniyah();

        return view('siswa.edit', compact('siswa', 'kelasFormal', 'kelasDiniyah'));
    }

    public function update(Request $request, $id)
    {
        $siswa = DB::table('siswa')->where('id_siswa', $id)->first();

        if (!$siswa) {
            abort(404);
        }

        $request->validate($this->rulesSiswa(true));

        $data = [
            'nisn' => $this->nullableTeks($request->nisn),
            'nama_siswa' => $this->wajibTeks($request->nama_siswa),
            'jk' => $this->nullableTeks($request->jk),
            'tempat_lahir' => $this->nullableTeks($request->tempat_lahir),
            'tgl_lahir' => $request->tgl_lahir,
            'alamat' => $this->nullableTeks($request->alamat),
            'asal_sekolah' => $this->nullableTeks($request->asal_sekolah),
            'kelas_formal' => $this->nullableTeks($request->kelas_formal),
            'kelas_diniyah' => $this->wajibTeks($request->kelas_diniyah, '-'),
            'nama_wali' => $this->wajibTeks($request->nama_wali, '-'),
            'nama_ibu' => $this->nullableTeks($request->nama_ibu),
            'no_hp' => $this->wajibTeks($request->no_hp, '-'),
            'status_mukim' => $this->wajibTeks($request->status_mukim, 'Asrama'),
            'tahun_ajaran' => $this->nullableTeks($request->tahun_ajaran),
            'potongan_formal' => (int) ($request->potongan_formal ?? 0),
            'potongan_diniyah' => (int) ($request->potongan_diniyah ?? 0),
            'status_aktif' => $request->status_aktif ?: 'Aktif',
        ];

        // NIS sengaja tidak diupdate.

        if ($request->hasFile('foto')) {
            if (!empty($siswa->foto) && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $data['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        DB::table('siswa')->where('id_siswa', $id)->update($this->filterKolomSiswa($data));

        return redirect()->route('siswa.index')
            ->with('success', 'Data santri berhasil diperbarui. NIS tetap tidak berubah.');
    }

    public function destroy($id)
    {
        $siswa = DB::table('siswa')->where('id_siswa', $id)->first();

        if (!$siswa) {
            abort(404);
        }

        $punyaTransaksi = DB::table('pembayaran')->where('id_siswa', $id)->exists()
            || DB::table('pembayaran_diniyah')->where('id_siswa', $id)->exists()
            || DB::table('pembayaran_pangkal')->where('id_siswa', $id)->exists();

        if ($punyaTransaksi) {
            return back()->with('error', 'Santri tidak dapat dihapus karena sudah memiliki riwayat pembayaran.');
        }

        if (!empty($siswa->foto) && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        DB::table('siswa')->where('id_siswa', $id)->delete();

        return redirect()->route('siswa.index')->with('success', 'Data santri berhasil dihapus.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'data-santri-' . now()->format('Ymd-His') . '.csv';

        $query = DB::table('siswa');
        $this->applyAdminUnitScope($query);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        foreach (['kelas_formal', 'kelas_diniyah', 'status_mukim', 'status_aktif'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->{$filter});
            }
        }

        $siswas = $query
            ->orderByRaw("CAST(NULLIF(nis, '') AS UNSIGNED) ASC")
            ->orderBy('nama_siswa')
            ->get();

        return response()->stream(function () use ($siswas) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'nis',
                'nisn',
                'nama_siswa',
                'jk',
                'tempat_lahir',
                'tgl_lahir',
                'alamat',
                'asal_sekolah',
                'kelas_formal',
                'kelas_diniyah',
                'nama_wali',
                'nama_ibu',
                'no_hp',
                'status_mukim',
                'tahun_ajaran',
                'potongan_formal',
                'potongan_diniyah',
                'status_aktif',
            ]);

            foreach ($siswas as $siswa) {
                fputcsv($handle, [
                    $siswa->nis ?? '',
                    $siswa->nisn ?? '',
                    $siswa->nama_siswa ?? '',
                    $siswa->jk ?? '',
                    $siswa->tempat_lahir ?? '',
                    $siswa->tgl_lahir ?? '',
                    $siswa->alamat ?? '',
                    $siswa->asal_sekolah ?? '',
                    $siswa->kelas_formal ?? '',
                    $siswa->kelas_diniyah ?? '',
                    $siswa->nama_wali ?? '',
                    $siswa->nama_ibu ?? '',
                    $siswa->no_hp ?? '',
                    $siswa->status_mukim ?? '',
                    $siswa->tahun_ajaran ?? '',
                    $siswa->potongan_formal ?? 0,
                    $siswa->potongan_diniyah ?? 0,
                    $siswa->status_aktif ?? '',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function templateImport()
    {
        $fileName = 'template-import-santri.xls';

        $columns = [
            'nisn',
            'nama_siswa',
            'jk',
            'tempat_lahir',
            'tgl_lahir',
            'alamat',
            'asal_sekolah',
            'kelas_formal',
            'kelas_diniyah',
            'nama_wali',
            'nama_ibu',
            'no_hp',
            'status_mukim',
            'tahun_ajaran',
            'potongan_formal',
            'potongan_diniyah',
            'status_aktif',
        ];

        $example = [
            '1234567890',
            'CONTOH NAMA SANTRI',
            'L',
            'Jember',
            '2010-01-01',
            'Alamat lengkap santri',
            'SD/MI Asal',
            '7 MTS',
            'IBTIDAIYAH INDUK PA',
            'Nama Ayah/Wali',
            'Nama Ibu',
            '08123456789',
            'Mukim',
            '2026/2027',
            '120000',
            '65000',
            'Aktif',
        ];

        $html = '<html><head><meta charset="UTF-8"><style>
            table{border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:12px}
            th{background:#0f766e;color:#fff;font-weight:bold;border:1px solid #0f766e;padding:8px;text-align:left}
            td{border:1px solid #d1d5db;padding:8px;mso-number-format:"\@"}
            .title{background:#e0f2f1;color:#0f766e;font-size:18px;font-weight:bold}
            .note{background:#fef3c7;color:#92400e;font-weight:bold}
        </style></head><body>';

        $html .= '<table>';
        $html .= '<tr><td colspan="' . count($columns) . '" class="title">TEMPLATE IMPORT DATA SANTRI</td></tr>';
        $html .= '<tr><td colspan="' . count($columns) . '" class="note">PETUNJUK: Kolom NIS tidak ada karena NIS dibuat otomatis oleh sistem. Kolom potongan_formal dan potongan_diniyah dipakai sebagai NOMINAL SPP KHUSUS bulanan, bukan pengurang. Isi 0 jika santri ikut nominal default kelas. Setelah selesai, simpan menjadi CSV sebelum import.</td></tr>';

        $html .= '<tr>';
        foreach ($columns as $column) {
            $html .= '<th>' . htmlspecialchars($column) . '</th>';
        }
        $html .= '</tr>';

        $html .= '<tr>';
        foreach ($example as $value) {
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
        }
        $html .= '</tr>';

        $html .= '</table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path = $request->file('file_import')->getRealPath();
        $firstLineHandle = fopen($path, 'r');
        $firstLine = $firstLineHandle ? fgets($firstLineHandle) : '';
        if ($firstLineHandle) {
            fclose($firstLineHandle);
        }

        $delimiter = $this->detectDelimiter($firstLine ?: '');
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->with('error', 'File import tidak bisa dibaca.');
        }

        $header = fgetcsv($handle, 0, $delimiter);

        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File import kosong atau format tidak sesuai.');
        }

        $header = array_map(function ($item) {
            $item = preg_replace('/^\xEF\xBB\xBF/', '', (string) $item);
            $item = strtolower(trim($item));
            return str_replace(' ', '_', $item);
        }, $header);

        $berhasil = 0;
        $dilewati = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count(array_filter($row, fn($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $rowData = [];
                foreach ($header as $index => $key) {
                    $rowData[$key] = trim((string) ($row[$index] ?? ''));
                }

                if (empty($rowData['nama_siswa'])) {
                    $dilewati++;
                    continue;
                }

                if (!empty($rowData['nisn'])) {
                    $sudahAda = DB::table('siswa')->where('nisn', $rowData['nisn'])->exists();
                    if ($sudahAda) {
                        $dilewati++;
                        continue;
                    }
                }

                $data = [
                    'nis' => $this->generateNisOtomatis(),
                    'nisn' => $this->nullableTeks($rowData['nisn'] ?? null),
                    'nama_siswa' => $this->wajibTeks($rowData['nama_siswa'] ?? null),
                    'jk' => $this->normalisasiJk($rowData['jk'] ?? null),
                    'tempat_lahir' => $this->nullableTeks($rowData['tempat_lahir'] ?? null),
                    'tgl_lahir' => $this->normalisasiTanggal($rowData['tgl_lahir'] ?? null),
                    'alamat' => $this->nullableTeks($rowData['alamat'] ?? null),
                    'asal_sekolah' => $this->nullableTeks($rowData['asal_sekolah'] ?? null),
                    'kelas_formal' => $this->nullableTeks($rowData['kelas_formal'] ?? null),
                    'kelas_diniyah' => $this->wajibTeks($rowData['kelas_diniyah'] ?? null, '-'),
                    'nama_wali' => $this->wajibTeks($rowData['nama_wali'] ?? null, '-'),
                    'nama_ibu' => $this->nullableTeks($rowData['nama_ibu'] ?? null),
                    'no_hp' => $this->wajibTeks($rowData['no_hp'] ?? null, '-'),
                    'status_mukim' => $this->wajibTeks($rowData['status_mukim'] ?? null, 'Asrama'),
                    'tahun_ajaran' => $this->nullableTeks($rowData['tahun_ajaran'] ?? null),
                    'potongan_formal' => (int) ($rowData['potongan_formal'] ?? 0),
                    'potongan_diniyah' => (int) ($rowData['potongan_diniyah'] ?? 0),
                    'status_aktif' => $rowData['status_aktif'] ?? 'Aktif',
                ];

                if ($this->cariDuplikatSiswa($data)) {
                    $dilewati++;
                    continue;
                }

                DB::table('siswa')->insert($this->filterKolomSiswa($data));
                $berhasil++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        fclose($handle);

        return redirect()->route('siswa.index')
            ->with('success', 'Import selesai. Berhasil: ' . $berhasil . ' data. Dilewati: ' . $dilewati . ' data.');
    }

    private function dataSiswaDariRequest(Request $request): array
    {
        return [
            'nisn' => $this->nullableTeks($request->nisn),
            'nama_siswa' => $this->wajibTeks($request->nama_siswa),
            'jk' => $this->nullableTeks($request->jk),
            'tempat_lahir' => $this->nullableTeks($request->tempat_lahir),
            'tgl_lahir' => $request->tgl_lahir,
            'alamat' => $this->nullableTeks($request->alamat),
            'asal_sekolah' => $this->nullableTeks($request->asal_sekolah),
            'kelas_formal' => $this->nullableTeks($request->kelas_formal),
            'kelas_diniyah' => $this->wajibTeks($request->kelas_diniyah, '-'),
            'nama_wali' => $this->wajibTeks($request->nama_wali, '-'),
            'nama_ibu' => $this->nullableTeks($request->nama_ibu),
            'no_hp' => $this->wajibTeks($request->no_hp, '-'),
            'status_mukim' => $this->wajibTeks($request->status_mukim, 'Asrama'),
            'tahun_ajaran' => $this->nullableTeks($request->tahun_ajaran),
            'potongan_formal' => (int) ($request->potongan_formal ?? 0),
            'potongan_diniyah' => (int) ($request->potongan_diniyah ?? 0),
            'status_aktif' => $request->status_aktif ?: 'Aktif',
        ];
    }

    private function cariDuplikatSiswa(array $data)
    {
        // Cek NISN dulu karena paling kuat sebagai identitas.
        if (!empty($data['nisn'])) {
            $byNisn = DB::table('siswa')->where('nisn', $data['nisn'])->first();

            if ($byNisn) {
                return $byNisn;
            }
        }

        // Cek data yang sama persis. Ini mencegah kasus tombol submit terkirim dua kali.
        $query = DB::table('siswa')
            ->whereRaw('LOWER(TRIM(nama_siswa)) = ?', [strtolower(trim((string) ($data['nama_siswa'] ?? '')))]);

        $this->whereNullable($query, 'tgl_lahir', $data['tgl_lahir'] ?? null);
        $this->whereNullable($query, 'jk', $data['jk'] ?? null);
        $this->whereNullable($query, 'tempat_lahir', $data['tempat_lahir'] ?? null);
        $this->whereNullable($query, 'kelas_formal', $data['kelas_formal'] ?? null);
        $this->whereNullable($query, 'kelas_diniyah', $data['kelas_diniyah'] ?? null);
        $this->whereNullable($query, 'nama_wali', $data['nama_wali'] ?? null);
        $this->whereNullable($query, 'nama_ibu', $data['nama_ibu'] ?? null);
        $this->whereNullable($query, 'no_hp', $data['no_hp'] ?? null);
        $this->whereNullable($query, 'tahun_ajaran', $data['tahun_ajaran'] ?? null);

        return $query->first();
    }

    private function whereNullable($query, string $column, $value): void
    {
        if (!Schema::hasColumn('siswa', $column)) {
            return;
        }

        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $query->where(function ($q) use ($column) {
                $q->whereNull($column)->orWhere($column, '');
            });

            return;
        }

        $query->where($column, $value);
    }

    private function rulesSiswa(bool $isUpdate = false): array
    {
        return [
            'nama_siswa' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:30',
            'jk' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'asal_sekolah' => 'nullable|string|max:150',
            'kelas_formal' => 'nullable|string|max:100',
            'kelas_diniyah' => 'nullable|string|max:100',
            'nama_wali' => 'nullable|string|max:150',
            'nama_ibu' => 'nullable|string|max:150',
            'no_hp' => 'nullable|string|max:30',
            'status_mukim' => 'nullable|string|max:50',
            'tahun_ajaran' => 'nullable|string|max:20',
            'potongan_formal' => 'nullable|numeric|min:0',
            'potongan_diniyah' => 'nullable|numeric|min:0',
            'status_aktif' => 'nullable|string|max:30',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    private function previewNisOtomatis(): string
    {
        $lastNis = DB::table('siswa')
            ->whereNotNull('nis')
            ->where('nis', '!=', '')
            ->orderByRaw("CAST(NULLIF(nis, '') AS UNSIGNED) DESC")
            ->value('nis');

        return $this->nextNisFromLast($lastNis);
    }

    private function generateNisOtomatis(): string
    {
        $lastNis = DB::table('siswa')
            ->whereNotNull('nis')
            ->where('nis', '!=', '')
            ->lockForUpdate()
            ->orderByRaw("CAST(NULLIF(nis, '') AS UNSIGNED) DESC")
            ->value('nis');

        return $this->nextNisFromLast($lastNis);
    }

    private function nextNisFromLast(?string $lastNis): string
    {
        if (!$lastNis) {
            return '1';
        }

        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastNis);
        $nextNumber = $lastNumber + 1;
        $length = max(strlen((string) $lastNis), strlen((string) $nextNumber));

        return str_pad((string) $nextNumber, $length, '0', STR_PAD_LEFT);
    }

    private function nullableTeks($value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function wajibTeks($value, string $default = '-'): string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? $default : $value;
    }

    private function filterKolomSiswa(array $data): array
    {
        $columns = Schema::getColumnListing('siswa');

        return collect($data)->only($columns)->toArray();
    }

    private function ambilKelasFormal()
    {
        if (!Schema::hasTable('data_kelas')) {
            return collect();
        }

        return DB::table('data_kelas')->orderByRaw('1 ASC')->get();
    }

    private function ambilKelasDiniyah()
    {
        if (!Schema::hasTable('data_kelas_diniyah')) {
            return collect();
        }

        return DB::table('data_kelas_diniyah')->orderByRaw('1 ASC')->get();
    }

    private function applyAdminUnitScope($query): void
    {
        if (class_exists(\App\Support\AdminUnitScope::class)) {
            \App\Support\AdminUnitScope::applyToSiswaQuery($query);
        }
    }

    private function detectDelimiter(string $line): string
    {
        return substr_count($line, ';') > substr_count($line, ',') ? ';' : ',';
    }

    private function normalisasiJk(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = strtoupper(trim($value));

        if (in_array($value, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PUTRA'], true)) {
            return 'L';
        }

        if (in_array($value, ['P', 'PEREMPUAN', 'PUTRI'], true)) {
            return 'P';
        }

        return $value;
    }

    private function normalisasiTanggal(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            [$day, $month, $year] = explode('/', $value);
            return $year . '-' . $month . '-' . $day;
        }

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
            [$day, $month, $year] = explode('-', $value);
            return $year . '-' . $month . '-' . $day;
        }

        return $value;
    }
}
