<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_personas_demo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_pats', 40)->nullable();
            $table->string('curp', 32);
            $table->string('nombre', 120);
            $table->string('apellido_paterno', 120)->nullable();
            $table->string('apellido_materno', 120)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('sexo', 10)->nullable();
            $table->string('ruta_foto')->nullable();
            $table->string('estatus', 30)->default('ACTIVO');
            $table->boolean('activo')->default(1);
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->useCurrent()->useCurrentOnUpdate();

            $table->unique('curp', 'uniq_pats_curp');
            $table->index('estatus', 'idx_pats_demo_estatus');
            $table->index('activo', 'idx_pats_demo_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_personas_demo');
    }
};
