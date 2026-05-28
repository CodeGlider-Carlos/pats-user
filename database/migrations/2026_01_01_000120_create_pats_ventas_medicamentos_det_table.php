<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_ventas_medicamentos_det', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_venta');
            $table->integer('id_medicamento')->nullable();
            $table->string('medicamento');
            $table->decimal('precio', 12, 2)->default(0.00);
            $table->decimal('cantidad', 12, 2)->default(1.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->dateTime('created_at');

            $table->index('id_venta', 'idx_venta');
            $table->index('id_medicamento', 'idx_medicamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_ventas_medicamentos_det');
    }
};
