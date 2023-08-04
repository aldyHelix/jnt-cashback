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
        Schema::create('log_resi', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('periode_id');
            $table->string('batch_id');
            $table->string('resi');
            $table->text('before_raw')->nullable();
            $table->text('after_raw')->nullable();
            $table->string('type')->nullable();
            $table->string('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_resi');
    }
};
