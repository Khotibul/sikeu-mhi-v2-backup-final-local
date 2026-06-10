<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bayar';
    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'tgl_bayar',
        'bulan_bayar',
        'tahun_bayar',
        'semester',
        'jumlah_bayar',
        'terbayar',
        'status_bayar',
        'keterangan',
        'id_admin',
        'tahun_ajaran',
    ];
}