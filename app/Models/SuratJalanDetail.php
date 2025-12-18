<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_detail';

    protected $fillable = [
        'surat_jalan_id',
        'material_id',
        'quantity',
        'satuan',
        'keterangan',
        'is_manual',
        'nama_barang_manual',
        'satuan_manual',
        'jumlah_kembali',
        'tanggal_kembali',
    ];

    public function suratJalan(): BelongsTo
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id', 'id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }
    public function pengembalianHistories()
{
    return $this->hasMany(\App\Models\PengembalianHistory::class, 'surat_jalan_detail_id');
}


}
