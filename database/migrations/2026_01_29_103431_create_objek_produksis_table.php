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
        Schema::create('objek_produksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemilik_id');
            $table->unsignedBigInteger('komoditas_id');

            $table->string('kode_identitas')->nullable();
            $table->integer('jumlah')->default(1);

            // FK manual (lebih aman)
            $table->foreign('pemilik_id')
                ->references('id')
                ->on('pemiliks')
                ->onDelete('cascade');

            $table->foreign('komoditas_id')
                ->references('id')
                ->on('komoditas')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objek_produksis');
    }
};
