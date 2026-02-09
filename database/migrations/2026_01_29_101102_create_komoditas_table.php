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
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_bidang_id')
          ->constrained('master_bidang')
          ->cascadeOnDelete();

    $table->string('kode')->unique();
    $table->string('nama');
    $table->string('satuan_default'); // ekor, kg, ton
    $table->boolean('is_individual')->default(false);
    $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
