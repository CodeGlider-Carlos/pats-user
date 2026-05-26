<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_comisiones_generadas', function (Blueprint $table) {
            $table->bigIncrements('id_comision');
            $table->string('tipo_origen', 100);
            $table->unsignedBigInteger('id_origen');
            $table->unsignedBigInteger('id_regla')->nullable();
            $table->string('beneficiario_tipo', 100);
            $table->unsignedBigInteger('beneficiario_id')->nullable();
            $table->unsignedBigInteger('id_contrato_compensado')->nullable();
            $table->decimal('monto_comision', 12, 2);
            $table->decimal('monto_aplicado_deuda', 12, 2)->default(0.00);
            $table->decimal('monto_liberado', 12, 2)->default(0.00);
            $table->string('moneda', 10)->default('MXN');
            $table->dateTime('fecha_generacion');
            $table->dateTime('fecha_pago')->nullable();
            $table->string('referencia_pago', 120)->nullable();
            $table->string('evidencia_pago')->nullable();
            $table->unsignedBigInteger('user_pago')->nullable();
            $table->string('estatus', 50)->default('por_pagar');
            $table->string('estatus_operativo', 30)->default('GENERADA');
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('tipo_origen', 'idx_pats_comisiones_generadas_tipo_origen');
            $table->index('id_origen', 'idx_pats_comisiones_generadas_id_origen');
            $table->index('beneficiario_tipo', 'idx_pats_comisiones_generadas_beneficiario_tipo');
            $table->index('beneficiario_id', 'idx_pats_comisiones_generadas_beneficiario_id');
            $table->index('estatus', 'idx_pats_comisiones_generadas_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_comisiones_generadas');
    }
};
