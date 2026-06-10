<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranDiniyah extends Model
{
    protected $table = 'pembayaran_diniyah';
    protected $primaryKey = 'id_bayar_diniyah';
    public $timestamps = false;

    protected $fillable = [
        'id_admin',
        'id_siswa',
        'tgl_bayar',
        'bulan_bayar',
        'tahun_bayar',
        'jumlah_bayar',
        'terbayar',
        'keterangan',
        'tahun_ajaran',
    ];
}