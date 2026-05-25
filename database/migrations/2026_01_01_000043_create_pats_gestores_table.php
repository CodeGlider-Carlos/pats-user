<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_gestores', function (Blueprint $table) {
            $table->increments('id_gestor');
            $table->string('nombre_gestor', 180);
            $table->string('tipo_persona', 20)->default('FISICA');
            $table->string('razon_social', 180)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 180);
            $table->text('direccion')->nullable();
            $table->string('pais')->nullable()->default('México');
            $table->string('estado')->nullable();
            $table->string('municipio')->nullable();
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero_exterior', 50)->nullable();
            $table->string('numero_interior', 50)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->text('referencias_direccion')->nullable();
            $table->string('banco')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->string('clabe')->nullable();
            $table->string('titular_cuenta')->nullable();
            $table->integer('id_user_pats')->nullable();
            $table->boolean('activo')->default(1);
            $table->decimal('comision_acumulada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_pagada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_por_pagar_periodo', 12, 2)->default(0.00);
            $table->string('public_checkout_token', 128)->nullable();
            $table->boolean('public_checkout_activo')->default(1);
            $table->dateTime('public_checkout_updated_at')->nullable();
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('correo', 'uq_gestor_correo');
            $table->unique('public_checkout_token', 'uk_pats_gestores_public_checkout_token');
            $table->index('id_user_pats', 'idx_user_pats');
            $table->index('tipo_persona', 'idx_pats_gestores_tipo_persona');
            $table->index('activo', 'idx_pats_gestores_activo');
            $table->index('pais', 'idx_pats_gestores_pais');
            $table->index('estado', 'idx_pats_gestores_estado');
            $table->index('municipio', 'idx_pats_gestores_municipio');
            $table->index('codigo_postal', 'idx_pats_gestores_cp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_gestores');
    }
};
