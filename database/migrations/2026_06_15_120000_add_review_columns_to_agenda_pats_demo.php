<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_pats_demo', function (Blueprint $table) {
            $table->string('reviewed', 20)->nullable()->after('id_tarjeta');
            $table->unsignedTinyInteger('review_value')->nullable()->after('reviewed');
            $table->text('review_comment')->nullable()->after('review_value');

            $table->index('reviewed', 'idx_pats_reviewed');
        });
    }

    public function down(): void
    {
        Schema::table('agenda_pats_demo', function (Blueprint $table) {
            $table->dropIndex('idx_pats_reviewed');
            $table->dropColumn(['reviewed', 'review_value', 'review_comment']);
        });
    }
};
