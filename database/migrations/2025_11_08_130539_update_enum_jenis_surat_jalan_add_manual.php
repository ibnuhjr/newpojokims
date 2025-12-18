<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ubah kolom enum ke text sementara
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->text('jenis_temp')->nullable();
        });

        // 2. Salin data lama
        DB::statement('UPDATE surat_jalan SET jenis_temp = jenis_surat_jalan');

        // 3. Hapus kolom lama
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropColumn('jenis_surat_jalan');
        });

        // 4. Tambahkan enum baru termasuk "Manual"
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->enum('jenis_surat_jalan', ['Normal', 'Garansi', 'Peminjaman', 'Perbaikan', 'Manual'])
                  ->default('Normal');
        });

        // 5. Salin data kembali
        DB::statement('UPDATE surat_jalan SET jenis_surat_jalan = jenis_temp');

        // 6. Hapus kolom sementara
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropColumn('jenis_temp');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->text('jenis_temp')->nullable();
        });

        DB::statement('UPDATE surat_jalan SET jenis_temp = jenis_surat_jalan');

        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropColumn('jenis_surat_jalan');
        });

        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->enum('jenis_surat_jalan', ['Normal', 'Garansi', 'Peminjaman', 'Perbaikan'])
                  ->default('Normal');
        });

        DB::statement('UPDATE surat_jalan SET jenis_surat_jalan = jenis_temp');
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropColumn('jenis_temp');
        });
    }
};
