<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_bot_ops_knowledge', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 80);
            $table->string('id_misional', 40)->nullable();
            $table->string('id_modelo', 40)->nullable();
            $table->string('proceso', 180);
            $table->string('fase', 180)->nullable();
            $table->string('tipo_caso', 100);
            $table->string('subtipo_caso', 140)->nullable();
            $table->string('pregunta_operativa');
            $table->text('situacion_usuario')->nullable();
            $table->text('respuesta_usuario_sugerida');
            $table->mediumText('pasos_operativos');
            $table->mediumText('validaciones')->nullable();
            $table->string('modulos_consultar')->nullable();
            $table->mediumText('cuando_escalar')->nullable();
            $table->mediumText('frases_sugeridas')->nullable();
            $table->mediumText('errores_evitar')->nullable();
            $table->string('roles_aplica', 180)->default('CON,CONCIERGE,ADM,ADMISION,CAJ,CAJA,ADMIN,ADMINPATS');
            $table->enum('nivel', ['BASICO', 'OPERATIVO', 'CRITICO'])->default('OPERATIVO');
            $table->integer('prioridad')->default(10);
            $table->text('keywords')->nullable();
            $table->string('fuente', 120)->default('PROCESOS_OPERATIVOS_PATS');
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique('codigo', 'uk_codigo');
            $table->index('id_misional', 'idx_id_misional');
            $table->index('id_modelo', 'idx_id_modelo');
            $table->index('tipo_caso', 'idx_tipo_caso');
            $table->index('roles_aplica', 'idx_roles');
            $table->index('activo', 'idx_activo');
            $table->index('prioridad', 'idx_prioridad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_bot_ops_knowledge');
    }
};
