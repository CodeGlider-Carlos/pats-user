<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use App\Mail\SolicitudDistribucionRecibida;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Hash, Log, Mail, Storage};
use Illuminate\Support\Str;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class DistribucionLinkController extends Controller
{
    private const ESTADOS = [
        'AGS'  => 'Aguascalientes',      'BCN'  => 'Baja California',
        'BCS'  => 'Baja California Sur', 'CAM'  => 'Campeche',
        'CHP'  => 'Chiapas',             'CHH'  => 'Chihuahua',
        'CDMX' => 'Ciudad de México',    'COA'  => 'Coahuila',
        'COL'  => 'Colima',              'DUR'  => 'Durango',
        'MEX'  => 'Estado de México',    'GTO'  => 'Guanajuato',
        'GRO'  => 'Guerrero',            'HGO'  => 'Hidalgo',
        'JAL'  => 'Jalisco',             'MIC'  => 'Michoacán',
        'MOR'  => 'Morelos',             'NAY'  => 'Nayarit',
        'NLE'  => 'Nuevo León',          'OAX'  => 'Oaxaca',
        'PUE'  => 'Puebla',              'QRO'  => 'Querétaro',
        'ROO'  => 'Quintana Roo',        'SLP'  => 'San Luis Potosí',
        'SIN'  => 'Sinaloa',             'SON'  => 'Sonora',
        'TAB'  => 'Tabasco',             'TAM'  => 'Tamaulipas',
        'TLAX' => 'Tlaxcala',            'VER'  => 'Veracruz',
        'YUC'  => 'Yucatán',             'ZAC'  => 'Zacatecas',
    ];

    private const DOCS_REQUERIDOS = [
        'doc_ine'       => 'INE',
        'doc_domicilio' => 'COMPROBANTE_DOMICILIO',
        'doc_cedula'    => 'CEDULA_FISCAL',
    ];

    private const DOCS_OPCIONALES = [
        'doc_caratula_bancaria' => 'CARATULA_BANCARIA',
        'doc_acta_constitutiva' => 'ACTA_CONSTITUTIVA',
        'doc_poder_notarial'    => 'PODER_NOTARIAL',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /distribucion/link/{token}  — Password gate
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404, 'El enlace no existe o ya no está disponible.');

        if (session("dist_link_auth_{$token}")) {
            return redirect()->route('dist.link.formulario', $token);
        }

        return view('pats.distribucion_link_password', compact('link', 'token'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /distribucion/link/{token}/auth  — Verificar contraseña
    // ─────────────────────────────────────────────────────────────────────────

    public function auth(Request $request, string $token): \Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404);

        if (! Hash::check((string) $request->input('password', ''), $link->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.'])->withInput();
        }

        session(["dist_link_auth_{$token}" => true]);

        return redirect()->route('dist.link.formulario', $token);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /distribucion/link/{token}/formulario
    // ─────────────────────────────────────────────────────────────────────────

    public function formulario(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404);

        if (! session("dist_link_auth_{$token}")) {
            return redirect()->route('dist.link.show', $token);
        }

        return view('pats.solicitud_distribuidor', [
            'link'               => $link,
            'linkMode'           => true,
            'token'              => $token,
            'conToken'           => false,
            'precioDistribucion' => (float) $link->amount,
            'estados'            => self::ESTADOS,
            'prefill'            => json_decode($link->prefill_json ?? 'null', true) ?: null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /distribucion/link/{token}/pre-validar
    // ─────────────────────────────────────────────────────────────────────────

    public function preValidar(Request $request, string $token): JsonResponse
    {
        if (! session("dist_link_auth_{$token}")) {
            return response()->json(['ok' => false, 'error' => 'Sesión no autorizada.'], 401);
        }

        $link = $this->findActiveLink($token);
        if ($link === null) {
            return response()->json(['ok' => false, 'error' => 'El enlace ya no está disponible.'], 404);
        }

        $this->applyPrefillToRequest($request, $link);

        $error = $this->validarDatos($request);
        if ($error !== null) return $error;

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /distribucion/link/{token}/guardar
    // ─────────────────────────────────────────────────────────────────────────

    public function guardar(Request $request, string $token): JsonResponse
    {
        if (! session("dist_link_auth_{$token}")) {
            return response()->json(['ok' => false, 'error' => 'Sesión no autorizada.'], 401);
        }

        $link = $this->findActiveLink($token);
        if ($link === null) {
            return response()->json(['ok' => false, 'error' => 'El enlace ya no está disponible o ya fue utilizado.'], 404);
        }

        // Idempotencia: reintento tras timeout de red
        $stripeIntentId = $this->clean($request->input('stripe_payment_intent_id'));
        if ($stripeIntentId !== '') {
            $pagoExistente = DB::table('pats_pagos')
                ->where('pasarela', 'stripe')
                ->where('referencia_pasarela', $stripeIntentId)
                ->first();
            if ($pagoExistente) {
                $existing = DB::table('pats_solicitudes_distribuidor')
                    ->where('id_solicitud', $pagoExistente->id_solicitud)
                    ->first();
                if ($existing) {
                    return response()->json([
                        'ok'           => true,
                        'id_solicitud' => $existing->id_solicitud,
                        'referencia'   => 'DIST-' . strtoupper(Str::random(6)) . '-' . $existing->id_solicitud,
                        'estatus'      => $existing->estatus,
                        'message'      => 'Solicitud procesada correctamente.',
                    ]);
                }
            }
        }

        return $this->procesarSolicitud($request, $link);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LÓGICA CENTRAL
    // ─────────────────────────────────────────────────────────────────────────

    private function procesarSolicitud(Request $request, object $link): JsonResponse
    {
        // ── 0. Merge prefill for any blank fields ─────────────────────────────
        $this->applyPrefillToRequest($request, $link);

        // ── 1. Inputs ─────────────────────────────────────────────────────────
        $pais     = $this->clean($request->input('pais'));
        $region   = strtoupper($this->clean($request->input('region')));
        $municipio= $this->clean($request->input('municipio'));
        $ciudad   = $this->clean($request->input('ciudad'));
        $calle    = $this->clean($request->input('calle'));
        $numExt   = $this->clean($request->input('num_ext'));
        $numInt   = $this->clean($request->input('num_int'));
        $cp       = $this->digits($request->input('cp'));
        $colonia  = $this->clean($request->input('colonia'));

        $direccion = implode(', ', array_filter([
            trim("{$calle} {$numExt}" . ($numInt ? " Int. {$numInt}" : '')),
            "Col. {$colonia}",
            "C.P. {$cp}",
            $municipio,
            self::ESTADOS[$region] ?? $region,
        ]));

        $tipoPersona     = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $apellidoPaterno = $this->clean($request->input('apellido_paterno'));
        $apellidoMaterno = $this->clean($request->input('apellido_materno'));
        $nombre          = $this->clean($request->input('nombre'));
        $razonSocial     = $this->clean($request->input('razon_social'));
        $rfc             = strtoupper($this->clean($request->input('rfc')));
        $fechaNacimiento = $this->clean($request->input('fecha_nacimiento'));
        $paisNacimiento  = $this->clean($request->input('pais_nacimiento'));
        $nacionalidad    = $this->clean($request->input('nacionalidad'));
        $ocupacion       = $this->clean($request->input('ocupacion'));
        $tipoIdentificacion       = $this->clean($request->input('tipo_identificacion'));
        $identificacionEmitidaPor = $this->clean($request->input('identificacion_emitida_por'));
        $numeroIdentificacion     = $this->clean($request->input('numero_identificacion'));
        $telefono        = $this->digits($request->input('telefono'));
        $correo          = strtolower($this->clean($request->input('correo')));

        $banco         = $this->clean($request->input('banco'));
        $numeroCuenta  = $this->clean($request->input('numero_cuenta'));
        $clabe         = $this->digits($request->input('clabe'));
        $titularCuenta = $this->clean($request->input('titular_cuenta'));

        $modalidad    = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $tipoOperacion= $this->clean($request->input('tipo_operacion', 'Adquisición de Derecho de Distribución'));
        $moneda       = $this->clean($request->input('moneda', 'MXN'));
        $plazoMeses   = (int) $request->input('plazo_meses', 0);
        $periodicidad = strtoupper($this->clean($request->input('periodicidad', 'MENSUAL')));
        $fechaInicio  = now()->toDateString();
        $fechaPrimerV = $this->clean($request->input('fecha_primer_vencimiento'));

        $selfieData = $request->input('selfie_data', '');
        $firmaData  = $request->input('firma_data', '');

        // ── 2. Validaciones ───────────────────────────────────────────────────
        $errorValidacion = $this->validarDatos($request);
        if ($errorValidacion !== null) return $errorValidacion;

        // ── 3. Stripe (solo si type_pay = card) ───────────────────────────────
        $stripeIntentId = $this->clean($request->input('stripe_payment_intent_id'));

        if ($link->type_pay === 'card') {
            if ($stripeIntentId === '') {
                return $this->err('El pago con tarjeta es requerido.');
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $intent = PaymentIntent::retrieve($stripeIntentId);

                if ($intent->status !== 'succeeded') {
                    return $this->err('El pago no fue procesado correctamente. Intenta de nuevo.');
                }

                $expectedCentavos = (int) round((float) $link->amount * 100);
                if ($intent->amount !== $expectedCentavos) {
                    Log::warning('DistribucionLink.Stripe.amountMismatch', [
                        'intent_id' => $stripeIntentId,
                        'expected'  => $expectedCentavos,
                        'received'  => $intent->amount,
                        'link_id'   => $link->id,
                    ]);
                    return $this->err('El monto del pago no coincide. Contacta a soporte.');
                }

                if (DB::table('pats_pagos')->where('pasarela', 'stripe')->where('referencia_pasarela', $stripeIntentId)->exists()) {
                    return $this->err('Este pago ya fue utilizado en otra solicitud.', 409);
                }

            } catch (\Throwable $e) {
                Log::error('DistribucionLink.Stripe.verify', ['error' => $e->getMessage(), 'intent' => $stripeIntentId]);
                return $this->err('No fue posible verificar el pago. Intenta de nuevo.');
            }
        }

        // ── 4. Precio y plan ──────────────────────────────────────────────────
        $valorTotal = (float) $link->amount;
        $enganche   = 0.00;
        $saldoFin   = $modalidad !== 'CONTADO' ? $valorTotal : 0.00;

        if ($modalidad === 'CONTADO') {
            $plazoMeses   = 0;
            $fechaPrimerV = null;
        }

        $esquemaPagos = null;
        if ($modalidad !== 'CONTADO' && $plazoMeses > 0 && $fechaPrimerV) {
            $esquemaPagos = $this->generarEsquema($valorTotal, $plazoMeses, $periodicidad, $fechaPrimerV);
        }

        // ── 5. Transacción ────────────────────────────────────────────────────
        DB::beginTransaction();

        try {
            $idSolicitud = DB::table('pats_solicitudes_distribuidor')->insertGetId([
                'token_referido'             => null,
                'id_franquicia'              => (int) $link->id_franquicia,
                'id_gestor'                  => 0,
                'id_distribuidor_generado'   => null,
                'user_solicita'              => null,
                'user_valida'                => null,
                'user_autoriza'              => null,
                'pais'                       => $pais,
                'region'                     => $region,
                'zona'                       => $municipio,
                'ciudad'                     => $ciudad ?: null,
                'unidad'                     => null,
                'apellido_paterno'           => $apellidoPaterno ?: null,
                'apellido_materno'           => $apellidoMaterno ?: null,
                'nombre'                     => $nombre,
                'tipo_persona'               => $tipoPersona,
                'razon_social'               => $razonSocial ?: null,
                'rfc'                        => $rfc ?: null,
                'fecha_nacimiento'           => $fechaNacimiento ?: null,
                'pais_nacimiento'            => $paisNacimiento ?: null,
                'nacionalidad'               => $nacionalidad ?: null,
                'ocupacion'                  => $ocupacion ?: null,
                'tipo_identificacion'        => $tipoIdentificacion ?: null,
                'identificacion_emitida_por' => $identificacionEmitidaPor ?: null,
                'numero_identificacion'      => $numeroIdentificacion ?: null,
                'telefono'                   => $telefono,
                'correo'                     => $correo,
                'direccion'                  => $direccion,
                'banco'                      => $banco ?: null,
                'numero_cuenta'              => $numeroCuenta ?: null,
                'clabe'                      => $clabe ?: null,
                'titular_cuenta'             => $titularCuenta ?: null,
                'modalidad_pago'             => $modalidad,
                'tipo_operacion'             => $tipoOperacion ?: null,
                'moneda'                     => $moneda ?: 'MXN',
                'valor_total'                => $valorTotal,
                'enganche'                   => $enganche,
                'saldo_financiado'           => $saldoFin,
                'plazo_meses'                => $plazoMeses,
                'periodicidad'               => $periodicidad,
                'fecha_inicio'               => $fechaInicio,
                'fecha_primer_vencimiento'   => $fechaPrimerV,
                'esquema_pagos_json'         => $esquemaPagos ? json_encode($esquemaPagos, JSON_UNESCAPED_UNICODE) : null,
                'estatus'                    => 'ENVIADA',
                'motivo_rechazo'             => null,
                'observaciones_admin'        => null,
                'observaciones_franquicia'   => null,
                'fecha_envio_contrato'       => null,
                'fecha_carga_firmado'        => null,
                'fecha_autorizacion'         => null,
                'fecha_conversion_alta'      => null,
                'activo'                     => 1,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ]);

            // Registrar pago en tabla centralizada
            DB::table('pats_pagos')->insert([
                'tipo_solicitud'      => 'distribuidor',
                'id_solicitud'        => $idSolicitud,
                'pasarela'            => $link->type_pay === 'card' ? 'stripe' : 'free',
                'referencia_pasarela' => $stripeIntentId ?: ('FREE-' . $idSolicitud),
                'estatus'             => $link->type_pay === 'card' ? 'succeeded' : 'free',
                'monto'               => (float) $link->amount,
                'moneda'              => 'MXN',
                'metadata_json'       => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Documentos requeridos
            $baseDir = "solicitudes/distribuidor/{$idSolicitud}";
            foreach (self::DOCS_REQUERIDOS as $campo => $tipoDoc) {
                $this->guardarDocumento($request->file($campo), $idSolicitud, $tipoDoc, $baseDir);
            }
            foreach (self::DOCS_OPCIONALES as $campo => $tipoDoc) {
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $this->guardarDocumento($request->file($campo), $idSolicitud, $tipoDoc, $baseDir);
                }
            }

            // Biometría
            $previewRow = ['id_solicitud' => $idSolicitud, 'created_at' => now(), 'updated_at' => now()];
            $selfieInfo = $this->guardarBase64Imagen($selfieData, $baseDir, 'selfie');
            $firmaInfo  = $this->guardarBase64Imagen($firmaData,  $baseDir, 'firma');

            $previewRow['selfie_path']   = $selfieInfo['path'] ?? null;
            $previewRow['selfie_mime']   = $selfieInfo['mime'] ?? null;
            $previewRow['selfie_kb']     = $selfieInfo['kb']   ?? null;
            $previewRow['firma_path']    = $firmaInfo['path']  ?? null;
            $previewRow['firma_mime']    = $firmaInfo['mime']  ?? null;
            $previewRow['firma_kb']      = $firmaInfo['kb']    ?? null;
            $previewRow['contrato_path'] = 'static/contrato_dist.pdf';
            $previewRow['contrato_mime'] = 'application/pdf';
            $previewRow['contrato_kb']   = 662;

            DB::table('pats_preview_dist')->insert($previewRow);

            // Historial
            DB::table('pats_solicitudes_distribuidor_historial')->insert([
                'id_solicitud'     => $idSolicitud,
                'evento_tipo'      => 'solicitud_enviada',
                'estatus_anterior' => null,
                'estatus_nuevo'    => 'ENVIADA',
                'payload_json'     => json_encode([
                    'nombre'    => $nombre,
                    'correo'    => $correo,
                    'region'    => $region,
                    'link_id'   => $link->id,
                    'type_pay'  => $link->type_pay,
                    'amount'    => $link->amount,
                ], JSON_UNESCAPED_UNICODE),
                'user_evento'  => null,
                'fecha_evento' => now(),
                'created_at'   => now(),
            ]);

            // Desactivar link
            DB::table('distribuidor_links')
                ->where('id', $link->id)
                ->update(['id_solicitud' => $idSolicitud, 'active' => 0, 'updated_at' => now()]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DistribucionLink.procesarSolicitud', ['error' => $e->getMessage(), 'correo' => $correo ?? '']);
            return response()->json(['ok' => false, 'error' => 'No fue posible guardar la solicitud. Intenta de nuevo.'], 500);
        }

        $referencia = 'DIST-' . strtoupper(Str::random(6)) . '-' . $idSolicitud;

        try {
            Mail::to($correo)->send(new SolicitudDistribucionRecibida(
                nombreSolicitante: trim("{$apellidoPaterno} {$nombre}"),
                referencia:        $referencia,
                correo:            $correo,
            ));
        } catch (\Throwable $e) {
            Log::warning('DistribucionLink.correoConfirmacion', ['error' => $e->getMessage(), 'correo' => $correo]);
        }

        session()->forget("dist_link_auth_{$link->token}");

        return response()->json([
            'ok'           => true,
            'id_solicitud' => $idSolicitud,
            'referencia'   => $referencia,
            'estatus'      => 'ENVIADA',
            'message'      => 'Solicitud enviada correctamente.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function applyPrefillToRequest(Request $request, object $link): void
    {
        if (empty($link->prefill_json)) return;
        $prefill = json_decode($link->prefill_json, true);
        if (! is_array($prefill)) return;

        $merge = [];
        foreach ($prefill as $key => $value) {
            if ($value !== null && $value !== '' && $this->clean($request->input($key)) === '') {
                $merge[$key] = $value;
            }
        }
        if (! empty($merge)) {
            $request->merge($merge);
        }
    }

    private function findActiveLink(string $token): ?object
    {
        return DB::table('distribuidor_links')
            ->where('token', $token)
            ->where('active', 1)
            ->where('id_solicitud', 0)
            ->first();
    }

    private function validarDatos(Request $request): ?JsonResponse
    {
        $correo    = strtolower($this->clean($request->input('correo')));
        $telefono  = $this->digits($request->input('telefono'));
        $nombre    = $this->clean($request->input('nombre'));
        $apellido  = $this->clean($request->input('apellido_paterno'));
        $pais      = $this->clean($request->input('pais'));
        $region    = $this->clean($request->input('region'));
        $municipio = $this->clean($request->input('municipio'));
        $ciudad    = $this->clean($request->input('ciudad'));
        $calle     = $this->clean($request->input('calle'));
        $numExt    = $this->clean($request->input('num_ext'));
        $cp        = $this->digits($request->input('cp'));
        $colonia   = $this->clean($request->input('colonia'));
        $clabe     = $this->digits($request->input('clabe'));

        $tipoPersona  = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $razonSocial  = $this->clean($request->input('razon_social'));
        $modalidad    = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses   = (int) $request->input('plazo_meses', 0);
        $fechaPrimerV = $this->clean($request->input('fecha_primer_vencimiento'));

        $fechaNacimiento          = $this->clean($request->input('fecha_nacimiento'));
        $paisNacimiento           = $this->clean($request->input('pais_nacimiento'));
        $nacionalidad             = $this->clean($request->input('nacionalidad'));
        $ocupacion                = $this->clean($request->input('ocupacion'));
        $tipoIdentificacion       = $this->clean($request->input('tipo_identificacion'));
        $identificacionEmitidaPor = $this->clean($request->input('identificacion_emitida_por'));
        $numeroIdentificacion     = $this->clean($request->input('numero_identificacion'));

        foreach (compact(
            'pais', 'region', 'municipio', 'ciudad', 'calle', 'numExt', 'cp', 'colonia',
            'apellido', 'nombre', 'telefono', 'correo',
            'fechaNacimiento', 'paisNacimiento', 'nacionalidad', 'ocupacion',
            'tipoIdentificacion', 'identificacionEmitidaPor', 'numeroIdentificacion'
        ) as $campo => $valor) {
            if ($valor === '') return $this->err("El campo '{$campo}' es obligatorio.");
        }

        if (! filter_var($correo, FILTER_VALIDATE_EMAIL))      return $this->err('El correo electrónico no es válido.');
        if (strlen($telefono) !== 10)                          return $this->err('El teléfono debe tener 10 dígitos.');
        if ($clabe !== '' && strlen($clabe) !== 18)            return $this->err('La CLABE debe tener 18 dígitos.');
        if (! in_array($tipoPersona, ['FISICA', 'MORAL'], true)) return $this->err('Tipo de persona no válido.');
        if ($tipoPersona === 'MORAL' && $razonSocial === '')   return $this->err('Para persona moral es obligatoria la razón social.');
        if (! in_array($modalidad, ['CONTADO', 'DIFERIDO'], true)) return $this->err('Modalidad de pago no válida.');
        if ($modalidad !== 'CONTADO') {
            if ($plazoMeses <= 0)   return $this->err('Indica el número de meses.');
            if ($fechaPrimerV === '') return $this->err('La fecha del primer vencimiento es obligatoria.');
        }

        $etiquetas = ['doc_ine' => 'INE / IFE', 'doc_domicilio' => 'Comprobante de domicilio', 'doc_cedula' => 'Cédula fiscal'];
        foreach (array_keys(self::DOCS_REQUERIDOS) as $campo) {
            if (! $request->hasFile($campo) || ! $request->file($campo)->isValid()) {
                return $this->err("El documento '{$etiquetas[$campo]}' es obligatorio.");
            }
        }

        if ($tipoPersona === 'MORAL') {
            $tieneActa  = $request->hasFile('doc_acta_constitutiva') && $request->file('doc_acta_constitutiva')->isValid();
            $tienePoder = $request->hasFile('doc_poder_notarial')    && $request->file('doc_poder_notarial')->isValid();
            if (! $tieneActa && ! $tienePoder) return $this->err('Para persona moral sube al menos el acta constitutiva o el poder notarial.');
        }

        if (! str_starts_with($request->input('selfie_data', ''), 'data:image')) return $this->err('La selfie del titular es obligatoria.');
        if (! str_starts_with($request->input('firma_data',  ''), 'data:image')) return $this->err('La firma del titular es obligatoria.');

        if (DB::table('pats_distribuidores')->where('correo', $correo)->exists()) {
            return $this->err('Ya existe un registro con ese correo.', 409);
        }
        if (DB::table('pats_solicitudes_distribuidor')->where('correo', $correo)->whereNotIn('estatus', ['RECHAZADA', 'CONVERTIDA_ALTA'])->exists()) {
            return $this->err('Ya existe una solicitud activa con ese correo.', 409);
        }

        return null;
    }

    private function guardarBase64Imagen(string $dataUrl, string $baseDir, string $prefix): ?array
    {
        if (! preg_match('/^data:(image\/[a-z+]+);base64,(.+)$/s', $dataUrl, $m)) return null;
        $mime = $m[1];
        $raw  = base64_decode($m[2], true);
        if ($raw === false) return null;

        $ext  = match ($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', default => 'bin' };
        $path = "{$baseDir}/" . now()->format('YmdHis') . '_' . Str::random(8) . "__{$prefix}.{$ext}";
        Storage::disk('local')->put("private/{$path}", $raw);

        return ['path' => "private/{$path}", 'mime' => $mime, 'kb' => (int) ceil(strlen($raw) / 1024)];
    }

    private function guardarDocumento(\Illuminate\Http\UploadedFile $file, int $idSolicitud, string $tipoDoc, string $baseDir): void
    {
        $ext      = preg_replace('/[^a-z0-9]/i', '', strtolower($file->getClientOriginalExtension())) ?: 'bin';
        $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $ext;
        $path     = "{$baseDir}/{$filename}";
        Storage::disk('local')->put("private/{$path}", file_get_contents($file->getRealPath()));

        DB::table('pats_solicitudes_distribuidor_documentos')
            ->where('id_solicitud', $idSolicitud)->where('tipo_documento', $tipoDoc)->where('vigente', 1)
            ->update(['vigente' => 0, 'updated_at' => now()]);

        DB::table('pats_solicitudes_distribuidor_documentos')->insert([
            'id_solicitud'            => $idSolicitud,
            'tipo_documento'          => $tipoDoc,
            'archivo_path'            => "private/{$path}",
            'archivo_nombre_original' => $file->getClientOriginalName(),
            'mime_type'               => $file->getMimeType(),
            'size_kb'                 => (int) ceil($file->getSize() / 1024),
            'vigente'                 => 1,
            'observaciones'           => null,
            'user_alta'               => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    }

    private function generarEsquema(float $total, int $plazo, string $periodicidad, string $fechaPrimerPago): array
    {
        $base      = Carbon::parse($fechaPrimerPago);
        $montoBase = floor(($total / $plazo) * 100) / 100;
        $acum      = 0.0;
        $pagos     = [];

        for ($i = 0; $i < $plazo; $i++) {
            $fecha = match ($periodicidad) {
                'SEMANAL'   => $base->copy()->addWeeks($i),
                'QUINCENAL' => $base->copy()->addDays($i * 15),
                'UNICA'     => $base->copy(),
                default     => $base->copy()->addMonthsNoOverflow($i),
            };
            $monto  = $montoBase;
            $acum  += $monto;
            if ($i === $plazo - 1) $monto = round($total - ($acum - $monto), 2);
            $pagos[] = ['parcialidad' => $i + 1, 'fecha' => $fecha->toDateString(), 'monto' => round($monto, 2)];
            if ($periodicidad === 'UNICA') break;
        }

        return $pagos;
    }

    private function clean(mixed $v): string  { return trim((string) ($v ?? '')); }
    private function digits(mixed $v): string  { return preg_replace('/\D+/', '', (string) ($v ?? '')); }
    private function err(string $msg, int $status = 422): JsonResponse { return response()->json(['ok' => false, 'error' => $msg], $status); }
}
