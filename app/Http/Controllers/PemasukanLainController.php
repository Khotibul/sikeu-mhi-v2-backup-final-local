<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PemasukanLainController extends Controller
{
    private string $table = 'pemasukan_lain';
    private string $primaryKey = 'id_masuk';

    private function filterColumns(array $data): array
    {
        $columns = Schema::getColumnListing($this->table);

        return collect($data)
            ->only($columns)
            ->toArray();
    }

    private function nomorBukti(object $item): string
    {
        $tanggal = !empty($item->tanggal)
            ? date('Ym', strtotime($item->tanggal))
            : date('Ym');

        return 'BS-' . $tanggal . '-' . str_pad((string) ($item->id_masuk ?? 0), 4, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = DB::table($this->table . ' as pl')
            ->leftJoin('admin as a', 'a.id_admin', '=', 'pl.id_admin')
            ->select(
                'pl.*',
                'a.nama_lengkap as nama_admin',
                'a.username as username_admin'
            );

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('pl.nama_penyetor', 'like', '%' . $search . '%')
                    ->orWhere('pl.uraian', 'like', '%' . $search . '%')
                    ->orWhere('pl.nominal', 'like', '%' . $search . '%')
                    ->orWhere('pl.id_masuk', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tgl_awal')) {
            $query->whereDate('pl.tanggal', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('pl.tanggal', '<=', $request->tgl_akhir);
        }

        $totalNominal = (clone $query)->sum('pl.nominal');
        $totalTransaksi = (clone $query)->count();

        $items = $query
            ->orderByDesc('pl.tanggal')
            ->orderByDesc('pl.id_masuk')
            ->paginate(15)
            ->appends($request->query());

        return view('pembayaran-lain.bebas.index', compact(
            'items',
            'totalNominal',
            'totalTransaksi'
        ));
    }

    public function create()
    {
        $item = null;

        return view('pembayaran-lain.bebas.create', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_penyetor' => 'required|string|max:150',
            'uraian' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nama_penyetor.required' => 'Nama penyetor wajib diisi.',
            'uraian.required' => 'Uraian wajib diisi.',
            'nominal.required' => 'Nominal wajib diisi.',
        ]);

        $data = [
            'tanggal' => $validated['tanggal'],
            'nama_penyetor' => trim($validated['nama_penyetor']),
            'uraian' => trim($validated['uraian']),
            'nominal' => (int) $validated['nominal'],
            'id_admin' => session('admin_id'),
        ];

        if (Schema::hasColumn($this->table, 'created_at')) {
            $data['created_at'] = now();
        }

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $data['updated_at'] = now();
        }

        $id = DB::table($this->table)->insertGetId($this->filterColumns($data));

        return redirect()
            ->route('pembayaran-lain.bebas.cetak', $id)
            ->with('success', 'Setoran bebas berhasil disimpan. Silakan cetak bukti setor.');
    }

    public function edit($id)
    {
        $item = DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->first();

        if (!$item) {
            abort(404);
        }

        return view('pembayaran-lain.bebas.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->first();

        if (!$item) {
            abort(404);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_penyetor' => 'required|string|max:150',
            'uraian' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        $data = [
            'tanggal' => $validated['tanggal'],
            'nama_penyetor' => trim($validated['nama_penyetor']),
            'uraian' => trim($validated['uraian']),
            'nominal' => (int) $validated['nominal'],
            'id_admin' => session('admin_id') ?? $item->id_admin,
        ];

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->update($this->filterColumns($data));

        return redirect()
            ->route('pembayaran-lain.bebas.index')
            ->with('success', 'Setoran bebas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->first();

        if (!$item) {
            abort(404);
        }

        DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->delete();

        return redirect()
            ->route('pembayaran-lain.bebas.index')
            ->with('success', 'Setoran bebas berhasil dihapus.');
    }

    public function cetak($id)
    {
        $item = DB::table($this->table . ' as pl')
            ->leftJoin('admin as a', 'a.id_admin', '=', 'pl.id_admin')
            ->select(
                'pl.*',
                'a.nama_lengkap as nama_admin',
                'a.username as username_admin'
            )
            ->where('pl.' . $this->primaryKey, $id)
            ->first();

        if (!$item) {
            abort(404);
        }

        $nomorBukti = $this->nomorBukti($item);

        return view('pembayaran-lain.bebas.cetak', compact('item', 'nomorBukti'));
    }
}
