<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log, Mail, Storage};
use App\Mail\SolicitudDistribucionRecibida;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 * SolicitudDistribucionController
 *
 * Módulo 1 — Alta sin token: landing pública, sin sesión.
 *   GET  /distribucion/solicitud          → showPublico()
 *   POST /distribucion/solicitud          → guardarPublico()
 *
 * Módulo 2 — Alta con token: dentro del hospital, con sesión.
 *   GET  /pats/distribucion?t=TOKEN       → show()      (usa token de gestor/franquicia)
 *   POST /pats/distribucion/orden         → generarOrden()
 *
 * Rutas sugeridas (routes/web.php):
 *   // Módulo 1 — pública, sin auth
 *   Route::get('/distribucion/solicitud',  [SolicitudDistribucionController::class, 'showPublico'])->name('dist.publico.show');
 *   Route::post('/distribucion/solicitud', [SolicitudDistribucionController::class, 'guardarPublico'])->name('dist.publico.guardar');
 *
 *   // Módulo 2 — interna, con auth
 *   Route::middleware('auth')->group(function () {
 *       Route::get('/pats/distribucion',        [SolicitudDistribucionController::class, 'show'])->name('pats.pago-distribucion.show');
 *       Route::post('/pats/distribucion/orden', [SolicitudDistribucionController::class, 'generarOrden'])->name('pats.pago-distribucion.generar-orden');
 *   });
 */
class SolicitudDistribucionController extends Controller
{
    // ─── Constantes ──────────────────────────────────────────────────────────

    private const PRECIO_DEFAULT = 20000.00;

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
        'AGS'  => 'Aguascalientes',
        'BCN'  => 'Baja California',
        'BCS'  => 'Baja California Sur',
        'CAM'  => 'Campeche',
        'CHP'  => 'Chiapas',
        'CHH'  => 'Chihuahua',
        'CDMX' => 'Ciudad de México',
        'COA'  => 'Coahuila',
        'COL'  => 'Colima',
        'DUR'  => 'Durango',
        'MEX'  => 'Estado de México',
        'GTO'  => 'Guanajuato',
        'GRO'  => 'Guerrero',
        'HGO'  => 'Hidalgo',
        'JAL'  => 'Jalisco',
        'MIC'  => 'Michoacán',
        'MOR'  => 'Morelos',
        'NAY'  => 'Nayarit',
        'NLE'  => 'Nuevo León',
        'OAX'  => 'Oaxaca',
        'PUE'  => 'Puebla',
        'QRO'  => 'Querétaro',
        'ROO'  => 'Quintana Roo',
        'SLP'  => 'San Luis Potosí',
        'SIN'  => 'Sinaloa',
        'SON'  => 'Sonora',
        'TAB'  => 'Tabasco',
        'TAM'  => 'Tamaulipas',
        'TLAX' => 'Tlaxcala',
        'VER'  => 'Veracruz',
        'YUC'  => 'Yucatán',
        'ZAC'  => 'Zacatecas',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  MÓDULO 1 — SIN TOKEN (Landing pública)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Muestra el formulario público de solicitud de distribución.
     * No requiere sesión ni token — directo a corporativa.
     */
    public function showPublico(Request $request): \Illuminate\View\View
    {
        $token = trim((string) $request->query('t', ''));

        return view('pats.solicitud_distribuidor', [
            'conToken'           => $token !== '',
            'token'              => $token,
            'precioDistribucion' => $this->getPrecioDistribucion(),
            'estados'            => self::ESTADOS,
        ]);
    }

    /**
     * POST /distribucion/solicitud/pre-validar
     * Valida todos los campos, archivos, biometría y duplicados SIN cobrar.
     */
    public function preValidar(Request $request): JsonResponse
    {
        $error = $this->validarDatos($request, tablaDistribuidores: 'pats_distribuidores', tablaSolicitudes: 'pats_solicitudes_distribuidor');
        if ($error !== null) return $error;
        return response()->json(['ok' => true]);
    }

    /**
     * POST /distribucion/solicitud
     * Guarda la solicitud. Token es solo referencia — nullable, sin validación contra tabla.
     */
    public function guardarPublico(Request $request): JsonResponse
    {
        $token = trim((string) $request->input('public_token', '')) ?: null;

        // Idempotencia: si el pago ya fue guardado (reintento tras timeout de red),
        // devolver el registro existente en lugar de un error de duplicado.
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

        return $this->procesarSolicitud($request, $token);
    }

    /**
     * GET /distribucion/solicitud/confirmacion
     * Pantalla de éxito mostrada tras envío exitoso del formulario.
     */
    public function confirmacion(Request $request)
    {
        $ref    = $this->clean($request->query('ref', ''));
        $nombre = $this->clean($request->query('nombre', ''));
        $correo = $this->clean($request->query('correo', ''));

        return view('pats.solicitud_distribuidor_success', compact('ref', 'nombre', 'correo'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  MÓDULO 3 — LISTADO (JSON para admin / gestión)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /pats/distribucion/solicitudes
     * Devuelve todas las solicitudes paginadas con toda la data relacionada.
     * Filtros opcionales: ?estatus=ENVIADA  ?region=JAL  ?q=texto  ?per_page=25
     */
    public function listar(Request $request): JsonResponse
    {
        $q       = $this->clean($request->input('q'));
        $estatus = strtoupper($this->clean($request->input('estatus')));
        $region  = strtoupper($this->clean($request->input('region')));
        $perPage = min((int) $request->input('per_page', 25), 100);

        $query = DB::table('pats_solicitudes_distribuidor')
            ->orderByDesc('id_solicitud');

        if ($estatus !== '') {
            $query->where('estatus', $estatus);
        }

        if ($region !== '') {
            $query->where('region', $region);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = "%{$q}%";
                $sub->where('nombre',    'like', $like)
                    ->orWhere('correo',   'like', $like)
                    ->orWhere('rfc',      'like', $like)
                    ->orWhere('telefono', 'like', $like)
                    ->orWhere('razon_social', 'like', $like);
            });
        }

        $paginator = $query->paginate($perPage);
        $ids       = array_column($paginator->items(), 'id_solicitud');

        // ── Documentos (solo vigentes) ────────────────────────────────────────
        $documentosPorId = [];
        if ($ids) {
            $docs = DB::table('pats_solicitudes_distribuidor_documentos')
                ->whereIn('id_solicitud', $ids)
                ->where('vigente', 1)
                ->select([
                    'id_solicitud',
                    'tipo_documento',
                    'archivo_path',
                    'archivo_nombre_original',
                    'mime_type',
                    'size_kb',
                    'created_at',
                ])
                ->orderBy('tipo_documento')
                ->get();

            foreach ($docs as $doc) {
                $documentosPorId[$doc->id_solicitud][] = (array) $doc;
            }
        }

        // ── Historial ─────────────────────────────────────────────────────────
        $historialPorId = [];
        if ($ids) {
            $hist = DB::table('pats_solicitudes_distribuidor_historial')
                ->whereIn('id_solicitud', $ids)
                ->select([
                    'id_solicitud',
                    'evento_tipo',
                    'estatus_anterior',
                    'estatus_nuevo',
                    'payload_json',
                    'user_evento',
                    'fecha_evento',
                ])
                ->orderBy('fecha_evento')
                ->get();

            foreach ($hist as $h) {
                $row                = (array) $h;
                $row['payload_json'] = $h->payload_json
                    ? json_decode($h->payload_json, true)
                    : null;
                $historialPorId[$h->id_solicitud][] = $row;
            }
        }

        // ── Preview biométrico ────────────────────────────────────────────────
        $previewPorId = [];
        if ($ids) {
            $previews = DB::table('pats_preview_dist')
                ->whereIn('id_solicitud', $ids)
                ->select([
                    'id_solicitud',
                    'selfie_path',
                    'selfie_mime',
                    'selfie_kb',
                    'firma_path',
                    'firma_mime',
                    'firma_kb',
                    'contrato_path',
                    'contrato_mime',
                    'contrato_kb',
                ])
                ->get();

            foreach ($previews as $p) {
                $previewPorId[$p->id_solicitud] = (array) $p;
            }
        }

        // ── Ensamblar respuesta ───────────────────────────────────────────────
        $data = array_map(function ($row) use ($documentosPorId, $historialPorId, $previewPorId) {
            $item = (array) $row;

            $item['esquema_pagos_json'] = $item['esquema_pagos_json']
                ? json_decode($item['esquema_pagos_json'], true)
                : null;

            $item['documentos'] = $documentosPorId[$item['id_solicitud']] ?? [];
            $item['historial']  = $historialPorId[$item['id_solicitud']]  ?? [];
            $item['preview']    = $previewPorId[$item['id_solicitud']]    ?? null;

            return $item;
        }, $paginator->items());

        return response()->json([
            'ok'   => true,
            'data' => $data,
            'meta' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  MÓDULO 2 — CON TOKEN (Dentro del hospital)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Muestra el formulario con contexto de token (gestor o franquicia).
     * Requiere sesión activa.
     */
    public function show(Request $request): \Illuminate\View\View
    {
        $token = trim((string) $request->query('t', ''));
        abort_if($token === '', 400, 'Token requerido.');

        $ctx = $this->resolveToken($token);
        abort_if($ctx === null, 404, 'El enlace no es válido o ya no está activo.');

        $region = strtoupper(trim((string) ($ctx['region'] ?? '')));

        return view('pats.solicitud_distribuidor', [
            'conToken'           => true,
            'ctx'                => $ctx,
            'precioDistribucion' => $this->getPrecioDistribucion(),
            'estados'            => self::ESTADOS,
            'token'              => $token,
            'actorTipo'          => $ctx['actor_tipo'],
            'region'             => $region,
            'estadoNombre'       => self::ESTADOS[$region] ?? $region,
            'zona'               => (string) ($ctx['zona'] ?? ''),
            'unidad'             => (string) ($ctx['unidad'] ?? ''),
            'pais'               => (string) ($ctx['pais'] ?? 'México'),
        ]);
    }


    // ─────────────────────────────────────────────────────────────────────────
    //  VALIDACIÓN COMPARTIDA (pre-validar + procesarSolicitud)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Valida campos, archivos, biometría y duplicados.
     * Devuelve JsonResponse de error o null si todo está bien.
     */
    private function validarDatos(
        Request $request,
        string $tablaDistribuidores,
        string $tablaSolicitudes
    ): ?JsonResponse {

        $pais        = $this->clean($request->input('pais'));
        $region      = strtoupper($this->clean($request->input('region')));
        $municipio   = $this->clean($request->input('municipio'));
        $ciudad      = $this->clean($request->input('ciudad')) ?: $municipio;
        $calle       = $this->clean($request->input('calle'));
        $numExt      = $this->clean($request->input('num_ext'));
        $cp          = $this->digits($request->input('cp'));
        $colonia     = $this->clean($request->input('colonia'));
        $tipoPersona = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $apellidoPaterno = $this->clean($request->input('apellido_paterno'));
        $nombre      = $this->clean($request->input('nombre'));
        $razonSocial = $this->clean($request->input('razon_social'));
        $fechaNacimiento = $this->clean($request->input('fecha_nacimiento'));
        $paisNacimiento  = $this->clean($request->input('pais_nacimiento'));
        $nacionalidad    = $this->clean($request->input('nacionalidad'));
        $ocupacion       = $this->clean($request->input('ocupacion'));
        $tipoIdentificacion       = $this->clean($request->input('tipo_identificacion'));
        $identificacionEmitidaPor = $this->clean($request->input('identificacion_emitida_por'));
        $numeroIdentificacion     = $this->clean($request->input('numero_identificacion'));
        $telefono    = $this->digits($request->input('telefono'));
        $correo      = strtolower($this->clean($request->input('correo')));
        $clabe          = $this->digits($request->input('clabe'));
        $routingNumber  = $this->digits($request->input('routing_number'));
        $modalidad   = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses  = (int) $request->input('plazo_meses', 0);
        $fechaInicio = now()->toDateString();
        $fechaPrimerV = $this->clean($request->input('fecha_primer_vencimiento'));

        $isUS = strtoupper($pais) === 'US';

        // Campos básicos obligatorios
        $camposBase = [
            'pais',
            'region',
            'ciudad',
            'calle',
            'numExt',
            'cp',
            'nombre',
            'telefono',
            'correo',
        ];
        // municipio y colonia solo son obligatorios para MX
        if (! $isUS) {
            array_push($camposBase, 'municipio', 'colonia');
        }
        foreach (compact(...$camposBase) as $campo => $valor) {
            if ($valor === '') return $this->err("El campo '{$campo}' es obligatorio.");
        }

        if (! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->err('El correo electrónico no es válido.');
        }
        if (strlen($telefono) !== 10) {
            return $this->err('El teléfono debe tener 10 dígitos.');
        }
        if ($isUS) {
            if ($routingNumber !== '' && strlen($routingNumber) !== 9) {
                return $this->err('El ABA Routing Number debe tener 9 dígitos.');
            }
        } else {
            if ($clabe !== '' && ! $this->validarClabe($clabe)) {
                return $this->err('La CLABE interbancaria no es válida.');
            }
        }
        if (! in_array($tipoPersona, ['FISICA', 'MORAL'], true)) {
            return $this->err('Tipo de persona no válido.');
        }
        if ($tipoPersona === 'MORAL' && $razonSocial === '') {
            return $this->err('Para persona moral es obligatoria la razón social.');
        }
        if (! in_array($modalidad, self::MODALIDADES_VALIDAS, true)) {
            return $this->err('Modalidad de pago no válida.');
        }
        if ($modalidad !== 'CONTADO') {
            if ($plazoMeses <= 0) return $this->err('Indica el número de meses para el financiamiento.');
            if ($fechaPrimerV === '') return $this->err('La fecha del primer vencimiento es obligatoria para pago diferido.');
        }

        // Documentos requeridos
        $etiquetas = ['doc_ine' => 'INE / IFE', 'doc_domicilio' => 'Comprobante de domicilio', 'doc_cedula' => 'Cédula fiscal'];
        foreach (array_keys(self::DOCS_REQUERIDOS) as $campo) {
            if (! $request->hasFile($campo) || ! $request->file($campo)->isValid()) {
                return $this->err("El documento '{$etiquetas[$campo]}' es obligatorio.");
            }
        }

        // Persona moral: al menos acta o poder
        if ($tipoPersona === 'MORAL') {
            $tieneActa  = $request->hasFile('doc_acta_constitutiva')  && $request->file('doc_acta_constitutiva')->isValid();
            $tienePoder = $request->hasFile('doc_poder_notarial')     && $request->file('doc_poder_notarial')->isValid();
            if (! $tieneActa && ! $tienePoder) {
                return $this->err('Para persona moral sube al menos el acta constitutiva o el poder notarial.');
            }
        }

        // Biometría
        if (! str_starts_with($request->input('selfie_data', ''), 'data:image')) {
            return $this->err('La selfie del titular es obligatoria.');
        }
        if (! str_starts_with($request->input('firma_data', ''), 'data:image')) {
            return $this->err('La firma del titular es obligatoria.');
        }

        // Declaración dueño beneficiario (Anexo 12)
        $beneficiarioDirecto = strtoupper($this->clean($request->input('beneficiario_directo')));
        if (! in_array($beneficiarioDirecto, ['SI', 'NO'], true)) {
            return $this->err('La declaración de dueño beneficiario es obligatoria (Anexo 12).');
        }
        if ($beneficiarioDirecto === 'NO') {
            return $this->err('No es posible procesar la solicitud si existe otro dueño beneficiario. Contacta a soporte.');
        }

        // Duplicados
        if (DB::table($tablaDistribuidores)->where('correo', $correo)->exists()) {
            return $this->err('Ya existe un registro con ese correo electrónico.', 409);
        }
        if (DB::table($tablaSolicitudes)->where('correo', $correo)->whereNotIn('estatus', ['RECHAZADA', 'CONVERTIDA_ALTA'])->exists()) {
            return $this->err('Ya existe una solicitud activa con ese correo electrónico.', 409);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LÓGICA CENTRAL DE PROCESAMIENTO
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Valida, guarda documentos e inserta la solicitud en la BD.
     * Compartido por módulo 1 y módulo 2.
     */
    private function procesarSolicitud(
        Request $request,
        ?string $tokenReferido = null
    ): JsonResponse {

        // ── 1. Inputs ─────────────────────────────────────────────────────────
        $pais           = $this->clean($request->input('pais'));
        $region         = strtoupper($this->clean($request->input('region')));
        $municipio      = $this->clean($request->input('municipio'));
        $ciudad         = $this->clean($request->input('ciudad')) ?: $municipio;
        $calle          = $this->clean($request->input('calle'));
        $numExt         = $this->clean($request->input('num_ext'));
        $numInt         = $this->clean($request->input('num_int'));
        $cp             = $this->digits($request->input('cp'));
        $colonia        = $this->clean($request->input('colonia'));

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

        $beneficiarioDirecto  = strtoupper($this->clean($request->input('beneficiario_directo', 'SI')));

        $telefono        = $this->digits($request->input('telefono'));
        $correo          = strtolower($this->clean($request->input('correo')));

        $banco          = $this->clean($request->input('banco'));
        $numeroCuenta   = $this->clean($request->input('numero_cuenta'));
        $clabe          = $this->digits($request->input('clabe'));
        $routingNumber  = $this->digits($request->input('routing_number'));
        $titularCuenta  = $this->clean($request->input('titular_cuenta'));

        $modalidad      = strtoupper($this->clean($request->input('modalidad_pago', 'CONTADO')));
        $tipoOperacion  = $this->clean($request->input('tipo_operacion', 'Adquisición de Derecho de Distribución'));
        $moneda         = $this->clean($request->input('moneda', 'MXN'));
        $plazoMeses     = (int) $request->input('plazo_meses', 0);
        $periodicidad   = strtoupper($this->clean($request->input('periodicidad', 'MENSUAL')));
        $fechaInicio    = $this->clean($request->input('fecha_inicio'));
        $fechaPrimerV   = $this->clean($request->input('fecha_primer_vencimiento'));

        // ── 2 + 3. Validaciones y duplicados (reutiliza validarDatos) ───────────

        $errorValidacion = $this->validarDatos(
            $request,
            tablaDistribuidores: 'pats_distribuidores',
            tablaSolicitudes: 'pats_solicitudes_distribuidor'
        );
        if ($errorValidacion !== null) return $errorValidacion;

        $selfieData = $request->input('selfie_data', '');
        $firmaData  = $request->input('firma_data', '');

        // ── 4. Verificar pago Stripe ──────────────────────────────────────────

        $stripeIntentId = $this->clean($request->input('stripe_payment_intent_id'));

        if ($stripeIntentId === '') {
            return $this->err('El pago con tarjeta es requerido.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::retrieve($stripeIntentId);

            if ($intent->status !== 'succeeded') {
                return $this->err('El pago no fue procesado correctamente. Intenta de nuevo.');
            }

            $expectedCentavos = (int) round($this->getPrecioDistribucion() * 100);
            if ($intent->amount !== $expectedCentavos) {
                Log::warning('Stripe amount mismatch', [
                    'intent_id' => $stripeIntentId,
                    'expected'  => $expectedCentavos,
                    'received'  => $intent->amount,
                ]);
                return $this->err('El monto del pago no coincide. Contacta a soporte.');
            }

            // Evitar reutilización del mismo intent en otra solicitud
            if (DB::table('pats_pagos')->where('pasarela', 'stripe')->where('referencia_pasarela', $stripeIntentId)->exists()) {
                return $this->err('Este pago ya fue utilizado en otra solicitud.', 409);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe.verify', ['error' => $e->getMessage(), 'intent' => $stripeIntentId]);
            return $this->err('No fue posible verificar el pago. Intenta de nuevo.');
        }

        // ── 5. Precio y plan ──────────────────────────────────────────────────

        $valorTotal  = $this->getPrecioDistribucion();
        $enganche    = 0.00;
        $saldoFin    = $modalidad !== 'CONTADO' ? $valorTotal : 0.00;

        if ($modalidad === 'CONTADO') {
            $plazoMeses  = 0;
            $fechaPrimerV = null;
        }

        // ── 5a. Resolver token → ids reales ──────────────────────────────────

        $idFranquicia = 0;
        $idGestor     = 0;
        if ($tokenReferido !== null && $tokenReferido !== '') {
            $ctx = $this->resolveToken($tokenReferido);
            if ($ctx !== null) {
                $idFranquicia = (int) ($ctx['id_franquicia'] ?? 0);
                $idGestor     = (int) ($ctx['id_gestor'] ?? 0);
            }
        }

        // ── 5b. Generar esquema de pagos ──────────────────────────────────────

        $esquemaPagos = null;

        if ($modalidad !== 'CONTADO' && $plazoMeses > 0 && $fechaPrimerV) {
            $esquemaPagos = $this->generarEsquema(
                $valorTotal,
                $plazoMeses,
                $periodicidad,
                $fechaPrimerV
            );
        }

        // ── 6. Transacción: insertar solicitud + docs + historial ─────────────

        DB::beginTransaction();

        try {
            // 6a. Insertar solicitud
            $idSolicitud = DB::table('pats_solicitudes_distribuidor')->insertGetId([
                'token_referido'             => $tokenReferido,
                'id_franquicia'              => $idFranquicia,
                'id_gestor'                  => $idGestor,
                'id_distribuidor_generado'   => null,
                'user_solicita'              => auth()->id(),
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
                'beneficiario_directo'       => $beneficiarioDirecto ?: null,
                'telefono'                   => $telefono,
                'correo'                     => $correo,
                'direccion'                  => $direccion,
                'banco'                      => $banco ?: null,
                'numero_cuenta'              => $numeroCuenta ?: null,
                'clabe'                      => $clabe ?: null,
                'routing_number'             => $routingNumber ?: null,
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
                'esquema_pagos_json'         => $esquemaPagos
                    ? json_encode($esquemaPagos, JSON_UNESCAPED_UNICODE)
                    : null,
                'contrato_admin_path'        => null,
                'contrato_firmado_path'      => null,
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

            // 6b. Registrar pago en tabla centralizada
            DB::table('pats_pagos')->insert([
                'tipo_solicitud'      => 'distribuidor',
                'id_solicitud'        => $idSolicitud,
                'pasarela'            => 'stripe',
                'referencia_pasarela' => $stripeIntentId,
                'estatus'             => 'succeeded',
                'monto'               => $valorTotal,
                'moneda'              => $moneda ?: 'MXN',
                'metadata_json'       => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // 6c. Guardar documentos
            $baseDir = "solicitudes/distribuidor/{$idSolicitud}";

            // Requeridos
            foreach (self::DOCS_REQUERIDOS as $campo => $tipoDoc) {
                $this->guardarDocumento(
                    $request->file($campo),
                    $idSolicitud,
                    $tipoDoc,
                    $baseDir
                );
            }

            // Opcionales (solo si vienen)
            foreach (self::DOCS_OPCIONALES as $campo => $tipoDoc) {
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $this->guardarDocumento(
                        $request->file($campo),
                        $idSolicitud,
                        $tipoDoc,
                        $baseDir
                    );
                }
            }

            // 6d. Preview biométrico (selfie + firma como archivos)
            $previewRow = [
                'id_solicitud' => $idSolicitud,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            $selfieInfo  = $this->guardarBase64Imagen($selfieData, $baseDir, 'selfie');
            $firmaInfo   = $this->guardarBase64Imagen($firmaData,  $baseDir, 'firma');

            $previewRow['selfie_path']    = $selfieInfo['path']  ?? null;
            $previewRow['selfie_mime']    = $selfieInfo['mime']  ?? null;
            $previewRow['selfie_kb']      = $selfieInfo['kb']    ?? null;
            $previewRow['firma_path']     = $firmaInfo['path']   ?? null;
            $previewRow['firma_mime']     = $firmaInfo['mime']   ?? null;
            $previewRow['firma_kb']       = $firmaInfo['kb']     ?? null;

            // Contrato: plantilla estática pública (no se sube archivo)
            $previewRow['contrato_path'] = 'static/contrato_dist.pdf';
            $previewRow['contrato_mime'] = 'application/pdf';
            $previewRow['contrato_kb']   = 662;

            DB::table('pats_preview_dist')->insert($previewRow);

            // 6e. Historial
            $this->insertarHistorial(
                idSolicitud: $idSolicitud,
                eventoTipo: 'solicitud_enviada',
                estatusAnterior: null,
                estatusNuevo: 'ENVIADA',
                payload: [
                    'nombre'        => $nombre,
                    'correo'        => $correo,
                    'telefono'      => $telefono,
                    'region'        => $region,
                    'modalidad'     => $modalidad,
                    'valor_total'    => $valorTotal,
                    'plazo_meses'    => $plazoMeses,
                    'token_referido' => $tokenReferido,
                    'beneficiario_directo' => $beneficiarioDirecto,
                ]
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SolicitudDistribucion.procesarSolicitud', [
                'error'  => $e->getMessage(),
                'correo' => $correo,
            ]);
            return response()->json([
                'ok'    => false,
                'error' => 'No fue posible guardar la solicitud. Intenta de nuevo.',
            ], 500);
        }

        // ── 7. Generar referencia legible ─────────────────────────────────────

        $referencia = 'DIST-' . strtoupper(Str::random(6)) . '-' . $idSolicitud;

        // ── 8. Enviar correo de confirmación al solicitante ───────────────────

        try {
            Mail::to($correo)->send(
                new SolicitudDistribucionRecibida(
                    nombreSolicitante: trim("{$apellidoPaterno} {$nombre}"),
                    referencia: $referencia,
                    correo: $correo,
                )
            );
        } catch (\Throwable $e) {
            Log::warning('SolicitudDistribucion.correoConfirmacion', [
                'error'  => $e->getMessage(),
                'correo' => $correo,
            ]);
            // El correo es opcional — no bloquea la respuesta exitosa
        }

        return response()->json([
            'ok'          => true,
            'id_solicitud' => $idSolicitud,
            'referencia'  => $referencia,
            'estatus'     => 'ENVIADA',
            'message'     => 'Solicitud enviada correctamente. Revisaremos en 24–48 horas hábiles.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: BIOMETRÍA (base64 → archivo en storage)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Decodifica un data URL base64, lo guarda en storage y devuelve metadatos.
     * Retorna null si el data URL no es válido.
     *
     * @return array{path:string,mime:string,kb:int}|null
     */
    private function guardarBase64Imagen(string $dataUrl, string $baseDir, string $prefix): ?array
    {
        if (! preg_match('/^data:(image\/[a-z+]+);base64,(.+)$/s', $dataUrl, $m)) {
            return null;
        }

        $mime      = $m[1];                      // e.g. image/jpeg
        $raw       = base64_decode($m[2], true);
        if ($raw === false) return null;

        $ext       = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'bin',
        };

        $filename  = now()->format('YmdHis') . '_' . Str::random(8) . "__{$prefix}.{$ext}";
        $path      = "{$baseDir}/{$filename}";

        Storage::disk('local')->put("private/{$path}", $raw);

        return [
            'path' => "private/{$path}",
            'mime' => $mime,
            'kb'   => (int) ceil(strlen($raw) / 1024),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
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

        // Guarda en storage/app/private/...
        Storage::disk('local')->put("private/{$path}", file_get_contents($file->getRealPath()));

        // Desactiva versión anterior si existiera
        DB::table('pats_solicitudes_distribuidor_documentos')
            ->where('id_solicitud', $idSolicitud)
            ->where('tipo_documento', $tipoDoc)
            ->where('vigente', 1)
            ->update(['vigente' => 0, 'updated_at' => now()]);

        // Inserta nuevo registro
        DB::table('pats_solicitudes_distribuidor_documentos')->insert([
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

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: HISTORIAL
    // ─────────────────────────────────────────────────────────────────────────

    private function insertarHistorial(
        int $idSolicitud,
        string $eventoTipo,
        ?string $estatusAnterior,
        ?string $estatusNuevo,
        array $payload = []
    ): void {
        DB::table('pats_solicitudes_distribuidor_historial')->insert([
            'id_solicitud'    => $idSolicitud,
            'evento_tipo'     => $eventoTipo,
            'estatus_anterior' => $estatusAnterior,
            'estatus_nuevo'   => $estatusNuevo,
            'payload_json'    => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'user_evento'     => auth()->id(),
            'fecha_evento'    => now(),
            'created_at'      => now(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: TOKEN
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resuelve un token público buscando en gestores primero, luego franquicias.
     * Devuelve array con contexto o null si no se encuentra.
     */
    private function resolveToken(string $token): ?array
    {
        // 1. Gestor
        $gestor = DB::table('pats_gestores as g')
            ->where('g.public_checkout_token', $token)
            ->where('g.public_checkout_activo', 1)
            ->where('g.activo', 1)
            ->select([
                'g.id_gestor',
                'g.nombre_gestor as nombre_actor',
                'g.correo',
            ])
            ->first();

        if ($gestor) {
            return [
                'actor_tipo'      => 'GESTOR',
                'id_gestor'       => (int) $gestor->id_gestor,
                'id_franquicia'   => 0,
                'nombre_actor'    => $gestor->nombre_actor,
                'region'          => '',
                'zona'            => null,
                'unidad'          => null,
                'pais'            => 'México',
                'nombre_franquicia' => null,
            ];
        }

        // 2. Franquicia directa
        $franquicia = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->select([
                'id_franquicia',
                'nombre_franquicia',
                'region',
                'zona',
                'unidad',
                'pais',
            ])
            ->first();

        if ($franquicia) {
            return [
                'actor_tipo'      => 'FRANQUICIA',
                'id_gestor'       => 0,
                'id_franquicia'   => (int) $franquicia->id_franquicia,
                'nombre_actor'    => $franquicia->nombre_franquicia,
                'region'          => $franquicia->region,
                'zona'            => $franquicia->zona,
                'unidad'          => $franquicia->unidad,
                'pais'            => $franquicia->pais ?? 'México',
                'nombre_franquicia' => $franquicia->nombre_franquicia,
            ];
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: FINANZAS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lee el precio de distribución del catálogo.
     * Fallback a $20,000 si no existe.
     */
    private function getPrecioDistribucion(): float
    {
        $precio = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = 'distribucion'")
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($precio ?? self::PRECIO_DEFAULT);
    }

    /**
     * Genera el esquema de pagos con fechas y montos.
     * Devuelve array de parcialidades.
     */
    private function generarEsquema(
        float $total,
        int $plazo,
        string $periodicidad,
        string $fechaPrimerPago
    ): array {

        $base     = Carbon::parse($fechaPrimerPago);
        $montoBase = floor(($total / $plazo) * 100) / 100;
        $acum     = 0.0;
        $pagos    = [];

        for ($i = 0; $i < $plazo; $i++) {
            $fecha = match ($periodicidad) {
                'SEMANAL'   => $base->copy()->addWeeks($i),
                'QUINCENAL' => $base->copy()->addDays($i * 15),
                'UNICA'     => $base->copy(),
                default     => $base->copy()->addMonthsNoOverflow($i), // MENSUAL
            };

            $monto  = $montoBase;
            $acum  += $monto;

            // Ajustar diferencia de redondeo en el último pago
            if ($i === $plazo - 1) {
                $monto = round($total - ($acum - $monto), 2);
            }

            $pagos[] = [
                'parcialidad' => $i + 1,
                'fecha'       => $fecha->toDateString(),
                'monto'       => round($monto, 2),
            ];

            if ($periodicidad === 'UNICA') break;
        }

        return $pagos;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: VALIDACIONES
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Valida CLABE interbancaria con dígito verificador (algoritmo SAT/Banxico).
     */
    private function validarClabe(string $clabe): bool
    {
        if (strlen($clabe) !== 18) return false;

        $factores = [3, 7, 1];
        $suma     = 0;

        for ($i = 0; $i < 17; $i++) {
            $suma += ((int) $clabe[$i] * $factores[$i % 3]) % 10;
        }

        $verificador = (10 - ($suma % 10)) % 10;

        return $verificador === (int) $clabe[17];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS: UTILIDADES
    // ─────────────────────────────────────────────────────────────────────────

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
