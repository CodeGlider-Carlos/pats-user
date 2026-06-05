<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prosa_pending_checkouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('merchant_transaction_id')->unique();
            $table->string('payment_id')->nullable()->index();
            $table->string('flow', 40)->index();
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->json('payload')->nullable();
            $table->json('redirect')->nullable();
            $table->string('result_code', 20)->nullable();
            $table->string('result_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prosa_pending_checkouts');
    }
};
