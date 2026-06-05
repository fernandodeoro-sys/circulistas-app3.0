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
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                  ->orWhere('apellido', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('localidad', 'ilike', "%{$search}%")
                  ->orWhere('provincia', 'ilike', "%{$search}%");
            });
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

        // Evitar duplicados por nombre y apellido
        $existeDuplicado = Circulista::where('nombre', 'ilike', trim($validated['nombre']))
            ->where('apellido', 'ilike', trim($validated['apellido']))
            ->exists();

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Ya existe un circulista registrado con ese nombre y apellido.'
                ]);
        }

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
        $nombre = trim($request->input('nombre'));
        $apellido = trim($request->input('apellido'));
        $ignoreId = $request->input('ignore_id');

        if (empty($nombre) || empty($apellido)) {
            return response()->json(['existe' => false]);
        }

        $query = Circulista::where('nombre', 'ilike', $nombre)
            ->where('apellido', 'ilike', $apellido);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $duplicado = $query->first();

        if ($duplicado) {
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

        // Evitar duplicados por nombre y apellido (excluyendo el actual)
        $existeDuplicado = Circulista::where('nombre', 'ilike', trim($validated['nombre']))
            ->where('apellido', 'ilike', trim($validated['apellido']))
            ->where('id', '!=', $id)
            ->exists();

        if ($existeDuplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'nombre' => 'Ya existe otro circulista registrado con ese nombre y apellido.'
                ]);
        }

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
}
