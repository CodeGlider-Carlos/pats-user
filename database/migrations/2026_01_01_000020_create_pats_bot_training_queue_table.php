<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_training_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('log_id')->nullable();
            $table->text('pregunta');
            $table->mediumText('respuesta_actual')->nullable();
            $table->enum('motivo', ['SIN_RESULTADO', 'BAJA_CONFIANZA', 'INCORRECTO', 'CONFUSO', 'NO_UTIL', 'REPETIDA']);
            $table->string('intent_detectado', 80)->nullable();
            $table->string('source', 80)->nullable();
            $table->decimal('score', 10, 4)->nullable();
            $table->integer('veces_detectada')->default(1);
            $table->enum('estado', ['PENDIENTE', 'EN_REVISION', 'APROBADO', 'DESCARTADO', 'RESUELTO'])->default('PENDIENTE');
            $table->string('revisado_por', 120)->nullable();
            $table->text('comentario_revision')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('log_id', 'idx_log_id');
            $table->index('motivo', 'idx_motivo');
            $table->index('estado', 'idx_estado');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_training_queue');
    }
};
