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

    public function getFotoEventoUrlAttribute()
    {
        if (!$this->foto_evento) return null;
        if (str_starts_with($this->foto_evento, 'http://') || str_starts_with($this->foto_evento, 'https://')) {
            return $this->foto_evento;
        }
        $disk = config('filesystems.default', 'public');
        return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->foto_evento);
    }

    public function getFotoCocinaUrlAttribute()
    {
        if (!$this->foto_cocina) return null;
        if (str_starts_with($this->foto_cocina, 'http://') || str_starts_with($this->foto_cocina, 'https://')) {
            return $this->foto_cocina;
        }
        $disk = config('filesystems.default', 'public');
        return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->foto_cocina);
    }
}
