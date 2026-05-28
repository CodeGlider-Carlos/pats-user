<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_clientes', function (Blueprint $table) {
            $table->bigIncrements('id_cliente');
            $table->unsignedBigInteger('id_franquicia');
            $table->unsignedBigInteger('id_distribuidor');
            $table->string('pais')->nullable();
            $table->string('region')->nullable();
            $table->string('zona')->nullable();
            $table->string('unidad')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->string('tipo_cliente', 50);
            $table->string('nombre_cliente');
            $table->string('razon_social')->nullable();
            $table->string('contacto_principal')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('rfc')->nullable();
            $table->text('direccion')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->unsignedBigInteger('user_alta')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_franquicia', 'idx_pats_clientes_franquicia');
            $table->index('id_distribuidor', 'idx_pats_clientes_distribuidor');
            $table->index('tipo_cliente', 'idx_pats_clientes_tipo');
            $table->index('pais', 'idx_pats_clientes_pais');
            $table->index('region', 'idx_pats_clientes_region');
            $table->index('zona', 'idx_pats_clientes_zona');
            $table->index('unidad', 'idx_pats_clientes_unidad');
            $table->index('nombre_cliente', 'idx_pats_clientes_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_clientes');
    }
};
