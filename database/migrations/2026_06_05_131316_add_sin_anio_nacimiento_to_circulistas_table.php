<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('circulistas', function (Blueprint $table) {
            $table->boolean('sin_anio_nacimiento')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circulistas', function (Blueprint $table) {
            $table->dropColumn('sin_anio_nacimiento');
        });
    }
};
