@extends('layouts.app')

@section('content')

<!-- Encabezado con estadísticas -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('circulistas.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-indigo-600 transition gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Padrón
            </a>
        </div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
            Circulistas Duplicados
            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                Módulo de Limpieza
            </span>
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            <span id="stats-description">
                @if($criterio === 'celular')
                    Identifica y compara registros que comparten el mismo número de celular para resolver duplicaciones.
                @elseif($criterio === 'telefono')
                    Identifica y compara registros que comparten el mismo número de teléfono para resolver duplicaciones.
                @elseif($criterio === 'email')
                    Identifica y compara registros que comparten el mismo correo electrónico para resolver duplicaciones.
                @elseif($criterio === 'fecha_nacimiento')
                    Identifica y compara registros que comparten la misma fecha de nacimiento para resolver duplicaciones.
                @else
                    Identifica y compara registros que comparten el mismo Apellido y Nombre para resolver duplicaciones.
                @endif
            </span>
        </p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <!-- Indicador de Grupos Duplicados -->
        <span class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-700 ring-1 ring-amber-600/10 shadow-sm">
            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span id="stats-total-grupos">
                {{ $totalGrupos }} 
                @if($criterio === 'celular')
                    celulares duplicados
                @elseif($criterio === 'telefono')
                    teléfonos duplicados
                @elseif($criterio === 'email')
                    emails duplicados
                @elseif($criterio === 'fecha_nacimiento')
                    fechas duplicadas
                @else
                    personas duplicadas
                @endif
            </span>
        </span>
        
        <!-- Indicador de Total de Registros Afectados -->
        <span class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/10 shadow-sm">
            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span id="stats-total-registros">{{ $totalRegistros }} fichas afectadas</span>
        </span>
    </div>
</div>

<!-- Contenedor Principal de Filtros y Grupos -->
<div class="space-y-6">
    <!-- Card Filtros y Búsqueda -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
        <form action="{{ route('circulistas.duplicados') }}" method="GET" onsubmit="event.preventDefault();" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       id="live-search-input"
                       value="{{ request('search') }}"
                       oninput="handleLiveSearch()"
                       placeholder="Filtrar duplicados por nombre o apellido..." 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-sm">
            </div>
            
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none cursor-pointer">
                    Buscar
                </button>
                <a href="{{ route('circulistas.duplicados') }}" 
                   id="clear-search-btn"
                   style="{{ request('search') ? 'display: inline-flex;' : 'display: none;' }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Contenedor dinámico de resultados -->
    <div id="live-search-container" class="transition-opacity duration-200 space-y-6">
        
        <!-- Selector de Criterio de Duplicación (Premium Tailwind Tab System) -->
        <div class="border border-slate-200/80 bg-slate-50/50 rounded-2xl p-1.5 shadow-sm flex flex-wrap gap-1">
            <button type="button" onclick="changeCriterio('nombre_apellido')" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer {{ $criterio === 'nombre_apellido' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Nombre y Apellido
            </button>
            
            <button type="button" onclick="changeCriterio('celular')" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer {{ $criterio === 'celular' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Celular
            </button>

            <button type="button" onclick="changeCriterio('telefono')" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer {{ $criterio === 'telefono' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Teléfono
            </button>

            <button type="button" onclick="changeCriterio('email')" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer {{ $criterio === 'email' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email
            </button>

            <button type="button" onclick="changeCriterio('fecha_nacimiento')" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer {{ $criterio === 'fecha_nacimiento' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Fecha Nacimiento
            </button>
        </div>

        @if($totalGrupos > 0)
            @foreach($gruposPaginados as $grupo)
                @php
                    $registros = $grupo->registros;
                    $primerRegistro = $registros->first();
                @endphp

                <div class="rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden">
                    <!-- Cabecera del Grupo Repetido -->
                    <div class="bg-gradient-to-r from-amber-50/70 via-slate-50 to-white border-b border-slate-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-white font-bold shadow-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                    {{ $grupo->grupo_label }}
                                </h3>
                                <div class="text-xs text-slate-500">
                                    {{ $grupo->total_repetidos }} {{ $grupo->grupo_detail }}
                                </div>
                            </div>
                        </div>

                        <span class="self-start sm:self-center inline-flex items-center rounded-lg bg-amber-100/80 px-3 py-1 text-xs font-semibold text-amber-800">
                            {{ $grupo->total_repetidos }} coincidencias
                        </span>
                    </div>

                    <!-- Tabla de Fichas Duplicadas en el grupo -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">ID / Circulista</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Nacimiento</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Ubicación</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Participaciones</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                    <th scope="col" class="py-3 pl-3 pr-6 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($registros as $circulista)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- ID y Nombre -->
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-7 px-2.5 items-center justify-center rounded-md bg-slate-100 font-mono text-xs font-bold text-slate-600 border border-slate-200">
                                                    #{{ $circulista->id }}
                                                </span>
                                                <div>
                                                    <div class="font-semibold text-slate-900 text-sm">
                                                        {{ $circulista->apellido }}, {{ $circulista->nombre }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400">
                                                        Registrado: {{ $circulista->created_at ? $circulista->created_at->format('d/m/Y H:i') : 'N/D' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Fecha Nacimiento -->
                                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                                            @if($circulista->fecha_nacimiento)
                                                <div class="font-medium text-slate-700">
                                                    @if($circulista->sin_anio_nacimiento)
                                                        {{ $circulista->fecha_nacimiento->format('d/m') }} <span class="text-slate-400">(Sin año)</span>
                                                    @else
                                                        {{ $circulista->fecha_nacimiento->format('d/m/Y') }}
                                                        <span class="text-slate-400">({{ $circulista->fecha_nacimiento->age }} a.)</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">No registrada</span>
                                            @endif
                                        </td>

                                        <!-- Contacto -->
                                        <td class="whitespace-nowrap px-3 py-4 text-xs">
                                            @if($circulista->celular)
                                                <div class="font-medium text-slate-800 flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $circulista->celular }}
                                                </div>
                                            @elseif($circulista->telefono)
                                                <div class="text-slate-700">{{ $circulista->telefono }}</div>
                                            @else
                                                <span class="text-slate-400 italic">Sin teléfono</span>
                                            @endif
                                            <div class="text-slate-500 text-[11px] truncate max-w-[160px]">{{ $circulista->email ?: 'Sin email' }}</div>
                                        </td>

                                        <!-- Ubicación -->
                                        <td class="whitespace-nowrap px-3 py-4 text-xs text-slate-700">
                                            @if($circulista->localidad || $circulista->provincia)
                                                <div class="font-medium">{{ $circulista->localidad }}</div>
                                                <div class="text-slate-400 text-[11px]">{{ $circulista->provincia }}</div>
                                            @else
                                                <span class="text-slate-400 italic">Sin ubicación</span>
                                            @endif
                                        </td>

                                        <!-- Participaciones -->
                                        <td class="whitespace-nowrap px-3 py-4 text-center">
                                            @php $numPart = $circulista->participaciones->count(); @endphp
                                            <span class="inline-flex items-center rounded-lg {{ $numPart > 0 ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20' : 'bg-slate-50 text-slate-500 ring-slate-500/10' }} px-2.5 py-1 text-xs font-semibold ring-1 ring-inset">
                                                {{ $numPart }} {{ $numPart === 1 ? 'evento' : 'eventos' }}
                                            </span>
                                        </td>

                                        <!-- Estado -->
                                        <td class="whitespace-nowrap px-3 py-4 text-center">
                                            @if($circulista->activo)
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Ver Ficha -->
                                                <a href="{{ route('circulistas.show', $circulista->id) }}" 
                                                    target="_blank"
                                                    title="Ver Ficha Completa (Abre en nueva pestaña)"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600 focus:outline-none">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                <!-- Editar -->
                                                <a href="{{ route('circulistas.edit', $circulista->id) }}"
                                                    target="_blank"
                                                    title="Editar Ficha (Abre en nueva pestaña)"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600 focus:outline-none">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <!-- Eliminar -->
                                                <form action="{{ route('circulistas.destroy', $circulista->id) }}" 
                                                        method="POST" 
                                                        class="inline" 
                                                        onsubmit="return confirm('¿Estás seguro de eliminar el registro #{{ $circulista->id }} de {{ $circulista->nombre }} {{ $circulista->apellido }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            title="Eliminar este duplicado"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 text-red-500 bg-white hover:bg-red-50 transition focus:outline-none cursor-pointer">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                </div>
            @endforeach

            <!-- Paginación de Grupos -->
            <div id="pagination-container" class="mt-6">
                @if($gruposPaginados->hasPages())
                    <div class="rounded-2xl border border-slate-200/80 bg-white px-6 py-4 shadow-sm">
                        {{ $gruposPaginados->links() }}
                    </div>
                @endif
            </div>

        @else
            <!-- Estado sin duplicados encontrados -->
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-12 text-center shadow-sm">
                <div class="flex flex-col items-center justify-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-200">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">¡Todo en orden!</h3>
                    <p class="text-sm text-slate-600 max-w-md">
                        @if(request('search'))
                            No se encontraron registros duplicados que coincidan con la búsqueda "<strong>{{ request('search') }}</strong>" con el criterio seleccionado.
                        @else
                            No se detectaron circulistas repetidos bajo el criterio seleccionado en el padrón. La base de datos se encuentra limpia.
                        @endif
                    </p>
                    @if(request('search') || request('criterio'))
                        <a href="{{ route('circulistas.duplicados') }}" class="mt-2 inline-flex items-center justify-center rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                            Restaurar filtros y ver todos
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    let debounceTimer;

    function changeCriterio(criterio) {
        const url = new URL(window.location.href);
        url.searchParams.set('criterio', criterio);
        url.searchParams.delete('page'); // Reiniciar a página 1 al cambiar de criterio
        loadResults(url.toString());
    }

    function handleLiveSearch() {
        clearTimeout(debounceTimer);
        const query = document.getElementById('live-search-input').value;
        
        debounceTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            
            if (!query) {
                url.searchParams.delete('search');
            }
            url.searchParams.delete('page'); // Reiniciar a página 1 al buscar
            
            loadResults(url.toString());
        }, 250);
    }

    function loadResults(url) {
        const container = document.getElementById('live-search-container');
        if (!container) {
            window.location.href = url;
            return;
        }

        container.style.opacity = '0.5';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newContainer = doc.getElementById('live-search-container');
            if (newContainer && container) {
                container.innerHTML = newContainer.innerHTML;
                container.style.opacity = '1';
            }
            
            // Actualizar estadísticas y descripción en el encabezado
            ['stats-total-grupos', 'stats-total-registros', 'stats-description'].forEach(id => {
                const newElem = doc.getElementById(id);
                const currentElem = document.getElementById(id);
                if (newElem && currentElem) {
                    currentElem.innerHTML = newElem.innerHTML;
                }
            });
            
            // Actualizar URL del navegador
            history.pushState(null, '', url);
            
            // Quitar el foco del botón/enlace
            if (document.activeElement) {
                document.activeElement.blur();
            }

            // Mostrar/ocultar el botón limpiar
            const hasSearch = new URL(url).searchParams.has('search');
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) {
                clearBtn.style.display = hasSearch ? 'inline-flex' : 'none';
            }
        })
        .catch(error => {
            console.error('Error al realizar búsqueda en vivo:', error);
            if (container) {
                container.style.opacity = '1';
            }
            window.location.href = url;
        });
    }

    // Interceptar clics en los enlaces de paginación
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-container a');
        if (link && link.href) {
            e.preventDefault();
            link.blur();
            loadResults(link.href);
        }
    });

</script>

@endsection
