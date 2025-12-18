<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    protected $fillable = [
        'hari',
        'tanggal',
        'tanggal_teks',
        'mengetahui',
        'jabatan_mengetahui',
        'pembuat',
        'jabatan_pembuat',
    ];
}
