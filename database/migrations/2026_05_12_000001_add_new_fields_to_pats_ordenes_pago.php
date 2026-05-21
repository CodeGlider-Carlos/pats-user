<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pats_ordenes_pago')) {
            return;
        }
        Schema::table('pats_ordenes_pago', function (Blueprint $table) {
            // Token y gestor
            $table->string('token_publico', 128)->nullable()->after('id_orden');
            $table->unsignedBigInteger('id_gestor')->default(0)->after('id_franquicia');

            // Datos fiscales e identificación del paciente
            $table->string('rfc_usuario', 13)->nullable()->after('curp_usuario');
            $table->string('actividad_ocupacion', 120)->nullable()->after('rfc_usuario');
            $table->string('estado_civil', 30)->nullable()->after('actividad_ocupacion');
            $table->string('nacionalidad_tipo', 20)->nullable()->after('estado_civil');
            $table->string('nacionalidad', 60)->nullable()->after('nacionalidad_tipo');
            $table->string('pais_nacimiento', 60)->nullable()->after('nacionalidad');
            $table->string('tipo_documento_identidad', 30)->nullable()->after('pais_nacimiento');
            $table->string('pais_documento_identidad', 60)->nullable()->after('tipo_documento_identidad');
            $table->string('numero_documento_identidad', 60)->nullable()->after('pais_documento_identidad');

            // Domicilio desglosado
            $table->string('dom_calle', 120)->nullable()->after('pais');
            $table->string('dom_num_ext', 20)->nullable()->after('dom_calle');
            $table->string('dom_num_int', 20)->nullable()->after('dom_num_ext');
            $table->string('dom_colonia', 100)->nullable()->after('dom_num_int');
            $table->string('dom_cp', 10)->nullable()->after('dom_colonia');
            $table->string('dom_municipio', 100)->nullable()->after('dom_cp');
            $table->string('dom_estado_acronimo', 10)->nullable()->after('dom_municipio');
            $table->string('dom_estado', 100)->nullable()->after('dom_estado_acronimo');
            $table->string('dom_pais', 60)->nullable()->after('dom_estado');

            // Tipo de paciente y representación
            $table->string('tipo_paciente', 30)->nullable()->after('tipo_cliente');
            $table->boolean('es_menor')->default(false)->after('tipo_paciente');
            $table->boolean('es_adulto_mayor')->default(false)->after('es_menor');
            $table->string('modo_firma', 30)->nullable()->after('es_adulto_mayor');
            $table->boolean('requiere_responsable')->default(false)->after('modo_firma');
            $table->string('tipo_representacion', 30)->nullable()->after('requiere_responsable');
            $table->string('relacion_responsable_paciente', 40)->nullable()->after('tipo_representacion');
            $table->string('motivo_responsable', 60)->nullable()->after('relacion_responsable_paciente');

            // Tutor / responsable
            $table->string('tutor_nombre', 100)->nullable()->after('motivo_responsable');
            $table->string('tutor_apellido_pa', 80)->nullable()->after('tutor_nombre');
            $table->string('tutor_apellido_ma', 80)->nullable()->after('tutor_apellido_pa');
            $table->string('tutor_curp', 18)->nullable()->after('tutor_apellido_ma');
            $table->string('tutor_rfc', 13)->nullable()->after('tutor_curp');
            $table->date('tutor_fecha_nacimiento')->nullable()->after('tutor_rfc');
            $table->string('tutor_correo', 120)->nullable()->after('tutor_fecha_nacimiento');
            $table->string('tutor_telefono', 15)->nullable()->after('tutor_correo');
            $table->string('tutor_nacionalidad_tipo', 20)->nullable()->after('tutor_telefono');
            $table->string('tutor_nacionalidad', 60)->nullable()->after('tutor_nacionalidad_tipo');
            $table->string('tutor_pais_nacimiento', 60)->nullable()->after('tutor_nacionalidad');
            $table->string('tutor_tipo_documento_identidad', 30)->nullable()->after('tutor_pais_nacimiento');
            $table->string('tutor_pais_documento_identidad', 60)->nullable()->after('tutor_tipo_documento_identidad');
            $table->string('tutor_numero_documento_identidad', 60)->nullable()->after('tutor_pais_documento_identidad');

            // Adulto mayor (2 pasaportes vigentes validados)
            $table->boolean('adulto_mayor_pasaportes_validados')->default(false)->after('tutor_numero_documento_identidad');
            $table->text('adulto_mayor_pasaporte_1_json')->nullable()->after('adulto_mayor_pasaportes_validados');
            $table->text('adulto_mayor_pasaporte_2_json')->nullable()->after('adulto_mayor_pasaporte_1_json');

            // Archivos y firma
            $table->string('foto_path', 255)->nullable()->after('adulto_mayor_pasaporte_2_json');
            $table->string('firma_path', 255)->nullable()->after('foto_path');
            $table->longText('html_contrato')->nullable()->after('firma_path');
            $table->string('hash_contrato', 64)->nullable()->after('html_contrato');
            $table->string('nombre_firmante', 200)->nullable()->after('hash_contrato');
            $table->dateTime('fecha_firma')->nullable()->after('nombre_firmante');
            $table->string('ip_firma', 45)->nullable()->after('fecha_firma');
            $table->string('user_agent_firma', 500)->nullable()->after('ip_firma');

            // Stripe
            $table->string('stripe_payment_intent_id', 80)->nullable()->index()->after('user_agent_firma');
        });
    }

    public function down(): void
    {
        Schema::table('pats_ordenes_pago', function (Blueprint $table) {
            $table->dropColumn([
                'token_publico', 'id_gestor',
                'rfc_usuario', 'actividad_ocupacion', 'estado_civil',
                'nacionalidad_tipo', 'nacionalidad', 'pais_nacimiento',
                'tipo_documento_identidad', 'pais_documento_identidad', 'numero_documento_identidad',
                'dom_calle', 'dom_num_ext', 'dom_num_int', 'dom_colonia', 'dom_cp',
                'dom_municipio', 'dom_estado_acronimo', 'dom_estado', 'dom_pais',
                'tipo_paciente', 'es_menor', 'es_adulto_mayor',
                'modo_firma', 'requiere_responsable', 'tipo_representacion',
                'relacion_responsable_paciente', 'motivo_responsable',
                'tutor_nombre', 'tutor_apellido_pa', 'tutor_apellido_ma',
                'tutor_curp', 'tutor_rfc', 'tutor_fecha_nacimiento',
                'tutor_correo', 'tutor_telefono',
                'tutor_nacionalidad_tipo', 'tutor_nacionalidad', 'tutor_pais_nacimiento',
                'tutor_tipo_documento_identidad', 'tutor_pais_documento_identidad', 'tutor_numero_documento_identidad',
                'adulto_mayor_pasaportes_validados', 'adulto_mayor_pasaporte_1_json', 'adulto_mayor_pasaporte_2_json',
                'foto_path', 'firma_path', 'html_contrato', 'hash_contrato',
                'nombre_firmante', 'fecha_firma', 'ip_firma', 'user_agent_firma',
                'stripe_payment_intent_id',
            ]);
        });
    }
};
