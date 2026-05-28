<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_intents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('intent', 80);
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->text('keywords')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique('intent', 'uk_intent');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_intents');
    }
};
