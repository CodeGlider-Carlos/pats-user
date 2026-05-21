<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feenicia_tokens', function (Blueprint $table) {
            $table->id();

            // ── Relación con el usuario de tu sistema ──────────
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ── Datos del token ────────────────────────────────
            $table->string('token')->unique();          // token de Balder
            $table->string('alias')->nullable();        // nombre amigable ej: "Mi Visa"
            $table->string('affiliation', 15);

            // ── Info visible de la tarjeta (no sensible) ───────
            $table->string('card_brand', 20)->nullable();   // VISA, MASTER CARD
            $table->string('card_product', 10)->nullable(); // DEBITO, CREDIT
            $table->string('card_last4', 4)->nullable();
            $table->string('card_first6', 6)->nullable();
            $table->string('cardholder_name')->nullable();
            $table->string('exp_date', 4)->nullable();      // MMYY — NO es sensible mostrar

            // ── Estado ────────────────────────────────────────
            $table->enum('status', ['active', 'cancelled', 'expired'])
                  ->default('active');

            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feenicia_tokens');
    }
};
