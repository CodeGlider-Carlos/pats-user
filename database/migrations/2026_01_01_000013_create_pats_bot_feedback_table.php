<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('log_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('usuario', 120)->nullable();
            $table->enum('valor', ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO']);
            $table->text('comentario')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('log_id', 'idx_log_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('valor', 'idx_valor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_feedback');
    }
};
