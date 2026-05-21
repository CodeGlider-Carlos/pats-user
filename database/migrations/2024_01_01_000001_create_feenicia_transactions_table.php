<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feenicia_transactions', function (Blueprint $table) {

            $table->id();

            // ── Relación con tu modelo de negocio ──────────────────
            // Ajusta según tu sistema: puede ser order_id, payment_id, etc.
            $table->nullableMorphs('payable'); // payable_type + payable_id

            // ── Tipo de operación ───────────────────────────────────
            $table->enum('type', [
                'ONE_STEP_SALE',
                'CASH_SALE',
                'RECURRING',
                'REFUND',
                'CANCELLATION',
                'REVERSAL',
            ]);

            // ── Estado de la transacción ────────────────────────────
            $table->enum('status', [
                'approved',
                'rejected',
                'timeout',
                'reversed',
                'refunded',
                'cancelled',
            ])->default('rejected');

            // ── Datos de Feenicia (guardados tras respuesta exitosa) ─
            $table->string('order_id')->nullable();          // orderId de Feenicia
            $table->string('transaction_id')->nullable();    // transactionId único
            $table->string('authnum', 20)->nullable();       // número de autorización
            $table->string('affiliation', 15);
            $table->string('merchant', 16);
            $table->decimal('amount', 12, 2);
            $table->decimal('tip', 10, 2)->default(0);
            $table->string('response_code', 10)->nullable(); // '00', 'A006', etc.
            $table->boolean('approved')->default(false);

            // ── Datos de la tarjeta (solo info no sensible) ─────────
            $table->string('card_brand', 20)->nullable();    // VISA, MASTER CARD
            $table->string('card_product', 10)->nullable();  // DEBITO, CREDIT
            $table->string('card_last4', 4)->nullable();
            $table->string('card_first6', 6)->nullable();

            // ── Banco ───────────────────────────────────────────────
            $table->string('issuer_bank')->nullable();
            $table->string('acquirer_bank')->nullable();

            // ── Moneda ──────────────────────────────────────────────
            $table->integer('currency_id')->nullable();       // 484 = MXN
            $table->string('currency_description')->nullable();

            // ── MSI (diferimiento) ──────────────────────────────────
            $table->string('msi_payments')->nullable();       // 03, 06, 12...
            $table->string('msi_plan_type')->nullable();      // 00, 03, 05, 07

            // ── Respuesta completa (para debugging y auditoría) ─────
            $table->json('feenicia_response')->nullable();

            // ── Referencia cruzada para post-venta ──────────────────
            // La transacción de refund/cancel/reversal apunta a la venta original
            $table->foreignId('original_transaction_id')
                  ->nullable()
                  ->constrained('feenicia_transactions')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // ── Índices ─────────────────────────────────────────────
            $table->index('transaction_id');
            $table->index('order_id');
            $table->index('authnum');
            $table->index('status');
            $table->index('type');
            $table->index('affiliation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feenicia_transactions');
    }
};
