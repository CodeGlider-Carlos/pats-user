<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cols = Schema::getColumnListing('pats_solicitudes_distribuidor');

        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) use ($cols) {
            if (!in_array('apellido_paterno', $cols))
                $table->string('apellido_paterno', 100)->nullable()->after('nombre');
            if (!in_array('apellido_materno', $cols))
                $table->string('apellido_materno', 100)->nullable()->after('apellido_paterno');
            if (!in_array('fecha_nacimiento', $cols))
                $table->date('fecha_nacimiento')->nullable()->after('apellido_materno');
            if (!in_array('pais_nacimiento', $cols))
                $table->string('pais_nacimiento', 80)->nullable()->after('fecha_nacimiento');
            if (!in_array('nacionalidad', $cols))
                $table->string('nacionalidad', 80)->nullable()->after('pais_nacimiento');
            if (!in_array('ocupacion', $cols))
                $table->string('ocupacion', 120)->nullable()->after('nacionalidad');
            if (!in_array('ciudad', $cols))
                $table->string('ciudad', 100)->nullable()->after('zona');
            if (!in_array('tipo_identificacion', $cols))
                $table->string('tipo_identificacion', 60)->nullable()->after('rfc');
            if (!in_array('identificacion_emitida_por', $cols))
                $table->string('identificacion_emitida_por', 100)->nullable()->after('tipo_identificacion');
            if (!in_array('numero_identificacion', $cols))
                $table->string('numero_identificacion', 60)->nullable()->after('identificacion_emitida_por');
            if (!in_array('tipo_operacion', $cols))
                $table->string('tipo_operacion', 120)->nullable()->after('modalidad_pago');
            if (!in_array('moneda', $cols))
                $table->string('moneda', 10)->nullable()->default('MXN')->after('tipo_operacion');
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->dropColumn([
                'apellido_paterno', 'apellido_materno', 'fecha_nacimiento',
                'pais_nacimiento', 'nacionalidad', 'ocupacion',
                'ciudad',
                'tipo_identificacion', 'identificacion_emitida_por', 'numero_identificacion',
                'tipo_operacion', 'moneda',
            ]);
        });
    }
};
