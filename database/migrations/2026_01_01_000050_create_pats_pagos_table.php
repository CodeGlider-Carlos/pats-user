<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pagos', function (Blueprint $table) {
            $table->increments('id_pago');
            $table->string('tipo_solicitud', 40);
            $table->unsignedInteger('id_solicitud');
            $table->string('pasarela', 40)->default('manual');
            $table->string('referencia_pasarela', 150);
            $table->string('estatus', 30)->default('pending');
            $table->decimal('monto', 14, 2)->default(0.00);
            $table->char('moneda', 3)->default('MXN');
            $table->json('metadata_json')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['pasarela', 'referencia_pasarela'], 'uq_pasarela_referencia');
            $table->index(['tipo_solicitud', 'id_solicitud'], 'idx_tipo_solicitud');
            $table->index('estatus', 'idx_estatus');
            $table->index('created_at', 'idx_fecha_creacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pagos');
    }
};
