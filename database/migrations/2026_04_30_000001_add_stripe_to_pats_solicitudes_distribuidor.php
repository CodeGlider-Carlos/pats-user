<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cols = Schema::getColumnListing('pats_solicitudes_distribuidor');
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) use ($cols) {
            if (!in_array('stripe_payment_intent_id', $cols))
                $table->string('stripe_payment_intent_id', 120)->nullable()->after('esquema_pagos_json');
            if (!in_array('stripe_payment_status', $cols))
                $table->string('stripe_payment_status', 30)->nullable()->after('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::table('pats_solicitudes_distribuidor', function (Blueprint $table) {
            $table->dropColumn(['stripe_payment_intent_id', 'stripe_payment_status']);
        });
    }
};