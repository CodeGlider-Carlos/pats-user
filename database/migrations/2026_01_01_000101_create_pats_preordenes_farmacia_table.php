<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_preordenes_farmacia', function (Blueprint $table) {
            $table->bigIncrements('id_preorden');
            $table->unsignedBigInteger('id_pats');
            $table->string('tabla_persona_origen', 40)->default('pats_personas_demo');
            $table->string('curp', 30)->nullable();
            $table->string('nombre_paciente', 180)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('sexo', 10)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->integer('id_servicio')->nullable();
            $table->integer('id_recurso')->nullable();
            $table->date('fecha_programada')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('estatus', 30)->default('PENDIENTE');
            $table->text('observaciones')->nullable();
            $table->bigInteger('creado_por_id')->nullable();
            $table->string('creado_por_usuario', 120)->nullable();
            $table->string('creado_por_nombre', 180)->nullable();
            $table->dateTime('creado_en')->useCurrent();

            $table->index('id_pats', 'idx_ppf_pats');
            $table->index('estatus', 'idx_ppf_estatus');
            $table->index(['region', 'unidad'], 'idx_ppf_region_unidad');
            $table->index('fecha_programada', 'idx_ppf_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_preordenes_farmacia');
    }
};
