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
        Schema::create('global_metode_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('metode_pembayaran')->nullable();
            $table->timestamps();
        });

        Schema::create('global_category_resi', function (Blueprint $table) {
            $table->id();
            $table->string('kat')->nullable();
            $table->timestamps();
        });

        Schema::table('master_category', function (Blueprint $table) {
            $table->string('metode_pembayaran')->nullable();
            $table->string('kat')->nullable();
        });
    }

       /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_metode_pembayaran');

        Schema::dropIfExists('global_category_resi');

        Schema::table('master_category', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
            $table->dropColumn('kat');
        });
    }
};
