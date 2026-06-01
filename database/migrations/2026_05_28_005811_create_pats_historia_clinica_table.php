<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_historia_clinica', function (Blueprint $table) {
            $table->bigIncrements('id_historia_clinica');
            $table->unsignedBigInteger('id_pasaporte');

            // Perfil social y hábitos
            $table->string('ocupacion')->nullable();
            $table->string('estado_civil', 50)->nullable();
            $table->string('escolaridad', 50)->nullable();
            $table->string('actividad_fisica', 50)->nullable();
            $table->string('tabaquismo', 30)->nullable();
            $table->string('alcohol', 30)->nullable();
            $table->string('alimentacion', 50)->nullable();

            // Antecedentes médicos
            $table->json('heredo_familiares')->nullable();
            $table->string('personales_patologicos')->nullable();
            $table->string('personales_no_patologicos')->nullable();
            $table->string('enfermedades_previas')->nullable();

            // Alertas de seguridad
            $table->text('alergias')->nullable();
            $table->text('cirugias')->nullable();
            $table->text('medicamentos')->nullable();
            $table->text('intolerancias')->nullable();

            // Estado general
            $table->decimal('peso', 5, 1)->nullable();
            $table->decimal('altura', 4, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_pasaporte')
                ->references('id_pasaporte')
                ->on('pats_pasaportes')
                ->onDelete('cascade');

            $table->index('id_pasaporte', 'idx_pats_historia_clinica_pasaporte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_historia_clinica');
    }
};
