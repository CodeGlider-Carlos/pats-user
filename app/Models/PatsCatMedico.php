<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatsCatMedico extends Model
{
    protected $table = 'pats_cats_medicos';
    protected $primaryKey = 'id_registro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_registro',
        'id_medico_leadplus',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'nombre_completo',
        'especialidad',
        'cedula_mg',
        'cedula_esp',
        'telefono',
        'email',
        'region',
        'unidad',
        'redes_json',
        'activo',
        'usuario_registro',
        'usuario_actualizo'
    ];

    protected $casts = [
        'id_medico_leadplus' => 'integer',
        'activo' => 'boolean',
    ];

    // Accessors to maintain compatibility with existing views and controllers
    public function getIdRecursoAttribute()
    {
        return $this->id_medico_leadplus;
    }

    public function getNombreRecursoAttribute()
    {
        return $this->nombre_completo;
    }

    public function getCapacidadAttribute()
    {
        return 1; // Default backwards compatibility
    }
}
