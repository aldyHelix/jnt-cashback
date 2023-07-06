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
        Schema::create('master_periode', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->bigInteger('inserted_row')->default(0);
            $table->bigInteger('processed_row')->default(0);
            $table->bigInteger('count_row')->default(0);
            $table->string('processed_by')->default(NULL)->nullable();
            $table->string('status')->nullable()->comment();
            $table->tinyInteger('is_processing_done')->default(0);
            $table->tinyInteger('is_pivot_processing_done')->default(0);
            $table->tinyInteger('is_locked')->default(0);
            $table->timestamp('start_processed_at')->nullable();
            $table->timestamp('done_processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_periode');
    }
};
