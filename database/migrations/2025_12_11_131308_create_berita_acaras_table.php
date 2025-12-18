<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();

            // Tanggal & hari
            $table->string('hari');                         // "Selasa"
            $table->date('tanggal');                       // 2025-11-04 (dipakai untuk 04-11-2025 & Cimahi, 04 November 2025)
            $table->string('tanggal_teks');                // "Empat Bulan November Tahun..."

            // Pejabat Mengetahui
            $table->string('mengetahui');                  // "ARYTA WULANDARI"
            $table->string('jabatan_mengetahui');          // "Manager UP3 Cimahi"

            // Pejabat Pembuat
            $table->string('pembuat');                     // "DENI PURNAMA"
            $table->string('jabatan_pembuat');             // "Asman Konstruksi UP3 Cimahi"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_acaras');
    }
};
