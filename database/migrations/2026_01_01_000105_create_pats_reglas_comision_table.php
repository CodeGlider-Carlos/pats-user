<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_reglas_comision', function (Blueprint $table) {
            $table->bigIncrements('id_regla');
            $table->string('tipo_operacion', 100);
            $table->string('subtipo_operacion', 100)->nullable();
            $table->string('modalidad_pago', 50)->nullable();
            $table->string('ambito_region', 50)->nullable();
            $table->string('beneficiario', 100);
            $table->string('tipo_calculo', 50);
            $table->decimal('valor_calculo', 12, 2);
            $table->string('moneda', 10)->default('MXN');
            $table->integer('orden_aplicacion')->default(1);
            $table->boolean('activo')->default(1);
            $table->date('vigencia_ini');
            $table->date('vigencia_fin')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('tipo_operacion', 'idx_pats_reglas_tipo_operacion');
            $table->index('modalidad_pago', 'idx_pats_reglas_modalidad');
            $table->index('beneficiario', 'idx_pats_reglas_beneficiario');
            $table->index('ambito_region', 'idx_pats_reglas_ambito');
            $table->index('activo', 'idx_pats_reglas_activo');
            $table->index(['vigencia_ini', 'vigencia_fin'], 'idx_pats_reglas_vigencia');
            $table->index(['tipo_operacion', 'beneficiario'], 'idx_pats_reglas_tipo_beneficiario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_reglas_comision');
    }
};
