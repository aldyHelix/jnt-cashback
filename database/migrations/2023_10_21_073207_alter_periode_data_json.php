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
            $table->json('cashback_reguler_a')->nullable()->after('pivot_vip');
            $table->json('cashback_reguler_b')->nullable()->after('pivot_vip');
            $table->json('cashback_reguler_c')->nullable()->after('pivot_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_data_json', function (Blueprint $table) {
            $table->dropColumn('cashback_reguler_a');
            $table->dropColumn('cashback_reguler_b');
            $table->dropColumn('cashback_reguler_c');
        });
    }
};
