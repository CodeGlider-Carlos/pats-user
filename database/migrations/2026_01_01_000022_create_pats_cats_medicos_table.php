<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_cats_medicos', function (Blueprint $table) {
            $table->string('id_registro', 36)->nullable();
            $table->integer('id_medico_leadplus')->nullable();
            $table->string('nombre', 180)->nullable();
            $table->string('apellido_paterno', 120)->nullable();
            $table->string('apellido_materno', 120)->nullable();
            $table->string('nombre_completo', 260)->nullable();
            $table->text('direccion')->nullable();
            $table->string('especialidad', 180)->nullable();
            $table->string('perfil_profesional', 100)->nullable();
            $table->text('perfil_profesional_desc')->nullable();
            $table->string('es_especialista', 10)->nullable();
            $table->string('estatus_credencializacion', 80)->nullable();
            $table->string('credencializado', 10)->nullable();
            $table->dateTime('fecha_credencializacion')->nullable();
            $table->dateTime('fecha_vencimiento_credencializacion')->nullable();
            $table->string('cedula_prof', 80)->nullable();
            $table->string('cedula_especialidad', 80)->nullable();
            $table->string('cedula_mg', 80)->nullable();
            $table->string('cedula_esp', 80)->nullable();
            $table->string('telefono', 80)->nullable();
            $table->string('telefono_celular', 50)->nullable();
            $table->string('telefono_consultorio', 50)->nullable();
            $table->string('email', 180)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('unidad', 180)->nullable();
            $table->longText('redes_json')->nullable();
            $table->dateTime('fecha_sync')->nullable();
            $table->boolean('activo')->nullable()->default(1);
            $table->longText('privilegios_json')->nullable();
            $table->longText('entornos_json')->nullable();
            $table->longText('grupos_poblacionales_json')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('usuario_registro', 120)->nullable();
            $table->string('usuario_actualizo', 120)->nullable();
            $table->string('usuario_sync', 120)->nullable();

            $table->index('id_medico_leadplus', 'idx_pats_medicos_leadplus');
            $table->index('activo', 'idx_pats_medicos_activo');
            $table->index(['region', 'unidad'], 'idx_pats_medicos_region_unidad');
            $table->index('nombre_completo', 'idx_pats_medicos_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_cats_medicos');
    }
};
