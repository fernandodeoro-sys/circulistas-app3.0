<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function participaciones()
    {
        return $this->hasMany(Participacion::class, 'rol_id');
    }
}
