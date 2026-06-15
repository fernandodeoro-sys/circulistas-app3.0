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
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Nuevo Circulista</h1>
    <p class="mt-1.5 text-sm text-slate-500">Ingresa los datos para registrar un nuevo circulista en el padrón.</p>
</div>

<!-- Alerta de duplicado (Premium Tailwind) -->
<div id="alerta-duplicado" class="hidden rounded-2xl border border-amber-200 bg-amber-50/70 p-4 mb-6 transition duration-300">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 text-amber-600 mt-0.5">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-amber-800">¡Posible circulista duplicado!</h3>
            <div class="mt-1 text-sm text-amber-700">
                <p>Ya existe alguien registrado con el nombre <strong id="duplicado-nombre"></strong>. Puedes ver su ficha aquí: 
                   <a id="duplicado-link" href="#" target="_blank" class="font-bold underline hover:text-amber-900 inline-flex items-center gap-0.5">
                       Ver Ficha del Circulista
                       <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                       </svg>
                   </a>.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Formulario principal -->
<form action="{{ route('circulistas.store') }}" method="POST" class="space-y-8">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Bloque Principal: Datos de Perfil (2 Columnas de ancho en LG) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos Personales -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Datos Personales</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="apellido" class="block text-sm font-semibold text-slate-700">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('apellido') border-red-300 ring-2 ring-red-500/10 @enderror"
                               placeholder="Ej: Pérez" required>
                        @error('apellido')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nombre') border-red-300 ring-2 ring-red-500/10 @enderror"
                               placeholder="Ej: Juan Ramón" required>
                        @error('nombre')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-slate-700">Fecha de Nacimiento / Cumpleaños</label>
                    
                    <!-- Opciones de tipo de fecha -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="completa" class="text-indigo-600 focus:ring-indigo-500 mr-2" 
                                   {{ old('fecha_nacimiento_tipo', 'completa') === 'completa' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            Fecha completa
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="solo_dia_mes" class="text-indigo-600 focus:ring-indigo-500 mr-2"
                                   {{ old('fecha_nacimiento_tipo') === 'solo_dia_mes' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            Solo día y mes
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                            <input type="radio" name="fecha_nacimiento_tipo" value="ninguna" class="text-indigo-600 focus:ring-indigo-500 mr-2"
                                   {{ old('fecha_nacimiento_tipo') === 'ninguna' ? 'checked' : '' }} onchange="toggleFechaNacimientoTipo()">
                            No registrar
                        </label>
                    </div>

                    <!-- Input Fecha Completa -->
                    <div id="container-fecha-completa" class="{{ old('fecha_nacimiento_tipo', 'completa') === 'completa' ? '' : 'hidden' }}">
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                               class="block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('fecha_nacimiento') border-red-300 ring-2 ring-red-500/10 @enderror">
                        @error('fecha_nacimiento')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Inputs Solo Día y Mes -->
                    <div id="container-fecha-dia-mes" class="grid grid-cols-2 gap-4 {{ old('fecha_nacimiento_tipo') === 'solo_dia_mes' ? '' : 'hidden' }}">
                        <div>
                            <label for="nacimiento_dia" class="block text-xs font-semibold text-slate-500 mb-1">Día</label>
                            <select name="nacimiento_dia" id="nacimiento_dia" 
                                    class="block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nacimiento_dia') border-red-300 ring-2 ring-red-500/10 @enderror">
                                <option value="">Selecciona el día...</option>
                                @for ($d = 1; $d <= 31; $d++)
                                    <option value="{{ $d }}" {{ old('nacimiento_dia') == $d ? 'selected' : '' }}>{{ $d }}</option>
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
                                    <option value="{{ $num }}" {{ old('nacimiento_mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
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
                    <input type="text" name="domicilio" id="domicilio" value="{{ old('domicilio') }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: Av. San Martín 123">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="localidad" class="block text-sm font-semibold text-slate-700">Localidad</label>
                        <input type="text" name="localidad" id="localidad" value="{{ old('localidad') }}"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div>
                        <label for="provincia" class="block text-sm font-semibold text-slate-700">Provincia</label>
                        <input type="text" name="provincia" id="provincia" value="{{ old('provincia') }}"
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
                    <input type="text" name="celular" id="celular" value="{{ old('celular') }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: 2615555555">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-semibold text-slate-700">Teléfono Fijo</label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="Ej: 2614200000">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
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
                        <input type="checkbox" name="activo" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-semibold text-slate-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="4"
                              class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                              placeholder="Cualquier aclaración sobre el circulista...">{{ old('observaciones') }}</textarea>
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
            Crear Circulista
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

    document.addEventListener('DOMContentLoaded', function() {
        const nombreInput = document.getElementById('nombre');
        const apellidoInput = document.getElementById('apellido');
        const celularInput = document.getElementById('celular');
        const alertaDuplicado = document.getElementById('alerta-duplicado');
        const duplicadoNombre = document.getElementById('duplicado-nombre');
        const duplicadoLink = document.getElementById('duplicado-link');

        let timeout = null;

        function buscarDuplicado() {
            const nombre = nombreInput.value.trim();
            const apellido = apellidoInput.value.trim();
            const celular = celularInput ? celularInput.value.trim() : '';

            const cleanCel = celular.replace(/[^\d]/g, '');
            if ((nombre.length < 2 || apellido.length < 2) && cleanCel.length < 7) {
                alertaDuplicado.classList.add('hidden');
                return;
            }

            fetch('{{ route('circulistas.verificarDuplicado') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nombre: nombre,
                    apellido: apellido,
                    celular: celular
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    duplicadoNombre.textContent = data.nombre_completo;
                    duplicadoLink.href = data.url;
                    alertaDuplicado.classList.remove('hidden');
                } else {
                    alertaDuplicado.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error al verificar duplicado:', error));
        }

        function debouncedBuscar() {
            clearTimeout(timeout);
            timeout = setTimeout(buscarDuplicado, 500);
        }

        nombreInput.addEventListener('input', debouncedBuscar);
        apellidoInput.addEventListener('input', debouncedBuscar);
        if (celularInput) {
            celularInput.addEventListener('input', debouncedBuscar);
        }
    });
</script>

@endsection
