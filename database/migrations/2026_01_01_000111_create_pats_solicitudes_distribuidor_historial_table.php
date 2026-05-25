<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_distribuidor_historial', function (Blueprint $table) {
            $table->increments('id_historial_solicitud');
            $table->integer('id_solicitud');
            $table->string('evento_tipo', 60);
            $table->string('estatus_anterior', 50)->nullable();
            $table->string('estatus_nuevo', 50)->nullable();
            $table->longText('payload_json')->nullable();
            $table->integer('user_evento')->nullable();
            $table->dateTime('fecha_evento')->useCurrent();
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_solicitud', 'idx_psdh_solicitud');
            $table->index('evento_tipo', 'idx_psdh_evento');
            $table->index('fecha_evento', 'idx_psdh_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor_historial');
    }
};
