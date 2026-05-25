<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_franquicias', function (Blueprint $table) {
            $table->bigIncrements('id_franquicia');
            $table->string('region');
            $table->string('nombre_franquicia');
            $table->string('razon_social');
            $table->string('franquiciatario');
            $table->string('rfc');
            $table->string('zona')->nullable();
            $table->string('pais')->nullable();
            $table->string('telefono');
            $table->string('correo');
            $table->decimal('valor_franquicia', 12, 2);
            $table->date('fecha_alta');
            $table->string('file')->nullable();
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->string('unidad')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->string('codigo_franquicia', 100)->nullable();
            $table->string('banco')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->string('clabe')->nullable();
            $table->string('titular_cuenta')->nullable();
            $table->string('estatus', 100)->default('ACTIVA');
            $table->boolean('activo')->default(1);
            $table->decimal('comision_acumulada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_pagada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_por_pagar_periodo', 12, 2)->default(0.00);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('titularidad_tipo', 20)->default('INDIVIDUAL');
            $table->integer('total_titulares')->default(1);
            $table->boolean('tiene_gestor')->default(0);
            $table->string('public_checkout_token', 128)->nullable();
            $table->boolean('public_checkout_activo')->default(1);
            $table->dateTime('public_checkout_updated_at')->nullable();

            $table->unique('correo', 'uq_pats_franquicias_correo');
            $table->unique('codigo_franquicia', 'uq_pats_franquicias_codigo');
            $table->unique('public_checkout_token', 'uk_pats_franquicias_public_checkout_token');
            $table->index('pais', 'idx_pats_franquicias_pais');
            $table->index('region', 'idx_pats_franquicias_region');
            $table->index('zona', 'idx_pats_franquicias_zona');
            $table->index('unidad', 'idx_pats_franquicias_unidad');
            $table->index('id_unidad', 'idx_pats_franquicias_id_unidad');
            $table->index('estatus', 'idx_pats_franquicias_estatus');
            $table->index('activo', 'idx_pats_franquicias_activo');
            $table->index('nombre_franquicia', 'idx_pats_franquicias_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_franquicias');
    }
};
