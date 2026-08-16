<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Circulista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Circulista::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            // Reemplazar comas por espacios para permitir búsquedas como "Alvarez, María Alejandra"
            $searchClean = str_replace(',', ' ', $search);
            // Dividir por espacios y filtrar términos vacíos
            $words = array_filter(explode(' ', $searchClean));

            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhereRaw('unaccent(apellido) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhere('email', 'ilike', "%{$word}%")
                                 ->orWhereRaw('unaccent(localidad) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhereRaw('unaccent(provincia) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhere('celular', 'ilike', "%{$word}%")
                                 ->orWhere('telefono', 'ilike', "%{$word}%");
                        });
                    }
                });
            }
        }

        $circulistas = $query->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('circulistas.index', compact('circulistas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('circulistas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apellido' => 'required|string|max:100',
            'nombre' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150|unique:circulistas,email',
            'observaciones' => 'nullable|string',
            'fecha_nacimiento_tipo' => 'nullable|string|in:completa,solo_dia_mes,ninguna',
            'nacimiento_dia' => 'nullable|integer|between:1,31',
            'nacimiento_mes' => 'nullable|integer|between:1,12',
        ]);

        // Lógica de fecha de nacimiento
        $tipoFecha = $request->input('fecha_nacimiento_tipo', 'completa');
        $sinAnio = false;
        $fechaNacimiento = null;

        if ($tipoFecha === 'completa' && $request->filled('fecha_nacimiento')) {
            $fechaNacimiento = $request->input('fecha_nacimiento');
        } elseif ($tipoFecha === 'solo_dia_mes' && $request->filled('nacimiento_dia') && $request->filled('nacimiento_mes')) {
            $dia = (int)$request->input('nacimiento_dia');
            $mes = (int)$request->input('nacimiento_mes');
            if (!checkdate($mes, $dia, 1904)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'nacimiento_dia' => 'La combinación de día y mes ingresada no corresponde a una fecha válida.'
                    ]);
            }
            $fechaNacimiento = sprintf('1904-%02d-%02d', $mes, $dia);
            $sinAnio = true;
        }

        $nombre = trim($validated['nombre']);
        $apellido = trim($validated['apellido']);

        // Evitar duplicados por nombre y apellido AND (celular OR fecha_nacimiento)
        $existeDuplicado = false;
        $candidatos = Circulista::whereRaw('unaccent(nombre) ilike unaccent(?)', [$nombre])
            ->whereRaw('unaccent(apellido) ilike unaccent(?)', [$apellido])
            ->get();

        foreach ($candidatos as $candidato) {
            // Caso 1: Coincide el celular (si ambos lo tienen registrado)
            $celularNuevo = !empty($validated['celular']) ? preg_replace('/[^\d]/', '', $validated['celular']) : null;
            $celularExistente = !empty($candidato->celular) ? preg_replace('/[^\d]/', '', $candidato->celular) : null;
            
            if ($celularNuevo && $celularExistente && $celularNuevo === $celularExistente) {
                $existeDuplicado = true;
                break;
            }

            // Caso 2: Coincide la fecha de nacimiento (si ambos la tienen registrada)
            $fechaNacNuevo = !empty($fechaNacimiento) ? date('Y-m-d', strtotime($fechaNacimiento)) : null;
            $fechaNacExistente = $candidato->fecha_nacimiento ? $candidato->fecha_nacimiento->format('Y-m-d') : null;
            
            if ($fechaNacNuevo && $fechaNacExistente && $fechaNacNuevo === $fechaNacExistente) {
                $existeDuplicado = true;
                break;
            }
        }

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Ya existe un circulista registrado con ese nombre y apellido con el mismo celular o fecha de nacimiento.'
                ]);
        }

        $validated['fecha_nacimiento'] = $fechaNacimiento;
        $validated['sin_anio_nacimiento'] = $sinAnio;
        $validated['activo'] = $request->has('activo');

        Circulista::create($validated);

        return redirect()->route('circulistas.index')
            ->with('success', 'Circulista creado exitosamente.');
    }

    /**
     * Verify if a circulista already exists with the same name and surname.
     */
    public function verificarDuplicado(Request $request)
    {
        $nombre = trim($request->input('nombre', ''));
        $apellido = trim($request->input('apellido', ''));
        $celular = trim($request->input('celular', ''));
        $ignoreId = $request->input('ignore_id');

        if (empty($nombre) || empty($apellido)) {
            return response()->json(['existe' => false]);
        }

        // Determinar fecha de nacimiento ingresada
        $tipoFecha = $request->input('fecha_nacimiento_tipo', 'completa');
        $fechaNacimiento = null;

        if ($tipoFecha === 'completa' && $request->filled('fecha_nacimiento')) {
            $fechaNacimiento = $request->input('fecha_nacimiento');
        } elseif ($tipoFecha === 'solo_dia_mes' && $request->filled('nacimiento_dia') && $request->filled('nacimiento_mes')) {
            $dia = (int)$request->input('nacimiento_dia');
            $mes = (int)$request->input('nacimiento_mes');
            if (checkdate($mes, $dia, 1904)) {
                $fechaNacimiento = sprintf('1904-%02d-%02d', $mes, $dia);
            }
        }

        // Si no hay celular ni fecha de nacimiento, no se puede verificar duplicado bajo el criterio combinado
        if (empty($celular) && empty($fechaNacimiento)) {
            return response()->json(['existe' => false]);
        }

        $query = Circulista::whereRaw('unaccent(nombre) ilike unaccent(?)', [$nombre])
            ->whereRaw('unaccent(apellido) ilike unaccent(?)', [$apellido]);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $candidatos = $query->get();
        $existeDuplicado = false;
        $duplicado = null;

        foreach ($candidatos as $candidato) {
            // Caso 1: Coincide el celular (si ambos lo tienen registrado)
            $celularNuevo = !empty($celular) ? preg_replace('/[^\d]/', '', $celular) : null;
            $celularExistente = !empty($candidato->celular) ? preg_replace('/[^\d]/', '', $candidato->celular) : null;
            
            if ($celularNuevo && $celularExistente && $celularNuevo === $celularExistente) {
                $existeDuplicado = true;
                $duplicado = $candidato;
                break;
            }

            // Caso 2: Coincide la fecha de nacimiento (si ambos la tienen registrada)
            $fechaNacNuevo = !empty($fechaNacimiento) ? date('Y-m-d', strtotime($fechaNacimiento)) : null;
            $fechaNacExistente = $candidato->fecha_nacimiento ? $candidato->fecha_nacimiento->format('Y-m-d') : null;
            
            if ($fechaNacNuevo && $fechaNacExistente && $fechaNacNuevo === $fechaNacExistente) {
                $existeDuplicado = true;
                $duplicado = $candidato;
                break;
            }
        }

        if ($existeDuplicado && $duplicado) {
            return response()->json([
                'existe' => true,
                'url' => route('circulistas.show', $duplicado->id),
                'nombre_completo' => $duplicado->nombre . ' ' . $duplicado->apellido
            ]);
        }

        return response()->json(['existe' => false]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $circulista = Circulista::with(['participaciones.evento.tipoEvento', 'participaciones.rol'])->findOrFail($id);

        // Ordenar las participaciones por la fecha del evento de forma descendente (más recientes primero)
        $circulista->setRelation('participaciones', $circulista->participaciones->sortByDesc(function ($participacion) {
            return $participacion->evento->fecha_inicio ? $participacion->evento->fecha_inicio->timestamp : 0;
        }));

        return view('circulistas.show', compact('circulista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $circulista = Circulista::findOrFail($id);
        return view('circulistas.edit', compact('circulista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $circulista = Circulista::findOrFail($id);

        $validated = $request->validate([
            'apellido' => 'required|string|max:100',
            'nombre' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150|unique:circulistas,email,' . $id,
            'observaciones' => 'nullable|string',
            'fecha_nacimiento_tipo' => 'nullable|string|in:completa,solo_dia_mes,ninguna',
            'nacimiento_dia' => 'nullable|integer|between:1,31',
            'nacimiento_mes' => 'nullable|integer|between:1,12',
        ]);

        // Lógica de fecha de nacimiento
        $tipoFecha = $request->input('fecha_nacimiento_tipo', 'completa');
        $sinAnio = false;
        $fechaNacimiento = null;

        if ($tipoFecha === 'completa' && $request->filled('fecha_nacimiento')) {
            $fechaNacimiento = $request->input('fecha_nacimiento');
        } elseif ($tipoFecha === 'solo_dia_mes' && $request->filled('nacimiento_dia') && $request->filled('nacimiento_mes')) {
            $dia = (int)$request->input('nacimiento_dia');
            $mes = (int)$request->input('nacimiento_mes');
            if (!checkdate($mes, $dia, 1904)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'nacimiento_dia' => 'La combinación de día y mes ingresada no corresponde a una fecha válida.'
                    ]);
            }
            $fechaNacimiento = sprintf('1904-%02d-%02d', $mes, $dia);
            $sinAnio = true;
        }

        $nombre = trim($validated['nombre']);
        $apellido = trim($validated['apellido']);

        // Evitar duplicados por nombre y apellido (excluyendo el actual) AND (celular OR fecha_nacimiento)
        $existeDuplicado = false;
        $candidatos = Circulista::whereRaw('unaccent(nombre) ilike unaccent(?)', [$nombre])
            ->whereRaw('unaccent(apellido) ilike unaccent(?)', [$apellido])
            ->where('id', '!=', $id)
            ->get();

        foreach ($candidatos as $candidato) {
            // Caso 1: Coincide el celular (si ambos lo tienen registrado)
            $celularNuevo = !empty($validated['celular']) ? preg_replace('/[^\d]/', '', $validated['celular']) : null;
            $celularExistente = !empty($candidato->celular) ? preg_replace('/[^\d]/', '', $candidato->celular) : null;
            
            if ($celularNuevo && $celularExistente && $celularNuevo === $celularExistente) {
                $existeDuplicado = true;
                break;
            }

            // Caso 2: Coincide la fecha de nacimiento (si ambos la tienen registrada)
            $fechaNacNuevo = !empty($fechaNacimiento) ? date('Y-m-d', strtotime($fechaNacimiento)) : null;
            $fechaNacExistente = $candidato->fecha_nacimiento ? $candidato->fecha_nacimiento->format('Y-m-d') : null;
            
            if ($fechaNacNuevo && $fechaNacExistente && $fechaNacNuevo === $fechaNacExistente) {
                $existeDuplicado = true;
                break;
            }
        }

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Ya existe otro circulista registrado con ese nombre y apellido con el mismo celular o fecha de nacimiento.'
                ]);
        }

        $validated['fecha_nacimiento'] = $fechaNacimiento;
        $validated['sin_anio_nacimiento'] = $sinAnio;
        $validated['activo'] = $request->has('activo');

        $circulista->update($validated);

        return redirect()->route('circulistas.index')
            ->with('success', 'Circulista actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $circulista = Circulista::findOrFail($id);
        $circulista->delete();

        return redirect()->route('circulistas.index')
            ->with('success', 'Circulista eliminado exitosamente.');
    }

    /**
     * Verify importable people names against database to check for existing records.
     */
    public function verificarImportables(Request $request)
    {
        $request->validate([
            'personas' => 'required|array',
            'personas.*.nombre' => 'required|string',
            'personas.*.apellido' => 'required|string',
            'personas.*.celular' => 'nullable|string',
        ]);

        $personas = $request->input('personas');
        
        // 1. Cargar todos los circulistas activos en memoria para evitar consultas recurrentes
        $allCirculistas = Circulista::select([
            'id', 'nombre', 'apellido', 'email', 'celular', 'telefono', 
            'fecha_nacimiento', 'sin_anio_nacimiento', 'domicilio', 'localidad', 'provincia'
        ])->where('activo', true)->get();

        // 2. Indexar en memoria por clave de nombre y por últimos 7 dígitos del teléfono
        $nameIndex = [];
        $phoneIndex = [];

        foreach ($allCirculistas as $circulista) {
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

        $coincidencias = [];

        // 3. Realizar emparejamiento en memoria
        foreach ($personas as $index => $persona) {
            $nombre = trim($persona['nombre']);
            $apellido = trim($persona['apellido']);
            $celular = isset($persona['celular']) ? trim($persona['celular']) : '';

            $match = null;

            // Intentar coincidencia por nombre y apellido
            $nameKey = $this->normalizeString($apellido) . '|' . $this->normalizeString($nombre);
            if (isset($nameIndex[$nameKey])) {
                $match = $nameIndex[$nameKey];
            }

            // Si no coincide, intentar por celular (últimos 7 dígitos)
            if (!$match && !empty($celular)) {
                $last7 = $this->getPhoneLast7($celular);
                if ($last7 && isset($phoneIndex[$last7])) {
                    $match = $phoneIndex[$last7];
                }
            }

            if ($match) {
                $coincidencias[$index] = [
                    'id' => $match->id,
                    'nombre' => $match->nombre,
                    'apellido' => $match->apellido,
                    'email' => $match->email,
                    'celular' => $match->celular,
                    'fecha_nacimiento' => $match->fecha_nacimiento ? $match->fecha_nacimiento->format('Y-m-d') : null,
                    'sin_anio_nacimiento' => $match->sin_anio_nacimiento,
                    'domicilio' => $match->domicilio,
                    'localidad' => $match->localidad,
                    'provincia' => $match->provincia,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'coincidencias' => $coincidencias
        ]);
    }

    /**
     * Display a listing of circulistas that have duplicated records based on selected criteria.
     */
    public function duplicados(Request $request)
    {
        $criterio = $request->input('criterio', 'nombre_apellido');
        if (!in_array($criterio, ['nombre_apellido', 'celular', 'telefono', 'email', 'fecha_nacimiento'])) {
            $criterio = 'nombre_apellido';
        }

        // Subconsulta base para encontrar duplicados
        $subquery = DB::table('circulistas');

        switch ($criterio) {
            case 'celular':
                $subquery->selectRaw("regexp_replace(celular, '\D', '', 'g') as norm_celular, count(*) as total_repetidos")
                    ->whereNotNull('celular')
                    ->whereRaw("regexp_replace(celular, '\D', '', 'g') != ''")
                    ->groupByRaw("regexp_replace(celular, '\D', '', 'g')")
                    ->havingRaw('count(*) > 1');
                break;

            case 'telefono':
                $subquery->selectRaw("regexp_replace(telefono, '\D', '', 'g') as norm_telefono, count(*) as total_repetidos")
                    ->whereNotNull('telefono')
                    ->whereRaw("regexp_replace(telefono, '\D', '', 'g') != ''")
                    ->groupByRaw("regexp_replace(telefono, '\D', '', 'g')")
                    ->havingRaw('count(*) > 1');
                break;

            case 'email':
                $subquery->selectRaw("lower(trim(email)) as norm_email, count(*) as total_repetidos")
                    ->whereNotNull('email')
                    ->whereRaw("lower(trim(email)) != ''")
                    ->groupByRaw("lower(trim(email))")
                    ->havingRaw('count(*) > 1');
                break;

            case 'fecha_nacimiento':
                $subquery->selectRaw("fecha_nacimiento, count(*) as total_repetidos")
                    ->whereNotNull('fecha_nacimiento')
                    ->groupBy("fecha_nacimiento")
                    ->havingRaw('count(*) > 1');
                break;

            case 'nombre_apellido':
            default:
                $subquery->selectRaw('unaccent(lower(trim(apellido))) as norm_apellido, unaccent(lower(trim(nombre))) as norm_nombre, count(*) as total_repetidos')
                    ->groupByRaw('unaccent(lower(trim(apellido))), unaccent(lower(trim(nombre)))')
                    ->havingRaw('count(*) > 1');
                break;
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchClean = str_replace(',', ' ', $search);
            $words = array_filter(explode(' ', $searchClean));

            if (!empty($words)) {
                $subquery->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->where(function ($subQ) use ($word) {
                            $subQ->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhereRaw('unaccent(apellido) ilike unaccent(?)', ["%{$word}%"])
                                 ->orWhere('celular', 'like', "%{$word}%")
                                 ->orWhere('telefono', 'like', "%{$word}%")
                                 ->orWhere('email', 'like', "%{$word}%");
                        });
                    }
                });
            }
        }

        // Obtener totales para los indicadores
        $todosLosGrupos = (clone $subquery)->get();
        $totalGrupos = $todosLosGrupos->count();
        $totalRegistros = $todosLosGrupos->sum('total_repetidos');

        // Ordenar según el criterio
        switch ($criterio) {
            case 'celular':
                $subquery->orderBy('norm_celular');
                break;
            case 'telefono':
                $subquery->orderBy('norm_telefono');
                break;
            case 'email':
                $subquery->orderBy('norm_email');
                break;
            case 'fecha_nacimiento':
                $subquery->orderBy('fecha_nacimiento', 'desc');
                break;
            case 'nombre_apellido':
            default:
                $subquery->orderBy('norm_apellido')->orderBy('norm_nombre');
                break;
        }

        // Paginar los grupos repetidos
        $gruposPaginados = $subquery->paginate(10)->withQueryString();

        // Traer todos los circulistas pertenecientes a los grupos de la página actual
        $circulistasPorGrupo = collect();

        if ($gruposPaginados->count() > 0) {
            $queryCirculistas = Circulista::with('participaciones');

            $queryCirculistas->where(function ($q) use ($gruposPaginados, $criterio) {
                foreach ($gruposPaginados as $grupo) {
                    switch ($criterio) {
                        case 'celular':
                            $q->orWhereRaw("regexp_replace(celular, '\D', '', 'g') = ?", [$grupo->norm_celular]);
                            break;
                        case 'telefono':
                            $q->orWhereRaw("regexp_replace(telefono, '\D', '', 'g') = ?", [$grupo->norm_telefono]);
                            break;
                        case 'email':
                            $q->orWhereRaw("lower(trim(email)) = ?", [$grupo->norm_email]);
                            break;
                        case 'fecha_nacimiento':
                            $q->orWhere('fecha_nacimiento', $grupo->fecha_nacimiento);
                            break;
                        case 'nombre_apellido':
                        default:
                            $q->orWhere(function ($subQ) use ($grupo) {
                                $subQ->whereRaw('unaccent(lower(trim(apellido))) = ?', [$grupo->norm_apellido])
                                     ->whereRaw('unaccent(lower(trim(nombre))) = ?', [$grupo->norm_nombre]);
                            });
                            break;
                    }
                }
            });

            $registrosPagina = $queryCirculistas->orderBy('apellido')
                ->orderBy('nombre')
                ->orderBy('id', 'asc')
                ->get();

            // Agrupar los registros correspondientes
            $circulistasPorGrupo = $registrosPagina->groupBy(function ($item) use ($criterio) {
                switch ($criterio) {
                    case 'celular':
                        return preg_replace('/\D/', '', $item->celular);
                    case 'telefono':
                        return preg_replace('/\D/', '', $item->telefono);
                    case 'email':
                        return strtolower(trim($item->email));
                    case 'fecha_nacimiento':
                        return $item->fecha_nacimiento ? $item->fecha_nacimiento->format('Y-m-d') : '';
                    case 'nombre_apellido':
                    default:
                        return $this->normalizeString($item->apellido) . '|' . $this->normalizeString($item->nombre);
                }
            });
        }

        // Enriquecer cada grupo paginado para que la vista sea genérica y limpia
        $gruposPaginados->getCollection()->transform(function ($grupo) use ($criterio, $circulistasPorGrupo) {
            switch ($criterio) {
                case 'celular':
                    $key = $grupo->norm_celular;
                    $detail = 'registros con el mismo celular';
                    break;
                case 'telefono':
                    $key = $grupo->norm_telefono;
                    $detail = 'registros con el mismo teléfono';
                    break;
                case 'email':
                    $key = $grupo->norm_email;
                    $detail = 'registros con el mismo email';
                    break;
                case 'fecha_nacimiento':
                    $key = $grupo->fecha_nacimiento;
                    $detail = 'registros con la misma fecha de nacimiento';
                    break;
                case 'nombre_apellido':
                default:
                    $key = $grupo->norm_apellido . '|' . $grupo->norm_nombre;
                    $detail = 'registros con el mismo nombre y apellido';
                    break;
            }

            $registros = $circulistasPorGrupo->get($key, collect());
            $primerRegistro = $registros->first();

            switch ($criterio) {
                case 'celular':
                    $label = $primerRegistro ? 'Celular: ' . $primerRegistro->celular : $key;
                    break;
                case 'telefono':
                    $label = $primerRegistro ? 'Teléfono: ' . $primerRegistro->telefono : $key;
                    break;
                case 'email':
                    $label = $primerRegistro ? 'Email: ' . $primerRegistro->email : $key;
                    break;
                case 'fecha_nacimiento':
                    if ($primerRegistro && $primerRegistro->fecha_nacimiento) {
                        $label = 'Fecha Nacimiento: ' . ($primerRegistro->sin_anio_nacimiento 
                            ? $primerRegistro->fecha_nacimiento->format('d/m') . ' (Sin año)' 
                            : $primerRegistro->fecha_nacimiento->format('d/m/Y'));
                    } else {
                        $label = $key;
                    }
                    break;
                case 'nombre_apellido':
                default:
                    $label = $primerRegistro ? $primerRegistro->apellido . ', ' . $primerRegistro->nombre : strtoupper(str_replace('|', ' ', $key));
                    break;
            }

            $grupo->grupo_key = $key;
            $grupo->grupo_label = $label;
            $grupo->grupo_detail = $detail;
            $grupo->registros = $registros;

            return $grupo;
        });

        return view('circulistas.duplicados', compact('gruposPaginados', 'totalGrupos', 'totalRegistros', 'criterio'));
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

