<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_tokens_recuperacion', function (Blueprint $table) {
            $table->bigIncrements('id_token');
            $table->string('token_hash', 128);
            $table->boolean('usado')->default(0);
            $table->dateTime('expira_at');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('usado_at')->nullable();
            $table->string('ip_creacion', 80)->nullable();
            $table->string('ip_uso', 80)->nullable();

            $table->index('token_hash', 'idx_token_hash');
            $table->index('usado', 'idx_usado');
            $table->index('expira_at', 'idx_expira');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_tokens_recuperacion');
    }
};
