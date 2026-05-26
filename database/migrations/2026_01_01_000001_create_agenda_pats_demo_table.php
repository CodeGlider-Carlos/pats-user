<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_pats_demo', function (Blueprint $table) {
            $table->increments('id_agenda');
            $table->string('region', 120);
            $table->string('unidad', 120);
            $table->string('curp', 32);
            $table->string('nombre_paciente', 220);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('sexo', 10)->nullable();
            $table->string('id_misional', 50);
            $table->string('misional', 180);
            $table->integer('id_servicio');
            $table->integer('id_recurso');
            $table->string('resumen_estudios')->nullable();
            $table->date('fecha_programada');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('tipo_registro', 80)->default('CONSULTA');
            $table->integer('prioridad')->default(2);
            $table->string('folio_externo', 120)->nullable();
            $table->string('origen_sistema', 120)->nullable()->default('PATS_DEMO');
            $table->string('estatus', 50)->default('PROGRAMADO');
            $table->text('observaciones')->nullable();
            $table->boolean('confirmado')->default(0);
            $table->dateTime('confirmado_en')->nullable();
            $table->string('confirmado_por', 120)->nullable();
            $table->dateTime('cancelado_en')->nullable();
            $table->string('cancelado_por', 120)->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->boolean('iniciado_servicio')->default(0);
            $table->dateTime('iniciado_en')->nullable();
            $table->string('iniciado_por', 120)->nullable();
            $table->integer('id_expediente')->nullable();
            $table->integer('id_episodio')->nullable();
            $table->integer('id_tarjeta')->nullable();
            $table->boolean('activo')->default(1);
            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            $table->index('fecha_programada', 'idx_pats_fecha');
            $table->index('region', 'idx_pats_region');
            $table->index('unidad', 'idx_pats_unidad');
            $table->index('estatus', 'idx_pats_estatus');
            $table->index('curp', 'idx_pats_curp');
            $table->index('id_misional', 'idx_pats_id_misional');
            $table->index('id_servicio', 'idx_pats_id_servicio');
            $table->index('id_recurso', 'idx_pats_id_recurso');
            $table->index('activo', 'idx_pats_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_pats_demo');
    }
};
