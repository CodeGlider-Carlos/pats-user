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
 * SolicitudPatsController
 *
 * Registro público de usuario PATS con documentos, fotografía y firma digital.
 *
 *   GET  /pats/registro?t={token}     → show()      (token opcional)
 *   GET  /pats/registro/directo       → showDirecto()
 *   POST /pats/registro/orden         → generarOrden()
 *   POST /pats/registro/contrato      → contratoPreview()
 *   POST /pats/registro/pasaporte-validar → validarPasaporte()
 *   POST /pats/registro/stripe/intent → StripePatsController@createIntent
 */
class SolicitudPatsController extends Controller
{
    private const MONTO_MENSUAL = 800;
    private const MONTO_ANUAL   = 9600;

    private const ESTADOS = [
        'AGS'  => 'Aguascalientes',      'BCN'  => 'Baja California',
        'BCS'  => 'Baja California Sur', 'CAM'  => 'Campeche',
        'CHP'  => 'Chiapas',             'CHH'  => 'Chihuahua',
        'CDMX' => 'Ciudad de México',    'COA'  => 'Coahuila',
        'COL'  => 'Colima',              'DGO'  => 'Durango',
        'GTO'  => 'Guanajuato',          'GRO'  => 'Guerrero',
        'HGO'  => 'Hidalgo',             'JAL'  => 'Jalisco',
        'MEX'  => 'Estado de México',    'MIC'  => 'Michoacán',
        'MOR'  => 'Morelos',             'NAY'  => 'Nayarit',
        'NLE'  => 'Nuevo León',          'OAX'  => 'Oaxaca',
        'PUE'  => 'Puebla',              'QRO'  => 'Querétaro',
        'ROO'  => 'Quintana Roo',        'SLP'  => 'San Luis Potosí',
        'SIN'  => 'Sinaloa',             'SON'  => 'Sonora',
        'TAB'  => 'Tabasco',             'TAM'  => 'Tamaulipas',
        'TLAX' => 'Tlaxcala',            'VER'  => 'Veracruz',
        'YUC'  => 'Yucatán',             'ZAC'  => 'Zacatecas',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /pats/registro?t={token}  (token opcional)
    // ─────────────────────────────────────────────────────────────────────────

    public function show(Request $request): \Illuminate\View\View
    {
        $token = trim((string) $request->query('t', ''));

        if ($token !== '') {
            $ctx = $this->resolverToken($token);
            if ($ctx === null) {
                abort(404, 'El link no es válido o ya no está activo.');
            }
        } else {
            $ctx = $this->ctxDirecto();
        }

        $region = strtoupper(trim((string) ($ctx['region'] ?? '')));

        return view('pats.solicitud_pats', [
            'token'          => $token,
            'ctx'            => $ctx,
            'estadoAcronimo' => $region,
            'estadoNombre'   => self::ESTADOS[$region] ?? $region,
            'estados'        => self::ESTADOS,
            'montoMensual'   => self::MONTO_MENSUAL,
            'montoAnual'     => self::MONTO_ANUAL,
            'pais'           => (string) ($ctx['pais'] ?? 'México'),
            'stripePk'       => config('services.stripe.key', ''),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /pats/registro/directo  (sin token — acceso libre)
    // ─────────────────────────────────────────────────────────────────────────

    public function showDirecto(): \Illuminate\View\View
    {
        $ctx = $this->ctxDirecto();

        return view('pats.solicitud_pats', [
            'token'          => '',
            'ctx'            => $ctx,
            'estadoAcronimo' => '',
            'estadoNombre'   => '',
            'estados'        => self::ESTADOS,
            'montoMensual'   => self::MONTO_MENSUAL,
            'montoAnual'     => self::MONTO_ANUAL,
            'pais'           => 'México',
            'stripePk'       => config('services.stripe.key', ''),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /pats/registro/pasaporte-validar
    // ─────────────────────────────────────────────────────────────────────────

    public function validarPasaporte(Request $request): JsonResponse
    {
        $idPasaporte     = (int) preg_replace('/\D+/', '', (string) $request->input('id_pasaporte', '0'));
        $fechaNacimiento = trim((string) $request->input('fecha_nacimiento', ''));

        if ($idPasaporte <= 0) {
            return response()->json(['ok' => false, 'error' => 'ID de pasaporte inválido.'], 422);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNacimiento)) {
            return response()->json(['ok' => false, 'error' => 'Fecha de nacimiento inválida.'], 422);
        }

        $row = DB::table('pats_pasaportes')
            ->where('id_pasaporte', $idPasaporte)
            ->where('fecha_nacimiento', $fechaNacimiento)
            ->where('activo', 1)
            ->whereRaw("LOWER(estatus) = 'activo'")
            ->whereRaw('vigencia >= CURDATE()')
            ->select([
                'id_pasaporte', 'curp', 'nombres', 'apellido_pa', 'apellido_ma',
                'fecha_nacimiento', 'vigencia', 'estatus', 'region', 'zona', 'unidad',
            ])
            ->first();

        if (!$row) {
            return response()->json([
                'ok'    => false,
                'error' => 'No se encontró un pasaporte activo y vigente con ese ID y fecha de nacimiento.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'pasaporte' => [
                'id_pasaporte'    => (int) $row->id_pasaporte,
                'curp'            => (string) $row->curp,
                'nombres'         => (string) $row->nombres,
                'apellido_pa'     => (string) $row->apellido_pa,
                'apellido_ma'     => (string) ($row->apellido_ma ?? ''),
                'fecha_nacimiento'=> (string) $row->fecha_nacimiento,
                'vigencia'        => (string) $row->vigencia,
                'estatus'         => (string) $row->estatus,
                'region'          => (string) ($row->region ?? ''),
                'zona'            => (string) ($row->zona   ?? ''),
                'unidad'          => (string) ($row->unidad ?? ''),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /pats/registro/orden
    // ─────────────────────────────────────────────────────────────────────────

    public function generarOrden(Request $request): JsonResponse
    {
        // ── 1. Token / contexto ───────────────────────────────────────────────
        $token = trim((string) $request->input('token_publico', ''));

        if ($token !== '') {
            $ctx = $this->resolverToken($token);
            if ($ctx === null) {
                return $this->err('Token inválido o expirado.', 404);
            }
        } else {
            $ctx = $this->ctxDirecto();
        }

        // ── 2. Stripe: verificar pago ─────────────────────────────────────────
        $stripeIntentId = trim((string) $request->input('stripe_payment_intent_id', ''));

        if ($stripeIntentId === '') {
            return $this->err('Pago no completado. Debes confirmar el pago antes de continuar.');
        }

        // Idempotencia: si el PI ya fue guardado, devolver la orden existente
        $existente = DB::table('pats_pagos')
            ->where('pasarela', 'stripe')
            ->where('referencia_pasarela', $stripeIntentId)
            ->first();

        if ($existente) {
            $orden = DB::table('pats_ordenes_pago')
                ->where('id_orden', $existente->id_solicitud)
                ->first();
            return response()->json([
                'ok'        => true,
                'id_orden'  => $existente->id_solicitud,
                'referencia'=> $orden->referencia_pago ?? '-',
            ]);
        }

        // Verificar PaymentIntent en Stripe
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($stripeIntentId);
        } catch (\Throwable $e) {
            Log::error('SolicitudPats.generarOrden Stripe retrieve', ['error' => $e->getMessage()]);
            return $this->err('No fue posible verificar el pago. Intenta nuevamente.');
        }

        if ($intent->status !== 'succeeded') {
            return $this->err('El pago no fue completado o fue rechazado. Estatus: ' . $intent->status);
        }

        // ── 3. Inputs básicos ─────────────────────────────────────────────────
        $correo          = strtolower(trim((string) $request->input('correo_usuario_pats', '')));
        $telefono        = $this->digits($request->input('telefono_usuario', ''));
        $nombre          = trim((string) $request->input('nombre_usuario', ''));
        $apellidoPa      = trim((string) $request->input('apellido_pa', ''));
        $apellidoMa      = trim((string) $request->input('apellido_ma', ''));
        $curp            = strtoupper(trim((string) $request->input('curp_usuario', '')));
        $rfcUsuario      = strtoupper(trim((string) $request->input('rfc_usuario', '')));
        $fechaNac        = trim((string) $request->input('fecha_nacimiento', ''));
        $tipoCliente     = strtolower(trim((string) $request->input('tipo_cliente', 'privado')));
        $nombreEmp       = trim((string) $request->input('nombre_empresa', ''));

        // Identificación
        $nacionalidadTipo     = strtoupper(trim((string) $request->input('nacionalidad_tipo', 'MEXICANA')));
        $nacionalidad         = trim((string) $request->input('nacionalidad', 'mexicana'));
        $paisNacimiento       = trim((string) $request->input('pais_nacimiento', 'México'));
        $actividadOcupacion   = trim((string) $request->input('actividad_ocupacion', ''));
        $estadoCivil          = trim((string) $request->input('estado_civil', ''));
        $tipoDocIdentidad     = trim((string) $request->input('tipo_documento_identidad', 'INE'));
        $paisDocIdentidad     = trim((string) $request->input('pais_documento_identidad', 'México'));
        $numDocIdentidad      = trim((string) $request->input('numero_documento_identidad', ''));

        // Domicilio
        $domCalle     = trim((string) $request->input('dom_calle', ''));
        $domNumExt    = trim((string) $request->input('dom_num_ext', ''));
        $domNumInt    = trim((string) $request->input('dom_num_int', ''));
        $domColonia   = trim((string) $request->input('dom_colonia', ''));
        $domCp        = $this->digits($request->input('dom_cp', ''));
        $domMunicipio = trim((string) $request->input('dom_municipio', ''));
        $domEstado    = trim((string) $request->input('dom_estado', ''));
        $domEstadoAcr = strtoupper(trim((string) $request->input('dom_estado_acronimo', '')));
        $domPais      = trim((string) $request->input('dom_pais', 'México'));

        // Frecuencia y monto
        $frecuencia   = strtoupper(trim((string) $request->input('frecuencia', 'MENSUAL')));
        if (!in_array($frecuencia, ['MENSUAL', 'ANUAL'], true)) { $frecuencia = 'MENSUAL'; }
        $idTipoPrecio = $frecuencia === 'ANUAL' ? 1 : 2;
        $montoOrden   = $frecuencia === 'ANUAL' ? self::MONTO_ANUAL : self::MONTO_MENSUAL;

        // Verificar monto contra el intent
        $montoPagado = $intent->amount / 100;
        if (abs($montoPagado - $montoOrden) > 1) {
            return $this->err("El monto pagado ({$montoPagado}) no coincide con el monto de la orden ({$montoOrden}).");
        }

        // Firma y foto
        $fotoBase64     = trim((string) $request->input('foto_base64', ''));
        $firmaBase64    = trim((string) $request->input('firma_base64', ''));
        $aceptaContrato = $request->has('acepta_contrato');

        // Tipo de paciente y representación
        $tipoPaciente               = strtoupper(trim((string) $request->input('tipo_paciente', 'ADULTO')));
        $modoFirma                  = strtoupper(trim((string) $request->input('modo_firma', 'FIRMA_PROPIA')));
        $requiereResponsable        = (bool) (int) $request->input('requiere_responsable', '0');
        $tipoRepresentacion         = strtoupper(trim((string) $request->input('tipo_representacion', 'FIRMA_PROPIA')));
        $relacionResponsable        = trim((string) $request->input('relacion_responsable_paciente', ''));
        $motivoResponsable          = trim((string) $request->input('motivo_responsable', ''));

        // Tutor/responsable
        $tutorNombre               = trim((string) $request->input('tutor_nombre', ''));
        $tutorApellidoPa           = trim((string) $request->input('tutor_apellido_pa', ''));
        $tutorApellidoMa           = trim((string) $request->input('tutor_apellido_ma', ''));
        $tutorCurp                 = strtoupper(trim((string) $request->input('tutor_curp', '')));
        $tutorRfc                  = strtoupper(trim((string) $request->input('tutor_rfc', '')));
        $tutorFechaNac             = trim((string) $request->input('tutor_fecha_nacimiento', ''));
        $tutorCorreo               = strtolower(trim((string) $request->input('tutor_correo', '')));
        $tutorTelefono             = $this->digits($request->input('tutor_telefono', ''));
        $tutorNacionalidadTipo     = strtoupper(trim((string) $request->input('tutor_nacionalidad_tipo', 'MEXICANA')));
        $tutorNacionalidad         = trim((string) $request->input('tutor_nacionalidad', 'mexicana'));
        $tutorPaisNacimiento       = trim((string) $request->input('tutor_pais_nacimiento', 'México'));
        $tutorTipoDocIdentidad     = trim((string) $request->input('tutor_tipo_documento_identidad', 'INE'));
        $tutorPaisDocIdentidad     = trim((string) $request->input('tutor_pais_documento_identidad', 'México'));
        $tutorNumDocIdentidad      = trim((string) $request->input('tutor_numero_documento_identidad', ''));

        // Adulto mayor
        $amPasaportesValidados     = (bool) (int) $request->input('adulto_mayor_pasaportes_validados', '0');
        $amPasaporte1Json          = trim((string) $request->input('adulto_mayor_pasaporte_1_json', ''));
        $amPasaporte2Json          = trim((string) $request->input('adulto_mayor_pasaporte_2_json', ''));

        // ── 4. Validaciones ───────────────────────────────────────────────────
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->err('Correo electrónico no válido.');
        }
        if (strlen($telefono) !== 10) {
            return $this->err('El teléfono debe tener 10 dígitos.');
        }
        if ($nombre === '')     { return $this->err('Falta nombre.'); }
        if ($apellidoPa === '') { return $this->err('Falta apellido paterno.'); }
        if ($fechaNac === '')   { return $this->err('Falta fecha de nacimiento.'); }

        $esMexicano = strtoupper($nacionalidadTipo) === 'MEXICANA';
        if ($esMexicano && $curp === '') {
            return $this->err('Falta CURP.');
        }
        if (!$esMexicano && $numDocIdentidad === '') {
            return $this->err('Falta número de identificación o pasaporte.');
        }

        if ($domCalle === '' || $domNumExt === '' || $domColonia === ''
            || strlen($domCp) !== 5 || $domMunicipio === '' || $domEstado === '') {
            return $this->err('Domicilio incompleto.');
        }

        if ($fotoBase64 === '')  { return $this->err('La fotografía es obligatoria.'); }
        if ($firmaBase64 === '') { return $this->err('La firma es obligatoria.'); }
        if (!$aceptaContrato)   { return $this->err('Debes aceptar el contrato.'); }

        // ── 5. Transacción ────────────────────────────────────────────────────
        $ahora      = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-' . strtoupper(Str::random(8));
        $folio      = 'ORD-'  . $ahora->format('Ymd')    . '-' . strtoupper(Str::random(6));

        $domicilio = implode(', ', array_filter([
            trim("{$domCalle} {$domNumExt}" . ($domNumInt ? " Int. {$domNumInt}" : '')),
            "Col. {$domColonia}",
            "C.P. {$domCp}",
            $domMunicipio,
            $domEstado,
        ]));

        $esMenor       = $tipoPaciente === 'MENOR';
        $esAdultoMayor = $tipoPaciente === 'ADULTO_MAYOR';

        DB::beginTransaction();
        try {
            // 5a. Insertar orden
            $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
                'token_publico'                  => $token ?: null,
                'folio_orden'                    => $folio,
                'referencia_pago'                => $referencia,
                'tipo_origen'                    => $ctx['tipo_origen'],
                'origen_checkout'                => 'PORTAL_PUBLICO',
                'id_distribuidor'                => $ctx['id_distribuidor'],
                'id_franquicia'                  => $ctx['id_franquicia'],
                'id_gestor'                      => (int) ($ctx['id_gestor'] ?? 0),
                'correo_usuario_pats'            => $correo,
                'curp_usuario'                   => $curp ?: null,
                'nombre_usuario'                 => $nombre,
                'apellido_pa'                    => $apellidoPa,
                'apellido_ma'                    => $apellidoMa ?: null,
                'fecha_nacimiento'               => $fechaNac,
                'telefono_usuario'               => $telefono,
                'rfc_usuario'                    => $rfcUsuario ?: null,
                'actividad_ocupacion'            => $actividadOcupacion ?: null,
                'estado_civil'                   => $estadoCivil ?: null,
                'nacionalidad_tipo'              => $nacionalidadTipo,
                'nacionalidad'                   => $nacionalidad,
                'pais_nacimiento'                => $paisNacimiento,
                'tipo_documento_identidad'       => $tipoDocIdentidad,
                'pais_documento_identidad'       => $paisDocIdentidad,
                'numero_documento_identidad'     => $numDocIdentidad ?: null,
                'dom_calle'                      => $domCalle,
                'dom_num_ext'                    => $domNumExt,
                'dom_num_int'                    => $domNumInt ?: null,
                'dom_colonia'                    => $domColonia,
                'dom_cp'                         => $domCp,
                'dom_municipio'                  => $domMunicipio,
                'dom_estado_acronimo'            => $domEstadoAcr ?: null,
                'dom_estado'                     => $domEstado,
                'dom_pais'                       => $domPais,
                'id_tipo_precio'                 => $idTipoPrecio,
                'tipo_operacion'                 => 'ALTA_PATS',
                'frecuencia'                     => $frecuencia,
                'monto_orden'                    => $montoOrden,
                'monto_nominal_base'             => $montoOrden,
                'monto_extra_recargo'            => 0.00,
                'moneda'                         => 'MXN',
                'pais'                           => $ctx['pais'],
                'region'                         => $ctx['region'],
                'zona'                           => $ctx['zona'],
                'unidad'                         => $ctx['unidad'],
                'tipo_cliente'                   => $tipoCliente,
                'nombre_empresa'                 => $nombreEmp ?: null,
                'tipo_paciente'                  => $tipoPaciente,
                'es_menor'                       => $esMenor,
                'es_adulto_mayor'                => $esAdultoMayor,
                'modo_firma'                     => $modoFirma,
                'requiere_responsable'           => $requiereResponsable,
                'tipo_representacion'            => $tipoRepresentacion,
                'relacion_responsable_paciente'  => $relacionResponsable ?: null,
                'motivo_responsable'             => $motivoResponsable ?: null,
                'tutor_nombre'                   => $tutorNombre ?: null,
                'tutor_apellido_pa'              => $tutorApellidoPa ?: null,
                'tutor_apellido_ma'              => $tutorApellidoMa ?: null,
                'tutor_curp'                     => $tutorCurp ?: null,
                'tutor_rfc'                      => $tutorRfc ?: null,
                'tutor_fecha_nacimiento'         => $tutorFechaNac ?: null,
                'tutor_correo'                   => $tutorCorreo ?: null,
                'tutor_telefono'                 => $tutorTelefono ?: null,
                'tutor_nacionalidad_tipo'        => $tutorNacionalidadTipo,
                'tutor_nacionalidad'             => $tutorNacionalidad ?: null,
                'tutor_pais_nacimiento'          => $tutorPaisNacimiento ?: null,
                'tutor_tipo_documento_identidad' => $tutorTipoDocIdentidad ?: null,
                'tutor_pais_documento_identidad' => $tutorPaisDocIdentidad ?: null,
                'tutor_numero_documento_identidad'=> $tutorNumDocIdentidad ?: null,
                'adulto_mayor_pasaportes_validados'=> $amPasaportesValidados,
                'adulto_mayor_pasaporte_1_json'  => $amPasaporte1Json ?: null,
                'adulto_mayor_pasaporte_2_json'  => $amPasaporte2Json ?: null,
                'stripe_payment_intent_id'       => $stripeIntentId,
                'estatus_orden'                  => 'PENDIENTE',
                'estatus_pago'                   => 'PAGADO',
                'proveedor_pasarela'             => 'STRIPE',
                'user_creo'                      => $correo,
                'fecha_orden'                    => $ahora,
                'created_at'                     => $ahora,
                'updated_at'                     => $ahora,
            ]);

            // 5b. Guardar documentos del paciente
            $baseDir  = "private/ordenes_pats/{$idOrden}";
            $docPaths = [];

            $docsCampos = [
                'doc_identificacion_frente'  => 'ID_FRENTE',
                'doc_identificacion_reverso' => 'ID_REVERSO',
                'doc_curp'                   => 'CURP_DOC',
                'doc_comprobante_domicilio'  => 'COMPROBANTE_DOM',
                'doc_constancia_fiscal'      => 'CONSTANCIA_FISCAL',
            ];

            foreach ($docsCampos as $campo => $tipo) {
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $file  = $request->file($campo);
                    $ext   = preg_replace('/[^a-z0-9]/i', '', strtolower($file->getClientOriginalExtension())) ?: 'bin';
                    $path  = "{$baseDir}/{$tipo}_{$ahora->format('YmdHis')}_" . Str::random(6) . ".{$ext}";
                    Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));
                    $docPaths[$tipo] = $path;
                }
            }

            // 5c. Documentos del tutor/responsable
            if ($requiereResponsable) {
                $docsTutor = [
                    'tutor_doc_identificacion_frente'  => 'TUTOR_ID_FRENTE',
                    'tutor_doc_identificacion_reverso' => 'TUTOR_ID_REVERSO',
                    'tutor_doc_curp'                   => 'TUTOR_CURP',
                    'tutor_doc_constancia_fiscal'      => 'TUTOR_CIF',
                    'doc_acreditacion_representacion'  => 'ACREDITACION_REP',
                ];
                foreach ($docsTutor as $campo => $tipo) {
                    if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                        $file  = $request->file($campo);
                        $ext   = preg_replace('/[^a-z0-9]/i', '', strtolower($file->getClientOriginalExtension())) ?: 'bin';
                        $path  = "{$baseDir}/{$tipo}_{$ahora->format('YmdHis')}_" . Str::random(6) . ".{$ext}";
                        Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));
                        $docPaths[$tipo] = $path;
                    }
                }
            }

            // 5d. Foto (base64 → archivo)
            $fotoPath = null;
            if (str_starts_with($fotoBase64, 'data:')) {
                $fotoData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64));
                $fotoPath = "{$baseDir}/FOTO_{$ahora->format('YmdHis')}.jpg";
                Storage::disk('local')->put($fotoPath, $fotoData);
                $docPaths['FOTO'] = $fotoPath;
            }

            // 5e. Firma (base64 → archivo)
            $firmaPath = null;
            if (str_starts_with($firmaBase64, 'data:')) {
                $firmaData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $firmaBase64));
                $firmaPath = "{$baseDir}/FIRMA_{$ahora->format('YmdHis')}.png";
                Storage::disk('local')->put($firmaPath, $firmaData);
                $docPaths['FIRMA'] = $firmaPath;
            }

            // 5f. Nombre firmante
            $nombreFirmante = $requiereResponsable && $tutorNombre !== ''
                ? implode(' ', array_filter([$tutorNombre, $tutorApellidoPa, $tutorApellidoMa]))
                : implode(' ', array_filter([$nombre, $apellidoPa, $apellidoMa]));

            // 5g. Actualizar rutas de archivos en la orden
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'foto_path'           => $fotoPath,
                'firma_path'          => $firmaPath,
                'nombre_firmante'     => $nombreFirmante,
                'fecha_firma'         => $ahora,
                'ip_firma'            => $request->ip(),
                'user_agent_firma'    => substr((string) $request->userAgent(), 0, 500),
                'payload_checkout_json' => json_encode([
                    'token_publico' => $token,
                    'ctx'           => $ctx,
                    'domicilio'     => $domicilio,
                    'doc_paths'     => $docPaths,
                ], JSON_UNESCAPED_UNICODE),
                'updated_at'          => now(),
            ]);

            // 5h. Insertar en pats_pagos
            DB::table('pats_pagos')->insert([
                'tipo_solicitud'      => 'pats',
                'id_solicitud'        => $idOrden,
                'pasarela'            => 'stripe',
                'referencia_pasarela' => $stripeIntentId,
                'estatus'             => 'succeeded',
                'monto'               => $montoOrden,
                'moneda'              => 'MXN',
                'metadata_json'       => json_encode([
                    'frecuencia' => $frecuencia,
                    'correo'     => $correo,
                ], JSON_UNESCAPED_UNICODE),
                'created_at'          => $ahora,
                'updated_at'          => $ahora,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SolicitudPats.generarOrden', [
                'error'  => $e->getMessage(),
                'correo' => $correo,
            ]);
            return response()->json([
                'ok'    => false,
                'error' => 'No fue posible guardar el registro. Intenta nuevamente.',
            ], 500);
        }

        return response()->json([
            'ok'        => true,
            'id_orden'  => $idOrden,
            'referencia'=> $referencia,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /pats/registro/contrato
    // ─────────────────────────────────────────────────────────────────────────

    public function contratoPreview(Request $request): JsonResponse
    {
        $nombre     = trim((string) $request->input('nombre_usuario', ''));
        $apPa       = trim((string) $request->input('apellido_pa', ''));
        $apMa       = trim((string) $request->input('apellido_ma', ''));
        $curp       = trim((string) $request->input('curp_usuario', ''));
        $correo     = trim((string) $request->input('correo_usuario_pats', ''));
        $telefono   = trim((string) $request->input('telefono_usuario', ''));
        $frecuencia = strtoupper(trim((string) $request->input('frecuencia', 'MENSUAL')));
        $monto      = $frecuencia === 'ANUAL' ? self::MONTO_ANUAL : self::MONTO_MENSUAL;

        $nacionalidadTipo = strtoupper(trim((string) $request->input('nacionalidad_tipo', 'MEXICANA')));
        $numDocId         = trim((string) $request->input('numero_documento_identidad', ''));

        $tipoPaciente      = strtoupper(trim((string) $request->input('tipo_paciente', 'ADULTO')));
        $modoFirma         = strtoupper(trim((string) $request->input('modo_firma', 'FIRMA_PROPIA')));
        $reqResponsable    = (bool) (int) $request->input('requiere_responsable', '0');

        $tutorNombre    = trim((string) $request->input('tutor_nombre', ''));
        $tutorApPa      = trim((string) $request->input('tutor_apellido_pa', ''));
        $tutorApMa      = trim((string) $request->input('tutor_apellido_ma', ''));
        $tutorCurp      = trim((string) $request->input('tutor_curp', ''));
        $tutorCorreo    = trim((string) $request->input('tutor_correo', ''));
        $tutorTelefono  = trim((string) $request->input('tutor_telefono', ''));
        $relacionResp   = trim((string) $request->input('relacion_responsable_paciente', ''));

        $domParts = array_filter([
            trim($request->input('dom_calle', '') . ' ' . $request->input('dom_num_ext', '')),
            $request->input('dom_num_int', '') ? 'Int. ' . $request->input('dom_num_int', '') : '',
            $request->input('dom_colonia', '') ? 'Col. ' . $request->input('dom_colonia', '') : '',
            $request->input('dom_cp', '') ? 'C.P. ' . $this->digits($request->input('dom_cp', '')) : '',
            $request->input('dom_municipio', ''),
            $request->input('dom_estado', ''),
            $request->input('dom_pais', ''),
        ]);
        $domicilio = implode(', ', $domParts);

        $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto',
                  'septiembre','octubre','noviembre','diciembre'];
        $now   = Carbon::now();
        $fecha = $now->day . ' de ' . $meses[$now->month - 1] . ' de ' . $now->year;

        $fullName     = implode(' ', array_filter([$nombre, $apPa, $apMa]));
        $tutorName    = implode(' ', array_filter([$tutorNombre, $tutorApPa, $tutorApMa]));
        $frecText     = $frecuencia === 'ANUAL' ? 'anual' : 'mensual';
        $vigText      = $frecuencia === 'ANUAL' ? '12 meses' : '30 días';
        $montoFmt     = '$' . number_format($monto, 0, '.', ',');
        $isMx         = $nacionalidadTipo === 'MEXICANA';
        $esMenor      = $tipoPaciente === 'MENOR';
        $firmante     = ($reqResponsable && $tutorName !== '') ? $tutorName : $fullName;
        $firmanteCurp = ($reqResponsable && $tutorCurp !== '') ? $tutorCurp : $curp;

        $e = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $identidadPaciente = $isMx
            ? '<p><strong>CURP:</strong> ' . $e($curp ?: '________________') . '</p>'
            : '<p><strong>Identificación:</strong> ' . $e($numDocId ?: '________________') . '</p>';

        $htmlResponsable = '';
        if ($reqResponsable && $tutorName !== '') {
            $htmlResponsable = '
<h4>Datos de la persona responsable</h4>
<p><strong>Nombre:</strong> ' . $e($tutorName ?: '________________') . '</p>
<p><strong>' . ($isMx ? 'CURP' : 'Identificación') . ':</strong> ' . $e($tutorCurp ?: '________________') . '</p>
<p><strong>Correo:</strong> ' . $e($tutorCorreo ?: '________________') . '</p>
<p><strong>Teléfono:</strong> ' . $e($tutorTelefono ?: '________________') . '</p>
<p><strong>Relación con el paciente:</strong> ' . $e($relacionResp ?: '________________') . '</p>';
        }

        $htmlAdultoMayor = '';
        if ($tipoPaciente === 'ADULTO_MAYOR') {
            $htmlAdultoMayor = '
<h4>Requisito de adulto mayor</h4>
<p>El afiliado ha validado 2 pasaportes PATS vigentes como requisito para la activación.</p>';
        }

        $html = '
<h3>Contrato de adquisición de tarjeta de descuento</h3>
<p>Contrato celebrado entre <strong>"EL PRESTADOR"</strong> y <strong>"EL AFILIADO"</strong>.</p>

<h4>Paciente / Beneficiario del pasaporte</h4>
<p><strong>Nombre completo:</strong> ' . $e($fullName ?: '________________') . '</p>
' . $identidadPaciente . '
<p><strong>Correo:</strong> '    . $e($correo   ?: '________________') . '</p>
<p><strong>Teléfono:</strong> '  . $e($telefono ?: '________________') . '</p>
<p><strong>Domicilio:</strong> ' . $e($domicilio ?: '________________') . '</p>
' . $htmlResponsable . '
' . $htmlAdultoMayor . '

<h4>Objeto</h4>
<p>El prestador otorga a título oneroso y temporal el uso de la Tarjeta de Descuento en favor
del afiliado, para el aprovechamiento de un programa de beneficios que permite acceder a
descuentos y servicios médicos privados de alta calidad de forma accesible, clara y sin
restricciones conforme al programa PATS.</p>

<h4>Precio</h4>
<p>La Tarjeta de Descuento tendrá un costo ' . $e($frecText) . ' de
<strong>' . $e($montoFmt) . '</strong>, IVA incluido, pagadero dentro de los primeros
cinco días de cada período.</p>

<h4>Vigencia</h4>
<p>La tarjeta tendrá una vigencia de ' . $e($vigText) . ' y se prorrogará automáticamente
siempre que el afiliado continúe cubriendo el pago ' . $e($frecText) . '.</p>

<h4>Beneficios</h4>
<p>El afiliado gozará de los beneficios del programa incluyendo consultas médicas y costos
preferenciales dentro de la red aplicable, conforme al tabulador vigente.</p>

<h4>Protección de datos</h4>
<p>El afiliado autoriza el uso de sus datos para la gestión de la tarjeta y reconoce que el
prestador no es una aseguradora ni asume responsabilidad por los servicios médicos prestados
por terceros.</p>

<h4>Jurisdicción</h4>
<p>Las partes se someten a la jurisdicción de los tribunales competentes del Distrito Judicial
de Puebla, Estado de Puebla.</p>

<p>Leído y entendido, se firma electrónicamente el día
<strong>' . $e($fecha) . '</strong>
por <strong>' . $e($firmante ?: '________________') . '</strong>.</p>';

        return response()->json(['ok' => true, 'html' => $html]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function ctxDirecto(): array
    {
        return [
            'tipo_origen'    => 'DIRECTO',
            'id_distribuidor'=> 0,
            'id_franquicia'  => 0,
            'id_gestor'      => 0,
            'pais'           => 'México',
            'region'         => '',
            'zona'           => null,
            'unidad'         => 'PATS',
            'nombre'         => 'PATS',
        ];
    }

    private function resolverToken(string $token): ?array
    {
        $dist = DB::table('pats_distribuidores')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->first();

        if ($dist) {
            return [
                'tipo_origen'    => 'DISTRIBUIDOR',
                'id_distribuidor'=> (int) $dist->id_distribuidor,
                'id_franquicia'  => (int) $dist->id_franquicia,
                'id_gestor'      => 0,
                'pais'           => (string) ($dist->pais   ?? 'México'),
                'region'         => (string) ($dist->region ?? ''),
                'zona'           => (string) ($dist->zona   ?? ''),
                'unidad'         => (string) ($dist->unidad ?? 'PATS'),
                'nombre'         => (string) ($dist->nombre ?? ''),
            ];
        }

        $franq = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('activo', 1)
            ->first();

        if ($franq) {
            return [
                'tipo_origen'    => 'FRANQUICIA',
                'id_distribuidor'=> 0,
                'id_franquicia'  => (int) $franq->id_franquicia,
                'id_gestor'      => 0,
                'pais'           => (string) ($franq->pais   ?? 'México'),
                'region'         => (string) ($franq->region ?? ''),
                'zona'           => (string) ($franq->zona   ?? ''),
                'unidad'         => (string) ($franq->unidad ?? 'PATS'),
                'nombre'         => (string) ($franq->nombre ?? ''),
            ];
        }

        return null;
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
