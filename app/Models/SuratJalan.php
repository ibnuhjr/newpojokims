<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';

    protected $fillable = [
        'nomor_surat',
        'jenis_surat_jalan',
        'tanggal',
        'kepada',
        'berdasarkan',
        'security',
        'keterangan',
        'kendaraan',
        'no_polisi',
        'pengemudi',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime'
    ];

    /**
     * Relasi ke User yang membuat surat jalan
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang menyetujui surat jalan
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke detail surat jalan
     */
    public function details(): HasMany
    {
        return $this->hasMany(SuratJalanDetail::class);
    }

    /**
     * Generate nomor surat dengan sequence berdasarkan jenis, bulan, dan tahun
     */
        public static function generateNomorSurat($jenisSuratJalan = 'Normal')
{
    $year = date('Y');

    // Tentukan kode prefix berdasarkan jenis surat
    $jenisKode = [
        'Normal'     => 'SJ',
        'Garansi'    => 'GRN',
        'Peminjaman' => 'PMJ',
        'Perbaikan'  => 'PBK',
        'Manual'     => 'MNL',
    ];

    $kode = $jenisKode[$jenisSuratJalan] ?? 'SJ';
    $kodeLog = 'LOG.00.02';
    $kodeFungsi = 'F02050000';

    // 🔹 Ambil surat terakhir dari semua jenis (global numbering)
    $lastSurat = self::whereYear('created_at', $year)
        ->orderBy('id', 'desc')
        ->first();

    // Tentukan urutan berikutnya
    if ($lastSurat) {
        preg_match('/^(\d{3})/', $lastSurat->nomor_surat, $matches);
        $sequence = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
    } else {
        $sequence = 1;
    }

    // 🔹 Format hasil akhir dengan prefix sesuai jenis
    return sprintf("%03d.%s/%s/%s/%s", $sequence, $kode, $kodeLog, $kodeFungsi, $year);
}


        private static function getNextSequence()
        {
            $lastSurat = self::orderByRaw("CAST(split_part(nomor_surat, '.', 1) AS INTEGER) DESC")
                ->whereRaw("split_part(nomor_surat, '.', 1) ~ '^[0-9]+$'")
                ->first();

            if (!$lastSurat) {
                return 1;
            }

            $nomorParts = explode('.', $lastSurat->nomor_surat);
            $lastSequence = isset($nomorParts[0]) ? intval($nomorParts[0]) : 0;

            return $lastSequence + 1;
        }


    
    /**
     * Konversi angka bulan ke romawi
     */
    private static function getMonthRoman($month)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        
        return $romans[$month] ?? 'I';
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
