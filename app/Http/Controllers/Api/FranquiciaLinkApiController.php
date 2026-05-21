<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

class FranquiciaLinkApiController extends Controller
{
    private const PREFILL_FIELDS = [
        'nombre', 'apellido_paterno', 'apellido_materno',
        'correo', 'telefono',
        'pais', 'region', 'municipio',
        'calle', 'num_ext', 'num_int', 'cp', 'colonia',
        'rfc', 'tipo_persona', 'razon_social',
        'banco', 'numero_cuenta', 'clabe', 'titular_cuenta',
        'ocupacion', 'nacionalidad',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    //  GET /api/franquicia-links
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 50), 100);

        $links = DB::table('franquicia_links')
            ->orderByDesc('id')
            ->paginate($perPage);

        $appUrl = rtrim(config('app.url'), '/');

        $items = collect($links->items())->map(function ($lnk) use ($appUrl) {
            $row = (array) $lnk;
            unset($row['password']);
            $row['url']            = "{$appUrl}/franquicia/link/{$lnk->token}";
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
    //  POST /api/franquicia-links
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $password = trim((string) $request->input('password', ''));
        if ($password === '') {
            return response()->json(['ok' => false, 'error' => 'El campo password es obligatorio.'], 422);
        }

        $prefill = [];
        foreach (self::PREFILL_FIELDS as $field) {
            $val = trim((string) $request->input("prefill_{$field}", ''));
            if ($val !== '') $prefill[$field] = $val;
        }

        $token  = Str::uuid()->toString();
        $appUrl = rtrim(config('app.url'), '/');

        $id = DB::table('franquicia_links')->insertGetId([
            'token'        => $token,
            'id_solicitud' => 0,
            'password'     => Hash::make($password),
            'active'       => 1,
            'prefill_json' => empty($prefill) ? null : json_encode($prefill, JSON_UNESCAPED_UNICODE),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'ok'    => true,
            'id'    => $id,
            'token' => $token,
            'url'   => "{$appUrl}/franquicia/link/{$token}",
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  DELETE /api/franquicia-links/{id}
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $link = DB::table('franquicia_links')->where('id', $id)->first();

        if (! $link) {
            return response()->json(['ok' => false, 'error' => 'Link no encontrado.'], 404);
        }

        if ($link->id_solicitud != 0) {
            return response()->json(['ok' => false, 'error' => 'No se puede eliminar un link ya utilizado.'], 409);
        }

        DB::table('franquicia_links')->where('id', $id)->delete();

        return response()->json(['ok' => true, 'message' => 'Link eliminado.']);
    }
}
