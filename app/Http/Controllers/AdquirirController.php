<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\ProsaPendingCheckout;
use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Services\Prosa\PaymentService;
use App\Services\Prosa\Checkout\AdquirirCheckout;
use App\Services\Prosa\Checkout\CheckoutManager;

class AdquirirController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    // ──────────────────────────────────────────────
    //  GET /adquirir?t={token}
    // ──────────────────────────────────────────────

    public function show(Request $request)
    {
        $token = trim($request->query('t', ''));

        if (empty($token)) {
            abort(400, 'Falta el token público.');
        }

        $ctx = $this->resolverToken($token);

        if (!$ctx) {
            abort(404, 'El link no es válido o ya no está activo.');
        }

        return view('adquirir.index', [
            'token' => $token,
            'ctx'   => $ctx,
            'precios' => [
                'mensual' => ['monto' => 800,  'id_tipo_precio' => 2],
                'anual'   => ['monto' => 9600, 'id_tipo_precio' => 1],
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    //  POST /adquirir/procesar
    // ──────────────────────────────────────────────

    public function procesar(Request $request): JsonResponse
    {
        $request->validate([
            // Cuenta nueva
            'correo'          => ['required', 'email'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'telefono_usuario'=> ['required', 'string', 'max:15'],

            // Datos personales
            'nombre_usuario'  => ['required', 'string'],
            'apellido_pa'     => ['required', 'string'],
            'apellido_ma'     => ['nullable', 'string'],
            'curp_usuario'    => ['required', 'string', 'max:18'],
            'fecha_nacimiento'=> ['required', 'date'],
            'tipo_cliente'    => ['required', 'in:privado,empresa'],

            // Domicilio
            'dom_calle'       => ['required', 'string'],
            'dom_num_ext'     => ['required', 'string'],
            'dom_colonia'     => ['required', 'string'],
            'dom_cp'          => ['required', 'string'],
            'dom_municipio'   => ['required', 'string'],
            'dom_estado'      => ['required', 'string'],
            'dom_pais'        => ['required', 'string'],

            // Plan
            'frecuencia'      => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden'     => ['required', 'numeric', 'min:1'],
            'id_tipo_precio'  => ['required', 'integer'],

            // Tarjeta
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'expDate'         => ['required', 'string', 'size:4'],

            // Token del distribuidor
            'token_publico'   => ['required', 'string'],
        ]);

        // ── Resolver token ─────────────────────────────────────
        $ctx = $this->resolverToken($request->token_publico);
        if (!$ctx) {
            return response()->json(['success' => false, 'error' => 'Token inválido.'], 400);
        }

        // ── Verificar que el correo no exista ya ───────────────
        $correo = strtolower(trim($request->correo));
        if (DB::table('pats_users')->where('correo', $correo)->exists()) {
            return response()->json([
                'success' => false,
                'error'   => 'Ya existe una cuenta con ese correo. Por favor inicia sesión.',
                'code'    => 'EMAIL_EXISTS',
            ], 422);
        }

        $ahora      = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $folio      = 'ORD-'  . $ahora->format('Ymd')    . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        // ── 1. Orden PENDIENTE ─────────────────────────────────
        $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden'         => $folio,
            'referencia_pago'     => $referencia,
            'tipo_origen'         => $ctx['tipo_origen'],
            'origen_checkout'     => 'PORTAL_PUBLICO',
            'id_distribuidor'     => $ctx['id_distribuidor'],
            'id_franquicia'       => $ctx['id_franquicia'],
            'correo_usuario_pats' => $correo,
            'curp_usuario'        => strtoupper($request->curp_usuario),
            'nombre_usuario'      => $request->nombre_usuario,
            'apellido_pa'         => $request->apellido_pa,
            'apellido_ma'         => $request->apellido_ma,
            'fecha_nacimiento'    => $request->fecha_nacimiento,
            'telefono_usuario'    => $request->telefono_usuario,
            'id_tipo_precio'      => $request->id_tipo_precio,
            'tipo_operacion'      => 'ALTA_PATS',
            'frecuencia'          => $request->frecuencia,
            'monto_orden'         => $request->monto_orden,
            'monto_nominal_base'  => $request->monto_orden,
            'monto_extra_recargo' => 0.00,
            'moneda'              => 'MXN',
            'pais'                => $ctx['pais'],
            'region'              => $ctx['region'],
            'zona'                => $ctx['zona'],
            'unidad'              => $ctx['unidad'],
            'tipo_cliente'        => $request->tipo_cliente,
            'nombre_empresa'      => $request->nombre_empresa,
            'estatus_orden'       => 'PENDIENTE',
            'estatus_pago'        => 'PENDIENTE',
            'proveedor_pasarela'  => 'PROSA',
            'user_creo'           => $correo,
            'payload_checkout_json' => json_encode(['token_publico' => $request->token_publico, 'ctx' => $ctx]),
            'fecha_orden'         => $ahora,
            'created_at'          => $ahora,
            'updated_at'          => $ahora,
        ]);

        // ── 2. Iniciar cobro con Prosa + 3-D Secure ────────────
        $card = CardData::fromForm(
            number:   $request->pan,
            holder:   $request->cardholderName,
            expMonth: substr($request->expDate, 2, 2),
            expYear:  substr($request->expDate, 0, 2),
            cvv:      $request->cvv2,
        );

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $this->mtxFromReference($referencia),
            'flow'                    => AdquirirCheckout::FLOW,
            'status'                  => ProsaPendingCheckout::STATUS_PENDING,
            'amount'                  => $request->monto_orden,
            'payload'                 => [
                'id_orden'         => $idOrden,
                'referencia'       => $referencia,
                'folio'            => $folio,
                'token_publico'    => $request->token_publico,
                'ctx'              => $ctx,
                'correo'           => $correo,
                'password_hash'    => Hash::make($request->password),
                'telefono_usuario' => $request->telefono_usuario,
                'nombre_usuario'   => $request->nombre_usuario,
                'apellido_pa'      => $request->apellido_pa,
                'apellido_ma'      => $request->apellido_ma,
                'curp_usuario'     => $request->curp_usuario,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'tipo_cliente'     => $request->tipo_cliente,
                'nombre_empresa'   => $request->nombre_empresa,
                'frecuencia'       => $request->frecuencia,
                'monto_orden'      => $request->monto_orden,
                'id_tipo_precio'   => $request->id_tipo_precio,
            ],
        ]);

        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $checkout->merchant_transaction_id]),
            email: $correo,
            givenName: $request->nombre_usuario,
            surname: $request->apellido_pa,
            billing: [
                'street1'  => trim($request->dom_calle . ' ' . $request->dom_num_ext),
                'city'     => $request->dom_municipio,
                'state'    => $request->dom_estado,
                'postcode' => $request->dom_cp,
                'country'  => 'MX',
            ],
            browser: $request->input('browser'),
        );

        try {
            $result = $this->paymentService->initiate(new ChargeData(
                card:     $card,
                amount:   (float) $request->monto_orden,
                currency: config('prosa.currency'),
                merchantTransactionId: $checkout->merchant_transaction_id,
            ), $threeDs);
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden'     => 'FALLIDA',
                'estatus_pago'      => 'TIMEOUT',
                'error_integracion' => 'Timeout',
                'updated_at'        => now(),
            ]);

            return response()->json(['success' => false, 'error' => 'Timeout al procesar el pago.', 'code' => 'TIMEOUT'], 504);
        }

        $checkout->update(['payment_id' => $result['paymentId'] ?? null]);

        // ── 3. Resolver según estado ───────────────────────────
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
            $redirectUrl = app(CheckoutManager::class)->finish($checkout, $result);

            return response()->json([
                'success'  => true,
                'status'   => 'approved',
                'redirect' => $redirectUrl,
            ]);
        }

        $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
        DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
            'estatus_orden'     => 'FALLIDA',
            'estatus_pago'      => 'RECHAZADO',
            'error_integracion' => $result['resultDescription'] ?? 'Rechazado',
            'updated_at'        => now(),
        ]);

        return response()->json([
            'success' => false,
            'status'  => 'declined',
            'error'   => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code'    => $result['resultCode'] ?? '',
        ], 400);
    }

    private function mtxFromReference(string $referencia): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $referencia);

        return substr($clean, 0, 255);
    }

    // ──────────────────────────────────────────────
    //  Resolver token — busca en distribuidores y franquicias
    // ──────────────────────────────────────────────

    private function resolverToken(string $token): ?array
    {
        // Primero buscar en distribuidores
        $dist = DB::table('pats_distribuidores')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->first();

        if ($dist) {
            return [
                'tipo_origen'    => 'DISTRIBUIDOR',
                'id_distribuidor'=> $dist->id_distribuidor,
                'id_franquicia'  => $dist->id_franquicia,
                'pais'           => $dist->pais ?? 'México',
                'region'         => $dist->region,
                'zona'           => $dist->zona,
                'unidad'         => $dist->unidad ?? 'PATS',
                'nombre'         => $dist->nombre,
            ];
        }

        // Luego en franquicias
        $franq = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('activo', 1)
            ->first();

        if ($franq) {
            return [
                'tipo_origen'    => 'FRANQUICIA',
                'id_distribuidor'=> 0,
                'id_franquicia'  => $franq->id_franquicia,
                'pais'           => $franq->pais ?? 'México',
                'region'         => $franq->region ?? '',
                'zona'           => $franq->zona ?? '',
                'unidad'         => $franq->unidad ?? 'PATS',
                'nombre'         => $franq->nombre,
            ];
        }

        return null;
    }
}