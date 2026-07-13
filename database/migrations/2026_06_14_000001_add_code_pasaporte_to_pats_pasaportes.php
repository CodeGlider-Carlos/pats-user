<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->string('code_pasaporte', 30)->nullable()->unique()->after('token_qr')
                ->comment('Código legible: inicial_nombre + inicial_apellido + CRT + MMDDYY. Ej: JFCRT082281');
        });
    }

    public function down(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->dropColumn('code_pasaporte');
        });
    }
};
