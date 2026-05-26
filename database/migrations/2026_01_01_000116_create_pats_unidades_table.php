<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_unidades', function (Blueprint $table) {
            $table->bigIncrements('id_unidad');
            $table->string('pais')->nullable();
            $table->string('region');
            $table->string('unidad');
            $table->string('nombre_unidad');
            $table->string('razon_social')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->text('direccion')->nullable();
            $table->string('estatus', 100)->default('ACTIVA');
            $table->boolean('activo')->default(1);
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('pais', 'idx_pats_unidades_pais');
            $table->index('region', 'idx_pats_unidades_region');
            $table->index('unidad', 'idx_pats_unidades_unidad');
            $table->index('activo', 'idx_pats_unidades_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_unidades');
    }
};
