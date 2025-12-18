<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('material_masuk', function (Blueprint $table) {
        $table->date('tanggal_keluar')->nullable();
        $table->string('jenis')->nullable();
        $table->string('nomor_po')->nullable();
        $table->string('nomor_doc')->nullable();
        $table->string('tugas_4')->nullable();
    });
}

public function down()
{
    Schema::table('material_masuk', function (Blueprint $table) {
        $table->dropColumn([
            'tanggal_keluar',
            'jenis',
            'nomor_po',
            'nomor_doc',
            'tugas_4'
        ]);
    });
}
};