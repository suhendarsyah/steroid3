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
        Schema::table('objek_produksis', function (Blueprint $table) {
            $table->integer('populasi_awal')
                  ->default(0)
                  ->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objek_produksis', function (Blueprint $table) {
             $table->dropColumn('populasi_awal');
        });
    }
};
