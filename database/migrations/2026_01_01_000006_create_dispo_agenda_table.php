<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispo_agenda', function (Blueprint $table) {
            $table->increments('id_agenda');
            $table->integer('id_servicio');
            $table->integer('id_recurso');
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('tipo_bloque', 40);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->integer('cupos')->nullable()->default(1);
            $table->integer('ocupado')->nullable()->default(0);
            $table->boolean('recurrente')->default(0);
            $table->string('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('creado_por')->nullable();
            $table->string('usuario', 120)->nullable();
            $table->dateTime('creado_en');
            $table->dateTime('actualizado_en')->nullable();
            $table->boolean('activo')->default(1);

            $table->index('id_servicio', 'idx_servicio');
            $table->index('id_recurso', 'idx_recurso');
            $table->index('fecha_inicio', 'idx_fecha_inicio');
            $table->index('fecha_fin', 'idx_fecha_fin');
            $table->index('region', 'idx_region');
            $table->index('unidad', 'idx_unidad');
            $table->index('tipo_bloque', 'idx_tipo_bloque');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispo_agenda');
    }
};
