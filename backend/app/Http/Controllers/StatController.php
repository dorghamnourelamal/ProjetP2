<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\StatEntry;
use App\Models\Reservation;
use Illuminate\Http\Request;

/**
 * Indicateurs et tableaux de bord (croisement données relationnelles MySQL
 * et indicateurs d'usage MongoDB), exploitables pour des rapports/visualisations
 * (par ex. export vers Power BI ou affichage de graphiques côté Angular).
 */
class StatController extends Controller
{
    /**
     * Vue d'ensemble : volumes (MySQL) + activité récente et indicateurs (MongoDB).
     * GET /api/stats/overview
     */
    public function overview()
    {
        return response()->json([
            'totals' => [
                'events' => Event::count(),
                'reservations' => Reservation::count(),
                'places_reservees' => (int) Reservation::sum('nombre_places'),
            ],
            'top_events_by_reservations' => Reservation::selectRaw('event_id, SUM(nombre_places) as total_places, COUNT(*) as total_reservations')
                ->with('event:id,titre')
                ->groupBy('event_id')
                ->orderByDesc('total_places')
                ->limit(5)
                ->get(),
            'metrics_by_type' => StatEntry::raw(function ($collection) {
                return $collection->aggregate([
                    ['$group' => ['_id' => '$metric', 'total' => ['$sum' => '$value'], 'count' => ['$sum' => 1]]],
                    ['$sort' => ['total' => -1]],
                ]);
            }),
            'recent_activity' => ActivityLog::orderByDesc('created_at')->limit(20)->get(),
        ]);
    }

    /**
     * Historique paginé des activités (audit / sécurité).
     * GET /api/stats/activity?action=login&user_id=3
     */
    public function activity(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate(25)
        );
    }
}
