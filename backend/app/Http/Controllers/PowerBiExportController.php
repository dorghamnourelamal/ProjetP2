<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\StatEntry;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class PowerBiExportController extends Controller
{
    public function events()
    {
        $events = Event::with(['salle', 'reservations.tickets'])->get();

        $rows = $events->map(function (Event $event) {
            $placesReservees = $event->reservations->sum('nombre_places');
            $capaciteSalle = $event->salle?->capacite ?? 0;
            $chiffreAffaires = $event->reservations
                ->flatMap(fn ($reservation) => $reservation->tickets)
                ->sum('prix');

            return [
                'id' => $event->id,
                'titre' => $event->titre,
                'statut' => $event->statut ?? 'actif',
                'description' => $event->description,
                'date_event' => $event->date_event,
                'heure_debut' => $event->heure,
                'heure_fin' => $event->heure_fin,
                'prix' => $event->prix,
                'salle_id' => $event->salle_id,
                'salle_nom' => $event->salle?->nom,
                'salle_capacite' => $capaciteSalle,
                'places_disponibles' => $event->places_disponibles,
                'places_reservees' => $placesReservees,
                'taux_occupation' => $capaciteSalle > 0
                    ? round(($placesReservees / $capaciteSalle) * 100, 2)
                    : 0,
                'chiffre_affaires' => $chiffreAffaires,
                'created_at' => $this->formatDate($event->created_at),
                'updated_at' => $this->formatDate($event->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_events.csv', $rows);
    }

    public function reservations()
    {
        $reservations = Reservation::with(['event.salle', 'tickets'])->get();

        $rows = $reservations->map(function (Reservation $reservation) {
            return [
                'id' => $reservation->id,
                'nom_client' => $reservation->nom_client,
                'email_client' => $reservation->email_client,
                'nombre_places' => $reservation->nombre_places,
                'event_id' => $reservation->event_id,
                'event_titre' => $reservation->event?->titre,
                'event_statut' => $reservation->event?->statut ?? 'actif',
                'date_event' => $reservation->event?->date_event,
                'heure_debut' => $reservation->event?->heure,
                'heure_fin' => $reservation->event?->heure_fin,
                'salle_nom' => $reservation->event?->salle?->nom,
                'tickets_count' => $reservation->tickets->count(),
                'montant_total' => $reservation->tickets->sum('prix'),
                'created_at' => $this->formatDate($reservation->created_at),
                'updated_at' => $this->formatDate($reservation->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_reservations.csv', $rows);
    }

    public function tickets()
    {
        $tickets = Ticket::with(['reservation.event.salle'])->get();

        $rows = $tickets->map(function (Ticket $ticket) {
            return [
                'id' => $ticket->id,
                'code' => $ticket->code,
                'type' => $ticket->type,
                'statut' => $ticket->statut,
                'prix' => $ticket->prix,
                'reservation_id' => $ticket->reservation_id,
                'nom_client' => $ticket->reservation?->nom_client,
                'email_client' => $ticket->reservation?->email_client,
                'event_id' => $ticket->reservation?->event_id,
                'event_titre' => $ticket->reservation?->event?->titre,
                'event_statut' => $ticket->reservation?->event?->statut ?? 'actif',
                'date_event' => $ticket->reservation?->event?->date_event,
                'heure_debut' => $ticket->reservation?->event?->heure,
                'heure_fin' => $ticket->reservation?->event?->heure_fin,
                'salle_nom' => $ticket->reservation?->event?->salle?->nom,
                'created_at' => $this->formatDate($ticket->created_at),
                'updated_at' => $this->formatDate($ticket->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_tickets.csv', $rows);
    }

    public function salles()
    {
        $salles = Salle::with(['events.reservations'])->get();

        $rows = $salles->map(function (Salle $salle) {
            $totalEvents = $salle->events->count();
            $totalReservations = $salle->events
                ->flatMap(fn ($event) => $event->reservations)
                ->count();

            $placesReservees = $salle->events
                ->flatMap(fn ($event) => $event->reservations)
                ->sum('nombre_places');

            $capaciteTheorique = $salle->capacite * max(1, $totalEvents);

            return [
                'id' => $salle->id,
                'nom' => $salle->nom,
                'capacite' => $salle->capacite,
                'adresse' => $salle->adresse,
                'total_events' => $totalEvents,
                'total_reservations' => $totalReservations,
                'places_reservees' => $placesReservees,
                'taux_occupation_global' => $capaciteTheorique > 0
                    ? round(($placesReservees / $capaciteTheorique) * 100, 2)
                    : 0,
                'created_at' => $this->formatDate($salle->created_at),
                'updated_at' => $this->formatDate($salle->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_salles.csv', $rows);
    }

    public function activity()
    {
        $activities = ActivityLog::orderByDesc('created_at')->get();

        $rows = $activities->map(function ($activity) {
            return [
                'id' => (string) ($activity->_id ?? $activity->id),
                'user_id' => $activity->user_id,
                'user_email' => $activity->user_email,
                'action' => $activity->action,
                'entity' => $activity->entity,
                'entity_id' => $activity->entity_id,
                'description' => $activity->description,
                'ip_address' => $activity->ip_address,
                'user_agent' => $activity->user_agent,
                'created_at' => $this->formatDate($activity->created_at),
                'updated_at' => $this->formatDate($activity->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_activity.csv', $rows);
    }

    public function stats()
    {
        $stats = StatEntry::orderByDesc('created_at')->get();

        $rows = $stats->map(function ($stat) {
            return [
                'id' => (string) ($stat->_id ?? $stat->id),
                'metric' => $stat->metric,
                'entity' => $stat->entity,
                'entity_id' => $stat->entity_id,
                'value' => $stat->value,
                'meta_json' => json_encode($stat->meta ?? [], JSON_UNESCAPED_UNICODE),
                'created_at' => $this->formatDate($stat->created_at),
                'updated_at' => $this->formatDate($stat->updated_at),
            ];
        });

        return $this->downloadCsv('powerbi_stats.csv', $rows);
    }

    private function downloadCsv(string $filename, Collection $rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($rows->isEmpty()) {
                fputcsv($handle, ['aucune_donnee'], ';');
                fclose($handle);
                return;
            }

            fputcsv($handle, array_keys($rows->first()), ';');

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function formatDate($date): ?string
    {
        if (! $date) {
            return null;
        }

        if (is_string($date)) {
            return $date;
        }

        return $date->format('Y-m-d H:i:s');
    }
}
