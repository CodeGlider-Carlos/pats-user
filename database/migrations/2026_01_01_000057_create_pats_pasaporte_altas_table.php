<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasaporte_altas', function (Blueprint $table) {
            $table->bigIncrements('id_alta');
            $table->unsignedBigInteger('id_pasaporte')->default(0);
            $table->unsignedBigInteger('id_orden')->default(0);
            $table->string('referencia_pago', 120)->nullable();
            $table->string('stripe_payment_intent_id', 120)->nullable();
            $table->string('stripe_charge_id', 120)->nullable();
            $table->string('token_publico', 160)->nullable();
            $table->string('actor_tipo_publico', 40)->nullable()->default('ADMINPATS');
            $table->string('tipo_origen', 40)->nullable()->default('ADMINPATS');
            $table->string('origen_checkout', 60)->nullable()->default('PORTAL_PUBLICO');
            $table->unsignedBigInteger('id_franquicia')->default(0);
            $table->unsignedBigInteger('id_distribuidor')->default(0);
            $table->unsignedBigInteger('id_gestor')->default(0);
            $table->string('tipo_paciente', 60)->nullable()->default('ADULTO');
            $table->string('modo_firma', 60)->nullable()->default('FIRMA_PROPIA');
            $table->boolean('requiere_responsable')->default(0);
            $table->string('tipo_representacion', 80)->nullable();
            $table->string('relacion_responsable_paciente', 120)->nullable();
            $table->string('motivo_responsable', 180)->nullable();
            $table->string('nacionalidad_tipo', 40)->nullable()->default('MEXICANA');
            $table->boolean('paciente_es_menor')->default(0);
            $table->boolean('paciente_es_adulto_mayor')->default(0);
            $table->longText('datos_paciente_json')->nullable();
            $table->longText('datos_responsable_json')->nullable();
            $table->longText('domicilio_json')->nullable();
            $table->longText('adulto_mayor_json')->nullable();
            $table->longText('origen_comercial_json')->nullable();
            $table->longText('stripe_json')->nullable();
            $table->longText('payload_formulario_json')->nullable();
            $table->string('ip_registro', 80)->nullable();
            $table->string('user_agent_registro')->nullable();
            $table->string('estatus', 60)->default('PAGO_CONFIRMADO');
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('id_pasaporte', 'idx_ppa_id_pasaporte');
            $table->index('id_orden', 'idx_ppa_id_orden');
            $table->index('referencia_pago', 'idx_ppa_referencia_pago');
            $table->index('stripe_payment_intent_id', 'idx_ppa_stripe_payment_intent_id');
            $table->index('tipo_paciente', 'idx_ppa_tipo_paciente');
            $table->index('modo_firma', 'idx_ppa_modo_firma');
            $table->index('actor_tipo_publico', 'idx_ppa_actor_tipo_publico');
            $table->index('created_at', 'idx_ppa_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasaporte_altas');
    }
};
