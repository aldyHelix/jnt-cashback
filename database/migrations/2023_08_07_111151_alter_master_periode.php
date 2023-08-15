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
        Schema::table('master_periode', function (Blueprint $table) {
            $table->tinyInteger('processed_grade_1')->default(0)->after('is_processing_done');
            $table->tinyInteger('processed_grade_2')->default(0)->after('is_processing_done');
            $table->tinyInteger('processed_grade_3')->default(0)->after('is_processing_done');
            $table->tinyInteger('processed_grade_1_by')->default(0)->after('is_processing_done');
            $table->tinyInteger('processed_grade_2_by')->default(0)->after('is_processing_done');
            $table->tinyInteger('processed_grade_3_by')->default(0)->after('is_processing_done');
            $table->tinyInteger('locked_grade_1')->default(0)->after('is_processing_done');
            $table->tinyInteger('locked_grade_2')->default(0)->after('is_processing_done');
            $table->tinyInteger('locked_grade_3')->default(0)->after('is_processing_done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_periode', function (Blueprint $table) {
            $table->dropColumn('processed_grade_1');
            $table->dropColumn('processed_grade_2');
            $table->dropColumn('processed_grade_3');
            $table->dropColumn('processed_grade_1_by');
            $table->dropColumn('processed_grade_2_by');
            $table->dropColumn('processed_grade_3_by');
            $table->dropColumn('locked_grade_1');
            $table->dropColumn('locked_grade_2');
            $table->dropColumn('locked_grade_3');
        });
    }
};
