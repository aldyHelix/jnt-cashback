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
        Schema::create('denda_delivery_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('delivery_periode_id');
            $table->unsignedInteger('collection_point_id'); //CP ID
            $table->string('drop_point_outgoing');
            $table->bigInteger('denda_lost_scan_kirim')->default(0);
            $table->bigInteger('denda_late_pickup_reg')->default(0);
            $table->bigInteger('denda_auto_claim')->default(0);
            $table->bigInteger('dpp')->default(0);
            $table->bigInteger('tarif')->default(0);
            $table->bigInteger('admin_bank')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_denda_periode');
    }
};
