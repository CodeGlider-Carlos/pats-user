<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franquicia_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 36);
            $table->string('password');
            $table->text('prefill_json')->nullable();
            $table->unsignedBigInteger('id_solicitud')->default(0);
            $table->boolean('active')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique('token', 'franquicia_links_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franquicia_links');
    }
};
