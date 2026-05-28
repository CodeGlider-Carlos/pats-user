<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('usuario', 120)->nullable();
            $table->string('rol', 60)->nullable();
            $table->string('region', 40)->nullable();
            $table->string('unidad', 40)->nullable();
            $table->text('pregunta');
            $table->mediumText('respuesta')->nullable();
            $table->string('intent_detectado', 80)->nullable();
            $table->unsignedBigInteger('knowledge_id')->nullable();
            $table->decimal('score', 10, 4)->nullable();
            $table->string('origen_respuesta', 80)->nullable();
            $table->string('ip', 80)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('user_id', 'idx_user_id');
            $table->index('usuario', 'idx_usuario');
            $table->index('intent_detectado', 'idx_intent');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_logs');
    }
};
