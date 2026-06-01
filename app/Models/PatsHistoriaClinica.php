<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsHistoriaClinica extends Model
{
    protected $table = 'pats_historia_clinica';

    protected $primaryKey = 'id_historia_clinica';

    protected $fillable = [
        'id_pasaporte',
        'ocupacion',
        'estado_civil',
        'escolaridad',
        'actividad_fisica',
        'tabaquismo',
        'alcohol',
        'alimentacion',
        'heredo_familiares',
        'personales_patologicos',
        'personales_no_patologicos',
        'enfermedades_previas',
        'alergias',
        'cirugias',
        'medicamentos',
        'intolerancias',
        'peso',
        'altura',
        'imc',
    ];

    protected $casts = [
        'heredo_familiares' => 'array',
        'peso' => 'float',
        'altura' => 'float',
        'imc' => 'float',
    ];
}
