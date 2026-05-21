<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pats_solicitudes_distribuidor_documentos')) {
            return;
        }

        Schema::create('pats_solicitudes_distribuidor_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud')->index();

            $table->string('tipo_documento', 60); // INE, COMPROBANTE_DOMICILIO, etc.
            $table->string('archivo_path', 500);
            $table->string('archivo_nombre_original', 260)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('size_kb')->nullable();

            $table->tinyInteger('vigente')->default(1)->index();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_alta')->nullable();

            $table->timestamps();

            $table->foreign('id_solicitud')
                ->references('id_solicitud')
                ->on('pats_solicitudes_distribuidor')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor_documentos');
    }
};
