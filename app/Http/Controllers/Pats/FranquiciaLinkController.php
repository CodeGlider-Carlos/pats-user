<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Hash, Log, Storage};
use Illuminate\Support\Str;

class FranquiciaLinkController extends Controller
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
    //  GET /franquicia/link/{token}  — Password gate
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404, 'El enlace no existe o ya no está disponible.');

        if (session("franq_link_auth_{$token}")) {
            return redirect()->route('franq.link.formulario', $token);
        }

        return view('pats.franquicia_link_password', compact('link', 'token'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /franquicia/link/{token}/auth
    // ─────────────────────────────────────────────────────────────────────────

    public function auth(Request $request, string $token): \Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404);

        if (! Hash::check((string) $request->input('password', ''), $link->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.'])->withInput();
        }

        session(["franq_link_auth_{$token}" => true]);

        return redirect()->route('franq.link.formulario', $token);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /franquicia/link/{token}/formulario
    // ─────────────────────────────────────────────────────────────────────────

    public function formulario(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $link = $this->findActiveLink($token);
        abort_if($link === null, 404);

        if (! session("franq_link_auth_{$token}")) {
            return redirect()->route('franq.link.show', $token);
        }

        return view('pats.solicitud_franquicia_link', [
            'link'    => $link,
            'token'   => $token,
            'estados' => self::ESTADOS,
            'prefill' => json_decode($link->prefill_json ?? 'null', true) ?: null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /franquicia/link/{token}/pre-validar
    // ─────────────────────────────────────────────────────────────────────────

    public function preValidar(Request $request, string $token): JsonResponse
    {
        if (! session("franq_link_auth_{$token}")) {
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
    //  POST /franquicia/link/{token}/guardar
    // ─────────────────────────────────────────────────────────────────────────

    public function guardar(Request $request, string $token): JsonResponse
    {
        if (! session("franq_link_auth_{$token}")) {
            return response()->json(['ok' => false, 'error' => 'Sesión no autorizada.'], 401);
        }

        $link = $this->findActiveLink($token);
        if ($link === null) {
            return response()->json(['ok' => false, 'error' => 'El enlace ya no está disponible o ya fue utilizado.'], 404);
        }

        return $this->procesarSolicitud($request, $link);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /franquicia/link/{token}/contrato-preview
    // ─────────────────────────────────────────────────────────────────────────

    public function contratoPreview(Request $request, string $token): \Illuminate\Http\Response
    {
        if (! session("franq_link_auth_{$token}")) {
            abort(401);
        }

        $link = $this->findActiveLink($token);
        if ($link) $this->applyPrefillToRequest($request, $link);

        $nombre       = $this->clean($request->input('nombre'));
        $apPaterno    = $this->clean($request->input('apellido_paterno'));
        $apMaterno    = $this->clean($request->input('apellido_materno'));
        $nombreCompleto = trim("{$nombre} {$apPaterno} {$apMaterno}");
        $tipoPersona  = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $razonSocial  = $this->clean($request->input('razon_social'));
        $rfc          = strtoupper($this->clean($request->input('rfc')));
        $pais         = $this->clean($request->input('pais'));
        $region       = strtoupper($this->clean($request->input('region')));
        $municipio    = $this->clean($request->input('municipio'));
        $calle        = $this->clean($request->input('calle'));
        $numExt       = $this->clean($request->input('num_ext'));
        $numInt       = $this->clean($request->input('num_int'));
        $cp           = $this->digits($request->input('cp'));
        $colonia      = $this->clean($request->input('colonia'));
        $banco        = $this->clean($request->input('banco'));
        $clabe        = $this->digits($request->input('clabe'));
        $titularCuenta = $this->clean($request->input('titular_cuenta'));
        $ocupacion    = $this->clean($request->input('ocupacion'));
        $nacionalidad = $this->clean($request->input('nacionalidad'));

        $estadoNombre = self::ESTADOS[$region] ?? $region;
        $direccion = implode(', ', array_filter([
            trim("{$calle} {$numExt}" . ($numInt ? " Int. {$numInt}" : '')),
            "Col. {$colonia}", "C.P. {$cp}", $municipio, $estadoNombre,
        ]));

        $denominacion = $tipoPersona === 'MORAL' && $razonSocial
            ? $razonSocial
            : $nombreCompleto;

        $data = [
            // Variables que usa el contrato
            'franquiciatario_denominacion'   => $denominacion ?: null,
            'franquiciatario_cargo'          => $tipoPersona === 'MORAL' ? 'Representante Legal' : 'Titular',
            'franquiciatario_representante'  => $nombreCompleto ?: null,
            'rfc_franquiciatario'            => $rfc ?: null,
            'domicilio_fiscal_franquiciatario' => $direccion ?: null,
            'territorio_autorizado'          => trim("{$municipio}, {$estadoNombre}") ?: null,
            'dia_firma'                      => now()->format('d'),
            'mes_firma'                      => now()->locale('es')->isoFormat('MMMM'),
            // Variables adicionales que puede usar el contrato
            'nombre'        => $nombre,      'apPaterno'   => $apPaterno,
            'apMaterno'     => $apMaterno,   'nombreCompleto' => $nombreCompleto,
            'tipoPersona'   => $tipoPersona, 'razonSocial' => $razonSocial,
            'rfc'           => $rfc,         'correo'      => $this->clean($request->input('correo')),
            'telefono'      => $this->digits($request->input('telefono')),
            'pais'          => $pais,        'region'      => $region,
            'municipio'     => $municipio,   'calle'       => $calle,
            'numExt'        => $numExt,      'numInt'      => $numInt,
            'cp'            => $cp,          'colonia'     => $colonia,
            'direccion'     => $direccion,   'estadoNombre' => $estadoNombre,
            'banco'         => $banco,       'clabe'       => $clabe,
            'titularCuenta' => $titularCuenta,
            'ocupacion'     => $ocupacion,   'nacionalidad' => $nacionalidad,
            'fechaInicio'   => now()->toDateString(),
            'fechaHoy'      => now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
        ];

        $html = view('pats.contrato-franquicia', $data)->render();

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LÓGICA CENTRAL
    // ─────────────────────────────────────────────────────────────────────────

    private function procesarSolicitud(Request $request, object $link): JsonResponse
    {
        $this->applyPrefillToRequest($request, $link);

        // ── Inputs ────────────────────────────────────────────────────────────
        $pais      = $this->clean($request->input('pais'));
        $region    = strtoupper($this->clean($request->input('region')));
        $municipio = $this->clean($request->input('municipio'));
        $calle     = $this->clean($request->input('calle'));
        $numExt    = $this->clean($request->input('num_ext'));
        $numInt    = $this->clean($request->input('num_int'));
        $cp        = $this->digits($request->input('cp'));
        $colonia   = $this->clean($request->input('colonia'));

        $direccion = implode(', ', array_filter([
            trim("{$calle} {$numExt}" . ($numInt ? " Int. {$numInt}" : '')),
            "Col. {$colonia}", "C.P. {$cp}", $municipio,
            self::ESTADOS[$region] ?? $region,
        ]));

        $tipoPersona = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $nombre      = $this->clean($request->input('nombre'));
        $apPaterno   = $this->clean($request->input('apellido_paterno'));
        $apMaterno   = $this->clean($request->input('apellido_materno'));
        $razonSocial = $this->clean($request->input('razon_social'));
        $rfc         = strtoupper($this->clean($request->input('rfc')));
        $telefono    = $this->digits($request->input('telefono'));
        $correo      = strtolower($this->clean($request->input('correo')));
        $ocupacion   = $this->clean($request->input('ocupacion'));
        $nacionalidad= $this->clean($request->input('nacionalidad'));

        $banco         = $this->clean($request->input('banco'));
        $numeroCuenta  = $this->clean($request->input('numero_cuenta'));
        $clabe         = $this->digits($request->input('clabe'));
        $titularCuenta = $this->clean($request->input('titular_cuenta'));

        $fechaInicio = now()->toDateString();
        $selfieData  = $request->input('selfie_data', '');
        $firmaData   = $request->input('firma_data', '');

        // ── Validación ────────────────────────────────────────────────────────
        $errorValidacion = $this->validarDatos($request);
        if ($errorValidacion !== null) return $errorValidacion;

        // ── Contrato digital ──────────────────────────────────────────────────
        $nombreCompleto = trim("{$nombre} {$apPaterno} {$apMaterno}");
        $contratoData = compact(
            'nombre', 'apPaterno', 'apMaterno', 'nombreCompleto',
            'tipoPersona', 'razonSocial', 'rfc', 'correo', 'telefono',
            'pais', 'region', 'municipio', 'calle', 'numExt', 'numInt', 'cp', 'colonia',
            'direccion', 'banco', 'clabe', 'titularCuenta', 'ocupacion', 'nacionalidad',
            'fechaInicio'
        );
        $contratoData['estadoNombre'] = self::ESTADOS[$region] ?? $region;
        $contratoData['fechaHoy']     = now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

        try {
            $contratoHtml = view('pats.contrato-franquicia', $contratoData)->render();
        } catch (\Throwable $e) {
            $contratoHtml = null;
            Log::warning('FranquiciaLink.contratoRender', ['error' => $e->getMessage()]);
        }

        $contratoHash = $contratoHtml ? hash('sha256', $contratoHtml) : null;

        // ── Transacción ───────────────────────────────────────────────────────
        DB::beginTransaction();

        try {
            $idSolicitud = DB::table('pats_solicitudes_franquicia')->insertGetId([
                'id_franquicia_link'     => $link->id,
                'id_franquicia_generada' => null,
                'id_gestor'              => 0,
                'token_origen'           => $link->token,
                'origen_solicitud'       => 'LINK_FRANQUICIA',
                'user_solicita'          => null,
                'user_valida'            => null,
                'user_autoriza'          => null,
                'pais'                   => $pais,
                'region'                 => $region,
                'zona'                   => $municipio,
                'unidad'                 => null,
                'nombre_comercial'       => '',
                'tipo_persona'           => $tipoPersona,
                'nombre_titular'         => $nombreCompleto,
                'razon_social'           => $razonSocial ?: null,
                'rfc'                    => $rfc ?: null,
                'telefono'               => $telefono,
                'correo'                 => $correo,
                'direccion'              => $direccion,
                'calle'                  => $calle ?: null,
                'numero_exterior'        => $numExt ?: null,
                'numero_interior'        => $numInt ?: null,
                'colonia'                => $colonia ?: null,
                'codigo_postal'          => $cp ?: null,
                'banco'                  => $banco ?: null,
                'numero_cuenta'          => $numeroCuenta ?: null,
                'clabe'                  => $clabe ?: null,
                'titular_cuenta'         => $titularCuenta ?: null,
                'modalidad_pago'         => 'CONTADO',
                'valor_total'            => 0.00,
                'enganche'               => 0.00,
                'saldo_financiado'       => 0.00,
                'plazo_meses'            => 0,
                'periodicidad'           => 'UNICA',
                'fecha_inicio'           => $fechaInicio,
                'fecha_primer_vencimiento' => null,
                'contrato_admin_path'    => null,
                'contrato_firmado_path'  => null,
                'contrato_digital_html'  => $contratoHtml,
                'contrato_digital_hash'  => $contratoHash,
                'contrato_digital_firmado_at' => now(),
                'firma_digital_nombre'   => $nombreCompleto,
                'firma_digital_rfc'      => $rfc ?: null,
                'firma_digital_ip'       => $request->ip(),
                'firma_digital_user_agent' => $request->userAgent(),
                'firma_digital_data'     => $firmaData,
                'estatus'                => 'ENVIADA',
                'motivo_rechazo'         => null,
                'observaciones_admin'    => null,
                'observaciones_solicitante' => null,
                'activo'                 => 1,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

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

            $previewRow['selfie_path']   = $selfieInfo['path'] ?? null;
            $previewRow['selfie_mime']   = $selfieInfo['mime'] ?? null;
            $previewRow['selfie_kb']     = $selfieInfo['kb']   ?? null;
            $previewRow['firma_path']    = $firmaInfo['path']  ?? null;
            $previewRow['firma_mime']    = $firmaInfo['mime']  ?? null;
            $previewRow['firma_kb']      = $firmaInfo['kb']    ?? null;
            $previewRow['contrato_path'] = 'static/contrato_franq.pdf';
            $previewRow['contrato_mime'] = 'application/pdf';
            $previewRow['contrato_kb']   = 662;

            DB::table('pats_preview_franq')->insert($previewRow);

            // Historial
            DB::table('pats_solicitudes_franquicia_historial')->insert([
                'id_solicitud'     => $idSolicitud,
                'evento_tipo'      => 'solicitud_enviada',
                'estatus_anterior' => null,
                'estatus_nuevo'    => 'ENVIADA',
                'payload_json'     => json_encode([
                    'nombre'       => $nombreCompleto,
                    'correo'       => $correo,
                    'region'       => $region,
                    'link_id'      => $link->id,
                    'origen'       => 'LINK_FRANQUICIA',
                ], JSON_UNESCAPED_UNICODE),
                'user_evento'  => null,
                'fecha_evento' => now(),
                'created_at'   => now(),
            ]);

            // Desactivar link
            DB::table('franquicia_links')
                ->where('id', $link->id)
                ->update(['id_solicitud' => $idSolicitud, 'active' => 0, 'updated_at' => now()]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('FranquiciaLink.procesarSolicitud', ['error' => $e->getMessage(), 'correo' => $correo ?? '']);
            return response()->json(['ok' => false, 'error' => 'No fue posible guardar la solicitud. Intenta de nuevo.'], 500);
        }

        $referencia = 'FRANQ-' . strtoupper(Str::random(6)) . '-' . $idSolicitud;

        session()->forget("franq_link_auth_{$link->token}");

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

    private function validarDatos(Request $request): ?JsonResponse
    {
        $nombre    = $this->clean($request->input('nombre'));
        $apPaterno = $this->clean($request->input('apellido_paterno'));
        $correo    = strtolower($this->clean($request->input('correo')));
        $telefono  = $this->digits($request->input('telefono'));
        $pais      = $this->clean($request->input('pais'));
        $region    = $this->clean($request->input('region'));
        $municipio = $this->clean($request->input('municipio'));
        $calle     = $this->clean($request->input('calle'));
        $numExt    = $this->clean($request->input('num_ext'));
        $cp        = $this->digits($request->input('cp'));
        $colonia   = $this->clean($request->input('colonia'));
        $clabe     = $this->digits($request->input('clabe'));
        $tipoPersona = strtoupper($this->clean($request->input('tipo_persona', 'FISICA')));
        $razonSocial = $this->clean($request->input('razon_social'));

        foreach (compact('nombre', 'apPaterno', 'correo', 'telefono', 'pais', 'region', 'municipio', 'calle', 'numExt', 'cp', 'colonia') as $campo => $valor) {
            if ($valor === '') return $this->err("El campo '{$campo}' es obligatorio.");
        }

        if (! filter_var($correo, FILTER_VALIDATE_EMAIL))      return $this->err('El correo electrónico no es válido.');
        if (strlen($telefono) !== 10)                          return $this->err('El teléfono debe tener 10 dígitos.');
        if ($clabe !== '' && strlen($clabe) !== 18)            return $this->err('La CLABE debe tener 18 dígitos.');
        if (! in_array($tipoPersona, ['FISICA', 'MORAL'], true)) return $this->err('Tipo de persona no válido.');
        if ($tipoPersona === 'MORAL' && $razonSocial === '')   return $this->err('Para persona moral es obligatoria la razón social.');

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
        if (! $request->input('acepta_terminos'))                                return $this->err('Debes aceptar los términos del contrato.');

        if (DB::table('pats_franquicias')->where('correo', $correo)->exists()) {
            return $this->err('Ya existe una franquicia registrada con ese correo.', 409);
        }
        if (DB::table('pats_solicitudes_franquicia')->where('correo', $correo)->whereNotIn('estatus', ['RECHAZADA', 'CONVERTIDA_ALTA'])->exists()) {
            return $this->err('Ya existe una solicitud activa con ese correo.', 409);
        }

        return null;
    }

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
        if (! empty($merge)) $request->merge($merge);
    }

    private function findActiveLink(string $token): ?object
    {
        return DB::table('franquicia_links')
            ->where('token', $token)
            ->where('active', 1)
            ->where('id_solicitud', 0)
            ->first();
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

        DB::table('pats_solicitudes_franquicia_documentos')
            ->where('id_solicitud', $idSolicitud)->where('tipo_documento', $tipoDoc)->where('vigente', 1)
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
            'user_alta'               => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    }

    private function clean(mixed $v): string  { return trim((string) ($v ?? '')); }
    private function digits(mixed $v): string  { return preg_replace('/\D+/', '', (string) ($v ?? '')); }
    private function err(string $msg, int $status = 422): JsonResponse { return response()->json(['ok' => false, 'error' => $msg], $status); }
}
