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
        Schema::table('surat_jalan_detail', function (Blueprint $table) {
            // Kolom tambahan untuk surat jalan manual
            $table->boolean('is_manual')->default(false)->after('material_id');
            $table->string('nama_barang_manual')->nullable()->after('is_manual');
            $table->string('satuan_manual')->nullable()->after('nama_barang_manual');

            // material_id bisa nullable karena kalau manual, tidak ada material
            $table->unsignedBigInteger('material_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_detail', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'nama_barang_manual', 'satuan_manual']);
            $table->unsignedBigInteger('material_id')->nullable(false)->change();
        });
    }
};
