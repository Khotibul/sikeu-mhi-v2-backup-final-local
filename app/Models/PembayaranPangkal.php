<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPangkal extends Model
{
    protected $table = 'pembayaran_pangkal';
    protected $primaryKey = 'id_pangkal';

    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'tgl_bayar',
        'jenis_tagihan',
        'nominal_bayar',
        'keterangan',
        'id_admin',
    ];
}