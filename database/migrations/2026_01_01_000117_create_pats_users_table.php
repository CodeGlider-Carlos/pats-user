<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app', 60)->default('PATS');
            $table->string('rolapp', 40);
            $table->string('rol', 30);
            $table->enum('tipo_actor', ['ADMIND', 'FRANQUICIATARIO', 'DISTRIBUIDOR', 'MEDICO', 'LAB', 'RX', 'FAV'])->default('ADMIND');
            $table->integer('id_actor')->nullable();
            $table->string('nombre', 180);
            $table->string('usuario', 120);
            $table->string('correo', 180)->nullable();
            $table->string('contrasena');
            $table->string('region', 120)->nullable();
            $table->string('acroregion', 20)->nullable();
            $table->string('unidad', 120)->nullable();
            $table->string('acronu', 20)->nullable();
            $table->date('vigente')->nullable();
            $table->boolean('activo')->default(1);
            $table->boolean('must_change_password')->default(1);
            $table->dateTime('password_last_change')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->dateTime('locked_until')->nullable();
            $table->string('perfil', 120)->nullable();
            $table->string('ced', 120)->nullable();
            $table->string('telefono', 60)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('usuario', 'uq_pats_users_usuario');
            $table->unique('correo', 'uq_pats_users_correo');
            $table->index('app', 'idx_pats_users_app');
            $table->index('rolapp', 'idx_pats_users_rolapp');
            $table->index('rol', 'idx_pats_users_rol');
            $table->index('tipo_actor', 'idx_pats_users_tipo_actor');
            $table->index(['tipo_actor', 'id_actor'], 'idx_pats_users_actor');
            $table->index(['region', 'acroregion', 'unidad', 'acronu'], 'idx_pats_users_scope');
            $table->index('activo', 'idx_pats_users_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_users');
    }
};
