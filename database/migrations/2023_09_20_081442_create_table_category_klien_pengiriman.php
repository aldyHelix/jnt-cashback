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
        Schema::create('master_category', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->nullable();
            $table->timestamps();
        });

        Schema::create('category_klien_pengiriman', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->integer('klien_pengiriman_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('master_category')
                ->onDelete('cascade');
            $table->foreign('klien_pengiriman_id')->references('id')->on('master_klien_pengiriman_setting')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_category');
        Schema::dropIfExists('category_klien_pengiriman');
    }
};
