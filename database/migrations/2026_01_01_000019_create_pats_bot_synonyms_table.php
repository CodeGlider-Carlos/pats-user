<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_synonyms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('termino_usuario', 180);
            $table->string('termino_normalizado', 180);
            $table->string('intent', 80)->nullable();
            $table->enum('tipo', ['SINONIMO', 'ERROR_ESCRITURA', 'JERGA_OPERATIVA', 'MODULO', 'SERVICIO'])->default('SINONIMO');
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('intent', 'idx_intent');
            $table->index('tipo', 'idx_tipo');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_synonyms');
    }
};
