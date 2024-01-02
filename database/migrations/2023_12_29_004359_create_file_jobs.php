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
        Schema::create('file_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('schema_name');
            $table->string('file_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('disk')->default('local');
            $table->string('file_hash')->unique();
            $table->string('collection')->nullable();
            $table->tinyInteger('type_file')->nullable()->comment('0:cashback;1:ttd;');
            $table->tinyInteger('is_uploaded')->default(0);
            $table->tinyInteger('is_imported')->default(0);
            $table->tinyInteger('is_schema_created')->default(0);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_jobs');
    }
};
