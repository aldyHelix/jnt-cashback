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
        Schema::create('setting_dp_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->string('drop_point_outgoing');
            $table->bigInteger('pengurangan_total')->default(0);
            $table->bigInteger('penambahan_total')->default(0);
            $table->integer('diskon_cod')->default(7);
            $table->string('grouping')->default('cod');
            $table->string('grading_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_dp_periode');
    }
};
