<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_preview_dist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_solicitud');
            $table->string('selfie_path', 500)->nullable();
            $table->string('contrato_path', 500)->nullable();
            $table->string('firma_path', 500)->nullable();
            $table->string('selfie_mime', 60)->nullable();
            $table->string('contrato_mime', 60)->nullable();
            $table->string('firma_mime', 60)->nullable();
            $table->unsignedInteger('selfie_kb')->nullable();
            $table->unsignedInteger('contrato_kb')->nullable();
            $table->unsignedInteger('firma_kb')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_preview_dist');
    }
};
