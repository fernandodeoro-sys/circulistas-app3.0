<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Búsqueda Avanzada - Padrón MCJ</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            background-color: #fff;
        }

        /* Barra de acciones en pantalla */
        .actions-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        /* Cabecera del Documento */
        .header-doc {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-left img, .header-left svg {
            width: 50px;
            height: 50px;
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .header-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }

        .header-right strong {
            display: block;
            font-size: 13px;
            color: #4f46e5;
        }

        /* Resumen de filtros */
        .filters-summary {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 11px;
        }

        .filter-item strong {
            color: #334155;
        }

        /* Tabla de Resultados */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 10px;
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            text-align: left;
        }

        td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            vertical-align: middle;
            color: #1e293b;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .circulista-nombre {
            font-weight: 700;
            color: #0f172a;
        }

        .circulista-contacto {
            font-size: 10px;
            color: #64748b;
        }

        .badge-rol {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 600;
            border-radius: 4px;
            background-color: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .fecha-rango {
            font-size: 10px;
            color: #64748b;
        }

        .footer-print {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0 !important;
                background: #fff !important;
            }
            .actions-bar {
                display: none !important;
            }
            th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tbody tr:nth-child(even) {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-rol {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de acciones en pantalla -->
    <div class="actions-bar no-print">
        <div>
            <strong style="font-size: 13px; color: #0f172a;">Vista de Impresión / Guardar PDF</strong>
            <span style="font-size: 12px; color: #64748b; margin-left: 8px;">(Se exportan los {{ $participaciones->count() }} resultados obtenidos)</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.close()" class="btn btn-secondary">
                ✕ Cerrar ventana
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimir / Guardar como PDF
            </button>
        </div>
    </div>

    <!-- Encabezado del reporte oficial -->
    <div class="header-doc">
        <div class="header-left">
            @if(file_exists(public_path('images/logo-mcj.png')))
                <img src="{{ asset('images/logo-mcj.png') }}" alt="Logo MCJ">
            @elseif(file_exists(public_path('images/logo-mcj.jpg')))
                <img src="{{ asset('images/logo-mcj.jpg') }}" alt="Logo MCJ">
            @else
                <svg viewBox="0 0 100 100" style="color: #4f46e5;" fill="none" stroke="currentColor">
                    <circle cx="50" cy="50" r="45" stroke-width="3"/>
                    <path d="M 50 16 L 50 82 M 30 36 L 70 36" stroke-width="4"/>
                </svg>
            @endif
            <div>
                <h1 class="header-title">Movimiento de Círculos de Juventud</h1>
                <div class="header-sub">Reporte Oficial de Búsqueda Avanzada - San Juan</div>
            </div>
        </div>
        <div class="header-right">
            <strong>Padrón MCJ</strong>
            Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Resumen de criterios filtrados -->
    <div class="filters-summary">
        <div class="filter-item">
            <strong>Tipo de Evento:</strong> {{ $tipoEvento ? $tipoEvento->nombre : 'Todos los Tipos' }}
        </div>
        <div class="filter-item">
            <strong>Rol:</strong> {{ $rol ? $rol->nombre : 'Todos los Roles' }}
        </div>
        <div class="filter-item">
            <strong>Total de registros obtenidos:</strong> {{ $participaciones->count() }}
        </div>
    </div>

    <!-- Tabla con todos los registros -->
    @if($participaciones->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p style="font-size: 14px; font-weight: bold;">No se encontraron registros con los filtros seleccionados.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">#</th>
                    <th style="width: 26%;">Circulista</th>
                    <th style="width: 22%;">Evento</th>
                    <th style="width: 15%;">Fecha del Evento</th>
                    <th style="width: 15%;">Rol desempeñado</th>
                    <th style="width: 10%;">Grupo</th>
                    <th style="width: 12%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participaciones as $idx => $p)
                    @php
                        $nombreCirculista = trim(($p->circulista_apellido ?? '') . ', ' . ($p->circulista_nombre ?? ''));
                        if ($nombreCirculista === ',' || $nombreCirculista === '') {
                            $nombreCirculista = '—';
                        }
                        $contacto = $p->circulista_celular ?: $p->circulista_email ?: '';

                        $tipoNombre = $p->tipo_evento_nombre ?? 'Retiro';
                        $numEvento = $p->numero_evento ? "#{$p->numero_evento}" : '';
                        $eventoNombre = trim("{$tipoNombre} {$numEvento}");

                        $fecha = '—';
                        $fechaFin = null;
                        if (!empty($p->evento_fecha_inicio)) {
                            try {
                                $fecha = \Carbon\Carbon::parse($p->evento_fecha_inicio)->format('d/m/Y');
                                if (!empty($p->evento_fecha_fin) && $p->evento_fecha_fin !== $p->evento_fecha_inicio) {
                                    $fechaFin = \Carbon\Carbon::parse($p->evento_fecha_fin)->format('d/m/Y');
                                }
                            } catch (\Throwable $e) {
                                $fecha = $p->evento_fecha_inicio;
                            }
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #94a3b8; font-size: 10px;">{{ $idx + 1 }}</td>
                        <td>
                            <div class="circulista-nombre">
                                {{ $nombreCirculista }}
                            </div>
                            @if($contacto)
                                <div class="circulista-contacto">
                                    {{ $contacto }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $eventoNombre }}</strong>
                            @if($p->evento_lugar)
                                <div style="font-size: 10px; color: #64748b;">{{ $p->evento_lugar }}</div>
                            @endif
                        </td>
                        <td>
                            @if($fecha !== '—')
                                <span style="font-weight: 600;">{{ $fecha }}</span>
                                @if($fechaFin)
                                    <div class="fecha-rango">al {{ $fechaFin }}</div>
                                @endif
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-rol">
                                {{ $p->rol_nombre ?? 'Sin Rol' }}
                            </span>
                        </td>
                        <td>
                            {{ $p->grupo ?: '—' }}
                        </td>
                        <td style="font-size: 10px; color: #475569;">
                            {{ $p->observaciones ?: '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-print">
            MCJ San Juan • Reporte generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }} • Página 1
        </div>
    @endif

    <script>
        // Disparar diálogo de impresión automáticamente al terminar de cargar
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 350);
        });
    </script>
</body>
</html>
