<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // php artisan make:migration add_beneficiario_directo_to_pats_solicitudes_distribuidor
    public function up(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->enum('beneficiario_directo', ['SI', 'NO'])
                ->nullable()
                ->after('numero_identificacion')
                ->comment('Declaración dueño beneficiario — Anexo 12');
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->dropColumn('beneficiario_directo');
        });
    }
};
