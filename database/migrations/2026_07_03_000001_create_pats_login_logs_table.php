<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_login_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_acceso')->nullable();
            $table->string('correo', 180)->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('resultado', 20);
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->index('id_acceso', 'idx_login_logs_id_acceso');
            $table->index('resultado', 'idx_login_logs_resultado');
            $table->index('created_at', 'idx_login_logs_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_login_logs');
    }
};
