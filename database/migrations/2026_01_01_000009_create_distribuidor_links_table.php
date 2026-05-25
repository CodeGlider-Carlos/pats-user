<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribuidor_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 64);
            $table->unsignedBigInteger('id_esquema')->default(0);
            $table->unsignedBigInteger('id_franquicia')->default(0);
            $table->unsignedBigInteger('id_solicitud')->default(0);
            $table->string('password');
            $table->boolean('active')->default(1);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('type_pay', 20)->default('card');
            $table->text('prefill_json')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique('token', 'distribuidor_links_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribuidor_links');
    }
};
