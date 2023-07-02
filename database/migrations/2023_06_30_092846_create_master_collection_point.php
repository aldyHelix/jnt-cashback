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
        Schema::create('master_collection_point', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cp')->nullable();
            $table->string('nama_cp')->nullable();
            $table->string('nama_pt')->nullable();
            $table->string('drop_point_outgoing')->nullable();
            $table->string('grading_pickup')->nullable();
            $table->string('zona_delivery')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_collection_point');
    }
};
