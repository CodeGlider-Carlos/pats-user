<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pasaporte
        $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
            'id_franquicia' => 1,
            'id_distribuidor' => 1,
            'id_tipo_precio' => 1,
            'curp' => 'GOCA900101HDFNRR09',
            'nombres' => 'Carlos',
            'apellido_pa' => 'González',
            'apellido_ma' => 'Prueba',
            'fecha_nacimiento' => '1990-01-01',
            'telefono' => '5512345678',
            'correo' => 'prueba@pats.mx',
            'fecha_alta' => now(),
            'vigencia' => now()->addYear()->toDateString(),
            'frecuencia_pago' => 'ANUAL',
            'estatus' => 'ACTIVO',
            'valor_pasaporte' => 1200.00,
            'valor_final_pasaporte' => 1200.00,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Acceso (credenciales de login)
        DB::table('pats_pasaporte_accesos')->insert([
            'id_pasaporte' => $idPasaporte,
            'tipo_acceso' => 'PACIENTE',
            'correo_usuario' => 'prueba@pats.mx',
            'telefono_usuario' => '5512345678',
            'nombre_usuario' => 'Carlos González',
            'nombre_paciente' => 'Carlos González Prueba',
            'password_hash' => Hash::make('Password123!'),
            'password_temporal' => 0,
            'debe_cambiar_password' => 0,
            'estatus' => 'ACTIVO',
            'activo' => 1,
            'created_at' => now(),
        ]);

        // 3. Historia clínica
        DB::table('pats_historia_clinica')->insert([
            'id_pasaporte' => $idPasaporte,
            'ocupacion' => 'Desarrollador de software',
            'estado_civil' => 'SOLTERO',
            'escolaridad' => 'UNIVERSITARIO',
            'actividad_fisica' => 'MODERADA',
            'tabaquismo' => 'NO',
            'alcohol' => 'OCASIONAL',
            'alimentacion' => 'NORMAL',
            'heredo_familiares' => json_encode(['diabetes', 'hipertension']),
            'personales_patologicos' => 'Ninguno',
            'personales_no_patologicos' => 'Ninguno',
            'enfermedades_previas' => 'Ninguna',
            'alergias' => 'Penicilina',
            'cirugias' => 'Ninguna',
            'medicamentos' => 'Ninguno',
            'intolerancias' => 'Lactosa',
            'peso' => 75.0,
            'altura' => 1.75,
            'imc' => round(75.0 / (1.75 ** 2), 2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Usuario de prueba creado:');
        $this->command->info('  Correo:     prueba@pats.mx');
        $this->command->info('  Contraseña: Password123!');
        $this->command->info("  Pasaporte:  #{$idPasaporte}");
    }
}
