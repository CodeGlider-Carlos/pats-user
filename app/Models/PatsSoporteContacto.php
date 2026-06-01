<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsSoporteContacto extends Model
{
    protected $table = 'pats_soporte_contacto';

    protected $primaryKey = 'id_soporte';

    protected $fillable = [
        'nombre',
        'correo',
        'mensaje',
    ];
}
