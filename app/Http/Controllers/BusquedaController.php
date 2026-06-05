<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Rol;
use App\Models\TipoEvento;
use App\Models\Participacion;
use Illuminate\Http\Request;

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
}
