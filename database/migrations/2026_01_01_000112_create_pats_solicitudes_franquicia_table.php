<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_solicitudes_franquicia', function (Blueprint $table) {
            $table->increments('id_solicitud');
            $table->integer('id_franquicia_generada')->nullable();
            $table->integer('id_gestor')->nullable();
            $table->string('token_origen', 128)->nullable();
            $table->unsignedBigInteger('id_franquicia_link')->default(0);
            $table->string('origen_solicitud', 40)->nullable()->default('CORPORATIVO');
            $table->integer('user_solicita')->nullable();
            $table->integer('user_valida')->nullable();
            $table->integer('user_autoriza')->nullable();
            $table->string('pais', 120)->nullable()->default('México');
            $table->string('region', 80)->nullable();
            $table->string('zona', 160)->nullable();
            $table->string('unidad', 80)->nullable();
            $table->string('nombre_comercial');
            $table->string('tipo_persona', 20)->default('FISICA');
            $table->string('nombre_titular');
            $table->string('razon_social')->nullable();
            $table->string('rfc', 30)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('correo', 180)->nullable();
            $table->text('direccion')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero_exterior', 50)->nullable();
            $table->string('numero_interior', 50)->nullable();
            $table->string('colonia')->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->text('referencias_direccion')->nullable();
            $table->string('titularidad_tipo', 30)->default('INDIVIDUAL');
            $table->decimal('porcentaje_titular_real', 8, 2)->default(100.00);
            $table->decimal('porcentaje_adminpats', 8, 2)->default(0.00);
            $table->string('banco')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->string('clabe')->nullable();
            $table->string('titular_cuenta')->nullable();
            $table->string('modalidad_pago', 40)->nullable()->default('CONTADO');
            $table->decimal('valor_total', 14, 2)->default(0.00);
            $table->decimal('enganche', 14, 2)->default(0.00);
            $table->decimal('saldo_financiado', 14, 2)->default(0.00);
            $table->integer('plazo_meses')->default(0);
            $table->string('periodicidad', 40)->nullable()->default('MENSUAL');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_primer_vencimiento')->nullable();
            $table->string('contrato_admin_path')->nullable();
            $table->string('contrato_firmado_path')->nullable();
            $table->longText('contrato_digital_html')->nullable();
            $table->string('contrato_digital_hash', 128)->nullable();
            $table->dateTime('contrato_digital_firmado_at')->nullable();
            $table->string('firma_digital_nombre', 180)->nullable();
            $table->string('firma_digital_rfc', 30)->nullable();
            $table->string('firma_digital_ip', 80)->nullable();
            $table->string('firma_digital_user_agent')->nullable();
            $table->longText('firma_digital_data')->nullable();
            $table->string('fotografia_path')->nullable();
            $table->string('fotografia_nombre_original')->nullable();
            $table->string('fotografia_mime_type', 120)->nullable();
            $table->integer('fotografia_size_kb')->default(0);
            $table->dateTime('fecha_fotografia')->nullable();
            $table->string('estatus', 60)->default('ENVIADA');
            $table->text('motivo_rechazo')->nullable();
            $table->text('observaciones_admin')->nullable();
            $table->text('observaciones_solicitante')->nullable();
            $table->dateTime('fecha_envio_contrato')->nullable();
            $table->dateTime('fecha_carga_firmado')->nullable();
            $table->dateTime('fecha_autorizacion')->nullable();
            $table->dateTime('fecha_conversion_alta')->nullable();
            $table->boolean('activo')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('estatus', 'idx_psf_estatus');
            $table->index('activo', 'idx_psf_activo');
            $table->index('region', 'idx_psf_region');
            $table->index('zona', 'idx_psf_zona');
            $table->index('unidad', 'idx_psf_unidad');
            $table->index('tipo_persona', 'idx_psf_tipo_persona');
            $table->index('titularidad_tipo', 'idx_psf_titularidad_tipo');
            $table->index('id_franquicia_generada', 'idx_psf_id_franquicia_generada');
            $table->index('id_gestor', 'idx_psf_id_gestor');
            $table->index('token_origen', 'idx_psf_token_origen');
            $table->index('correo', 'idx_psf_correo');
            $table->index('rfc', 'idx_psf_rfc');
            $table->index('contrato_digital_hash', 'idx_psf_contrato_digital_hash');
            $table->index('created_at', 'idx_psf_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_franquicia');
    }
};
