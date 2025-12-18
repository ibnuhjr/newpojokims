<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('monitorings', function (Blueprint $table) {
        $table->id();
        $table->string('unit');
        $table->decimal('target_harian', 15, 2)->nullable();
        $table->decimal('lm1', 15, 2)->nullable();
        $table->decimal('lm2', 15, 2)->nullable();
        $table->decimal('lm3', 15, 2)->nullable();
        $table->decimal('realisasi_harian', 15, 2)->nullable();
        $table->decimal('penerimaan_material', 15, 2)->nullable();
        $table->decimal('target_persediaan', 15, 2)->nullable();
        $table->decimal('realisasi_persediaan', 15, 2)->nullable();
        $table->decimal('saldo_sebelumnya', 15, 2)->nullable();
        $table->decimal('target_pemakaian', 15, 2)->nullable();
        $table->decimal('realisasi_pemakaian', 15, 2)->nullable();
        $table->decimal('persen_pemakaian', 5, 2)->nullable();
        $table->decimal('persen_pencapaian', 5, 2)->nullable();
        $table->decimal('target_jansept', 15, 2)->nullable();
        $table->decimal('realisasi_jansept', 15, 2)->nullable();
        $table->decimal('ito', 5, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
