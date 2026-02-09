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
            $table->foreignId('upt_id')
                ->nullable()
                ->constrained('upts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objek_produksis', function (Blueprint $table) {
            $table->dropForeign(['upt_id']);
            $table->dropColumn('upt_id');
        });
    }
};

// DB::table('upts')->insert([
//     ['nama' => 'UPT Wilatah 1', 'jenis' => 'wilayah'],
//     ['nama' => 'UPT Wilayah 2', 'jenis' => 'wilayah'],
//     ['nama' => 'UPT RPH', 'jenis' => 'tematik'],
// ]);

