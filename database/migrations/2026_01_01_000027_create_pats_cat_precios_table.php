<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_cat_precios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo');
            $table->string('modalidad')->nullable();
            $table->decimal('precio', 12, 2);
            $table->decimal('comision_franquicia', 12, 2)->default(0.00);
            $table->decimal('comision_distribuidor', 12, 2)->default(0.00);
            $table->decimal('comision_admin', 12, 2)->default(0.00);
            $table->decimal('ingreso_unidad', 12, 2)->default(0.00);
            $table->string('ambito_region', 50)->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['tipo', 'modalidad', 'ambito_region'], 'uq_pats_cat_precios_tipo_modalidad_ambito');
            $table->index('tipo', 'idx_pats_cat_precios_tipo');
            $table->index('modalidad', 'idx_pats_cat_precios_modalidad');
            $table->index('ambito_region', 'idx_pats_cat_precios_ambito');
            $table->index('activo', 'idx_pats_cat_precios_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_cat_precios');
    }
};
