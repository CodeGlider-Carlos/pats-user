<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_franquicia_historial', function (Blueprint $table) {
            $table->increments('id_historial');
            $table->integer('id_solicitud');
            $table->string('evento_tipo', 80);
            $table->string('estatus_anterior', 60)->nullable();
            $table->string('estatus_nuevo', 60)->nullable();
            $table->longText('payload_json')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('user_evento')->nullable();
            $table->dateTime('fecha_evento')->useCurrent();
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_solicitud', 'idx_psfh_id_solicitud');
            $table->index('evento_tipo', 'idx_psfh_evento_tipo');
            $table->index('estatus_anterior', 'idx_psfh_estatus_anterior');
            $table->index('estatus_nuevo', 'idx_psfh_estatus_nuevo');
            $table->index('user_evento', 'idx_psfh_user_evento');
            $table->index('fecha_evento', 'idx_psfh_fecha_evento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_franquicia_historial');
    }
};
