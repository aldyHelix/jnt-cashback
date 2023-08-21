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
        Schema::create('delivery_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('collection_point_id');
            $table->string('drop_point_outgoing');
            $table->string('drop_point_ttd');
            $table->integer('kpi_target_count')->default(0);
            $table->integer('kpi_reduce_not_achievement')->default(100);
            $table->tinyInteger('is_show')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zone');
    }
};
