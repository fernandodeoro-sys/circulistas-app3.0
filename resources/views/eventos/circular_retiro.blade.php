<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circular Retiro - {{ $evento->tipoEvento->nombre ?? 'Retiro' }} #{{ $evento->numero_evento }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 20px;
            padding: 0;
            font-size: 11px;
            background-color: #fff;
        }

        /* Contenedor principal */
        .circular-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .header-title-cell {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            padding-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .event-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 6px 0;
        }

        .event-meta {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            padding-top: 4px;
        }

        /* Tabla de Datos estilo planilla Excel */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-top: 10px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .data-table th {
            font-weight: bold;
            text-align: center;
        }

        /* Columna de nombres */
        .col-name {
            width: 32%;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Columna de Domicilio */
        .col-domicilio {
            width: 48%;
            text-align: left;
        }

        /* Columna de Teléfono */
        .col-tel {
            width: 12%;
            text-align: center;
        }

        /* Columna de Fecha de Nacimiento */
        .col-fn {
            width: 8%;
            text-align: center;
        }

        /* Filas de títulos de sección */
        .section-row td {
            font-weight: bold;
            text-decoration: underline;
            background-color: #f8fafc;
            text-align: left;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 6px 8px;
            font-size: 11px;
        }

        .section-patrulla {
            text-align: center !important;
            font-weight: bold !important;
            text-decoration: none !important;
        }

        /* Clases auxiliares para centrar u ocultar */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* Estilos de Impresión */
        @media print {
            body {
                margin: 10px;
                font-size: 10px;
            }
            .no-print {
                display: none !important;
            }
            .data-table th, .data-table td {
                padding: 4px 6px;
            }
            .circular-container {
                max-width: 100%;
            }
        }

        /* Botón de impresión */
        .print-btn-container {
            text-align: right;
            margin-bottom: 15px;
        }

        .btn {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }
    </style>
</head>
<body>

    @php
        function formatFN($date) {
            if (!$date) return '—';
            $months = [
                1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr',
                5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago',
                9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'
            ];
            return sprintf('%02d-%s', $date->day, $months[$date->month]);
        }

        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        $diaInicio = $evento->fecha_inicio ? $evento->fecha_inicio->day : '';
        $diaFin = $evento->fecha_fin ? $evento->fecha_fin->day : '';
        $mesFin = $evento->fecha_fin ? $meses[$evento->fecha_fin->month] : '';
        $anioFin = $evento->fecha_fin ? $evento->fecha_fin->year : '';

        $rangoFechas = $evento->fecha_inicio && $evento->fecha_fin 
            ? "{$diaInicio} AL {$diaFin} DE {$mesFin} DE {$anioFin}" 
            : '';
        $lugar = $evento->lugar ? strtoupper($evento->lugar) : '';
    @endphp

    <div class="circular-container">
        
        <!-- Acciones en pantalla -->
        <div class="print-btn-container no-print">
            <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
            <button onclick="window.print()" class="btn">Imprimir Circular</button>
        </div>

        <!-- Encabezado con logotipo -->
        <table class="header-table">
            <tr>
                <td class="header-logo-cell">
                    <svg viewBox="0 0 100 100" style="width: 85px; height: 85px; color: #1e3a8a;">
                        <!-- Outer Circles -->
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="2"/>
                        <circle cx="50" cy="50" r="41" fill="none" stroke="currentColor" stroke-width="0.5" stroke-dasharray="1 1"/>
                        
                        <!-- Cross -->
                        <path d="M 50 16 L 50 82 M 30 36 L 70 36" stroke="currentColor" stroke-width="3" fill="none"/>
                        
                        <!-- Pilgrim Figure -->
                        <path d="M 49 53 C 47 57 44 62 46 66 L 41 78 M 46 66 L 51 78 M 49 53 C 51 53 53 50 51 47 C 49 44 46 47 49 53 Z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                        <circle cx="51.5" cy="42" r="2.5" fill="currentColor"/>
                        <!-- Pilgrim stick -->
                        <path d="M 44 45 L 39 78" stroke="currentColor" stroke-width="1.2"/>
                        
                        <!-- Circular Text (Simulado para que quepa en SVG nativo sin dependencias) -->
                        <path id="textPath" d="M 17 50 A 33 33 0 1 1 83 50" fill="none" />
                        <text font-size="6.5" font-family="Arial" font-weight="bold" fill="currentColor">
                            <textPath href="#textPath" startOffset="50%" text-anchor="middle">
                                MOVIMIENTO CÍRCULOS DE JUVENTUD
                            </textPath>
                        </text>
                        <text x="50" y="91" font-size="8" font-family="Arial" font-weight="bold" fill="currentColor" text-anchor="middle">
                            EN CADENA
                        </text>
                    </svg>
                </td>
                <td class="header-title-cell">
                    <h1 class="main-title">Movimiento Círculos de Juventud - San Juan</h1>
                    <h2 class="event-title">{{ $evento->tipoEvento->nombre ?? 'Retiro' }} Nº {{ $evento->numero_evento }}</h2>
                    <p class="event-meta">{{ $rangoFechas }} - {{ $lugar }}</p>
                </td>
            </tr>
        </table>

        <!-- Planilla de Datos -->
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-name"></th>
                    <th class="col-domicilio">Domicilio</th>
                    <th class="col-tel">Tel.</th>
                    <th class="col-fn">F/N</th>
                </tr>
            </thead>
            <tbody>
                <!-- Asesor Espiritual -->
                @if($asesores->isNotEmpty())
                    <tr class="section-row">
                        <td colspan="4">Asesor Espiritual</td>
                    </tr>
                    @foreach($asesores as $p)
                        <tr>
                            <td class="col-name">{{ $p->circulista->apellido }}, {{ $p->circulista->nombre }}</td>
                            <td class="col-domicilio">{{ $p->circulista->domicilio ?: '—' }}</td>
                            <td class="col-tel">{{ $p->circulista->celular ?: $p->circulista->telefono ?: '—' }}</td>
                            <td class="col-fn">{{ formatFN($p->circulista->fecha_nacimiento) }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Rector/a -->
                @if($rectores->isNotEmpty())
                    <tr class="section-row">
                        <td colspan="4">Rector/a</td>
                    </tr>
                    @foreach($rectores as $p)
                        <tr>
                            <td class="col-name">{{ $p->circulista->apellido }}, {{ $p->circulista->nombre }}</td>
                            <td class="col-domicilio">{{ $p->circulista->domicilio ?: '—' }}</td>
                            <td class="col-tel">{{ $p->circulista->celular ?: $p->circulista->telefono ?: '—' }}</td>
                            <td class="col-fn">{{ formatFN($p->circulista->fecha_nacimiento) }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Vicerrector/a -->
                @if($vicerectores->isNotEmpty())
                    <tr class="section-row">
                        <td colspan="4">Vicerrector/a</td>
                    </tr>
                    @foreach($vicerectores as $p)
                        <tr>
                            <td class="col-name">{{ $p->circulista->apellido }}, {{ $p->circulista->nombre }}</td>
                            <td class="col-domicilio">{{ $p->circulista->domicilio ?: '—' }}</td>
                            <td class="col-tel">{{ $p->circulista->celular ?: $p->circulista->telefono ?: '—' }}</td>
                            <td class="col-fn">{{ formatFN($p->circulista->fecha_nacimiento) }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Grupos / Patrullas -->
                @foreach($grupos as $nombreGrupo => $participantesGrupo)
                    @php
                        // Separar asistentes y circulistas
                        $asistentesGrupo = $participantesGrupo->filter(function($p) {
                            return in_array($p->rol_id, [5, 10, 11, 12]) 
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'asistente')
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'servidor')
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'mensajero')
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'ganchista');
                        })->sortBy(fn($p) => $p->circulista->apellido);

                        $circulistasGrupo = $participantesGrupo->filter(function($p) {
                            return in_array($p->rol_id, [1, 2, 13, 14, 16])
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'circulista')
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'peregrino')
                                || str_contains(strtolower($p->rol->nombre ?? ''), 'participante');
                        })->sortBy(fn($p) => $p->circulista->apellido);
                    @endphp

                    <!-- Asistentes de la Patrulla -->
                    @if($asistentesGrupo->isNotEmpty())
                        <tr class="section-row">
                            <td>Asistentes</td>
                            <td class="section-patrulla">Patrulla: "{{ $nombreGrupo }}"</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($asistentesGrupo as $p)
                            <tr>
                                <td class="col-name">{{ $p->circulista->apellido }}, {{ $p->circulista->nombre }}</td>
                                <td class="col-domicilio">{{ $p->circulista->domicilio ?: '—' }}</td>
                                <td class="col-tel">{{ $p->circulista->celular ?: $p->circulista->telefono ?: '—' }}</td>
                                <td class="col-fn">{{ formatFN($p->circulista->fecha_nacimiento) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Circulistas de la Patrulla -->
                    @if($circulistasGrupo->isNotEmpty())
                        <tr class="section-row">
                            <td>Circulistas</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($circulistasGrupo as $p)
                            <tr>
                                <td class="col-name">{{ $p->circulista->apellido }}, {{ $p->circulista->nombre }}</td>
                                <td class="col-domicilio">{{ $p->circulista->domicilio ?: '—' }}</td>
                                <td class="col-tel">{{ $p->circulista->celular ?: $p->circulista->telefono ?: '—' }}</td>
                                <td class="col-fn">{{ formatFN($p->circulista->fecha_nacimiento) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>

    <script>
        // Lanzar impresión automáticamente al cargar la página en la ventana/pestaña nueva
        window.onload = function() {
            // Un pequeño delay para asegurar renderizado correcto
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
