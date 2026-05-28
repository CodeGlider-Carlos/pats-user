<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_movimientos_financieros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 50);
            $table->unsignedBigInteger('id_relacionado');
            $table->unsignedBigInteger('id_pasaporte')->nullable();
            $table->decimal('monto', 12, 2);
            $table->string('tipo_movimiento', 50);
            $table->string('referencia', 120)->nullable();
            $table->string('estatus', 50)->default('pendiente');
            $table->dateTime('fecha_generado');
            $table->string('evidencia')->nullable();
            $table->string('pais')->nullable();
            $table->string('region')->nullable();
            $table->string('zona')->nullable();
            $table->string('unidad')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_movimiento')->nullable();
            $table->string('origen_tabla', 120)->nullable();
            $table->unsignedBigInteger('origen_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('tipo', 'idx_pats_mov_fin_tipo');
            $table->index('id_relacionado', 'idx_pats_mov_fin_relacionado');
            $table->index('id_pasaporte', 'idx_pats_mov_fin_pasaporte');
            $table->index('estatus', 'idx_pats_mov_fin_estatus');
            $table->index('fecha_generado', 'idx_pats_mov_fin_fecha');
            $table->index('region', 'idx_pats_mov_fin_region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_movimientos_financieros');
    }
};
