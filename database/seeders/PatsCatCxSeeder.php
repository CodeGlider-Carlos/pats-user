<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatsCatCxSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('America/Mexico_City');

        $procedimientos = [
            [
                'especialidad'    => 'Cirugía General',
                'procedimiento'   => 'Colecistectomía laparoscópica',
                'precio_nopats'   => 45000.00,
                'descuento'       => 35.00,
                'precio_pats'     => 29250.00,
            ],
            [
                'especialidad'    => 'Cirugía General',
                'procedimiento'   => 'Apendicectomía laparoscópica',
                'precio_nopats'   => 38000.00,
                'descuento'       => 35.00,
                'precio_pats'     => 24700.00,
            ],
            [
                'especialidad'    => 'Ortopedia',
                'procedimiento'   => 'Artroscopía de rodilla',
                'precio_nopats'   => 52000.00,
                'descuento'       => 30.00,
                'precio_pats'     => 36400.00,
            ],
            [
                'especialidad'    => 'Ortopedia',
                'procedimiento'   => 'Reducción de fractura con fijación interna',
                'precio_nopats'   => 60000.00,
                'descuento'       => 30.00,
                'precio_pats'     => 42000.00,
            ],
            [
                'especialidad'    => 'Ginecología',
                'procedimiento'   => 'Cesárea',
                'precio_nopats'   => 40000.00,
                'descuento'       => 25.00,
                'precio_pats'     => 30000.00,
            ],
            [
                'especialidad'    => 'Ginecología',
                'procedimiento'   => 'Histerectomía laparoscópica',
                'precio_nopats'   => 55000.00,
                'descuento'       => 25.00,
                'precio_pats'     => 41250.00,
            ],
        ];

        foreach ($procedimientos as $proc) {
            $exists = DB::table('pats_cat_cx')
                ->where('procedimiento', $proc['procedimiento'])
                ->exists();

            if ($exists) {
                $this->command->warn("Ya existe: {$proc['procedimiento']}, omitiendo.");
                continue;
            }

            DB::table('pats_cat_cx')->insert([
                'id_registro'      => Str::uuid()->toString(),
                'id_proveedor'     => null,
                'especialidad'     => $proc['especialidad'],
                'procedimiento'    => $proc['procedimiento'],
                'precio_nopats'    => $proc['precio_nopats'],
                'descuento'        => $proc['descuento'],
                'precio_pats'      => $proc['precio_pats'],
                'activo'           => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
                'usuario_registro' => 'seeder',
            ]);

            $this->command->info("Creado: {$proc['procedimiento']} ({$proc['especialidad']})");
        }
    }
}
