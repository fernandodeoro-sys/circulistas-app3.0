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
     * Display the advanced search results.
     */
    public function index(Request $request)
    {
        $tiposEvento = TipoEvento::orderBy('nombre')->get();
        
        $roles = Rol::orderBy('nombre')->get();

        $query = Participacion::with(['circulista', 'evento.tipoEvento', 'rol']);

        if ($request->filled('tipo_evento_id')) {
            $query->whereHas('evento', function ($q) use ($request) {
                $q->where('tipo_evento_id', $request->tipo_evento_id);
            });
        }

        if ($request->filled('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        $resultados = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('busqueda.index', compact('tiposEvento', 'roles', 'resultados'));
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
