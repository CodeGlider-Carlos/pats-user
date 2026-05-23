<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $disponibilidad = collect([
            1 => 3, 2 => 5, 3 => 2, 4 => 4, 5 => 1,
            6 => 0, 7 => 6, 8 => 2, 9 => 3, 10 => 4,
            11 => 2, 12 => 1,
        ]);

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
        $now    = $this->now();
        $dbMedico = \App\Models\PatsCatMedico::where('id_medico_leadplus', $idRecurso)->first();
        $datos = $dbMedico ? $dbMedico->toArray() : ['nombre_completo' => 'Médico Demo', 'especialidad' => 'General'];
        $medico = (object)array_merge(['id_recurso' => $idRecurso, 'activo' => 1, 'region' => 'JAL', 'unidad' => 'ZR', 'nombre_recurso' => $datos['nombre_completo'] ?? 'Médico Demo'], $datos);

        $bloquesRaw = [];
        $id = 1;
        for ($d = 1; $d <= 8; $d++) {
            $fecha = $now->copy()->addDays($d);
            if ($fecha->isWeekend()) continue;
            foreach (['09:00', '10:00', '11:00', '16:00', '17:00'] as $hora) {
                $inicio = $fecha->copy()->setTimeFromTimeString($hora);
                $fin    = $inicio->copy()->addHour();
                $bloquesRaw[] = (object)[
                    'id_agenda'    => $id++,
                    'id_recurso'   => $idRecurso,
                    'fecha_inicio' => $inicio->toDateTimeString(),
                    'fecha_fin'    => $fin->toDateTimeString(),
                    'cupos'        => 1,
                    'ocupado'      => 0,
                    'tipo_bloque'  => 'DISPONIBLE',
                    'activo'       => 1,
                ];
            }
        }

        $bloques = collect($bloquesRaw)
            ->groupBy(fn($b) => Carbon::parse($b->fecha_inicio)->toDateString());

        $citasAgendadas = collect([
            (object)[
                'id_recurso'       => $idRecurso,
                'nombre_paciente'  => 'Roberto Solís Mora',
                'fecha_programada' => $now->copy()->addDays(2)->toDateString(),
                'hora_inicio'      => '09:00:00',
                'estatus'          => 'PROGRAMADO',
            ],
            (object)[
                'id_recurso'       => $idRecurso,
                'nombre_paciente'  => 'Patricia Lima Vega',
                'fecha_programada' => $now->copy()->addDays(3)->toDateString(),
                'hora_inicio'      => '11:00:00',
                'estatus'          => 'CONFIRMADO',
            ],
        ]);

        return view('servicios.especialidades-agenda', [
            'medico'         => $medico,
            'bloques'        => $bloques,
            'citasAgendadas' => $citasAgendadas,
            'fecha_hoy'      => $now->toDateString(),
            'fecha_display'  => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
        ]);
    }

    public function guardarCita(Request $request)
    {
        $request->validate([
            'id_agenda'     => 'required',
            'curp'          => 'required|string|max:18',
            'nombre'        => 'required|string|max:220',
            'fecha_nac'     => 'required|date',
            'sexo'          => 'required|in:M,F',
            'id_estudio'    => 'nullable|integer',
            'observaciones' => 'nullable|string|max:500',
        ]);

        return redirect()
            ->route('especialidades.index')
            ->with('success', '¡Cita agendada correctamente! Recibirás confirmación en breve.');
    }
}
