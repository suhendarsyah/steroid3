<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterKegiatanTeknisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_kegiatan_teknis')->insert([
            ['nama' => 'Penimbangan'],
            ['nama' => 'Vaksinasi'],
            ['nama' => 'Kelahiran'],
            ['nama' => 'Kematian'],
            ['nama' => 'Panen'],
        ]);
    }
}
