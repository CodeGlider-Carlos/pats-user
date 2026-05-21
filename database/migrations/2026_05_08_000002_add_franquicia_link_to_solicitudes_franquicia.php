<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_solicitudes_franquicia', function (Blueprint $table) {
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'id_franquicia_link'))
                $table->unsignedBigInteger('id_franquicia_link')->default(0);
            // Address detail columns — only add if they don't already exist
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'calle')) {
                $table->string('calle', 200)->nullable()->after('direccion');
            }
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'numero_exterior')) {
                $table->string('numero_exterior', 20)->nullable()->after('calle');
            }
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'numero_interior')) {
                $table->string('numero_interior', 20)->nullable()->after('numero_exterior');
            }
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'colonia')) {
                $table->string('colonia', 120)->nullable()->after('numero_interior');
            }
            if (! Schema::hasColumn('pats_solicitudes_franquicia', 'codigo_postal')) {
                $table->string('codigo_postal', 10)->nullable()->after('colonia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_franquicia', function (Blueprint $table) {
            $table->dropColumn(['id_franquicia_link']);
            // Only drop address columns if they were added by this migration
            // (they may have pre-existed, so we leave them)
        });
    }
};
