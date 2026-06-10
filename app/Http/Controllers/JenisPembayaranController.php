<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = JenisPembayaran::query();

        if (!empty($search)) {
            $query->where('nama_jenis', 'like', '%' . $search . '%');
        }

        $jenisPembayaran = $query
            ->orderBy('nama_jenis')
            ->get();

        return view('jenis-pembayaran.index', compact(
            'jenisPembayaran',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:100',
            'nominal_standar' => 'required|numeric|min:0',
        ]);

        JenisPembayaran::create([
            'nama_jenis' => $request->nama_jenis,
            'nominal_standar' => (int) $request->nominal_standar,
        ]);

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisPembayaran::findOrFail($id);

        $request->validate([
            'nama_jenis' => 'required|string|max:100',
            'nominal_standar' => 'required|numeric|min:0',
        ]);

        $jenis->update([
            'nama_jenis' => $request->nama_jenis,
            'nominal_standar' => (int) $request->nominal_standar,
        ]);

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenis = JenisPembayaran::findOrFail($id);
        $jenis->delete();

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}