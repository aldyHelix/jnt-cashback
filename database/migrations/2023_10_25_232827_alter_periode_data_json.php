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
        Schema::table('periode_data_json', function (Blueprint $table) {
            $table->json('dpf_pivot')->nullable();
            $table->json('dpf_pivot_mp')->nullable();
            $table->json('dpf_pivot_vip')->nullable();
            $table->json('dpf_cashback_reguler')->nullable();
            $table->json('dpf_cashback_marketplace_cod')->nullable();
            $table->json('dpf_cashback_marketplace_non_cod')->nullable();
            $table->json('dpf_cashback_klien_vip')->nullable();
            $table->json('dpf_cashback_rekap_klien_vip')->nullable();
            $table->json('dpf_cashback_rekap')->nullable();
            $table->json('dpf_cashback_rekap_denda')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_data_json', function (Blueprint $table) {
            $table->dropColumn('dpf_pivot');
            $table->dropColumn('dpf_pivot_mp');
            $table->dropColumn('dpf_pivot_vip');
            $table->dropColumn('dpf_cashback_reguler');
            $table->dropColumn('dpf_cashback_marketplace_cod');
            $table->dropColumn('dpf_cashback_marketplace_non_cod');
            $table->dropColumn('dpf_cashback_klien_vip');
            $table->dropColumn('dpf_cashback_rekap_klien_vip');
            $table->dropColumn('dpf_cashback_rekap');
            $table->dropColumn('dpf_cashback_rekap_denda');
        });
    }
};
