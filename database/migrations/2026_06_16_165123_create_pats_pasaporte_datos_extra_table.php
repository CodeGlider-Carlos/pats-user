<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasaporte_datos_extra', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('id_pasaporte')->unique();
            $table->string('rfc', 13)->default('');
            $table->string('estado_civil', 30)->default('');
            $table->string('actividad_ocupacion', 150)->default('');
            $table->string('nacionalidad_tipo', 20)->default('MEXICANA');
            $table->string('metodo_pago', 30)->default('');
            $table->string('pago_referencia', 100)->default('');
            $table->timestamps();

            $table->index('id_pasaporte', 'idx_extra_pasaporte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasaporte_datos_extra');
    }
};
