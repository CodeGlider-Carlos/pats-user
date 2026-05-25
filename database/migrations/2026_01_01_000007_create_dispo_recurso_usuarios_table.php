<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispo_recurso_usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_recurso');
            $table->integer('id_usuario');
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->integer('id_servicio');
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['id_recurso', 'id_usuario', 'id_servicio'], 'uk_recurso_usuario_servicio');
            $table->index('id_recurso', 'idx_recurso');
            $table->index('id_usuario', 'idx_usuario');
            $table->index('id_servicio', 'idx_servicio');
            $table->index('region', 'idx_region');
            $table->index('unidad', 'idx_unidad');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispo_recurso_usuarios');
    }
};
