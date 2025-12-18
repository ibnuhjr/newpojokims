<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_jalan_detail_id',
        'nomor_surat_masuk',
        'tanggal_masuk',
        'jumlah_kembali',
        'keterangan',
    ];

    public function detail()
    {
        return $this->belongsTo(SuratJalanDetail::class, 'surat_jalan_detail_id');
    }
}
