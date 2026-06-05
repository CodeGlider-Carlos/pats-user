<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FarmaciaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('America/Mexico_City');

        $medicamentos = [
            // Analgésicos / antiinflamatorios
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-001',
                'descripcion'   => 'Paracetamol 500 mg — 20 tabletas',
                'precio_nopats' => 85.00,
                'descuento'     => 20.00,
                'precio_pats'   => 68.00,
            ],
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-002',
                'descripcion'   => 'Ibuprofeno 400 mg — 20 tabletas',
                'precio_nopats' => 95.00,
                'descuento'     => 20.00,
                'precio_pats'   => 76.00,
            ],
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-003',
                'descripcion'   => 'Naproxeno 250 mg — 20 tabletas',
                'precio_nopats' => 110.00,
                'descuento'     => 20.00,
                'precio_pats'   => 88.00,
            ],
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-004',
                'descripcion'   => 'Ketorolaco 10 mg — 10 tabletas',
                'precio_nopats' => 130.00,
                'descuento'     => 25.00,
                'precio_pats'   => 97.50,
            ],
            // Antibióticos
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-005',
                'descripcion'   => 'Amoxicilina 500 mg — 12 cápsulas',
                'precio_nopats' => 180.00,
                'descuento'     => 25.00,
                'precio_pats'   => 135.00,
            ],
            [
                'id_proveedor'  => 'HOSP-001',
                'id_medicamento' => 'MED-006',
                'descripcion'   => 'Azitromicina 500 mg — 3 tabletas',
                'precio_nopats' => 210.00,
                'descuento'     => 25.00,
                'precio_pats'   => 157.50,
            ],
            // Antihipertensivos
            [
                'id_proveedor'  => 'HOSP-002',
                'id_medicamento' => 'MED-007',
                'descripcion'   => 'Losartán 50 mg — 30 tabletas',
                'precio_nopats' => 220.00,
                'descuento'     => 30.00,
                'precio_pats'   => 154.00,
            ],
            [
                'id_proveedor'  => 'HOSP-002',
                'id_medicamento' => 'MED-008',
                'descripcion'   => 'Enalapril 10 mg — 30 tabletas',
                'precio_nopats' => 190.00,
                'descuento'     => 30.00,
                'precio_pats'   => 133.00,
            ],
            [
                'id_proveedor'  => 'HOSP-002',
                'id_medicamento' => 'MED-009',
                'descripcion'   => 'Amlodipino 5 mg — 30 tabletas',
                'precio_nopats' => 240.00,
                'descuento'     => 30.00,
                'precio_pats'   => 168.00,
            ],
            // Antidiabéticos
            [
                'id_proveedor'  => 'HOSP-002',
                'id_medicamento' => 'MED-010',
                'descripcion'   => 'Metformina 850 mg — 30 tabletas',
                'precio_nopats' => 160.00,
                'descuento'     => 30.00,
                'precio_pats'   => 112.00,
            ],
            [
                'id_proveedor'  => 'HOSP-002',
                'id_medicamento' => 'MED-011',
                'descripcion'   => 'Glibenclamida 5 mg — 30 tabletas',
                'precio_nopats' => 145.00,
                'descuento'     => 30.00,
                'precio_pats'   => 101.50,
            ],
            // Gastrointestinales
            [
                'id_proveedor'  => 'HOSP-003',
                'id_medicamento' => 'MED-012',
                'descripcion'   => 'Omeprazol 20 mg — 14 cápsulas',
                'precio_nopats' => 130.00,
                'descuento'     => 20.00,
                'precio_pats'   => 104.00,
            ],
            [
                'id_proveedor'  => 'HOSP-003',
                'id_medicamento' => 'MED-013',
                'descripcion'   => 'Metoclopramida 10 mg — 20 tabletas',
                'precio_nopats' => 90.00,
                'descuento'     => 20.00,
                'precio_pats'   => 72.00,
            ],
            // Antihistamínicos
            [
                'id_proveedor'  => 'HOSP-003',
                'id_medicamento' => 'MED-014',
                'descripcion'   => 'Loratadina 10 mg — 10 tabletas',
                'precio_nopats' => 75.00,
                'descuento'     => 20.00,
                'precio_pats'   => 60.00,
            ],
            [
                'id_proveedor'  => 'HOSP-003',
                'id_medicamento' => 'MED-015',
                'descripcion'   => 'Cetirizina 10 mg — 10 tabletas',
                'precio_nopats' => 80.00,
                'descuento'     => 20.00,
                'precio_pats'   => 64.00,
            ],
        ];

        foreach ($medicamentos as $med) {
            $exists = DB::table('pats_cat_medicamentos')
                ->where('id_medicamento', $med['id_medicamento'])
                ->where('id_proveedor', $med['id_proveedor'])
                ->exists();

            if ($exists) {
                $this->command->warn("Ya existe: {$med['descripcion']}, omitiendo.");
                continue;
            }

            DB::table('pats_cat_medicamentos')->insert([
                'id_registro'      => Str::uuid()->toString(),
                'id_proveedor'     => $med['id_proveedor'],
                'id_medicamento'   => $med['id_medicamento'],
                'descripcion'      => $med['descripcion'],
                'precio_nopats'    => $med['precio_nopats'],
                'descuento'        => $med['descuento'],
                'precio_pats'      => $med['precio_pats'],
                'activo'           => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
                'usuario_registro' => 'seeder',
            ]);

            $this->command->info("Creado: {$med['descripcion']}");
        }
    }
}
