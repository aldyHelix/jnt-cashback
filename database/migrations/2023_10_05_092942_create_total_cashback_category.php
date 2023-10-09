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
        Schema::create('total_cashback_category', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->string('sumber_waybill_grouping')->nullable();
            $table->string('category_grouping')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_cashback_category');
    }
};
