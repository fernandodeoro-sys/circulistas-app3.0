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

        $tiposEvento = TipoEvento::withCount('eventos')->orderBy('nombre')->get();
        $totalEventos = Evento::count();

        $tipoEventoSeleccionado = null;
        if ($request->filled('tipo_evento_id')) {
            $tipoEventoId = $request->input('tipo_evento_id');
            $query->where('tipo_evento_id', $tipoEventoId);
            $tipoEventoSeleccionado = $tiposEvento->firstWhere('id', $tipoEventoId);
        }

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

        return view('eventos.index', compact('eventos', 'tiposEvento', 'totalEventos', 'tipoEventoSeleccionado'));
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
            'foto_evento' => 'nullable|image|max:10240',
            'foto_cocina' => 'nullable|image|max:10240',
            'observaciones' => 'nullable|string',
        ]);

        $validated['activo'] = $request->has('activo');

        $disk = config('filesystems.default', 'public');

        if ($request->hasFile('foto_evento')) {
            $validated['foto_evento'] = $request->file('foto_evento')->store('eventos', $disk);
        }

        if ($request->hasFile('foto_cocina')) {
            $validated['foto_cocina'] = $request->file('foto_cocina')->store('eventos', $disk);
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
            'foto_evento' => 'nullable|image|max:10240',
            'foto_cocina' => 'nullable|image|max:10240',
            'observaciones' => 'nullable|string',
        ]);

        $validated['activo'] = $request->has('activo');

        $disk = config('filesystems.default', 'public');

        if ($request->hasFile('foto_evento')) {
            // Eliminar archivo viejo si existe
            if ($evento->foto_evento) {
                Storage::disk($disk)->delete($evento->foto_evento);
            }
            $validated['foto_evento'] = $request->file('foto_evento')->store('eventos', $disk);
        }

        if ($request->hasFile('foto_cocina')) {
            // Eliminar archivo viejo si existe
            if ($evento->foto_cocina) {
                Storage::disk($disk)->delete($evento->foto_cocina);
            }
            $validated['foto_cocina'] = $request->file('foto_cocina')->store('eventos', $disk);
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

        $disk = config('filesystems.default', 'public');

        // Eliminar archivos físicos si existen
        if ($evento->foto_evento) {
            Storage::disk($disk)->delete($evento->foto_evento);
        }
        if ($evento->foto_cocina) {
            Storage::disk($disk)->delete($evento->foto_cocina);
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
            'participantes.*.db_id' => 'nullable|integer',
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

            // 3.1 Cargar todos los circulistas activos en memoria
            $allCirculistas = \App\Models\Circulista::select([
                'id', 'nombre', 'apellido', 'email', 'celular', 'telefono', 
                'fecha_nacimiento', 'sin_anio_nacimiento', 'domicilio', 'localidad', 'provincia'
            ])->where('activo', true)->get();

            // 3.2 Indexar circulistas en memoria
            $idIndex = [];
            $nameIndex = [];
            $phoneIndex = [];

            foreach ($allCirculistas as $circulista) {
                $idIndex[$circulista->id] = $circulista;

                $nameKey = $this->normalizeString($circulista->apellido) . '|' . $this->normalizeString($circulista->nombre);
                if (!isset($nameIndex[$nameKey])) {
                    $nameIndex[$nameKey] = $circulista;
                }

                if (!empty($circulista->celular)) {
                    $last7 = $this->getPhoneLast7($circulista->celular);
                    if ($last7 && !isset($phoneIndex[$last7])) {
                        $phoneIndex[$last7] = $circulista;
                    }
                }

                if (!empty($circulista->telefono)) {
                    $last7 = $this->getPhoneLast7($circulista->telefono);
                    if ($last7 && !isset($phoneIndex[$last7])) {
                        $phoneIndex[$last7] = $circulista;
                    }
                }
            }

            // 3.3 Cargar todas las participaciones del evento destino en memoria
            $existingParticipaciones = \App\Models\Participacion::where('evento_id', $eventoId)
                ->get()
                ->keyBy('circulista_id');

            $participacionesToCreate = [];
            
            // Llevar registro de circulistas actualizados para no repetir la query de update
            $updatedCirculistas = [];

            foreach ($participantes as $p) {
                $dbId = isset($p['db_id']) ? $p['db_id'] : null;
                $nombre = trim($p['nombre']);
                $apellido = trim($p['apellido']);
                $celular = !empty($p['celular']) ? trim($p['celular']) : '';

                $circulista = null;

                // A. Intentar buscar por ID si viene pre-verificado del frontend
                if ($dbId && isset($idIndex[$dbId])) {
                    $circulista = $idIndex[$dbId];
                }

                // B. Si no, buscar por coincidencia exacta de nombre y apellido normalizados
                if (!$circulista) {
                    $nameKey = $this->normalizeString($apellido) . '|' . $this->normalizeString($nombre);
                    if (isset($nameIndex[$nameKey])) {
                        $circulista = $nameIndex[$nameKey];
                    }
                }

                // C. Si no coincide, intentar buscar por celular (últimos 7 dígitos)
                if (!$circulista && !empty($celular)) {
                    $last7 = $this->getPhoneLast7($celular);
                    if ($last7 && isset($phoneIndex[$last7])) {
                        $circulista = $phoneIndex[$last7];
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
                    
                    if (!empty($updatedData) && !isset($updatedCirculistas[$circulista->id])) {
                        $circulista->update($updatedData);
                        $updatedCirculistas[$circulista->id] = true;
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

                    // Agregar al índice en memoria para evitar crearlo duplicado
                    // si vuelve a aparecer en la misma lista del Excel
                    $idIndex[$circulista->id] = $circulista;
                    
                    $nameKey = $this->normalizeString($circulista->apellido) . '|' . $this->normalizeString($circulista->nombre);
                    $nameIndex[$nameKey] = $circulista;

                    if (!empty($circulista->celular)) {
                        $last7 = $this->getPhoneLast7($circulista->celular);
                        if ($last7) {
                            $phoneIndex[$last7] = $circulista;
                        }
                    }
                }

                // Registrar la Participación
                // Evitamos duplicar si la persona ya está vinculada a ese mismo evento
                $participacionExistente = $existingParticipaciones->get($circulista->id);

                if ($participacionExistente) {
                    // Si ya existe, actualizamos su rol y grupo si son distintos
                    $grupoVal = !empty($p['grupo']) ? trim($p['grupo']) : null;
                    if ($participacionExistente->rol_id !== (int)$p['rol_id'] || $participacionExistente->grupo !== $grupoVal) {
                        $participacionExistente->update([
                            'rol_id' => $p['rol_id'],
                            'grupo' => $grupoVal
                        ]);
                    }
                } else {
                    $participacionesToCreate[] = [
                        'circulista_id' => $circulista->id,
                        'evento_id' => $eventoId,
                        'rol_id' => $p['rol_id'],
                        'grupo' => !empty($p['grupo']) ? trim($p['grupo']) : null,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ];
                    $participacionesCreadasCount++;
                }
            }

            // Insertar todas las nuevas participaciones en una sola consulta
            if (!empty($participacionesToCreate)) {
                \App\Models\Participacion::insert($participacionesToCreate);
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

    /**
     * Normalizes a string: removes accents, converts to lowercase, and trims whitespace.
     */
    private function normalizeString(string $str): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', \Illuminate\Support\Str::ascii($str))));
    }

    /**
     * Extracts the last 7 digits of a phone number.
     */
    private function getPhoneLast7(string $phone): string
    {
        $clean = preg_replace('/[^\d]/', '', $phone);
        return strlen($clean) >= 7 ? substr($clean, -7) : '';
    }
}
