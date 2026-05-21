<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (! Schema::hasColumn('pats_solicitudes_distribuidor', 'id_gestor')) {
                $table->unsignedBigInteger('id_gestor')->default(0)->after('id_franquicia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            if (Schema::hasColumn('pats_solicitudes_distribuidor', 'id_gestor')) {
                $table->dropColumn('id_gestor');
            }
        });
    }
};
