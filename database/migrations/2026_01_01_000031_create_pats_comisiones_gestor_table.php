<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_comisiones_gestor', function (Blueprint $table) {
            $table->increments('id_comision_gestor');
            $table->integer('id_gestor');
            $table->integer('id_franquicia')->nullable();
            $table->string('origen_tipo', 40);
            $table->integer('origen_id')->nullable();
            $table->integer('periodo_anio')->nullable();
            $table->integer('periodo_mes')->nullable();
            $table->decimal('base_calculo', 14, 2)->default(0.00);
            $table->decimal('monto_comision_gestor', 14, 2)->default(0.00);
            $table->string('estatus', 30)->default('GENERADA');
            $table->dateTime('fecha_calculo')->useCurrent();
            $table->dateTime('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_gestor', 'idx_gestor');
            $table->index('id_franquicia', 'idx_franquicia');
            $table->index(['origen_tipo', 'origen_id'], 'idx_origen');
            $table->index(['periodo_anio', 'periodo_mes'], 'idx_periodo');
            $table->index('estatus', 'idx_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_comisiones_gestor');
    }
};
