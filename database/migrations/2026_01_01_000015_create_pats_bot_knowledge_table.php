<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_knowledge', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('categoria', 100);
            $table->string('subcategoria', 140)->nullable();
            $table->string('intent', 80)->nullable();
            $table->string('pregunta_base');
            $table->text('respuesta');
            $table->text('nota_operativa')->nullable();
            $table->boolean('requiere_revision')->default(0);
            $table->text('observacion_revision')->nullable();
            $table->enum('nivel_confianza', ['ALTA', 'MEDIA', 'BAJA'])->default('ALTA');
            $table->text('keywords')->nullable();
            $table->string('respuesta_corta', 500)->nullable();
            $table->boolean('requiere_login')->default(0);
            $table->boolean('requiere_datos_usuario')->default(0);
            $table->integer('prioridad')->default(10);
            $table->boolean('activo')->default(1);
            $table->string('fuente', 120)->nullable()->default('MANUAL_PATS');
            $table->string('version_conocimiento', 40)->nullable()->default('PATS_MANUAL_2026');
            $table->enum('canal', ['GENERAL', 'USUARIO', 'CONCIERGE', 'ADMISION', 'SOPORTE'])->default('GENERAL');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('categoria', 'idx_categoria');
            $table->index('intent', 'idx_intent');
            $table->index('activo', 'idx_activo');
            $table->index('prioridad', 'idx_prioridad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_knowledge');
    }
};
