<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_dispo_recursos', function (Blueprint $table) {
            $table->increments('id_recurso');
            $table->integer('id_user')->nullable();
            $table->integer('id_med');
            $table->integer('id_servicio');
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('nombre_recurso', 180);
            $table->string('especialidad', 150)->nullable();
            $table->string('tipo_recurso', 80)->nullable();
            $table->integer('capacidad')->nullable()->default(1);
            $table->boolean('activo')->default(1);

            $table->index('id_servicio', 'idx_servicio');
            $table->index('region', 'idx_region');
            $table->index('unidad', 'idx_unidad');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_dispo_recursos');
    }
};
