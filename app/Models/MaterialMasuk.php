<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaterialMasuk extends Model
{
    use HasFactory;

    protected $table = 'material_masuk';

protected $fillable = [
    'nomor_kr',
    'pabrikan',
    'tanggal_masuk',
    'tanggal_keluar',
    'jenis',
    'nomor_po',
    'nomor_doc',
    'tugas_4',
    'keterangan',
    'status_sap',
    // 'tanggal_sap',
    'created_by',
];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relasi ke model Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Relasi ke User (pembuat)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke MaterialMasukDetail
     */
    public function details()
    {
        return $this->hasMany(MaterialMasukDetail::class, 'material_masuk_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_masuk', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan material
     */
    public function scopeByMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }


}