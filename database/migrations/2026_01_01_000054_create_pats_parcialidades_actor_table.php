<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_parcialidades_actor', function (Blueprint $table) {
            $table->bigIncrements('id_parcialidad');
            $table->unsignedBigInteger('id_contrato');
            $table->integer('numero_parcialidad');
            $table->date('fecha_vencimiento');
            $table->decimal('monto_programado', 14, 2)->default(0.00);
            $table->decimal('monto_pagado', 14, 2)->default(0.00);
            $table->decimal('saldo_pendiente', 14, 2)->default(0.00);
            $table->decimal('recargo_acumulado', 14, 2)->default(0.00);
            $table->integer('dias_mora')->default(0);
            $table->dateTime('fecha_pago_total')->nullable();
            $table->string('estatus', 50)->default('PENDIENTE');
            $table->text('observaciones')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_contrato', 'numero_parcialidad'], 'uq_pats_parcialidades_actor_numero');
            $table->index('id_contrato', 'idx_pats_parcialidades_contrato');
            $table->index('fecha_vencimiento', 'idx_pats_parcialidades_vencimiento');
            $table->index('estatus', 'idx_pats_parcialidades_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_parcialidades_actor');
    }
};
