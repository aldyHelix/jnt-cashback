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
            $table->json('cashback_marketplace_awb_cod')->nullable()->after('cashback_marketplace_cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_data_json', function (Blueprint $table) {
            $table->dropColumn('cashback_marketplace_awb_cod');
        });
    }
};
