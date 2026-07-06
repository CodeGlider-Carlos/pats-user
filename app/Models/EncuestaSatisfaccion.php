<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Respuesta de una encuesta de satisfacción PATS ligada a una
 * tarjeta_misional cerrada. Se almacena en la base de la aplicación.
 */
class EncuestaSatisfaccion extends Model
{
    protected $table = 'pats_encuestas_satisfaccion';

    protected $primaryKey = 'id_encuesta';

    protected $fillable = [
        'id_tarjeta',
        'code_pasaporte',
        'id_pasaporte',
        'tipo_servicio',
        'modelo',
        'estatus',
        'adm_recepcion',
        'adm_recepcion_com',
        'urgencias',
        'urgencias_com',
        'medico',
        'medico_com',
        'enfermeria',
        'enfermeria_com',
        'personal',
        'personal_com',
        'instalaciones',
        'instalaciones_com',
        'pats_explicacion',
        'pats_explicacion_com',
        'pats_descuentos',
        'pats_descuentos_com',
        'nps',
        'nps_com',
        'lo_que_mas_gusto',
        'que_mejorar',
    ];

    protected $casts = [
        'id_tarjeta' => 'integer',
        'id_pasaporte' => 'integer',
        'adm_recepcion' => 'integer',
        'urgencias' => 'integer',
        'medico' => 'integer',
        'enfermeria' => 'integer',
        'personal' => 'integer',
        'instalaciones' => 'integer',
        'pats_explicacion' => 'integer',
        'pats_descuentos' => 'integer',
        'nps' => 'integer',
    ];
}
