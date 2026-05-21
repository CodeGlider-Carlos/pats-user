<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pats_solicitudes_distribuidor_historial')) {
            return;
        }

        Schema::create('pats_solicitudes_distribuidor_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud')->index();

            $table->string('evento_tipo', 80)->index();
            $table->string('estatus_anterior', 40)->nullable();
            $table->string('estatus_nuevo', 40)->nullable();
            $table->json('payload_json')->nullable();

            $table->unsignedBigInteger('user_evento')->nullable();
            $table->timestamp('fecha_evento')->useCurrent();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_solicitud')
                ->references('id_solicitud')
                ->on('pats_solicitudes_distribuidor')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_solicitudes_distribuidor_historial');
    }
};
