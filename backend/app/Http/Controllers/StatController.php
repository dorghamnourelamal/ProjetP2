<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\StatEntry;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Ticket;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function overview()
    {
        return response()->json([
            'totals' => [
                'events' => Event::count(),
                'reservations' => Reservation::count(),
                'tickets' => Ticket::count(),
                'places_reservees' => (int) Reservation::sum('nombre_places'),
                'chiffre_affaires' => (float) Ticket::sum('prix'),
            ],

            'top_events_by_reservations' => Reservation::selectRaw('event_id, SUM(nombre_places) as total_places, COUNT(*) as total_reservations')
                ->with('event:id,titre')
                ->groupBy('event_id')
                ->orderByDesc('total_places')
                ->limit(5)
                ->get(),

            'tickets_by_status' => Ticket::selectRaw('statut, COUNT(*) as total')
                ->groupBy('statut')
                ->orderByDesc('total')
                ->get(),

            'places_by_salle' => Salle::with('events.reservations')
                ->get()
                ->map(function (Salle $salle) {
                    $placesReservees = $salle->events
                        ->flatMap(fn ($event) => $event->reservations)
                        ->sum('nombre_places');

                    return [
                        'salle_id' => $salle->id,
                        'salle_nom' => $salle->nom,
                        'capacite' => $salle->capacite,
                        'places_reservees' => $placesReservees,
                        'taux_occupation' => $salle->capacite > 0
                            ? round(($placesReservees / $salle->capacite) * 100, 2)
                            : 0,
                    ];
                })
                ->sortByDesc('places_reservees')
                ->values(),

            'metrics_by_type' => StatEntry::all()
                ->groupBy('metric')
                ->map(fn ($items, $metric) => [
                    '_id'   => $metric ?: 'inconnu',
                    'total' => $items->sum('value'),
                    'count' => $items->count(),
                ])
                ->sortByDesc('total')
                ->values(),

            'recent_activity' => ActivityLog::orderByDesc('created_at')
                ->limit(20)
                ->get(),
        ]);
    }

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
