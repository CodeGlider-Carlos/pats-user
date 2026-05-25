<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_lotes_pago_comision_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_detalle');
            $table->unsignedBigInteger('id_lote_pago');
            $table->unsignedBigInteger('id_comision');
            $table->decimal('monto_aplicado', 14, 2)->default(0.00);
            $table->text('observaciones')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['id_lote_pago', 'id_comision'], 'uq_pats_lote_pago_detalle');
            $table->index('id_lote_pago', 'idx_pats_lote_pago_detalle_lote');
            $table->index('id_comision', 'idx_pats_lote_pago_detalle_comision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_lotes_pago_comision_detalle');
    }
};
