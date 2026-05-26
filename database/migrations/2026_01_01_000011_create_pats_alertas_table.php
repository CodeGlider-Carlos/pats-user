<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_alertas', function (Blueprint $table) {
            $table->bigIncrements('id_alerta');
            $table->string('tipo_alerta', 100);
            $table->string('entidad_tipo', 100);
            $table->unsignedBigInteger('entidad_id');
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('nivel', 50)->default('info');
            $table->boolean('visto')->default(0);
            $table->dateTime('fecha_alerta');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('tipo_alerta', 'idx_pats_alertas_tipo');
            $table->index('entidad_tipo', 'idx_pats_alertas_entidad_tipo');
            $table->index('entidad_id', 'idx_pats_alertas_entidad_id');
            $table->index('visto', 'idx_pats_alertas_visto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_alertas');
    }
};
