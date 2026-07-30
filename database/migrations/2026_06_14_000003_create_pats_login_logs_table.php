<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_acceso')->nullable()->index();
            $table->string('correo', 150)->default('')->index();
            $table->string('ip', 45)->default('');
            $table->text('user_agent')->nullable();
            $table->enum('resultado', ['EXITOSO', 'FALLIDO', 'BLOQUEADO', 'NO_ENCONTRADO'])->default('FALLIDO');
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_login_logs');
    }
};
