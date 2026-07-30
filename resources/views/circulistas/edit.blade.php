@extends('layouts.app')

@section('content')

<!-- Botón de Volver -->
<div class="mb-6">
    <a href="{{ route('circulistas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al listado
    </a>
</div>

<!-- Título de Sección -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Editar Circulista</h1>
    <p class="mt-1.5 text-sm text-slate-500">Actualiza los datos del circulista en el padrón.</p>
</div>

<!-- Formulario principal -->
<form action="{{ route('circulistas.update', $circulista->id) }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Bloque Principal: Datos de Perfil (2 Columnas de ancho en LG) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos Personales -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Datos Personales</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="apellido" class="block text-sm font-semibold text-slate-700">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $circulista->apellido) }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('apellido') border-red-300 ring-2 ring-red-500/10 @enderror"
                               placeholder="Ej: Pérez" required>
                        @error('apellido')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $circulista->nombre) }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nombre') border-red-300 ring-2 ring-red-500/10 @enderror"
                               placeholder="Ej: Juan Ramón" required>
                        @error('nombre')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @php
                    $tipoFecha = 'ninguna';
                    $fechaVal = '';
                    $diaVal = '';
                    $mesVal = '';
                    if ($circulista->fecha_nacimiento) {
                        if ($circulista->sin_anio_nacimiento) {
                            $tipoFecha = 'solo_dia_mes';
                            $diaVal = $circulista->fecha_nacimiento->format('j');
                            $mesVal = $circulista->fecha_nacimiento->format('n');
                        } else {
                            $tipoFecha = 'completa';
                            $fechaVal = $circulista->fecha_nacimiento->format('Y-m-d');
                        }
                    }
                @endphp

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-slate-700">Fecha de Nacimiento / Cumpleaños</label>
                    
                    <!-- Opciones de tipo de fecha -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="completa" class="text-indigo-600 focus:ring-indigo-500 mr-2" 
                                   {{ old('fecha_nacimiento_tipo', $tipoFecha) === 'completa' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            Fecha completa
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="solo_dia_mes" class="text-indigo-600 focus:ring-indigo-500 mr-2"
                                   {{ old('fecha_nacimiento_tipo', $tipoFecha) === 'solo_dia_mes' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            Solo día y mes
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="ninguna" class="text-indigo-600 focus:ring-indigo-500 mr-2"
                                   {{ old('fecha_nacimiento_tipo', $tipoFecha) === 'ninguna' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            No registrar
                        </label>
                    </div>

                    <!-- Input Fecha Completa -->
                    <div id="container-fecha-completa" class="{{ old('fecha_nacimiento_tipo', $tipoFecha) === 'completa' ? '' : 'hidden' }}">
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento', $fechaVal) }}"
                               class="block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('fecha_nacimiento') border-red-300 ring-2 ring-red-500/10 @enderror">
                        @error('fecha_nacimiento')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Inputs Solo Día y Mes -->
                    <div id="container-fecha-dia-mes" class="grid grid-cols-2 gap-4 {{ old('fecha_nacimiento_tipo', $tipoFecha) === 'solo_dia_mes' ? '' : 'hidden' }}">
                        <div>
                            <label for="nacimiento_dia" class="block text-xs font-semibold text-slate-500 mb-1">Día</label>
                            <select name="nacimiento_dia" id="nacimiento_dia" 
                                    class="block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nacimiento_dia') border-red-300 ring-2 ring-red-500/10 @enderror">
                                <option value="">Selecciona el día...</option>
                                @for ($d = 1; $d <= 31; $d++)
                                    <option value="{{ $d }}" {{ old('nacimiento_dia', $diaVal) == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endfor
                            </select>
                            @error('nacimiento_dia')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nacimiento_mes" class="block text-xs font-semibold text-slate-500 mb-1">Mes</label>
                            <select name="nacimiento_mes" id="nacimiento_mes" 
                                    class="block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nacimiento_mes') border-red-300 ring-2 ring-red-500/10 @enderror">
                                <option value="">Selecciona el mes...</option>
                                @php
                                    $meses = [
                                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                    ];
                                @endphp
                                @foreach ($meses as $num => $nombre)
                                    <option value="{{ $num }}" {{ old('nacimiento_mes', $mesVal) == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                @endforeach
                            </select>
                            @error('nacimiento_mes')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Domicilio / Ubicación -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Domicilio y Ubicación</h2>
                
                <div>
                    <label for="domicilio" class="block text-sm font-semibold text-slate-700">Calle y Número (Domicilio)</label>
                    <input type="text" name="domicilio" id="domicilio" value="{{ old('domicilio', $circulista->domicilio) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: Av. San Martín 123">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="localidad" class="block text-sm font-semibold text-slate-700">Localidad</label>
                        <input type="text" name="localidad" id="localidad" value="{{ old('localidad', $circulista->localidad) }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div>
                        <label for="provincia" class="block text-sm font-semibold text-slate-700">Provincia</label>
                        <input type="text" name="provincia" id="provincia" value="{{ old('provincia', $circulista->provincia) }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra Lateral: Contacto y Estado (1 Columna de ancho en LG) -->
        <div class="space-y-6">
            <!-- Contacto -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Información de Contacto</h2>
                
                <div>
                    <label for="celular" class="block text-sm font-semibold text-slate-700">Celular</label>
                    <input type="text" name="celular" id="celular" value="{{ old('celular', $circulista->celular) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: 2615555555">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-semibold text-slate-700">Teléfono Fijo</label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $circulista->telefono) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: 2614200000">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $circulista->email) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('email') border-red-300 ring-2 ring-red-500/10 @enderror"
                           placeholder="Ej: usuario@correo.com">
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estado e Internos -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Estado y Observaciones</h2>
                
                <!-- Toggle Switch para Activo -->
                <div class="flex items-center justify-between py-1.5">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">¿Se encuentra activo?</span>
                        <span class="block text-xs text-slate-400">Determina si participa activamente en el padrón</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="activo" value="1" class="sr-only peer" 
                               {{ old('activo', $circulista->activo) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-semibold text-slate-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="4"
                              class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                              placeholder="Cualquier aclaración sobre el circulista...">{{ old('observaciones', $circulista->observaciones) }}</textarea>
                </div>
            </div>
        </div>

    </div>

    <!-- Botones de Acción al fondo -->
    <div class="flex items-center justify-end gap-3 border-t border-slate-200/60 pt-6">
        <a href="{{ route('circulistas.index') }}" 
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            Cancelar
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Guardar Cambios
        </button>
    </div>

</form>

<script>
    window.toggleFechaNacimientoTipo = function() {
        const selectedType = document.querySelector('input[name="fecha_nacimiento_tipo"]:checked')?.value;
        const containerCompleta = document.getElementById('container-fecha-completa');
        const containerDiaMes = document.getElementById('container-fecha-dia-mes');

        if (selectedType === 'completa') {
            containerCompleta.classList.remove('hidden');
            containerDiaMes.classList.add('hidden');
        } else if (selectedType === 'solo_dia_mes') {
            containerCompleta.classList.add('hidden');
            containerDiaMes.classList.remove('hidden');
        } else {
            containerCompleta.classList.add('hidden');
            containerDiaMes.classList.add('hidden');
        }
    };
</script>

@endsection
