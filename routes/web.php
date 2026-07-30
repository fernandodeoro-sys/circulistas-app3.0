<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CirculistaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ParticipacionController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Rutas Públicas (Huéspedes)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas Protegidas (Autenticados)
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('circulistas.index');
    });

    // ----------------------------------------------------
    // CIRCULISTAS
    // ----------------------------------------------------
    Route::get('circulistas', [CirculistaController::class, 'index'])->name('circulistas.index');
    
    // Rutas específicas del Administrador/Supervisor (registradas antes del comodín {circulista})
    Route::middleware('role:administrador,supervisor')->group(function () {
        Route::get('circulistas/create', [CirculistaController::class, 'create'])->name('circulistas.create');
        Route::get('circulistas/duplicados', [CirculistaController::class, 'duplicados'])->name('circulistas.duplicados');
        Route::get('circulistas/{circulista}/edit', [CirculistaController::class, 'edit'])->name('circulistas.edit');
    });

    Route::get('circulistas/{circulista}', [CirculistaController::class, 'show'])->name('circulistas.show');

    Route::middleware('role:administrador,supervisor')->group(function () {
        Route::post('circulistas', [CirculistaController::class, 'store'])->name('circulistas.store');
        Route::put('circulistas/{circulista}', [CirculistaController::class, 'update'])->name('circulistas.update');
        Route::delete('circulistas/{circulista}', [CirculistaController::class, 'destroy'])->name('circulistas.destroy');
        Route::post('circulistas/verificar-duplicado', [CirculistaController::class, 'verificarDuplicado'])->name('circulistas.verificarDuplicado');
        Route::post('circulistas/verificar-importables', [CirculistaController::class, 'verificarImportables'])->name('circulistas.verificarImportables');
    });

    // ----------------------------------------------------
    // EVENTOS
    // ----------------------------------------------------
    Route::get('eventos', [EventoController::class, 'index'])->name('eventos.index');

    Route::middleware('role:administrador,supervisor')->group(function () {
        Route::get('eventos/importar/masivo', [EventoController::class, 'showImportForm'])->name('eventos.import.form');
        Route::post('eventos/importar/masivo', [EventoController::class, 'importMasivo'])->name('eventos.import.submit');
        Route::get('eventos/create', [EventoController::class, 'create'])->name('eventos.create');
        Route::get('eventos/{evento}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
    });

    Route::get('eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
    Route::get('eventos/{evento}/circular-retiro', [EventoController::class, 'circularRetiro'])->name('eventos.circular-retiro');
    Route::get('eventos/{evento}/circular-cocina', [EventoController::class, 'circularCocina'])->name('eventos.circular-cocina');

    Route::middleware('role:administrador,supervisor')->group(function () {
        Route::post('eventos', [EventoController::class, 'store'])->name('eventos.store');
        Route::put('eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
        Route::delete('eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
    });

    // ----------------------------------------------------
    // OTROS (Búsqueda, Participaciones, Usuarios CRUD)
    // ----------------------------------------------------
    Route::middleware('role:administrador,supervisor')->group(function () {
        Route::get('busqueda-avanzada', [BusquedaController::class, 'index'])->name('busqueda.avanzada');
        Route::resource('participaciones', ParticipacionController::class)->only(['store', 'update', 'destroy']);
    });

    Route::middleware('role:administrador')->group(function () {
        Route::resource('usuarios', UserController::class);
    });
});