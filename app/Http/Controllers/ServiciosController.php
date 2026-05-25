<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

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

    // ── 1. Atención Médica  →  /atencion-medica ─────────────────
    // View defines all its own mock data inline; nothing to pass.
    public function atencionMedica()
    {
        return view('servicios.atencion-medica');
    }

    // ── 2. Estudios Clínicos  →  /estudios-clinicos ─────────────
    public function estudiosСlinicos()
    {
        $fecha = $this->fechaContexto();
        $now   = Carbon::now('America/Mexico_City');
        $idLab = 5; $idImagen = 3;

        $estudiosLab = collect([
            (object)['nombre_estudio' => 'Biometría Hemática Completa',        'preparacion_resumen' => 'Ayuno de 8 horas',              'duracion_min' => 15, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Química Sanguínea 6 elementos',      'preparacion_resumen' => 'Ayuno de 8 horas',              'duracion_min' => 15, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Perfil de Lípidos',                  'preparacion_resumen' => 'Ayuno de 12 horas',             'duracion_min' => 20, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Glucosa en ayunas',                  'preparacion_resumen' => 'Ayuno de 8 horas',              'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Hemoglobina Glicosilada (HbA1c)',    'preparacion_resumen' => null,                            'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Examen General de Orina',            'preparacion_resumen' => 'Primera orina del día',         'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Prueba de Embarazo (hCG)',           'preparacion_resumen' => null,                            'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'TSH (Tiroides)',                     'preparacion_resumen' => 'Sin ayuno necesario',           'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Ácido Úrico',                        'preparacion_resumen' => 'Ayuno de 4 horas',              'duracion_min' => 10, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Cultivo de orina',                   'preparacion_resumen' => 'Muestra de primera orina',      'duracion_min' => 15, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Perfil Hepático',                    'preparacion_resumen' => 'Ayuno de 8 horas',              'duracion_min' => 20, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Antígeno Prostático Específico (PSA)','preparacion_resumen' => 'Sin contacto sexual 48 h antes','duracion_min' => 10, 'requiere_cita' => false],
        ]);

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

        return view('servicios.estudios-clinicos', compact(
            'fecha', 'estudiosLab', 'estudiosImagen',
            'recursosLab', 'recursosImagen',
            'proximoLab', 'proximoImagen',
            'citasHoy', 'idLab', 'idImagen'
        ) + [
            'totalBloqLab'    => $proximoLab->flatten()->count(),
            'totalBloqImagen' => $proximoImagen->flatten()->count(),
            'servicios'       => collect(),
        ]);
    }

    // ── 3. Rayos / Imagenología  →  /rayos ──────────────────────
    public function rayos()
    {
        $fecha = $this->fechaContexto();
        $now   = Carbon::now('America/Mexico_City');

        $servicio = (object)['id_servicio' => 3, 'clave' => 'IMAGEN', 'region' => 'JAL', 'unidad' => 'ZR'];

        $estudios = collect([
            (object)['nombre_estudio' => 'Radiografía de Tórax AP/PA',    'preparacion_resumen' => null,                    'duracion_min' => 15, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Radiografía de Columna Lumbar', 'preparacion_resumen' => null,                    'duracion_min' => 15, 'requiere_cita' => false],
            (object)['nombre_estudio' => 'Ultrasonido Abdominal',         'preparacion_resumen' => 'Ayuno de 4 horas',      'duracion_min' => 30, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Ultrasonido Pélvico',           'preparacion_resumen' => 'Vejiga llena',          'duracion_min' => 30, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'TAC de Cráneo simple',          'preparacion_resumen' => null,                    'duracion_min' => 20, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'TAC de Tórax',                  'preparacion_resumen' => null,                    'duracion_min' => 25, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'TAC de Abdomen contrastada',    'preparacion_resumen' => 'Ayuno de 4 horas',      'duracion_min' => 30, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Resonancia Magnética Cerebral', 'preparacion_resumen' => 'Sin objetos metálicos', 'duracion_min' => 45, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Resonancia Magnética de Rodilla','preparacion_resumen' => null,                   'duracion_min' => 40, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Mamografía',                    'preparacion_resumen' => null,                    'duracion_min' => 20, 'requiere_cita' => true],
            (object)['nombre_estudio' => 'Densitometría Ósea',            'preparacion_resumen' => null,                    'duracion_min' => 20, 'requiere_cita' => true],
        ]);

        $recursos = collect([
            (object)['id_recurso' => 1, 'nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC',          'capacidad' => 1],
            (object)['id_recurso' => 2, 'nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO',  'capacidad' => 1],
            (object)['id_recurso' => 3, 'nombre_recurso' => 'Mamógrafo Digital',   'tipo_recurso' => 'MAMOGRAFIA',   'capacidad' => 1],
            (object)['id_recurso' => 4, 'nombre_recurso' => 'Densitómetro DXA',    'tipo_recurso' => 'DENSITOMETRIA','capacidad' => 1],
            (object)['id_recurso' => 5, 'nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X',      'capacidad' => 1],
            (object)['id_recurso' => 6, 'nombre_recurso' => 'Rayos X Digital 2',   'tipo_recurso' => 'RAYOS_X',      'capacidad' => 1],
        ]);

        $agendaHoy = collect([
            (object)['tipo_bloque' => 'RESERVADO',   'fecha_inicio' => $now->copy()->setTime(8,  0)->toDateTimeString(), 'nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(9,  0)->toDateTimeString(), 'nombre_recurso' => 'TAC 64 cortes',       'tipo_recurso' => 'TAC'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(9, 30)->toDateTimeString(), 'nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO'],
            (object)['tipo_bloque' => 'RESERVADO',   'fecha_inicio' => $now->copy()->setTime(10, 0)->toDateTimeString(), 'nombre_recurso' => 'Ultrasonido General', 'tipo_recurso' => 'ULTRASONIDO'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(10,30)->toDateTimeString(), 'nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X'],
            (object)['tipo_bloque' => 'DISPONIBLE',  'fecha_inicio' => $now->copy()->setTime(11, 0)->toDateTimeString(), 'nombre_recurso' => 'Rayos X Digital 1',   'tipo_recurso' => 'RAYOS_X'],
        ]);

        $citasHoy = collect([
            (object)['hora_inicio' => '08:00:00', 'nombre_paciente' => 'Silvia Moreno Castro'],
            (object)['hora_inicio' => '10:00:00', 'nombre_paciente' => 'Felipe Ríos Blanco'],
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

        $medicamentos = collect([
            (object)['medicamento' => 'Acetaminofén 500mg (Paracetamol)',     'precio' => 45.00],
            (object)['medicamento' => 'Amoxicilina 500mg',                    'precio' => 120.00],
            (object)['medicamento' => 'Atorvastatina 20mg',                   'precio' => 180.00],
            (object)['medicamento' => 'Azitromicina 500mg',                   'precio' => 95.00],
            (object)['medicamento' => 'Captopril 25mg',                       'precio' => 55.00],
            (object)['medicamento' => 'Clonazepam 0.5mg',                     'precio' => 85.00],
            (object)['medicamento' => 'Diclofenaco Sódico 100mg',             'precio' => 60.00],
            (object)['medicamento' => 'Enalapril 10mg',                       'precio' => 75.00],
            (object)['medicamento' => 'Fluconazol 150mg',                     'precio' => 110.00],
            (object)['medicamento' => 'Ibuprofeno 400mg',                     'precio' => 50.00],
            (object)['medicamento' => 'Losartán 50mg',                        'precio' => 140.00],
            (object)['medicamento' => 'Metformina 850mg',                     'precio' => 65.00],
            (object)['medicamento' => 'Metoprolol 50mg',                      'precio' => 90.00],
            (object)['medicamento' => 'Naproxeno Sódico 275mg',               'precio' => 55.00],
            (object)['medicamento' => 'Omeprazol 20mg',                       'precio' => 70.00],
            (object)['medicamento' => 'Pantoprazol 40mg',                     'precio' => 95.00],
            (object)['medicamento' => 'Ranitidina 150mg',                     'precio' => 45.00],
            (object)['medicamento' => 'Sertralina 50mg',                      'precio' => 160.00],
            (object)['medicamento' => 'Sildenafil 50mg',                      'precio' => 200.00],
            (object)['medicamento' => 'Trimetoprim/Sulfametoxazol 800/160mg', 'precio' => 80.00],
        ]);

        $medicamentosInactivos = collect([
            (object)['medicamento' => 'Codeína 30mg (baja temporal)', 'precio' => 0],
            (object)['medicamento' => 'Tramadol 50mg (sin stock)',    'precio' => 0],
        ]);

        $stats = [
            'total_activos'   => $medicamentos->count(),
            'total_inactivos' => $medicamentosInactivos->count(),
            'precio_promedio' => round($medicamentos->avg('precio'), 2),
            'precio_max'      => $medicamentos->max('precio'),
            'precio_min'      => $medicamentos->min('precio'),
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
            'fecha', 'medicamentos', 'medicamentosInactivos',
            'stats', 'entregasHoy', 'unidades'
        ) + [
            'totalUnidades'       => $unidades->count(),
            'preordenesPendientes'=> collect(),
            'ventasHoy'           => collect(),
        ]);
    }
}
