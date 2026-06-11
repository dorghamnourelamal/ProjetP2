<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Mongo\FileMeta;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use Illuminate\Http\Request;

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
            'places_disponibles' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'salle_id' => 'required|exists:salles,id',
        ]);

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
            'places_disponibles' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'salle_id' => 'required|exists:salles,id',
        ]);

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

        $event->delete();

        $this->activityLogger->log(
            $request->user(),
            'delete',
            "Suppression de l'événement '{$titre}'",
            $request,
            'Event',
            $eventId
        );

        return response()->json([
            'message' => 'Événement supprimé avec succès'
        ]);
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
