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
            $table->tinyInteger('process_percentage')->default(0);
            $table->tinyInteger('file_count')->default(0);
            $table->tinyInteger('global_setting_done')->default(0);
            $table->tinyInteger('upload_done')->default(0);
            $table->tinyInteger('setting_periode_done')->default(0);
            $table->tinyInteger('upload_process_done')->default(0);
            $table->tinyInteger('uninserted_resi_count')->default(0);
            $table->tinyInteger('resi_error_count')->default(0);
            $table->tinyInteger('is_pivot_done')->default(0);
            $table->tinyInteger('is_grading_done')->default(0);
            $table->tinyInteger('is_summary_done')->default(0);
            $table->tinyInteger('is_report_done')->default(0);
            $table->tinyInteger('is_locked')->default(0);
            $table->timestamps();
        });

        //alter table percentage upload
        Schema::table('file_upload', function (Blueprint $table) {
            $table->tinyInteger('processed_percentage')->default(0);
            $table->string('batch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_process_state');

        chema::table('file_upload', function (Blueprint $table) {
            $table->dropColumn('processed_percentage');
            $table->dropColumn('batch_id');
        });
    }
};
