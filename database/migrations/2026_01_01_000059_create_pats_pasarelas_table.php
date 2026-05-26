<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasarelas', function (Blueprint $table) {
            $table->increments('id_pasarela');
            $table->string('nombre_pasarela', 80);
            $table->unsignedInteger('intervalo_meses')->default(1);
            $table->decimal('monto_minimo', 14, 2)->default(0.00);
            $table->decimal('porcentaje_comision', 8, 4)->default(0.0000);
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['nombre_pasarela', 'intervalo_meses'], 'uq_pasarela_intervalo');
            $table->index(['nombre_pasarela', 'activo'], 'idx_pasarela_activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasarelas');
    }
};
