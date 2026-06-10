<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = DB::table('admin');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('level', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            });
        }

        $admins = $query
            ->orderByDesc('id_admin')
            ->get();

        $units = [
            'SEMUA',
            'SMP',
            'MTS',
            'SMK',
            'MA',
            'SPM ULYA',
            "MA'HAD ALY",
            'MADIN NUHA',
            'YAYASAN',
            'PONDOK PA',
            'PONDOK PI',
        ];

        $levels = [
            'superadmin',
            'admin',
            'bendahara',
            'operator',
        ];

        return view('atur-admin.index', compact(
            'admins',
            'search',
            'units',
            'levels'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:4',
            'level' => 'required|string|max:50',
            'unit' => 'required|string|max:50',
        ]);

        $usernameSudahAda = DB::table('admin')
            ->where('username', $request->username)
            ->exists();

        if ($usernameSudahAda) {
            return back()
                ->withInput()
                ->with('error', 'Username sudah digunakan admin lain.');
        }

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'unit' => $request->unit,
            'session_token' => null,
        ];

        DB::table('admin')->insert($this->filterKolomAdmin($data));

        return redirect()
            ->route('atur-admin.index')
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'password' => 'nullable|string|min:4',
            'level' => 'required|string|max:50',
            'unit' => 'required|string|max:50',
        ]);

        $admin = DB::table('admin')
            ->where('id_admin', $id)
            ->first();

        if (!$admin) {
            return back()->with('error', 'Data admin tidak ditemukan.');
        }

        $usernameSudahDipakai = DB::table('admin')
            ->where('username', $request->username)
            ->where('id_admin', '!=', $id)
            ->exists();

        if ($usernameSudahDipakai) {
            return back()
                ->withInput()
                ->with('error', 'Username sudah digunakan admin lain.');
        }

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'level' => $request->level,
            'unit' => $request->unit,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
            $data['session_token'] = null;
        }

        DB::table('admin')
            ->where('id_admin', $id)
            ->update($this->filterKolomAdmin($data));

        return redirect()
            ->route('atur-admin.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $adminLogin = session('admin_id');

        if ((int) $adminLogin === (int) $id) {
            return back()->with('error', 'Admin yang sedang login tidak boleh menghapus dirinya sendiri.');
        }

        $admin = DB::table('admin')
            ->where('id_admin', $id)
            ->first();

        if (!$admin) {
            return back()->with('error', 'Data admin tidak ditemukan.');
        }

        DB::table('admin')
            ->where('id_admin', $id)
            ->delete();

        return redirect()
            ->route('atur-admin.index')
            ->with('success', 'Admin berhasil dihapus.');
    }

    private function filterKolomAdmin(array $data): array
    {
        $columns = Schema::getColumnListing('admin');

        return collect($data)
            ->only($columns)
            ->toArray();
    }
}