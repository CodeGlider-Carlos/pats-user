<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_lotes_pago_comision', function (Blueprint $table) {
            $table->bigIncrements('id_lote_pago');
            $table->string('beneficiario_tipo', 50);
            $table->unsignedBigInteger('beneficiario_id')->nullable();
            $table->dateTime('fecha_solicitud')->nullable();
            $table->dateTime('fecha_revision')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->decimal('monto_total', 14, 2)->default(0.00);
            $table->string('estatus', 50)->default('SOLICITADO');
            $table->string('referencia_pago', 120)->nullable();
            $table->string('evidencia_pago_path')->nullable();
            $table->string('evidencia_pago_nombre_original')->nullable();
            $table->string('evidencia_pago_mime', 120)->nullable();
            $table->integer('evidencia_pago_size_kb')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('usuario_solicita', 120)->nullable();
            $table->string('usuario_revisa', 120)->nullable();
            $table->string('usuario_paga', 120)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['beneficiario_tipo', 'beneficiario_id'], 'idx_pats_lotes_pago_comision_benef');
            $table->index('estatus', 'idx_pats_lotes_pago_comision_estatus');
            $table->index('fecha_pago', 'idx_pats_lotes_pago_comision_fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_lotes_pago_comision');
    }
};
