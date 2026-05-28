<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_ventas_medicamentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pats')->nullable();
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('curp', 18)->nullable();
            $table->integer('id_expediente');
            $table->integer('id_episodio');
            $table->string('id_misional', 30)->nullable();
            $table->string('documento', 80)->nullable();
            $table->integer('version_nota')->nullable();
            $table->dateTime('fecha_venta');
            $table->integer('usuario_id')->nullable();
            $table->string('usuario', 120)->nullable();
            $table->decimal('total', 12, 2)->nullable()->default(0.00);
            $table->text('observaciones')->nullable();
            $table->dateTime('created_at');

            $table->index(['id_expediente', 'id_episodio'], 'idx_exp_epi');
            $table->index('curp', 'idx_curp');
            $table->index('fecha_venta', 'idx_fecha');
            $table->index('id_misional', 'idx_misional');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_ventas_medicamentos');
    }
};
