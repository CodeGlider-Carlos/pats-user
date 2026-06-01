<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->string('token_qr', 64)->nullable()->unique()->after('foto_usuario');
        });

        // Generar token para pasaportes existentes
        DB::table('pats_pasaportes')->whereNull('token_qr')->orderBy('id_pasaporte')->each(function ($p) {
            DB::table('pats_pasaportes')
                ->where('id_pasaporte', $p->id_pasaporte)
                ->update(['token_qr' => Str::uuid()->toString()]);
        });
    }

    public function down(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->dropColumn('token_qr');
        });
    }
};
