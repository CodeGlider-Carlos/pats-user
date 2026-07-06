<?php

namespace App\Models;

use App\Support\EncuestaPats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Tarjeta misional (episodio de atención) del modelo de operación.
 *
 * Vive en la base `dcommx1_ezsystem_modelpats`, por eso usa la conexión
 * `modelpats`. Cuando `activo = 0` la atención terminó y, si aún no tiene
 * reseña, dispara la encuesta de satisfacción.
 */
class TarjetaMisional extends Model
{
    protected $connection = 'modelpats';

    protected $table = 'tarjetas_misional';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    // La tabla legada no maneja created_at / updated_at de Laravel.
    public $timestamps = false;

    /**
     * Valores de la columna `reviewed` (agregada para la encuesta).
     */
    public const REVIEW_DONE = 'completada';
    public const REVIEW_REJECTED = 'rechazada';

    protected $fillable = [
        'reviewed',
    ];

    protected $casts = [
        'activo' => 'integer',
    ];

    /**
     * Tarjetas cerradas (activo = 0) de un pasaporte que aún no tienen reseña.
     */
    public function scopePendientes(Builder $query, string $codePasaporte): Builder
    {
        return $query->where('code_pasaporte', $codePasaporte)
            ->where('activo', 0)
            ->whereNull('reviewed');
    }

    /**
     * Tipo de servicio derivado de la columna `modelo`.
     */
    public function getTipoServicioAttribute(): string
    {
        return EncuestaPats::clasificarServicio($this->modelo);
    }
}
