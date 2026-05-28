<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_respaldo', function (Blueprint $table) {
            $table->bigIncrements('id_respaldo');
            $table->string('stripe_payment_intent_id', 120)->nullable();
            $table->string('referencia_pago', 120)->nullable();
            $table->string('folio_orden', 60)->nullable();
            $table->string('estatus_respaldo', 40)->default('PENDIENTE_PAGO');
            $table->unsignedBigInteger('id_franquicia')->default(0);
            $table->unsignedBigInteger('id_distribuidor')->default(0);
            $table->integer('id_tipo_precio')->default(0);
            $table->string('curp')->nullable();
            $table->string('nombres')->nullable();
            $table->string('apellido_pa')->nullable();
            $table->string('apellido_ma')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono', 80)->nullable();
            $table->string('correo', 180)->nullable();
            $table->string('frecuencia_pago', 40)->nullable();
            $table->decimal('monto_orden', 12, 2)->nullable();
            $table->string('moneda', 10)->nullable()->default('MXN');
            $table->string('pais', 100)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('zona', 120)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('tipo_cliente', 50)->nullable();
            $table->string('tipo_origen', 40)->nullable();
            $table->string('actor_tipo_publico', 40)->nullable();
            $table->string('tipo_paciente', 60)->nullable();
            $table->string('modo_firma', 60)->nullable();
            $table->string('nacionalidad_tipo', 40)->nullable();
            $table->string('nombre_firmante')->nullable();
            $table->longText('payload_post_json')->nullable();
            $table->unsignedBigInteger('id_pasaporte_generado')->nullable();
            $table->unsignedBigInteger('id_orden_generado')->nullable();
            $table->boolean('recuperado')->default(0);
            $table->dateTime('recuperado_en')->nullable();
            $table->text('error_proceso')->nullable();
            $table->string('ip_registro', 80)->nullable();
            $table->string('user_agent_registro')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('stripe_payment_intent_id', 'idx_stripe_pi');
            $table->index('correo', 'idx_correo');
            $table->index('curp', 'idx_curp');
            $table->index('estatus_respaldo', 'idx_estatus');
            $table->index('created_at', 'idx_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_respaldo');
    }
};
