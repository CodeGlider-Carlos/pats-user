<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_contratos_firmados', function (Blueprint $table) {
            $table->increments('id_contrato_firmado');
            $table->integer('id_orden')->nullable();
            $table->integer('id_pasaporte')->nullable();
            $table->integer('id_distribuidor');
            $table->integer('id_franquicia');
            $table->string('token_publico', 80)->nullable();
            $table->string('contrato_clave', 80)->default('contrato_pats_base');
            $table->string('contrato_version', 30)->default(1.0);
            $table->longText('html_contrato_renderizado');
            $table->longText('firma_afiliado_base64')->nullable();
            $table->string('nombre_firmante', 180)->nullable();
            $table->char('hash_contrato', 64);
            $table->dateTime('fecha_firma');
            $table->string('ip_firma', 80)->nullable();
            $table->text('user_agent_firma')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('estatus', 30)->default('firmado');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_orden', 'idx_pcf_orden');
            $table->index('id_pasaporte', 'idx_pcf_pasaporte');
            $table->index('id_distribuidor', 'idx_pcf_distribuidor');
            $table->index('id_franquicia', 'idx_pcf_franquicia');
            $table->index('token_publico', 'idx_pcf_token');
            $table->index('hash_contrato', 'idx_pcf_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_contratos_firmados');
    }
};
