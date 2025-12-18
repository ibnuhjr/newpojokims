<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah kolom 'role' jadi varchar biar mudah menambah role baru
            $table->string('role', 20)->default('petugas')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke enum dua nilai
            $table->enum('role', ['admin', 'petugas'])->default('petugas')->change();
        });
    }
};
