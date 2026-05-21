<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Siembra registros de ejemplo en pats_preview_dist.
 * Solo para entornos de desarrollo/demo.
 * Requiere que existan solicitudes en pats_solicitudes_distribuidor.
 */
class PreviewDistSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los primeros 3 IDs de solicitud existentes
        $solicitudes = DB::table('pats_solicitudes_distribuidor')
            ->orderBy('id_solicitud')
            ->limit(3)
            ->pluck('id_solicitud');

        if ($solicitudes->isEmpty()) {
            $this->command->warn('PreviewDistSeeder: no hay solicitudes en pats_solicitudes_distribuidor. Omitiendo.');
            return;
        }

        $now = now();

        foreach ($solicitudes as $idSolicitud) {
            // Evitar duplicados
            $existe = DB::table('pats_preview_dist')
                ->where('id_solicitud', $idSolicitud)
                ->exists();

            if ($existe) {
                continue;
            }

            DB::table('pats_preview_dist')->insert([
                'id_solicitud'   => $idSolicitud,
                'selfie_path'    => "private/solicitudes/distribuidor/{$idSolicitud}/selfie_demo.jpg",
                'contrato_path'  => "private/solicitudes/distribuidor/{$idSolicitud}/contrato_demo.pdf",
                'firma_path'     => "private/solicitudes/distribuidor/{$idSolicitud}/firma_demo.png",
                'selfie_mime'    => 'image/jpeg',
                'contrato_mime'  => 'application/pdf',
                'firma_mime'     => 'image/png',
                'selfie_kb'      => 120,
                'contrato_kb'    => 350,
                'firma_kb'       => 45,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->command->info('PreviewDistSeeder: ' . $solicitudes->count() . ' registros procesados.');
    }
}
