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
            $table->json('data_pivot')->nullable();
            $table->json('data_pivot_mp')->nullable();
            $table->json('data_pivot_vip')->nullable();
            $table->json('data_cashback_reguler')->nullable();
            $table->json('data_cashback_marketplace_cod')->nullable();
            $table->json('data_cashback_marketplace_non_cod')->nullable();
            $table->json('data_cashback_klien_vip')->nullable();
            $table->json('data_cashback_grading_1')->nullable();
            $table->json('data_cashback_grading_2')->nullable();
            $table->json('data_cashback_grading_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_periode', function (Blueprint $table) {
            $table->dropColumn('data_pivot');
            $table->dropColumn('data_pivot_mp');
            $table->dropColumn('data_cashback_reguler');
            $table->dropColumn('data_cashback_marketplace_cod');
            $table->dropColumn('data_cashback_marketplace_non_cod');
            $table->dropColumn('data_cashback_klien_vip');
            $table->dropColumn('data_cashback_grading_1');
            $table->dropColumn('data_cashback_grading_2');
            $table->dropColumn('data_cashback_grading_3');
        });
    }
};
