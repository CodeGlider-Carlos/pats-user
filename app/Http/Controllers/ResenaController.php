<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarResenaCitaRequest;
use App\Models\AgendaPatsDemo;
use App\Models\PatsAcceso;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ResenaController extends Controller
{
    /**
     * Guarda la reseña (o el rechazo) de una cita completada del usuario autenticado.
     */
    public function guardar(GuardarResenaCitaRequest $request): JsonResponse
    {
        /** @var PatsAcceso $user */
        $user = auth('pasaporte')->user();

        $curp = $this->curpUsuario($user);

        if ($curp === null) {
            return response()->json(['error' => 'No se encontró el pasaporte del usuario.'], 422);
        }

        $cita = AgendaPatsDemo::query()
            ->where('id_agenda', $request->integer('id_agenda'))
            ->where('curp', $curp)
            ->where('estatus', AgendaPatsDemo::ESTATUS_COMPLETADO)
            ->first();

        if ($cita === null) {
            return response()->json(['error' => 'Cita no encontrada.'], 404);
        }

        if ($request->input('accion') === 'rechazar') {
            $cita->reviewed = AgendaPatsDemo::REVIEW_REJECTED;
            $cita->review_value = null;
            $cita->review_comment = null;
        } else {
            $cita->reviewed = AgendaPatsDemo::REVIEW_DONE;
            $cita->review_value = $request->integer('review_value') ?: null;
            $cita->review_comment = $request->input('review_comment') ?: null;
        }

        $cita->save();

        return response()->json(['success' => true]);
    }

    /**
     * CURP del paciente asociado al pasaporte del usuario autenticado.
     */
    private function curpUsuario(PatsAcceso $user): ?string
    {
        if (! $user->id_pasaporte) {
            return null;
        }

        return DB::table('pats_pasaportes')
            ->where('id_pasaporte', $user->id_pasaporte)
            ->value('curp');
    }
}
