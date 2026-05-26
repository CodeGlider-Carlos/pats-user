<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_cats_medicos_redes', function (Blueprint $table) {
            $table->string('id_registro', 36)->nullable();
            $table->string('id_medico_pats', 36)->nullable();
            $table->integer('id_medico_leadplus')->nullable();
            $table->integer('id_red')->nullable();
            $table->string('nombre_red', 220)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('unidad', 180)->nullable();
            $table->boolean('activo')->nullable()->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('usuario_registro', 120)->nullable();
            $table->string('usuario_actualizo', 120)->nullable();
            $table->string('usuario_sync', 120)->nullable();

            $table->index('id_medico_leadplus', 'idx_pats_medicos_redes_leadplus');
            $table->index('id_red', 'idx_pats_medicos_redes_red');
            $table->index(['region', 'unidad'], 'idx_pats_medicos_redes_region_unidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_cats_medicos_redes');
    }
};
