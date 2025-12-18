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
            if (!Schema::hasColumn('surat_jalan_detail', 'is_manual')) {
                $table->boolean('is_manual')->default(false)->after('material_id');
            }
            if (!Schema::hasColumn('surat_jalan_detail', 'nama_barang_manual')) {
                $table->string('nama_barang_manual')->nullable()->after('is_manual');
            }
            if (!Schema::hasColumn('surat_jalan_detail', 'satuan_manual')) {
                $table->string('satuan_manual')->nullable()->after('nama_barang_manual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_detail', function (Blueprint $table) {
            if (Schema::hasColumn('surat_jalan_detail', 'is_manual')) {
                $table->dropColumn('is_manual');
            }
            if (Schema::hasColumn('surat_jalan_detail', 'nama_barang_manual')) {
                $table->dropColumn('nama_barang_manual');
            }
            if (Schema::hasColumn('surat_jalan_detail', 'satuan_manual')) {
                $table->dropColumn('satuan_manual');
            }
        });
    }
};
