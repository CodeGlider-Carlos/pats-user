<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'token_referido')) {
                $table->string('token_referido', 128)->nullable()
                    ->comment('Token del agente/canal que refirió la solicitud')
                    ->after('id_solicitud');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (Schema::hasColumn('pats_solicitudes_distribuidor', 'token_referido')) {
                $table->dropColumn('token_referido');
            }
        });
    }
};
