<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsCatEstudioRx extends Model
{
    protected $table = 'pats_estudios_imagen';

    protected $primaryKey = 'id_registro';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_registro',
        'id_proveedor',
        'estudio',
        'precio_nopats',
        'descuento',
        'precio_pats',
        'activo',
        'usuario_registro',
        'usuario_actualizo',
    ];

    protected $casts = [
        'precio_nopats' => 'decimal:2',
        'descuento'     => 'decimal:2',
        'precio_pats'   => 'decimal:2',
        'activo'        => 'boolean',
    ];

    // Accessor to map study name to the view's expected attribute
    public function getNombreEstudioAttribute(): string
    {
        return $this->estudio ?? '';
    }

    /**
     * Get the provider that owns the study.
     */
    public function proveedor()
    {
        return $this->belongsTo(PatsCatProveedor::class, 'id_proveedor', 'id_proveedor');
    }
}
