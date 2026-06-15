<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaPatsDemo extends Model
{
    protected $table = 'agenda_pats_demo';

    protected $primaryKey = 'id_agenda';

    public $incrementing = true;

    protected $keyType = 'int';

    // La tabla usa creado_en / actualizado_en en lugar de los nombres por defecto
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Estatus que marca una cita como completada y, por tanto, lista para reseña.
     */
    public const ESTATUS_COMPLETADO = 'COMPLETADO';

    /**
     * Valores posibles de la columna reviewed.
     */
    public const REVIEW_DONE = 'reviewed';
    public const REVIEW_REJECTED = 'rejected';

    protected $fillable = [
        'reviewed',
        'review_value',
        'review_comment',
    ];

    protected $casts = [
        'review_value'     => 'integer',
        'confirmado'       => 'boolean',
        'activo'           => 'boolean',
        'fecha_programada' => 'date',
        'creado_en'        => 'datetime',
        'actualizado_en'   => 'datetime',
    ];

    /**
     * El médico asignado a la cita.
     * id_recurso mapea a pats_cats_medicos.id_medico_leadplus.
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(PatsCatMedico::class, 'id_recurso', 'id_medico_leadplus');
    }

    /**
     * Citas completadas de un paciente (por CURP) que aún no tienen reseña.
     */
    public function scopePendienteResena(Builder $query, string $curp): Builder
    {
        return $query->where('curp', $curp)
            ->where('estatus', self::ESTATUS_COMPLETADO)
            ->whereNull('reviewed');
    }
}
