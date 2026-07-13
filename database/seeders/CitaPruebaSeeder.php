<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaPruebaSeeder extends Seeder
{
    /**
     * Cita de prueba para mañana, ligada al CURP del usuario de prueba
     * (Carlos González Prueba — ver UsuarioPruebaSeeder).
     *
     * Sirve para verificar que /agenda solo muestra las citas cuyo CURP
     * coincide con el del usuario autenticado.
     */
    public function run(): void
    {
        $curp = 'GOCA900101HDFNRR09';
        $manana = Carbon::now('America/Mexico_City')->addDay()->toDateString();

        DB::table('agenda_pats_demo')->updateOrInsert(
            [
                'curp' => $curp,
                'fecha_programada' => $manana,
                'hora_inicio' => '09:00:00',
            ],
            [
                'region' => 'JAL',
                'unidad' => 'ZR',
                'nombre_paciente' => 'Carlos González Prueba',
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'M',
                'id_misional' => '1CES',
                'misional' => 'Consulta de especialidad',
                'id_servicio' => 1,
                'id_recurso' => 1001, // Dr. Alejandro Ramírez Torres (Cardiología)
                'hora_fin' => '09:30:00',
                'tipo_registro' => 'CONSULTA_ESPECIALIDAD',
                'prioridad' => 2,
                'folio_externo' => 'SEED-CITA-PRUEBA',
                'origen_sistema' => 'CITA_PRUEBA_SEED',
                'estatus' => 'PROGRAMADO',
                'observaciones' => 'Cita de prueba para validar el filtrado por CURP en /agenda.',
                'confirmado' => 0,
                'iniciado_servicio' => 0,
                'activo' => 1,
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]
        );

        $this->command->info("Cita de prueba creada para {$manana} 09:00 — CURP {$curp} (Carlos González Prueba).");
    }
}
