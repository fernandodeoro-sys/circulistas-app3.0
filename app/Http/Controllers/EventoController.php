<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Evento::with('tipoEvento');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('lugar', 'ilike', "%{$search}%")
                  ->orWhere('numero_evento', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'ilike', "%{$search}%")
                  ->orWhereHas('tipoEvento', function ($qSub) use ($search) {
                      $qSub->where('nombre', 'ilike', "%{$search}%");
                  });
            });
        }

        $eventos = $query->orderBy('fecha_inicio', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposEvento = TipoEvento::orderBy('nombre')->get();
        return view('eventos.create', compact('tiposEvento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'numero_evento' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('eventos')->where(function ($query) use ($request) {
                    return $query->where('tipo_evento_id', $request->tipo_evento_id);
                })
            ],
            'lugar' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'foto_evento' => 'nullable|image|max:2048',
            'foto_cocina' => 'nullable|image|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        $validated['activo'] = $request->has('activo');

        if ($request->hasFile('foto_evento')) {
            $validated['foto_evento'] = $request->file('foto_evento')->store('eventos', 'public');
        }

        if ($request->hasFile('foto_cocina')) {
            $validated['foto_cocina'] = $request->file('foto_cocina')->store('eventos', 'public');
        }

        Evento::create($validated);

        return redirect()->route('eventos.index')
            ->with('success', 'Evento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $evento = Evento::with(['tipoEvento', 'participaciones.circulista', 'participaciones.rol'])->findOrFail($id);
        
        $roles = \App\Models\Rol::orderBy('nombre')->get();
        
        $participandoIds = $evento->participaciones->pluck('circulista_id')->toArray();
        $circulistasDisponibles = \App\Models\Circulista::whereNotIn('id', $participandoIds)
            ->where('activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('eventos.show', compact('evento', 'roles', 'circulistasDisponibles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $evento = Evento::findOrFail($id);
        $tiposEvento = TipoEvento::orderBy('nombre')->get();
        return view('eventos.edit', compact('evento', 'tiposEvento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'numero_evento' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('eventos')->where(function ($query) use ($request) {
                    return $query->where('tipo_evento_id', $request->tipo_evento_id);
                })->ignore($evento->id)
            ],
            'lugar' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'foto_evento' => 'nullable|image|max:2048',
            'foto_cocina' => 'nullable|image|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        $validated['activo'] = $request->has('activo');

        if ($request->hasFile('foto_evento')) {
            // Eliminar archivo viejo si existe
            if ($evento->foto_evento) {
                Storage::disk('public')->delete($evento->foto_evento);
            }
            $validated['foto_evento'] = $request->file('foto_evento')->store('eventos', 'public');
        }

        if ($request->hasFile('foto_cocina')) {
            // Eliminar archivo viejo si existe
            if ($evento->foto_cocina) {
                Storage::disk('public')->delete($evento->foto_cocina);
            }
            $validated['foto_cocina'] = $request->file('foto_cocina')->store('eventos', 'public');
        }

        $evento->update($validated);

        return redirect()->route('eventos.index')
            ->with('success', 'Evento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $evento = Evento::findOrFail($id);

        // Eliminar archivos físicos si existen
        if ($evento->foto_evento) {
            Storage::disk('public')->delete($evento->foto_evento);
        }
        if ($evento->foto_cocina) {
            Storage::disk('public')->delete($evento->foto_cocina);
        }

        $evento->delete();

        return redirect()->route('eventos.index')
            ->with('success', 'Evento eliminado exitosamente.');
    }
}
