<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tipodisp extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipos_dispositivos')->insert([
            ['nombre' => 'temperatura'],
            ['nombre' => 'humedad'],
            ['nombre' => 'sonido'],
            ['nombre' => 'voltaje'],
            ['nombre' => 'polvo'],
            ['nombre' => 'humo '],
            ['nombre' => 'nfc'],
        ]);
    }
}
