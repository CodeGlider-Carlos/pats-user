<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_pasaportes', function (Blueprint $table) {
            $table->bigIncrements('id_pasaporte');
            $table->unsignedBigInteger('id_franquicia');
            $table->unsignedBigInteger('id_distribuidor');
            $table->integer('id_gestor')->nullable();
            $table->unsignedBigInteger('id_tipo_precio');
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->unsignedBigInteger('id_beneficiario')->nullable();
            $table->unsignedBigInteger('id_unidad')->nullable();
            $table->string('curp');
            $table->string('nombres');
            $table->string('apellido_pa');
            $table->string('apellido_ma');
            $table->date('fecha_nacimiento');
            $table->string('telefono');
            $table->string('correo');
            $table->dateTime('fecha_alta');
            $table->date('vigencia');
            $table->date('fecha_baja')->nullable();
            $table->string('frecuencia_pago');
            $table->string('estatus');
            $table->decimal('valor_pasaporte', 12, 2);
            $table->decimal('valor_final_pasaporte', 12, 2);
            $table->string('cupon')->nullable();
            $table->string('pais')->nullable();
            $table->string('region')->nullable();
            $table->string('zona')->nullable();
            $table->string('unidad')->nullable();
            $table->string('tipo_cliente', 50)->nullable();
            $table->string('nombre_empresa')->nullable();
            $table->string('fotografia_path', 500)->nullable();
            $table->string('fotografia_nombre')->nullable();
            $table->string('fotografia_mime', 120)->nullable();
            $table->dateTime('fecha_ultimo_pago')->nullable();
            $table->dateTime('fecha_vencimiento_real')->nullable();
            $table->integer('meses_vencidos')->default(0);
            $table->decimal('recargo_acumulado', 12, 2)->default(0.00);
            $table->boolean('activo')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('id_franquicia', 'idx_pats_pasaportes_franquicia');
            $table->index('id_distribuidor', 'idx_pats_pasaportes_distribuidor');
            $table->index('id_tipo_precio', 'idx_pats_pasaportes_tipo_precio');
            $table->index('id_cliente', 'idx_pats_pasaportes_cliente');
            $table->index('id_beneficiario', 'idx_pats_pasaportes_beneficiario');
            $table->index('estatus', 'idx_pats_pasaportes_estatus');
            $table->index('frecuencia_pago', 'idx_pats_pasaportes_frecuencia');
            $table->index('vigencia', 'idx_pats_pasaportes_vigencia');
            $table->index('region', 'idx_pats_pasaportes_region');
            $table->index('zona', 'idx_pats_pasaportes_zona');
            $table->index('tipo_cliente', 'idx_pats_pasaportes_tipo_cliente');
            $table->index('id_gestor', 'idx_pats_pasaportes_id_gestor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_pasaportes');
    }
};
