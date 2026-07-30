<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pats_cats_medicos', function (Blueprint $table) {
            $table->decimal('precio_consulta', 10, 2)->nullable()->after('unidad');
        });
    }

    public function down(): void
    {
        Schema::table('pats_cats_medicos', function (Blueprint $table) {
            $table->dropColumn('precio_consulta');
        });
    }
};
