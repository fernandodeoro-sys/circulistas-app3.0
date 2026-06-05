<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participacion extends Model
{
    protected $table = 'participaciones';

    protected $fillable = [
        'circulista_id',
        'evento_id',
        'rol_id',
        'grupo',
        'observaciones'
    ];

    public function circulista()
    {
        return $this->belongsTo(Circulista::class, 'circulista_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
