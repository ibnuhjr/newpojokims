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
        $table->string('status_sap')->default('Belum Selesai SAP');
    });
}

    public function down()
    {
        Schema::table('material_masuk', function (Blueprint $table) {
            $table->dropColumn('status_sap');
        });
    }
};
