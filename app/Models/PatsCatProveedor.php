<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsCatProveedor extends Model
{
    // The table associated with the model.
    protected $table = 'pats_cat_proveedores';

    // The primary key associated with the table.
    protected $primaryKey = 'id_registro';

    // Indicates if the IDs are auto-incrementing.
    public $incrementing = false;

    // The "type" of the primary key ID.
    protected $keyType = 'string';

    // The attributes that are mass assignable.
    protected $fillable = [
        'pais',
        'region',
        'id_proveedor',
        'id_registro',
        'categoria',
        'nombre_unidad',
        'telefono',
        'direccion',
        'activo',
        'usuario_registro',
        'usuario_actualizo',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Get the studies associated with this provider.
     */
    public function estudiosLaboratorio()
    {
        return $this->hasMany(PatsCatEstudioLaboratorio::class, 'id_proveedor', 'id_proveedor');
    }
}
