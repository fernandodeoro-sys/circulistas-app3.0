<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En PostgreSQL, la extensión unaccent nos permite realizar búsquedas
        // ignorando acentos/tildes en textos (ej: 'Álvarez' coincide con 'Alvarez').
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: Se puede comentar o descomentar según la política de rollback de la BD
        // DB::statement('DROP EXTENSION IF EXISTS unaccent');
    }
};
