<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CirculistaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ParticipacionController;
use App\Http\Controllers\BusquedaController;

Route::get('/', function () {
    return redirect('/circulistas');
});
Route::post('circulistas/verificar-duplicado', [CirculistaController::class, 'verificarDuplicado'])->name('circulistas.verificarDuplicado');
Route::resource('circulistas', CirculistaController::class);
Route::resource('eventos', EventoController::class);
Route::get('busqueda-avanzada', [BusquedaController::class, 'index'])->name('busqueda.avanzada');
Route::resource('participaciones', ParticipacionController::class)->only(['store', 'update', 'destroy']);