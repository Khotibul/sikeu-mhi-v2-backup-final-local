<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin) {
            return back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        $inputPassword = $request->password;
        $passwordValid = false;
        $passwordDatabase = (string) $admin->password;

        if (
            str_starts_with($passwordDatabase, '$2y$') ||
            str_starts_with($passwordDatabase, '$argon2')
        ) {
            $passwordValid = Hash::check($inputPassword, $passwordDatabase);
        } else {
            $passwordValid = md5($inputPassword) === $passwordDatabase;
        }

        if (!$passwordValid) {
            return back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        $level = strtolower(trim((string) ($admin->level ?? 'operator')));
        $unit = strtoupper(trim((string) ($admin->unit ?? 'SEMUA')));

        if (!in_array($level, ['superadmin', 'admin', 'bendahara', 'operator'], true)) {
            $level = 'operator';
        }

        if ($unit === '') {
            $unit = 'SEMUA';
        }

        $request->session()->regenerate();

        session([
            'admin_id' => $admin->id_admin,
            'admin_nama' => $admin->nama_lengkap ?? $admin->username,
            'admin_username' => $admin->username,
            'admin_level' => $level,
            'admin_unit' => $unit,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
