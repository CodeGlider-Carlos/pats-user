<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispoAgenda extends Model
{
    protected $table = 'dispo_agenda';

    protected $primaryKey = 'id_agenda';

    // id_agenda is an integer auto-increment
    public $incrementing = true;
    protected $keyType = 'int';

    // The table uses creado_en / actualizado_en instead of the Laravel defaults
    const CREATED_AT  = 'creado_en';
    const UPDATED_AT  = 'actualizado_en';

    protected $fillable = [
        'id_servicio',
        'id_recurso',
        'region',
        'unidad',
        'tipo_bloque',
        'fecha_inicio',
        'fecha_fin',
        'cupos',
        'ocupado',
        'recurrente',
        'motivo',
        'observaciones',
        'creado_por',
        'usuario',
        'activo',
    ];

    protected $casts = [
        'id_recurso'   => 'integer',
        'id_servicio'  => 'integer',
        'cupos'        => 'integer',
        'ocupado'      => 'integer',
        'recurrente'   => 'boolean',
        'activo'       => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
        'creado_por'     => 'integer',
        'creado_en'    => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    /**
     * The médico assigned to this slot.
     * id_recurso maps to pats_cats_medicos.id_medico_leadplus.
     */
    public function medico()
    {
        return $this->belongsTo(PatsCatMedico::class, 'id_recurso', 'id_medico_leadplus');
    }
}
