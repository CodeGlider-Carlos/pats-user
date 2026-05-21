<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pats_solicitudes_distribuidor')) {
            return;
        }

        Schema::create('pats_solicitudes_distribuidor', function (Blueprint $table) {
            // PK con nombre explícito para que las FK hijas puedan referenciarlo
            $table->bigIncrements('id_solicitud');

            // Contexto de origen
            $table->unsignedBigInteger('id_franquicia')->default(0);
            $table->unsignedBigInteger('id_gestor')->default(0);
            $table->unsignedBigInteger('id_distribuidor_generado')->nullable();

            // Usuarios responsables
            $table->unsignedBigInteger('user_solicita')->nullable();
            $table->unsignedBigInteger('user_valida')->nullable();
            $table->unsignedBigInteger('user_autoriza')->nullable();

            // Ubicación
            $table->string('pais', 10);
            $table->string('region', 10);
            $table->string('zona', 120)->nullable();
            $table->string('unidad', 60)->nullable();
            $table->text('direccion');

            // Titular
            $table->string('nombre', 200);
            $table->enum('tipo_persona', ['FISICA', 'MORAL'])->default('FISICA');
            $table->string('razon_social', 250)->nullable();
            $table->string('rfc', 14)->nullable();
            $table->string('telefono', 10);
            $table->string('correo', 180)->index();

            // Bancarios
            $table->string('banco', 100)->nullable();
            $table->string('numero_cuenta', 11)->nullable();
            $table->string('clabe', 18)->nullable();
            $table->string('titular_cuenta', 200)->nullable();

            // Plan financiero
            $table->enum('modalidad_pago', ['CONTADO', 'DIFERIDO'])->default('CONTADO');
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->decimal('enganche', 12, 2)->default(0);
            $table->decimal('saldo_financiado', 12, 2)->default(0);
            $table->unsignedSmallInteger('plazo_meses')->default(0);
            $table->enum('periodicidad', ['MENSUAL', 'QUINCENAL', 'SEMANAL', 'UNICA'])->default('MENSUAL');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_primer_vencimiento')->nullable();
            $table->json('esquema_pagos_json')->nullable();

            // Contrato
            $table->string('contrato_admin_path', 500)->nullable();
            $table->string('contrato_firmado_path', 500)->nullable();

            // Flujo de estatus
            $table->enum('estatus', [
                'ENVIADA',
                'EN_REVISION',
                'APROBADA',
                'RECHAZADA',
                'CONTRATO_ENVIADO',
                'CONTRATO_RECIBIDO',
                'CONVERTIDA_ALTA',
            ])->default('ENVIADA')->index();

            $table->text('motivo_rechazo')->nullable();
            $table->text('observaciones_admin')->nullable();
            $table->text('observaciones_franquicia')->nullable();

            // Fechas de hitos
            $table->timestamp('fecha_envio_contrato')->nullable();
            $table->timestamp('fecha_carga_firmado')->nullable();
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->timestamp('fecha_conversion_alta')->nullable();

            $table->tinyInteger('activo')->default(1)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor');
    }
};
