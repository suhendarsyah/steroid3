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
        Schema::table('upts', function (Blueprint $table) {
            // Tambah jenis UPT
            if (! Schema::hasColumn('upts', 'jenis_upt')) {
                $table->enum('jenis_upt', ['wilayah', 'tematis'])
                      ->after('nama')
                      ->default('wilayah');
            }

            // HAPUS semua kode terkait bidang_id karena tidak ada
            // ❌ Jangan drop foreign key atau kolom bidang_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upts', function (Blueprint $table) {
            // Hapus jenis_upt
            if (Schema::hasColumn('upts', 'jenis_upt')) {
                $table->dropColumn('jenis_upt');
            }

            // ❌ Jangan tambahkan kembali bidang_id
        });
    }
};
