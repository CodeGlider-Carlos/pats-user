<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('America/Mexico_City');

        // ── 1. Doctors ────────────────────────────────────────────────────────
        $medicos = [
            [
                'id_registro'        => Str::uuid()->toString(),
                'id_medico_leadplus' => 1001,
                'nombre'             => 'Alejandro',
                'apellido_paterno'   => 'Ramírez',
                'apellido_materno'   => 'Torres',
                'nombre_completo'    => 'Dr. Alejandro Ramírez Torres',
                'especialidad'       => 'Cardiología',
                'cedula_mg'          => '5012301',
                'cedula_esp'         => '8823401',
                'telefono'           => '8111234567',
                'email'              => 'a.ramirez@pats.mx',
                'region'             => 'Monterrey',
                'unidad'             => 'Clínica PATS Norte',
                'direccion'          => 'Av. Insurgentes 340, Col. Del Valle, Monterrey, N.L.',
                'activo'             => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id_registro'        => Str::uuid()->toString(),
                'id_medico_leadplus' => 1002,
                'nombre'             => 'Laura',
                'apellido_paterno'   => 'Vega',
                'apellido_materno'   => 'Mendoza',
                'nombre_completo'    => 'Dra. Laura Vega Mendoza',
                'especialidad'       => 'Pediatría',
                'cedula_mg'          => '6034501',
                'cedula_esp'         => '9145602',
                'telefono'           => '8119876543',
                'email'              => 'l.vega@pats.mx',
                'region'             => 'Monterrey',
                'unidad'             => 'Clínica PATS Sur',
                'direccion'          => 'Blvd. Díaz Ordaz 780, Col. Santa María, Monterrey, N.L.',
                'activo'             => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'id_registro'        => Str::uuid()->toString(),
                'id_medico_leadplus' => 1003,
                'nombre'             => 'Sofía',
                'apellido_paterno'   => 'Morales',
                'apellido_materno'   => 'Ibarra',
                'nombre_completo'    => 'Dra. Sofía Morales Ibarra',
                'especialidad'       => 'Cardiología',
                'cedula_mg'          => '7098412',
                'cedula_esp'         => '9934217',
                'telefono'           => '8113456789',
                'email'              => 's.morales@pats.mx',
                'region'             => 'Monterrey',
                'unidad'             => 'Clínica PATS Centro',
                'direccion'          => 'Calle Hidalgo 120, Col. Centro, Monterrey, N.L.',
                'activo'             => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];

        foreach ($medicos as $medico) {
            $exists = DB::table('pats_cats_medicos')
                ->where('id_medico_leadplus', $medico['id_medico_leadplus'])
                ->exists();

            if (!$exists) {
                DB::table('pats_cats_medicos')->insert($medico);
                $this->command->info("Médico creado: {$medico['nombre_completo']}");
            } else {
                $this->command->warn("Médico ya existe (id_medico_leadplus={$medico['id_medico_leadplus']}), omitiendo.");
            }
        }

        // ── 2. Scheduling slots ───────────────────────────────────────────────
        // Generate slots for the next 30 days — 9:00, 11:00, 16:00 every other day.
        $unidades = [
            1001 => 'Clínica PATS Norte',
            1002 => 'Clínica PATS Sur',
            1003 => 'Clínica PATS Centro',
        ];

        DB::table('dispo_agenda')
            ->whereIn('id_recurso', array_keys($unidades))
            ->delete();

        $slots    = [];
        $horarios = ['09:00', '11:00', '16:00'];

        foreach (array_keys($unidades) as $idRecurso) {
            for ($day = 1; $day <= 28; $day += 2) {
                foreach ($horarios as $hora) {
                    $inicio = $now->copy()->addDays($day)->setTimeFromTimeString($hora);
                    $fin    = $inicio->copy()->addMinutes(30);

                    $slots[] = [
                        'id_servicio'    => 1,
                        'id_recurso'     => $idRecurso,
                        'region'         => 'Monterrey',
                        'unidad'         => $unidades[$idRecurso],
                        'tipo_bloque'    => 'DISPONIBLE',
                        'fecha_inicio'   => $inicio->toDateTimeString(),
                        'fecha_fin'      => $fin->toDateTimeString(),
                        'cupos'          => 1,
                        'ocupado'        => 0,
                        'recurrente'     => 0,
                        'motivo'         => null,
                        'observaciones'  => null,
                        'creado_por'     => null,
                        'usuario'        => 'seeder',
                        'creado_en'      => $now->toDateTimeString(),
                        'actualizado_en' => null,
                        'activo'         => 1,
                    ];
                }
            }
        }

        DB::table('dispo_agenda')->insert($slots);

        $this->command->info(count($slots) . ' bloques de agenda creados para los médicos demo.');
    }
}
