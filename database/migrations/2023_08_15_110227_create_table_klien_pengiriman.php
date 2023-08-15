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
        Schema::create('master_klien_pengiriman_setting', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->string('klien_pengiriman')->nullable();
            $table->tinyInteger('is_reguler');
            $table->tinyInteger('is_dfod');
            $table->tinyInteger('is_super');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_klien_pengiriman_setting');
    }
};
