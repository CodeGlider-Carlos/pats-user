<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoDistribucionController extends Controller
{
    // ─── Mapa de estados de México ───────────────────────────────────────────

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
        'DGO'  => 'Durango',
        'GTO'  => 'Guanajuato',
        'GRO'  => 'Guerrero',
        'HGO'  => 'Hidalgo',
        'JAL'  => 'Jalisco',
        'MEX'  => 'Estado de México',
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

    // ─── Mostrar el formulario público ───────────────────────────────────────

    public function show(Request $request)
    {
        $token = trim((string) $request->query('t', ''));

        abort_if($token === '', 400, 'Falta token público.');

        $ctx = $this->resolveCheckoutToken($token);

        abort_if(! $ctx, 404, 'El link público no es válido o ya no está activo.');

        $precio = $this->getPrecioDistribucion();

        $region      = strtoupper(trim((string) ($ctx['region'] ?? '')));
        $actorTipo   = (string) ($ctx['actor_tipo_publico'] ?? '');

        return view('pats.pago_distribucion', [
            'token'              => $token,
            'ctx'                => $ctx,
            'precioDistribucion' => $precio,
            'actorTipo'          => $actorTipo,
            'nombreActor'        => (string) ($ctx['nombre_actor_publico'] ?? ''),
            'nombreFranquicia'   => (string) ($ctx['nombre_franquicia'] ?? ''),
            'region'             => $region,
            'zona'               => (string) ($ctx['zona'] ?? ''),
            'unidad'             => (string) ($ctx['unidad'] ?? ''),
            'pais'               => (string) ($ctx['pais'] ?? 'México'),
            'estadoNombre'       => self::ESTADOS[$region] ?? $region,
            'labelOrigen'        => $this->labelOrigen($actorTipo),
            'descOrigen'         => $this->descOrigen($actorTipo),
        ]);
    }

    // ─── Generar orden y redirigir a pasarela ────────────────────────────────

    public function generarOrden(Request $request): JsonResponse
    {
        // Validación básica de los campos del formulario
        $validated = $request->validate([
            'public_token'          => 'required|string',
            'nombre'                => 'required|string|max:180',
            'razon_social'          => 'nullable|string|max:220',
            'direccion'             => 'nullable|string',
            'telefono'              => ['required', 'string', 'regex:/^\d{10}$/'],
            'correo'                => 'required|email|max:180',
            'rfc'                   => 'nullable|string|max:30',
            'clabe'                 => ['nullable', 'string', 'regex:/^\d{18}$/'],
            'banco'                 => 'nullable|string|max:150',
            'numero_cuenta'         => 'nullable|string|max:60',
            'titular_cuenta'        => 'nullable|string|max:180',
            'modalidad_pago'        => 'required|in:CONTADO,ENGANCHE_DIFERIDO,DIFERIDO',
            'valor_total'           => 'required|numeric|min:0',
            'enganche'              => 'nullable|numeric|min:0',
            'saldo_financiado'      => 'nullable|numeric|min:0',
            'plazo_meses'           => 'nullable|integer|min:0',
            'periodicidad'          => 'nullable|in:MENSUAL,QUINCENAL,SEMANAL,UNICA',
            'fecha_inicio'          => 'required|date',
            'fecha_primer_vencimiento' => 'nullable|date',
            'id_franquicia'         => 'required|integer',
            'id_distribuidor_origen' => 'required|integer',
            'id_gestor_origen'      => 'required|integer',
            'actor_tipo_publico'    => 'required|string',
        ]);

        $token = $validated['public_token'];
        $ctx   = $this->resolveCheckoutToken($token);

        if (! $ctx) {
            return response()->json(['ok' => false, 'error' => 'Token inválido o expirado.'], 404);
        }

        // Aquí iría la lógica de negocio:
        // 1. Guardar solicitud en pats_solicitudes_distribuidor
        // 2. Guardar documentos en pats_solicitudes_distribuidor_documentos
        // 3. Crear orden de pago en pats_ordenes_pago
        // 4. Retornar URL de pasarela

        // Ejemplo de retorno — ajusta según tu pasarela:
        $checkoutUrl = route('pats.checkout.pagar', ['ref' => 'SIMULADO-' . time()]);

        return response()->json([
            'ok'           => true,
            'checkout_url' => $checkoutUrl,
        ]);
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function resolveCheckoutToken(string $token): ?array
    {
        // Busca en distribuidores
        $dist = DB::table('pats_distribuidores as d')
            ->join('pats_franquicias as f', 'f.id_franquicia', '=', 'd.id_franquicia')
            ->where('d.public_checkout_token', $token)
            ->where('d.public_checkout_activo', 1)
            ->where('d.activo', 1)
            ->select([
                'd.id_distribuidor',
                'd.nombre as nombre_actor_publico',
                'd.region',
                'd.zona',
                'd.unidad',
                'd.pais',
                'f.id_franquicia',
                'f.nombre_franquicia',
                'd.public_checkout_token',
            ])
            ->first();

        if ($dist) {
            return array_merge((array) $dist, [
                'actor_tipo_publico' => 'DISTRIBUIDOR',
                'id_gestor'          => 0,
            ]);
        }

        // Busca en gestores
        $gestor = DB::table('pats_gestores as g')
            ->join('pats_gestor_franquicias as gf', 'gf.id_gestor', '=', 'g.id_gestor')
            ->join('pats_franquicias as f', 'f.id_franquicia', '=', 'gf.id_franquicia')
            ->where('g.public_checkout_token', $token)
            ->where('g.public_checkout_activo', 1)
            ->where('g.activo', 1)
            ->where('gf.activo', 1)
            ->select([
                'g.id_gestor',
                'g.nombre_gestor as nombre_actor_publico',
                'f.region',
                'f.zona',
                'f.unidad',
                'f.pais',
                'f.id_franquicia',
                'f.nombre_franquicia',
                'g.public_checkout_token',
            ])
            ->first();

        if ($gestor) {
            return array_merge((array) $gestor, [
                'actor_tipo_publico'  => 'GESTOR',
                'id_distribuidor'     => 0,
            ]);
        }

        // Busca en franquicias directamente
        $franquicia = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->select([
                'id_franquicia',
                'nombre_franquicia',
                'nombre_franquicia as nombre_actor_publico',
                'region',
                'zona',
                'unidad',
                'pais',
                'public_checkout_token',
            ])
            ->first();

        if ($franquicia) {
            return array_merge((array) $franquicia, [
                'actor_tipo_publico' => 'FRANQUICIA',
                'id_distribuidor'    => 0,
                'id_gestor'          => 0,
            ]);
        }

        return null;
    }

    private function getPrecioDistribucion(): float
    {
        $row = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = 'distribucion'")
            ->whereRaw("LOWER(TRIM(modalidad)) = 'misma_region'")
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($row ?? 20000);
    }

    private function labelOrigen(string $tipo): string
    {
        return match ($tipo) {
            'GESTOR'       => 'Gestor autorizado',
            'DISTRIBUIDOR' => 'Distribuidor autorizado',
            default        => 'Franquicia autorizada',
        };
    }

    private function descOrigen(string $tipo): string
    {
        return match ($tipo) {
            'GESTOR'       => 'Este enlace pertenece a un gestor y registrará la distribución vinculada a su franquicia asociada.',
            'DISTRIBUIDOR' => 'Este enlace pertenece a un distribuidor y mantendrá la trazabilidad comercial de la franquicia asociada.',
            default        => 'Este enlace pertenece directamente a la franquicia y generará el alta de distribución sobre su propia estructura comercial.',
        };
    }
}
