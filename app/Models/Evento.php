<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'tipo_evento_id',
        'numero_evento',
        'lugar',
        'fecha_inicio',
        'fecha_fin',
        'foto_evento',
        'foto_cocina',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function participaciones()
    {
        return $this->hasMany(Participacion::class, 'evento_id');
    }

    public function circulistas()
    {
        return $this->belongsToMany(Circulista::class, 'participaciones', 'evento_id', 'circulista_id')
            ->withPivot('id', 'rol_id', 'grupo', 'observaciones')
            ->withTimestamps();
    }
}
