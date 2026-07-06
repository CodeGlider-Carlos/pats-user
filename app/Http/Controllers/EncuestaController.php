<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarEncuestaRequest;
use App\Models\EncuestaSatisfaccion;
use App\Models\PatsAcceso;
use App\Models\TarjetaMisional;
use App\Support\EncuestaPats;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    /**
     * Guarda (o rechaza) la encuesta de satisfacción de una tarjeta
     * misional cerrada del usuario autenticado.
     */
    public function guardar(GuardarEncuestaRequest $request): JsonResponse
    {
        /** @var PatsAcceso $user */
        $user = auth('pasaporte')->user();

        $codePasaporte = $this->codePasaporte($user);

        if ($codePasaporte === null) {
            return response()->json(['error' => 'No se encontró el pasaporte del usuario.'], 422);
        }

        $tarjeta = TarjetaMisional::query()
            ->where('id', $request->integer('id_tarjeta'))
            ->where('code_pasaporte', $codePasaporte)
            ->where('activo', 0)
            ->first();

        if ($tarjeta === null) {
            return response()->json(['error' => 'Registro de atención no encontrado.'], 404);
        }

        if ($tarjeta->reviewed !== null) {
            return response()->json(['success' => true, 'message' => 'La encuesta ya había sido registrada.']);
        }

        $rechazo = $request->input('accion') === 'rechazar';
        $tipo = EncuestaPats::clasificarServicio($tarjeta->modelo);

        $datos = array_merge([
            'id_tarjeta' => (int) $tarjeta->id,
            'code_pasaporte' => $codePasaporte,
            'id_pasaporte' => $user->id_pasaporte,
            'tipo_servicio' => $tipo,
            'modelo' => $tarjeta->modelo,
            'estatus' => $rechazo ? TarjetaMisional::REVIEW_REJECTED : TarjetaMisional::REVIEW_DONE,
        ], $rechazo ? [] : $this->respuestas($request, $tipo));

        EncuestaSatisfaccion::create($datos);

        $tarjeta->reviewed = $rechazo ? TarjetaMisional::REVIEW_REJECTED : TarjetaMisional::REVIEW_DONE;
        $tarjeta->save();

        return response()->json(['success' => true]);
    }

    /**
     * Extrae solo las respuestas aplicables al tipo de servicio.
     *
     * @return array<string, int|string|null>
     */
    private function respuestas(GuardarEncuestaRequest $request, string $tipo): array
    {
        $datos = [];

        foreach (EncuestaPats::preguntasParaTipo($tipo) as $pregunta) {
            $key = $pregunta['key'];

            if ($pregunta['tipo'] === 'texto') {
                $datos[$key] = $request->filled($key) ? trim((string) $request->input($key)) : null;

                continue;
            }

            $datos[$key] = $request->filled($key) ? (int) $request->input($key) : null;

            if ($pregunta['comentario']) {
                $datos[$key.'_com'] = $request->filled($key.'_com') ? trim((string) $request->input($key.'_com')) : null;
            }
        }

        return $datos;
    }

    /**
     * code_pasaporte del pasaporte asociado al usuario autenticado.
     */
    private function codePasaporte(PatsAcceso $user): ?string
    {
        if (! $user->id_pasaporte) {
            return null;
        }

        return DB::table('pats_pasaportes')
            ->where('id_pasaporte', $user->id_pasaporte)
            ->value('code_pasaporte');
    }
}
