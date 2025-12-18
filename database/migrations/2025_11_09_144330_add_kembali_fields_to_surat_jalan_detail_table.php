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
        $table->integer('jumlah_kembali')->nullable()->after('quantity');
        $table->date('tanggal_kembali')->nullable()->after('jumlah_kembali');
    });
}

public function down(): void
{
    Schema::table('surat_jalan_detail', function (Blueprint $table) {
        $table->dropColumn(['jumlah_kembali', 'tanggal_kembali']);
    });
}
};
