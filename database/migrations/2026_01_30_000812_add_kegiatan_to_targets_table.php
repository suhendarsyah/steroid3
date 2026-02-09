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
        Schema::table('targets', function (Blueprint $table) {
            $table->foreignId('master_kegiatan_teknis_id')
                ->after('komoditas_id')
                ->constrained('master_kegiatan_teknis')
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropForeign(['master_kegiatan_teknis_id']);
            $table->dropColumn('master_kegiatan_teknis_id');
        });
    }
};
