<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prosa_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('registration_id')->nullable()->index();
            $table->string('payment_id')->nullable()->index();
            $table->string('payment_type', 4)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('MXN');
            $table->string('result_code', 20)->nullable();
            $table->string('result_description')->nullable();
            $table->string('brand', 30)->nullable();
            $table->string('last4', 4)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('origen', 40)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prosa_transactions');
    }
};
