<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pagos_comision', function (Blueprint $table) {
            $table->bigIncrements('id_pago_comision');
            $table->unsignedBigInteger('id_comision');
            $table->dateTime('fecha_pago');
            $table->decimal('monto_pagado', 12, 2);
            $table->string('moneda', 10)->default('MXN');
            $table->string('evidencia_archivo')->nullable();
            $table->string('evidencia_tipo', 50)->nullable();
            $table->string('referencia_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_pago')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_comision', 'idx_pats_pagos_comision_id_comision');
            $table->index('fecha_pago', 'idx_pats_pagos_comision_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pagos_comision');
    }
};
