<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Crea un pasaporte PATS de ejemplo con su usuario, orden de pago,
 * pago histórico, comisiones y movimientos financieros.
 *
 * Ejecutar con:
 *   php artisan db:seed --class=PatsEjemploSeeder
 *
 * Datos del pasaporte de prueba:
 *   Titular:  Juan Carlos Pérez López
 *   CURP:     PELJ900315HJCRPN01
 *   Correo:   juancarlos@ejemplo.com
 *   Plan:     Mensual $800 MXN
 *   Pasarela: FEENICIA
 */
class PatsEjemploSeeder extends Seeder
{
    private const CURP       = 'PELJ900315HJCRPN01';
    private const CORREO     = 'juancarlos@ejemplo.com';
    private const NOMBRES    = 'Juan Carlos';
    private const APE_PA     = 'Pérez';
    private const APE_MA     = 'López';
    private const TELEFONO   = '3312345678';
    private const PASSWORD   = 'Pats2026*';

    private const ID_FRANQUICIA   = 1;
    private const ID_DISTRIBUIDOR = 1;
    private const ID_TIPO_PRECIO  = 2; // mensual $800

    private const TRANSACTION_ID = '9627841';
    private const AUTHNUM        = '577054';
    private const MONTO          = 800.00;
    private const FRECUENCIA     = 'mensual';

    public function run(): void
    {
        $ahora      = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-EJEMPLO';
        $folio      = 'ORD-' . $ahora->format('Ymd') . '-EJEMPLO';

        $this->command->info('');
        $this->command->info('Creando PATS de ejemplo con Feenicia...');

        DB::transaction(function () use ($ahora, $referencia, $folio) {

            // ── 1. Pasaporte ─────────────────────────────────
            $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
                'id_franquicia'          => self::ID_FRANQUICIA,
                'id_distribuidor'        => self::ID_DISTRIBUIDOR,
                'id_tipo_precio'         => self::ID_TIPO_PRECIO,
                'curp'                   => self::CURP,
                'nombres'                => self::NOMBRES,
                'apellido_pa'            => self::APE_PA,
                'apellido_ma'            => self::APE_MA,
                'fecha_nacimiento'       => '1990-03-15',
                'telefono'               => self::TELEFONO,
                'correo'                 => self::CORREO,
                'fecha_alta'             => $ahora,
                'vigencia'               => $ahora->copy()->addMonth()->toDateString(),
                'frecuencia_pago'        => 'MENSUAL',
                'estatus'                => 'activo',
                'valor_pasaporte'        => self::MONTO,
                'valor_final_pasaporte'  => self::MONTO,
                'pais'                   => 'México',
                'region'                 => 'JAL',
                'zona'                   => 'GUADALAJARA',
                'unidad'                 => 'ZR',
                'tipo_cliente'           => 'privado',
                'fecha_ultimo_pago'      => $ahora,
                'fecha_vencimiento_real' => $ahora->copy()->addMonth()->endOfDay(),
                'meses_vencidos'         => 0,
                'recargo_acumulado'      => 0.00,
                'activo'                 => 1,
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            $this->command->info("  ✓ Pasaporte ID: {$idPasaporte}");

            // ── 2. Orden de pago ─────────────────────────────
            $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
                'folio_orden'                     => $folio,
                'referencia_pago'                 => $referencia,
                'tipo_origen'                     => 'DISTRIBUIDOR',
                'origen_checkout'                 => 'PORTAL_PUBLICO',
                'id_distribuidor'                 => self::ID_DISTRIBUIDOR,
                'id_franquicia'                   => self::ID_FRANQUICIA,
                'id_pasaporte'                    => $idPasaporte,
                'correo_usuario_pats'             => self::CORREO,
                'curp_usuario'                    => self::CURP,
                'nombre_usuario'                  => self::NOMBRES,
                'apellido_pa'                     => self::APE_PA,
                'apellido_ma'                     => self::APE_MA,
                'fecha_nacimiento'                => '1990-03-15',
                'telefono_usuario'                => self::TELEFONO,
                'id_tipo_precio'                  => self::ID_TIPO_PRECIO,
                'tipo_operacion'                  => 'ALTA_PATS',
                'frecuencia'                      => 'MENSUAL',
                'monto_orden'                     => self::MONTO,
                'monto_nominal_base'              => self::MONTO,
                'monto_extra_recargo'             => 0.00,
                'moneda'                          => 'MXN',
                'pais'                            => 'México',
                'region'                          => 'JAL',
                'zona'                            => 'GUADALAJARA',
                'unidad'                          => 'ZR',
                'tipo_cliente'                    => 'privado',
                'estatus_orden'                   => 'PAGADA',
                'estatus_pago'                    => 'CONFIRMADO',
                'proveedor_pasarela'              => 'FEENICIA',
                'transaccion_id_externa'          => self::TRANSACTION_ID,
                'payment_intent_id'               => self::AUTHNUM,
                'usuario_creado'                  => 0,
                'pasaporte_creado'                => 1,
                'id_pasaporte_generado'           => $idPasaporte,
                'fecha_alta_pasaporte'            => $ahora,
                'procesado_integracion'           => 1,
                'fecha_procesamiento_integracion' => $ahora,
                'intentos_procesamiento'          => 1,
                'fecha_orden'                     => $ahora,
                'fecha_pago'                      => $ahora,
                'fecha_confirmacion'              => $ahora,
                'created_at'                      => $ahora,
                'updated_at'                      => $ahora,
            ]);

            $this->command->info("  ✓ Orden ID: {$idOrden} | Folio: {$folio}");

            // ── 3. Pago histórico ────────────────────────────
            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden'               => $idOrden,
                'id_pasaporte'           => $idPasaporte,
                'id_franquicia'          => self::ID_FRANQUICIA,
                'id_distribuidor'        => self::ID_DISTRIBUIDOR,
                'id_tipo_precio'         => self::ID_TIPO_PRECIO,
                'correo'                 => self::CORREO,
                'curp'                   => self::CURP,
                'nombre_usuario'         => self::NOMBRES,
                'apellido_pa'            => self::APE_PA,
                'apellido_ma'            => self::APE_MA,
                'tipo_operacion'         => 'ALTA_PATS',
                'monto'                  => self::MONTO,
                'monto_nominal_base'     => self::MONTO,
                'monto_extra_recargo'    => 0.00,
                'frecuencia'             => 'mensual',
                'metodo_pago'            => 'tarjeta_MasterCard',
                'referencia_pago'        => $referencia,
                'referencia_externa'     => self::AUTHNUM,
                'transaccion_id_externa' => self::TRANSACTION_ID,
                'proveedor_pasarela'     => 'FEENICIA',
                'estatus_pago'           => 'confirmado',
                'response_json'          => json_encode([
                    'transactionId' => self::TRANSACTION_ID,
                    'authnum'       => self::AUTHNUM,
                    'responseCode'  => '00',
                    'approved'      => true,
                    'amount'        => self::MONTO,
                    'card'          => [
                        'brand'        => 'MasterCard',
                        'product'      => 'DEBIT',
                        'last4Digits'  => '3004',
                        'first6Digits' => '543924',
                    ],
                    'proveedor' => 'FEENICIA',
                ]),
                'fecha_pago'             => $ahora,
                'fecha_confirmacion'     => $ahora,
                'moneda'                 => 'MXN',
                'observaciones'          => 'Pago procesado vía Feenicia OneStepSale',
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            $this->command->info('  ✓ Pago histórico registrado');

            // ── 4. Comisiones y movimientos financieros ──────
            // Lee las reglas activas de la BD — sin montos hardcodeados
            $reglas = DB::table('pats_reglas_comision')
                ->where('tipo_operacion', 'pasaporte')
                ->where('subtipo_operacion', 'membresia')
                ->where('modalidad_pago', self::FRECUENCIA)
                ->where('activo', 1)
                ->whereNull('vigencia_fin')
                ->orderBy('orden_aplicacion')
                ->get();

            foreach ($reglas as $regla) {

                $monto = $regla->tipo_calculo === 'monto_fijo'
                    ? (float) $regla->valor_calculo
                    : round(self::MONTO * (float) $regla->valor_calculo / 100, 2);

                // Mapeo beneficiario → tipo de movimiento (igual que datos existentes en BD)
                $tipoMovimiento = match($regla->beneficiario) {
                    'admin'       => 'comision_pats_admin',
                    'unidad'      => 'ingreso_pats_unidad',
                    'franquicia'  => 'comision_pats_franquicia',
                    'distribuidor'=> 'comision_pats_distribuidor',
                    default       => 'pago_pats_confirmado',
                };

                // tipo e id_relacionado según la BD real
                $tipo        = match($regla->beneficiario) {
                    'admin'       => 'corpo',
                    'unidad'      => 'unidad',
                    'franquicia'  => 'franquicia',
                    'distribuidor'=> 'distribuidor',
                    default       => 'corpo',
                };

                $idRelacionado = match($regla->beneficiario) {
                    'franquicia'  => self::ID_FRANQUICIA,
                    'distribuidor'=> self::ID_DISTRIBUIDOR,
                    default       => 1,
                };

                // Franquicia y distribuidor → también en pats_comisiones_generadas
                if (in_array($regla->beneficiario, ['franquicia', 'distribuidor'])) {
                    DB::table('pats_comisiones_generadas')->insert([
                        'tipo_origen'          => 'pago_pasaporte',
                        'id_origen'            => $idOrden,
                        'id_regla'             => $regla->id_regla,
                        'beneficiario_tipo'    => $regla->beneficiario,
                        'beneficiario_id'      => $idRelacionado,
                        'monto_comision'       => $monto,
                        'monto_aplicado_deuda' => 0.00,
                        'monto_liberado'       => 0.00,
                        'moneda'               => 'MXN',
                        'fecha_generacion'     => $ahora,
                        'created_at'           => $ahora,
                        'updated_at'           => $ahora,
                    ]);
                }

                // Movimiento financiero para todos los actores
                DB::table('pats_movimientos_financieros')->insert([
                    'tipo'            => $tipo,
                    'id_relacionado'  => $idRelacionado,
                    'id_pasaporte'    => $idPasaporte,
                    'monto'           => $monto,
                    'tipo_movimiento' => $tipoMovimiento,
                    'referencia'      => $referencia,
                    'estatus'         => in_array($regla->beneficiario, ['admin', 'unidad'])
                                        ? 'pagado' : 'pendiente',
                    'fecha_generado'  => $ahora,
                    'pais'            => 'México',
                    'region'          => 'JAL',
                    'zona'            => 'GUADALAJARA',
                    'unidad'          => 'ZR',
                    'moneda'          => 'MXN',
                    'observaciones'   => "Comisión {$regla->beneficiario} por pago PATS {$referencia}",
                    'origen_tabla'    => 'pats_ordenes_pago',
                    'origen_id'       => $idOrden,
                    'created_at'      => $ahora,
                    'updated_at'      => $ahora,
                ]);

                $this->command->info("  ✓ {$regla->beneficiario}: \${$monto}");
            }

            // Movimiento del pago total confirmado (corpo)
            DB::table('pats_movimientos_financieros')->insert([
                'tipo'            => 'corpo',
                'id_relacionado'  => $idOrden,
                'id_pasaporte'    => $idPasaporte,
                'monto'           => self::MONTO,
                'tipo_movimiento' => 'pago_pats_confirmado',
                'referencia'      => $referencia,
                'estatus'         => 'pagado',
                'fecha_generado'  => $ahora,
                'pais'            => 'México',
                'region'          => 'JAL',
                'zona'            => 'GUADALAJARA',
                'unidad'          => 'ZR',
                'moneda'          => 'MXN',
                'observaciones'   => "Pago PATS confirmado {$referencia}",
                'origen_tabla'    => 'pats_ordenes_pago',
                'origen_id'       => $idOrden,
                'created_at'      => $ahora,
                'updated_at'      => $ahora,
            ]);

            // ── 5. Usuario del portal PATS ───────────────────
            $existeUser = DB::table('pats_users')
                ->where('correo', self::CORREO)
                ->exists();

            if (!$existeUser) {
                $idUser = DB::table('pats_users')->insertGetId([
                    'app'                  => 'PATS',
                    'rolapp'               => 'CLIENTEPATS',
                    'rol'                  => 'CLIENTE',
                    'tipo_actor'           => 'DISTRIBUIDOR',
                    'id_actor'             => self::ID_DISTRIBUIDOR,
                    'nombre'               => self::NOMBRES . ' ' . self::APE_PA . ' ' . self::APE_MA,
                    'usuario'              => self::CORREO,
                    'correo'               => self::CORREO,
                    'contrasena'           => Hash::make(self::PASSWORD),
                    'region'               => 'JAL',
                    'acroregion'           => 'JAL',
                    'unidad'               => 'ZR',
                    'acronu'               => 'ZR',
                    'activo'               => 1,
                    'must_change_password' => 0,
                    'telefono'             => self::TELEFONO,
                    'created_at'           => $ahora,
                    'updated_at'           => $ahora,
                ]);

                DB::table('pats_usuarios_scope')->insert([
                    'user_id'         => $idUser,
                    'rol_pats'        => 'CLIENTE',
                    'pais'            => 'México',
                    'region'          => 'JAL',
                    'zona'            => 'GUADALAJARA',
                    'unidad'          => 'ZR',
                    'id_distribuidor' => self::ID_DISTRIBUIDOR,
                    'id_franquicia'   => self::ID_FRANQUICIA,
                    'activo'          => 1,
                    'created_at'      => $ahora,
                    'updated_at'      => $ahora,
                ]);

                $this->command->info("  ✓ Usuario ID: {$idUser}");
            } else {
                $this->command->warn('  ! Usuario ya existe, omitiendo');
            }

            // ── Resumen ──────────────────────────────────────
            $this->command->info('');
            $this->command->info('══════════════════════════════════════════════');
            $this->command->info('  ✅  PATS de ejemplo creado exitosamente');
            $this->command->info('══════════════════════════════════════════════');
            $this->command->info("  Pasaporte ID : {$idPasaporte}");
            $this->command->info("  Orden        : {$folio}");
            $this->command->info("  Referencia   : {$referencia}");
            $this->command->info("  Correo       : " . self::CORREO);
            $this->command->info("  Password     : " . self::PASSWORD);
            $this->command->info("  CURP         : " . self::CURP);
            $this->command->info("  Monto        : $" . self::MONTO . " MXN (mensual)");
            $this->command->info("  Vigencia     : " . $ahora->copy()->addMonth()->toDateString());
            $this->command->info("  Pasarela     : FEENICIA");
            $this->command->info('══════════════════════════════════════════════');
            $this->command->info('');
            $this->command->info('  Comisiones generadas:');
            $this->command->info('  Admin        → $200 (movimiento financiero)');
            $this->command->info('  Unidad       → $500 (movimiento financiero)');
            $this->command->info('  Franquicia   → $20  (comisión + movimiento)');
            $this->command->info('  Distribuidor → $80  (comisión + movimiento)');
            $this->command->info('══════════════════════════════════════════════');
        });
    }
}