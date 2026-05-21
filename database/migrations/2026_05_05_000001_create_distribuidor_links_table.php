<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('distribuidor_links')) {
            return;
        }
        Schema::create('distribuidor_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('id_esquema')->default(0);
            $table->unsignedBigInteger('id_franquicia')->default(0);
            $table->unsignedBigInteger('id_solicitud')->default(0);
            $table->string('password', 255);          // bcrypt hash
            $table->boolean('active')->default(true);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('type_pay', 20)->default('card'); // card | free
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribuidor_links');
    }
};
