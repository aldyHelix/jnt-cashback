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
        Schema::create('cashback_setting', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_paket');
            $table->string('diskon')->default(0);
            $table->timestamps();
        });

        Schema::table('master_category', function (Blueprint $table) {
            $table->string('cashback_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashback_setting');

        Schema::table('master_category', function (Blueprint $table) {
            $table->dropColumn('cashback_type');
        });
    }
};
