<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `tarjetas_misional` vive en la base dcommx1_ezsystem_modelpats
     * (conexión `modelpats`). Solo agregamos la bandera de reseña; el
     * detalle de la encuesta se guarda en pats_encuestas_satisfaccion.
     */
    public function up(): void
    {
        Schema::connection('modelpats')->table('tarjetas_misional', function (Blueprint $table) {
            $table->string('reviewed', 20)->nullable()->after('cerrado_at');
            $table->index('reviewed', 'idx_tm_reviewed');
        });
    }

    public function down(): void
    {
        Schema::connection('modelpats')->table('tarjetas_misional', function (Blueprint $table) {
            $table->dropIndex('idx_tm_reviewed');
            $table->dropColumn('reviewed');
        });
    }
};
