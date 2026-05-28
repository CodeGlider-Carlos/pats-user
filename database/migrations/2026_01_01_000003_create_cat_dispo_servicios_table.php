<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_dispo_servicios', function (Blueprint $table) {
            $table->increments('id_servicio');
            $table->string('region', 20)->nullable();
            $table->string('unidad', 20)->nullable();
            $table->string('clave', 60);
            $table->string('id_misional', 20)->nullable();
            $table->string('servicio', 150);
            $table->string('icono', 80)->nullable();
            $table->string('color', 30)->nullable();
            $table->boolean('activo')->default(1);

            $table->index('clave', 'idx_clave');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_dispo_servicios');
    }
};
