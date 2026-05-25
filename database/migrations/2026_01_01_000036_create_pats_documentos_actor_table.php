<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_documentos_actor', function (Blueprint $table) {
            $table->bigIncrements('id_documento');
            $table->string('actor_tipo', 50);
            $table->unsignedBigInteger('actor_id');
            $table->string('tipo_documento', 100);
            $table->string('archivo_path');
            $table->string('archivo_nombre_original')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->integer('size_kb')->nullable();
            $table->boolean('vigente')->default(1);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['actor_tipo', 'actor_id'], 'idx_pats_documentos_actor');
            $table->index('tipo_documento', 'idx_pats_documentos_tipo');
            $table->index('vigente', 'idx_pats_documentos_vigente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_documentos_actor');
    }
};
