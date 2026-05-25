<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_historial', function (Blueprint $table) {
            $table->bigIncrements('id_historial');
            $table->string('entidad_tipo', 100);
            $table->unsignedBigInteger('entidad_id');
            $table->string('evento_tipo', 100);
            $table->string('estado_anterior', 100)->nullable();
            $table->string('estado_nuevo', 100)->nullable();
            $table->longText('payload_json')->nullable();
            $table->unsignedBigInteger('user_evento')->nullable();
            $table->dateTime('fecha_evento');
            $table->timestamp('created_at')->nullable();

            $table->index('entidad_tipo', 'idx_pats_historial_entidad_tipo');
            $table->index('entidad_id', 'idx_pats_historial_entidad_id');
            $table->index('evento_tipo', 'idx_pats_historial_evento_tipo');
            $table->index('fecha_evento', 'idx_pats_historial_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_historial');
    }
};
