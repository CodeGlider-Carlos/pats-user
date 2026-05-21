<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AgendaController extends Controller
{
    private function now(): Carbon
    {
        return Carbon::now('America/Mexico_City');
    }

    private function ubicacion(): array
    {
        return [
            'region' => config('pats.region', 'JAL'),
            'unidad' => config('pats.unidad', 'ZR'),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  Vista principal del calendario
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $now       = $this->now();
        $ubicacion = $this->ubicacion();

        // Mes navegado (default = mes actual)
        $anio = (int) $request->query('anio', $now->year);
        $mes  = (int) $request->query('mes',  $now->month);

        // Clamp: no permitir navegar más de 6 meses atrás ni más de 12 adelante
        $mesNav = Carbon::createFromDate($anio, $mes, 1, 'America/Mexico_City');
        $min    = $now->copy()->startOfMonth();
        $max    = $now->copy()->addMonths(12)->startOfMonth();
        if ($mesNav->lt($min)) $mesNav = $min->copy();
        if ($mesNav->gt($max)) $mesNav = $max->copy();

        $inicioMes = $mesNav->copy()->startOfMonth();
        $finMes    = $mesNav->copy()->endOfMonth();

        // ── Filtros opcionales ────────────────────────────────────
        $idServicio = $request->query('servicio');
        $idRecurso  = $request->query('recurso');

        // ── Catálogos para filtros ─────────────────────────────────
        $servicios = DB::table('cat_dispo_servicios')
            ->where('region', $ubicacion['region'])
            ->where('unidad', $ubicacion['unidad'])
            ->where('activo', 1)
            ->orderBy('servicio')
            ->get();

        $recursos = DB::table('cat_dispo_recursos')
            ->where('region', $ubicacion['region'])
            ->where('unidad', $ubicacion['unidad'])
            ->where('activo', 1)
            ->when($idServicio, fn($q) => $q->where('id_servicio', $idServicio))
            ->orderBy('nombre_recurso')
            ->get();

        // ── Citas reales del mes desde agenda_pats_demo ────────────
        // Solo se muestran citas ya agendadas (no bloques vacíos)
        $query = DB::table('agenda_pats_demo as a')
            ->join('cat_dispo_servicios as s', 's.id_servicio', '=', 'a.id_servicio')
            ->leftJoin('cat_dispo_recursos as r', 'r.id_recurso', '=', 'a.id_recurso')
            ->where('a.region', $ubicacion['region'])
            ->where('a.unidad', $ubicacion['unidad'])
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
                'a.estatus as tipo_bloque',  // alias para compatibilidad con el Blade
                'a.fecha_programada',
                'a.hora_inicio',
                'a.hora_fin',
                'a.nombre_paciente',
                'a.curp',
                'a.estatus',
                'a.confirmado',
                's.id_servicio',
                's.servicio',
                's.color as servicio_color',
                's.icono as servicio_icono',
                'r.nombre_recurso',
                'r.especialidad',
                'r.tipo_recurso',
            ])
            ->orderBy('a.fecha_programada')
            ->orderBy('a.hora_inicio')
            ->get();

        // ── Indexar citas por fecha para el render del calendario ──
        $bloquesPorFecha = $query->groupBy('fecha_programada');

        // ── Stats por estatus ──────────────────────────────────────
        $totalDisp   = $query->whereIn('estatus', ['PROGRAMADO'])->count();
        $totalReserv = $query->whereIn('estatus', ['CONFIRMADO', 'EN_PROCESO'])->count();
        $totalBloq   = $query->whereIn('estatus', ['CANCELADO'])->count();

        // ── Días del mes con layout de semanas ─────────────────────
        // Necesitamos saber en qué columna empieza el mes (lun=0…dom=6)
        $diasMes       = $inicioMes->daysInMonth;
        $offsetInicio  = ($inicioMes->dayOfWeek + 6) % 7; // 0=lun … 6=dom

        return view('servicios.agenda', [
            // Fecha
            'now'           => $now,
            'fecha_display' => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'hora_actual'   => $now->format('H:i'),
            'fecha_hoy'     => $now->toDateString(),
            // Navegación
            'mesNav'        => $mesNav,
            'anio'          => $mesNav->year,
            'mes'           => $mesNav->month,
            'nombreMes'     => $mesNav->isoFormat('MMMM YYYY'),
            'diasMes'       => $diasMes,
            'offsetInicio'  => $offsetInicio,
            'mesPrev'       => $mesNav->copy()->subMonth(),
            'mesSig'        => $mesNav->copy()->addMonth(),
            'puedeRetroceder' => $mesNav->gt($min),
            'puedeAvanzar'    => $mesNav->lt($max),
            // Datos
            'bloquesPorFecha' => $bloquesPorFecha,
            'servicios'       => $servicios,
            'recursos'        => $recursos,
            // Filtros activos
            'filtroServicio'  => $idServicio,
            'filtroRecurso'   => $idRecurso,
            'soloDisp'        => false,
            // Stats
            'totalDisp'       => $totalDisp,
            'totalReserv'     => $totalReserv,
            'totalBloq'       => $totalBloq,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  JSON: bloques de un día específico (para el panel lateral)
    // ─────────────────────────────────────────────────────────────

    public function dia(Request $request, string $fecha)
    {
        $ubicacion = $this->ubicacion();

        $bloques = DB::table('agenda_pats_demo as a')
            ->join('cat_dispo_servicios as s', 's.id_servicio', '=', 'a.id_servicio')
            ->leftJoin('cat_dispo_recursos as r', 'r.id_recurso', '=', 'a.id_recurso')
            ->where('a.region', $ubicacion['region'])
            ->where('a.unidad', $ubicacion['unidad'])
            ->where('a.activo', 1)
            ->whereDate('a.fecha_programada', $fecha)
            ->when($request->query('servicio'), fn($q, $v) => $q->where('a.id_servicio', $v))
            ->when($request->query('recurso'),  fn($q, $v) => $q->where('a.id_recurso', $v))
            ->select([
                'a.id_agenda',
                'a.id_recurso',
                'a.estatus as tipo_bloque',
                'a.estatus',
                'a.nombre_paciente',
                'a.curp',
                'a.hora_inicio',
                'a.hora_fin',
                'a.confirmado',
                's.servicio',
                's.color as servicio_color',
                's.icono as servicio_icono',
                'r.nombre_recurso',
                'r.especialidad',
                'r.tipo_recurso',
            ])
            ->orderBy('a.hora_inicio')
            ->get()
            ->map(fn($b) => [
                ...(array)$b,
                'hora_inicio'  => \Illuminate\Support\Str::limit($b->hora_inicio, 5, ''),
                'hora_fin'     => $b->hora_fin ? \Illuminate\Support\Str::limit($b->hora_fin, 5, '') : null,
                'duracion_min' => ($b->hora_inicio && $b->hora_fin)
                    ? Carbon::parse($b->hora_inicio)->diffInMinutes(Carbon::parse($b->hora_fin))
                    : null,
                'cupos_libres' => 1,
            ]);

        return response()->json([
            'fecha'   => $fecha,
            'bloques' => $bloques,
            'total'   => $bloques->count(),
        ]);
    }
}
