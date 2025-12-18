<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materials';

    protected $fillable = [
    'nomor',
    'company_code',
    'company_code_description',
    'plant',
    'plant_description',
    'storage_location',
    'storage_location_description',
    'material_type',
    'material_type_description',
    'material_code',
    'material_description',
    'material_group',
    'base_unit_of_measure',
    'valuation_type',
    'valuation_class',
    'valuation_description',
    'harga_satuan',
    'currency',
    'rak',
    'created_by',
];

protected $attributes = [
    'unrestricted_use_stock' => 0,
    'quality_inspection_stock' => 0,
    'blocked_stock' => 0,
    'in_transit_stock' => 0,
    'project_stock' => 0,
    'qty' => 0,
    // 'total_harga' => 0,
    'status' => self::STATUS_BAIK,
];



    protected $casts = [
        'tanggal_terima' => 'date',
        'harga_satuan' => 'decimal:2',
        // 'total_harga' => 'decimal:2',
        'qty' => 'integer',
        'unrestricted_use_stock' => 'decimal:0',
        'quality_inspection_stock' => 'decimal:0',
        'blocked_stock' => 'decimal:0',
        'in_transit_stock' => 'decimal:0',
        'project_stock' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    const STATUS_BAIK = 'BAIK';
    const STATUS_RUSAK = 'RUSAK';
    const STATUS_DALAM_PERBAIKAN = 'DALAM PERBAIKAN';

    public static function getStatuses()
    {
        return [
            self::STATUS_BAIK => 'Baik',
            self::STATUS_RUSAK => 'Rusak',
            self::STATUS_DALAM_PERBAIKAN => 'Dalam Perbaikan',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nomor_kr', 'LIKE', "%{$search}%")
                ->orWhere('pabrikan', 'LIKE', "%{$search}%")
                ->orWhere('material_description', 'LIKE', "%{$search}%")
                ->orWhere('material_code', 'LIKE', "%{$search}%")
                ->orWhere('keterangan', 'LIKE', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPabrikan($query, $pabrikan)
    {
        return $query->where('pabrikan', 'LIKE', "%{$pabrikan}%");
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('qty', '<=', $threshold);
    }

    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getFormattedTotalHargaAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function getFormattedTanggalTerimaAttribute()
    {
        return $this->tanggal_terima ? $this->tanggal_terima->format('d F Y') : '-';
    }

    public function getStatusBadgeColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_BAIK:
                return 'success';
            case self::STATUS_RUSAK:
                return 'danger';
            case self::STATUS_DALAM_PERBAIKAN:
                return 'warning';
            default:
                return 'secondary';
        }
    }

    public function getTotalStockAttribute()
    {
        return $this->unrestricted_use_stock
            + $this->quality_inspection_stock
            + $this->blocked_stock
            + $this->in_transit_stock
            + $this->project_stock;
    }
    public function getTotalHargaAttribute()
{
    $stok = $this->unrestricted_use_stock ?? 0;
    $harga = $this->harga_satuan ?? 0;

    return $stok * $harga;
}


    public static function generateNomor()
    {
        try {
            $lastMaterial = self::orderBy('nomor', 'desc')->first();
            $lastNumber = $lastMaterial ? (int) $lastMaterial->nomor : 0;
            return $lastNumber + 1;
        } catch (\Exception $e) {
            return time();
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
            if (empty($model->status)) {
                $model->status = self::STATUS_BAIK;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function canBeDeleted()
    {
        return true;
    }

    public function updateStock($newQty, $reason = null)
    {
        $oldQty = $this->qty;
        $this->update(['qty' => $newQty]);
        return $this;
    }

    // === START FIX: Anti Stok Negatif ===
    public function safeIncrement($column, $amount)
    {
        $this->$column = ($this->$column ?? 0) + $amount;
        $this->save();
    }

    public function safeDecrement($column, $amount)
    {
        $current = $this->$column ?? 0;

        if ($current < $amount) {
            throw new \Exception("❌ Stok material '{$this->material_description}' tidak mencukupi! 
            (tersedia: {$current}, dibutuhkan: {$amount})");
        }

        $this->$column = $current - $amount;
        $this->save();
    }
    // === END FIX ===
}
