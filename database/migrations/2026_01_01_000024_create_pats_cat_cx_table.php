<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_cat_cx', function (Blueprint $table) {
            $table->string('id_registro', 36)->nullable();
            $table->string('id_proveedor', 50)->nullable();
            $table->string('especialidad', 120)->nullable();
            $table->string('procedimiento')->nullable();
            $table->decimal('precio_nopats', 12, 2)->nullable();
            $table->decimal('descuento', 8, 2)->nullable();
            $table->decimal('precio_pats', 12, 2)->nullable();
            $table->boolean('activo')->nullable()->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('usuario_registro', 120)->nullable();
            $table->string('usuario_actualizo', 120)->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_cat_cx');
    }
};
