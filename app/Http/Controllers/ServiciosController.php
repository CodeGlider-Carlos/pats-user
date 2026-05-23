<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PatsCatMedicamento;
use App\Models\PatsCatEstudioLaboratorio;
use App\Models\PatsCatEstudioRx;
use App\Models\PatsCatProveedor;
use App\Models\PatsCatCx;



class ServiciosController extends Controller
{
    private function fechaContexto(): array
    {
        $now = Carbon::now('America/Mexico_City');
        return [
            'fecha_hoy'     => $now->toDateString(),
            'fecha_display' => $now->isoFormat('dddd D [de] MMMM [de] YYYY'),
            'hora_actual'   => $now->format('H:i'),
            'dia_semana'    => $now->dayOfWeek,
            'mes_actual'    => $now->month,
            'anio_actual'   => $now->year,
            'timestamp'     => $now->toDateTimeString(),
        ];
    }
    public function hospitales()
    {
        $hospitales = PatsCatProveedor::where('categoria', 'hospital')->where('activo', true)->get();

        return view('servicios.hospitales', compact('hospitales'));
    }
    // ── 1. Atención Médica  →  /atencion-medica ─────────────────
    public function atencionMedica()
    {
        // Proveedores activos (categoría Hospitales) para el banner de urgencias
        $hospitales = PatsCatProveedor::where('categoria', 'Hospitales')
            ->where('activo', true)
            ->get();

        // Procedimientos activos con su proveedor, agrupados por especialidad
        $serviciosMedicos = PatsCatCx::with('proveedor')
            ->where('activo', true)
            ->orderBy('especialidad')
            ->orderBy('procedimiento')
            ->get()
            ->groupBy('especialidad');

        return view('servicios.atencion-medica', compact('hospitales', 'serviciosMedicos'));
    }

    // ── 2. Estudios Clínicos  →  /estudios-clinicos ─────────────
    public function estudiosСlinicos()
    {
        $fecha = $this->fechaContexto();
        $now   = Carbon::now('America/Mexico_City');
        $idLab = 5; $idImagen = 3;

        $estudiosLab = PatsCatEstudioLaboratorio::with('proveedor')->where('activo', true)->get();

        // The following variables are commented out as they are unused in the active parts of the view.
        /*
        $estudiosImagen = collect([
            (object)['nombre_estudio' => 'Ultrasonido Abdominal',  'preparacion_resumen' => 'Ayuno de 4 horas',     'duracion_min' => 30, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'TAC de Cráneo',          'preparacion_resumen' => null,                   'duracion_min' => 20, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Resonancia Magnética',   'preparacion_resumen' => 'Sin objetos metálicos', 'duracion_min' => 45, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Radiografía de Tórax',   'preparacion_resumen' => null,                   'duracion_min' => 15, 'requiere_cita' => false],
        ]);

        $recursosLab = collect([
            (object)['nombre_recurso' => 'Laboratorio Central JAL',  'tipo_recurso' => 'LABORATORIO'],
            (object)['nombre_recurso' => 'Laboratorio Satélite ZR',  'tipo_recurso' => 'LABORATORIO'],
        ]);

        $recursosImagen = collect([
            (object)['nombre_recurso' => 'Equipo TAC 64 cortes',    'tipo_recurso' => 'IMAGEN'],
            (object)['nombre_recurso' => 'Ultrasonido General',      'tipo_recurso' => 'IMAGEN'],
        ]);

        // Próximos horarios disponibles — laboratorio
        $proximoLabRaw = [];
        for ($d = 0; $d <= 4; $d++) {
            $fecha_d = $now->copy()->addDays($d);
            if ($fecha_d->isWeekend()) continue;
            foreach (['07:00', '08:00', '09:00', '10:00'] as $hora) {
                $inicio = $fecha_d->copy()->setTimeFromTimeString($hora);
                $proximoLabRaw[] = (object)[
                    'id_agenda'      => $d * 10 + intval($hora),
                    'id_recurso'     => 1,
                    'fecha_inicio'   => $inicio->toDateTimeString(),
                    'fecha_fin'      => $inicio->copy()->addMinutes(30)->toDateTimeString(),
                    'cupos'          => 4,
                    'ocupado'        => 1,
                    'nombre_recurso' => 'Lab Central JAL',
                ];
            }
        }
        $proximoLab = collect($proximoLabRaw)
            ->groupBy(fn($b) => Carbon::parse($b->fecha_inicio)->toDateString());

        $proximoImagen = collect([]);

        $citasHoy = collect([
            (object)['id_servicio' => $idLab,    'hora_inicio' => '07:30:00', 'nombre_paciente' => 'Ana Torres Ruiz'],
            (object)['id_servicio' => $idLab,    'hora_inicio' => '08:00:00', 'nombre_paciente' => 'Marcos Lima Vega'],
            (object)['id_servicio' => $idImagen, 'hora_inicio' => '09:00:00', 'nombre_paciente' => 'Elena Soto Mora'],
        ]);
        */

        return view('servicios.estudios-clinicos', compact(
            'fecha', 'estudiosLab'
            // 'estudiosImagen',
            // 'recursosLab', 'recursosImagen',
            // 'proximoLab', 'proximoImagen',
            // 'citasHoy', 'idLab', 'idImagen'
        ) /* + [
            'totalBloqLab'    => $proximoLab->flatten()->count(),
            'totalBloqImagen' => $proximoImagen->flatten()->count(),
            'servicios'       => collect(),
        ] */);
    }


    // ── 3. Rayos / Imagenología  →  /rayos ──────────────────────
    public function rayos()
    {
        $fecha = $this->fechaContexto();
        $now   = Carbon::now('America/Mexico_City');

        $servicio = (object)['id_servicio' => 3, 'clave' => 'IMAGEN', 'region' => 'JAL', 'unidad' => 'ZR'];

        $estudios = PatsCatEstudioRx::with('proveedor')->where('activo', true)->get();

        $recursos = collect([
            (object)['id_recurso'=>'001','nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC', 'capacidad' => '1'],
            (object)['id_recurso'=>'002','nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO', 'capacidad' => '1'],
            (object)['id_recurso'=>'003','nombre_recurso' => 'Mamógrafo Digital',   'tipo_recurso' => 'MAMOGRAFIA', 'capacidad' => '1'],
            (object)['id_recurso'=>'004','nombre_recurso' => 'Densitómetro DXA',    'tipo_recurso' => 'DENSITOMETRIA', 'capacidad' => '1'],
            (object)['id_recurso'=>'005','nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X', 'capacidad' => '1'],
            (object)['id_recurso'=>'006','nombre_recurso' => 'Rayos X Digital 2',   'tipo_recurso' => 'RAYOS_X', 'capacidad' => '1'],
        ]);

        $agendaHoy = collect([
            (object)['tipo_bloque' => 'RESERVADO',   'fecha_inicio' => $now->copy()->setTime(8,  0)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(9,  0)->toDateTimeString(), 'id_recurso'=>'001','nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(9,  0)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(9,  30)->toDateTimeString(), 'id_recurso'=>'001','nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(9, 30)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(10,  0)->toDateTimeString(), 'id_recurso'=>'002','nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO'],
            (object)['tipo_bloque' => 'RESERVADO',   'fecha_inicio' => $now->copy()->setTime(10, 0)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(10,  30)->toDateTimeString(), 'id_recurso'=>'002','nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(10,30)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(11,  0)->toDateTimeString(), 'id_recurso'=>'005','nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(11, 0)->toDateTimeString(),'fecha_fin' => $now->copy()->setTime(11,  30)->toDateTimeString(), 'id_recurso'=>'005','nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X'],
        ]);

        $citasHoy = collect([
            (object)['hora_inicio' => '08:00:00', 'nombre_paciente' => 'Silvia Moreno Castro', 'estatus'=>'CONFIRMADO'],
            (object)['hora_inicio' => '10:00:00', 'nombre_paciente' => 'Felipe Ríos Blanco', 'estatus'=>'EN PROCESO'],
        ]);

        $proximaDispRaw = [];
        $id = 1;
        for ($d = 1; $d <= 5; $d++) {
            $fd = $now->copy()->addDays($d);
            if ($fd->isWeekend()) continue;
            foreach (['09:00', '10:00', '11:00', '15:00', '16:00'] as $hora) {
                $inicio = $fd->copy()->setTimeFromTimeString($hora);
                $proximaDispRaw[] = (object)[
                    'id_agenda'      => $id++,
                    'fecha_inicio'   => $inicio->toDateTimeString(),
                    'fecha_fin'      => $inicio->copy()->addMinutes(30)->toDateTimeString(),
                    'cupos'          => 1,
                    'ocupado'        => 0,
                    'nombre_recurso' => ['TAC 64 cortes', 'Ultrasonido General', 'Rayos X Digital 1'][$id % 3],
                ];
            }
        }
        $proximaDisp = collect($proximaDispRaw)
            ->groupBy(fn($b) => Carbon::parse($b->fecha_inicio)->toDateString());

        return view('servicios.rayos', compact(
            'fecha', 'servicio', 'estudios', 'recursos',
            'agendaHoy', 'citasHoy', 'proximaDisp'
        ));
    }

    // ── 4. Farmacia  →  /farmacia ────────────────────────────────
    public function farmacia()
    {
        $fecha = $this->fechaContexto();

        $medicamentos = PatsCatMedicamento::where('activo', true)->get();

        $medicamentosInactivos = PatsCatMedicamento::where('activo', false)->get();

        $stats = [
            'total_activos'   => $medicamentos->count(),
            'total_inactivos' => $medicamentosInactivos->count(),
            'precio_promedio' => round($medicamentos->avg('precio_pats') ?? 0, 2),
            'precio_max'      => $medicamentos->max('precio_pats') ?? 0,
            'precio_min'      => $medicamentos->min('precio_pats') ?? 0,
        ];


        $entregasHoy = collect([
            (object)['hora_inicio' => '09:00', 'nombre_paciente' => 'Carlos Méndez Torres'],
            (object)['hora_inicio' => '11:30', 'nombre_paciente' => 'Rosa Elena Vidal'],
            (object)['hora_inicio' => '13:00', 'nombre_paciente' => 'Miguel Ángel Fuentes'],
        ]);

        $unidades = collect([
            (object)['nombre_unidad' => 'Fifty Doctors Angelópolis', 'direccion' => 'Anillo Perif. Ecológico 3505, San Andrés Cholula, Pue.', 'telefono' => '2226892995', 'correo' => 'angelopolis@fiftydoctors.mx'],
            (object)['nombre_unidad' => 'Fifty Doctors San Manuel',  'direccion' => 'Blvrd 14 Sur 4302, Jardines de San Manuel, Puebla',      'telefono' => '2226895140', 'correo' => 'sanmanuel@fiftydoctors.mx'],
            (object)['nombre_unidad' => 'Fifty Doctors Homi La Paz', 'direccion' => 'Av. Teziutlán Sur 36, Col. La Paz, Puebla',              'telefono' => '2226895141', 'correo' => null],
        ]);

        return view('servicios.farmacia', compact(
            'fecha', 'medicamentos', 'medicamentosInactivos', 'stats','entregasHoy', 'unidades'
        ) + [
            'totalUnidades'       => $unidades->count(),
            'preordenesPendientes'=> collect(),
            'ventasHoy'           => collect(),
        ]);
    }
}
