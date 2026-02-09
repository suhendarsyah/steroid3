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
        Schema::create('data_teknis', function (Blueprint $table) {
            $table->id();

             // objek ternak/ikan per individu atau kelompok
            $table->foreignId('objek_produksi_id')
                ->constrained('objek_produksis')
                ->cascadeOnDelete();

            // jenis kegiatan diambil dari master, bukan teks bebas
            $table->foreignId('kegiatan_id')
                ->constrained('master_kegiatan_teknis');

            $table->date('tanggal');
            $table->decimal('nilai', 12, 2)->nullable(); // misal berat, jumlah panen, dll
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_teknis');
    }
};
