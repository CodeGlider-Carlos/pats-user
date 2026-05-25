<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_distribuidor_documentos', function (Blueprint $table) {
            $table->increments('id_documento_solicitud');
            $table->integer('id_solicitud');
            $table->string('tipo_documento', 60);
            $table->string('archivo_path');
            $table->string('archivo_nombre_original');
            $table->string('mime_type', 120)->nullable();
            $table->integer('size_kb')->default(0);
            $table->boolean('vigente')->default(1);
            $table->text('observaciones')->nullable();
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_solicitud', 'idx_psdd_solicitud');
            $table->index('tipo_documento', 'idx_psdd_tipo');
            $table->index('vigente', 'idx_psdd_vigente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor_documentos');
    }
};
