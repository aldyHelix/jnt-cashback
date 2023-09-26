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
        Schema::create('periode_process_state', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->tinyInteger('global_setting_done');
            $table->tinyInteger('upload_done');
            $table->tinyInteger('setting_periode_done');
            $table->tinyInteger('upload_process_done');
            $table->tinyInteger('uninserted_resi_count');
            $table->tinyInteger('resi_error_count');
            $table->tinyInteger('is_pivot_done');
            $table->tinyInteger('is_grading_done');
            $table->tinyInteger('is_summary_done');
            $table->tinyInteger('is_report_done');
            $table->tinyInteger('is_locked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_process_state');
    }
};
