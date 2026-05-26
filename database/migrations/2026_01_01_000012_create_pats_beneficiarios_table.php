<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_beneficiarios', function (Blueprint $table) {
            $table->bigIncrements('id_beneficiario');
            $table->unsignedBigInteger('id_cliente');
            $table->string('nombre_completo');
            $table->string('curp')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('ruta_fotografia')->nullable();
            $table->string('estatus_laboral', 100)->nullable()->default('activo');
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_cliente', 'idx_pats_beneficiarios_cliente');
            $table->index('nombre_completo', 'idx_pats_beneficiarios_nombre');
            $table->index('curp', 'idx_pats_beneficiarios_curp');
            $table->index('estatus_laboral', 'idx_pats_beneficiarios_estatus_laboral');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_beneficiarios');
    }
};
