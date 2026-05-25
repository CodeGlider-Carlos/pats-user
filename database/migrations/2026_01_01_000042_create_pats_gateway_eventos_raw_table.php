<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_gateway_eventos_raw', function (Blueprint $table) {
            $table->bigIncrements('id_evento_raw');
            $table->string('proveedor', 60);
            $table->string('ambiente', 20)->default('PROD');
            $table->string('event_id', 120)->nullable();
            $table->string('event_type', 120)->nullable();
            $table->string('event_status', 60)->nullable();
            $table->string('transaccion_id_externa', 120)->nullable();
            $table->string('referencia_externa', 180)->nullable();
            $table->string('payment_intent_id', 120)->nullable();
            $table->string('charge_id', 120)->nullable();
            $table->string('order_id', 120)->nullable();
            $table->string('customer_id_externo', 120)->nullable();
            $table->decimal('monto', 14, 2)->nullable();
            $table->string('moneda', 10)->nullable();
            $table->longText('payload_json');
            $table->longText('headers_json')->nullable();
            $table->boolean('firma_valida')->default(0);
            $table->boolean('procesado')->default(0);
            $table->integer('intentos_procesamiento')->default(0);
            $table->string('fuente', 30)->default('WEBHOOK');
            $table->string('ip_origen', 80)->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('fecha_evento_proveedor')->nullable();
            $table->dateTime('fecha_recepcion')->useCurrent();
            $table->dateTime('fecha_procesamiento')->nullable();
            $table->text('error_procesamiento')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['proveedor', 'event_id'], 'uk_evento_proveedor');
            $table->index('referencia_externa', 'idx_referencia_externa');
            $table->index('order_id', 'idx_order_id');
            $table->index('procesado', 'idx_procesado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_gateway_eventos_raw');
    }
};
