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
       Schema::create('eventos', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->foreignId('tipo_evento_id')
                ->constrained('tipos_evento')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('numero_evento');

            $table->string('lugar', 255);

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->string('foto_evento')->nullable();
            $table->string('foto_cocina')->nullable();

            $table->boolean('activo')->default(true);

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(
                ['tipo_evento_id', 'numero_evento'],
                'uk_evento_numero_por_tipo'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
