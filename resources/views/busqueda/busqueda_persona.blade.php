@extends('layouts.app')

@section('content')

<style>
    @page {
        size: auto;
        margin: 0mm;
    }

    @media print {
        header, footer, nav, .no-print, button, form, .modal, .modal-backdrop, #pagination-container, .border-t {
            display: none !important;
        }
        
        body, html, main, .max-w-7xl, #yield-content {
            background: #fff !important;
            color: #000 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            padding: 15mm !important;
        }

        .shadow-sm, .shadow-md, .shadow-lg, .ring-1, .ring-4 {
            box-shadow: none !important;
            border-color: #cbd5e1 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 8px 12px !important;
        }
    }
</style>

<!-- Cabecera de impresión exclusiva -->
@if($circulistaSeleccionado)
<div class="hidden print:block mb-8 border-b-2 border-slate-900 pb-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Historial de Participaciones (Últimos 2 Años)</h1>
            <p class="text-sm font-semibold text-slate-700 mt-1">Circulista: {{ $circulistaSeleccionado->apellido }}, {{ $circulistaSeleccionado->nombre }}</p>
            <p class="text-xs text-slate-500">Periodo evaluado: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaConsulta)->format('d/m/Y') }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-indigo-800">Padrón MCJ</h2>
            <p class="text-xs text-slate-400">Fecha de consulta: {{ \Carbon\Carbon::parse($fechaConsulta)->format('d/m/Y') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Encabezado de Página -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Historial por Persona (Últimos 2 Años)</h1>
        <p class="mt-1.5 text-sm text-slate-500">Consulta los retiros y eventos en los que ha participado una persona dentro de una ventana de 2 años hacia atrás.</p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="{{ route('busqueda.avanzada') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="-ml-0.5 mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Ir a Búsqueda Avanzada
        </a>
    </div>
</div>

<!-- Tarjeta del Formulario de Búsqueda -->
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-8 no-print">
    <form action="{{ route('busqueda.persona') }}" method="GET" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Buscador Predictivo de Circulista -->
            <div class="md:col-span-2 relative">
                <label for="persona_search_input" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Escribir Nombre o Apellido de la Persona *</label>
                
                <div class="relative">
                    <input type="text" 
                           id="persona_search_input" 
                           placeholder="Escribe para buscar... (ej. Fernández, Laura)" 
                           value="{{ $circulistaSeleccionado ? $circulistaSeleccionado->apellido . ', ' . $circulistaSeleccionado->nombre . ($circulistaSeleccionado->localidad ? ' ('.$circulistaSeleccionado->localidad.')' : '') : '' }}"
                           autocomplete="off"
                           class="w-full rounded-xl border border-slate-200 pl-10 pr-10 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none bg-white text-slate-800 font-medium">
                    
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <button type="button" id="clear_persona_btn" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 {{ $circulistaSeleccionado ? '' : 'hidden' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <input type="hidden" name="circulista_id" id="circulista_id_hidden" value="{{ request('circulista_id') }}" required>

                <!-- Menú Desplegable Predictivo -->
                <div id="autocomplete_dropdown" class="absolute z-50 left-0 right-0 mt-1.5 max-h-60 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl hidden">
                    <ul id="autocomplete_list" class="divide-y divide-slate-100 text-sm text-slate-700">
                    </ul>
                </div>
            </div>

            <!-- Selector de Fecha de Consulta -->
            <div>
                <label for="fecha_consulta" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fecha de Consulta</label>
                <input type="date" 
                       name="fecha_consulta" 
                       id="fecha_consulta" 
                       value="{{ request('fecha_consulta', date('Y-m-d')) }}"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-slate-800 font-medium">
                <span class="text-[11px] text-slate-400 mt-1 block">Filtrará 2 años hacia atrás desde esta fecha.</span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            @if(request('circulista_id'))
                <a href="{{ route('busqueda.persona') }}" 
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    Limpiar Búsqueda
                </a>
            @endif
            <button type="submit" 
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none cursor-pointer">
                <svg class="-ml-0.5 mr-1.5 h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar Historial de Retiros
            </button>
        </div>
    </form>
</div>

<!-- Ficha de la Persona Seleccionada y Resultados -->
@if($circulistaSeleccionado)

    <!-- Datos de la Persona -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 font-bold text-xl border border-indigo-100">
                {{ strtoupper(substr($circulistaSeleccionado->nombre, 0, 1) . substr($circulistaSeleccionado->apellido, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $circulistaSeleccionado->apellido }}, {{ $circulistaSeleccionado->nombre }}</h2>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1">
                    @if($circulistaSeleccionado->celular)
                        <span>📱 {{ $circulistaSeleccionado->celular }}</span>
                    @endif
                    @if($circulistaSeleccionado->email)
                        <span>✉️ {{ $circulistaSeleccionado->email }}</span>
                    @endif
                    @if($circulistaSeleccionado->localidad)
                        <span>📍 {{ $circulistaSeleccionado->localidad }} {{ $circulistaSeleccionado->provincia ? ', '.$circulistaSeleccionado->provincia : '' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 w-full sm:w-auto">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-800 ring-1 ring-inset ring-indigo-700/10">
                <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Periodo: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaConsulta)->format('d/m/Y') }}
            </span>
            <div class="text-xs text-slate-500 font-medium mt-1.5">
                Total participaciones encontradas: <strong class="text-slate-900 font-bold">{{ $participaciones->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div id="resultados-card" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm mb-8">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-sm font-bold text-slate-900">Retiros y Eventos Realizados (Últimos 2 Años)</h3>
            
            @if(!$participaciones->isEmpty())
            <!-- Botones de Exportación e Impresión -->
            <div class="flex items-center gap-2 no-print">
                <span class="text-xs text-slate-400 font-medium">Exportar:</span>
                <button onclick="exportToCSV()" 
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 cursor-pointer">
                    CSV
                </button>
                <button onclick="exportToExcel()" 
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 cursor-pointer">
                    Excel
                </button>
                <button onclick="window.print()" 
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100 cursor-pointer gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir / PDF
                </button>
            </div>
            @endif
        </div>

        @if($participaciones->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 px-4 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-200/50 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Sin participaciones en el periodo</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-md">
                    {{ $circulistaSeleccionado->nombre }} no registra participaciones en eventos entre el <strong>{{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}</strong> y el <strong>{{ \Carbon\Carbon::parse($fechaConsulta)->format('d/m/Y') }}</strong>.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50/50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3">Número de Evento</th>
                            <th scope="col" class="px-4 py-4">Fecha del Evento</th>
                            <th scope="col" class="px-4 py-4">Rol Desempeñado</th>
                            <th scope="col" class="px-4 py-4">Lugar del Evento</th>
                            <th scope="col" class="px-4 py-4">Grupo / Patrulla</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm font-medium text-slate-700">
                        @foreach($participaciones as $participacion)
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Número y Tipo de Evento -->
                                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                    <a href="{{ route('eventos.show', $participacion->evento_id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition flex items-center gap-2">
                                        <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $participacion->evento->tipoEvento->nombre ?? 'Retiro' }} Nº {{ $participacion->evento->numero_evento }}</span>
                                    </a>
                                </td>

                                <!-- Fecha del Evento -->
                                <td class="whitespace-nowrap px-4 py-4 text-slate-800">
                                    <div class="font-bold">
                                        {{ $participacion->evento->fecha_inicio ? $participacion->evento->fecha_inicio->format('d/m/Y') : 'Sin fecha' }}
                                        @if($participacion->evento->fecha_fin && $participacion->evento->fecha_fin->ne($participacion->evento->fecha_inicio))
                                            <span class="font-normal text-slate-500">al {{ $participacion->evento->fecha_fin->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-normal">
                                        ({{ $participacion->evento->fecha_inicio ? $participacion->evento->fecha_inicio->diffForHumans() : '' }})
                                    </span>
                                </td>

                                <!-- Rol Desempeñado -->
                                <td class="whitespace-nowrap px-4 py-4">
                                    @php
                                        $rolNombre = $participacion->rol->nombre ?? 'Sin Rol';
                                        $badgeClass = 'bg-slate-50 text-slate-700 ring-slate-600/10';
                                        if ($rolNombre === 'Rector' || $rolNombre === 'Vice Rector') {
                                            $badgeClass = 'bg-indigo-50 text-indigo-800 ring-indigo-700/10 font-bold';
                                        } elseif ($rolNombre === 'Peregrino' || $rolNombre === 'Participante Enganche' || $rolNombre === 'Participante Retiro Mariano' || $rolNombre === 'Circulista') {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 font-bold';
                                        } elseif (str_contains(strtolower($rolNombre), 'cocina') || str_contains(strtolower($rolNombre), 'cocinero')) {
                                            $badgeClass = 'bg-amber-50 text-amber-800 ring-amber-600/10 font-semibold';
                                        } elseif ($rolNombre === 'Servidor' || $rolNombre === 'Mensajero' || $rolNombre === 'Ganchista' || $rolNombre === 'Asistente') {
                                            $badgeClass = 'bg-sky-50 text-sky-800 ring-sky-600/10 font-semibold';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                        {{ $rolNombre }}
                                    </span>
                                </td>

                                <!-- Lugar -->
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600 font-normal">
                                    {{ $participacion->evento->lugar ?: '—' }}
                                </td>

                                <!-- Grupo -->
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600 font-normal">
                                    {{ $participacion->grupo ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@else
    <!-- Estado Vacío cuando aún no se ha buscado ninguna persona -->
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 mb-4">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900">Selecciona una persona para consultar su historial</h3>
        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
            Utiliza el buscador superior para seleccionar un circulista y visualizar los retiros en los que ha participado en los últimos 2 años.
        </p>
    </div>
@endif

<script>
function getTableData() {
    const table = document.querySelector('#resultados-card table');
    if (!table) return null;

    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());

    const data = rows.map(row => {
        const cols = Array.from(row.querySelectorAll('td'));
        return cols.map(col => col.textContent.trim().replace(/\s+/g, ' '));
    });

    return { headers, data };
}

function exportToCSV() {
    const tableData = getTableData();
    if (!tableData) return;

    let csvContent = "";
    csvContent += tableData.headers.join(";") + "\n";
    tableData.data.forEach(row => {
        const cleanRow = row.map(val => {
            let cleanVal = val.replace(/"/g, '""');
            return `"${cleanVal}"`;
        });
        csvContent += cleanRow.join(";") + "\n";
    });

    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", `historial_retiros_2_anios.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel() {
    const tableData = getTableData();
    if (!tableData) return;

    let excelContent = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; }
            th { background-color: #f1f5f9; font-weight: bold; border: 1px solid #cbd5e1; padding: 8px; }
            td { border: 1px solid #cbd5e1; padding: 8px; }
        </style>
    </head>
    <body>
        <h2>Historial de Retiros en los Últimos 2 Años</h2>
        <table>
            <thead>
                <tr>
                    ${tableData.headers.map(h => `<th>${h}</th>`).join('')}
                </tr>
            </thead>
            <tbody>
                ${tableData.data.map(row => `
                    <tr>
                        ${row.map(val => `<td>${val}</td>`).join('')}
                    </tr>
                `).join('')}
            </tbody>
        </table>
    </body>
    </html>`;

    const blob = new Blob(["\uFEFF" + excelContent], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", `historial_retiros_2_anios.xls`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Lógica de Autocompletado Predictivo para buscar persona
document.addEventListener('DOMContentLoaded', function() {
    const circulistas = {!! json_encode($circulistasData) !!};

    const searchInput = document.getElementById('persona_search_input');
    const hiddenInput = document.getElementById('circulista_id_hidden');
    const dropdown = document.getElementById('autocomplete_dropdown');
    const list = document.getElementById('autocomplete_list');
    const clearBtn = document.getElementById('clear_persona_btn');

    if (!searchInput || !hiddenInput || !dropdown || !list) return;

    function normalize(str) {
        return (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }

    function renderMatches(query) {
        const normQuery = normalize(query.trim());
        list.innerHTML = '';

        if (!normQuery) {
            dropdown.classList.add('hidden');
            return;
        }

        const terms = normQuery.split(/\s+/);
        const matches = circulistas.filter(c => {
            const fullText = normalize(`${c.apellido} ${c.nombre} ${c.localidad || ''} ${c.celular || ''}`);
            return terms.every(term => fullText.includes(term));
        }).slice(0, 15);

        if (matches.length === 0) {
            list.innerHTML = `
                <li class="px-4 py-3 text-xs text-slate-400 italic text-center">
                    No se encontraron personas que coincidan con "${query}"
                </li>`;
        } else {
            matches.forEach(item => {
                const li = document.createElement('li');
                li.className = 'px-4 py-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between transition-colors';
                li.innerHTML = `
                    <div>
                        <span class="font-bold text-slate-800">${item.apellido}, ${item.nombre}</span>
                        ${item.localidad ? `<span class="text-xs text-slate-500 ml-1">(${item.localidad})</span>` : ''}
                    </div>
                    ${item.celular ? `<span class="text-xs font-mono text-slate-400">📱 ${item.celular}</span>` : ''}
                `;
                li.addEventListener('click', function() {
                    selectCirculista(item);
                });
                list.appendChild(li);
            });
        }

        dropdown.classList.remove('hidden');
    }

    function selectCirculista(item) {
        searchInput.value = item.label;
        hiddenInput.value = item.id;
        dropdown.classList.add('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');
    }

    searchInput.addEventListener('input', function() {
        hiddenInput.value = '';
        if (clearBtn) {
            if (this.value) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }
        renderMatches(this.value);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value && !hiddenInput.value) {
            renderMatches(this.value);
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            hiddenInput.value = '';
            dropdown.classList.add('hidden');
            clearBtn.classList.add('hidden');
            searchInput.focus();
        });
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>

@endsection
