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

        // Un utilisateur "user" ne voit que ses propres réservations (par email) ; un admin voit tout.
        if (! $request->user()?->isAdmin()) {
            $query->where('email_client', $request->user()?->email);
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

        $event = Event::findOrFail($data['event_id']);

        if ($event->places_disponibles < $data['nombre_places']) {
            return response()->json([
                'message' => 'Nombre de places insuffisant'
            ], 400);
        }

        $reservation = Reservation::create($data);

        $event->places_disponibles -= $data['nombre_places'];
        $event->save();

        $this->activityLogger->log($request->user(), 'create', "Réservation de {$data['nombre_places']} place(s) pour '{$event->titre}'", $request, 'Reservation', $reservation->id);
        $this->statRecorder->record('reservation_created', 'Event', $event->id, $data['nombre_places']);

        $reservation->load('event.salle');

        // Email de confirmation avec le billet en pièce jointe (PDF généré à la volée).
        // Une erreur d'envoi (SMTP non configuré, etc.) ne doit pas faire échouer la réservation :
        // elle est journalisée et l'utilisateur reçoit tout de même sa confirmation à l'écran.
        try {
            Mail::to($reservation->email_client)->send(new ReservationConfirmation($reservation));
        } catch (\Throwable $e) {
            Log::error("Échec de l'envoi de l'email de confirmation pour la réservation #{$reservation->id} : {$e->getMessage()}");
        }

        return response()->json($reservation, 201);
    }

    public function show(Reservation $reservation)
    {
        return response()->json(
            $reservation->load('event', 'tickets')
        );
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $event = $reservation->event;

        if ($event) {
            $event->places_disponibles += $reservation->nombre_places;
            $event->save();
        }

        $this->activityLogger->log($request->user(), 'delete', "Annulation de la réservation #{$reservation->id}", $request, 'Reservation', $reservation->id);

        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès'
        ]);
    }
}
