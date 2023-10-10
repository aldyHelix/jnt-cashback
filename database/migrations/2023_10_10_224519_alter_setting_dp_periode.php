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
        Schema::table('setting_dp_periode', function (Blueprint $table) {
            $table->bigInteger('retur_klien_pengirim_hq')->default(0);
            $table->bigInteger('retur_belum_terpotong')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_dp_periode', function (Blueprint $table) {
            $table->dropColumn('retur_klien_pengirim_hq');
            $table->dropColumn('retur_belum_terpotong');
        });
    }
};
