<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasaporte_documentos', function (Blueprint $table) {
            $table->bigIncrements('id_documento');
            $table->unsignedBigInteger('id_pasaporte')->default(0);
            $table->unsignedBigInteger('id_alta')->default(0);
            $table->unsignedBigInteger('id_orden')->default(0);
            $table->string('actor_documento', 40)->default('PACIENTE');
            $table->string('tipo_documento', 100);
            $table->string('etiqueta', 180)->nullable();
            $table->string('archivo_path', 500)->nullable();
            $table->string('archivo_nombre_original')->nullable();
            $table->string('archivo_mime_type', 120)->nullable();
            $table->string('archivo_extension', 20)->nullable();
            $table->unsignedBigInteger('archivo_size_bytes')->default(0);
            $table->boolean('es_obligatorio')->default(0);
            $table->string('estatus', 60)->default('CARGADO');
            $table->longText('metadata_json')->nullable();
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('id_pasaporte', 'idx_ppd_id_pasaporte');
            $table->index('id_alta', 'idx_ppd_id_alta');
            $table->index('id_orden', 'idx_ppd_id_orden');
            $table->index('actor_documento', 'idx_ppd_actor_documento');
            $table->index('tipo_documento', 'idx_ppd_tipo_documento');
            $table->index('estatus', 'idx_ppd_estatus');
            $table->index('created_at', 'idx_ppd_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasaporte_documentos');
    }
};
