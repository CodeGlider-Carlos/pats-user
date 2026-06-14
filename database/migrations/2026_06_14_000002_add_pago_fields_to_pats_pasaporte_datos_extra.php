<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_pasaporte_datos_extra', function (Blueprint $table) {
            $table->string('metodo_pago', 30)->default('')->after('nacionalidad_tipo');
            $table->string('pago_referencia', 100)->default('')->after('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::table('pats_pasaporte_datos_extra', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'pago_referencia']);
        });
    }
};
