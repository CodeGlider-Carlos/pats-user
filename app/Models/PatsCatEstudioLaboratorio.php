<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsCatEstudioLaboratorio extends Model
{
    // The table associated with the model.
    protected $table = 'pats_cat_estudiosLaboratorio';

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
        'estudio',
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

    // Accessor to map study name to the view's expected attribute
    public function getNombreEstudioAttribute()
    {
        return $this->estudio;
    }

    // Dynamic preparacion_resumen mapping to support premium view features cleanly
    public function getPreparacionResumenAttribute()
    {
        $estudioLower = strtolower($this->estudio);
        if (str_contains($estudioLower, 'biometría') || str_contains($estudioLower, 'química') || str_contains($estudioLower, 'glucosa') || str_contains($estudioLower, 'hepático')) {
            return 'Ayuno de 8 horas';
        }
        if (str_contains($estudioLower, 'lípidos')) {
            return 'Ayuno de 12 horas';
        }
        if (str_contains($estudioLower, 'examen general') || str_contains($estudioLower, 'orina')) {
            return 'Primera orina del día';
        }
        if (str_contains($estudioLower, 'prostático')) {
            return 'Sin contacto sexual 48 h antes';
        }
        if (str_contains($estudioLower, 'ácido')) {
            return 'Ayuno de 4 horas';
        }
        return null;
    }

    // Dynamic duracion_min mapping to support premium view features cleanly
    public function getDuracionMinAttribute()
    {
        $estudioLower = strtolower($this->estudio);
        if (str_contains($estudioLower, 'perfil') || str_contains($estudioLower, 'lípidos') || str_contains($estudioLower, 'hepático')) {
            return 20;
        }
        if (str_contains($estudioLower, 'biometría') || str_contains($estudioLower, 'química') || str_contains($estudioLower, 'cultivo')) {
            return 15;
        }
        return 10;
    }

    // Dynamic requiere_cita mapping to support premium view features cleanly
    public function getRequiereCitaAttribute()
    {
        $estudioLower = strtolower($this->estudio);
        if (str_contains($estudioLower, 'cultivo')) {
            return true;
        }
        return false;
    }

    /**
     * Get the provider that owns the study.
     */
    public function proveedor()
    {
        return $this->belongsTo(PatsCatProveedor::class, 'id_proveedor', 'id_proveedor');
    }
}

