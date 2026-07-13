<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prosa_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('correo_usuario')->index();
            $table->string('registration_id')->unique();
            $table->string('card_brand', 30)->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('frecuencia', 10);            // MENSUAL | ANUAL
            $table->unsignedTinyInteger('meses')->default(1);
            $table->unsignedInteger('id_tipo_precio');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('MXN');
            $table->string('status', 20)->default('active'); // active | suspended | cancelled
            $table->unsignedTinyInteger('failure_count')->default(0);
            $table->timestamp('last_charged_at')->nullable();
            $table->timestamp('next_charge_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prosa_subscriptions');
    }
};
