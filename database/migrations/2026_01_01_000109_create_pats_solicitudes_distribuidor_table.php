<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->increments('id_solicitud');
            $table->string('token_referido', 128)->nullable();
            $table->integer('id_franquicia');
            $table->unsignedBigInteger('id_gestor')->default(0);
            $table->integer('id_distribuidor_generado')->nullable();
            $table->integer('user_solicita')->nullable();
            $table->integer('user_valida')->nullable();
            $table->integer('user_autoriza')->nullable();
            $table->string('pais', 120);
            $table->string('region', 120);
            $table->string('zona', 120)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('unidad', 120)->nullable();
            $table->string('nombre', 180);
            $table->string('apellido_paterno', 100)->nullable();
            $table->string('apellido_materno', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('pais_nacimiento', 80)->nullable();
            $table->string('nacionalidad', 80)->nullable();
            $table->string('ocupacion', 120)->nullable();
            $table->enum('tipo_persona', ['FISICA', 'MORAL'])->default('FISICA');
            $table->string('razon_social', 220)->nullable();
            $table->string('rfc', 30)->nullable();
            $table->string('tipo_identificacion', 60)->nullable();
            $table->string('identificacion_emitida_por', 100)->nullable();
            $table->string('numero_identificacion', 60)->nullable();
            $table->enum('beneficiario_directo', ['SI', 'NO'])->nullable();
            $table->string('telefono', 20);
            $table->string('correo', 180);
            $table->text('direccion')->nullable();
            $table->string('banco', 150)->nullable();
            $table->string('numero_cuenta', 20)->nullable();
            $table->string('clabe', 30)->nullable();
            $table->string('routing_number', 9)->nullable();
            $table->string('titular_cuenta', 180)->nullable();
            $table->enum('modalidad_pago', ['CONTADO', 'DIFERIDO'])->default('CONTADO');
            $table->string('tipo_operacion', 120)->nullable();
            $table->string('moneda', 10)->nullable()->default('MXN');
            $table->decimal('valor_total', 12, 2)->default(0.00);
            $table->decimal('enganche', 12, 2)->default(0.00);
            $table->decimal('saldo_financiado', 12, 2)->default(0.00);
            $table->unsignedSmallInteger('plazo_meses')->default(0);
            $table->enum('periodicidad', ['MENSUAL', 'QUINCENAL', 'SEMANAL', 'UNICA'])->default('MENSUAL');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_primer_vencimiento')->nullable();
            $table->longText('esquema_pagos_json')->nullable();
            $table->string('stripe_payment_intent_id', 120)->nullable();
            $table->string('stripe_payment_status', 30)->nullable();
            $table->string('contrato_admin_path')->nullable();
            $table->string('contrato_firmado_path')->nullable();
            $table->string('estatus', 50)->default('BORRADOR');
            $table->text('motivo_rechazo')->nullable();
            $table->text('observaciones_admin')->nullable();
            $table->text('observaciones_franquicia')->nullable();
            $table->dateTime('fecha_envio_contrato')->nullable();
            $table->dateTime('fecha_carga_firmado')->nullable();
            $table->dateTime('fecha_autorizacion')->nullable();
            $table->dateTime('fecha_conversion_alta')->nullable();
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_franquicia', 'idx_psd_franquicia');
            $table->index('estatus', 'idx_psd_estatus');
            $table->index('correo', 'idx_psd_correo');
            $table->index('user_solicita', 'idx_psd_user_solicita');
            $table->index('user_valida', 'idx_psd_user_valida');
            $table->index('user_autoriza', 'idx_psd_user_autoriza');
            $table->index('id_distribuidor_generado', 'idx_psd_distribuidor_generado');
            $table->index(['correo', 'estatus'], 'idx_psd_correo_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor');
    }
};
