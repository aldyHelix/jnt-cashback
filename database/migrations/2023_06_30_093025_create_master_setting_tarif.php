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
        Schema::create('master_setting_tarif', function (Blueprint $table) {
            $table->id();
            $table->char('grading_type', 1)->nullable()->comment('A;B;C');
            $table->string('sumber_waybill')->nullable();
            $table->integer('diskon_persen')->nullable();
            $table->string('fee')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_setting_tarif');
    }
};
