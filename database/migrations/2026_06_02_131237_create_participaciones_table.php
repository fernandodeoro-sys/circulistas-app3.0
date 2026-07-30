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
       Schema::create('participaciones', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->foreignId('circulista_id')
                ->constrained('circulistas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('evento_id')
                ->constrained('eventos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('rol_id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('grupo', 50)->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(
                ['circulista_id', 'evento_id'],
                'uk_circulista_evento'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participaciones');
    }
};
