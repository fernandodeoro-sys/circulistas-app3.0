@extends('layouts.app')

@section('content')

<!-- Encabezado con estadísticas rápidas -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Circulistas</h1>
        <p class="mt-1.5 text-sm text-slate-500">Administra el padrón de miembros activos e inactivos del movimiento.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <!-- Contador de Registros -->
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-600/10">
            Total: {{ $circulistas->total() }} registros
        </span>
        
        <!-- Botón Nuevo -->
        <a href="{{ route('circulistas.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="h-4.5 w-4.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Circulista
        </a>
    </div>
</div>

<!-- Listado en Tarjeta Premium -->
<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
    <!-- Filtros y Búsqueda -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 p-4">
        <form action="{{ route('circulistas.index') }}" method="GET" onsubmit="event.preventDefault();" class="flex flex-col sm:flex-row gap-3">
            <!-- Input de Búsqueda -->
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
                       placeholder="Buscar circulista por nombre, apellido, correo, localidad..." 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-sm">
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none cursor-pointer">
                    Buscar
                </button>
                <a href="{{ route('circulistas.index') }}" 
                   id="clear-search-btn"
                   style="{{ request('search') ? 'display: inline-flex;' : 'display: none;' }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Contenedor dinámico de búsqueda en tiempo real -->
    <div id="live-search-container" class="transition-opacity duration-200">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/70">
                <tr>
                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Apellido y Nombre</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Contacto</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ubicación</th>
                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Estado</th>
                    <th scope="col" class="py-4 pl-3 pr-6 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($circulistas as $circulista)
                <tr class="hover:bg-slate-50/50 transition">
                    <!-- Apellido y Nombre -->
                    <td class="whitespace-nowrap py-4 pl-6 pr-3">
                        <div class="flex items-center gap-3">
                            <!-- Avatar Iniciales -->
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-600 text-sm tracking-wide">
                                {{ strtoupper(substr($circulista->nombre, 0, 1) . substr($circulista->apellido, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">{{ $circulista->apellido }}, {{ $circulista->nombre }}</div>
                                @if($circulista->fecha_nacimiento)
                                    <div class="text-xs text-slate-400">
                                        @if($circulista->sin_anio_nacimiento)
                                            Nac: {{ $circulista->fecha_nacimiento->format('d/m') }} (Cumpleaños)
                                        @else
                                            Nac: {{ $circulista->fecha_nacimiento->format('d/m/Y') }}
                                            ({{ $circulista->fecha_nacimiento->age }} años)
                                        @endif
                                    </div>
                                @else
                                    <div class="text-xs text-slate-300">Sin fecha nac.</div>
                                @endif                            </div>
                        </div>
                    </td>
                    
                    <!-- Contacto -->
                    <td class="whitespace-nowrap px-3 py-4">
                        <div class="text-sm text-slate-700">
                            @if($circulista->celular)
                                <div class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    {{ $circulista->celular }}
                                </div>
                            @elseif($circulista->telefono)
                                <div class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $circulista->telefono }}
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Sin teléfono</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $circulista->email ?: 'Sin correo' }}</div>
                    </td>
                    
                    <!-- Ubicación -->
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600">
                        @if($circulista->localidad || $circulista->provincia)
                            <div>{{ $circulista->localidad ?: '' }}</div>
                            <div class="text-xs text-slate-400">{{ $circulista->provincia ?: '' }}</div>
                        @else
                            <span class="text-xs text-slate-400">Sin ubicación</span>
                        @endif
                    </td>
                    
                    <!-- Estado Activo -->
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
                            <!-- Botón Ver -->
                            <a href="{{ route('circulistas.show', $circulista->id) }}" 
                               title="Ver Ficha Completa"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600 focus:outline-none">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <!-- Botón Editar -->
                            <a href="{{ route('circulistas.edit', $circulista->id) }}"
                               title="Editar Circulista"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600 focus:outline-none">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <!-- Formulario/Botón Eliminar -->
                            <form action="{{ route('circulistas.destroy', $circulista->id) }}" 
                                  method="POST" 
                                  class="inline" 
                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este circulista?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Eliminar Circulista"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 text-red-500 bg-white hover:bg-red-50 transition focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm font-semibold text-slate-500">No se encontraron circulistas</span>
                            <span class="text-xs text-slate-400">Comienza dando de alta a un circulista en el botón superior.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        </div>
        
        <!-- Paginación -->
        <div id="pagination-container">
            @if($circulistas->hasPages())
                <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                    {{ $circulistas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    let debounceTimer;

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
        if (container) {
            container.style.opacity = '0.5';
        }

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
            
            // Actualizar URL del navegador
            history.pushState(null, '', url);
            
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
        });
    }

    // Interceptar clics en los enlaces de paginación
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-container a');
        if (link) {
            e.preventDefault();
            loadResults(link.href);
        }
    });
</script>

@endsection