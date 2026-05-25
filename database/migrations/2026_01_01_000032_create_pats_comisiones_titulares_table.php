<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_comisiones_titulares', function (Blueprint $table) {
            $table->increments('id_comision_titular');
            $table->integer('id_franquicia');
            $table->integer('id_titular');
            $table->string('origen_tipo', 40);
            $table->integer('origen_id')->nullable();
            $table->integer('periodo_anio')->nullable();
            $table->integer('periodo_mes')->nullable();
            $table->decimal('base_comision_franquicia', 14, 2)->default(0.00);
            $table->decimal('porcentaje_participacion', 7, 4)->default(0.0000);
            $table->decimal('monto_comision_titular', 14, 2)->default(0.00);
            $table->string('estatus', 30)->default('GENERADA');
            $table->dateTime('fecha_calculo')->useCurrent();
            $table->dateTime('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_franquicia', 'idx_franquicia');
            $table->index('id_titular', 'idx_titular');
            $table->index(['origen_tipo', 'origen_id'], 'idx_origen');
            $table->index(['periodo_anio', 'periodo_mes'], 'idx_periodo');
            $table->index('estatus', 'idx_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_comisiones_titulares');
    }
};
