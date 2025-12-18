<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Material;

class MaterialHistory extends Model
{
    protected $fillable = [
    'material_id',
    'tanggal',
    'tipe',
    'no_slip',
    'masuk',
    'keluar',
    'sisa_persediaan',
    'catatan',
    'keterangan',        // TAMBAH
    'surat_jalan_id',    // TAMBAH
];


    protected $casts = [
        'tanggal' => 'date',
        'masuk' => 'integer',
        'keluar' => 'integer',
        'sisa_persediaan' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * =======================================================
     *  UNIVERSAL FUNCTION UNTUK RECORD MATERIAL MASUK/KELUAR
     * =======================================================
     */
    public static function record(
    $material_id,
    $tipe,
    $qty,
    $no_slip = '-',
    $catatan = null,
    $tanggal = null
) {
    $material = Material::find($material_id);

    if (!$material) {
        \Log::warning("❌ MaterialHistory::record() gagal — Material ID {$material_id} tidak ditemukan.");
        return null;
    }

    $tipe = strtoupper($tipe);

    if ($tipe === 'MASUK') {
        $material->unrestricted_use_stock += $qty;
    } elseif ($tipe === 'KELUAR') {
        $material->unrestricted_use_stock -= $qty;
    }

    $material->save();

    $existing = self::where('material_id', $material_id)
        ->whereDate('tanggal', $tanggal ?? now())
        ->where('tipe', $tipe)
        ->where('no_slip', $no_slip ?: '-')
        ->first();

    if ($existing) {
        $existing->update([
            'masuk' => $tipe === 'MASUK' ? $qty : 0,
            'keluar' => $tipe === 'KELUAR' ? $qty : 0,
            'sisa_persediaan' => $material->unrestricted_use_stock,
            'catatan' => $catatan
        ]);
        return $existing;
    }

    return self::create([
        'material_id'     => $material_id,
        'tanggal'         => $tanggal ?? now(),
        'tipe'            => $tipe,
        'no_slip'         => $no_slip ?: '-',
        'masuk'           => $tipe === 'MASUK' ? $qty : 0,
        'keluar'          => $tipe === 'KELUAR' ? $qty : 0,
        'sisa_persediaan' => $material->unrestricted_use_stock,
        'catatan'         => $catatan,
    ]);
}
    public static function stokBulanan($materialId, $bulan, $tahun)
{
    // 1. Stok awal bulan
    $stokAwal = self::where('material_id', $materialId)
        ->whereDate('tanggal', '<', "{$tahun}-{$bulan}-01")
        ->orderBy('tanggal', 'desc')
        ->value('sisa_persediaan');

    if ($stokAwal === null) {
        $stokAwal = Material::find($materialId)->unrestricted_use_stock ?? 0;
    }

    // 2. Total masuk bulan ini
    $masuk = self::where('material_id', $materialId)
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->sum('masuk');

    // 3. Total keluar bulan ini
    $keluar = self::where('material_id', $materialId)
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->sum('keluar');

    return $stokAwal + $masuk - $keluar;
}


}