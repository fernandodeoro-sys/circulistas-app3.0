@extends('layouts.app')

@section('content')

<!-- Botón de Volver -->
<div class="mb-6">
    <a href="{{ route('eventos.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al listado
    </a>
</div>

<!-- Título de Sección -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Editar Evento</h1>
    <p class="mt-1.5 text-sm text-slate-500">Modifica los detalles organizativos del evento seleccionado.</p>
</div>

<!-- Formulario principal con carga de archivos -->
<form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Bloque Principal (2 Columnas de ancho en LG) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos de Identificación y Lugar -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Identificación del Evento</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tipo_evento_id" class="block text-sm font-semibold text-slate-700">Tipo de Evento <span class="text-red-500">*</span></label>
                        <select name="tipo_evento_id" id="tipo_evento_id" required
                                class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('tipo_evento_id') border-red-300 ring-2 ring-red-500/10 @enderror">
                            <option value="">-- Selecciona un tipo --</option>
                            @foreach($tiposEvento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_evento_id', $evento->tipo_evento_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }} ({{ $tipo->descripcion }})
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_evento_id')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_evento" class="block text-sm font-semibold text-slate-700">Número de Evento <span class="text-red-500">*</span></label>
                        <input type="number" name="numero_evento" id="numero_evento" value="{{ old('numero_evento', $evento->numero_evento) }}" min="1"
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('numero_evento') border-red-300 ring-2 ring-red-500/10 @enderror"
                               placeholder="Ej: 3" required>
                        @error('numero_evento')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="lugar" class="block text-sm font-semibold text-slate-700">Lugar <span class="text-red-500">*</span></label>
                    <input type="text" name="lugar" id="lugar" value="{{ old('lugar', $evento->lugar) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('lugar') border-red-300 ring-2 ring-red-500/10 @enderror"
                           placeholder="Ej: Casa de Retiros Villa Marista" required>
                    @error('lugar')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Rango de Fechas -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Fechas de Realización</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-semibold text-slate-700">Fecha de Inicio <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $evento->fecha_inicio?->format('Y-m-d')) }}" required
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('fecha_inicio') border-red-300 ring-2 ring-red-500/10 @enderror">
                        @error('fecha_inicio')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-semibold text-slate-700">Fecha de Cierre <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $evento->fecha_fin?->format('Y-m-d')) }}" required
                               class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('fecha_fin') border-red-300 ring-2 ring-red-500/10 @enderror">
                        @error('fecha_fin')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra Lateral (1 Columna de ancho en LG) -->
        <div class="space-y-6">
            <!-- Estado y Observaciones -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Estado y Notas</h2>
                
                <!-- Toggle Switch para Activo -->
                <div class="flex items-center justify-between py-1.5">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">¿Evento Activo?</span>
                        <span class="block text-xs text-slate-400">Permite ver y gestionar participantes</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="activo" value="1" class="sr-only peer" 
                               {{ old('activo', $evento->activo) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-semibold text-slate-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="4"
                              class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                              placeholder="Detalles organizativos o notas generales...">{{ old('observaciones', $evento->observaciones) }}</textarea>
                </div>
            </div>

            <!-- Carga de Fotos e Imágenes Actuales -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Fotos e Imágenes</h2>
                
                <!-- Foto Evento -->
                <div>
                    <label for="foto_evento" class="block text-sm font-semibold text-slate-700">Foto del Evento (Grupal)</label>
                    @if($evento->foto_evento)
                        <div class="mt-2 mb-3 relative rounded-xl overflow-hidden border border-slate-200 bg-slate-50 aspect-video">
                            <img src="{{ $evento->foto_evento_url }}" alt="Foto del Evento" class="object-cover w-full h-full">
                        </div>
                    @endif
                    <input type="file" name="foto_evento" id="foto_evento" accept="image/*"
                           class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer @error('foto_evento') ring-2 ring-red-500/10 @enderror">
                    @error('foto_evento')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Cocina -->
                <div class="border-t border-slate-50 pt-4">
                    <label for="foto_cocina" class="block text-sm font-semibold text-slate-700">Foto de la Cocina / Servidores</label>
                    @if($evento->foto_cocina)
                        <div class="mt-2 mb-3 relative rounded-xl overflow-hidden border border-slate-200 bg-slate-50 aspect-video">
                            <img src="{{ $evento->foto_cocina_url }}" alt="Foto de la Cocina" class="object-cover w-full h-full">
                        </div>
                    @endif
                    <input type="file" name="foto_cocina" id="foto_cocina" accept="image/*"
                           class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer @error('foto_cocina') ring-2 ring-red-500/10 @enderror">
                    @error('foto_cocina')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

    </div>

    <!-- Botones de Acción al fondo -->
    <div class="flex items-center justify-end gap-3 border-t border-slate-200/60 pt-6">
        <a href="{{ route('eventos.index') }}" 
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            Cancelar
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 hover:shadow-lg focus:outline-none">
            Guardar Cambios
        </button>
    </div>

</form>

@endsection
