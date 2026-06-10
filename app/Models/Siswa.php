<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $timestamps = false;

    protected $fillable = [
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
        'foto',
        'status_aktif',
    ];
}
