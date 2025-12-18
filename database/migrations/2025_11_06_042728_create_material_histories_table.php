<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');

            // Pakai lowercase untuk enum consistency
            $table->enum('tipe', ['Masuk', 'Keluar']); 

            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            $table->text('keterangan')->nullable();

            // Perbaikan nama tabel surat jalan
            $table->foreignId('surat_jalan_id')->nullable()
                ->constrained('surat_jalans') // Ubah dari 'surat_jalan' ke 'surat_jalans'
                ->onDelete('set null');

            $table->date('tanggal'); // Ubah ke 'date' jika hanya butuh tanggal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_histories');
    }
};
