@extends('layouts.app')

@section('content')

<!-- Botón de Volver -->
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('eventos.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al listado
    </a>

    <div class="flex items-center gap-2">
        <a href="{{ route('eventos.edit', $evento->id) }}" 
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4.5 w-4.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar Evento
        </a>

        <form action="{{ route('eventos.destroy', $evento->id) }}" 
              method="POST" 
              class="inline" 
              onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente este evento?');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">
                <svg class="h-4.5 w-4.5 mr-1.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Eliminar
            </button>
        </form>
    </div>
</div>

<!-- Grid de Información -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    
    <!-- Ficha Técnica del Evento -->
    <div class="lg:col-span-1 space-y-6">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <!-- Encabezado Ficha -->
            <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 font-bold text-lg ring-1 ring-indigo-500/10">
                    {{ substr($evento->tipoEvento->nombre ?? 'E', 0, 2) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">
                        {{ $evento->tipoEvento->nombre ?? 'Sin tipo' }}
                    </h2>
                    <span class="text-sm font-semibold text-slate-600">Número #{{ $evento->numero_evento }}</span>
                </div>
            </div>
            
            <!-- Detalles en lista -->
            <div class="mt-5 space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado</span>
                    <div class="mt-1">
                        @if($evento->activo)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Lugar</span>
                    <span class="text-sm font-medium text-slate-800 flex items-center gap-1.5 mt-1">
                        <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        {{ $evento->lugar }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Inicio</span>
                        <span class="text-sm font-medium text-slate-800 block mt-0.5">{{ $evento->fecha_inicio->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Fin</span>
                        <span class="text-sm font-medium text-slate-800 block mt-0.5">{{ $evento->fecha_fin->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-50 pt-4 space-y-2.5">
                    <div>
                        <span class="block text-xs text-slate-400">Creado el: {{ $evento->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Actualizado el: {{ $evento->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fotos y Observaciones -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Observaciones -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Observaciones / Notas</h3>
            @if($evento->observaciones)
                <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600 leading-relaxed border border-slate-100">
                    {!! nl2br(e($evento->observaciones)) !!}
                </div>
            @else
                <p class="text-sm italic text-slate-400">Sin notas de organización registradas para este evento.</p>
            @endif
        </div>

        <!-- Imágenes del Evento -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Foto Evento -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-3">
                <h4 class="text-sm font-bold text-slate-900">Foto del Evento (Retiro)</h4>
                @if($evento->foto_evento)
                    <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-video bg-slate-50 shadow-sm transition hover:shadow-md">
                        <a href="{{ Storage::url($evento->foto_evento) }}" target="_blank">
                            <img src="{{ Storage::url($evento->foto_evento) }}" alt="Foto del Evento" class="object-cover w-full h-full">
                        </a>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-xl aspect-video text-slate-400">
                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs mt-1.5">Sin imagen de grupo</span>
                    </div>
                @endif
            </div>

            <!-- Foto Cocina -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-3">
                <h4 class="text-sm font-bold text-slate-900">Foto de Cocina / Servidores</h4>
                @if($evento->foto_cocina)
                    <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-video bg-slate-50 shadow-sm transition hover:shadow-md">
                        <a href="{{ Storage::url($evento->foto_cocina) }}" target="_blank">
                            <img src="{{ Storage::url($evento->foto_cocina) }}" alt="Foto de la Cocina" class="object-cover w-full h-full">
                        </a>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-xl aspect-video text-slate-400">
                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs mt-1.5">Sin imagen de cocina</span>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

<!-- Sección de Participantes -->
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Participantes del Evento</h3>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Total: {{ $evento->participaciones->count() }} personas registradas
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Imprimir Circular Retiro -->
            <a href="{{ route('eventos.circular-retiro', $evento->id) }}" 
               target="_blank"
               class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100">
                <svg class="-ml-0.5 mr-1.5 h-4.5 w-4.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-9v2m-5-2v2m-5-2v2" />
                </svg>
                Circular Retiro
            </a>

            <!-- Imprimir Circular Cocina -->
            <a href="{{ route('eventos.circular-cocina', $evento->id) }}" 
               target="_blank"
               class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 shadow-sm transition hover:bg-amber-100">
                <svg class="-ml-0.5 mr-1.5 h-4.5 w-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-9v2m-5-2v2m-5-2v2" />
                </svg>
                Circular Cocina
            </a>

            <!-- Agregar Participante -->
            <button type="button" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addParticipantModal"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Agregar Participante
            </button>
        </div>
    </div>

    @if($evento->participaciones->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-500/10 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-800">No hay participantes aún</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm">
                Este evento aún no tiene circulistas registrados. Haz clic en "Agregar Participante" para inscribir o asignar roles al equipo de servidores.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Circulista</th>
                        <th class="py-3 px-4">Rol</th>
                        <th class="py-3 px-4">Grupo</th>
                        <th class="py-3 px-4">Observaciones</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-medium text-slate-700">
                    @foreach($evento->participaciones as $participacion)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                        {{ strtoupper(substr($participacion->circulista->nombre, 0, 1) . substr($participacion->circulista->apellido, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('circulistas.show', $participacion->circulista_id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                            {{ $participacion->circulista->apellido }}, {{ $participacion->circulista->nombre }}
                                        </a>
                                        <span class="text-xs font-normal text-slate-400">
                                            {{ $participacion->circulista->celular ?: $participacion->circulista->email ?: 'Sin datos de contacto' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $rolNombre = $participacion->rol->nombre;
                                    $badgeClass = 'bg-slate-50 text-slate-700 ring-slate-600/10';
                                    if ($rolNombre === 'Rector' || $rolNombre === 'Vice Rector') {
                                        $badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-700/10';
                                    } elseif ($rolNombre === 'Peregrino' || $rolNombre === 'Participante Enganche' || $rolNombre === 'Participante Retiro Mariano') {
                                        $badgeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/10';
                                    } elseif (str_contains(strtolower($rolNombre), 'cocina') || str_contains(strtolower($rolNombre), 'cocinero')) {
                                        $badgeClass = 'bg-amber-50 text-amber-700 ring-amber-600/10';
                                    } elseif ($rolNombre === 'Servidor' || $rolNombre === 'Mensajero' || $rolNombre === 'Ganchista' || $rolNombre === 'Asistente') {
                                        $badgeClass = 'bg-sky-50 text-sky-700 ring-sky-600/10';
                                    }
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                    {{ $rolNombre }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $participacion->grupo ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-normal max-w-xs truncate" title="{{ $participacion->observaciones }}">
                                {{ $participacion->observaciones ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2.5">
                                    <!-- Editar -->
                                    <button type="button" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editParticipantModal"
                                            data-id="{{ $participacion->id }}"
                                            data-name="{{ $participacion->circulista->apellido }}, {{ $participacion->circulista->nombre }}"
                                            data-rol-id="{{ $participacion->rol_id }}"
                                            data-grupo="{{ $participacion->grupo }}"
                                            data-observaciones="{{ $participacion->observaciones }}"
                                            class="inline-flex items-center justify-center text-slate-400 hover:text-slate-900 transition p-1 hover:bg-slate-100 rounded-lg cursor-pointer">
                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Eliminar -->
                                    <form action="{{ route('participaciones.destroy', $participacion->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de que deseas quitar a este participante del evento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center text-slate-400 hover:text-red-600 transition p-1 hover:bg-red-50 rounded-lg cursor-pointer">
                                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal Agregar Participante -->
<div class="modal fade" id="addParticipantModal" tabindex="-1" aria-labelledby="addParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <div class="modal-header border-b border-slate-100 p-4">
                <h5 class="modal-title font-bold text-slate-900" id="addParticipantModalLabel">Agregar Participante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('participaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                
                <div class="modal-body p-4 space-y-4">
                    <!-- Buscar y Seleccionar Circulistas -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Seleccionar Circulistas</label>
                        @if($circulistasDisponibles->isEmpty())
                            <div class="rounded-xl bg-amber-50 border border-amber-100 p-3 text-xs text-amber-800 font-medium">
                                No hay circulistas activos disponibles para agregar. Todos los registrados ya están participando o no hay circulistas cargados en el sistema.
                            </div>
                        @else
                            <input type="text" 
                                   id="circulista-search" 
                                   placeholder="🔍 Buscar circulista por nombre..." 
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none mb-2"
                                   onkeyup="filterCirculistas()">
                            
                            <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                                <div id="circulistas-list-container" class="max-h-48 overflow-y-auto p-2.5 space-y-1">
                                    @foreach($circulistasDisponibles as $circulista)
                                        <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-indigo-50/50 cursor-pointer text-sm text-slate-700 transition">
                                            <input type="checkbox" 
                                                   name="circulista_ids[]" 
                                                   value="{{ $circulista->id }}" 
                                                   class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-medium text-slate-800">{{ $circulista->apellido }}, {{ $circulista->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <span class="block text-[10px] font-semibold text-slate-400 mt-1.5 uppercase tracking-wide">💡 Tip: Puedes seleccionar varios circulistas a la vez</span>
                        @endif
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="rol_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rol en el Evento</label>
                        <select name="rol_id" 
                                id="rol_id" 
                                required 
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none bg-white">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Grupo -->
                    <div>
                        <label for="grupo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Grupo (Opcional)</label>
                        <input type="text" 
                               name="grupo" 
                               id="grupo" 
                               placeholder="Ej: Grupo 3, Cocina A, etc." 
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Observaciones (Opcional)</label>
                        <textarea name="observaciones" 
                                  id="observaciones" 
                                  rows="2" 
                                  placeholder="Notas o comentarios..." 
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-t border-slate-100 p-4 flex gap-2">
                    <button type="button" class="flex-1 rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    @if(!$circulistasDisponibles->isEmpty())
                        <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                            Guardar
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Participación -->
<div class="modal fade" id="editParticipantModal" tabindex="-1" aria-labelledby="editParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <div class="modal-header border-b border-slate-100 p-4">
                <h5 class="modal-title font-bold text-slate-900" id="editParticipantModalLabel">Editar Participación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body p-4 space-y-4">
                    <!-- Circulista (Solo Lectura) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Circulista</label>
                        <input type="text" 
                               id="edit-name" 
                               readonly 
                               class="w-full rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm text-slate-500 focus:outline-none">
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="edit-rol" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rol en el Evento</label>
                        <select name="rol_id" 
                                id="edit-rol" 
                                required 
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none bg-white">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Grupo -->
                    <div>
                        <label for="edit-grupo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Grupo (Opcional)</label>
                        <input type="text" 
                               name="grupo" 
                               id="edit-grupo" 
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="edit-observaciones" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Observaciones (Opcional)</label>
                        <textarea name="observaciones" 
                                  id="edit-observaciones" 
                                  rows="2" 
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-t border-slate-100 p-4 flex gap-2">
                    <button type="button" class="flex-1 rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function filterCirculistas() {
        const query = document.getElementById('circulista-search').value.toLowerCase();
        const container = document.getElementById('circulistas-list-container');
        if (!container) return;
        
        const labels = container.getElementsByTagName('label');
        for (let i = 0; i < labels.length; i++) {
            const text = labels[i].textContent.toLowerCase();
            if (text.includes(query)) {
                labels[i].style.setProperty('display', 'flex', 'important');
            } else {
                labels[i].style.setProperty('display', 'none', 'important');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editParticipantModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const rolId = button.getAttribute('data-rol-id');
                const grupo = button.getAttribute('data-grupo');
                const observaciones = button.getAttribute('data-observaciones');

                const form = editModal.querySelector('form');
                form.action = `{{ url('participaciones') }}/${id}`;

                editModal.querySelector('#edit-name').value = name;
                editModal.querySelector('#edit-rol').value = rolId;
                editModal.querySelector('#edit-grupo').value = grupo || '';
                editModal.querySelector('#edit-observaciones').value = observaciones || '';
            });
        }
    });
</script>

@endsection
