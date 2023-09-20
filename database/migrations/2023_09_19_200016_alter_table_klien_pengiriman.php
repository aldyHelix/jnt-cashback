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
        Schema::table('master_klien_pengiriman_setting', function (Blueprint $table) {
            $table->tinyInteger('is_marketplace_reguler')->default(0);
            $table->tinyInteger('is_vip')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_delivery_fee', function (Blueprint $table) {
            $table->dropColumn('is_marketplace_reguler');
            $table->dropColumn('is_vip');
        });
    }
};
