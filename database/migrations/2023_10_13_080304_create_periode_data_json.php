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
        Schema::create('periode_data_json', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->json('pivot_')->nullable();
            $table->json('pivot_mp')->nullable();
            $table->json('pivot_vip')->nullable();
            $table->json('cashback_reguler')->nullable();
            $table->json('cashback_marketplace_cod')->nullable();
            $table->json('cashback_marketplace_non_cod')->nullable();
            $table->json('cashback_klien_vip')->nullable();
            $table->json('cashback_luar_zona')->nullable();
            $table->json('cashback_setting')->nullable();
            $table->json('cashback_grading_1')->nullable();
            $table->json('cashback_grading_1_denda')->nullable();
            $table->json('cashback_grading_2')->nullable();
            $table->json('cashback_grading_2_denda')->nullable();
            $table->json('cashback_grading_3')->nullable();
            $table->json('cashback_grading_3_denda')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_data_json');
    }
};
