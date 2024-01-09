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
        Schema::table('setting_dp_periode', function (Blueprint $table) {
            $table->bigInteger('tokopedia_reguler')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_dp_periode', function (Blueprint $table) {
            $table->dropColumn('tokopedia_reguler');
        });
    }
};
