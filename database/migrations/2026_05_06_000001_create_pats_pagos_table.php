<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pats_pagos')) {
            return;
        }
        Schema::create('pats_pagos', function (Blueprint $table) {
            $table->id();

            // Polimorfismo ligero: sin FK dura para soportar múltiples tablas de solicitudes
            $table->string('tipo_solicitud', 30)->index();          // distribuidor | franquicia | pats | ...
            $table->unsignedBigInteger('id_solicitud')->index();

            // Pasarela de pago
            $table->string('pasarela', 30)->index();                // stripe | mercadopago | free | manual
            $table->string('referencia_pasarela', 255);             // pi_xxx, MP-xxx, FREE-1, etc.

            $table->string('estatus', 30)->default('succeeded');    // pending | succeeded | failed | refunded | free
            $table->decimal('monto', 12, 2)->default(0);
            $table->string('moneda', 3)->default('MXN');

            // Datos extra de la pasarela (detalles del método de pago, MSI, etc.)
            $table->text('metadata_json')->nullable();

            $table->timestamps();

            // Evita reutilizar la misma transacción en dos solicitudes
            $table->unique(['pasarela', 'referencia_pasarela'], 'uq_pago_pasarela_ref');

            // Para listar todos los pagos de una solicitud rápidamente
            $table->index(['tipo_solicitud', 'id_solicitud'], 'idx_pago_solicitud');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pagos');
    }
};
