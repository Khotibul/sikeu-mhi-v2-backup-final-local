<?php

namespace App\Http\Controllers;

use App\Models\DataKelasDiniyah;
use Illuminate\Http\Request;

class KelasDiniyahController extends Controller
{
    public function index(Request $request)
    {
        $query = DataKelasDiniyah::query();

        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        $kelasDiniyah = $query->orderBy('nama_kelas')->get();

        return view('kelas-diniyah.index', compact('kelasDiniyah'));
    }

    public function create()
    {
        return view('kelas-diniyah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'nominal_spp' => 'required|numeric|min:0',
        ]);

        DataKelasDiniyah::create([
            'nama_kelas' => $request->nama_kelas,
            'nominal_spp' => $request->nominal_spp,
        ]);

        return redirect()->route('kelas-diniyah.index')
            ->with('success', 'Kelas diniyah berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = DataKelasDiniyah::findOrFail($id);

        return view('kelas-diniyah.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = DataKelasDiniyah::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'nominal_spp' => 'required|numeric|min:0',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'nominal_spp' => $request->nominal_spp,
        ]);

        return redirect()->route('kelas-diniyah.index')
            ->with('success', 'Kelas diniyah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = DataKelasDiniyah::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas-diniyah.index')
            ->with('success', 'Kelas diniyah berhasil dihapus.');
    }
}