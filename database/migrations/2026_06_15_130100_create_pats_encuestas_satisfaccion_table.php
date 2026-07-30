<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_encuestas_satisfaccion', function (Blueprint $table) {
            $table->bigIncrements('id_encuesta');
            $table->unsignedBigInteger('id_tarjeta');
            $table->string('code_pasaporte', 30)->nullable();
            $table->unsignedBigInteger('id_pasaporte')->nullable();
            $table->string('tipo_servicio', 20);
            $table->string('modelo', 500)->nullable();
            $table->string('estatus', 20)->default('completada');

            // Calificaciones por escala 1-5 con su comentario opcional.
            $table->unsignedTinyInteger('adm_recepcion')->nullable();
            $table->text('adm_recepcion_com')->nullable();
            $table->unsignedTinyInteger('urgencias')->nullable();
            $table->text('urgencias_com')->nullable();
            $table->unsignedTinyInteger('medico')->nullable();
            $table->text('medico_com')->nullable();
            $table->unsignedTinyInteger('enfermeria')->nullable();
            $table->text('enfermeria_com')->nullable();
            $table->unsignedTinyInteger('personal')->nullable();
            $table->text('personal_com')->nullable();
            $table->unsignedTinyInteger('instalaciones')->nullable();
            $table->text('instalaciones_com')->nullable();
            $table->unsignedTinyInteger('pats_explicacion')->nullable();
            $table->text('pats_explicacion_com')->nullable();
            $table->unsignedTinyInteger('pats_descuentos')->nullable();
            $table->text('pats_descuentos_com')->nullable();

            // Recomendación (NPS) 0-10.
            $table->unsignedTinyInteger('nps')->nullable();
            $table->text('nps_com')->nullable();

            // Preguntas abiertas.
            $table->text('lo_que_mas_gusto')->nullable();
            $table->text('que_mejorar')->nullable();

            $table->timestamps();

            $table->unique('id_tarjeta', 'uq_encuesta_tarjeta');
            $table->index('code_pasaporte', 'idx_encuesta_code');
            $table->index('tipo_servicio', 'idx_encuesta_tipo');
            $table->index('estatus', 'idx_encuesta_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_encuestas_satisfaccion');
    }
};
