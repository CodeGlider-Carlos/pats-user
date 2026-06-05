<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HospitalesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('America/Mexico_City');

        $hospitales = [
            [
                'id_proveedor' => 'HOSP-001',
                'nombre_unidad' => 'Hospital Ángeles Monterrey',
                'telefono'      => '8181441200',
                'direccion'     => 'Av. Jerónimo Siller 300, Valle Oriente, San Pedro Garza García, N.L.',
                'region'        => 'Monterrey',
            ],
            [
                'id_proveedor' => 'HOSP-002',
                'nombre_unidad' => 'Hospital Christus Muguerza Alta Especialidad',
                'telefono'      => '8183992000',
                'direccion'     => 'Av. Hidalgo 2525 Pte., Obispado, Monterrey, N.L.',
                'region'        => 'Monterrey',
            ],
            [
                'id_proveedor' => 'HOSP-003',
                'nombre_unidad' => 'Hospital San José TecSalud',
                'telefono'      => '8183488000',
                'direccion'     => 'Av. Morones Prieto 3000, Los Doctores, Monterrey, N.L.',
                'region'        => 'Monterrey',
            ],
        ];

        foreach ($hospitales as $hospital) {
            $exists = DB::table('pats_cat_proveedores')
                ->where('id_proveedor', $hospital['id_proveedor'])
                ->exists();

            if ($exists) {
                $this->command->warn("Ya existe: {$hospital['nombre_unidad']}, omitiendo.");
                continue;
            }

            DB::table('pats_cat_proveedores')->insert([
                'id_registro'      => Str::uuid()->toString(),
                'id_proveedor'     => $hospital['id_proveedor'],
                'pais'             => 'México',
                'region'           => $hospital['region'],
                'categoria'        => 'hospital',
                'nombre_unidad'    => $hospital['nombre_unidad'],
                'telefono'         => $hospital['telefono'],
                'direccion'        => $hospital['direccion'],
                'activo'           => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
                'usuario_registro' => 'seeder',
            ]);

            $this->command->info("Creado: {$hospital['nombre_unidad']}");
        }
    }
}
