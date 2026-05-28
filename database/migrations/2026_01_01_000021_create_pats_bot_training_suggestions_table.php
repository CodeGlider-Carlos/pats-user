<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_training_suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('queue_id')->nullable();
            $table->enum('tipo_destino', ['MANUAL', 'OPERATIVO', 'SINONIMO', 'REGLA'])->default('MANUAL');
            $table->string('categoria', 100)->nullable();
            $table->string('subcategoria', 140)->nullable();
            $table->string('intent', 80)->nullable();
            $table->string('pregunta_base');
            $table->mediumText('respuesta_sugerida');
            $table->mediumText('respuesta_usuario_sugerida')->nullable();
            $table->mediumText('pasos_operativos')->nullable();
            $table->mediumText('validaciones')->nullable();
            $table->string('modulos_consultar')->nullable();
            $table->mediumText('cuando_escalar')->nullable();
            $table->text('keywords')->nullable();
            $table->enum('estado', ['BORRADOR', 'APROBADA', 'RECHAZADA', 'PUBLICADA'])->default('BORRADOR');
            $table->string('creado_por', 120)->nullable();
            $table->string('aprobado_por', 120)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('queue_id', 'idx_queue_id');
            $table->index('tipo_destino', 'idx_tipo_destino');
            $table->index('estado', 'idx_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_training_suggestions');
    }
};
