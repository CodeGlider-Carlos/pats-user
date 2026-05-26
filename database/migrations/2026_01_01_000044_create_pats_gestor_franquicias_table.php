<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_gestor_franquicias', function (Blueprint $table) {
            $table->increments('id_relacion');
            $table->integer('id_gestor');
            $table->integer('id_franquicia');
            $table->boolean('activo')->default(1);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('user_alta')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_gestor', 'id_franquicia', 'activo'], 'uq_gestor_franquicia_activa');
            $table->index('id_franquicia', 'idx_franquicia');
            $table->index('id_gestor', 'idx_gestor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_gestor_franquicias');
    }
};
