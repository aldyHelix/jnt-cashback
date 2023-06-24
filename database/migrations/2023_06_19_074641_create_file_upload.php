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
        Schema::create('file_upload', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->nullable();
            $table->string('month_period')->nullable();
            $table->string('year_period')->nullable();
            $table->bigInteger('processed_row')->default(0);
            $table->bigInteger('count_row')->default(0);
            $table->bigInteger('file_size')->default(0);
            $table->string('table_name')->nullable();
            $table->string('processed_by')->default(NULL)->nullable();
            $table->string('processing_status')->nullable()->comment();
            $table->tinyInteger('type_file')->nullable()->comment('0:cashback;1:ttd;');
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
        Schema::dropIfExists('file_upload');
    }
};
