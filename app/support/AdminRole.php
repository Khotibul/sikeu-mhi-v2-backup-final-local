<?php

namespace App\Support;

class AdminRole
{
    public static function level(): string
    {
        return strtolower(trim(session('admin_level') ?? 'operator'));
    }

    public static function unit(): string
    {
        return strtoupper(trim(session('admin_unit') ?? 'SEMUA'));
    }

    public static function is(string $level): bool
    {
        return self::level() === strtolower($level);
    }

    public static function in(array $levels): bool
    {
        return in_array(self::level(), array_map('strtolower', $levels), true);
    }

    public static function superadmin(): bool
    {
        return self::is('superadmin');
    }

    public static function admin(): bool
    {
        return self::is('admin');
    }

    public static function bendahara(): bool
    {
        return self::is('bendahara');
    }

    public static function operator(): bool
    {
        return self::is('operator');
    }

    public static function can(string $permission): bool
    {
        $level = self::level();

        $permissions = [
            'superadmin' => [
                '*',
            ],

            'admin' => [
                'dashboard',
                'siswa.view',
                'siswa.create',
                'siswa.edit',
                'siswa.delete',
                'kelas.view',
                'kelas.manage',
                'jenis-pembayaran.view',
                'jenis-pembayaran.manage',
                'ppdb.view',
                'ppdb.manage',
                'pembayaran.view',
                'pembayaran.manage',
                'pengeluaran.view',
                'pengeluaran.manage',
                'laporan.view',
                'tunggakan.view',
                'riwayat.view',
            ],

            'bendahara' => [
                'dashboard',
                'siswa.view',
                'jenis-pembayaran.view',
                'pembayaran.view',
                'pembayaran.manage',
                'pengeluaran.view',
                'pengeluaran.manage',
                'laporan.view',
                'tunggakan.view',
                'riwayat.view',
            ],

            'operator' => [
                'dashboard',
                'siswa.view',
                'siswa.create',
                'siswa.edit',
                'kelas.view',
                'ppdb.view',
                'ppdb.manage',
            ],
        ];

        $allowed = $permissions[$level] ?? [];

        return in_array('*', $allowed, true) || in_array($permission, $allowed, true);
    }
}
