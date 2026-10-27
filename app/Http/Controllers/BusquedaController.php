<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Circulista;
use App\Models\Evento;
use App\Models\Rol;
use App\Models\TipoEvento;
use App\Models\Participacion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BusquedaController extends Controller
{
    /**
     * Construye la consulta filtrada de participaciones para búsqueda avanzada.
     */
    private function buildQuery(Request $request)
    {
        $query = Participacion::with(['circulista', 'evento.tipoEvento', 'rol']);

        if ($request->filled('tipo_evento_id')) {
            $query->whereHas('evento', function ($q) use ($request) {
                $q->where('tipo_evento_id', $request->tipo_evento_id);
            });
        }

        if ($request->filled('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Display the advanced search results.
     */
    public function index(Request $request)
    {
        $tiposEvento = TipoEvento::orderBy('nombre')->get();
        $roles = Rol::orderBy('nombre')->get();

        $resultados = $this->buildQuery($request)
            ->paginate(20)
            ->withQueryString();

        return view('busqueda.index', compact('tiposEvento', 'roles', 'resultados'));
    }

    /**
     * Construye la consulta SQL optimizada con JOINs para exportaciones masivas e impresión rápida.
     */
    private function buildExportQuery(Request $request)
    {
        $query = \Illuminate\Support\Facades\DB::table('participaciones')
            ->leftJoin('circulistas', 'participaciones.circulista_id', '=', 'circulistas.id')
            ->leftJoin('eventos', 'participaciones.evento_id', '=', 'eventos.id')
            ->leftJoin('tipos_evento', 'eventos.tipo_evento_id', '=', 'tipos_evento.id')
            ->leftJoin('roles', 'participaciones.rol_id', '=', 'roles.id')
            ->select(
                'participaciones.id',
                'participaciones.grupo',
                'participaciones.observaciones',
                'circulistas.nombre as circulista_nombre',
                'circulistas.apellido as circulista_apellido',
                'circulistas.celular as circulista_celular',
                'circulistas.email as circulista_email',
                'eventos.numero_evento',
                'eventos.lugar as evento_lugar',
                'eventos.fecha_inicio as evento_fecha_inicio',
                'eventos.fecha_fin as evento_fecha_fin',
                'tipos_evento.nombre as tipo_evento_nombre',
                'roles.nombre as rol_nombre'
            )
            ->orderBy('participaciones.created_at', 'desc');

        if ($request->filled('tipo_evento_id')) {
            $query->where('eventos.tipo_evento_id', $request->tipo_evento_id);
        }

        if ($request->filled('rol_id')) {
            $query->where('participaciones.rol_id', $request->rol_id);
        }

        return $query;
    }

    /**
     * Exporta TODOS los resultados obtenidos (sin paginar) a CSV o Excel de forma ultra rápida.
     */
    public function exportar(Request $request)
    {
        @set_time_limit(300);

        $formato = strtolower($request->input('formato', 'csv'));
        $query = $this->buildExportQuery($request);

        if ($formato === 'excel') {
            return $this->exportarExcel($query);
        }

        return $this->exportarCsv($query);
    }

    /**
     * Genera la descarga en formato CSV transmitido con UTF-8 BOM.
     */
    private function exportarCsv($query)
    {
        @set_time_limit(300);

        $fileName = 'resultados_busqueda_avanzada_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Circulista',
                'Evento',
                'Fecha del Evento',
                'Rol desempeñado',
                'Grupo',
                'Observaciones'
            ], ';');

            $query->chunk(500, function ($rows) use ($file) {
                foreach ($rows as $row) {
                    $nombreCirculista = trim(($row->circulista_apellido ?? '') . ', ' . ($row->circulista_nombre ?? ''));
                    if ($nombreCirculista === ',' || $nombreCirculista === '') {
                        $nombreCirculista = '—';
                    }
                    $contacto = $row->circulista_celular ?: $row->circulista_email ?: '';
                    $circulista = $contacto ? "{$nombreCirculista} ({$contacto})" : $nombreCirculista;

                    $tipoNombre = $row->tipo_evento_nombre ?? 'Retiro';
                    $numEvento = $row->numero_evento ? "#{$row->numero_evento}" : '';
                    $eventoNombre = trim("{$tipoNombre} {$numEvento}");
                    $lugar = $row->evento_lugar ?? '';
                    $evento = $lugar ? "{$eventoNombre} ({$lugar})" : $eventoNombre;

                    $fecha = '—';
                    if (!empty($row->evento_fecha_inicio)) {
                        try {
                            $inicio = \Carbon\Carbon::parse($row->evento_fecha_inicio)->format('d/m/Y');
                            if (!empty($row->evento_fecha_fin) && $row->evento_fecha_fin !== $row->evento_fecha_inicio) {
                                $fin = \Carbon\Carbon::parse($row->evento_fecha_fin)->format('d/m/Y');
                                $fecha = "{$inicio} al {$fin}";
                            } else {
                                $fecha = $inicio;
                            }
                        } catch (\Throwable $e) {
                            $fecha = $row->evento_fecha_inicio;
                        }
                    }

                    $rol = $row->rol_nombre ?? 'Sin Rol';
                    $grupo = $row->grupo ?: '—';
                    $observaciones = $row->observaciones ?: '—';

                    fputcsv($file, [
                        $circulista,
                        $evento,
                        $fecha,
                        $rol,
                        $grupo,
                        $observaciones
                    ], ';');
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Genera la descarga en formato Excel (.xls) transmitido con formato HTML Spreadsheet.
     */
    private function exportarExcel($query)
    {
        @set_time_limit(300);

        $fileName = 'resultados_busqueda_avanzada_' . date('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fwrite($file, "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\n");
            fwrite($file, "<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n");
            fwrite($file, "<style>\n");
            fwrite($file, "table { border-collapse: collapse; width: 100%; }\n");
            fwrite($file, "th { background-color: #f1f5f9; font-weight: bold; border: 1px solid #cbd5e1; padding: 8px; text-align: left; }\n");
            fwrite($file, "td { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; }\n");
            fwrite($file, "</style>\n</head>\n<body>\n");
            fwrite($file, "<h2>Resultados de Búsqueda Avanzada - Padrón MCJ</h2>\n");
            fwrite($file, "<p>Fecha de emisión: " . now()->format('d/m/Y H:i') . "</p>\n");
            fwrite($file, "<table>\n<thead>\n<tr>\n");
            fwrite($file, "<th>Circulista</th>\n");
            fwrite($file, "<th>Evento</th>\n");
            fwrite($file, "<th>Fecha del Evento</th>\n");
            fwrite($file, "<th>Rol desempeñado</th>\n");
            fwrite($file, "<th>Grupo</th>\n");
            fwrite($file, "<th>Observaciones</th>\n");
            fwrite($file, "</tr>\n</thead>\n<tbody>\n");

            $query->chunk(500, function ($rows) use ($file) {
                foreach ($rows as $row) {
                    $nombreCirculista = trim(($row->circulista_apellido ?? '') . ', ' . ($row->circulista_nombre ?? ''));
                    if ($nombreCirculista === ',' || $nombreCirculista === '') {
                        $nombreCirculista = '—';
                    }
                    $contacto = $row->circulista_celular ?: $row->circulista_email ?: '';
                    $circulista = htmlspecialchars($contacto ? "{$nombreCirculista} ({$contacto})" : $nombreCirculista, ENT_QUOTES, 'UTF-8');

                    $tipoNombre = $row->tipo_evento_nombre ?? 'Retiro';
                    $numEvento = $row->numero_evento ? "#{$row->numero_evento}" : '';
                    $eventoNombre = trim("{$tipoNombre} {$numEvento}");
                    $lugar = $row->evento_lugar ?? '';
                    $evento = htmlspecialchars($lugar ? "{$eventoNombre} ({$lugar})" : $eventoNombre, ENT_QUOTES, 'UTF-8');

                    $fecha = '—';
                    if (!empty($row->evento_fecha_inicio)) {
                        try {
                            $inicio = \Carbon\Carbon::parse($row->evento_fecha_inicio)->format('d/m/Y');
                            if (!empty($row->evento_fecha_fin) && $row->evento_fecha_fin !== $row->evento_fecha_inicio) {
                                $fin = \Carbon\Carbon::parse($row->evento_fecha_fin)->format('d/m/Y');
                                $fecha = "{$inicio} al {$fin}";
                            } else {
                                $fecha = $inicio;
                            }
                        } catch (\Throwable $e) {
                            $fecha = $row->evento_fecha_inicio;
                        }
                    }
                    $fecha = htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8');

                    $rol = htmlspecialchars($row->rol_nombre ?? 'Sin Rol', ENT_QUOTES, 'UTF-8');
                    $grupo = htmlspecialchars($row->grupo ?: '—', ENT_QUOTES, 'UTF-8');
                    $obs = htmlspecialchars($row->observaciones ?: '—', ENT_QUOTES, 'UTF-8');

                    fwrite($file, "<tr><td>{$circulista}</td><td>{$evento}</td><td>{$fecha}</td><td>{$rol}</td><td>{$grupo}</td><td>{$obs}</td></tr>\n");
                }
            });

            fwrite($file, "</tbody>\n</table>\n</body>\n</html>");
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Muestra la vista de impresión con TODOS los resultados para guardar como PDF.
     */
    public function imprimir(Request $request)
    {
        @set_time_limit(300);

        $participaciones = $this->buildExportQuery($request)->get();

        $tipoEvento = null;
        if ($request->filled('tipo_evento_id')) {
            $tipoEvento = TipoEvento::find($request->tipo_evento_id);
        }

        $rol = null;
        if ($request->filled('rol_id')) {
            $rol = Rol::find($request->rol_id);
        }

        return view('busqueda.imprimir', compact('participaciones', 'tipoEvento', 'rol'));
    }

    /**
     * Search for a person and display their participation history in the last 2 years from the query date.
     */
    public function busquedaPersona(Request $request)
    {
        $circulistas = Circulista::where('activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $circulistasData = $circulistas->map(function ($c) {
            return [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellido' => $c->apellido,
                'localidad' => $c->localidad,
                'celular' => $c->celular,
                'label' => $c->apellido . ', ' . $c->nombre . ($c->localidad ? ' (' . $c->localidad . ')' : '')
            ];
        })->values();

        $circulistaSeleccionado = null;
        $participaciones = collect();
        $fechaConsulta = $request->input('fecha_consulta', date('Y-m-d'));
        $fechaDesde = Carbon::parse($fechaConsulta)->subYears(2)->format('Y-m-d');

        if ($request->filled('circulista_id')) {
            $circulistaSeleccionado = Circulista::find($request->circulista_id);

            if ($circulistaSeleccionado) {
                $participaciones = Participacion::with(['evento.tipoEvento', 'rol'])
                    ->where('circulista_id', $circulistaSeleccionado->id)
                    ->whereHas('evento', function ($q) use ($fechaDesde, $fechaConsulta) {
                        $q->whereBetween('fecha_inicio', [$fechaDesde, $fechaConsulta]);
                    })
                    ->get()
                    ->sortByDesc(function ($p) {
                        return $p->evento->fecha_inicio ? $p->evento->fecha_inicio->timestamp : 0;
                    });
            }
        }

        return view('busqueda.busqueda_persona', compact(
            'circulistas',
            'circulistasData',
            'circulistaSeleccionado',
            'participaciones',
            'fechaConsulta',
            'fechaDesde'
        ));
    }
}
