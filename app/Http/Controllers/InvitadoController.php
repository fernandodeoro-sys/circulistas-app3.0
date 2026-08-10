<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;

class InvitadoController extends Controller
{
    /**
     * Muestra la ventana de búsqueda e información en Modo Invitado.
     */
    public function index(Request $request)
    {
        $tiposEvento = TipoEvento::orderBy('nombre')->get();
        $evento = null;
        $isEslabon = false;

        $tipoEventoId = $request->input('tipo_evento_id');
        $numeroEvento = $request->input('numero_evento');

        if ($tipoEventoId && $numeroEvento) {
            $evento = Evento::with(['tipoEvento', 'participaciones.circulista', 'participaciones.rol'])
                ->where('tipo_evento_id', $tipoEventoId)
                ->where('numero_evento', $numeroEvento)
                ->first();

            if ($evento && $evento->tipoEvento) {
                $nombreTipo = strtolower($evento->tipoEvento->nombre);
                // Verificamos si es retiros de Eslabón (ej. "Eslabón", "Jornada Eslabón")
                if (str_contains($nombreTipo, 'eslab')) {
                    $isEslabon = true;
                }
            }
        }

        return view('invitado.consulta', compact('tiposEvento', 'evento', 'isEslabon', 'tipoEventoId', 'numeroEvento'));
    }

    /**
     * Muestra la circular de retiro imprimible para invitados.
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
     * Muestra la circular de cocina para invitados (con restricción para Eslabón).
     */
    public function circularCocina(string $id)
    {
        $evento = Evento::with(['tipoEvento', 'participaciones.circulista', 'participaciones.rol'])->findOrFail($id);

        if ($evento->tipoEvento && str_contains(strtolower($evento->tipoEvento->nombre), 'eslab')) {
            abort(403, 'La circular de cocina no está disponible para eventos de Eslabón.');
        }

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
}
