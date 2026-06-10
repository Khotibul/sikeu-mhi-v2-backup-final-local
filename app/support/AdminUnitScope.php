<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class AdminUnitScope
{
    public static function unitLogin(): string
    {
        return strtoupper(trim(session('admin_unit') ?? 'SEMUA'));
    }

    public static function isSemua(): bool
    {
        return in_array(self::unitLogin(), ['SEMUA', 'YAYASAN', ''], true);
    }

    public static function applyToSiswaQuery($query, string $siswaTable = 'siswa')
    {
        $unit = self::unitLogin();

        if (self::isSemua()) {
            return $query;
        }

        $prefix = $siswaTable ? $siswaTable . '.' : '';

        return $query->where(function ($q) use ($unit, $prefix) {
            if (Schema::hasColumn('siswa', 'unit')) {
                $q->orWhere($prefix . 'unit', $unit)
                    ->orWhere($prefix . 'unit', 'like', '%' . $unit . '%');
            }

            if (in_array($unit, ['SMP', 'MTS', 'SMK', 'MA'], true)) {
                $q->orWhere($prefix . 'kelas_formal', 'like', '%' . $unit . '%');
            } elseif (in_array($unit, ['SPM ULYA', "MA'HAD ALY", 'MADIN NUHA'], true)) {
                $q->orWhere($prefix . 'kelas_formal', 'like', '%' . $unit . '%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%' . $unit . '%');
            } elseif ($unit === 'PONDOK PA') {
                $q->orWhere($prefix . 'status_mukim', 'like', '%PA%')
                    ->orWhere($prefix . 'status_mukim', 'like', '%PUTRA%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%PA%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%PUTRA%');
            } elseif ($unit === 'PONDOK PI') {
                $q->orWhere($prefix . 'status_mukim', 'like', '%PI%')
                    ->orWhere($prefix . 'status_mukim', 'like', '%PUTRI%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%PI%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%PUTRI%');
            } else {
                $q->orWhere($prefix . 'kelas_formal', 'like', '%' . $unit . '%')
                    ->orWhere($prefix . 'kelas_diniyah', 'like', '%' . $unit . '%')
                    ->orWhere($prefix . 'status_mukim', 'like', '%' . $unit . '%');
            }
        });
    }

    public static function bolehAksesSiswa($siswa): bool
    {
        $unit = self::unitLogin();

        if (self::isSemua()) {
            return true;
        }

        $kelasFormal = strtoupper((string) ($siswa->kelas_formal ?? ''));
        $kelasDiniyah = strtoupper((string) ($siswa->kelas_diniyah ?? ''));
        $statusMukim = strtoupper((string) ($siswa->status_mukim ?? ''));
        $unitSiswa = strtoupper((string) ($siswa->unit ?? ''));

        if ($unitSiswa !== '' && str_contains($unitSiswa, $unit)) {
            return true;
        }

        if (in_array($unit, ['SMP', 'MTS', 'SMK', 'MA'], true)) {
            return str_contains($kelasFormal, $unit);
        }

        if (in_array($unit, ['SPM ULYA', "MA'HAD ALY", 'MADIN NUHA'], true)) {
            return str_contains($kelasFormal, $unit) || str_contains($kelasDiniyah, $unit);
        }

        if ($unit === 'PONDOK PA') {
            return str_contains($statusMukim, 'PA')
                || str_contains($statusMukim, 'PUTRA')
                || str_contains($kelasDiniyah, 'PA')
                || str_contains($kelasDiniyah, 'PUTRA');
        }

        if ($unit === 'PONDOK PI') {
            return str_contains($statusMukim, 'PI')
                || str_contains($statusMukim, 'PUTRI')
                || str_contains($kelasDiniyah, 'PI')
                || str_contains($kelasDiniyah, 'PUTRI');
        }

        return str_contains($kelasFormal, $unit)
            || str_contains($kelasDiniyah, $unit)
            || str_contains($statusMukim, $unit);
    }
}