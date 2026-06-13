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
Route::post('circulistas/verificar-importables', [CirculistaController::class, 'verificarImportables'])->name('circulistas.verificarImportables');
Route::resource('circulistas', CirculistaController::class);

Route::get('eventos/importar/masivo', [EventoController::class, 'showImportForm'])->name('eventos.import.form');
Route::post('eventos/importar/masivo', [EventoController::class, 'importMasivo'])->name('eventos.import.submit');
Route::get('eventos/{evento}/circular-retiro', [EventoController::class, 'circularRetiro'])->name('eventos.circular-retiro');
Route::get('eventos/{evento}/circular-cocina', [EventoController::class, 'circularCocina'])->name('eventos.circular-cocina');
Route::resource('eventos', EventoController::class);

Route::get('busqueda-avanzada', [BusquedaController::class, 'index'])->name('busqueda.avanzada');
Route::resource('participaciones', ParticipacionController::class)->only(['store', 'update', 'destroy']);