<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
        return response()->json(
            Event::with('salle')->get()
        );
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

        $this->activityLogger->log($request->user(), 'create', "Création de l'événement '{$event->titre}'", $request, 'Event', $event->id);

        return response()->json($event, 201);
    }

    public function show(Request $request, Event $event)
    {
        $this->statRecorder->record('event_view', 'Event', $event->id);

        return response()->json(
            $event->load('salle', 'reservations')
        );
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

        $this->activityLogger->log($request->user(), 'update', "Modification de l'événement '{$event->titre}'", $request, 'Event', $event->id);

        return response()->json($event);
    }

    public function destroy(Request $request, Event $event)
    {
        $titre = $event->titre;
        $event->delete();

        $this->activityLogger->log($request->user(), 'delete', "Suppression de l'événement '{$titre}'", $request, 'Event', $event->id);

        return response()->json([
            'message' => 'Événement supprimé avec succès'
        ]);
    }
}
