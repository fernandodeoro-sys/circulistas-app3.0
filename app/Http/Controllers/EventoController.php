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

    /**
     * Show the printable circular of the retreat.
     */
    public function circularRetiro(string $id)
    {
        $evento = Evento::with(['tipoEvento', 'participaciones.circulista', 'participaciones.rol'])->findOrFail($id);

        $participaciones = $evento->participaciones;

        // Asesores (Asesor, Vice Asesor)
        $asesores = $participaciones->filter(function($p) {
            return in_array($p->rol_id, [9, 15]) || str_contains(strtolower($p->rol->nombre ?? ''), 'asesor');
        });

        // Rectores
        $rectores = $participaciones->filter(function($p) {
            return $p->rol_id == 3 || strtolower($p->rol->nombre ?? '') == 'rector';
        });

        // Vice Rectores
        $vicerectores = $participaciones->filter(function($p) {
            return $p->rol_id == 4 || strtolower($p->rol->nombre ?? '') == 'vice rector';
        });

        // Asistentes y Circulistas
        $restoParticipantes = $participaciones->reject(function($p) {
            return in_array($p->rol_id, [3, 4, 6, 7, 8, 9, 15]) 
                || str_contains(strtolower($p->rol->nombre ?? ''), 'asesor')
                || str_contains(strtolower($p->rol->nombre ?? ''), 'rector')
                || str_contains(strtolower($p->rol->nombre ?? ''), 'cocina')
                || str_contains(strtolower($p->rol->nombre ?? ''), 'cocinero');
        });

        // Agrupamos por patrulla/grupo
        $grupos = $restoParticipantes->groupBy(function($p) {
            return $p->grupo ?: 'Sin Patrulla';
        });

        return view('eventos.circular_retiro', compact('evento', 'asesores', 'rectores', 'vicerectores', 'grupos'));
    }

    /**
     * Show the printable circular of the kitchen.
     */
    public function circularCocina(string $id)
    {
        $evento = Evento::with(['tipoEvento', 'participaciones.circulista', 'participaciones.rol'])->findOrFail($id);

        $participaciones = $evento->participaciones;

        // Jefe de Cocina
        $jefesCocina = $participaciones->filter(function($p) {
            return $p->rol_id == 6 || str_contains(strtolower($p->rol->nombre ?? ''), 'jefe');
        });

        // Cocinero
        $cocineros = $participaciones->filter(function($p) {
            return $p->rol_id == 7 || (str_contains(strtolower($p->rol->nombre ?? ''), 'cocinero') && !str_contains(strtolower($p->rol->nombre ?? ''), 'jefe') && !str_contains(strtolower($p->rol->nombre ?? ''), 'integrante'));
        });

        // Integrantes de Cocina
        $integrantesCocina = $participaciones->filter(function($p) {
            return $p->rol_id == 8 || str_contains(strtolower($p->rol->nombre ?? ''), 'integrante');
        });

        return view('eventos.circular_cocina', compact('evento', 'jefesCocina', 'cocineros', 'integrantesCocina'));
    }

    /**
     * Show the view for mass importing participants from Excel or PDF.
     */
    public function showImportForm()
    {
        $tiposEvento = \App\Models\TipoEvento::orderBy('nombre')->get();
        $eventos = \App\Models\Evento::with('tipoEvento')->orderBy('fecha_inicio', 'desc')->get();
        $roles = \App\Models\Rol::orderBy('nombre')->get();

        return view('eventos.importar', compact('tiposEvento', 'eventos', 'roles'));
    }

    /**
     * Handle the mass import of participants.
     */
    public function importMasivo(Request $request)
    {
        // 1. Validar la estructura básica del payload
        $rules = [
            'evento_modo' => 'required|in:existente,nuevo',
            'participantes' => 'required|array|min:1',
            'participantes.*.nombre' => 'required|string|max:100',
            'participantes.*.apellido' => 'required|string|max:100',
            'participantes.*.rol_id' => 'required|exists:roles,id',
            'participantes.*.grupo' => 'nullable|string|max:50',
            'participantes.*.email' => 'nullable|email|max:150',
            'participantes.*.celular' => 'nullable|string|max:50',
            'participantes.*.fecha_nacimiento' => 'nullable|date',
            'participantes.*.sin_anio_nacimiento' => 'nullable|boolean',
            'participantes.*.domicilio' => 'nullable|string|max:255',
            'participantes.*.localidad' => 'nullable|string|max:100',
            'participantes.*.provincia' => 'nullable|string|max:100',
        ];

        if ($request->input('evento_modo') === 'existente') {
            $rules['evento_id'] = 'required|exists:eventos,id';
        } else {
            $rules['tipo_evento_id'] = 'required|exists:tipos_evento,id';
            $rules['numero_evento'] = 'required|integer';
            $rules['lugar'] = 'required|string|max:255';
            $rules['fecha_inicio'] = 'required|date';
            $rules['fecha_fin'] = 'required|date|after_or_equal:fecha_inicio';
            $rules['observaciones'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Iniciar transacción de base de datos
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // 2. Obtener o crear el Evento
            if ($request->input('evento_modo') === 'existente') {
                $eventoId = $request->input('evento_id');
                $evento = \App\Models\Evento::findOrFail($eventoId);
            } else {
                // Verificar si ya existe un evento del mismo tipo y número
                $eventoExistente = \App\Models\Evento::where('tipo_evento_id', $validated['tipo_evento_id'])
                    ->where('numero_evento', $validated['numero_evento'])
                    ->first();

                if ($eventoExistente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe un evento registrado con ese tipo y número (' . ($eventoExistente->tipoEvento->nombre ?? 'Retiro') . ' Nº ' . $eventoExistente->numero_evento . ').'
                    ], 422);
                }

                $evento = \App\Models\Evento::create([
                    'tipo_evento_id' => $validated['tipo_evento_id'],
                    'numero_evento' => $validated['numero_evento'],
                    'lugar' => $validated['lugar'],
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin' => $validated['fecha_fin'],
                    'observaciones' => $validated['observaciones'] ?? null,
                    'activo' => true
                ]);
                $eventoId = $evento->id;
            }

            // 3. Procesar participantes
            $personasCreadasCount = 0;
            $personasAsociadasCount = 0;
            $participacionesCreadasCount = 0;

            $participantes = $request->input('participantes');

            foreach ($participantes as $p) {
                $nombre = trim($p['nombre']);
                $apellido = trim($p['apellido']);

                // Buscar por coincidencia exacta de nombre y apellido (insensible a acentos)
                $circulista = \App\Models\Circulista::whereRaw('unaccent(nombre) ilike unaccent(?)', [$nombre])
                    ->whereRaw('unaccent(apellido) ilike unaccent(?)', [$apellido])
                    ->first();

                // Si no coincide, intentar buscar por celular (últimos 7 dígitos)
                $celular = !empty($p['celular']) ? trim($p['celular']) : '';
                if (!$circulista && !empty($celular)) {
                    $cleanCel = preg_replace('/[^\d]/', '', $celular);
                    if (strlen($cleanCel) >= 7) {
                        $last7 = substr($cleanCel, -7);
                        $circulista = \App\Models\Circulista::where(function($q) use ($last7) {
                            $q->whereRaw("regexp_replace(celular, '[^0-9]', '', 'g') LIKE ?", ['%' . $last7])
                              ->orWhereRaw("regexp_replace(telefono, '[^0-9]', '', 'g') LIKE ?", ['%' . $last7]);
                        })->first();
                    }
                }

                if ($circulista) {
                    $personasAsociadasCount++;
                    
                    // Opcional: Actualizar datos de contacto si vienen en el Excel y son diferentes/nuevos
                    $updatedData = [];
                    if (!empty($p['email']) && $circulista->email !== trim($p['email'])) {
                        $updatedData['email'] = trim($p['email']);
                    }
                    if (!empty($p['celular']) && $circulista->celular !== trim($p['celular'])) {
                        $updatedData['celular'] = trim($p['celular']);
                    }
                    
                    $dbFechaStr = $circulista->fecha_nacimiento ? $circulista->fecha_nacimiento->format('Y-m-d') : null;
                    if (!empty($p['fecha_nacimiento']) && $dbFechaStr !== $p['fecha_nacimiento']) {
                        $updatedData['fecha_nacimiento'] = $p['fecha_nacimiento'];
                        $updatedData['sin_anio_nacimiento'] = isset($p['sin_anio_nacimiento']) ? (bool)$p['sin_anio_nacimiento'] : false;
                    }
                    if (!empty($p['domicilio']) && $circulista->domicilio !== trim($p['domicilio'])) {
                        $updatedData['domicilio'] = trim($p['domicilio']);
                    }
                    if (!empty($p['localidad']) && $circulista->localidad !== trim($p['localidad'])) {
                        $updatedData['localidad'] = trim($p['localidad']);
                    }
                    if (!empty($p['provincia']) && $circulista->provincia !== trim($p['provincia'])) {
                        $updatedData['provincia'] = trim($p['provincia']);
                    }
                    
                    if (!empty($updatedData)) {
                        $circulista->update($updatedData);
                    }
                } else {
                    // Crear circulista nuevo
                    $circulista = \App\Models\Circulista::create([
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'email' => !empty($p['email']) ? trim($p['email']) : null,
                        'celular' => !empty($p['celular']) ? trim($p['celular']) : null,
                        'fecha_nacimiento' => !empty($p['fecha_nacimiento']) ? $p['fecha_nacimiento'] : null,
                        'sin_anio_nacimiento' => isset($p['sin_anio_nacimiento']) ? (bool)$p['sin_anio_nacimiento'] : false,
                        'domicilio' => !empty($p['domicilio']) ? trim($p['domicilio']) : null,
                        'localidad' => !empty($p['localidad']) ? trim($p['localidad']) : null,
                        'provincia' => !empty($p['provincia']) ? trim($p['provincia']) : null,
                        'activo' => true
                    ]);
                    $personasCreadasCount++;
                }

                // Registrar la Participación
                // Evitamos duplicar si la persona ya está vinculada a ese mismo evento
                $participacionExistente = \App\Models\Participacion::where('circulista_id', $circulista->id)
                    ->where('evento_id', $eventoId)
                    ->first();

                if ($participacionExistente) {
                    // Si ya existe, actualizamos su rol y grupo
                    $participacionExistente->update([
                        'rol_id' => $p['rol_id'],
                        'grupo' => !empty($p['grupo']) ? trim($p['grupo']) : null
                    ]);
                } else {
                    \App\Models\Participacion::create([
                        'circulista_id' => $circulista->id,
                        'evento_id' => $eventoId,
                        'rol_id' => $p['rol_id'],
                        'grupo' => !empty($p['grupo']) ? trim($p['grupo']) : null
                    ]);
                    $participacionesCreadasCount++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proceso finalizado correctamente.',
                'summary' => [
                    'evento' => ($request->input('evento_modo') === 'nuevo' ? 'Creado: ' : 'Asociado a: ') . ($evento->tipoEvento->nombre ?? 'Retiro') . ' Nº ' . $evento->numero_evento,
                    'evento_id' => $eventoId,
                    'circulistas_nuevos' => $personasCreadasCount,
                    'circulistas_existentes' => $personasAsociadasCount,
                    'participaciones' => $participacionesCreadasCount
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la importación: ' . $e->getMessage()
            ], 500);
        }
    }
}
