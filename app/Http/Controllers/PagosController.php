<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Models\ProsaPendingCheckout;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\Checkout\PagosRenovacionCheckout;
use App\Services\Prosa\PaymentService;

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

        // Historial de pagos desde pats_pagos_pasaporte
        $pagosRaw = DB::table('pats_pagos_pasaporte')
            ->where('correo', $user->correo_usuario)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $pagos = $pagosRaw->map(fn($p) => [
            'folio'    => $p->referencia_pago ?? 'PATS-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
            'producto' => match (strtoupper($p->tipo_operacion ?? '')) {
                'ALTA_PATS'       => 'Alta Pasaporte PATS',
                'RENOVACION_PATS' => 'Renovación Pasaporte PATS',
                default           => 'Membresía PATS',
            },
            'fecha'   => $p->fecha_pago ? Carbon::parse($p->fecha_pago)->format('d/m/Y') : '—',
            'monto'   => (float) $p->monto,
            'metodo'  => ucwords(str_replace('_', ' ', $p->metodo_pago ?? $p->proveedor_pasarela ?? 'Tarjeta')),
            'estatus' => match (strtolower($p->estatus_pago ?? '')) {
                'confirmado' => 'Pagado',
                'pendiente'  => 'Pendiente',
                'rechazado'  => 'Rechazado',
                default      => ucfirst($p->estatus_pago ?? 'Pagado'),
            },
            'frecuencia' => ucfirst(strtolower($p->frecuencia ?? '')),
            'authnum'    => $p->referencia_externa ?? null,
            'proveedor'  => $p->proveedor_pasarela ?? null,
        ]);

        return view('pagos.payment', [
            'user'        => $user,
            'pasaporte'   => $pasaporte,
            'pagos'       => $pagos,
            'totalPagos'  => $pagos->count(),
            'totalPagado' => $pagos->sum('monto'),
            'ultimoPago'  => $pagos->first()['fecha'] ?? '—',
        ]);
    }

    public function procesar(Request $request): JsonResponse
    {
        $request->validate([
            'frecuencia'      => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden'     => ['required', 'numeric', 'min:1'],
            'id_tipo_precio'  => ['required', 'integer'],
            'meses'           => ['nullable', 'integer', 'min:1'],
            'monto_membresia' => ['nullable', 'numeric', 'min:0'],
            'recargo'         => ['nullable', 'numeric', 'min:0'],
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'expMonth'        => ['required', 'string', 'max:2'],
            'expYear'         => ['required', 'string', 'max:4'],
            'saveCard'        => ['nullable', 'boolean'],
            'alias'           => ['nullable', 'string', 'max:50'],
            'browser'         => ['nullable', 'array'],
        ]);

        $user    = auth('pasaporte')->user();
        $ahora   = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $folio      = 'ORD-'  . $ahora->format('Ymd')    . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';
        $saveCard  = (bool) $request->input('saveCard', false);

        // ── 1. Crear orden PENDIENTE ───────────────────────────────────────────
        $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden'         => $folio,
            'referencia_pago'     => $referencia,
            'tipo_origen'         => 'PORTAL_CLIENTE',
            'origen_checkout'     => 'PORTAL_CLIENTE',
            'id_distribuidor'     => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia'       => $pasaporte->id_franquicia   ?? 1,
            'id_pasaporte'        => $pasaporte->id_pasaporte    ?? null,
            'correo_usuario_pats' => $user->correo_usuario,
            'id_tipo_precio'      => $request->id_tipo_precio,
            'tipo_operacion'      => $operacion,
            'frecuencia'          => $request->frecuencia,
            'monto_orden'         => $request->monto_orden,
            'monto_nominal_base'  => $request->monto_orden,
            'monto_extra_recargo' => 0.00,
            'moneda'              => 'MXN',
            'estatus_orden'       => 'PENDIENTE',
            'estatus_pago'        => 'PENDIENTE',
            'proveedor_pasarela'  => 'PROSA',
            'user_creo'           => $user->correo_usuario,
            'fecha_orden'         => $ahora,
            'created_at'          => $ahora,
            'updated_at'          => $ahora,
        ]);

        // ── 2. Preparar tarjeta y checkout ────────────────────────────────────
        $card = CardData::fromForm(
            number:   $request->pan,
            holder:   $request->cardholderName,
            expMonth: $request->expMonth,
            expYear:  $request->expYear,
            cvv:      $request->cvv2,
        );

        $mtx = preg_replace('/[^A-Za-z0-9]/', '', $referencia);

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow'                    => PagosRenovacionCheckout::FLOW,
            'status'                  => ProsaPendingCheckout::STATUS_PENDING,
            'user_id'                 => $user->getKey(),
            'amount'                  => $request->monto_orden,
            'payload'                 => [
                'id_orden'        => $idOrden,
                'referencia'      => $referencia,
                'folio'           => $folio,
                'operacion'       => $operacion,
                'frecuencia'      => $request->frecuencia,
                'monto_orden'     => $request->monto_orden,
                'id_tipo_precio'  => $request->id_tipo_precio,
                'meses'           => max(1, (int) $request->input('meses', 1)),
                'monto_membresia' => (float) $request->input('monto_membresia', $request->monto_orden),
                'recargo'         => (float) $request->input('recargo', 0),
                'saveCard'        => $saveCard,
                'alias'           => $request->alias,
                'card_brand'      => $card->brand(),
                'card_last4'      => $card->last4(),
                'holder'          => $card->holder,
                'expMonth'        => $card->expiryMonth,
                'expYear'         => $card->expiryYear,
                // Datos del titular para ALTA_PATS (cuando no hay pasaporte aún)
                'curp'            => strtoupper($pasaporte->curp ?? ''),
                'nombres'         => $pasaporte->nombres ?? $user->nombre ?? '',
                'apellido_pa'     => $pasaporte->apellido_pa ?? '',
                'apellido_ma'     => $pasaporte->apellido_ma ?? null,
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
                card:               $card,
                amount:             (float) $request->monto_orden,
                currency:           config('prosa.currency'),
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
                'status'   => ProsaPendingCheckout::STATUS_CHALLENGE,
                'redirect' => $result['redirect'],
            ]);

            return response()->json([
                'success'   => true,
                'status'    => 'challenge',
                'challenge' => $result['redirect'],
            ]);
        }

        if ($result['status'] === 'approved') {
            $url = $this->checkoutManager->finish($checkout, $result);

            return response()->json([
                'success'     => true,
                'status'      => 'approved',
                'redirectUrl' => $url,
            ]);
        }

        // Declined
        $checkout->update([
            'status'             => ProsaPendingCheckout::STATUS_DECLINED,
            'result_code'        => $result['resultCode'] ?? null,
            'result_description' => $result['resultDescription'] ?? null,
        ]);
        DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
            'estatus_orden' => 'FALLIDA', 'estatus_pago' => 'RECHAZADO',
            'error_integracion' => $result['resultDescription'] ?? 'Rechazado',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code'    => $result['resultCode'] ?? '',
        ], 400);
    }
}
