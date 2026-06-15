<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Circulista;
use Illuminate\Http\Request;

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
        $coincidencias = [];

        foreach ($personas as $index => $persona) {
            $nombre = trim($persona['nombre']);
            $apellido = trim($persona['apellido']);
            $celular = isset($persona['celular']) ? trim($persona['celular']) : '';

            // 1. Intentar por nombre y apellido
            $match = Circulista::whereRaw('unaccent(nombre) ilike unaccent(?)', [$nombre])
                ->whereRaw('unaccent(apellido) ilike unaccent(?)', [$apellido])
                ->first();

            // 2. Si no coincide, intentar por celular (últimos 7 dígitos)
            if (!$match && !empty($celular)) {
                $cleanCel = preg_replace('/[^\d]/', '', $celular);
                if (strlen($cleanCel) >= 7) {
                    $last7 = substr($cleanCel, -7);
                    $match = Circulista::where(function($q) use ($last7) {
                        $q->whereRaw("regexp_replace(celular, '[^0-9]', '', 'g') LIKE ?", ['%' . $last7])
                          ->orWhereRaw("regexp_replace(telefono, '[^0-9]', '', 'g') LIKE ?", ['%' . $last7]);
                    })->first();
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
}
