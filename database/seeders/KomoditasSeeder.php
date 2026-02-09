<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Komoditas;

class KomoditasSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PETERNakan
        |--------------------------------------------------------------------------
        */
        Komoditas::firstOrCreate(
            ['kode' => 'SP_SAPI'],
            [
                'nama' => 'Sapi',
                'master_bidang_id' => 1,
                'satuan_default' => 'ekor',
                'is_individual' => true,
                'is_active' => true,
            ]
        );

        Komoditas::firstOrCreate(
            ['kode' => 'SP_KAMBING'],
            [
                'nama' => 'Kambing',
                'master_bidang_id' => 1,
                'satuan_default' => 'ekor',
                'is_individual' => true,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PERIKANAN BUDIDAYA
        |--------------------------------------------------------------------------
        */
        Komoditas::firstOrCreate(
            ['kode' => 'IK_NILA'],
            [
                'nama' => 'Ikan Nila',
                'master_bidang_id' => 2,
                'satuan_default' => 'kg',
                'is_individual' => false,
                'is_active' => true,
            ]
        );

        Komoditas::firstOrCreate(
            ['kode' => 'IK_LELE'],
            [
                'nama' => 'Ikan Lele',
                'master_bidang_id' => 2,
                'satuan_default' => 'kg',
                'is_individual' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PERIKANAN TANGKAP
        |--------------------------------------------------------------------------
        */
        Komoditas::firstOrCreate(
            ['kode' => 'IK_TUNA'],
            [
                'nama' => 'Ikan Tuna',
                'master_bidang_id' => 3,
                'satuan_default' => 'kg',
                'is_individual' => false,
                'is_active' => true,
            ]
        );
    }
}


