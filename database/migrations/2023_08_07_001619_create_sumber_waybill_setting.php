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
        Schema::create('sumber_waybill_setting', function (Blueprint $table) {
            $table->id();
            $table->string('sumber_waybill');
            $table->string('type')->default('reguler');
            $table->integer('order');
            $table->string('header_name');
            $table->tinyInteger('is_count');
            $table->tinyInteger('is_sum');
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumber_waybill_setting');
    }
};
