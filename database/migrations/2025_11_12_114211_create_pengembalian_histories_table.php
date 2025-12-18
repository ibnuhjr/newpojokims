<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_detail_id')->constrained('surat_jalan_detail')->onDelete('cascade');
            $table->string('nomor_surat_masuk');
            $table->date('tanggal_masuk');
            $table->integer('jumlah_kembali');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_histories');
    }
};
