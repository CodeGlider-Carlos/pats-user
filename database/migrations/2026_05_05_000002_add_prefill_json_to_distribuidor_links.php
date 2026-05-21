<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('distribuidor_links', 'prefill_json')) {
            return;
        }
        Schema::table('distribuidor_links', function (Blueprint $table) {
            $table->text('prefill_json')->nullable()->after('type_pay');
        });
    }

    public function down(): void
    {
        Schema::table('distribuidor_links', function (Blueprint $table) {
            $table->dropColumn('prefill_json');
        });
    }
};
