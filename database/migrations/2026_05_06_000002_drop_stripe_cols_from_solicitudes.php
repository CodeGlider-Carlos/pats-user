<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pats_solicitudes_distribuidor', 'stripe_payment_intent_id')) {
            Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
                $table->dropColumn(['stripe_payment_intent_id', 'stripe_payment_status']);
            });
        }

        if (Schema::hasColumn('pats_solicitudes_franquicia', 'stripe_payment_intent_id')) {
            Schema::table('pats_solicitudes_franquicia', function (Blueprint $table) {
                // El índice único existe en la tabla de franquicias — hay que eliminarlo primero
                $table->dropUnique('uq_psf_stripe_intent');
                $table->dropColumn(['stripe_payment_intent_id', 'stripe_payment_status']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id', 120)->nullable()->after('esquema_pagos_json');
            $table->string('stripe_payment_status', 30)->nullable()->after('stripe_payment_intent_id');
        });

        Schema::table('pats_solicitudes_franquicia', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id', 120)->nullable()->after('esquema_pagos_json');
            $table->string('stripe_payment_status', 30)->nullable()->after('stripe_payment_intent_id');
            $table->unique('stripe_payment_intent_id', 'uq_psf_stripe_intent');
        });
    }
};
