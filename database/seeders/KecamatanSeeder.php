<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kecamatans')->insert([
            ['nama' => 'Wanaraja'],
            ['nama' => 'Tarogong Kaler'],
            ['nama' => 'Tarogong Kidul'],
        ]);
    }
}
