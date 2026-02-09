<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterBidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_bidang')->insert([
            ['nama' => 'Peternakan'],
            ['nama' => 'Perikanan Budidaya'],
            ['nama' => 'Perikanan Tangkap'],
        ]);
    }
}
