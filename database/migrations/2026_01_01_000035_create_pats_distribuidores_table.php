<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_distribuidores', function (Blueprint $table) {
            $table->bigIncrements('id_distribuidor');
            $table->unsignedBigInteger('id_franquicia');
            $table->string('region');
            $table->string('nombre');
            $table->string('rfc');
            $table->string('telefono');
            $table->string('correo');
            $table->decimal('valor_distribucion', 12, 2);
            $table->date('fecha_alta');
            $table->string('zona');
            $table->string('file')->nullable();
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->string('pais')->nullable();
            $table->string('unidad')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->string('codigo_distribuidor', 100)->nullable();
            $table->string('banco')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->string('clabe')->nullable();
            $table->string('titular_cuenta')->nullable();
            $table->string('estatus', 100)->default('ACTIVO');
            $table->boolean('activo')->default(1);
            $table->decimal('comision_acumulada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_pagada_periodo', 12, 2)->default(0.00);
            $table->decimal('comision_por_pagar_periodo', 12, 2)->default(0.00);
            $table->date('fecha_renovacion')->nullable();
            $table->date('fecha_proxima_renovacion')->nullable();
            $table->string('renovacion_estatus', 100)->default('vigente');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('public_checkout_token', 64)->nullable();
            $table->boolean('public_checkout_activo')->default(1);
            $table->dateTime('public_checkout_updated_at')->nullable();

            $table->unique('correo', 'uq_pats_distribuidores_correo');
            $table->unique('codigo_distribuidor', 'uq_pats_distribuidores_codigo');
            $table->index('id_franquicia', 'idx_pats_distribuidores_franquicia');
            $table->index('pais', 'idx_pats_distribuidores_pais');
            $table->index('region', 'idx_pats_distribuidores_region');
            $table->index('zona', 'idx_pats_distribuidores_zona');
            $table->index('unidad', 'idx_pats_distribuidores_unidad');
            $table->index('id_unidad', 'idx_pats_distribuidores_id_unidad');
            $table->index('estatus', 'idx_pats_distribuidores_estatus');
            $table->index('activo', 'idx_pats_distribuidores_activo');
            $table->index('nombre', 'idx_pats_distribuidores_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_distribuidores');
    }
};
