<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatsLoginLog extends Model
{
    protected $table = 'pats_login_logs';

    protected $fillable = [
        'id_acceso',
        'correo',
        'ip',
        'user_agent',
        'resultado',
        'motivo',
    ];

    public static function registrar(
        string $correo,
        string $resultado,
        ?int $idAcceso = null,
        ?string $motivo = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        static::create([
            'id_acceso' => $idAcceso,
            'correo' => $correo,
            'ip' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'resultado' => $resultado,
            'motivo' => $motivo,
        ]);
    }
}
