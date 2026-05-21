<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log, Storage};

class SolicitudDistribuidorAdminController extends Controller
{
    const ESTATUS_LABELS = [
        'ENVIADA'           => ['texto' => 'Enviada',           'badge' => 'warning'],
        'EN_REVISION'       => ['texto' => 'En Revisión',       'badge' => 'info'],
        'APROBADA'          => ['texto' => 'Aprobada',          'badge' => 'success'],
        'RECHAZADA'         => ['texto' => 'Rechazada',         'badge' => 'danger'],
        'CONTRATO_ENVIADO'  => ['texto' => 'Contrato Enviado',  'badge' => 'primary'],
        'CONTRATO_RECIBIDO' => ['texto' => 'Contrato Recibido', 'badge' => 'secondary'],
        'CONVERTIDA_ALTA'   => ['texto' => 'Convertida a Alta', 'badge' => 'dark'],
    ];

    // Transiciones válidas: estatus_actual → [accion → estatus_nuevo]
    const TRANSICIONES = [
        'ENVIADA'           => ['iniciar_revision' => 'EN_REVISION'],
        'EN_REVISION'       => ['aprobar' => 'APROBADA', 'rechazar' => 'RECHAZADA'],
        'APROBADA'          => ['enviar_contrato' => 'CONTRATO_ENVIADO'],
        'CONTRATO_ENVIADO'  => ['marcar_recibido' => 'CONTRATO_RECIBIDO'],
        'CONTRATO_RECIBIDO' => ['convertir_alta'  => 'CONVERTIDA_ALTA'],
    ];

    const ACCION_META = [
        'iniciar_revision' => ['label' => 'Iniciar Revisión',     'color' => 'info',      'icono' => 'mdi-magnify'],
        'aprobar'          => ['label' => 'Aprobar Solicitud',     'color' => 'success',   'icono' => 'mdi-check-circle'],
        'rechazar'         => ['label' => 'Rechazar',              'color' => 'danger',    'icono' => 'mdi-close-circle'],
        'enviar_contrato'  => ['label' => 'Enviar Contrato',       'color' => 'primary',   'icono' => 'mdi-file-send'],
        'marcar_recibido'  => ['label' => 'Contrato Recibido',     'color' => 'secondary', 'icono' => 'mdi-file-check'],
        'convertir_alta'   => ['label' => 'Convertir a Alta',      'color' => 'dark',      'icono' => 'mdi-account-check'],
    ];

    // Tipos de archivo válidos para servir
    const TIPOS_DOC = [
        'INE', 'COMPROBANTE_DOMICILIO', 'CEDULA_FISCAL',
        'CONTRATO_FIRMADO', 'CARATULA_BANCARIA', 'ACTA_CONSTITUTIVA', 'PODER_NOTARIAL',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  LISTADO
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $estatus = strtoupper(trim((string) $request->input('estatus', '')));
        $region  = strtoupper(trim((string) $request->input('region', '')));

        $query = DB::table('pats_solicitudes_distribuidor')->orderByDesc('id_solicitud');

        if ($estatus !== '') {
            $query->where('estatus', $estatus);
        }
        if ($region !== '') {
            $query->where('region', $region);
        }
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = "%{$q}%";
                $sub->where('nombre',        'like', $like)
                    ->orWhere('correo',       'like', $like)
                    ->orWhere('rfc',          'like', $like)
                    ->orWhere('telefono',     'like', $like)
                    ->orWhere('razon_social', 'like', $like);
            });
        }

        $solicitudes = $query->paginate(20)->withQueryString();

        $conteoEstatus = DB::table('pats_solicitudes_distribuidor')
            ->selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        return view('admin.solicitudes_distribuidor.index', [
            'solicitudes'   => $solicitudes,
            'conteoEstatus' => $conteoEstatus,
            'estatusLabels' => self::ESTATUS_LABELS,
            'filtroQ'       => $q,
            'filtroEstatus' => $estatus,
            'filtroRegion'  => $region,
            'totalGeneral'  => DB::table('pats_solicitudes_distribuidor')->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  DETALLE
    // ─────────────────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $solicitud = DB::table('pats_solicitudes_distribuidor')
            ->where('id_solicitud', $id)
            ->first();

        abort_if(! $solicitud, 404, 'Solicitud no encontrada.');

        $documentos = DB::table('pats_solicitudes_distribuidor_documentos')
            ->where('id_solicitud', $id)
            ->where('vigente', 1)
            ->orderBy('tipo_documento')
            ->get();

        $historial = DB::table('pats_solicitudes_distribuidor_historial')
            ->where('id_solicitud', $id)
            ->orderBy('fecha_evento')
            ->get()
            ->map(function ($h) {
                $h->payload = $h->payload_json ? json_decode($h->payload_json, true) : null;
                return $h;
            });

        $preview = DB::table('pats_preview_dist')
            ->where('id_solicitud', $id)
            ->first();

        $esquema = $solicitud->esquema_pagos_json
            ? json_decode($solicitud->esquema_pagos_json, true)
            : null;

        $transicionesDisponibles = self::TRANSICIONES[$solicitud->estatus] ?? [];

        return view('admin.solicitudes_distribuidor.detalle', [
            'solicitud'               => $solicitud,
            'documentos'              => $documentos,
            'historial'               => $historial,
            'preview'                 => $preview,
            'esquema'                 => $esquema,
            'estatusLabels'           => self::ESTATUS_LABELS,
            'transicionesDisponibles' => $transicionesDisponibles,
            'accionMeta'              => self::ACCION_META,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  ACCIÓN (CAMBIO DE ESTATUS)
    // ─────────────────────────────────────────────────────────────────────────

    public function accion(Request $request, int $id): JsonResponse
    {
        $accion = trim((string) $request->input('accion', ''));

        $solicitud = DB::table('pats_solicitudes_distribuidor')
            ->where('id_solicitud', $id)
            ->first();

        if (! $solicitud) {
            return response()->json(['ok' => false, 'error' => 'Solicitud no encontrada.'], 404);
        }

        $transiciones = self::TRANSICIONES[$solicitud->estatus] ?? [];

        if (! isset($transiciones[$accion])) {
            return response()->json([
                'ok'    => false,
                'error' => "La acción '{$accion}' no está disponible en el estado '{$solicitud->estatus}'.",
            ], 422);
        }

        $estatusNuevo = $transiciones[$accion];
        $update       = ['estatus' => $estatusNuevo, 'updated_at' => now()];
        $payload      = ['accion' => $accion];

        // Rechazar: motivo obligatorio
        if ($accion === 'rechazar') {
            $motivo = trim((string) $request->input('motivo', ''));
            if ($motivo === '') {
                return response()->json(['ok' => false, 'error' => 'El motivo de rechazo es obligatorio.'], 422);
            }
            $update['motivo_rechazo'] = $motivo;
            $payload['motivo']        = $motivo;
        }

        // Aprobar: observaciones opcionales + timestamp
        if ($accion === 'aprobar') {
            $obs = trim((string) $request->input('observaciones', ''));
            if ($obs !== '') {
                $update['observaciones_admin'] = $obs;
                $payload['observaciones']       = $obs;
            }
            $update['fecha_autorizacion'] = now();
        }

        // Enviar contrato: archivo obligatorio
        if ($accion === 'enviar_contrato') {
            if (! $request->hasFile('contrato_admin') || ! $request->file('contrato_admin')->isValid()) {
                return response()->json(['ok' => false, 'error' => 'El archivo del contrato es obligatorio.'], 422);
            }
            $file     = $request->file('contrato_admin');
            $ext      = preg_replace('/[^a-z0-9]/i', '', strtolower($file->getClientOriginalExtension())) ?: 'pdf';
            $filename = now()->format('YmdHis') . '_contrato_admin.' . $ext;
            $path     = "private/solicitudes/distribuidor/{$id}/{$filename}";

            Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

            $update['contrato_admin_path']  = $path;
            $update['fecha_envio_contrato'] = now();
            $payload['contrato_path']       = $path;
        }

        // Marcar recibido
        if ($accion === 'marcar_recibido') {
            $update['fecha_carga_firmado'] = now();
        }

        // Convertir a alta
        if ($accion === 'convertir_alta') {
            $update['fecha_conversion_alta'] = now();
        }

        DB::beginTransaction();
        try {
            DB::table('pats_solicitudes_distribuidor')
                ->where('id_solicitud', $id)
                ->update($update);

            DB::table('pats_solicitudes_distribuidor_historial')->insert([
                'id_solicitud'     => $id,
                'evento_tipo'      => $accion,
                'estatus_anterior' => $solicitud->estatus,
                'estatus_nuevo'    => $estatusNuevo,
                'payload_json'     => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'user_evento'      => null,
                'fecha_evento'     => now(),
                'created_at'       => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SolicitudDistribuidorAdmin.accion', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['ok' => false, 'error' => 'Error interno al procesar la acción.'], 500);
        }

        return response()->json([
            'ok'           => true,
            'estatus_nuevo' => $estatusNuevo,
            'label'         => self::ESTATUS_LABELS[$estatusNuevo]['texto'] ?? $estatusNuevo,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SERVIR ARCHIVOS PRIVADOS
    // ─────────────────────────────────────────────────────────────────────────

    public function archivo(int $id, string $tipo): mixed
    {
        // Verificar que la solicitud existe
        abort_if(
            ! DB::table('pats_solicitudes_distribuidor')->where('id_solicitud', $id)->exists(),
            404
        );

        [$path, $mime] = match (true) {
            in_array($tipo, ['selfie', 'firma', 'contrato_preview'], true) => $this->pathPreview($id, $tipo),
            $tipo === 'contrato_admin'                                       => $this->pathContratoAdmin($id),
            in_array($tipo, self::TIPOS_DOC, true)                          => $this->pathDocumento($id, $tipo),
            default                                                          => abort(404),
        };

        abort_if(! $path || ! Storage::disk('local')->exists($path), 404, 'Archivo no encontrado.');

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $mime ?? 'application/octet-stream',
        ]);
    }

    private function pathPreview(int $id, string $tipo): array
    {
        $p = DB::table('pats_preview_dist')->where('id_solicitud', $id)->first();
        if (! $p) return [null, null];

        return match ($tipo) {
            'selfie'           => [$p->selfie_path,   $p->selfie_mime],
            'firma'            => [$p->firma_path,     $p->firma_mime],
            'contrato_preview' => [$p->contrato_path,  $p->contrato_mime],
            default            => [null, null],
        };
    }

    private function pathContratoAdmin(int $id): array
    {
        $path = DB::table('pats_solicitudes_distribuidor')
            ->where('id_solicitud', $id)
            ->value('contrato_admin_path');

        return [$path, 'application/pdf'];
    }

    private function pathDocumento(int $id, string $tipo): array
    {
        $doc = DB::table('pats_solicitudes_distribuidor_documentos')
            ->where('id_solicitud', $id)
            ->where('tipo_documento', $tipo)
            ->where('vigente', 1)
            ->first();

        return $doc ? [$doc->archivo_path, $doc->mime_type] : [null, null];
    }
}
