@extends('layouts.app')

@section('content')

<!-- Botón de Volver -->
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('circulistas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al listado
    </a>

    <div class="flex items-center gap-2">
        <a href="{{ route('circulistas.edit', $circulista->id) }}" 
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4.5 w-4.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar Ficha
        </a>

        <form action="{{ route('circulistas.destroy', $circulista->id) }}" 
              method="POST" 
              class="inline" 
              onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este circulista?');">
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
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Perfil Principal / Avatar Card -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm flex flex-col items-center text-center">
            <!-- Iniciales Grandes -->
            <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-indigo-50 text-indigo-700 text-3xl font-bold ring-4 ring-indigo-50/50">
                {{ strtoupper(substr($circulista->nombre, 0, 1) . substr($circulista->apellido, 0, 1)) }}
            </div>
            
            <h2 class="mt-5 text-xl font-bold text-slate-900 leading-tight">
                {{ $circulista->nombre }} {{ $circulista->apellido }}
            </h2>
            
            <p class="text-sm text-slate-400 mt-1">Circulista registrado</p>
            
            <div class="mt-4">
                @if($circulista->activo)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20">
                        Estado: Activo
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/10">
                        Estado: Inactivo
                    </span>
                @endif
            </div>

            <div class="mt-6 w-full border-t border-slate-100 pt-6 text-left space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Creado el</span>
                    <span class="text-sm font-medium text-slate-700">{{ $circulista->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Última actualización</span>
                    <span class="text-sm font-medium text-slate-700">{{ $circulista->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles Completos -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Bloque Datos Generales y Ubicación -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Información General</h3>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nombre Completo</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $circulista->apellido }}, {{ $circulista->nombre }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha de Nacimiento</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        @if($circulista->fecha_nacimiento)
                            @if($circulista->sin_anio_nacimiento)
                                {{ $circulista->fecha_nacimiento->format('d/m') }} <span class="text-xs text-slate-500 font-normal">(Cumpleaños)</span>
                            @else
                                {{ $circulista->fecha_nacimiento->format('d/m/Y') }} 
                                <span class="text-xs text-slate-500 font-normal">({{ $circulista->fecha_nacimiento->age }} años)</span>
                            @endif
                        @else
                            <span class="text-slate-400 font-normal italic">No registrada</span>
                        @endif                    </dd>
                </div>

                <div class="sm:col-span-2 border-t border-slate-50 pt-4">
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Domicilio</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        {{ $circulista->domicilio ?: 'No especificado' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Localidad</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        {{ $circulista->localidad ?: 'No especificada' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Provincia</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        {{ $circulista->provincia ?: 'No especificada' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Bloque Contacto -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Información de Contacto</h3>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Celular</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800 flex items-center gap-2">
                        @if($circulista->celular)
                            <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <a href="tel:{{ $circulista->celular }}" class="hover:text-indigo-600 transition">{{ $circulista->celular }}</a>
                        @else
                            <span class="text-slate-400 font-normal italic">No especificado</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Teléfono Fijo</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800 flex items-center gap-2">
                        @if($circulista->telefono)
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $circulista->telefono }}" class="hover:text-indigo-600 transition">{{ $circulista->telefono }}</a>
                        @else
                            <span class="text-slate-400 font-normal italic">No especificado</span>
                        @endif
                    </dd>
                </div>

                <div class="sm:col-span-2 border-t border-slate-50 pt-4">
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Correo Electrónico</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800 flex items-center gap-2">
                        @if($circulista->email)
                            <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $circulista->email }}" class="hover:text-indigo-600 transition">{{ $circulista->email }}</a>
                        @else
                            <span class="text-slate-400 font-normal italic">No especificado</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Historial de Participaciones -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Historial de Participaciones</h3>
            @if($circulista->participaciones->isEmpty())
                <p class="text-sm italic text-slate-400">Este circulista aún no ha participado en ningún evento.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Evento</th>
                                <th class="py-2.5 px-3">Rol</th>
                                <th class="py-2.5 px-3">Grupo</th>
                                <th class="py-2.5 px-3">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 font-medium text-slate-700">
                            @foreach($circulista->participaciones as $participacion)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-3">
                                        <a href="{{ route('eventos.show', $participacion->evento_id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition">
                                            {{ $participacion->evento->tipoEvento->nombre }} #{{ $participacion->evento->numero_evento }}
                                        </a>
                                        <span class="block text-xs font-normal text-slate-400">{{ $participacion->evento->lugar }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        @php
                                            $rolNombre = $participacion->rol->nombre ?? 'Sin Rol';
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
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                            {{ $rolNombre }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 font-normal">
                                        {{ $participacion->grupo ?: '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-500 font-normal text-xs">
                                        {{ $participacion->evento->fecha_inicio ? $participacion->evento->fecha_inicio->format('d/m/Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Bloque Observaciones -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Observaciones / Notas</h3>
            @if($circulista->observaciones)
                <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600 leading-relaxed border border-slate-100">
                    {!! nl2br(e($circulista->observaciones)) !!}
                </div>
            @else
                <p class="text-sm italic text-slate-400">Sin observaciones ni notas especiales registradas.</p>
            @endif
        </div>

    </div>

</div>

@endsection
