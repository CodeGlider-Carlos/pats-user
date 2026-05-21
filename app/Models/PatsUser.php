<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

/**
 * Modelo de autenticación para pats_users.
 *
 * No usa migraciones propias — apunta a la tabla
 * existente en ezsystem_model.
 *
 * Campos de sesión disponibles en auth()->user():
 *   id, nombre, correo, rol, rolapp, tipo_actor,
 *   id_actor, region, unidad, activo
 */
class PatsUser extends Authenticatable
{
    protected $table      = 'pats_users';
    protected $primaryKey = 'id';

    // La contraseña en la BD se llama 'contrasena'
    protected $hidden = ['contrasena', 'remember_token'];

    protected $fillable = [
        'nombre',
        'correo',
        'usuario',
        'contrasena',
        'rol',
        'rolapp',
        'tipo_actor',
        'id_actor',
        'region',
        'acroregion',
        'unidad',
        'acronu',
        'activo',
        'app',
    ];

    protected $casts = [
        'activo'               => 'boolean',
        'must_change_password' => 'boolean',
        'last_login_at'        => 'datetime',
        'locked_until'         => 'datetime',
    ];

    // ── Auth: Laravel espera 'password', la BD tiene 'contrasena' ──

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    // ── Helpers de rol ──────────────────────────────────────

    public function esAdmin(): bool
    {
        return in_array($this->rolapp, ['ADMINPATS', 'ADMIND', 'SUPERADMIN']);
    }

    public function esFranquicia(): bool
    {
        return $this->tipo_actor === 'FRANQUICIATARIO';
    }

    public function esDistribuidor(): bool
    {
        return $this->tipo_actor === 'DISTRIBUIDOR';
    }

    public function esCliente(): bool
    {
        return $this->rolapp === 'CLIENTEPATS';
    }

    public function estaActivo(): bool
    {
        return (bool) $this->activo;
    }

    public function estaBloqueado(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until->isFuture();
    }

    // ── Registrar último login ──────────────────────────────

    public function registrarLogin(): void
    {
        DB::table('pats_users')
            ->where('id', $this->id)
            ->update([
                'last_login_at'   => now(),
                'failed_attempts' => 0,
                'locked_until'    => null,
            ]);
    }

    // ── Incrementar intentos fallidos ───────────────────────

    public function incrementarFallos(): void
    {
        $intentos = $this->failed_attempts + 1;
        $bloqueo  = $intentos >= 5 ? now()->addMinutes(30) : null;

        DB::table('pats_users')
            ->where('id', $this->id)
            ->update([
                'failed_attempts' => $intentos,
                'locked_until'    => $bloqueo,
            ]);
    }
}
