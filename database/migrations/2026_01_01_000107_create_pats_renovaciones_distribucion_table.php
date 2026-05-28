<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_renovaciones_distribucion', function (Blueprint $table) {
            $table->bigIncrements('id_renovacion');
            $table->unsignedBigInteger('id_distribuidor');
            $table->unsignedBigInteger('id_franquicia');
            $table->date('fecha_inicio_vigencia');
            $table->date('fecha_fin_vigencia');
            $table->decimal('monto_renovacion', 12, 2);
            $table->string('ambito_region', 50)->nullable();
            $table->string('estatus', 100)->default('vigente');
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->string('referencia_pago', 120)->nullable();
            $table->string('evidencia_pago')->nullable();
            $table->unsignedBigInteger('user_confirmo')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_distribuidor', 'idx_pats_renovaciones_distribuidor');
            $table->index('id_franquicia', 'idx_pats_renovaciones_franquicia');
            $table->index('estatus', 'idx_pats_renovaciones_estatus');
            $table->index('fecha_fin_vigencia', 'idx_pats_renovaciones_fin_vigencia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_renovaciones_distribucion');
    }
};
