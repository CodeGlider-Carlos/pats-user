<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PatsCatMedico;
use App\Models\DispoAgenda;

class EspecialidadesController extends Controller
{
    private function now(): Carbon
    {
        return Carbon::now('America/Mexico_City');
    }


    public function index()
    {
        $now = $this->now();

        $medicos = \App\Models\PatsCatMedico::where('activo', true)->get();

        $porEspecialidad = $medicos->groupBy('especialidad');

        $disponibilidad = DB::table('dispo_agenda')
            ->where('tipo_bloque', 'DISPONIBLE')
            ->where('activo', 1)
            ->whereColumn('ocupado', '<', 'cupos')
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<=', $now->copy()->addDays(30))
            ->groupBy('id_recurso')
            ->selectRaw('id_recurso, COUNT(*) as total')
            ->pluck('total', 'id_recurso');
        $estudios = collect([
            (object)['id_estudio' => 1, 'nombre_estudio' => 'Primera vez',     'requiere_cita' => 1],
            (object)['id_estudio' => 2, 'nombre_estudio' => 'Seguimiento',     'requiere_cita' => 1],
            (object)['id_estudio' => 3, 'nombre_estudio' => 'Segunda opinión', 'requiere_cita' => 1],
        ]);

        $citasHoy = collect([
            (object)['hora_inicio' => '09:00', 'nombre_paciente' => 'María García López',  'estatus' => 'CONFIRMADO'],
            (object)['hora_inicio' => '10:30', 'nombre_paciente' => 'Juan Pérez Morales',  'estatus' => 'PROGRAMADO'],
            (object)['hora_inicio' => '12:00', 'nombre_paciente' => 'Laura Sánchez Vega',  'estatus' => 'EN_PROCESO'],
        ]);

        return view('servicios.especialidades', [
            'fecha_display'   => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'fecha_hoy'       => $now->toDateString(),
            'hora_actual'     => $now->format('H:i'),
            'servicio'        => (object)['id_servicio' => 1, 'servicio' => 'Consulta de Especialidad'],
            'porEspecialidad' => $porEspecialidad,
            'disponibilidad'  => $disponibilidad,
            'estudios'        => $estudios,
            'citasHoy'        => $citasHoy,
            'totalMedicos'    => $medicos->count(),
        ]);
    }

    public function bloquesMedico(Request $request, $idRecurso)
    {
        $now = $this->now();

        $medico = PatsCatMedico::where('id_medico_leadplus', $idRecurso)
            ->where('activo', true)
            ->firstOrFail();

        $bloques = DispoAgenda::where('id_recurso', $idRecurso)
            ->where('tipo_bloque', 'DISPONIBLE')
            ->where('activo', true)
            ->whereColumn('ocupado', '<', 'cupos')
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<=', $now->copy()->addDays(30))
            ->orderBy('fecha_inicio')
            ->get()
            ->groupBy(fn($b) => Carbon::parse($b->fecha_inicio)->toDateString());

        $acceso    = auth('pasaporte')->user();
        $pasaporte = null;

        if ($acceso?->id_pasaporte) {
            $pasaporte = DB::table('pats_pasaportes')
                ->where('id_pasaporte', $acceso->id_pasaporte)
                ->where('activo', 1)
                ->first();
        }

        return view('servicios.especialidades-agenda', [
            'medico'        => $medico,
            'bloques'       => $bloques,
            'fecha_hoy'     => $now->toDateString(),
            'fecha_display' => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'acceso'        => $acceso,
            'pasaporte'     => $pasaporte,
        ]);
    }

    public function guardarCita(Request $request)
    {
        $validated = $request->validate([
            'id_agenda'     => 'required|integer|exists:dispo_agenda,id_agenda',
            'curp'          => 'required|string|size:18',
            'nombre'        => 'required|string|max:220',
            'fecha_nac'     => 'required|date',
            'sexo'          => 'required|in:M,F',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $slot   = DispoAgenda::findOrFail($validated['id_agenda']);
        $medico = PatsCatMedico::where('id_medico_leadplus', $slot->id_recurso)->first();
        $acceso = auth('pasaporte')->user();

        DB::table('agenda_pats_demo')->insert([
            'estatus'          => 'PROGRAMADO',
            'region'           => $medico?->region  ?? $slot->region,
            'unidad'           => $medico?->unidad  ?? $slot->unidad,
            'curp'             => strtoupper($validated['curp']),
            'nombre_paciente'  => $validated['nombre'],
            'fecha_nacimiento' => $validated['fecha_nac'],
            'sexo'             => $validated['sexo'],
            'id_misional'      => '1CES',
            'misional'         => 'Consulta de especialidad',
            'id_servicio'      => 1,
            'id_recurso'       => $slot->id_recurso,
            'fecha_programada' => Carbon::parse($slot->fecha_inicio)->toDateString(),
            'hora_inicio'      => Carbon::parse($slot->fecha_inicio)->format('H:i:s'),
            'hora_fin'         => Carbon::parse($slot->fecha_fin)->format('H:i:s'),
            'tipo_registro'    => 'CONSULTA_ESPECIALIDAD',
            'prioridad'        => 2,
            'folio_externo'    => $acceso?->id_pasaporte,
            'origen_sistema'   => 'pats_usuario',
            'observaciones'    => $validated['observaciones'] ?? null,
            'confirmado'       => 0,
            'iniciado_servicio'=> 0,
            'activo'           => 1,
        ]);

        // Mark slot as reserved and consume the cupo
        $slot->update(['tipo_bloque' => 'RESERVADO']);
        $slot->increment('ocupado');

        return redirect()
            ->route('especialidades.agenda', $slot->id_recurso)
            ->with('success', '¡Cita agendada correctamente! Recibirás confirmación en breve.');
    }
}
