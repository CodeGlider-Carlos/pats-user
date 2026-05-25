<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Str;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 * SolicitudFranquiciaController
 *
 * Módulo 1 — Alta sin token: landing pública.
 *   GET  /franquicia/solicitud               → showPublico()
 *   POST /franquicia/solicitud               → guardarPublico()
 *   GET  /franquicia/solicitud/confirmacion  → confirmacion()
 *
 * Módulo 2 — Alta con token: dentro del hospital, con sesión.
 *   GET  /pats/franquicia?t=TOKEN            → show()
 *   POST /pats/franquicia/orden              → generarOrden()
 */
class SolicitudFranquiciaController extends Controller
{
    // ─── Constantes ──────────────────────────────────────────────────────────

    private const PRECIO_DEFAULT = 999999.00;

    private const MODALIDADES_VALIDAS = ['CONTADO', 'DIFERIDO'];

    private const PERIODICIDADES_VALIDAS = ['MENSUAL', 'QUINCENAL', 'SEMANAL', 'UNICA'];

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

    private const ESTADOS = [
        'AGS'  => 'Aguascalientes',    'BCN'  => 'Baja California',
        'BCS'  => 'Baja California Sur','CAM'  => 'Campeche',
        'CHP'  => 'Chiapas',           'CHH'  => 'Chihuahua',
        'CDMX' => 'Ciudad de México',  'COA'  => 'Coahuila',
        'COL'  => 'Colima',            'DUR'  => 'Durango',
        'MEX'  => 'Estado de México',  'GTO'  => 'Guanajuato',
        'GRO'  => 'Guerrero',          'HGO'  => 'Hidalgo',
        'JAL'  => 'Jalisco',           'MIC'  => 'Michoacán',
        'MOR'  => 'Morelos',           'NAY'  => 'Nayarit',
        'NLE'  => 'Nuevo León',        'OAX'  => 'Oaxaca',
        'PUE'  => 'Puebla',            'QRO'  => 'Querétaro',
        'QR'   => 'Quintana Roo',      'SLP'  => 'San Luis Potosí',
        'SIN'  => 'Sinaloa',           'SON'  => 'Sonora',
        'TAB'  => 'Tabasco',           'TAM'  => 'Tamaulipas',
        'TLX'  => 'Tlaxcala',          'VER'  => 'Veracruz',
        'YUC'  => 'Yucatán',           'ZAC'  => 'Zacatecas',
    ];

    // ────────────────────────────────��────────────────────────────────────────
    //  MÓDULO 1 — SIN TOKEN (Landing pública)
    // ──────────────────────────────────────────────────────────────────��──────

    public function showPublico(Request $request): \Illuminate\View\View
    {
        $token = trim((string) $request->query('t', ''));

        return view('pats.solicitud_franquicia', [
            'conToken'           => $token !== '',
            'token'              => $token,
            'precioFranquicia' => $this->getPrecioFranquicia(),
            'estados'            => self::ESTADOS,
        ]);
    }

    public function preValidar(Request $request): JsonResponse
    {
        $error = $this->validarDatos($request);
        if ($error !== null) return $error;
        return response()->json(['ok' => true]);
    }

    public function guardarPublico(Request $request): JsonResponse
    {
        $token = trim((string) $request->input('public_token', '')) ?: null;
        return $this->procesarSolicitud($request, $token);
    }

    public function confirmacion(Request $request)
    {
        $ref    = $this->clean($request->query('ref', ''));
        $nombre = $this->clean($request->query('nombre', ''));
        $correo = $this->clean($request->query('correo', ''));

        return view('pats.solicitud_distribuidor_success', compact('ref', 'nombre', 'correo'));
    }

    // ──────────────────────────────────────────────────────���──────────────────
    //  MÓDULO 2 — CON TOKEN
    // ────────────────────────────────────────────────��────────────────────────

    public function show(Request $request): \Illuminate\View\View
    {
        $token = trim((string) $request->query('t', ''));
        abort_if($token === '', 400, 'Token requerido.');

        $ctx = $this->resolveToken($token);
        abort_if($ctx === null, 404, 'El enlace no es válido o ya no está activo.');

        $region = strtoupper(trim((string) ($ctx['region'] ?? '')));

        return view('pats.solicitud_franquicia', [
            'conToken'         => true,
            'ctx'              => $ctx,
            'precioFranquicia' => $this->getPrecioFranquicia(),
            'estados'          => self::ESTADOS,
            'token'            => $token,
            'actorTipo'        => $ctx['actor_tipo'],
            'region'           => $region,
            'estadoNombre'     => self::ESTADOS[$region] ?? $region,
            'zona'             => (string) ($ctx['zona'] ?? ''),
            'unidad'           => (string) ($ctx['unidad'] ?? ''),
            'pais'             => (string) ($ctx['pais'] ?? 'México'),
        ]);
    }


    // ─────────────────────────────────────────────────────────────────────────
    //  VALIDACIÓN COMPARTIDA
    // ─────────────────────────────────────────────────────────────────────────

    private function validarDatos(Request $request): ?JsonResponse
    {
        $pais        = $this->clean($request->input('pais'));
        $region      = strtoupper($this->clean($request->input('region')));
        $municipio   = $this->clean($request->input('municipio'));
        $calle       = $this->clean($request->input('calle'));
        $numExt      = $this->clean($request->input('num_ext'));
        $cp          = $this->digits($request->input('cp'));
        $colonia     = $this->clean($request->input('colonia'));
        $tipoPersona = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $nombre      = $this->clean($request->input('nombre'));
        $razonSocial = $this->clean($request->input('razon_social'));
        $telefono    = $this->digits($request->input('telefono'));
        $correo      = strtolower($this->clean($request->input('correo')));
        $clabe       = $this->digits($request->input('clabe'));
        $modalidad   = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses  = (int) $request->input('plazo_meses', 0);
        $fechaInicio = $this->clean($request->input('fecha_inicio'));
        $fechaPrimerV= $this->clean($request->input('fecha_primer_vencimiento'));

        foreach (compact('pais','region','municipio','calle','numExt','cp','colonia','nombre','telefono','correo') as $campo => $valor) {
            if ($valor === '') return $this->err("El campo '{$campo}' es obligatorio.");
        }
        if (! filter_var($correo, FILTER_VALIDATE_EMAIL)) return $this->err('El correo electrónico no es válido.');
        if (strlen($telefono) !== 10) return $this->err('El teléfono debe tener 10 dígitos.');
        if ($clabe !== '' && ! $this->validarClabe($clabe)) return $this->err('La CLABE interbancaria no es válida.');
        if (! in_array($tipoPersona, ['FISICA', 'MORAL'], true)) return $this->err('Tipo de persona no válido.');
        if ($tipoPersona === 'MORAL' && $razonSocial === '') return $this->err('Para persona moral es obligatoria la razón social.');
        if (! in_array($modalidad, self::MODALIDADES_VALIDAS, true)) return $this->err('Modalidad de pago no válida.');
        if ($fechaInicio === '') return $this->err('La fecha de inicio es obligatoria.');
        if ($modalidad !== 'CONTADO') {
            if ($plazoMeses <= 0) return $this->err('Indica el número de meses para el financiamiento.');
            if ($fechaPrimerV === '') return $this->err('La fecha del primer vencimiento es obligatoria para pago diferido.');
        }

        $etiquetas = ['doc_ine' => 'INE / IFE', 'doc_domicilio' => 'Comprobante de domicilio', 'doc_cedula' => 'Cédula fiscal'];
        foreach (array_keys(self::DOCS_REQUERIDOS) as $campo) {
            if (! $request->hasFile($campo) || ! $request->file($campo)->isValid()) {
                return $this->err("El documento '{$etiquetas[$campo]}' es obligatorio.");
            }
        }
        if ($tipoPersona === 'MORAL') {
            $tieneActa  = $request->hasFile('doc_acta_constitutiva')  && $request->file('doc_acta_constitutiva')->isValid();
            $tienePoder = $request->hasFile('doc_poder_notarial')     && $request->file('doc_poder_notarial')->isValid();
            if (! $tieneActa && ! $tienePoder) return $this->err('Para persona moral sube al menos el acta constitutiva o el poder notarial.');
        }
        if (! str_starts_with($request->input('selfie_data', ''), 'data:image')) return $this->err('La selfie del titular es obligatoria.');
        if (! str_starts_with($request->input('firma_data',  ''), 'data:image')) return $this->err('La firma del titular es obligatoria.');

        if (DB::table('pats_franquicias')->where('correo', $correo)->exists()) {
            return $this->err('Ya existe una franquicia registrada con ese correo electrónico.', 409);
        }
        if (DB::table('pats_solicitudes_franquicia')->where('correo', $correo)->whereNotIn('estatus', ['RECHAZADA', 'CONVERTIDA_ALTA'])->exists()) {
            return $this->err('Ya existe una solicitud activa con ese correo electrónico.', 409);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LÓGICA CENTRAL
    // ─────────────────────────────────────────────────────────────────────────

    private function procesarSolicitud(
        Request $request,
        ?string $tokenReferido = null
    ): JsonResponse {

        // ── 1 + 2 + 3. Validaciones y duplicados ─────────────────────────────
        $errorValidacion = $this->validarDatos($request);
        if ($errorValidacion !== null) return $errorValidacion;

        // ── Inputs para inserción ─────────────────────────────────────────────
        $pais         = $this->clean($request->input('pais'));
        $region       = strtoupper($this->clean($request->input('region')));
        $municipio    = $this->clean($request->input('municipio'));
        $calle        = $this->clean($request->input('calle'));
        $numExt       = $this->clean($request->input('num_ext'));
        $numInt       = $this->clean($request->input('num_int'));
        $cp           = $this->digits($request->input('cp'));
        $colonia      = $this->clean($request->input('colonia'));
        $direccion    = implode(', ', array_filter([
            trim("{$calle} {$numExt}" . ($numInt ? " Int. {$numInt}" : '')),
            "Col. {$colonia}", "C.P. {$cp}", $municipio, self::ESTADOS[$region] ?? $region,
        ]));
        $tipoPersona  = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $nombre       = $this->clean($request->input('nombre'));
        $razonSocial  = $this->clean($request->input('razon_social'));
        $rfc          = strtoupper($this->clean($request->input('rfc')));
        $telefono     = $this->digits($request->input('telefono'));
        $correo       = strtolower($this->clean($request->input('correo')));
        $banco        = $this->clean($request->input('banco'));
        $numeroCuenta = $this->clean($request->input('numero_cuenta'));
        $clabe        = $this->digits($request->input('clabe'));
        $titularCuenta= $this->clean($request->input('titular_cuenta'));
        $modalidad    = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses   = (int) $request->input('plazo_meses', 0);
        $periodicidad = strtoupper($this->clean($request->input('periodicidad', 'MENSUAL')));
        $fechaInicio  = $this->clean($request->input('fecha_inicio'));
        $fechaPrimerV = $this->clean($request->input('fecha_primer_vencimiento'));
        $selfieData   = $request->input('selfie_data', '');
        $firmaData    = $request->input('firma_data', '');

        // ── 4. Verificar pago Stripe ──────────────────────────────────────────

        $stripeIntentId = $this->clean($request->input('stripe_payment_intent_id'));

        if ($stripeIntentId === '') {
            return $this->err('El pago con tarjeta es requerido.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // El ID puede ser "pi_xxx" o "pi_xxx|pi_yyy" cuando se dividió el cobro
        $intentIds        = array_filter(array_map('trim', explode('|', $stripeIntentId)));
        $expectedCentavos = (int) round($this->getPrecioFranquicia() * 100);
        $totalVerificado  = 0;
        $intentData       = []; // [iid => monto_pesos]

        try {
            foreach ($intentIds as $iid) {
                $intent = PaymentIntent::retrieve($iid);

                if ($intent->status !== 'succeeded') {
                    return $this->err('El pago no fue procesado correctamente. Intenta de nuevo.');
                }

                $totalVerificado += $intent->amount;

                if (DB::table('pats_pagos')->where('pasarela', 'stripe')->where('referencia_pasarela', $iid)->exists()) {
                    return $this->err('Este pago ya fue utilizado en otra solicitud.', 409);
                }

                $intentData[$iid] = round($intent->amount / 100, 2);
            }

            if ($totalVerificado !== $expectedCentavos) {
                Log::warning('Stripe amount mismatch (franquicia)', [
                    'intents'  => $stripeIntentId,
                    'expected' => $expectedCentavos,
                    'received' => $totalVerificado,
                ]);
                return $this->err('El monto del pago no coincide. Contacta a soporte.');
            }

        } catch (\Throwable $e) {
            Log::error('Stripe.verify (franquicia)', ['error' => $e->getMessage(), 'intent' => $stripeIntentId]);
            return $this->err('No fue posible verificar el pago. Intenta de nuevo.');
        }

        // ── 5. Precio y plan ──────────────────────────────────────────────────

        $valorTotal  = $this->getPrecioFranquicia();
        $enganche    = 0.00;
        $saldoFin    = $modalidad !== 'CONTADO' ? $valorTotal : 0.00;

        if ($modalidad === 'CONTADO') {
            $plazoMeses   = 0;
            $fechaPrimerV = null;
        }

        $esquemaPagos = null;
        if ($modalidad !== 'CONTADO' && $plazoMeses > 0 && $fechaPrimerV) {
            $esquemaPagos = $this->generarEsquema($valorTotal, $plazoMeses, $periodicidad, $fechaPrimerV);
        }

        // ── 6. Transacción ────────────────────────────────────────────────────

        DB::beginTransaction();

        try {
            $idSolicitud = DB::table('pats_solicitudes_franquicia')->insertGetId([
                'id_gestor'                  => 0,
                'id_franquicia_generada'     => null,
                'user_solicita'              => auth()->id(),
                'user_valida'                => null,
                'user_autoriza'              => null,
                'pais'                       => $pais,
                'region'                     => $region,
                'zona'                       => $municipio,
                'unidad'                     => null,
                'nombre_comercial'           => $tipoPersona === 'MORAL' ? ($razonSocial ?: $nombre) : $nombre,
                'nombre_titular'             => $nombre,
                'tipo_persona'               => $tipoPersona,
                'razon_social'               => $razonSocial ?: null,
                'rfc'                        => $rfc ?: null,
                'telefono'                   => $telefono,
                'correo'                     => $correo,
                'direccion'                  => $direccion,
                'banco'                      => $banco ?: null,
                'numero_cuenta'              => $numeroCuenta ?: null,
                'clabe'                      => $clabe ?: null,
                'titular_cuenta'             => $titularCuenta ?: null,
                'modalidad_pago'             => $modalidad,
                'valor_total'                => $valorTotal,
                'enganche'                   => $enganche,
                'saldo_financiado'           => $saldoFin,
                'plazo_meses'                => $plazoMeses,
                'periodicidad'               => $periodicidad,
                'fecha_inicio'               => $fechaInicio,
                'fecha_primer_vencimiento'   => $fechaPrimerV,
                'contrato_admin_path'        => null,
                'contrato_firmado_path'      => null,
                'estatus'                    => 'ENVIADA',
                'motivo_rechazo'             => null,
                'observaciones_admin'        => null,
                'observaciones_solicitante'  => null,
                'fecha_envio_contrato'       => null,
                'fecha_carga_firmado'        => null,
                'fecha_autorizacion'         => null,
                'fecha_conversion_alta'      => null,
                'activo'                     => 1,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ]);

            // Registrar cada intent en pats_pagos (soporta cobros divididos)
            foreach ($intentData as $iid => $montoIntent) {
                DB::table('pats_pagos')->insert([
                    'tipo_solicitud'      => 'franquicia',
                    'id_solicitud'        => $idSolicitud,
                    'pasarela'            => 'stripe',
                    'referencia_pasarela' => $iid,
                    'estatus'             => 'succeeded',
                    'monto'               => $montoIntent,
                    'moneda'              => 'MXN',
                    'metadata_json'       => null,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            $baseDir = "solicitudes/franquicia/{$idSolicitud}";

            foreach (self::DOCS_REQUERIDOS as $campo => $tipoDoc) {
                $this->guardarDocumento($request->file($campo), $idSolicitud, $tipoDoc, $baseDir);
            }

            foreach (self::DOCS_OPCIONALES as $campo => $tipoDoc) {
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $this->guardarDocumento($request->file($campo), $idSolicitud, $tipoDoc, $baseDir);
                }
            }

            $previewRow = ['id_solicitud' => $idSolicitud, 'created_at' => now(), 'updated_at' => now()];

            $selfieInfo = $this->guardarBase64Imagen($selfieData, $baseDir, 'selfie');
            $firmaInfo  = $this->guardarBase64Imagen($firmaData,  $baseDir, 'firma');

            $previewRow['selfie_path']  = $selfieInfo['path']  ?? null;
            $previewRow['selfie_mime']  = $selfieInfo['mime']  ?? null;
            $previewRow['selfie_kb']    = $selfieInfo['kb']    ?? null;
            $previewRow['firma_path']   = $firmaInfo['path']   ?? null;
            $previewRow['firma_mime']   = $firmaInfo['mime']   ?? null;
            $previewRow['firma_kb']     = $firmaInfo['kb']     ?? null;

            $previewRow['contrato_path'] = 'static/contrato_franq.pdf';
            $previewRow['contrato_mime'] = 'application/pdf';
            $previewRow['contrato_kb']   = 662;

            DB::table('pats_preview_franq')->insert($previewRow);

            $this->insertarHistorial(
                idSolicitud:     $idSolicitud,
                eventoTipo:      'solicitud_enviada',
                estatusAnterior: null,
                estatusNuevo:    'ENVIADA',
                payload: [
                    'nombre'        => $nombre,
                    'correo'        => $correo,
                    'telefono'      => $telefono,
                    'region'        => $region,
                    'modalidad'     => $modalidad,
                    'valor_total'    => $valorTotal,
                    'plazo_meses'    => $plazoMeses,
                ]
            );

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SolicitudFranquicia.procesarSolicitud', [
                'error'  => $e->getMessage(),
                'correo' => $correo,
            ]);
            return response()->json([
                'ok'    => false,
                'error' => 'No fue posible guardar la solicitud. Intenta de nuevo.',
            ], 500);
        }

        $referencia = 'FRANQ-' . strtoupper(Str::random(6)) . '-' . $idSolicitud;

        return response()->json([
            'ok'           => true,
            'id_solicitud' => $idSolicitud,
            'referencia'   => $referencia,
            'estatus'      => 'ENVIADA',
            'message'      => 'Solicitud enviada correctamente. Revisaremos en 24–48 horas hábiles.',
        ]);
    }

    // ─────────────────���─────────────────────────────���─────────────────────────
    //  HELPERS: BIOMETRÍA
    // ────────────────────────────────────────��─────────────────────────────���──

    private function guardarBase64Imagen(string $dataUrl, string $baseDir, string $prefix): ?array
    {
        if (! preg_match('/^data:(image\/[a-z+]+);base64,(.+)$/s', $dataUrl, $m)) {
            return null;
        }

        $mime  = $m[1];
        $raw   = base64_decode($m[2], true);
        if ($raw === false) return null;

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'bin',
        };

        $filename = now()->format('YmdHis') . '_' . Str::random(8) . "__{$prefix}.{$ext}";
        $path     = "{$baseDir}/{$filename}";

        Storage::disk('local')->put("private/{$path}", $raw);

        return ['path' => "private/{$path}", 'mime' => $mime, 'kb' => (int) ceil(strlen($raw) / 1024)];
    }

    // ───────────────────────────────────────────────────���─────────────────────
    //  HELPERS: DOCUMENTOS
    // ─────────────────────────────────────────────────────────────────────────

    private function guardarDocumento(
        \Illuminate\Http\UploadedFile $file,
        int $idSolicitud,
        string $tipoDoc,
        string $baseDir
    ): void {
        $extension = strtolower($file->getClientOriginalExtension());
        $safeExt   = preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'bin';
        $filename  = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $safeExt;
        $path      = "{$baseDir}/{$filename}";

        Storage::disk('local')->put("private/{$path}", file_get_contents($file->getRealPath()));

        DB::table('pats_solicitudes_franquicia_documentos')
            ->where('id_solicitud', $idSolicitud)
            ->where('tipo_documento', $tipoDoc)
            ->where('vigente', 1)
            ->update(['vigente' => 0, 'updated_at' => now()]);

        DB::table('pats_solicitudes_franquicia_documentos')->insert([
            'id_solicitud'            => $idSolicitud,
            'tipo_documento'          => $tipoDoc,
            'archivo_path'            => "private/{$path}",
            'archivo_nombre_original' => $file->getClientOriginalName(),
            'mime_type'               => $file->getMimeType(),
            'size_kb'                 => (int) ceil($file->getSize() / 1024),
            'vigente'                 => 1,
            'observaciones'           => null,
            'user_alta'               => auth()->id(),
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    }

    // ─────────────────────────────────────���───────────────────────────────────
    //  HELPERS: HISTORIAL
    // ────────────────────────────────────────────────────────────────────���────

    private function insertarHistorial(
        int $idSolicitud,
        string $eventoTipo,
        ?string $estatusAnterior,
        ?string $estatusNuevo,
        array $payload = []
    ): void {
        DB::table('pats_solicitudes_franquicia_historial')->insert([
            'id_solicitud'     => $idSolicitud,
            'evento_tipo'      => $eventoTipo,
            'estatus_anterior' => $estatusAnterior,
            'estatus_nuevo'    => $estatusNuevo,
            'payload_json'     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'user_evento'      => auth()->id(),
            'fecha_evento'     => now(),
            'created_at'       => now(),
        ]);
    }

    // ──────────────────────────────────────────────────���──────────────────────
    //  HELPERS: TOKEN
    // ─────────────────────────────────────────────────────────────────────��───

    private function resolveToken(string $token): ?array
    {
        // 1. Gestor
        $gestor = DB::table('pats_gestores as g')
            ->where('g.public_checkout_token', $token)
            ->where('g.public_checkout_activo', 1)
            ->where('g.activo', 1)
            ->select(['g.id_gestor', 'g.nombre_gestor as nombre_actor', 'g.region', 'g.correo'])
            ->first();

        if ($gestor) {
            $franquicia = DB::table('pats_franquicias as f')
                ->where('f.activo', 1)
                ->where('f.region', $gestor->region)
                ->orderBy('f.id_franquicia')
                ->select(['f.id_franquicia','f.zona','f.unidad','f.pais','f.nombre_franquicia'])
                ->first();

            return [
                'actor_tipo'       => 'GESTOR',
                'id_gestor'        => (int) $gestor->id_gestor,
                'id_franquicia'    => (int) ($franquicia->id_franquicia ?? 0),
                'nombre_actor'     => $gestor->nombre_actor,
                'region'           => $gestor->region,
                'zona'             => $franquicia->zona ?? null,
                'unidad'           => $franquicia->unidad ?? null,
                'pais'             => $franquicia->pais ?? 'México',
                'nombre_franquicia'=> $franquicia->nombre_franquicia ?? null,
            ];
        }

        // 2. Franquicia directa
        $franquicia = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->select(['id_franquicia','nombre_franquicia','region','zona','unidad','pais'])
            ->first();

        if ($franquicia) {
            return [
                'actor_tipo'       => 'FRANQUICIA',
                'id_gestor'        => 0,
                'id_franquicia'    => (int) $franquicia->id_franquicia,
                'nombre_actor'     => $franquicia->nombre_franquicia,
                'region'           => $franquicia->region,
                'zona'             => $franquicia->zona,
                'unidad'           => $franquicia->unidad,
                'pais'             => $franquicia->pais ?? 'México',
                'nombre_franquicia'=> $franquicia->nombre_franquicia,
            ];
        }

        return null;
    }

    // ────────────────────────────────────────────────────────���────────────────
    //  HELPERS: FINANZAS
    // ──────────────────────────────────────────────────────────────────────��──

    private function getPrecioFranquicia(): float
    {
        $precio = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = 'franquicia'")
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($precio ?? self::PRECIO_DEFAULT);
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

            if ($i === $plazo - 1) {
                $monto = round($total - ($acum - $monto), 2);
            }

            $pagos[] = ['parcialidad' => $i + 1, 'fecha' => $fecha->toDateString(), 'monto' => round($monto, 2)];

            if ($periodicidad === 'UNICA') break;
        }

        return $pagos;
    }

    // ───────────────────────────────────────────────────��─────────────────────
    //  HELPERS: VALIDACIONES / UTILIDADES
    // ─────────────────────────────────────────────────────────────────────────

    private function validarClabe(string $clabe): bool
    {
        if (strlen($clabe) !== 18) return false;
        $factores = [3, 7, 1];
        $suma = 0;
        for ($i = 0; $i < 17; $i++) {
            $suma += ((int) $clabe[$i] * $factores[$i % 3]) % 10;
        }
        return ((10 - ($suma % 10)) % 10) === (int) $clabe[17];
    }

    private function clean(mixed $v): string
    {
        return trim((string) ($v ?? ''));
    }

    private function digits(mixed $v): string
    {
        return preg_replace('/\D+/', '', (string) ($v ?? ''));
    }

    private function err(string $msg, int $status = 422): JsonResponse
    {
        return response()->json(['ok' => false, 'error' => $msg], $status);
    }
}