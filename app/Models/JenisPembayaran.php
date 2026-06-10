<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    protected $table = 'jenis_pembayaran';
    protected $primaryKey = 'id_jenis';

    public $timestamps = false;

    protected $fillable = [
        'nama_jenis',
        'nominal_standar',
    ];
}