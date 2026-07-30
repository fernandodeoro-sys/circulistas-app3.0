<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador
        User::updateOrCreate(
            ['email' => 'admin@mcj.org'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'administrador',
            ]
        );

        // Invitado
        User::updateOrCreate(
            ['email' => 'invitado@mcj.org'],
            [
                'name' => 'Invitado',
                'password' => Hash::make('invitado123'),
                'role' => 'invitado',
            ]
        );

        // Supervisor
        User::updateOrCreate(
            ['email' => 'supervisor@mcj.org'],
            [
                'name' => 'Supervisor',
                'password' => Hash::make('supervisor123'),
                'role' => 'supervisor',
            ]
        );
    }
}
