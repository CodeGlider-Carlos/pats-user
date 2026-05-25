<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_facturas_comision', function (Blueprint $table) {
            $table->increments('id_factura');
            $table->string('tipo_actor', 20);
            $table->integer('id_actor');
            $table->smallInteger('periodo_anio');
            $table->tinyInteger('periodo_mes');
            $table->decimal('saldo_reportado', 12, 2)->default(0.00);
            $table->string('folio_factura', 120)->nullable();
            $table->string('nombre_archivo');
            $table->string('archivo_url', 500);
            $table->string('mime_type', 120);
            $table->bigInteger('tamano_bytes')->default(0);
            $table->text('observaciones')->nullable();
            $table->string('estatus', 30)->default('CARGADA');
            $table->string('cargado_por', 120)->nullable();
            $table->dateTime('fecha_carga')->useCurrent();
            $table->string('revisado_por', 120)->nullable();
            $table->dateTime('fecha_revision')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['tipo_actor', 'id_actor', 'periodo_anio', 'periodo_mes'], 'uq_factura_actor_periodo');
            $table->index(['tipo_actor', 'id_actor'], 'idx_factura_actor');
            $table->index(['periodo_anio', 'periodo_mes'], 'idx_factura_periodo');
            $table->index('estatus', 'idx_factura_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_facturas_comision');
    }
};
