@extends('layouts.app')

@section('content')

<!-- Encabezado con estadísticas rápidas -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Eventos</h1>
        <p class="mt-1.5 text-sm text-slate-500">Mapeo y control de todos los retiros y jornadas realizadas por el movimiento.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <!-- Contador de Registros -->
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-600/10">
            Total: {{ $eventos->total() }} eventos
        </span>
        
        @if(in_array(Auth::user()->role, ['administrador', 'supervisor']))
        <!-- Botón Importar Masivo -->
        <a href="{{ route('eventos.import.form') }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
            <svg class="h-4.5 w-4.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Importar Masivo
        </a>
        
        <!-- Botón Nuevo -->
        <a href="{{ route('eventos.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700 hover:shadow-lg focus:outline-none">
            <svg class="h-4.5 w-4.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Evento
        </a>
        @endif
    </div>
</div>

<!-- Listado en Tarjeta Premium -->
<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
    <!-- Filtros y Búsqueda -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 p-4">
        <form action="{{ route('eventos.index') }}" method="GET" onsubmit="event.preventDefault();" class="flex flex-col sm:flex-row gap-3">
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
                       placeholder="Buscar evento por tipo, lugar, número u observaciones..." 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-sm">
            </div>
            
            <!-- Botones de Acción -->
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none cursor-pointer">
                    Buscar
                </button>
                <a href="{{ route('eventos.index') }}" 
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
                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Evento</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Lugar</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Fechas</th>
                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Imágenes</th>
                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Estado</th>
                    <th scope="col" class="py-4 pl-3 pr-6 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($eventos as $evento)
                <tr class="hover:bg-slate-50/50 transition">
                    <!-- Evento (Tipo + Número) -->
                    <td class="whitespace-nowrap py-4 pl-6 pr-3">
                        <div class="flex items-center gap-3">
                            <!-- Icono de Calendario o Evento -->
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-600 text-sm ring-1 ring-indigo-500/10">
                                {{ substr($evento->tipoEvento->nombre ?? 'E', 0, 2) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">
                                    {{ $evento->tipoEvento->nombre ?? 'Sin tipo' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    Número: <span class="font-semibold text-slate-700">#{{ $evento->numero_evento }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Lugar -->
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $evento->lugar }}
                        </div>
                    </td>
                    
                    <!-- Fechas -->
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-700">
                        <div class="font-medium text-slate-800">
                            {{ $evento->fecha_inicio->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-slate-400">
                            al {{ $evento->fecha_fin->format('d/m/Y') }}
                        </div>
                    </td>
                    
                    <!-- Indicador de Imágenes -->
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <!-- Foto Evento -->
                            @if($evento->foto_evento)
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-600 ring-1 ring-inset ring-slate-500/10" title="Foto del Evento disponible">
                                    📸 Retiro
                                </span>
                            @endif
                            <!-- Foto Cocina -->
                            @if($evento->foto_cocina)
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-600 ring-1 ring-inset ring-slate-500/10" title="Foto de Cocina disponible">
                                    🍳 Cocina
                                </span>
                            @endif
                            @if(!$evento->foto_evento && !$evento->foto_cocina)
                                <span class="text-xs text-slate-300 italic">Ninguna</span>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Estado Activo -->
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                        @if($evento->activo)
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
                            <a href="{{ route('eventos.show', $evento->id) }}" 
                               title="Ver Detalle"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            @if(in_array(Auth::user()->role, ['administrador', 'supervisor']))
                            <!-- Botón Editar -->
                            <a href="{{ route('eventos.edit', $evento->id) }}"
                               title="Editar Evento"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition hover:text-indigo-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <!-- Formulario/Botón Eliminar -->
                            <form action="{{ route('eventos.destroy', $evento->id) }}" 
                                  method="POST" 
                                  class="inline" 
                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente este evento y todas sus imágenes asociadas?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Eliminar Evento"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 text-red-500 bg-white hover:bg-red-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-semibold text-slate-500">No se encontraron eventos</span>
                            <span class="text-xs text-slate-400">Comienza registrando un retiro o jornada con el botón superior.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
            <!-- Paginación -->
            <div id="pagination-container">
                @if($eventos->hasPages())
                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                        {{ $eventos->links() }}
                    </div>
                @endif
            </div>
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
