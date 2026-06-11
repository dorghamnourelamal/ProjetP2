<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'heure_fin')) {
            Schema::table('events', function (Blueprint $table) {
                $table->time('heure_fin')->nullable()->after('heure');
            });
        }

        DB::table('events')->orderBy('id')->each(function ($event) {
            if (! empty($event->heure_fin)) {
                return;
            }

            $duration = $event->duree_minutes ?? 120;

            $heureFin = Carbon::parse($event->date_event . ' ' . $event->heure)
                ->addMinutes((int) $duration)
                ->format('H:i:s');

            DB::table('events')
                ->where('id', $event->id)
                ->update(['heure_fin' => $heureFin]);
        });

        if (Schema::hasColumn('events', 'duree_minutes')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('duree_minutes');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('events', 'duree_minutes')) {
            Schema::table('events', function (Blueprint $table) {
                $table->integer('duree_minutes')->default(120)->after('heure');
            });
        }

        if (Schema::hasColumn('events', 'heure_fin')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('heure_fin');
            });
        }
    }
};
