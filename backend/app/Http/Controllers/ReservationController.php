<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmation;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
        private StatRecorder $statRecorder,
    ) {
    }

    public function index(Request $request)
    {
        $query = Reservation::with('event.salle', 'tickets');

        if (! $request->user()->isAdmin()) {
            $query->where('email_client', $request->user()->email);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom_client' => 'required|string|max:255',
            'email_client' => 'required|email',
            'nombre_places' => 'required|integer|min:1',
            'event_id' => 'required|exists:events,id',
        ]);

        $user = $request->user();

        if (! $user->isAdmin()) {
            $data['nom_client'] = $user->name;
            $data['email_client'] = $user->email;
        }

        $event = Event::findOrFail($data['event_id']);

        if ($event->places_disponibles < $data['nombre_places']) {
            return response()->json([
                'message' => 'Nombre de places insuffisant.'
            ], 400);
        }

        $reservation = Reservation::where('email_client', $data['email_client'])
            ->where('event_id', $data['event_id'])
            ->first();

        if ($reservation) {
            $reservation->nombre_places += $data['nombre_places'];
            $reservation->save();

            $actionMessage = "Ajout de {$data['nombre_places']} place(s) à la réservation #{$reservation->id} pour '{$event->titre}'";
        } else {
            $reservation = Reservation::create($data);

            $actionMessage = "Création d'une réservation de {$data['nombre_places']} place(s) pour '{$event->titre}'";
        }

        $event->places_disponibles -= $data['nombre_places'];
        $event->save();

        $createdTickets = $this->createMissingTicketsForReservation($reservation, $event);

        $this->activityLogger->log(
            $user,
            'create',
            $actionMessage,
            $request,
            'Reservation',
            $reservation->id
        );

        foreach ($createdTickets as $ticket) {
            $this->activityLogger->log(
                $user,
                'create',
                "Génération automatique du billet {$ticket->code} pour la réservation #{$reservation->id}",
                $request,
                'Ticket',
                $ticket->id
            );

            $this->statRecorder->record('ticket_sold', 'Ticket', $ticket->id, (float) $ticket->prix, [
                'reservation_id' => $reservation->id,
            ]);
        }

        $this->statRecorder->record('reservation_created', 'Event', $event->id, $data['nombre_places']);

        $reservation->load('event.salle', 'tickets');

        try {
            Mail::to($reservation->email_client)->send(new ReservationConfirmation($reservation));
        } catch (\Throwable $e) {
            Log::error("Échec de l'envoi de l'email de confirmation pour la réservation #{$reservation->id} : {$e->getMessage()}");
        }

        return response()->json($reservation, 201);
    }

    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $reservation->email_client !== $user->email) {
            return response()->json([
                'message' => 'Accès interdit à cette réservation.'
            ], 403);
        }

        return response()->json(
            $reservation->load('event.salle', 'tickets')
        );
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            return response()->json([
                'message' => 'Seul un administrateur peut supprimer une réservation.'
            ], 403);
        }

        $event = $reservation->event;

        if ($event) {
            $event->places_disponibles += $reservation->nombre_places;
            $event->save();
        }

        foreach ($reservation->tickets as $ticket) {
            $this->activityLogger->log(
                $user,
                'delete',
                "Suppression du billet {$ticket->code} liée à l'annulation de la réservation #{$reservation->id}",
                $request,
                'Ticket',
                $ticket->id
            );

            $ticket->delete();
        }

        $this->activityLogger->log(
            $user,
            'delete',
            "Annulation de la réservation #{$reservation->id}",
            $request,
            'Reservation',
            $reservation->id
        );

        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès.'
        ]);
    }

    private function createMissingTicketsForReservation(Reservation $reservation, Event $event): array
    {
        $reservation->load('tickets');

        $reservation->tickets()->update([
            'prix' => (float) $event->prix,
        ]);

        $existingTicketsCount = $reservation->tickets()->count();
        $missingTicketsCount = max(0, $reservation->nombre_places - $existingTicketsCount);

        $createdTickets = [];

        for ($i = 0; $i < $missingTicketsCount; $i++) {
            $createdTickets[] = Ticket::create([
                'reservation_id' => $reservation->id,
                'code' => $this->generateUniqueTicketCode(),
                'type' => 'standard',
                'prix' => (float) $event->prix,
                'statut' => 'valide',
            ]);
        }

        return $createdTickets;
    }

    private function generateUniqueTicketCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Ticket::where('code', $code)->exists());

        return $code;
    }
}
