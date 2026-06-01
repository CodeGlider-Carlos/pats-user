<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatsRulesSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('pats_bot_rules')->count() > 0) {
            return;
        }

        DB::table('pats_bot_rules')->insert([
            [
                'id' => 1, 'rule_key' => 'calculo_reactivacion_mensual', 'nombre' => 'Cálculo de reactivación mensual',
                'descripcion' => 'Calcula adeudo estimado por mensualidades vencidas, mensualidad vigente y cuota de reactivación.',
                'tipo' => 'CALCULO',
                'config_json' => '{"moneda":"MXN","formula":"meses_vencidos * 800 + mensualidad_vigente + meses_vencidos * 100","mensualidad":800,"cuota_reactivacion_mes":100}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 2, 'rule_key' => 'validar_no_seguro', 'nombre' => 'Aclaración obligatoria: PATS no es seguro',
                'descripcion' => 'Cuando el usuario use palabras como seguro, póliza, cobertura o cubre, aclarar que PATS no es seguro médico.',
                'tipo' => 'RESPUESTA_DINAMICA',
                'config_json' => '{"mensaje_base":"PATS no es un seguro médico; es un programa de beneficios, descuentos y precios preferenciales dentro de la red autorizada."}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 3, 'rule_key' => 'validar_red_autorizada', 'nombre' => 'Validación de red autorizada',
                'descripcion' => 'Antes de confirmar un servicio, debe validarse que el médico, hospital, laboratorio, imagenología o farmacia esté dentro de la red autorizada PATS.',
                'tipo' => 'VALIDACION',
                'config_json' => '{"requiere_validar_red":true}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 4, 'rule_key' => 'validar_estatus_activo', 'nombre' => 'Validación de estatus activo',
                'descripcion' => 'Para usar beneficios, debe validarse que el usuario tenga PATS activo y vigente.',
                'tipo' => 'VALIDACION',
                'config_json' => '{"requiere_estatus_activo":true}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 5, 'rule_key' => 'validar_mayor_65', 'nombre' => 'Regla mayores de 65 años',
                'descripcion' => 'Usuarios de 65 años o más requieren incorporar dos personas adicionales menores de esa edad.',
                'tipo' => 'VALIDACION',
                'config_json' => '{"edad_minima_regla":65,"personas_adicionales_requeridas":2}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 6, 'rule_key' => 'validar_menor_dependiente', 'nombre' => 'Regla menores o dependientes',
                'descripcion' => 'Menores de edad o dependientes requieren padre, madre o tutor responsable.',
                'tipo' => 'VALIDACION',
                'config_json' => '{"edad_mayoria":18,"requiere_tutor":true}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
            [
                'id' => 7, 'rule_key' => 'validar_tabulador', 'nombre' => 'Validación de tabulador PATS',
                'descripcion' => 'Antes de confirmar precio, debe consultarse tabulador vigente o lista oficial autorizada.',
                'tipo' => 'VALIDACION',
                'config_json' => '{"requiere_tabulador_vigente":true}',
                'activo' => 1, 'created_at' => '2026-04-24 07:46:25', 'updated_at' => '2026-04-24 07:46:25',
            ],
        ]);
    }
}
