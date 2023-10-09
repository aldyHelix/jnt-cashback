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
        Schema::table('master_delivery_fee', function (Blueprint $table) {
            $table->integer('target_kpi_percent')->default(92);
            $table->integer('reduce_not_achievement')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_delivery_fee', function (Blueprint $table) {
            $table->dropColumn('target_kpi_percent');
            $table->dropColumn('reduce_not_achievement');
        });
    }
};
