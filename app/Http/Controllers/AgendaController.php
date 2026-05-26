<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgendaController extends Controller
{
    private function now(): Carbon
    {
        return Carbon::now('America/Mexico_City');
    }

    // ─────────────────────────────────────────────────────────────
    //  Vista principal del calendario
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $now = $this->now();

        // Month navigation (default = current month)
        $anio = (int) $request->query('anio', $now->year);
        $mes  = (int) $request->query('mes',  $now->month);

        $mesNav = Carbon::createFromDate($anio, $mes, 1, 'America/Mexico_City');
        $min    = $now->copy()->startOfMonth();
        $max    = $now->copy()->addMonths(12)->startOfMonth();
        if ($mesNav->lt($min)) $mesNav = $min->copy();
        if ($mesNav->gt($max)) $mesNav = $max->copy();

        $inicioMes = $mesNav->copy()->startOfMonth();
        $finMes    = $mesNav->copy()->endOfMonth();

        $idServicio = $request->query('servicio');
        $idRecurso  = $request->query('recurso');

        // ── Citas del mes desde agenda_pats_demo ──────────────────
        $query = DB::table('agenda_pats_demo as a')
            ->leftJoin('pats_cats_medicos as m', 'm.id_medico_leadplus', '=', 'a.id_recurso')
            ->where('a.activo', 1)
            ->whereBetween('a.fecha_programada', [
                $inicioMes->toDateString(),
                $finMes->toDateString(),
            ])
            ->when($idServicio, fn($q) => $q->where('a.id_servicio', $idServicio))
            ->when($idRecurso,  fn($q) => $q->where('a.id_recurso',  $idRecurso))
            ->select([
                'a.id_agenda',
                'a.id_recurso',
                'a.estatus',
                'a.fecha_programada',
                'a.hora_inicio',
                'a.hora_fin',
                'a.nombre_paciente',
                'a.curp',
                'a.confirmado',
                'a.id_servicio',
                'a.misional as servicio',
                'm.nombre_completo as nombre_recurso',
                'm.especialidad',
            ])
            ->orderBy('a.fecha_programada')
            ->orderBy('a.hora_inicio')
            ->get();

        $bloquesPorFecha = $query->groupBy('fecha_programada');

        // Stats by estatus
        $totalDisp   = $query->where('estatus', 'PROGRAMADO')->count();
        $totalReserv = $query->whereIn('estatus', ['CONFIRMADO', 'EN_PROCESO'])->count();
        $totalBloq   = $query->where('estatus', 'CANCELADO')->count();

        $diasMes      = $inicioMes->daysInMonth;
        $offsetInicio = ($inicioMes->dayOfWeek + 6) % 7; // 0=Mon … 6=Sun

        return view('servicios.agenda', [
            'now'             => $now,
            'fecha_display'   => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'hora_actual'     => $now->format('H:i'),
            'fecha_hoy'       => $now->toDateString(),
            'mesNav'          => $mesNav,
            'anio'            => $mesNav->year,
            'mes'             => $mesNav->month,
            'nombreMes'       => $mesNav->isoFormat('MMMM YYYY'),
            'diasMes'         => $diasMes,
            'offsetInicio'    => $offsetInicio,
            'mesPrev'         => $mesNav->copy()->subMonth(),
            'mesSig'          => $mesNav->copy()->addMonth(),
            'puedeRetroceder' => $mesNav->gt($min),
            'puedeAvanzar'    => $mesNav->lt($max),
            'bloquesPorFecha' => $bloquesPorFecha,
            'servicios'       => collect(), // filter form is commented out in the blade
            'recursos'        => collect(),
            'filtroServicio'  => $idServicio,
            'filtroRecurso'   => $idRecurso,
            'soloDisp'        => false,
            'totalDisp'       => $totalDisp,
            'totalReserv'     => $totalReserv,
            'totalBloq'       => $totalBloq,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  JSON: citas de un día específico (para el panel lateral)
    // ─────────────────────────────────────────────────────────────

    public function dia(Request $request, string $fecha)
    {
        $bloques = DB::table('agenda_pats_demo as a')
            ->leftJoin('pats_cats_medicos as m', 'm.id_medico_leadplus', '=', 'a.id_recurso')
            ->where('a.activo', 1)
            ->whereDate('a.fecha_programada', $fecha)
            ->when($request->query('servicio'), fn($q, $v) => $q->where('a.id_servicio', $v))
            ->when($request->query('recurso'),  fn($q, $v) => $q->where('a.id_recurso',  $v))
            ->select([
                'a.id_agenda',
                'a.id_recurso',
                'a.estatus',
                'a.nombre_paciente',
                'a.curp',
                'a.hora_inicio',
                'a.hora_fin',
                'a.confirmado',
                'a.misional as servicio',
                'm.nombre_completo as nombre_recurso',
                'm.especialidad',
            ])
            ->orderBy('a.hora_inicio')
            ->get()
            ->map(fn($b) => [
                ...(array) $b,
                'hora_inicio'  => substr($b->hora_inicio, 0, 5),
                'hora_fin'     => $b->hora_fin ? substr($b->hora_fin, 0, 5) : null,
                'duracion_min' => ($b->hora_inicio && $b->hora_fin)
                    ? Carbon::parse($b->hora_inicio)->diffInMinutes(Carbon::parse($b->hora_fin))
                    : null,
            ]);

        return response()->json([
            'fecha'   => $fecha,
            'bloques' => $bloques,
            'total'   => $bloques->count(),
        ]);
    }
}
