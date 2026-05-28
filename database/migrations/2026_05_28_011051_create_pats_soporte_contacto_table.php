<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pats_soporte_contacto', function (Blueprint $table) {
            $table->bigIncrements('id_soporte');
            $table->string('nombre', 100);
            $table->string('correo', 150);
            $table->text('mensaje');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_soporte_contacto');
    }
};
