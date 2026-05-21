<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feenicia_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_tx', 20);              // SALE, CANCEL, REVERSAL, REFUND
            $table->string('merchant', 16);
            $table->unsignedBigInteger('feenicia_id');  // campo 'id' del webhook
            $table->json('payload');                    // body completo recibido
            $table->boolean('jwt_valid')->default(false);
            $table->boolean('processed')->default(false);
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('tipo_tx');
            $table->index('feenicia_id');
            $table->index('processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feenicia_webhook_logs');
    }
};
