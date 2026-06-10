<?php

namespace App\Support;

class AdminRole
{
    public static function level(): string
    {
        return strtolower(trim((string) (session('admin_level') ?? 'operator')));
    }

    public static function unit(): string
    {
        return strtoupper(trim((string) (session('admin_unit') ?? 'SEMUA')));
    }

    public static function label(?string $level = null): string
    {
        $level = strtolower(trim((string) ($level ?? self::level())));

        return match ($level) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'bendahara' => 'Bendahara',
            'operator' => 'Operator',
            default => ucfirst($level ?: 'Operator'),
        };
    }

    public static function is(string $level): bool
    {
        return self::level() === strtolower(trim($level));
    }

    public static function in(array $levels): bool
    {
        return in_array(self::level(), array_map(fn($item) => strtolower(trim((string) $item)), $levels), true);
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
        $permission = strtolower(trim($permission));
        $level = self::level();

        if ($level === 'superadmin') {
            return true;
        }

        $permissions = [
            'admin' => [
                'dashboard',

                'siswa.view',
                'siswa.create',
                'siswa.edit',
                'siswa.delete',
                'siswa.import',
                'siswa.export',

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
                'siswa.export',

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
                'siswa.import',
                'siswa.export',

                'kelas.view',

                'ppdb.view',
                'ppdb.manage',
            ],
        ];

        $allowed = $permissions[$level] ?? [];

        return in_array('*', $allowed, true) || in_array($permission, $allowed, true);
    }
}
