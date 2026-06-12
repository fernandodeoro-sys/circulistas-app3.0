@extends('layouts.app')

@section('content')

<style>
    @page {
        size: auto;
        margin: 0mm;
    }

    @media print {
        /* Ocultar filtros, navegación, botones y paginación */
        header, footer, nav, .no-print, button, form, .modal, .modal-backdrop, #pagination-container, .border-t {
            display: none !important;
        }
        
        /* Ajustar contenedor y fondo */
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
            ring: none !important;
            border-color: #cbd5e1 !important;
        }

        /* Ajustes de tablas */
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
<div class="hidden print:block mb-8 border-b-2 border-slate-900 pb-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Resultados de Búsqueda Avanzada</h1>
            <p class="text-xs text-slate-500">Movimiento de Círculos de Juventud (MCJ)</p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-indigo-600">Padrón Oficial</h2>
            <p class="text-xs text-slate-400">Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

<!-- Encabezado -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Búsqueda Avanzada</h1>
    <p class="mt-1.5 text-sm text-slate-500">Cruza información de eventos y roles para encontrar circulistas rápidamente.</p>
</div>

<!-- Tarjeta de Filtros -->
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
    <form action="{{ route('busqueda.avanzada') }}" method="GET" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Selector de Tipo de Evento -->
            <div>
                <label for="tipo_evento_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filtrar por Tipo de Evento</label>
                <select name="tipo_evento_id" 
                        id="tipo_evento_id" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-slate-700">
                    <option value="">— Todos los Tipos —</option>
                    @foreach($tiposEvento as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_evento_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selector de Rol -->
            <div>
                <label for="rol_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filtrar por Rol</label>
                <select name="rol_id" 
                        id="rol_id" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white text-slate-700">
                    <option value="">— Todos los Roles —</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ request('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-50">
            @if(request('tipo_evento_id') || request('rol_id'))
                <a href="{{ route('busqueda.avanzada') }}" 
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    Limpiar Filtros
                </a>
            @endif
            <button type="submit" 
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none">
                <svg class="-ml-0.5 mr-1.5 h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtrar Resultados
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Resultados -->
<div id="resultados-card" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm mb-8">
    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h3 class="text-sm font-bold text-slate-900">Resultados de la Búsqueda</h3>
        
        @if(!$resultados->isEmpty())
        <!-- Botones de Exportación -->
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
            <button onclick="exportToPDF()" 
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100 cursor-pointer">
                PDF
            </button>
        </div>
        @endif
    </div>

    @if($resultados->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 ring-1 ring-slate-200/50 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-800">No se encontraron registros</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm">
                No hay circulistas que coincidan con los filtros seleccionados en este momento. Intenta cambiar los criterios de búsqueda.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50/50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3">Circulista</th>
                        <th scope="col" class="px-3 py-4">Evento</th>
                        <th scope="col" class="px-3 py-4">Rol desempeñado</th>
                        <th scope="col" class="px-3 py-4">Grupo</th>
                        <th scope="col" class="px-3 py-4">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm font-medium text-slate-700">
                    @foreach($resultados as $participacion)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Circulista -->
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-600 text-sm">
                                        {{ strtoupper(substr($participacion->circulista->nombre, 0, 1) . substr($participacion->circulista->apellido, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('circulistas.show', $participacion->circulista_id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                            {{ $participacion->circulista->apellido }}, {{ $participacion->circulista->nombre }}
                                        </a>
                                        <div class="text-xs text-slate-400 font-normal">
                                            {{ $participacion->circulista->celular ?: $participacion->circulista->email ?: 'Sin datos de contacto' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Evento -->
                            <td class="whitespace-nowrap px-3 py-4">
                                <a href="{{ route('eventos.show', $participacion->evento_id) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition block">
                                    {{ $participacion->evento->tipoEvento->nombre ?? 'Retiro' }} #{{ $participacion->evento->numero_evento }}
                                </a>
                                <span class="text-xs font-normal text-slate-400 block">{{ $participacion->evento->lugar }}</span>
                            </td>

                            <!-- Rol -->
                            <td class="whitespace-nowrap px-3 py-4">
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
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                    {{ $rolNombre }}
                                </span>
                            </td>

                            <!-- Grupo -->
                            <td class="whitespace-nowrap px-3 py-4 text-slate-600">
                                {{ $participacion->grupo ?: '—' }}
                            </td>

                            <!-- Observaciones -->
                            <td class="px-3 py-4 text-slate-500 font-normal max-w-xs truncate" title="{{ $participacion->observaciones }}">
                                {{ $participacion->observaciones ?: '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($resultados->hasPages())
            <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                {{ $resultados->links() }}
            </div>
        @endif
    @endif
</div>

<script>
function getTableData() {
    const table = document.querySelector('#resultados-card table');
    if (!table) return null;

    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());

    const data = rows.map(row => {
        const cols = Array.from(row.querySelectorAll('td'));
        
        // Columna 1: Circulista
        const nameLink = cols[0].querySelector('a');
        const name = nameLink ? nameLink.textContent.trim() : cols[0].textContent.trim();
        const contactDiv = cols[0].querySelector('div.text-xs');
        const contact = contactDiv ? contactDiv.textContent.trim() : '';
        const fullCirculista = contact ? `${name} (${contact})` : name;

        // Columna 2: Evento
        const eventLink = cols[1].querySelector('a');
        const event = eventLink ? eventLink.textContent.trim() : cols[1].textContent.trim();
        const placeSpan = cols[1].querySelector('span');
        const place = placeSpan ? placeSpan.textContent.trim() : '';
        const fullEvent = place ? `${event} (${place})` : event;

        // Columna 3: Rol
        const rolSpan = cols[2].querySelector('span');
        const rol = rolSpan ? rolSpan.textContent.trim() : cols[2].textContent.trim();

        // Columna 4: Grupo
        const grupo = cols[3].textContent.trim();

        // Columna 5: Observaciones
        const obs = cols[4].textContent.trim();

        return [fullCirculista, fullEvent, rol, grupo, obs];
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
    link.setAttribute("download", `resultados_busqueda_avanzada.csv`);
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
        <h2>Resultados de Búsqueda Avanzada - Padrón MCJ</h2>
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
    link.setAttribute("download", `resultados_busqueda_avanzada.xls`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    window.print();
}
</script>

</div>

@endsection
