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
        Schema::create('total_biaya_kirim', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->text('sumber_waybill_grouping')->nullable();
            $table->integer('discount')->default(0);
            $table->text('equation')->nullable(); //rumus dari sumber waybill
            $table->string('grading_type')->nullable();
            $table->string('cashback_grouping')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_biaya_kirim');
    }
};
