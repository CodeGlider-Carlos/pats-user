<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispo_reglas', function (Blueprint $table) {
            $table->increments('id_regla');
            $table->integer('id_servicio');
            $table->integer('id_recurso');
            $table->tinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('cupos')->nullable()->default(1);
            $table->string('tipo_bloque', 40)->default('DISPONIBLE');
            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fin')->nullable();
            $table->boolean('activo')->default(1);
            $table->integer('creado_por')->nullable();
            $table->dateTime('creado_en');

            $table->index('id_servicio', 'idx_servicio');
            $table->index('id_recurso', 'idx_recurso');
            $table->index('dia_semana', 'idx_dia_semana');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispo_reglas');
    }
};
