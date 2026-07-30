<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposEventoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_evento')->insert([
            [
                'nombre' => 'Enganche',
                'descripcion' => 'Retiro de Enganche'
            ],
            [
                'nombre' => 'Eslabón',
                'descripcion' => 'Retiro de Eslabón'
            ],
            [
                'nombre' => 'Jornada Eslabon',
                'descripcion' => 'Jornada de Vida Cristiana'
            ],
            [
                'nombre' => 'Senda',
                'descripcion' => 'Retiro de Senda'
            ],
            [
                'nombre' => 'Jornada Senda',
                'descripcion' => 'Jornada para participantes de Senda'
            ],
            [
                'nombre' => 'Retiro Mariano',
                'descripcion' => 'Retiro Mariano'
            ]
        ]);
    }
}