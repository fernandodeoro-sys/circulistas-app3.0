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
        Schema::create('circulistas', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('apellido', 100);
            $table->string('nombre', 100);

            $table->date('fecha_nacimiento')->nullable();

            $table->string('domicilio', 255)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->string('provincia', 100)->nullable();

            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();

            $table->string('email', 150)->nullable();

            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['apellido', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulistas');
    }
};
