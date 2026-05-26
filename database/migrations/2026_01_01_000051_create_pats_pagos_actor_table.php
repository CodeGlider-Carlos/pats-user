<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pagos_actor', function (Blueprint $table) {
            $table->bigIncrements('id_pago');
            $table->string('actor_tipo', 50);
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('id_contrato');
            $table->unsignedBigInteger('id_parcialidad')->nullable();
            $table->decimal('monto_pago', 14, 2)->default(0.00);
            $table->dateTime('fecha_pago');
            $table->string('tipo_pago', 50)->default('TRANSFERENCIA');
            $table->string('referencia_pago', 120)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->string('evidencia_nombre_original')->nullable();
            $table->string('evidencia_mime', 120)->nullable();
            $table->integer('evidencia_size_kb')->nullable();
            $table->string('estatus', 50)->default('APLICADO');
            $table->text('motivo_cancelacion')->nullable();
            $table->string('usuario_registra', 120)->nullable();
            $table->string('usuario_cancela', 120)->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['actor_tipo', 'actor_id'], 'idx_pats_pagos_actor_actor');
            $table->index('id_contrato', 'idx_pats_pagos_actor_contrato');
            $table->index('id_parcialidad', 'idx_pats_pagos_actor_parcialidad');
            $table->index('fecha_pago', 'idx_pats_pagos_actor_fecha');
            $table->index('estatus', 'idx_pats_pagos_actor_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pagos_actor');
    }
};
