<?php
/*
 |──────────────────────────────────────────────────────────────────────────
 |  TINKER — Seeder de citas futuras en agenda_pats_demo
 |──────────────────────────────────────────────────────────────────────────
 |  Uso:
 |    php artisan tinker --execute="require base_path('tinker/seed_citas.php');"
 |  O desde tinker interactivo:
 |    >>> require base_path('tinker/seed_citas.php');
 |
 |  Guarda este archivo en:  /tinker/seed_citas.php
 |──────────────────────────────────────────────────────────────────────────
*/

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// ── Configuración ─────────────────────────────────────────────────────────

$REGION    = 'JAL';
$UNIDAD    = 'ZR';
$HOY       = Carbon::now('America/Mexico_City');
$TOTAL     = 40;        // cuántas citas insertar
$DIAS_MAX  = 60;        // rango futuro en días desde hoy

// ── Catálogos reales de tu BD ─────────────────────────────────────────────

$servicios = [
    ['id' => 1, 'misional' => '1CES', 'nombre' => 'Consulta de especialidad',  'tipo' => 'CONSULTA_ESPECIALIDAD'],
    ['id' => 2, 'misional' => '1QX',  'nombre' => 'Cirugía',                   'tipo' => 'QUIROFANO'],
    ['id' => 3, 'misional' => '1RYX', 'nombre' => 'Citas para estudios de imagen', 'tipo' => 'IMAGENOLOGIA'],
    ['id' => 5, 'misional' => '1LAB', 'nombre' => 'Estudios de laboratorio',   'tipo' => 'LABORATORIO'],
    ['id' => 6, 'misional' => '1FAV', 'nombre' => 'Farmacia',                  'tipo' => 'ENTREGA_MEDICAMENTO'],
];

// Recursos por servicio (id_recurso → id_servicio)
$recursos = [
    1 => [1, 5],   // Consulta especialidad: Fonseca (1), Fuentes (5)
    2 => [2, 3],   // Cirugía: Angelópolis (2), Zona Real (3)
    3 => [6],      // Imagen: Rayos X (6)
    5 => [7],      // Lab: Laboratorio 1 (7)
    6 => [8],      // Farmacia: Farmacia 1 (8)
];

// Pacientes de ejemplo (CURP válidos con formato correcto)
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

// Horarios posibles (hora_inicio → hora_fin)
$horarios = [
    ['inicio' => '07:00', 'fin' => '07:30'],
    ['inicio' => '07:30', 'fin' => '08:00'],
    ['inicio' => '08:00', 'fin' => '08:30'],
    ['inicio' => '08:30', 'fin' => '09:00'],
    ['inicio' => '09:00', 'fin' => '09:30'],
    ['inicio' => '09:30', 'fin' => '10:00'],
    ['inicio' => '10:00', 'fin' => '10:30'],
    ['inicio' => '10:30', 'fin' => '11:00'],
    ['inicio' => '11:00', 'fin' => '11:30'],
    ['inicio' => '11:30', 'fin' => '12:00'],
    ['inicio' => '12:00', 'fin' => '12:30'],
    ['inicio' => '14:00', 'fin' => '14:30'],
    ['inicio' => '15:00', 'fin' => '15:30'],
    ['inicio' => '16:00', 'fin' => '16:45'],
];

$estatuses = [
    'PROGRAMADO'  => 70,  // 70% probabilidad
    'CONFIRMADO'  => 20,  // 20%
    'EN_PROCESO'  => 10,  // 10%
];

// ── Helpers ───────────────────────────────────────────────────────────────

function pick(array $arr): mixed
{
    return $arr[array_rand($arr)];
}

function weightedPick(array $weighted): string
{
    $total = array_sum($weighted);
    $rand  = rand(1, $total);
    $acum  = 0;
    foreach ($weighted as $key => $peso) {
        $acum += $peso;
        if ($rand <= $acum) return $key;
    }
    return array_key_first($weighted);
}

function fechaFutura(Carbon $hoy, int $diasMax): string
{
    // Excluir domingos (dayOfWeek = 0)
    do {
        $d = $hoy->copy()->addDays(rand(1, $diasMax));
    } while ($d->dayOfWeek === 0);

    return $d->toDateString();
}

// ── Inserción ─────────────────────────────────────────────────────────────

$insertados = 0;
$errores    = 0;
$ahora      = $HOY->toDateTimeString();

echo "\n🗓  Insertando {$TOTAL} citas futuras en agenda_pats_demo...\n";
echo str_repeat('─', 58) . "\n";

for ($i = 0; $i < $TOTAL; $i++) {

    $servicio  = pick($servicios);
    $idSrv     = $servicio['id'];
    $idRecurso = pick($recursos[$idSrv]);
    $paciente  = pick($pacientes);
    $horario   = pick($horarios);
    $estatus   = weightedPick($estatuses);
    $fecha     = fechaFutura($HOY, $DIAS_MAX);
    $folio     = 'WEB-' . strtoupper(substr($servicio['tipo'], 0, 3)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

    $confirmado   = $estatus === 'CONFIRMADO' || $estatus === 'EN_PROCESO' ? 1 : 0;
    $confirmadoEn = $confirmado ? $ahora : null;
    $iniciado     = $estatus === 'EN_PROCESO' ? 1 : 0;
    $iniciadoEn   = $iniciado ? $ahora : null;

    try {
        DB::table('agenda_pats_demo')->insert([
            'region'            => $REGION,
            'unidad'            => $UNIDAD,
            'curp'              => $paciente['curp'],
            'nombre_paciente'   => $paciente['nombre'],
            'fecha_nacimiento'  => $paciente['fn'],
            'sexo'              => $paciente['sexo'],
            'id_misional'       => $servicio['misional'],
            'misional'          => $servicio['nombre'],
            'id_servicio'       => $idSrv,
            'id_recurso'        => $idRecurso,
            'resumen_estudios'  => null,
            'fecha_programada'  => $fecha,
            'hora_inicio'       => $horario['inicio'] . ':00',
            'hora_fin'          => $horario['fin'] . ':00',
            'tipo_registro'     => $servicio['tipo'],
            'prioridad'         => 2,
            'folio_externo'     => $folio,
            'origen_sistema'    => 'TINKER_SEED',
            'estatus'           => $estatus,
            'observaciones'     => "Cita de prueba generada por Tinker — {$servicio['nombre']}.",
            'confirmado'        => $confirmado,
            'confirmado_en'     => $confirmadoEn,
            'confirmado_por'    => $confirmado ? 'SEED' : null,
            'cancelado_en'      => null,
            'cancelado_por'     => null,
            'motivo_cancelacion' => null,
            'iniciado_servicio' => $iniciado,
            'iniciado_en'       => $iniciadoEn,
            'iniciado_por'      => $iniciado ? 'SEED' : null,
            'id_expediente'     => null,
            'id_episodio'       => null,
            'id_tarjeta'        => null,
            'activo'            => 1,
            'creado_en'         => $ahora,
            'actualizado_en'    => $ahora,
        ]);

        $insertados++;
        echo "  ✓ #{$insertados}  {$paciente['nombre']} — {$servicio['nombre']} — {$fecha} {$horario['inicio']}  [{$estatus}]\n";
    } catch (\Throwable $e) {
        $errores++;
        echo "  ✗  Error en fila {$i}: " . $e->getMessage() . "\n";
    }
}

echo str_repeat('─', 58) . "\n";
echo "  Insertadas : {$insertados}\n";
echo "  Errores    : {$errores}\n";
echo "  Tabla      : agenda_pats_demo\n";
echo "  Rango      : " . $HOY->addDay()->toDateString() . " → " . $HOY->addDays($DIAS_MAX)->toDateString() . "\n\n";
