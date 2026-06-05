<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circulista extends Model
{
    protected $table = 'circulistas';

    protected $fillable = [
        'apellido',
        'nombre',
        'fecha_nacimiento',
        'sin_anio_nacimiento',
        'domicilio',
        'localidad',
        'provincia',
        'telefono',
        'celular',
        'email',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'sin_anio_nacimiento' => 'boolean',
        'activo' => 'boolean',
    ];

    public function participaciones()
    {
        return $this->hasMany(Participacion::class);
    }
}