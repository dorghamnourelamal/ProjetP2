<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmation;
use App\Models\Reservation;
use App\Models\Event;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
        private StatRecorder $statRecorder,
    ) {
    }

    public function index(Request $request)
    {
        $query = Reservation::with('event');

        // Admin : voit toutes les réservations.
        // User : voit uniquement ses propres réservations.
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

        // Un utilisateur simple réserve toujours avec son propre nom/email.
        // Cela évite qu'il réserve au nom d'un autre compte.
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

        // Si le même utilisateur a déjà réservé le même événement,
        // on ajoute les nouvelles places à sa réservation existante.
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

        // Dans les deux cas, on diminue les places disponibles.
        $event->places_disponibles -= $data['nombre_places'];
        $event->save();

        $this->activityLogger->log(
            $user,
            'create',
            $actionMessage,
            $request,
            'Reservation',
            $reservation->id
        );

        $this->statRecorder->record('reservation_created', 'Event', $event->id, $data['nombre_places']);

        $reservation->load('event.salle');

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
            $reservation->load('event', 'tickets')
        );
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $reservation->email_client !== $user->email) {
            return response()->json([
                'message' => 'Accès interdit à cette réservation.'
            ], 403);
        }

        $event = $reservation->event;

        if ($event) {
            $event->places_disponibles += $reservation->nombre_places;
            $event->save();
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
}
