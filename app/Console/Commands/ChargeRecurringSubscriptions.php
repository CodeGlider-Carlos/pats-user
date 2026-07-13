<?php

namespace App\Console\Commands;

use App\Exceptions\Prosa\ProsaException;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Models\ProsaSubscription;
use App\Models\ProsaTransaction;
use App\Services\Prosa\RecurringService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Ejecuta los cobros recurrentes que están vencidos.
 *
 * Uso:
 *   php artisan prosa:charge-recurring
 *   php artisan prosa:charge-recurring --dry-run     (sólo muestra qué cobraría)
 *   php artisan prosa:charge-recurring --id=42        (sólo una suscripción)
 */
class ChargeRecurringSubscriptions extends Command
{
    protected $signature = 'prosa:charge-recurring
                            {--dry-run : Lista las suscripciones sin cobrar}
                            {--id=     : Procesar sólo la suscripción con este ID}';

    protected $description = 'Cobra las suscripciones recurrentes PROSA vencidas';

    /** Número de fallos consecutivos antes de suspender la suscripción. */
    private const MAX_FAILURES = 3;

    public function __construct(
        private readonly RecurringService $recurringService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $onlyId = $this->option('id');

        $query = ProsaSubscription::due()->with([]);

        if ($onlyId) {
            $query->where('id', (int) $onlyId);
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No hay suscripciones vencidas.');

            return self::SUCCESS;
        }

        $this->info("Suscripciones a procesar: {$subscriptions->count()}");
        $this->newLine();

        $charged = 0;
        $failed  = 0;

        foreach ($subscriptions as $sub) {
            $label = "Sub #{$sub->id} — {$sub->correo_usuario} — {$sub->card_brand} ···· {$sub->card_last4} — \${$sub->amount} MXN";

            if ($dryRun) {
                $this->line("  [DRY] {$label}");
                continue;
            }

            $this->line("  Cobrando: {$label}");

            try {
                $this->chargeOne($sub);
                $this->info("    ✓ Aprobado");
                $charged++;
            } catch (Throwable $e) {
                $this->error("    ✗ Error: {$e->getMessage()}");
                $failed++;
            }
        }

        if (! $dryRun) {
            $this->newLine();
            $this->info("Resultado: {$charged} aprobados / {$failed} fallidos.");
        }

        return self::SUCCESS;
    }

    /**
     * Cobra una suscripción y actualiza el pasaporte + registros.
     *
     * @throws ProsaException|ProsaTimeoutException|Throwable
     */
    private function chargeOne(ProsaSubscription $sub): void
    {
        $ahora   = Carbon::now();
        $mtx     = 'PATS' . $ahora->format('YmdHis') . strtoupper(substr(md5($sub->id . $ahora->timestamp), 0, 8));

        // ── Cobrar via OPPWA ──────────────────────────────────────────────────
        try {
            $result = $this->recurringService->chargeRecurring(
                registrationId:        $sub->registration_id,
                amount:                (float) $sub->amount,
                currency:              $sub->currency,
                merchantTransactionId: $mtx,
            );
        } catch (ProsaException $e) {
            $this->handleFailure($sub, $e->getMessage(), $e->resultCode);
            throw $e;
        } catch (ProsaTimeoutException $e) {
            $this->handleFailure($sub, 'Timeout al cobrar', 'TIMEOUT');
            throw $e;
        }

        // ── Actualizar pasaporte y registros ──────────────────────────────────
        DB::transaction(function () use ($sub, $result, $ahora, $mtx) {
            $paymentId  = $result['paymentId'];
            $frecuencia = $sub->frecuencia;
            $meses      = $sub->meses;

            $vigencia = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonths($meses)->toDateString();
            $vencReal = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->endOfDay()
                : $ahora->copy()->addMonths($meses)->endOfDay();

            $pasaporte = DB::table('pats_pasaportes')
                ->where('correo', $sub->correo_usuario)
                ->where('activo', 1)
                ->orderBy('fecha_alta', 'desc')
                ->first();

            if ($pasaporte) {
                DB::table('pats_pasaportes')
                    ->where('id_pasaporte', $pasaporte->id_pasaporte)
                    ->update([
                        'vigencia'               => $vigencia,
                        'frecuencia_pago'        => $frecuencia,
                        'estatus'                => 'activo',
                        'fecha_ultimo_pago'      => $ahora,
                        'fecha_vencimiento_real' => $vencReal,
                        'meses_vencidos'         => 0,
                        'recargo_acumulado'      => 0.00,
                        'updated_at'             => $ahora,
                    ]);

                DB::table('pats_pagos_pasaporte')->insert([
                    'id_pasaporte'           => $pasaporte->id_pasaporte,
                    'id_franquicia'          => $pasaporte->id_franquicia   ?? 1,
                    'id_distribuidor'        => $pasaporte->id_distribuidor ?? 1,
                    'id_tipo_precio'         => $sub->id_tipo_precio,
                    'correo'                 => $sub->correo_usuario,
                    'tipo_operacion'         => 'RENOVACION_PATS',
                    'monto'                  => $sub->amount,
                    'monto_nominal_base'     => $sub->amount,
                    'monto_extra_recargo'    => 0.00,
                    'frecuencia'             => strtolower($frecuencia),
                    'metodo_pago'            => 'tarjeta_recurrente_' . $sub->card_brand,
                    'referencia_pago'        => $mtx,
                    'referencia_externa'     => $paymentId,
                    'transaccion_id_externa' => (string) $paymentId,
                    'proveedor_pasarela'     => 'PROSA',
                    'estatus_pago'           => 'confirmado',
                    'fecha_pago'             => $ahora,
                    'fecha_confirmacion'     => $ahora,
                    'moneda'                 => $sub->currency,
                    'observaciones'          => "Recurrente auto:{$paymentId} {$sub->card_brand}···{$sub->card_last4}",
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora,
                ]);
            } else {
                Log::warning('prosa:charge-recurring — pasaporte no encontrado', [
                    'subscription_id' => $sub->id,
                    'correo'          => $sub->correo_usuario,
                    'payment_id'      => $paymentId,
                ]);
            }

            ProsaTransaction::create([
                'user_id'         => $sub->user_id,
                'registration_id' => $sub->registration_id,
                'payment_id'      => $paymentId,
                'payment_type'    => 'DB',
                'amount'          => $sub->amount,
                'currency'        => $sub->currency,
                'result_code'     => $result['resultCode'],
                'brand'           => $sub->card_brand,
                'last4'           => $sub->card_last4,
                'status'          => 'approved',
                'origen'          => 'recurring_auto',
                'raw_response'    => $result['raw'],
            ]);

            // ── Avanzar la suscripción ────────────────────────────────────────
            $sub->update([
                'failure_count'   => 0,
                'last_charged_at' => $ahora,
                'next_charge_at'  => $sub->calculateNextChargeAt(),
            ]);
        });

        Log::info('prosa:charge-recurring — cobro exitoso', [
            'subscription_id' => $sub->id,
            'correo'          => $sub->correo_usuario,
            'amount'          => $sub->amount,
        ]);
    }

    private function handleFailure(ProsaSubscription $sub, string $message, string $code): void
    {
        $newFailures = $sub->failure_count + 1;
        $suspend     = $newFailures >= self::MAX_FAILURES;

        $sub->update([
            'failure_count' => $newFailures,
            'status'        => $suspend ? 'suspended' : 'active',
        ]);

        Log::error('prosa:charge-recurring — fallo al cobrar', [
            'subscription_id' => $sub->id,
            'correo'          => $sub->correo_usuario,
            'failure_count'   => $newFailures,
            'suspended'       => $suspend,
            'error'           => $message,
            'code'            => $code,
        ]);

        if ($suspend) {
            Log::critical('prosa:charge-recurring — suscripción suspendida por demasiados fallos', [
                'subscription_id' => $sub->id,
                'correo'          => $sub->correo_usuario,
            ]);
        }
    }
}
