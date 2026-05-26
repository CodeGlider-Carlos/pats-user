<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_pats_estudios', function (Blueprint $table) {
            $table->bigIncrements('id_estudio');
            $table->integer('id_servicio');
            $table->string('id_misional', 40);
            $table->string('clave_estudio', 60)->nullable();
            $table->string('nombre_estudio');
            $table->text('alias_busqueda')->nullable();
            $table->boolean('requiere_cita')->default(1);
            $table->integer('duracion_min')->default(30);
            $table->string('preparacion_resumen')->nullable();
            $table->text('preparacion_detalle')->nullable();
            $table->boolean('activo')->default(1);
            $table->dateTime('creado_en')->useCurrent();

            $table->index('id_servicio', 'idx_cpe_servicio');
            $table->index('id_misional', 'idx_cpe_misional');
            $table->index('requiere_cita', 'idx_cpe_requiere_cita');
            $table->index('activo', 'idx_cpe_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_pats_estudios');
    }
};
