<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rule_key', 100);
            $table->string('nombre', 140);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['CALCULO', 'VALIDACION', 'RESPUESTA_DINAMICA', 'DERIVACION'])->default('RESPUESTA_DINAMICA');
            $table->json('config_json')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique('rule_key', 'uk_rule_key');
            $table->index('tipo', 'idx_tipo');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_rules');
    }
};
