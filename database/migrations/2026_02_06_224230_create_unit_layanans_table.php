<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_layanans', function (Blueprint $table) {
            $table->id();

            // relasi ke UPT (organisasi induk)
            $table->foreignId('upt_id')
                ->constrained('upts')
                ->cascadeOnDelete();

            // nama unit layanan
            $table->string('nama');

            // keterangan opsional
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_layanans');
    }
};
