<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'Circulista'],
            ['nombre' => 'Peregrino'],
            ['nombre' => 'Rector'],
            ['nombre' => 'Vice Rector'],
            ['nombre' => 'Asistente'],
            ['nombre' => 'Jefe Cocina'],
            ['nombre' => 'Cocinero'],
            ['nombre' => 'Integrante de Cocina'],
            ['nombre' => 'Asesor'],
            ['nombre' => 'Mensajero'],
            ['nombre' => 'Ganchista'],
            ['nombre' => 'Servidor'],
            ['nombre' => 'Participante Enganche'],
            ['nombre' => 'Participante Retiro Mariano']
        ]);
    }
}