<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Participacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipacionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'circulista_ids' => 'required|array|min:1',
            'circulista_ids.*' => [
                'exists:circulistas,id',
                Rule::unique('participaciones', 'circulista_id')->where(function ($query) use ($request) {
                    return $query->where('evento_id', $request->evento_id);
                })
            ],
            'rol_id' => 'required|exists:roles,id',
            'grupo' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ], [
            'circulista_ids.*.unique' => 'Uno o más circulistas seleccionados ya están participando en este evento.',
            'circulista_ids.required' => 'Debe seleccionar al menos un circulista.',
        ]);

        // Validación de Regla de Negocio:
        // Si el evento es de tipo "Eslabón" y el rol asignado es "Circulista",
        // no permitir si ya participó como "Circulista" en algún evento de tipo "Eslabón" anteriormente.
        $evento = \App\Models\Evento::with('tipoEvento')->findOrFail($request->evento_id);
        $rol = \App\Models\Rol::findOrFail($request->rol_id);

        if ($evento->tipoEvento && in_array($evento->tipoEvento->nombre, ['Eslabón', 'Eslabon']) && $rol->nombre === 'Circulista') {
            foreach ($request->circulista_ids as $circulistaId) {
                $exists = Participacion::where('circulista_id', $circulistaId)
                    ->whereHas('rol', function ($q) {
                        $q->where('nombre', 'Circulista');
                    })
                    ->whereHas('evento.tipoEvento', function ($q) {
                        $q->whereIn('nombre', ['Eslabón', 'Eslabon']);
                    })
                    ->exists();

                if ($exists) {
                    $circulista = \App\Models\Circulista::find($circulistaId);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['circulista_ids' => "El circulista {$circulista->apellido}, {$circulista->nombre} ya ha participado como Circulista en un retiro de Eslabón anteriormente."]);
                }
            }
        }

        foreach ($validated['circulista_ids'] as $circulistaId) {
            Participacion::create([
                'evento_id' => $validated['evento_id'],
                'circulista_id' => $circulistaId,
                'rol_id' => $validated['rol_id'],
                'grupo' => $validated['grupo'],
                'observaciones' => $validated['observaciones'],
            ]);
        }

        return redirect()->back()
            ->with('success', 'Participantes agregados exitosamente al evento.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $participacion = Participacion::with(['evento.tipoEvento', 'circulista'])->findOrFail($id);

        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'grupo' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ]);

        $rol = \App\Models\Rol::findOrFail($request->rol_id);

        // Validación de Regla de Negocio:
        // Si el evento es de tipo "Eslabón" y el rol asignado es "Circulista",
        // no permitir si ya participó como "Circulista" en algún evento de tipo "Eslabón" anteriormente.
        if ($participacion->evento->tipoEvento && in_array($participacion->evento->tipoEvento->nombre, ['Eslabón', 'Eslabon']) && $rol->nombre === 'Circulista') {
            $exists = Participacion::where('id', '!=', $participacion->id)
                ->where('circulista_id', $participacion->circulista_id)
                ->whereHas('rol', function ($q) {
                    $q->where('nombre', 'Circulista');
                })
                ->whereHas('evento.tipoEvento', function ($q) {
                    $q->whereIn('nombre', ['Eslabón', 'Eslabon']);
                })
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['rol_id' => "Este circulista ya ha participado como Circulista en un retiro de Eslabón anteriormente."]);
            }
        }

        $participacion->update($validated);

        return redirect()->back()
            ->with('success', 'Participación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $participacion = Participacion::findOrFail($id);
        $participacion->delete();

        return redirect()->back()
            ->with('success', 'Participante quitado del evento exitosamente.');
    }
}
