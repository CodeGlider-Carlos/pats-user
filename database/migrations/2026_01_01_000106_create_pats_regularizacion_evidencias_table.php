<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_regularizacion_evidencias', function (Blueprint $table) {
            $table->increments('id_regularizacion');
            $table->unsignedInteger('id_pats');
            $table->string('curp', 18);
            $table->string('nombre_paciente', 220)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo_solicitud', ['COMPRA', 'REACTIVACION', 'ACTIVACION'])->default('COMPRA');
            $table->enum('estatus', ['PENDIENTE', 'APROBADA', 'RECHAZADA', 'CANCELADA'])->default('PENDIENTE');
            $table->string('numero_referencia', 120);
            $table->string('evidencia_archivo', 500);
            $table->string('evidencia_nombre_original')->nullable();
            $table->string('evidencia_mime', 120)->nullable();
            $table->unsignedInteger('evidencia_size')->nullable();
            $table->string('origen_modulo', 50)->default('VIAS');
            $table->string('id_misional', 80)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('creado_por')->nullable();
            $table->string('creado_por_usuario', 120)->nullable();
            $table->dateTime('creado_en')->useCurrent();
            $table->integer('revisado_por')->nullable();
            $table->dateTime('revisado_en')->nullable();
            $table->text('nota_revision')->nullable();

            $table->index('id_pats', 'idx_pats_regularizacion_id_pats');
            $table->index('curp', 'idx_pats_regularizacion_curp');
            $table->index('estatus', 'idx_pats_regularizacion_estatus');
            $table->index('numero_referencia', 'idx_pats_regularizacion_referencia');
            $table->index('creado_en', 'idx_pats_regularizacion_creado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_regularizacion_evidencias');
    }
};
