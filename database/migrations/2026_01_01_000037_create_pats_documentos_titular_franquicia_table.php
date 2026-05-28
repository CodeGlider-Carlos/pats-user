<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_documentos_titular_franquicia', function (Blueprint $table) {
            $table->increments('id_documento');
            $table->integer('id_titular');
            $table->string('tipo_documento', 60);
            $table->string('archivo_path');
            $table->string('archivo_nombre_original')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->integer('size_kb')->nullable();
            $table->boolean('vigente')->default(1);
            $table->text('observaciones')->nullable();
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_titular', 'idx_titular');
            $table->index(['id_titular', 'tipo_documento'], 'idx_tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_documentos_titular_franquicia');
    }
};
