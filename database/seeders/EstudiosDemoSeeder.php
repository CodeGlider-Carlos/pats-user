<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstudiosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('America/Mexico_City');

        $laboratorio = [
            [
                'id_proveedor' => 'HOSP-001',
                'estudio' => 'Biometría Hemática Completa',
                'precio_nopats' => 350.00,
                'descuento' => 20.00,
                'precio_pats' => 280.00,
            ],
            [
                'id_proveedor' => 'HOSP-001',
                'estudio' => 'Química Sanguínea 6 elementos',
                'precio_nopats' => 480.00,
                'descuento' => 20.00,
                'precio_pats' => 384.00,
            ],
            [
                'id_proveedor' => 'HOSP-002',
                'estudio' => 'Perfil de Lípidos',
                'precio_nopats' => 520.00,
                'descuento' => 25.00,
                'precio_pats' => 390.00,
            ],
            [
                'id_proveedor' => 'HOSP-002',
                'estudio' => 'Examen General de Orina',
                'precio_nopats' => 180.00,
                'descuento' => 20.00,
                'precio_pats' => 144.00,
            ],
        ];

        foreach ($laboratorio as $est) {
            $exists = DB::table('pats_cat_estudiosLaboratorio')
                ->where('estudio', $est['estudio'])
                ->where('id_proveedor', $est['id_proveedor'])
                ->exists();

            if ($exists) {
                $this->command->warn("Ya existe (lab): {$est['estudio']}, omitiendo.");

                continue;
            }

            DB::table('pats_cat_estudiosLaboratorio')->insert([
                'id_registro' => Str::uuid()->toString(),
                'id_proveedor' => $est['id_proveedor'],
                'estudio' => $est['estudio'],
                'precio_nopats' => $est['precio_nopats'],
                'descuento' => $est['descuento'],
                'precio_pats' => $est['precio_pats'],
                'activo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'usuario_registro' => 'seeder',
            ]);

            $this->command->info("Creado (lab): {$est['estudio']}");
        }

        $imagen = [
            [
                'id_proveedor' => 'HOSP-001',
                'estudio' => 'Radiografía de Tórax',
                'precio_nopats' => 650.00,
                'descuento' => 25.00,
                'precio_pats' => 487.50,
            ],
            [
                'id_proveedor' => 'HOSP-001',
                'estudio' => 'Ultrasonido Abdominal',
                'precio_nopats' => 1200.00,
                'descuento' => 25.00,
                'precio_pats' => 900.00,
            ],
            [
                'id_proveedor' => 'HOSP-002',
                'estudio' => 'Tomografía Computarizada de Cráneo',
                'precio_nopats' => 4500.00,
                'descuento' => 30.00,
                'precio_pats' => 3150.00,
            ],
            [
                'id_proveedor' => 'HOSP-002',
                'estudio' => 'Resonancia Magnética de Columna Lumbar',
                'precio_nopats' => 7800.00,
                'descuento' => 30.00,
                'precio_pats' => 5460.00,
            ],
        ];

        foreach ($imagen as $est) {
            $exists = DB::table('pats_estudios_imagen')
                ->where('estudio', $est['estudio'])
                ->where('id_proveedor', $est['id_proveedor'])
                ->exists();

            if ($exists) {
                $this->command->warn("Ya existe (imagen): {$est['estudio']}, omitiendo.");

                continue;
            }

            DB::table('pats_estudios_imagen')->insert([
                'id_registro' => Str::uuid()->toString(),
                'id_proveedor' => $est['id_proveedor'],
                'estudio' => $est['estudio'],
                'precio_nopats' => $est['precio_nopats'],
                'descuento' => $est['descuento'],
                'precio_pats' => $est['precio_pats'],
                'activo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'usuario_registro' => 'seeder',
            ]);

            $this->command->info("Creado (imagen): {$est['estudio']}");
        }
    }
}
