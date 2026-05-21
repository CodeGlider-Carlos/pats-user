<?php
/*
 |──────────────────────────────────────────────────────────────────────────
 |  TINKER — Seeder completo: bloques dispo_agenda + citas agenda_pats_demo
 |──────────────────────────────────────────────────────────────────────────
 |  Guarda en: /tinker/seed_agenda_completo.php
 |
 |  Uso:
 |    php artisan tinker --execute="require base_path('tinker/seed_agenda_completo.php');"
 |
 |  Limpieza rápida (revierte todo lo insertado):
 |    DELETE FROM dispo_agenda      WHERE usuario = 'TINKER_SEED';
 |    DELETE FROM agenda_pats_demo  WHERE origen_sistema = 'TINKER_SEED';
 |──────────────────────────────────────────────────────────────────────────
*/

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// ── Configuración ─────────────────────────────────────────────────────────

$REGION   = 'JAL';
$UNIDAD   = 'ZR';
$HOY      = Carbon::now('America/Mexico_City');
$DIAS     = 45;      // días hacia adelante que cubrirá la agenda
$AHORA    = $HOY->toDateTimeString();

// ── Catálogos reales ──────────────────────────────────────────────────────

// [id_servicio => [id_misional, nombre_servicio, tipo_registro]]
$servicios = [
    1 => ['1CES', 'Consulta de especialidad',      'CONSULTA_ESPECIALIDAD'],
    2 => ['1QX',  'Cirugía',                        'QUIROFANO'],
    3 => ['1RYX', 'Citas para estudios de imagen',  'IMAGENOLOGIA'],
    5 => ['1LAB', 'Estudios de laboratorio',         'LABORATORIO'],
    6 => ['1FAV', 'Farmacia',                        'ENTREGA_MEDICAMENTO'],
];

// [id_servicio => [[id_recurso, nombre_recurso], ...]]
$recursos = [
    1 => [[1, 'ALEJANDRO FONSECA RODRIGUEZ'], [5, 'JUAN CARLOS FUENTES MOYA']],
    2 => [[2, 'Fifty Doctors Angelópolis'],   [3, 'Fifty Doctors Zona Real']],
    3 => [[6, 'RAYOS X PRUEBA 1']],
    5 => [[7, 'LABORATORIO 1']],
    6 => [[8, 'FARMACIA 1']],
];

// Horarios por servicio [inicio, fin, duracion_min]
$horariosPorServicio = [
    1 => [ // Especialidad: consultas de 20 min, 07:00–14:00
        ['07:00', '07:20'],
        ['07:20', '07:40'],
        ['07:40', '08:00'],
        ['08:00', '08:20'],
        ['08:20', '08:40'],
        ['08:40', '09:00'],
        ['09:00', '09:20'],
        ['09:20', '09:40'],
        ['09:40', '10:00'],
        ['10:00', '10:20'],
        ['10:20', '10:40'],
        ['10:40', '11:00'],
        ['11:00', '11:20'],
        ['11:20', '11:40'],
        ['11:40', '12:00'],
        ['12:00', '12:20'],
        ['13:00', '13:20'],
        ['13:20', '13:40'],
    ],
    2 => [ // Cirugía: bloques largos
        ['06:00', '09:00'],
        ['09:30', '12:30'],
        ['13:00', '16:00'],
    ],
    3 => [ // Imagen: 30 min
        ['07:00', '07:30'],
        ['07:30', '08:00'],
        ['08:00', '08:30'],
        ['08:30', '09:00'],
        ['09:00', '09:30'],
        ['09:30', '10:00'],
        ['10:00', '10:30'],
        ['10:30', '11:00'],
        ['11:00', '11:30'],
    ],
    5 => [ // Lab: 15 min (generalmente sin cita, pero registramos)
        ['07:00', '07:15'],
        ['07:15', '07:30'],
        ['07:30', '07:45'],
        ['07:45', '08:00'],
        ['08:00', '08:15'],
        ['08:15', '08:30'],
        ['08:30', '08:45'],
        ['08:45', '09:00'],
    ],
    6 => [ // Farmacia: 20 min
        ['09:00', '09:20'],
        ['09:20', '09:40'],
        ['09:40', '10:00'],
        ['10:00', '10:20'],
        ['10:20', '10:40'],
        ['11:00', '11:20'],
        ['11:20', '11:40'],
        ['12:00', '12:20'],
    ],
];

// Pacientes de ejemplo
$pacientes = [
    ['curp' => 'GARM850312HJCRZR01', 'nombre' => 'Gabriel Ramos Mendoza',     'sexo' => 'M', 'fn' => '1985-03-12'],
    ['curp' => 'LOPE920617MJCRRR02', 'nombre' => 'Lorena Pérez Estrada',      'sexo' => 'F', 'fn' => '1992-06-17'],
    ['curp' => 'HEVR780904HJCRVL03', 'nombre' => 'Héctor Vera Villanueva',    'sexo' => 'M', 'fn' => '1978-09-04'],
    ['curp' => 'MARM010220MJCRNS04', 'nombre' => 'María Martínez Solano',     'sexo' => 'F', 'fn' => '2001-02-20'],
    ['curp' => 'GUJA970815HJCRLR05', 'nombre' => 'Gustavo Juárez Alvarado',   'sexo' => 'M', 'fn' => '1997-08-15'],
    ['curp' => 'ROSP880523MJCRLN06', 'nombre' => 'Rosa Sánchez Palomino',     'sexo' => 'F', 'fn' => '1988-05-23'],
    ['curp' => 'FEGM991130HJCRRN07', 'nombre' => 'Felipe González Moreno',    'sexo' => 'M', 'fn' => '1999-11-30'],
    ['curp' => 'VAGC030408MJCRRN08', 'nombre' => 'Valentina García Castillo', 'sexo' => 'F', 'fn' => '2003-04-08'],
    ['curp' => 'AQER650719HJCRRQ09', 'nombre' => 'Aquiles Reyes Quiróz',      'sexo' => 'M', 'fn' => '1965-07-19'],
    ['curp' => 'BEON830211MJCRLS10', 'nombre' => 'Beatriz Ortega Leal',       'sexo' => 'F', 'fn' => '1983-02-11'],
    ['curp' => 'CARM700906HJCRRD11', 'nombre' => 'Carlos Ramírez Delgado',    'sexo' => 'M', 'fn' => '1970-09-06'],
    ['curp' => 'DIAF941025MJCRRL12', 'nombre' => 'Diana Fuentes Roldán',      'sexo' => 'F', 'fn' => '1994-10-25'],
    ['curp' => 'ESGM590314HJCRRC13', 'nombre' => 'Ernesto Guzmán Cruz',       'sexo' => 'M', 'fn' => '1959-03-14'],
    ['curp' => 'FLAL001205MJCRRP14', 'nombre' => 'Flor Álvarez López',        'sexo' => 'F', 'fn' => '2000-12-05'],
    ['curp' => 'GORB861127HJCRRN15', 'nombre' => 'Gonzalo Ruiz Bravo',        'sexo' => 'M', 'fn' => '1986-11-27'],
];

// ── Helpers ───────────────────────────────────────────────────────────────

function pick(array $arr): mixed
{
    return $arr[array_rand($arr)];
}

// ── PASO 1: Insertar bloques en dispo_agenda ──────────────────────────────
// Genera bloques DISPONIBLES para los próximos $DIAS días (excepto domingos)
// Por cada servicio+recurso inserta todos sus horarios del día

echo "\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
echo "│  PASO 1 — Bloques de disponibilidad (dispo_agenda)      │\n";
echo "└─────────────────────────────────────────────────────────┘\n";

$bloquesInsertados = 0;
$bloquesError      = 0;

for ($offsetDia = 1; $offsetDia <= $DIAS; $offsetDia++) {

    $fecha = $HOY->copy()->addDays($offsetDia);

    // Saltar domingos
    if ($fecha->dayOfWeek === Carbon::SUNDAY) continue;

    $esSabado = $fecha->dayOfWeek === Carbon::SATURDAY;

    foreach ($servicios as $idServicio => $infoSrv) {

        $horariosDelSrv = $horariosPorServicio[$idServicio];
        $recursosDelSrv = $recursos[$idServicio];

        // Sábados: solo especialidad y farmacia (mitad de horarios)
        if ($esSabado && !in_array($idServicio, [1, 6])) continue;

        foreach ($recursosDelSrv as [$idRecurso, $nombreRecurso]) {

            // Sábados: solo el primer recurso de cada servicio
            if ($esSabado && $idRecurso !== $recursosDelSrv[0][0]) continue;

            foreach ($horariosDelSrv as $horario) {

                // Sábados: solo primeros 6 horarios
                if ($esSabado && $bloquesInsertados % 100 > 6) {
                }

                $fechaInicio = $fecha->toDateString() . ' ' . $horario[0] . ':00';
                $fechaFin    = $fecha->toDateString() . ' ' . $horario[1] . ':00';

                try {
                    DB::table('dispo_agenda')->insert([
                        'id_servicio'  => $idServicio,
                        'id_recurso'   => $idRecurso,
                        'region'       => $REGION,
                        'unidad'       => $UNIDAD,
                        'tipo_bloque'  => 'DISPONIBLE',
                        'fecha_inicio' => $fechaInicio,
                        'fecha_fin'    => $fechaFin,
                        'cupos'        => $idServicio === 1 ? 1 : 1,
                        'ocupado'      => 0,
                        'recurrente'   => 0,
                        'motivo'       => '',
                        'observaciones' => '',
                        'creado_por'   => 1,
                        'usuario'      => 'TINKER_SEED',
                        'creado_en'    => $AHORA,
                        'actualizado_en' => null,
                        'activo'       => 1,
                    ]);
                    $bloquesInsertados++;
                } catch (\Throwable $e) {
                    $bloquesError++;
                    echo "  ✗ Bloque {$fechaInicio}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}

echo "  ✓ Bloques insertados : {$bloquesInsertados}\n";
echo "  ✗ Errores            : {$bloquesError}\n\n";

// ── PASO 2: Insertar citas en agenda_pats_demo ────────────────────────────
// Toma bloques recién insertados, marca algunos como RESERVADO
// y crea la cita correspondiente en agenda_pats_demo

echo "┌─────────────────────────────────────────────────────────┐\n";
echo "│  PASO 2 — Citas programadas (agenda_pats_demo)          │\n";
echo "└─────────────────────────────────────────────────────────┘\n";

// Traer todos los bloques recién insertados (solo DISPONIBLES activos)
$bloques = DB::table('dispo_agenda')
    ->where('usuario', 'TINKER_SEED')
    ->where('tipo_bloque', 'DISPONIBLE')
    ->where('activo', 1)
    ->orderBy('fecha_inicio')
    ->get();

// Reservar aprox. 35% de los bloques como citas
$totalBloques = $bloques->count();
$numCitas     = (int) round($totalBloques * 0.35);

// Tomar una muestra aleatoria de bloques
$muestra = $bloques->shuffle()->take($numCitas);

$citasInsertadas = 0;
$citasError      = 0;

foreach ($muestra as $bloque) {

    $paciente  = pick($pacientes);
    $infoSrv   = $servicios[$bloque->id_servicio];
    $fechaIni  = Carbon::parse($bloque->fecha_inicio);
    $fechaFin  = Carbon::parse($bloque->fecha_fin);

    // Estatus variado: más citas futuras = PROGRAMADO
    $diasRestantes = $HOY->diffInDays($fechaIni);
    if ($diasRestantes <= 3) {
        $estatus = pick(['CONFIRMADO', 'EN_PROCESO', 'PROGRAMADO']);
    } elseif ($diasRestantes <= 10) {
        $estatus = pick(['CONFIRMADO', 'PROGRAMADO', 'PROGRAMADO']);
    } else {
        $estatus = 'PROGRAMADO';
    }

    $confirmado = in_array($estatus, ['CONFIRMADO', 'EN_PROCESO']) ? 1 : 0;
    $iniciado   = $estatus === 'EN_PROCESO' ? 1 : 0;

    DB::transaction(function () use (
        $bloque,
        $paciente,
        $infoSrv,
        $fechaIni,
        $fechaFin,
        $estatus,
        $confirmado,
        $iniciado,
        $REGION,
        $UNIDAD,
        $AHORA,
        &$citasInsertadas,
        &$citasError
    ) {
        try {
            // Insertar cita
            DB::table('agenda_pats_demo')->insert([
                'region'             => $REGION,
                'unidad'             => $UNIDAD,
                'curp'               => $paciente['curp'],
                'nombre_paciente'    => $paciente['nombre'],
                'fecha_nacimiento'   => $paciente['fn'],
                'sexo'               => $paciente['sexo'],
                'id_misional'        => $infoSrv[0],
                'misional'           => $infoSrv[1],
                'id_servicio'        => $bloque->id_servicio,
                'id_recurso'         => $bloque->id_recurso,
                'resumen_estudios'   => null,
                'fecha_programada'   => $fechaIni->toDateString(),
                'hora_inicio'        => $fechaIni->format('H:i:s'),
                'hora_fin'           => $fechaFin->format('H:i:s'),
                'tipo_registro'      => $infoSrv[2],
                'prioridad'          => 2,
                'folio_externo'      => 'SEED-' . strtoupper(uniqid()),
                'origen_sistema'     => 'TINKER_SEED',
                'estatus'            => $estatus,
                'observaciones'      => "Cita de prueba. Servicio: {$infoSrv[1]}.",
                'confirmado'         => $confirmado,
                'confirmado_en'      => $confirmado ? $AHORA : null,
                'confirmado_por'     => $confirmado ? 'SEED' : null,
                'cancelado_en'       => null,
                'cancelado_por'      => null,
                'motivo_cancelacion' => null,
                'iniciado_servicio'  => $iniciado,
                'iniciado_en'        => $iniciado ? $AHORA : null,
                'iniciado_por'       => $iniciado ? 'SEED' : null,
                'id_expediente'      => null,
                'id_episodio'        => null,
                'id_tarjeta'         => null,
                'activo'             => 1,
                'creado_en'          => $AHORA,
                'actualizado_en'     => $AHORA,
            ]);

            // Marcar bloque como RESERVADO y sumar ocupado
            DB::table('dispo_agenda')
                ->where('id_agenda', $bloque->id_agenda)
                ->update([
                    'tipo_bloque'    => 'RESERVADO',
                    'ocupado'        => 1,
                    'observaciones'  => "Reservado SEED | Paciente: {$paciente['nombre']} | CURP: {$paciente['curp']}",
                    'actualizado_en' => $AHORA,
                ]);

            $citasInsertadas++;
            echo "  ✓ {$paciente['nombre']} — {$infoSrv[1]} — {$fechaIni->toDateString()} {$fechaIni->format('H:i')}  [{$estatus}]\n";
        } catch (\Throwable $e) {
            $citasError++;
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    });
}

// ── Resumen final ─────────────────────────────────────────────────────────

$bloqDisp = DB::table('dispo_agenda')
    ->where('usuario', 'TINKER_SEED')
    ->where('tipo_bloque', 'DISPONIBLE')
    ->count();

echo "\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
echo "│  RESUMEN                                                │\n";
echo "├─────────────────────────────────────────────────────────┤\n";
echo "│  dispo_agenda    → {$bloquesInsertados} bloques totales insertados          \n";
echo "│  dispo_agenda    → {$bloqDisp} bloques siguen DISPONIBLES          \n";
echo "│  agenda_pats_demo→ {$citasInsertadas} citas insertadas                  \n";
echo "│  Rango           → " . $HOY->addDay()->toDateString() . " al " . $HOY->addDays($DIAS)->toDateString() . "\n";
echo "├─────────────────────────────────────────────────────────┤\n";
echo "│  Para revertir:                                         │\n";
echo "│  DELETE FROM dispo_agenda WHERE usuario='TINKER_SEED';  │\n";
echo "│  DELETE FROM agenda_pats_demo WHERE                     │\n";
echo "│    origen_sistema='TINKER_SEED';                        │\n";
echo "└─────────────────────────────────────────────────────────┘\n\n";
