<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_franquicia_titulares', function (Blueprint $table) {
            $table->increments('id_titular');
            $table->integer('id_franquicia');
            $table->string('nombre_titular', 180);
            $table->string('razon_social', 180)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 180)->nullable();
            $table->text('direccion')->nullable();
            $table->decimal('porcentaje_participacion', 7, 4)->default(0.0000);
            $table->decimal('monto_aportado', 14, 2)->default(0.00);
            $table->boolean('es_titular_principal')->default(0);
            $table->boolean('tiene_acceso')->default(0);
            $table->integer('id_user_pats')->nullable();
            $table->string('banco', 120)->nullable();
            $table->string('numero_cuenta', 60)->nullable();
            $table->string('clabe', 30)->nullable();
            $table->string('titular_cuenta', 180)->nullable();
            $table->boolean('activo')->default(1);
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_franquicia', 'idx_franquicia');
            $table->index('id_user_pats', 'idx_user_pats');
            $table->index(['id_franquicia', 'es_titular_principal'], 'idx_principal');
            $table->index(['id_franquicia', 'tiene_acceso'], 'idx_acceso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_franquicia_titulares');
    }
};
