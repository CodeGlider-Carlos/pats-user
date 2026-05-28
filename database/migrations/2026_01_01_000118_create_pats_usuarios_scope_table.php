<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_usuarios_scope', function (Blueprint $table) {
            $table->bigIncrements('id_scope');
            $table->unsignedBigInteger('user_id');
            $table->string('rol_pats', 50);
            $table->string('pais')->nullable();
            $table->string('region')->nullable();
            $table->string('zona')->nullable();
            $table->string('unidad')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->unsignedBigInteger('id_franquicia')->nullable();
            $table->unsignedBigInteger('id_distribuidor')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id', 'idx_pats_usuarios_scope_user');
            $table->index('rol_pats', 'idx_pats_usuarios_scope_rol');
            $table->index('region', 'idx_pats_usuarios_scope_region');
            $table->index('unidad', 'idx_pats_usuarios_scope_unidad');
            $table->index('id_franquicia', 'idx_pats_usuarios_scope_franquicia');
            $table->index('id_distribuidor', 'idx_pats_usuarios_scope_distribuidor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_usuarios_scope');
    }
};
