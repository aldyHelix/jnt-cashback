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
        Schema::create('denda_grading_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->unsignedInteger('sprinter_pickup'); //CP ID
            $table->char('grading_type', 1);
            $table->bigInteger('transit_fee');
            $table->bigInteger('denda_void');
            $table->bigInteger('denda_dfod');
            $table->bigInteger('denda_pusat');
            $table->bigInteger('denda_selisih_berat');
            $table->bigInteger('denda_lost_scan_kirim');
            $table->bigInteger('denda_auto_claim');
            $table->bigInteger('denda_sponsorship');
            $table->bigInteger('denda_late_pickup_ecommerce');
            $table->bigInteger('potongan_pop');
            $table->bigInteger('denda_lainnya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda_grading_periode');
    }
};
