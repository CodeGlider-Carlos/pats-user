<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

class DistribucionLinkApiController extends Controller
{
    private const PREFILL_FIELDS = [
        'gestor_token',
        'nombre', 'apellido_paterno', 'apellido_materno',
        'correo', 'telefono',
        'pais', 'region', 'municipio', 'ciudad',
        'calle', 'num_ext', 'num_int', 'cp', 'colonia',
        'fecha_nacimiento', 'pais_nacimiento', 'nacionalidad', 'ocupacion',
        'tipo_identificacion', 'identificacion_emitida_por', 'numero_identificacion',
        'rfc', 'tipo_persona',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /api/distribucion-links
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 50), 100);

        $links = DB::table('distribuidor_links')
            ->orderByDesc('id')
            ->paginate($perPage);

        $appUrl = rtrim(config('app.url'), '/');

        $items = collect($links->items())->map(function ($lnk) use ($appUrl) {
            $row = (array) $lnk;
            unset($row['password']);
            $row['url']            = "{$appUrl}/distribucion/link/{$lnk->token}";
            $row['prefill_campos'] = $lnk->prefill_json
                ? count((array) json_decode($lnk->prefill_json, true))
                : 0;
            return $row;
        });

        return response()->json([
            'ok'   => true,
            'data' => $items,
            'meta' => [
                'total'        => $links->total(),
                'per_page'     => $links->perPage(),
                'current_page' => $links->currentPage(),
                'last_page'    => $links->lastPage(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  POST /api/distribucion-links
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $password = trim((string) $request->input('password', ''));
        if ($password === '') {
            return response()->json(['ok' => false, 'error' => 'El campo password es obligatorio.'], 422);
        }

        $amount  = (float) str_replace(',', '', (string) $request->input('amount', '0'));
        $typePay = $request->input('type_pay', 'card') === 'free' ? 'free' : 'card';

        $prefill = [];
        foreach (self::PREFILL_FIELDS as $field) {
            $val = trim((string) $request->input("prefill_{$field}", ''));
            if ($val !== '') $prefill[$field] = $val;
        }

        $token = Str::uuid()->toString();
        $appUrl = rtrim(config('app.url'), '/');

        $id = DB::table('distribuidor_links')->insertGetId([
            'token'         => $token,
            'id_esquema'    => (int) $request->input('id_esquema', 0),
            'id_franquicia' => (int) $request->input('id_franquicia', 0),
            'id_solicitud'  => 0,
            'password'      => Hash::make($password),
            'active'        => 1,
            'amount'        => $amount,
            'type_pay'      => $typePay,
            'prefill_json'  => empty($prefill) ? null : json_encode($prefill, JSON_UNESCAPED_UNICODE),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'ok'    => true,
            'id'    => $id,
            'token' => $token,
            'url'   => "{$appUrl}/distribucion/link/{$token}",
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  DELETE /api/distribucion-links/{id}
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $link = DB::table('distribuidor_links')->where('id', $id)->first();

        if (! $link) {
            return response()->json(['ok' => false, 'error' => 'Link no encontrado.'], 404);
        }

        if ($link->id_solicitud != 0) {
            return response()->json(['ok' => false, 'error' => 'No se puede eliminar un link ya utilizado.'], 409);
        }

        DB::table('distribuidor_links')->where('id', $id)->delete();

        return response()->json(['ok' => true, 'message' => 'Link eliminado.']);
    }
}
