<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsCatCx extends Model
{
    // The table associated with the model.
    protected $table = 'pats_cat_cx';

    // The primary key associated with the table.
    protected $primaryKey = 'id_registro';

    // Indicates if the IDs are auto-incrementing.
    public $incrementing = false;

    // The "type" of the primary key ID.
    protected $keyType = 'string';

    // The attributes that are mass assignable.
    protected $fillable = [
        'id_registro',
        'id_proveedor',
        'especialidad',
        'procedimiento',
        'precio_nopats',
        'descuento',
        'precio_pats',
        'activo',
        'usuario_registro',
        'usuario_actualizo',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'precio_nopats' => 'decimal:2',
        'descuento'     => 'decimal:2',
        'precio_pats'   => 'decimal:2',
        'activo'        => 'boolean',
    ];

    /**
     * Get the provider associated with this procedure.
     */
    public function proveedor()
    {
        return $this->belongsTo(PatsCatProveedor::class, 'id_proveedor', 'id_proveedor');
    }
}
