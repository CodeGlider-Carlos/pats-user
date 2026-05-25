<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_contratos_actor', function (Blueprint $table) {
            $table->bigIncrements('id_contrato');
            $table->string('actor_tipo', 50);
            $table->unsignedBigInteger('actor_id');
            $table->string('tipo_contrato', 100);
            $table->string('numero_contrato', 120)->nullable();
            $table->string('modalidad_pago', 50);
            $table->decimal('valor_total', 14, 2)->default(0.00);
            $table->decimal('enganche', 14, 2)->default(0.00);
            $table->decimal('saldo_financiado', 14, 2)->default(0.00);
            $table->integer('plazo_meses')->default(0);
            $table->string('periodicidad', 50)->default('MENSUAL');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_primer_vencimiento')->nullable();
            $table->date('fecha_firma')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->decimal('tasa_recargo', 8, 2)->default(0.00);
            $table->string('estatus', 50)->default('VIGENTE');
            $table->string('comisiones_tratamiento', 40)->default('PAGO_REAL');
            $table->boolean('comisiones_liberar_completo')->default(0);
            $table->string('comisiones_observacion')->nullable();
            $table->boolean('comision_bancaria_aplica')->default(1);
            $table->decimal('comision_bancaria_monto', 14, 2)->default(2500.00);
            $table->string('comision_bancaria_motivo', 120)->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('numero_contrato', 'uq_pats_contratos_actor_numero');
            $table->index(['actor_tipo', 'actor_id'], 'idx_pats_contratos_actor');
            $table->index('tipo_contrato', 'idx_pats_contratos_tipo');
            $table->index('estatus', 'idx_pats_contratos_estatus');
            $table->index('activo', 'idx_pats_contratos_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_contratos_actor');
    }
};
