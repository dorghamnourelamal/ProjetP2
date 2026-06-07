<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Complète la table "tickets" : un billet est rattaché à une réservation,
     * possède un code unique, un type/tarif et un statut (valide/utilisé/annulé).
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('type')->default('standard'); // standard, vip, étudiant...
            $table->decimal('prix', 8, 2)->default(0);
            $table->string('statut')->default('valide'); // valide, utilisé, annulé
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['reservation_id', 'code', 'type', 'prix', 'statut']);
        });
    }
};
