<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Evitar duplicados si ya existe el correo demo
        if (DB::table('pats_pasaporte_accesos')->where('correo_usuario', 'demo@pats.mx')->exists()) {
            $this->command->warn('El usuario demo@pats.mx ya existe. Se omite la creación.');

            return;
        }

        // 1. Pasaporte de demostración
        $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
            'id_franquicia' => 1,
            'id_distribuidor' => 1,
            'id_tipo_precio' => 1,
            'curp' => 'DEMO900101HDFDMR00',
            'nombres' => 'Demo',
            'apellido_pa' => 'Ventas',
            'apellido_ma' => 'PATS',
            'fecha_nacimiento' => '1990-01-01',
            'telefono' => '5500000000',
            'correo' => 'demo@pats.mx',
            'fecha_alta' => now(),
            'vigencia' => now()->addYears(10)->toDateString(),
            'frecuencia_pago' => 'ANUAL',
            'estatus' => 'ACTIVO',
            'valor_pasaporte' => 1200.00,
            'valor_final_pasaporte' => 1200.00,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Acceso con tipo_acceso = DEMO
        DB::table('pats_pasaporte_accesos')->insert([
            'id_pasaporte' => $idPasaporte,
            'tipo_acceso' => 'DEMO',
            'correo_usuario' => 'demo@pats.mx',
            'telefono_usuario' => '5500000000',
            'nombre_usuario' => 'Demo Ventas',
            'nombre_paciente' => 'Demo Ventas PATS',
            'password_hash' => Hash::make('Demo@Pats2024!'),
            'password_temporal' => 0,
            'debe_cambiar_password' => 0,
            'estatus' => 'ACTIVO',
            'activo' => 1,
            'created_at' => now(),
        ]);

        // 3. Historia clínica de ejemplo para mostrar datos en la demo
        DB::table('pats_historia_clinica')->insert([
            'id_pasaporte' => $idPasaporte,
            'ocupacion' => 'Ejecutivo de ventas',
            'estado_civil' => 'CASADO',
            'escolaridad' => 'UNIVERSITARIO',
            'actividad_fisica' => 'MODERADA',
            'tabaquismo' => 'NO',
            'alcohol' => 'OCASIONAL',
            'alimentacion' => 'NORMAL',
            'heredo_familiares' => json_encode(['diabetes']),
            'personales_patologicos' => 'Ninguno',
            'personales_no_patologicos' => 'Ninguno',
            'enfermedades_previas' => 'Ninguna',
            'alergias' => 'Ninguna',
            'cirugias' => 'Ninguna',
            'medicamentos' => 'Ninguno',
            'intolerancias' => 'Ninguna',
            'peso' => 70.0,
            'altura' => 1.70,
            'imc' => round(70.0 / (1.70 ** 2), 2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Usuario demo creado:');
        $this->command->info('  Correo:     demo@pats.mx');
        $this->command->info('  Contraseña: Demo@Pats2024!');
        $this->command->info("  Pasaporte:  #{$idPasaporte}");
        $this->command->info('  Tipo:       DEMO (solo lectura)');
    }
}
