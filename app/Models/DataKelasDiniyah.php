<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKelasDiniyah extends Model
{
    protected $table = 'data_kelas_diniyah';
    protected $primaryKey = 'id_diniyah';
    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
        'nominal_spp',
    ];
}