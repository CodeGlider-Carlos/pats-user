<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_medicamentos_pats', function (Blueprint $table) {
            $table->increments('id_medicamento');
            $table->integer('idpats')->nullable();
            $table->string('region', 80)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('medicamento');
            $table->decimal('precio', 12, 2)->default(0.00);
            $table->boolean('activo')->default(1);

            $table->index('idpats', 'idx_idpats');
            $table->index('region', 'idx_region');
            $table->index('unidad', 'idx_unidad');
            $table->index('medicamento', 'idx_medicamento');
            $table->index('activo', 'idx_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_medicamentos_pats');
    }
};
