<?php

namespace App\Http\Controllers;

use App\Models\DataKelas;
use Illuminate\Http\Request;

class KelasFormalController extends Controller
{
    public function index(Request $request)
    {
        $query = DataKelas::query();

        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        $kelasFormal = $query->orderBy('nama_kelas')->get();

        return view('kelas-formal.index', compact('kelasFormal'));
    }

    public function create()
    {
        return view('kelas-formal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'nominal_spp' => 'required|numeric|min:0',
        ]);

        DataKelas::create([
            'nama_kelas' => $request->nama_kelas,
            'nominal_spp' => $request->nominal_spp,
        ]);

        return redirect()->route('kelas-formal.index')
            ->with('success', 'Kelas formal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = DataKelas::findOrFail($id);

        return view('kelas-formal.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = DataKelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'nominal_spp' => 'required|numeric|min:0',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'nominal_spp' => $request->nominal_spp,
        ]);

        return redirect()->route('kelas-formal.index')
            ->with('success', 'Kelas formal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = DataKelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas-formal.index')
            ->with('success', 'Kelas formal berhasil dihapus.');
    }
}