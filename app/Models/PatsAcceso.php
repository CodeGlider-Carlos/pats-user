<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

/**
 * Modelo de autenticación para pats_pasaporte_accesos.
 *
 * Campos disponibles en auth('pasaporte')->user():
 *   id_acceso, id_pasaporte, id_alta, id_orden,
 *   tipo_acceso, correo_usuario, telefono_usuario,
 *   nombre_usuario, nombre_paciente, estatus, activo
 */
class PatsAcceso extends Authenticatable
{
    protected $table      = 'pats_pasaporte_accesos';
    protected $primaryKey = 'id_acceso';

    protected $hidden = ['password_hash', 'remember_token', 'token_reset'];

    protected $fillable = [
        'id_pasaporte',
        'id_alta',
        'id_orden',
        'tipo_acceso',
        'correo_usuario',
        'telefono_usuario',
        'nombre_usuario',
        'nombre_paciente',
        'password_hash',
        'password_temporal',
        'debe_cambiar_password',
        'token_reset',
        'token_reset_expira',
        'estatus',
        'activo',
    ];

    protected $casts = [
        'activo'               => 'boolean',
        'password_temporal'    => 'boolean',
        'debe_cambiar_password'=> 'boolean',
        'ultimo_login'         => 'datetime',
        'bloqueado_hasta'      => 'datetime',
        'token_reset_expira'   => 'datetime',
    ];

    // ── Auth: Laravel espera 'password', la BD tiene 'password_hash' ──

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ── Helpers de estado ──────────────────────────────────

    public function estaActivo(): bool
    {
        return (bool) $this->activo && strtoupper($this->estatus ?? '') === 'ACTIVO';
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta !== null
            && $this->bloqueado_hasta->isFuture();
    }

    public function debecambiarPassword(): bool
    {
        return (bool) $this->debe_cambiar_password;
    }

    // ── Registrar último login ──────────────────────────────

    public function registrarLogin(): void
    {
        DB::table('pats_pasaporte_accesos')
            ->where('id_acceso', $this->id_acceso)
            ->update([
                'ultimo_login'      => now(),
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'updated_at'        => now(),
            ]);
    }

    // ── Incrementar intentos fallidos (bloqueo a los 5) ────

    public function incrementarFallos(): void
    {
        $intentos = (int) $this->intentos_fallidos + 1;
        $bloqueo  = $intentos >= 5 ? now()->addMinutes(30) : null;

        DB::table('pats_pasaporte_accesos')
            ->where('id_acceso', $this->id_acceso)
            ->update([
                'intentos_fallidos' => $intentos,
                'bloqueado_hasta'   => $bloqueo,
                'updated_at'        => now(),
            ]);
    }
}
