<?php

namespace App\Http\Controllers;

use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Mail\RenovacionAvisoMail;
use App\Models\ProsaPendingCheckout;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\Checkout\PagosRenovacionCheckout;
use App\Services\Prosa\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PagosController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CheckoutManager $checkoutManager,
    ) {}

    public function index()
    {
        $user = auth('pasaporte')->user();

        // Pasaporte activo del usuario logueado
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        // Historial: registros antiguos en pats_pagos_pasaporte + nuevos en pats_pagos_detalle
        $mapEstatus = static fn (string $e): string => match (strtolower($e)) {
            'confirmado' => 'Pagado',
            'pendiente_oxxo' => 'Pendiente Oxxo',
            'pendiente_validacion' => 'Pendiente Validacion',
            'pendiente' => 'Pendiente',
            'rechazado' => 'Rechazado',
            default => ucfirst($e),
        };
        $mapProducto = static fn (string $t): string => match (strtoupper($t)) {
            'ALTA_PATS' => 'Alta Pasaporte PATS',
            'RENOVACION_PATS' => 'Renovación Pasaporte PATS',
            default => 'Membresía PATS',
        };

        $pagosAntiguos = DB::table('pats_pagos_pasaporte')
            ->where('correo', $user->correo_usuario)
            ->orderBy('fecha_pago', 'desc')
            ->get()
            ->map(fn ($p) => [
                'folio' => $p->referencia_pago ?? 'PATS-'.str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'producto' => $mapProducto($p->tipo_operacion ?? ''),
                'fecha' => $p->fecha_pago ? Carbon::parse($p->fecha_pago)->format('d/m/Y') : '—',
                'fecha_sort' => $p->fecha_pago,
                'monto' => (float) $p->monto,
                'metodo' => ucwords(str_replace('_', ' ', $p->metodo_pago ?? $p->proveedor_pasarela ?? 'Tarjeta')),
                'estatus' => $mapEstatus($p->estatus_pago ?? 'Pagado'),
                'frecuencia' => ucfirst(strtolower($p->frecuencia ?? '')),
                'authnum' => $p->referencia_externa ?? null,
                'proveedor' => $p->proveedor_pasarela ?? null,
            ]);

        $idsPasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->pluck('id_pasaporte');

        $pagosNuevos = DB::table('pats_pagos_detalle')
            ->whereIn('id_pasaporte', $idsPasaporte)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => [
                'folio' => $p->referencia_pago ?? 'DET-'.str_pad($p->id_detalle, 4, '0', STR_PAD_LEFT),
                'producto' => $mapProducto($p->tipo_pago ?? ''),
                'fecha' => $p->created_at ? Carbon::parse($p->created_at)->format('d/m/Y') : '—',
                'fecha_sort' => $p->created_at,
                'monto' => (float) ($p->monto_decimal ?? 0),
                'metodo' => ucwords(str_replace('_', ' ', $p->metodo_pago ?? $p->pasarela ?? 'Tarjeta')),
                'estatus' => $mapEstatus($p->estatus_pago ?? 'confirmado'),
                'frecuencia' => ucfirst(strtolower($p->frecuencia ?? '')),
                'authnum' => $p->referencia_pasarela ?? null,
                'proveedor' => $p->pasarela ?? null,
            ]);

        $pagos = $pagosNuevos->concat($pagosAntiguos)
            ->sortByDesc('fecha_sort')
            ->values();

        return view('pagos.payment', [
            'user' => $user,
            'pasaporte' => $pasaporte,
            'pagos' => $pagos,
            'totalPagos' => $pagos->count(),
            'totalPagado' => $pagos->sum('monto'),
            'ultimoPago' => $pagos->first()['fecha'] ?? '—',
        ]);
    }

    public function procesar(Request $request): JsonResponse
    {
        $request->validate([
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
            'id_tipo_precio' => ['required', 'integer'],
            'meses' => ['nullable', 'integer', 'min:1'],
            'monto_membresia' => ['nullable', 'numeric', 'min:0'],
            'recargo' => ['nullable', 'numeric', 'min:0'],
            'pan' => ['required', 'string'],
            'cardholderName' => ['required', 'string'],
            'cvv2' => ['required', 'string', 'min:3', 'max:4'],
            'expMonth' => ['required', 'string', 'max:2'],
            'expYear' => ['required', 'string', 'max:4'],
            'saveCard' => ['nullable', 'boolean'],
            'alias' => ['nullable', 'string', 'max:50'],
            'browser' => ['nullable', 'array'],
        ]);

        $user = auth('pasaporte')->user();
        $ahora = Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';
        $saveCard = (bool) $request->input('saveCard', false);

        // ── 1. Crear orden PENDIENTE ───────────────────────────────────────────
        $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden' => $folio,
            'referencia_pago' => $referencia,
            'tipo_origen' => 'PORTAL_CLIENTE',
            'origen_checkout' => 'PORTAL_CLIENTE',
            'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia' => $pasaporte->id_franquicia ?? 1,
            'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
            'correo_usuario_pats' => $user->correo_usuario,
            'id_tipo_precio' => $request->id_tipo_precio,
            'tipo_operacion' => $operacion,
            'frecuencia' => $request->frecuencia,
            'monto_orden' => $request->monto_orden,
            'monto_nominal_base' => $request->monto_orden,
            'monto_extra_recargo' => 0.00,
            'moneda' => 'MXN',
            'estatus_orden' => 'PENDIENTE',
            'estatus_pago' => 'PENDIENTE',
            'proveedor_pasarela' => 'PROSA',
            'user_creo' => $user->correo_usuario,
            'fecha_orden' => $ahora,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        // ── 2. Preparar tarjeta y checkout ────────────────────────────────────
        $card = CardData::fromForm(
            number: $request->pan,
            holder: $request->cardholderName,
            expMonth: $request->expMonth,
            expYear: $request->expYear,
            cvv: $request->cvv2,
        );

        $mtx = preg_replace('/[^A-Za-z0-9]/', '', $referencia);

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow' => PagosRenovacionCheckout::FLOW,
            'status' => ProsaPendingCheckout::STATUS_PENDING,
            'user_id' => $user->getKey(),
            'amount' => $request->monto_orden,
            'payload' => [
                'id_orden' => $idOrden,
                'referencia' => $referencia,
                'folio' => $folio,
                'operacion' => $operacion,
                'frecuencia' => $request->frecuencia,
                'monto_orden' => $request->monto_orden,
                'id_tipo_precio' => $request->id_tipo_precio,
                'meses' => max(1, (int) $request->input('meses', 1)),
                'monto_membresia' => (float) $request->input('monto_membresia', $request->monto_orden),
                'recargo' => (float) $request->input('recargo', 0),
                'saveCard' => $saveCard,
                'alias' => $request->alias,
                'card_brand' => $card->brand(),
                'card_last4' => $card->last4(),
                'holder' => $card->holder,
                'expMonth' => $card->expiryMonth,
                'expYear' => $card->expiryYear,
                // Datos del titular para ALTA_PATS (cuando no hay pasaporte aún)
                'curp' => strtoupper($pasaporte->curp ?? ''),
                'nombres' => $pasaporte->nombres ?? $user->nombre ?? '',
                'apellido_pa' => $pasaporte->apellido_pa ?? '',
                'apellido_ma' => $pasaporte->apellido_ma ?? null,
                'fecha_nacimiento' => $pasaporte->fecha_nacimiento ?? null,
            ],
        ]);

        // ── 3. Iniciar cobro con 3DS ───────────────────────────────────────────
        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $mtx]),
            email: $user->correo_usuario ?? null,
            givenName: $card->holder,
            browser: $request->input('browser'),
        );

        try {
            $result = $this->paymentService->initiate(new ChargeData(
                card: $card,
                amount: (float) $request->monto_orden,
                currency: config('prosa.currency'),
                merchantTransactionId: $mtx,
                createRegistration: $saveCard,
            ), $threeDs);
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden' => 'FALLIDA', 'estatus_pago' => 'TIMEOUT',
                'error_integracion' => 'Timeout', 'updated_at' => now(),
            ]);

            return response()->json(['success' => false, 'error' => 'Timeout al procesar el pago.', 'code' => 'TIMEOUT'], 504);
        }

        $checkout->update(['payment_id' => $result['paymentId'] ?? null]);

        // ── 4. Resolver estado ────────────────────────────────────────────────
        if ($result['status'] === 'challenge') {
            $checkout->update([
                'status' => ProsaPendingCheckout::STATUS_CHALLENGE,
                'redirect' => $result['redirect'],
            ]);

            return response()->json([
                'success' => true,
                'status' => 'challenge',
                'challenge' => $result['redirect'],
            ]);
        }

        if ($result['status'] === 'approved') {
            $url = $this->checkoutManager->finish($checkout, $result);

            return response()->json([
                'success' => true,
                'status' => 'approved',
                'redirectUrl' => $url,
            ]);
        }

        // Declined
        $checkout->update([
            'status' => ProsaPendingCheckout::STATUS_DECLINED,
            'result_code' => $result['resultCode'] ?? null,
            'result_description' => $result['resultDescription'] ?? null,
        ]);
        DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
            'estatus_orden' => 'FALLIDA', 'estatus_pago' => 'RECHAZADO',
            'error_integracion' => $result['resultDescription'] ?? 'Rechazado',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => false,
            'error' => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code' => $result['resultCode'] ?? '',
        ], 400);
    }

    /** Crea un Stripe PaymentIntent para renovación de pasaporte. */
    public function crearIntentStripe(Request $request): JsonResponse
    {
        $request->validate([
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        if ($pasaporte && strtoupper($pasaporte->tipo_cliente ?? '') === 'EMPRESA') {
            return response()->json(['ok' => false, 'error' => 'Los pagos de empresa se gestionan a través de tu plan corporativo.'], 403);
        }

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        $amountCents = (int) round((float) $request->monto_orden * 100);
        $fullName = trim(($pasaporte->nombres ?? $user->nombre_usuario ?? '').' '.($pasaporte->apellido_pa ?? ''));

        try {
            $intent = PaymentIntent::create([
                'amount' => $amountCents,
                'currency' => 'mxn',
                'payment_method_types' => ['card'],
                'description' => 'Renovación PATS '.strtoupper($request->frecuencia).' - '.$fullName,
                'metadata' => [
                    'correo' => $user->correo_usuario,
                    'frecuencia' => $request->frecuencia,
                    'operacion' => $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS',
                ],
                'receipt_email' => $user->correo_usuario,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }

        return response()->json([
            'ok' => true,
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ]);
    }

    // ── Stripe helpers ────────────────────────────────────────────────────────

    /** Busca o crea el Stripe Customer asociado al usuario logueado. */
    private function stripeCustomer(): \Stripe\Customer
    {
        $user = auth('pasaporte')->user();
        $email = $user->correo_usuario;
        $list = \Stripe\Customer::all(['email' => $email, 'limit' => 1]);

        return $list->data[0] ?? \Stripe\Customer::create([
            'email' => $email,
            'name' => trim(($user->nombre_usuario ?? '').' '.($user->apellido_pa ?? '')) ?: $email,
        ]);
    }

    /** Aplica un PaymentIntent ya confirmado: actualiza pasaporte y registra pago. */
    private function aplicarPagoStripe(
        PaymentIntent $intent,
        string $frecuencia,
        float $monto,
        int $meses,
        $user,
        $pasaporte,
        bool $esSuscripcion = false
    ): JsonResponse {
        $ahora = Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        $frecuencia = strtoupper($frecuencia);

        // Opción A: recargo recupera meses vencidos
        $mesesVencidos = (int) ($pasaporte->meses_vencidos ?? 0);
        $vigActiva = $pasaporte && ! empty($pasaporte->vigencia) && Carbon::parse($pasaporte->vigencia)->gt($ahora);
        if ($vigActiva) {
            $baseVig = Carbon::parse($pasaporte->vigencia)->startOfDay();
            $mesesTotal = $meses;
        } elseif ($pasaporte && $mesesVencidos > 0 && ! empty($pasaporte->fecha_vencimiento_real)) {
            $baseVig = Carbon::parse($pasaporte->fecha_vencimiento_real)->startOfDay();
            $mesesTotal = $meses + $mesesVencidos;
        } else {
            $baseVig = $ahora->copy();
            $mesesTotal = $meses;
        }
        $vigencia = $baseVig->copy()->addMonths($mesesTotal)->toDateString();
        $vencReal = $baseVig->copy()->addMonths($mesesTotal)->endOfDay();

        $charge = $intent->latest_charge ?? '';
        $last4 = '????';
        $brand = 'card';
        try {
            if ($intent->payment_method) {
                $pm = \Stripe\PaymentMethod::retrieve($intent->payment_method);
                $last4 = $pm->card->last4 ?? '????';
                $brand = $pm->card->brand ?? 'card';
            }
        } catch (\Throwable) {
        }

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        return DB::transaction(function () use (
            $intent, $user, $pasaporte, $ahora, $referencia, $folio,
            $frecuencia, $vigencia, $vencReal, $monto,
            $charge, $last4, $brand, $operacion, $esSuscripcion
        ) {
            $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
                'folio_orden' => $folio,
                'referencia_pago' => $referencia,
                'tipo_origen' => 'PORTAL_CLIENTE',
                'origen_checkout' => 'PORTAL_CLIENTE',
                'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
                'id_franquicia' => $pasaporte->id_franquicia ?? 1,
                'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
                'correo_usuario_pats' => $user->correo_usuario,
                'id_tipo_precio' => $frecuencia === 'ANUAL' ? 1 : 2,
                'tipo_operacion' => $operacion,
                'frecuencia' => $frecuencia,
                'monto_orden' => $monto,
                'monto_nominal_base' => $monto,
                'monto_extra_recargo' => 0.00,
                'moneda' => 'MXN',
                'proveedor_pasarela' => 'STRIPE',
                'payment_intent_id' => $intent->id,
                'transaccion_id_externa' => $intent->id,
                'charge_id' => $charge,
                'estatus_orden' => 'PAGADA',
                'estatus_pago' => 'CONFIRMADO',
                'pasaporte_creado' => 1,
                'procesado_integracion' => 1,
                'fecha_orden' => $ahora,
                'fecha_pago' => $ahora,
                'fecha_confirmacion' => $ahora,
                'user_creo' => $user->correo_usuario,
                'user_confirmo' => $user->correo_usuario,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            $idPasaporte = null;
            if ($pasaporte) {
                DB::table('pats_pasaportes')->where('id_pasaporte', $pasaporte->id_pasaporte)->update([
                    'vigencia' => $vigencia,
                    'frecuencia_pago' => $frecuencia,
                    'estatus' => 'activo',
                    'valor_pasaporte' => $monto,
                    'valor_final_pasaporte' => $monto,
                    'fecha_ultimo_pago' => $ahora,
                    'fecha_vencimiento_real' => $vencReal,
                    'meses_vencidos' => 0,
                    'recargo_acumulado' => 0.00,
                    'updated_at' => $ahora,
                ]);
                $idPasaporte = $pasaporte->id_pasaporte;
                DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                    'id_pasaporte' => $idPasaporte,
                    'id_pasaporte_generado' => $idPasaporte,
                    'fecha_alta_pasaporte' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            $this->registrarPagoEnDetalle([
                'id_orden' => $idOrden,
                'id_pasaporte' => $idPasaporte,
                'pasarela' => 'STRIPE',
                'referencia_pasarela' => $charge ?: $intent->id,
                'referencia_pago' => $referencia,
                'referencia_interna' => $folio,
                'metodo_pago' => 'tarjeta_'.$brand,
                'tipo_pago' => $operacion,
                'frecuencia' => $frecuencia,
                'monto_decimal' => $monto,
                'estatus_pago' => 'confirmado',
                'customer_pasarela_id' => $intent->customer ?? null,
                'payment_method_pasarela_id' => $intent->payment_method ?? null,
                'token_pasarela' => $intent->id,
                'domiciliacion_solicitada' => $esSuscripcion ? 1 : 0,
                'domiciliacion_autorizada' => $esSuscripcion ? 1 : 0,
                'domiciliacion_activa' => $esSuscripcion ? 1 : 0,
                'domiciliacion_estatus' => $esSuscripcion ? 'activa' : null,
                'observaciones' => 'Stripe '.strtoupper($brand).'···'.$last4.($esSuscripcion ? ' · Suscripción' : ''),
                'payload_response_json' => json_encode(['id' => $intent->id, 'status' => $intent->status]),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            return response()->json([
                'ok' => true,
                'referencia' => $referencia,
                'vigencia' => $vigencia,
                'operacion' => $operacion,
            ]);
        });
    }

    // ── Tarjetas guardadas Stripe ─────────────────────────────────────────────

    /** Lista las tarjetas Stripe guardadas del usuario. */
    public function stripeListarTarjetas(): JsonResponse
    {
        Stripe::setApiKey(config('services.stripe_renovacion.secret'));
        try {
            $customer = $this->stripeCustomer();
            $pms = \Stripe\PaymentMethod::all(['customer' => $customer->id, 'type' => 'card']);
            $cards = array_map(fn ($pm) => [
                'id' => $pm->id,
                'brand' => ucfirst($pm->card->brand),
                'last4' => $pm->card->last4,
                'exp' => str_pad($pm->card->exp_month, 2, '0', STR_PAD_LEFT).'/'.$pm->card->exp_year,
            ], $pms->data);

            return response()->json(['ok' => true, 'cards' => $cards]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }
    }

    /** Crea un SetupIntent para guardar una nueva tarjeta. */
    public function stripeCrearSetupIntent(): JsonResponse
    {
        Stripe::setApiKey(config('services.stripe_renovacion.secret'));
        try {
            $customer = $this->stripeCustomer();
            $intent = \Stripe\SetupIntent::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
            ]);

            return response()->json(['ok' => true, 'client_secret' => $intent->client_secret]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }
    }

    /** Cancela la renovación automática: desvincula todas las tarjetas del cliente. */
    public function cancelarRecurrente(): JsonResponse
    {
        $user = auth('pasaporte')->user();

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        try {
            $customer = $this->stripeCustomer();
            $pms = \Stripe\PaymentMethod::all(['customer' => $customer->id, 'type' => 'card']);

            foreach ($pms->data as $pm) {
                $pm->detach();
            }

            $eliminadas = count($pms->data);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }

        // Marcar en BD que ya no tiene renovación automática activa
        DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->update(['frecuencia_pago' => '', 'updated_at' => Carbon::now()]);

        return response()->json(['ok' => true, 'eliminadas' => $eliminadas]);
    }

    /** Desvincula una tarjeta guardada. */
    public function stripeEliminarTarjeta(Request $request): JsonResponse
    {
        $request->validate(['pm_id' => ['required', 'string']]);
        Stripe::setApiKey(config('services.stripe_renovacion.secret'));
        try {
            \Stripe\PaymentMethod::retrieve($request->pm_id)->detach();

            return response()->json(['ok' => true]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }
    }

    /** Cobra con una tarjeta guardada. */
    public function stripePagarConGuardada(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => ['required', 'string'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')->first();

        if ($pasaporte && strtoupper($pasaporte->tipo_cliente ?? '') === 'EMPRESA') {
            return response()->json(['ok' => false, 'error' => 'No permitido para pasaportes de empresa.'], 403);
        }

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        $amountCents = (int) round((float) $request->monto_orden * 100);
        $customer = $this->stripeCustomer();
        $meses = max(1, (int) $request->input('_pats_meses', 1));

        try {
            $intent = PaymentIntent::create([
                'amount' => $amountCents,
                'currency' => 'mxn',
                'customer' => $customer->id,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'payment_method_types' => ['card'],
                'description' => 'Renovación PATS '.strtoupper($request->frecuencia),
                'receipt_email' => $user->correo_usuario,
                'return_url' => route('pagos'),
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }

        if ($intent->status === 'requires_action') {
            return response()->json([
                'ok' => false,
                'requires_action' => true,
                'client_secret' => $intent->client_secret,
            ]);
        }

        if ($intent->status !== 'succeeded') {
            return response()->json(['ok' => false, 'error' => 'Pago no aprobado (estado: '.$intent->status.').'], 400);
        }

        return $this->aplicarPagoStripe($intent, $request->frecuencia, (float) $request->monto_orden, $meses, $user, $pasaporte);
    }

    // ── Recurrente Stripe ─────────────────────────────────────────────────────

    /** Guarda tarjeta vía SetupIntent y cobra el primer período. */
    public function stripeCrearSuscripcion(Request $request): JsonResponse
    {
        $request->validate([
            'setup_intent_id' => ['required', 'string'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')->first();

        if ($pasaporte && strtoupper($pasaporte->tipo_cliente ?? '') === 'EMPRESA') {
            return response()->json(['ok' => false, 'error' => 'No permitido para pasaportes de empresa.'], 403);
        }

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        $meses = max(1, (int) $request->input('_pats_meses', 1));

        try {
            $si = \Stripe\SetupIntent::retrieve($request->setup_intent_id);

            if ($si->status !== 'succeeded') {
                return response()->json(['ok' => false, 'error' => 'La tarjeta no se guardó correctamente.'], 400);
            }

            $amountCents = (int) round((float) $request->monto_orden * 100);
            $customer = $this->stripeCustomer();

            $intent = PaymentIntent::create([
                'amount' => $amountCents,
                'currency' => 'mxn',
                'customer' => $customer->id,
                'payment_method' => $si->payment_method,
                'confirm' => true,
                'payment_method_types' => ['card'],
                'description' => 'Renovación Recurrente PATS '.strtoupper($request->frecuencia),
                'receipt_email' => $user->correo_usuario,
                'return_url' => route('pagos'),
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }

        if ($intent->status === 'requires_action') {
            return response()->json([
                'ok' => false,
                'requires_action' => true,
                'client_secret' => $intent->client_secret,
            ]);
        }

        if ($intent->status !== 'succeeded') {
            return response()->json(['ok' => false, 'error' => 'Pago no aprobado (estado: '.$intent->status.').'], 400);
        }

        return $this->aplicarPagoStripe($intent, $request->frecuencia, (float) $request->monto_orden, $meses, $user, $pasaporte, true);
    }

    // ── OXXO ─────────────────────────────────────────────────────────────────

    /** Crea y confirma un PaymentIntent OXXO; devuelve el número de referencia. */
    public function crearIntentOxxo(Request $request): JsonResponse
    {
        $request->validate([
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
            'nombre' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:150'],
        ]);

        $user = auth('pasaporte')->user();
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')->first();

        if (! $pasaporte) {
            return response()->json(['ok' => false, 'error' => 'No se encontró un pasaporte activo.'], 404);
        }
        if (strtoupper($pasaporte->tipo_cliente ?? '') === 'EMPRESA') {
            return response()->json(['ok' => false, 'error' => 'No permitido para pasaportes de empresa.'], 403);
        }

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        $amountCents = (int) round((float) $request->monto_orden * 100);
        $ahora = Carbon::now();
        $referencia = 'OXXO-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 6));

        try {
            $intent = PaymentIntent::create([
                'amount' => $amountCents,
                'currency' => 'mxn',
                'payment_method_types' => ['oxxo'],
                'payment_method_data' => [
                    'type' => 'oxxo',
                    'billing_details' => [
                        'name' => $request->nombre,
                        'email' => $request->correo,
                    ],
                ],
                'confirm' => true,
                'description' => 'Renovación PATS OXXO '.strtoupper($request->frecuencia),
                'receipt_email' => $request->correo,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }

        $oxxo = $intent->next_action->oxxo_display_details ?? null;
        $number = $oxxo?->number ?? null;
        $expiry = $oxxo?->expires_after ?? null;
        $url = $oxxo?->hosted_voucher_url ?? null;

        $this->registrarPagoEnDetalle([
            'id_pasaporte' => $pasaporte->id_pasaporte,
            'pasarela' => 'STRIPE',
            'referencia_pasarela' => $intent->id,
            'referencia_pago' => $referencia,
            'metodo_pago' => 'oxxo',
            'tipo_pago' => 'RENOVACION_PATS',
            'frecuencia' => strtoupper($request->frecuencia),
            'monto_decimal' => $request->monto_orden,
            'estatus_pago' => 'pendiente_oxxo',
            'token_pasarela' => $intent->id,
            'requiere_accion_usuario' => 1,
            'observaciones' => 'Ficha OXXO generada. Pendiente de pago en tienda.',
            'payload_response_json' => json_encode(['id' => $intent->id, 'status' => $intent->status]),
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        return response()->json([
            'ok' => true,
            'payment_intent_id' => $intent->id,
            'voucher_number' => $number,
            'expires_after' => $expiry,
            'hosted_voucher_url' => $url,
            'referencia' => $referencia,
            'monto' => $request->monto_orden,
        ]);
    }

    /** Verifica si el PaymentIntent OXXO ya fue pagado y actualiza el pasaporte. */
    public function verificarPagoOxxo(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => ['required', 'string'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        try {
            $intent = PaymentIntent::retrieve($request->payment_intent_id);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 502);
        }

        if ($intent->status !== 'succeeded') {
            return response()->json([
                'ok' => false,
                'status' => $intent->status,
                'msg' => 'El pago aún no ha sido procesado en OXXO. Inténtalo de nuevo más tarde.',
            ]);
        }

        // Evitar doble procesamiento
        $yaConfirmado = DB::table('pats_pagos_detalle')
            ->where('referencia_pasarela', $intent->id)
            ->where('estatus_pago', 'confirmado')
            ->exists();

        if ($yaConfirmado) {
            return response()->json(['ok' => true, 'status' => 'succeeded', 'ya_procesado' => true]);
        }

        $ahora = Carbon::now();
        $frecuencia = strtoupper($request->frecuencia);
        $meses = max(1, (int) $request->input('_pats_meses', 1));

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        // Opción A: recargo recupera meses vencidos
        $mesesVencidos = (int) ($pasaporte->meses_vencidos ?? 0);
        $vigActiva = $pasaporte && ! empty($pasaporte->vigencia) && Carbon::parse($pasaporte->vigencia)->gt($ahora);
        if ($vigActiva) {
            $baseVig = Carbon::parse($pasaporte->vigencia)->startOfDay();
            $mesesTotal = $meses;
        } elseif ($pasaporte && $mesesVencidos > 0 && ! empty($pasaporte->fecha_vencimiento_real)) {
            $baseVig = Carbon::parse($pasaporte->fecha_vencimiento_real)->startOfDay();
            $mesesTotal = $meses + $mesesVencidos;
        } else {
            $baseVig = $ahora->copy();
            $mesesTotal = $meses;
        }
        $vigencia = $baseVig->copy()->addMonths($mesesTotal)->toDateString();
        $vencReal = $baseVig->copy()->addMonths($mesesTotal)->endOfDay();

        // Actualizar registro pendiente a confirmado
        DB::table('pats_pagos_detalle')
            ->where('referencia_pasarela', $intent->id)
            ->where('estatus_pago', 'pendiente_oxxo')
            ->update([
                'estatus_pago' => 'confirmado',
                'requiere_accion_usuario' => 0,
                'updated_at' => $ahora,
            ]);

        // Actualizar pasaporte
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')->first();

        if ($pasaporte) {
            DB::table('pats_pasaportes')->where('id_pasaporte', $pasaporte->id_pasaporte)->update([
                'vigencia' => $vigencia,
                'estatus' => 'activo',
                'frecuencia_pago' => $frecuencia,
                'fecha_ultimo_pago' => $ahora,
                'fecha_vencimiento_real' => $vencReal,
                'meses_vencidos' => 0,
                'recargo_acumulado' => 0.00,
                'updated_at' => $ahora,
            ]);
        }

        return response()->json(['ok' => true, 'status' => 'succeeded', 'vigencia' => $vigencia]);
    }

    // ── Efectivo ──────────────────────────────────────────────────────────────

    /** Registra solicitud de pago en efectivo pendiente de validación. */
    public function registrarPagoEfectivo(Request $request): JsonResponse
    {
        $request->validate([
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')->first();

        if (! $pasaporte) {
            return response()->json(['ok' => false, 'error' => 'No se encontró un pasaporte activo.'], 404);
        }

        $ahora = Carbon::now();
        $referencia = 'EFT-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 6));

        $this->registrarPagoEnDetalle([
            'id_pasaporte' => $pasaporte->id_pasaporte,
            'pasarela' => 'EFECTIVO',
            'referencia_pago' => $referencia,
            'metodo_pago' => 'efectivo',
            'tipo_pago' => 'RENOVACION_PATS',
            'frecuencia' => strtoupper($request->frecuencia),
            'monto_decimal' => $request->monto_orden,
            'estatus_pago' => 'pendiente_validacion',
            'requiere_accion_usuario' => 1,
            'observaciones' => 'Solicitud de pago en efectivo — pendiente de validación',
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        return response()->json([
            'ok' => true,
            'referencia' => $referencia,
            'mensaje' => 'Tu solicitud fue registrada. Un asesor validará el pago y te notificará por correo.',
        ]);
    }

    /** Confirma el pago Stripe y actualiza pasaporte / registros. */
    public function confirmarStripe(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => ['required', 'string'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('pasaporte')->user();

        Stripe::setApiKey(config('services.stripe_renovacion.secret'));

        try {
            $intent = PaymentIntent::retrieve($request->payment_intent_id);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['ok' => false, 'error' => 'No se pudo verificar el pago: '.$e->getMessage()], 502);
        }

        if ($intent->status !== 'succeeded') {
            return response()->json(['ok' => false, 'error' => 'El pago no fue confirmado por Stripe (estado: '.$intent->status.').'], 400);
        }

        // Verificar que el monto coincide (tolerancia de 1 centavo)
        $expectedCents = (int) round((float) $request->monto_orden * 100);
        if (abs($intent->amount_received - $expectedCents) > 1) {
            return response()->json(['ok' => false, 'error' => 'El monto del pago no coincide.'], 400);
        }

        $ahora = Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        $frecuencia = strtoupper($request->frecuencia);
        $meses = max(1, (int) $request->input('_pats_meses', 1));

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        if ($pasaporte && strtoupper($pasaporte->tipo_cliente ?? '') === 'EMPRESA') {
            return response()->json(['ok' => false, 'error' => 'No permitido para pasaportes de empresa.'], 403);
        }

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        // Opción A: recargo recupera meses vencidos
        $mesesVencidos = (int) ($pasaporte->meses_vencidos ?? 0);
        $vigActiva = $pasaporte && ! empty($pasaporte->vigencia) && Carbon::parse($pasaporte->vigencia)->gt($ahora);
        if ($vigActiva) {
            $baseVig = Carbon::parse($pasaporte->vigencia)->startOfDay();
            $mesesTotal = $meses;
        } elseif ($pasaporte && $mesesVencidos > 0 && ! empty($pasaporte->fecha_vencimiento_real)) {
            $baseVig = Carbon::parse($pasaporte->fecha_vencimiento_real)->startOfDay();
            $mesesTotal = $meses + $mesesVencidos;
        } else {
            $baseVig = $ahora->copy();
            $mesesTotal = $meses;
        }
        $vigencia = $baseVig->copy()->addMonths($mesesTotal)->toDateString();
        $vencReal = $baseVig->copy()->addMonths($mesesTotal)->endOfDay();

        $charge = $intent->latest_charge ?? '';
        $last4 = $intent->payment_method_details?->card?->last4 ?? '????';
        $brand = $intent->payment_method_details?->card?->brand ?? 'card';

        return DB::transaction(function () use (
            $request, $user, $pasaporte, $intent, $ahora, $referencia, $folio,
            $frecuencia, $vigencia, $vencReal, $operacion, $charge, $last4, $brand
        ) {
            $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
                'folio_orden' => $folio,
                'referencia_pago' => $referencia,
                'tipo_origen' => 'PORTAL_CLIENTE',
                'origen_checkout' => 'PORTAL_CLIENTE',
                'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
                'id_franquicia' => $pasaporte->id_franquicia ?? 1,
                'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
                'correo_usuario_pats' => $user->correo_usuario,
                'id_tipo_precio' => $frecuencia === 'ANUAL' ? 1 : 2,
                'tipo_operacion' => $operacion,
                'frecuencia' => $frecuencia,
                'monto_orden' => $request->monto_orden,
                'monto_nominal_base' => $request->monto_orden,
                'monto_extra_recargo' => 0.00,
                'moneda' => 'MXN',
                'proveedor_pasarela' => 'STRIPE',
                'payment_intent_id' => $intent->id,
                'transaccion_id_externa' => $intent->id,
                'charge_id' => $charge,
                'estatus_orden' => 'PAGADA',
                'estatus_pago' => 'CONFIRMADO',
                'pasaporte_creado' => 1,
                'procesado_integracion' => 1,
                'fecha_orden' => $ahora,
                'fecha_pago' => $ahora,
                'fecha_confirmacion' => $ahora,
                'user_creo' => $user->correo_usuario,
                'user_confirmo' => $user->correo_usuario,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            if ($pasaporte) {
                DB::table('pats_pasaportes')->where('id_pasaporte', $pasaporte->id_pasaporte)->update([
                    'vigencia' => $vigencia,
                    'frecuencia_pago' => $frecuencia,
                    'estatus' => 'activo',
                    'valor_pasaporte' => $request->monto_orden,
                    'valor_final_pasaporte' => $request->monto_orden,
                    'fecha_ultimo_pago' => $ahora,
                    'fecha_vencimiento_real' => $vencReal,
                    'meses_vencidos' => 0,
                    'recargo_acumulado' => 0.00,
                    'updated_at' => $ahora,
                ]);
                $idPasaporte = $pasaporte->id_pasaporte;
            } else {
                $idPasaporte = null;
            }

            if ($idPasaporte) {
                DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                    'id_pasaporte' => $idPasaporte,
                    'id_pasaporte_generado' => $idPasaporte,
                    'fecha_alta_pasaporte' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            $this->registrarPagoEnDetalle([
                'id_orden' => $idOrden,
                'id_pasaporte' => $idPasaporte,
                'pasarela' => 'STRIPE',
                'referencia_pasarela' => $charge ?: $intent->id,
                'referencia_pago' => $referencia,
                'referencia_interna' => $folio,
                'metodo_pago' => 'tarjeta_'.$brand,
                'tipo_pago' => $operacion,
                'frecuencia' => $frecuencia,
                'monto_decimal' => $request->monto_orden,
                'estatus_pago' => 'confirmado',
                'token_pasarela' => $intent->id,
                'observaciones' => 'Stripe '.strtoupper($brand).'···'.$last4,
                'payload_response_json' => json_encode(['id' => $intent->id, 'status' => $intent->status, 'amount' => $intent->amount]),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            return response()->json([
                'ok' => true,
                'referencia' => $referencia,
                'vigencia' => $vigencia,
                'operacion' => $operacion,
            ]);
        });
    }

    /** Inserta un registro de pago en pats_pagos_detalle. */
    private function registrarPagoEnDetalle(array $d): void
    {
        $frecUpper = strtoupper($d['frecuencia'] ?? '');
        DB::table('pats_pagos_detalle')->insert([
            'id_orden' => $d['id_orden'] ?? null,
            'id_pasaporte' => $d['id_pasaporte'] ?? null,
            'id_respaldo' => $d['id_respaldo'] ?? null,
            'pasarela' => $d['pasarela'] ?? 'STRIPE',
            'ambiente' => app()->environment('production') ? 'live' : 'test',
            'referencia_pasarela' => $d['referencia_pasarela'] ?? null,
            'referencia_pago' => $d['referencia_pago'] ?? null,
            'referencia_interna' => $d['referencia_interna'] ?? null,
            'metodo_pago' => $d['metodo_pago'] ?? null,
            'tipo_pago' => $d['tipo_pago'] ?? null,
            'frecuencia' => $frecUpper ?: null,
            'monto_centavos' => isset($d['monto_decimal']) ? (int) round((float) $d['monto_decimal'] * 100) : null,
            'monto_decimal' => $d['monto_decimal'] ?? null,
            'moneda' => $d['moneda'] ?? 'MXN',
            'es_mensual' => $frecUpper === 'MENSUAL' ? 1 : 0,
            'es_anual' => $frecUpper === 'ANUAL' ? 1 : 0,
            'es_msi' => 0,
            'domiciliacion_solicitada' => $d['domiciliacion_solicitada'] ?? 0,
            'domiciliacion_autorizada' => $d['domiciliacion_autorizada'] ?? 0,
            'domiciliacion_activa' => $d['domiciliacion_activa'] ?? 0,
            'domiciliacion_estatus' => $d['domiciliacion_estatus'] ?? null,
            'customer_pasarela_id' => $d['customer_pasarela_id'] ?? null,
            'payment_method_pasarela_id' => $d['payment_method_pasarela_id'] ?? null,
            'token_pasarela' => $d['token_pasarela'] ?? null,
            'estatus_pago' => $d['estatus_pago'] ?? 'confirmado',
            'estatus_detalle' => $d['estatus_detalle'] ?? null,
            'requiere_accion_usuario' => $d['requiere_accion_usuario'] ?? 0,
            'mensaje_usuario' => $d['mensaje_usuario'] ?? null,
            'observaciones' => $d['observaciones'] ?? null,
            'payload_request_json' => $d['payload_request_json'] ?? null,
            'payload_response_json' => $d['payload_response_json'] ?? null,
            'payload_confirmacion_json' => $d['payload_confirmacion_json'] ?? null,
            'created_at' => $d['created_at'] ?? Carbon::now(),
            'updated_at' => $d['updated_at'] ?? Carbon::now(),
        ]);
    }

    /**
     * Lista los pasaportes que recibirían el aviso de renovación, sin enviar nada.
     */
    public function previewRenovacion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dias' => 'nullable|integer|min:1|max:90',
        ]);
        $dias = (int) ($validated['dias'] ?? 15);

        $pasaportes = $this->pasaportesParaRenovacion($dias);

        return response()->json([
            'ok' => true,
            'dias' => $dias,
            'total' => $pasaportes->count(),
            'pasaportes' => $pasaportes->values(),
        ]);
    }

    /**
     * Envía el aviso de renovación a los pasaportes por vencer o ya vencidos.
     * Requiere `confirmar=1` para evitar disparos accidentales.
     */
    public function enviarRenovacion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dias' => 'nullable|integer|min:1|max:90',
            'confirmar' => 'required|boolean',
        ]);

        if (! $validated['confirmar']) {
            return response()->json([
                'ok' => false,
                'error' => 'Debes confirmar el envío.',
            ], 422);
        }

        $dias = (int) ($validated['dias'] ?? 15);
        $pasaportes = $this->pasaportesParaRenovacion($dias);

        $enviados = 0;
        $fallidos = [];

        foreach ($pasaportes as $p) {
            try {
                Mail::to($p['correo'])->send(new RenovacionAvisoMail(
                    nombre: $p['nombre'] !== '' ? $p['nombre'] : 'Usuario PATS',
                    estado: $p['estado'],
                    fechaVencimiento: $p['fecha_vencimiento'],
                    diasRestantes: $p['dias_restantes'],
                    mesesVencidos: $p['meses_vencidos'],
                    recargoAcumulado: $p['recargo_acumulado'],
                ));
                $enviados++;
            } catch (\Throwable $e) {
                $fallidos[] = [
                    'correo' => $p['correo'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'dias' => $dias,
            'total' => $pasaportes->count(),
            'enviados' => $enviados,
            'fallidos' => $fallidos,
        ]);
    }

    /**
     * Pasaportes activos por vencer dentro de `$dias` días o ya vencidos con recargo pendiente.
     *
     * @return Collection<int, array{id_pasaporte:int,correo:string,nombre:string,fecha_vencimiento:?string,estado:string,dias_restantes:int,meses_vencidos:int,recargo_acumulado:float}>
     */
    private function pasaportesParaRenovacion(int $dias): Collection
    {
        $ahora = Carbon::now();
        $limite = $ahora->copy()->addDays($dias);

        $pasaportes = DB::table('pats_pasaportes')
            ->where('activo', 1)
            ->whereNotNull('correo')
            ->where('correo', '!=', '')
            ->where(function ($q) use ($ahora, $limite) {
                // Por vencer dentro del rango O ya vencidos (sin depender de meses_vencidos)
                $q->whereBetween('fecha_vencimiento_real', [$ahora, $limite])
                    ->orWhere('fecha_vencimiento_real', '<', $ahora);
            })
            ->orderBy('fecha_vencimiento_real')
            ->get(['id_pasaporte', 'correo', 'nombres', 'apellido_pa', 'apellido_ma', 'fecha_vencimiento_real', 'recargo_acumulado']);

        return $pasaportes->map(function ($p) use ($ahora) {
            $vencimiento = $p->fecha_vencimiento_real ? Carbon::parse($p->fecha_vencimiento_real) : null;
            $diasRestantes = $vencimiento ? (int) $ahora->diffInDays($vencimiento, false) : 0;
            // Meses vencidos calculados desde la fecha, no del campo BD
            $mesesVencidos = $vencimiento && $vencimiento->isPast()
                ? (int) $vencimiento->diffInMonths($ahora)
                : 0;

            return [
                'id_pasaporte' => (int) $p->id_pasaporte,
                'correo' => $p->correo,
                'nombre' => trim(($p->nombres ?? '').' '.($p->apellido_pa ?? '').' '.($p->apellido_ma ?? '')),
                'fecha_vencimiento' => $vencimiento?->format('d/m/Y'),
                'estado' => $diasRestantes < 0 ? 'vencido' : 'por_vencer',
                'dias_restantes' => $diasRestantes,
                'meses_vencidos' => $mesesVencidos,
                'recargo_acumulado' => (float) $p->recargo_acumulado,
            ];
        });
    }
}
