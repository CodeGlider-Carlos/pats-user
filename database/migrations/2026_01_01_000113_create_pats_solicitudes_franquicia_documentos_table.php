<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_franquicia_documentos', function (Blueprint $table) {
            $table->increments('id_documento_solicitud');
            $table->integer('id_solicitud');
            $table->string('tipo_documento', 80);
            $table->string('origen_documento', 40)->nullable()->default('CARGA');
            $table->string('archivo_path');
            $table->string('archivo_nombre_original')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->integer('size_kb')->default(0);
            $table->string('hash_archivo', 128)->nullable();
            $table->boolean('vigente')->default(1);
            $table->text('observaciones')->nullable();
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('id_solicitud', 'idx_psfd_id_solicitud');
            $table->index('tipo_documento', 'idx_psfd_tipo_documento');
            $table->index('origen_documento', 'idx_psfd_origen_documento');
            $table->index('vigente', 'idx_psfd_vigente');
            $table->index('hash_archivo', 'idx_psfd_hash_archivo');
            $table->index('created_at', 'idx_psfd_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_franquicia_documentos');
    }
};
