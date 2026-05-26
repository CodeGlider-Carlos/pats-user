<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_preordenes_farmacia_det', function (Blueprint $table) {
            $table->bigIncrements('id_det');
            $table->unsignedBigInteger('id_preorden');
            $table->bigInteger('id_medicamento')->nullable();
            $table->string('medicamento');
            $table->decimal('cantidad', 10, 2)->default(1.00);
            $table->dateTime('creado_en')->useCurrent();

            $table->index('id_preorden', 'idx_ppfd_preorden');
            $table->index('id_medicamento', 'idx_ppfd_medicamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_preordenes_farmacia_det');
    }
};
