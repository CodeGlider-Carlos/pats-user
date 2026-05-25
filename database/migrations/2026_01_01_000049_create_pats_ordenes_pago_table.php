<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_ordenes_pago', function (Blueprint $table) {
            $table->bigIncrements('id_orden');
            $table->string('folio_orden', 60);
            $table->string('referencia_pago', 120)->nullable();
            $table->string('public_token', 120)->nullable();
            $table->dateTime('public_token_expires_at')->nullable();
            $table->string('link_checkout_publico')->nullable();
            $table->boolean('enviado_cliente')->default(0);
            $table->dateTime('fecha_envio_cliente')->nullable();
            $table->string('referencia_externa', 180)->nullable();
            $table->string('order_id_externo', 120)->nullable();
            $table->string('tipo_origen', 30)->default('DISTRIBUIDOR');
            $table->string('origen_checkout', 30)->nullable();
            $table->integer('id_distribuidor')->nullable();
            $table->integer('id_franquicia')->nullable();
            $table->bigInteger('id_pasaporte')->nullable();
            $table->string('correo_usuario_pats', 180);
            $table->string('curp_usuario', 30)->nullable();
            $table->string('nombre_usuario', 180)->nullable();
            $table->string('apellido_pa', 180)->nullable();
            $table->string('apellido_ma', 180)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono_usuario', 20)->nullable();
            $table->bigInteger('id_tipo_precio')->nullable();
            $table->string('tipo_operacion', 40)->default('ALTA_PATS');
            $table->string('frecuencia', 20)->default('ANUAL');
            $table->decimal('monto_orden', 14, 2)->default(0.00);
            $table->decimal('monto_nominal_base', 14, 2)->nullable();
            $table->decimal('monto_extra_recargo', 14, 2)->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->string('pais', 80)->nullable();
            $table->string('region', 40)->nullable();
            $table->string('zona', 120)->nullable();
            $table->string('unidad', 40)->nullable();
            $table->string('tipo_cliente', 50)->nullable();
            $table->string('nombre_empresa')->nullable();
            $table->string('estatus_orden', 30)->default('PENDIENTE');
            $table->string('estatus_pago', 30)->default('PENDIENTE');
            $table->string('proveedor_pasarela', 60)->nullable();
            $table->string('transaccion_id_externa', 120)->nullable();
            $table->string('payment_intent_id', 120)->nullable();
            $table->string('charge_id', 120)->nullable();
            $table->longText('payload_checkout_json')->nullable();
            $table->longText('payload_confirmacion_json')->nullable();
            $table->boolean('usuario_creado')->default(0);
            $table->bigInteger('id_usuario_generado')->nullable();
            $table->dateTime('fecha_alta_usuario')->nullable();
            $table->boolean('pasaporte_creado')->default(0);
            $table->bigInteger('id_pasaporte_generado')->nullable();
            $table->dateTime('fecha_alta_pasaporte')->nullable();
            $table->boolean('procesado_integracion')->default(0);
            $table->dateTime('fecha_procesamiento_integracion')->nullable();
            $table->integer('intentos_procesamiento')->default(0);
            $table->text('error_integracion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('user_creo', 120)->nullable();
            $table->string('user_confirmo', 120)->nullable();
            $table->dateTime('fecha_orden')->useCurrent();
            $table->dateTime('fecha_pago')->nullable();
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('folio_orden', 'uk_folio_orden');
            $table->unique('referencia_pago', 'uk_referencia_pago');
            $table->unique('public_token', 'uk_public_token');
            $table->index('id_distribuidor', 'idx_id_distribuidor');
            $table->index('id_franquicia', 'idx_id_franquicia');
            $table->index('correo_usuario_pats', 'idx_correo_usuario_pats');
            $table->index('curp_usuario', 'idx_curp_usuario');
            $table->index('tipo_operacion', 'idx_tipo_operacion');
            $table->index('frecuencia', 'idx_frecuencia');
            $table->index('estatus_orden', 'idx_estatus_orden');
            $table->index('estatus_pago', 'idx_estatus_pago');
            $table->index('transaccion_id_externa', 'idx_transaccion_id_externa');
            $table->index('charge_id', 'idx_charge_id');
            $table->index('usuario_creado', 'idx_usuario_creado');
            $table->index('pasaporte_creado', 'idx_pasaporte_creado');
            $table->index('procesado_integracion', 'idx_procesado_integracion');
            $table->index('fecha_orden', 'idx_fecha_orden');
            $table->index('fecha_pago', 'idx_fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_ordenes_pago');
    }
};
