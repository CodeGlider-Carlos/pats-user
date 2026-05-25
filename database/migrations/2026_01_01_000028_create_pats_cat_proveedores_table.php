<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_cat_proveedores', function (Blueprint $table) {
            $table->string('pais', 80)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('id_proveedor', 50)->nullable();
            $table->string('id_registro', 36)->nullable();
            $table->string('categoria', 80)->nullable();
            $table->string('nombre_unidad', 160)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('direccion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->string('imagen_file', 180)->nullable();
            $table->string('imagen_mime', 120)->nullable();
            $table->integer('imagen_size')->nullable();
            $table->dateTime('imagen_uploaded_at')->nullable();
            $table->boolean('activo')->nullable()->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('usuario_registro', 120)->nullable();
            $table->string('usuario_actualizo', 120)->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_cat_proveedores');
    }
};
