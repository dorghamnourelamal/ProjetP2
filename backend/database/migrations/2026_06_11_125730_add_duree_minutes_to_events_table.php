<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la durée d'un événement en minutes.
     * Exemple :
     * 60  = 1 heure
     * 90  = 1h30
     * 120 = 2 heures
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->integer('duree_minutes')->default(120)->after('heure');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('duree_minutes');
        });
    }
};
