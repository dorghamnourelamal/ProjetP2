<?php

namespace App\Http\Controllers;

use App\Mail\EventCancellation;
use App\Models\Event;
use App\Models\Mongo\FileMeta;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
        private StatRecorder $statRecorder,
    ) {
    }

    public function index()
    {
        $events = Event::with('salle')->get();

        $events->transform(function ($event) {
            $event->image_url = $this->getEventImageUrl($event->id);
            return $event;
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_event' => 'required|date',
            'heure' => 'required',
            'heure_fin' => 'required',
            'places_disponibles' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'salle_id' => 'required|exists:salles,id',
        ]);

        $this->validateEventDateTime($data['date_event'], $data['heure'], $data['heure_fin']);
        $this->validateSalleAvailability(
            (int) $data['salle_id'],
            $data['date_event'],
            $data['heure'],
            $data['heure_fin']
        );

        $event = Event::create($data);

        $this->activityLogger->log(
            $request->user(),
            'create',
            "Création de l'événement '{$event->titre}'",
            $request,
            'Event',
            $event->id
        );

        $event->image_url = $this->getEventImageUrl($event->id);

        return response()->json($event, 201);
    }

    public function show(Request $request, Event $event)
    {
        $this->statRecorder->record('event_view', 'Event', $event->id);

        $event->load('salle', 'reservations');
        $event->image_url = $this->getEventImageUrl($event->id);

        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_event' => 'required|date',
            'heure' => 'required',
            'heure_fin' => 'required',
            'places_disponibles' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'salle_id' => 'required|exists:salles,id',
        ]);

        $this->validateEventDateTime($data['date_event'], $data['heure'], $data['heure_fin']);
        $this->validateSalleAvailability(
            (int) $data['salle_id'],
            $data['date_event'],
            $data['heure'],
            $data['heure_fin'],
            $event->id
        );

        $event->update($data);

        $this->activityLogger->log(
            $request->user(),
            'update',
            "Modification de l'événement '{$event->titre}'",
            $request,
            'Event',
            $event->id
        );

        $event->image_url = $this->getEventImageUrl($event->id);

        return response()->json($event);
    }

    public function destroy(Request $request, Event $event)
    {
        $titre = $event->titre;
        $eventId = $event->id;

        $event->load('reservations', 'salle');

        $event->update(['statut' => 'annulé']);

        foreach ($event->reservations as $reservation) {
            try {
                Mail::to($reservation->email_client)
                    ->send(new EventCancellation($event, $reservation));
            } catch (\Throwable $e) {
                Log::error("Échec envoi email annulation événement #{$eventId} à {$reservation->email_client} : {$e->getMessage()}");
            }
        }

        $this->activityLogger->log(
            $request->user(),
            'delete',
            "Annulation de l'événement '{$titre}'",
            $request,
            'Event',
            $eventId
        );

        return response()->json([
            'message' => 'Événement annulé avec succès'
        ]);
    }

    private function validateEventDateTime(string $date, string $heureDebut, string $heureFin): void
    {
        $start = Carbon::parse($date . ' ' . $heureDebut);
        $end = Carbon::parse($date . ' ' . $heureFin);

        if ($start->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'date_event' => [
                    "La date et l'heure de début doivent être supérieures à la date et l'heure actuelles."
                ],
            ]);
        }

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'heure_fin' => [
                    "L'heure de fin doit être supérieure à l'heure de début."
                ],
            ]);
        }
    }

    private function validateSalleAvailability(
        int $salleId,
        string $date,
        string $heureDebut,
        string $heureFin,
        ?int $eventIdToIgnore = null
    ): void {
        $newStart = Carbon::parse($date . ' ' . $heureDebut);
        $newEnd = Carbon::parse($date . ' ' . $heureFin);

        $events = Event::query()
            ->where('salle_id', $salleId)
            ->where('date_event', $date)
            ->when($eventIdToIgnore !== null, function ($query) use ($eventIdToIgnore) {
                $query->where('id', '!=', $eventIdToIgnore);
            })
            ->get();

        foreach ($events as $event) {
            $existingStart = Carbon::parse($event->date_event . ' ' . $event->heure);
            $existingEnd = Carbon::parse($event->date_event . ' ' . $event->heure_fin);

            $hasConflict = $newStart->lt($existingEnd) && $newEnd->gt($existingStart);

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'salle_id' => [
                        "Cette salle est déjà occupée entre "
                        . $existingStart->format('H:i')
                        . " et "
                        . $existingEnd->format('H:i')
                        . " pour l'événement \"{$event->titre}\"."
                    ],
                ]);
            }
        }
    }

    private function getEventImageUrl(int $eventId): ?string
    {
        $image = FileMeta::query()
            ->where('related_type', 'Event')
            ->where('related_id', (string) $eventId)
            ->where('mime_type', 'like', 'image/%')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $image) {
            return null;
        }

        $version = urlencode((string) ($image->updated_at ?? $image->created_at ?? now()));

        return url("/api/files/{$image->_id}/content?v={$version}");
    }
}
