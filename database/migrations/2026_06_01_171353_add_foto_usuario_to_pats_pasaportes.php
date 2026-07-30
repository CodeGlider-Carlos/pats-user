<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->string('foto_usuario', 500)->nullable()->after('fotografia_mime');
        });
    }

    public function down(): void
    {
        Schema::table('pats_pasaportes', function (Blueprint $table) {
            $table->dropColumn('foto_usuario');
        });
    }
};
