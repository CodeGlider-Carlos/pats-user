<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use App\Models\PatsUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Completer del alta pública (checkout /adquirir): crea pasaporte + usuario,
 * confirma la orden, registra el pago y genera comisiones tras el pago Prosa.
 *
 * El pipeline es idéntico al que vivía en AdquirirController::procesar; ahora
 * se ejecuta tanto en el camino frictionless como al regresar del reto 3DS.
 */
class AdquirirCheckout implements CheckoutCompleter
{
    public const FLOW = 'adquirir';

    public function flow(): string
    {
        return self::FLOW;
    }

    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $p   = $checkout->payload ?? [];
        $ctx = $p['ctx'] ?? [];

        // Idempotencia: si la orden ya quedó pagada, no repetir.
        $orden = DB::table('pats_ordenes_pago')->where('id_orden', $p['id_orden'])->first();
        if ($orden && $orden->estatus_pago === 'CONFIRMADO') {
            return route('pasaporte');
        }

        $ahora      = Carbon::now();
        $referencia = $p['referencia'];
        $paymentId  = $paymentResult['paymentId'];
        $cardBrand  = $paymentResult['brand'] ?: 'CARD';
        $cardLast4  = $paymentResult['last4'] ?? '????';
        $frecuencia = strtoupper($p['frecuencia']);
        $monto      = (float) $p['monto_orden'];

        $idUsuario = DB::transaction(function () use ($p, $ctx, $ahora, $referencia, $paymentId, $cardBrand, $cardLast4, $frecuencia, $monto) {
            $vigencia = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonth()->toDateString();
            $vencimientoReal = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->endOfDay()
                : $ahora->copy()->addMonth()->endOfDay();

            $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
                'id_franquicia'          => $ctx['id_franquicia'],
                'id_distribuidor'        => $ctx['id_distribuidor'],
                'id_tipo_precio'         => $p['id_tipo_precio'],
                'curp'                   => strtoupper($p['curp_usuario']),
                'nombres'                => $p['nombre_usuario'],
                'apellido_pa'            => $p['apellido_pa'],
                'apellido_ma'            => $p['apellido_ma'] ?? '',
                'fecha_nacimiento'       => $p['fecha_nacimiento'],
                'telefono'               => $p['telefono_usuario'],
                'correo'                 => $p['correo'],
                'fecha_alta'             => $ahora,
                'vigencia'               => $vigencia,
                'frecuencia_pago'        => $frecuencia,
                'estatus'                => 'activo',
                'valor_pasaporte'        => $monto,
                'valor_final_pasaporte'  => $monto,
                'pais'                   => $ctx['pais'],
                'region'                 => $ctx['region'],
                'zona'                   => $ctx['zona'],
                'unidad'                 => $ctx['unidad'],
                'tipo_cliente'           => $p['tipo_cliente'],
                'nombre_empresa'         => $p['nombre_empresa'] ?? null,
                'fecha_ultimo_pago'      => $ahora,
                'fecha_vencimiento_real' => $vencimientoReal,
                'meses_vencidos'         => 0,
                'recargo_acumulado'      => 0.00,
                'activo'                 => 1,
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            $nombreCompleto = trim("{$p['nombre_usuario']} {$p['apellido_pa']} " . ($p['apellido_ma'] ?? ''));
            $idUsuario = DB::table('pats_users')->insertGetId([
                'app'                  => 'PATS',
                'rolapp'               => 'CLIENTEPATS',
                'rol'                  => 'CLIENTE',
                'tipo_actor'           => 'DISTRIBUIDOR',
                'id_actor'             => $ctx['id_distribuidor'],
                'nombre'               => $nombreCompleto,
                'usuario'              => $p['correo'],
                'correo'               => $p['correo'],
                'contrasena'           => $p['password_hash'],
                'region'               => $ctx['region'],
                'acroregion'           => $ctx['region'],
                'unidad'               => $ctx['unidad'],
                'acronu'               => $ctx['unidad'],
                'activo'               => 1,
                'must_change_password' => 0,
                'telefono'             => $p['telefono_usuario'],
                'created_at'           => $ahora,
                'updated_at'           => $ahora,
            ]);

            DB::table('pats_usuarios_scope')->insert([
                'user_id'         => $idUsuario,
                'rol_pats'        => 'CLIENTE',
                'pais'            => $ctx['pais'],
                'region'          => $ctx['region'],
                'zona'            => $ctx['zona'],
                'unidad'          => $ctx['unidad'],
                'id_distribuidor' => $ctx['id_distribuidor'],
                'id_franquicia'   => $ctx['id_franquicia'],
                'activo'          => 1,
                'created_at'      => $ahora,
                'updated_at'      => $ahora,
            ]);

            DB::table('pats_ordenes_pago')->where('id_orden', $p['id_orden'])->update([
                'id_pasaporte'                    => $idPasaporte,
                'estatus_orden'                   => 'PAGADA',
                'estatus_pago'                    => 'CONFIRMADO',
                'transaccion_id_externa'          => (string) $paymentId,
                'payment_intent_id'               => $paymentId,
                'usuario_creado'                  => 1,
                'id_usuario_generado'             => $idUsuario,
                'fecha_alta_usuario'              => $ahora,
                'pasaporte_creado'                => 1,
                'id_pasaporte_generado'           => $idPasaporte,
                'fecha_alta_pasaporte'            => $ahora,
                'procesado_integracion'           => 1,
                'fecha_procesamiento_integracion' => $ahora,
                'intentos_procesamiento'          => 1,
                'fecha_pago'                      => $ahora,
                'fecha_confirmacion'              => $ahora,
                'payload_confirmacion_json'       => json_encode($paymentResult['raw'] ?? []),
                'user_confirmo'                   => $p['correo'],
                'updated_at'                      => $ahora,
            ]);

            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden'               => $p['id_orden'],
                'id_pasaporte'           => $idPasaporte,
                'id_franquicia'          => $ctx['id_franquicia'],
                'id_distribuidor'        => $ctx['id_distribuidor'],
                'id_tipo_precio'         => $p['id_tipo_precio'],
                'correo'                 => $p['correo'],
                'curp'                   => strtoupper($p['curp_usuario']),
                'nombre_usuario'         => $p['nombre_usuario'],
                'apellido_pa'            => $p['apellido_pa'],
                'apellido_ma'            => $p['apellido_ma'] ?? null,
                'tipo_operacion'         => 'ALTA_PATS',
                'monto'                  => $monto,
                'monto_nominal_base'     => $monto,
                'monto_extra_recargo'    => 0.00,
                'frecuencia'             => strtolower($frecuencia),
                'metodo_pago'            => 'tarjeta_' . $cardBrand,
                'referencia_pago'        => $referencia,
                'referencia_externa'     => $paymentId,
                'transaccion_id_externa' => (string) $paymentId,
                'proveedor_pasarela'     => 'PROSA',
                'estatus_pago'           => 'confirmado',
                'response_json'          => json_encode($paymentResult['raw'] ?? []),
                'fecha_pago'             => $ahora,
                'fecha_confirmacion'     => $ahora,
                'moneda'                 => 'MXN',
                'observaciones'          => "Prosa Pago:{$paymentId} {$cardBrand}···{$cardLast4}",
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            $this->generarComisiones($ctx, $idPasaporte, $p['id_orden'], $monto, $frecuencia, $referencia, $paymentId, $ahora);

            return $idUsuario;
        });

        $userModel = PatsUser::find($idUsuario);
        if ($userModel) {
            Auth::login($userModel);
        }

        return route('pasaporte');
    }

    public function failUrl(ProsaPendingCheckout $checkout, string $reason): string
    {
        $p = $checkout->payload ?? [];

        if (! empty($p['id_orden'])) {
            DB::table('pats_ordenes_pago')->where('id_orden', $p['id_orden'])->update([
                'estatus_orden'     => 'FALLIDA',
                'estatus_pago'      => 'RECHAZADO',
                'error_integracion' => substr($reason, 0, 255),
                'updated_at'        => now(),
            ]);
        }

        $token = $p['token_publico'] ?? '';

        return route('adquirir', ['t' => $token]) . '&pago=error';
    }

    /**
     * @param  array<string, mixed>  $ctx
     */
    private function generarComisiones(array $ctx, int $idPasaporte, int $idOrden, float $monto, string $frecuencia, string $referencia, string $paymentId, Carbon $ahora): void
    {
        $reglas = DB::table('pats_reglas_comision')
            ->where('tipo_operacion', 'pasaporte')
            ->where('subtipo_operacion', 'membresia')
            ->where('modalidad_pago', strtolower($frecuencia))
            ->where('activo', 1)
            ->whereNull('vigencia_fin')
            ->get();

        foreach ($reglas as $regla) {
            $mc = $regla->tipo_calculo === 'monto_fijo'
                ? (float) $regla->valor_calculo
                : round($monto * (float) $regla->valor_calculo / 100, 2);

            $tipo  = match ($regla->beneficiario) { 'admin' => 'corpo', 'unidad' => 'unidad', 'franquicia' => 'franquicia', 'distribuidor' => 'distribuidor', default => 'corpo' };
            $idRel = match ($regla->beneficiario) { 'franquicia' => $ctx['id_franquicia'], 'distribuidor' => $ctx['id_distribuidor'], default => 1 };

            if (in_array($regla->beneficiario, ['franquicia', 'distribuidor'])) {
                DB::table('pats_comisiones_generadas')->insert([
                    'tipo_origen' => 'pago_pasaporte', 'id_origen' => $idOrden,
                    'id_regla' => $regla->id_regla, 'beneficiario_tipo' => $regla->beneficiario,
                    'beneficiario_id' => $idRel, 'monto_comision' => $mc,
                    'monto_aplicado_deuda' => 0, 'monto_liberado' => 0, 'moneda' => 'MXN',
                    'fecha_generacion' => $ahora, 'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }

            DB::table('pats_movimientos_financieros')->insert([
                'tipo' => $tipo, 'id_relacionado' => $idRel, 'id_pasaporte' => $idPasaporte,
                'monto' => $mc, 'tipo_movimiento' => "comision_pats_{$regla->beneficiario}",
                'referencia' => $referencia,
                'estatus' => in_array($regla->beneficiario, ['admin', 'unidad']) ? 'pagado' : 'pendiente',
                'fecha_generado' => $ahora, 'moneda' => 'MXN',
                'observaciones' => "Prosa Pago:{$paymentId} | {$regla->beneficiario}",
                'origen_tabla' => 'pats_ordenes_pago', 'origen_id' => $idOrden,
                'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }
    }
}
