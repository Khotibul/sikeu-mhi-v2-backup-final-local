<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'level',
        'unit',
        'session_token',
    ];

    protected $hidden = [
        'password',
        'session_token',
    ];
}