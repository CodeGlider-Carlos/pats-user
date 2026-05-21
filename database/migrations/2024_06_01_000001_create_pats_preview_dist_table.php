<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pats_preview_dist')) {
            return;
        }

        Schema::create('pats_preview_dist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud')->index();
            $table->string('selfie_path', 500)->nullable();
            $table->string('contrato_path', 500)->nullable();
            $table->string('firma_path', 500)->nullable();
            $table->string('selfie_mime', 60)->nullable();
            $table->string('contrato_mime', 60)->nullable();
            $table->string('firma_mime', 60)->nullable();
            $table->unsignedInteger('selfie_kb')->nullable();
            $table->unsignedInteger('contrato_kb')->nullable();
            $table->unsignedInteger('firma_kb')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_preview_dist');
    }
};
