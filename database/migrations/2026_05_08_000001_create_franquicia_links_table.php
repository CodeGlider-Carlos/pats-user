<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('franquicia_links')) {
            return;
        }
        Schema::create('franquicia_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 36)->unique();
            $table->string('password');
            $table->text('prefill_json')->nullable();
            $table->unsignedBigInteger('id_solicitud')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franquicia_links');
    }
};
