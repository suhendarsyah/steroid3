<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('desas')->insert([
            ['kecamatan_id' => 1, 'nama' => 'Desa A'],
            ['kecamatan_id' => 1, 'nama' => 'Desa B'],
        ]);
    }
}
