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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');

            $table->foreignId('master_bidang_id')
                ->constrained('master_bidang')
                ->cascadeOnDelete();

            $table->foreignId('komoditas_id')
                ->constrained('komoditas')
                ->cascadeOnDelete();

            $table->bigInteger('target_jumlah');
                $table->timestamps();

            $table->unique(['tahun', 'master_bidang_id', 'komoditas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
