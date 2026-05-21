<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'tipo_persona')) {
                $table->enum('tipo_persona', ['FISICA', 'MORAL'])->default('FISICA')->after('nombre');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'modalidad_pago')) {
                $table->enum('modalidad_pago', ['CONTADO', 'DIFERIDO'])->default('CONTADO')->after('titular_cuenta');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'valor_total')) {
                $table->decimal('valor_total', 12, 2)->default(0)->after('modalidad_pago');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'enganche')) {
                $table->decimal('enganche', 12, 2)->default(0)->after('valor_total');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'saldo_financiado')) {
                $table->decimal('saldo_financiado', 12, 2)->default(0)->after('enganche');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'plazo_meses')) {
                $table->unsignedSmallInteger('plazo_meses')->default(0)->after('saldo_financiado');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'periodicidad')) {
                $table->enum('periodicidad', ['MENSUAL', 'QUINCENAL', 'SEMANAL', 'UNICA'])->default('MENSUAL')->after('plazo_meses');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('periodicidad');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'fecha_primer_vencimiento')) {
                $table->date('fecha_primer_vencimiento')->nullable()->after('fecha_inicio');
            }
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'esquema_pagos_json')) {
                $table->json('esquema_pagos_json')->nullable()->after('fecha_primer_vencimiento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $cols = [
                'tipo_persona', 'modalidad_pago', 'valor_total', 'enganche',
                'saldo_financiado', 'plazo_meses', 'periodicidad',
                'fecha_inicio', 'fecha_primer_vencimiento', 'esquema_pagos_json',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('pats_solicitudes_distribuidor', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
