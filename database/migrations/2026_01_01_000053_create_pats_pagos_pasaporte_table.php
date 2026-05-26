<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pagos_pasaporte', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_orden')->nullable();
            $table->unsignedBigInteger('id_pasaporte');
            $table->bigInteger('id_franquicia')->nullable();
            $table->bigInteger('id_distribuidor')->nullable();
            $table->bigInteger('id_tipo_precio')->nullable();
            $table->string('correo', 180)->nullable();
            $table->string('curp', 30)->nullable();
            $table->string('nombre_usuario', 180)->nullable();
            $table->string('apellido_pa', 180)->nullable();
            $table->string('apellido_ma', 180)->nullable();
            $table->string('tipo_operacion', 40)->nullable();
            $table->decimal('monto', 12, 2);
            $table->decimal('monto_nominal_base', 12, 2)->nullable();
            $table->decimal('monto_extra_recargo', 12, 2)->nullable();
            $table->string('frecuencia');
            $table->string('metodo_pago')->nullable();
            $table->string('referencia_pago')->nullable();
            $table->string('referencia_externa', 180)->nullable();
            $table->string('transaccion_id_externa', 120)->nullable();
            $table->string('payment_intent_id', 120)->nullable();
            $table->string('charge_id', 120)->nullable();
            $table->string('proveedor_pasarela', 60)->nullable();
            $table->string('estatus_pago');
            $table->longText('response_json')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->text('observaciones')->nullable();
            $table->string('evidencia_pago')->nullable();
            $table->unsignedBigInteger('user_confirmo')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_pasaporte', 'idx_pats_pagos_pasaporte_pasaporte');
            $table->index('estatus_pago', 'idx_pats_pagos_pasaporte_estatus');
            $table->index('fecha_pago', 'idx_pats_pagos_pasaporte_fecha');
            $table->index('referencia_pago', 'idx_pats_pagos_pasaporte_referencia');
            $table->index('fecha_confirmacion', 'idx_pats_pagos_pasaporte_confirmacion');
            $table->index('id_orden', 'idx_pats_pagos_pasaporte_id_orden');
            $table->index('id_franquicia', 'idx_pats_pagos_pasaporte_franquicia');
            $table->index('id_distribuidor', 'idx_pats_pagos_pasaporte_distribuidor');
            $table->index('id_tipo_precio', 'idx_pats_pagos_pasaporte_tipo_precio');
            $table->index('correo', 'idx_pats_pagos_pasaporte_correo');
            $table->index('curp', 'idx_pats_pagos_pasaporte_curp');
            $table->index('transaccion_id_externa', 'idx_pats_pagos_pasaporte_trx');
            $table->index('charge_id', 'idx_pats_pagos_pasaporte_charge');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pagos_pasaporte');
    }
};
