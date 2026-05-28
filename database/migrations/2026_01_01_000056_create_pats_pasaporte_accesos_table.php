<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasaporte_accesos', function (Blueprint $table) {
            $table->bigIncrements('id_acceso');
            $table->unsignedBigInteger('id_pasaporte')->default(0);
            $table->unsignedBigInteger('id_alta')->default(0);
            $table->unsignedBigInteger('id_orden')->default(0);
            $table->string('tipo_acceso', 60)->default('PACIENTE');
            $table->string('correo_usuario', 180);
            $table->string('telefono_usuario', 30)->nullable();
            $table->string('nombre_usuario')->nullable();
            $table->string('nombre_paciente')->nullable();
            $table->string('password_hash');
            $table->boolean('password_temporal')->default(1);
            $table->boolean('debe_cambiar_password')->default(1);
            $table->string('token_reset', 160)->nullable();
            $table->dateTime('token_reset_expira')->nullable();
            $table->dateTime('ultimo_login')->nullable();
            $table->integer('intentos_fallidos')->default(0);
            $table->dateTime('bloqueado_hasta')->nullable();
            $table->string('estatus', 60)->default('ACTIVO');
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('id_pasaporte', 'idx_ppacc_id_pasaporte');
            $table->index('id_alta', 'idx_ppacc_id_alta');
            $table->index('id_orden', 'idx_ppacc_id_orden');
            $table->index('correo_usuario', 'idx_ppacc_correo_usuario');
            $table->index('tipo_acceso', 'idx_ppacc_tipo_acceso');
            $table->index('estatus', 'idx_ppacc_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasaporte_accesos');
    }
};
