<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            // ABA routing number for US distributors (9 digits)
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'routing_number')) {
                $table->string('routing_number', 9)->nullable()->after('clabe');
            }

            // US account numbers can be up to 17 digits; widen from 11 to 20
            if (Schema::hasColumn('pats_solicitudes_distribuidor', 'numero_cuenta')) {
                $table->string('numero_cuenta', 20)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (Schema::hasColumn('pats_solicitudes_distribuidor', 'routing_number')) {
                $table->dropColumn('routing_number');
            }
            if (Schema::hasColumn('pats_solicitudes_distribuidor', 'numero_cuenta')) {
                $table->string('numero_cuenta', 11)->nullable()->change();
            }
        });
    }
};
