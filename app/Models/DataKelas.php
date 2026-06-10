<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKelas extends Model
{
    protected $table = 'data_kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
        'nominal_spp',
    ];
}