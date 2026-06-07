<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
        private StatRecorder $statRecorder,
    ) {
    }

    public function index()
    {
        return response()->json(
            Ticket::with('reservation.event')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'type' => 'nullable|string|max:50',
            'prix' => 'required|numeric|min:0',
            'statut' => 'nullable|in:valide,utilisé,annulé',
        ]);

        $reservation = Reservation::findOrFail($data['reservation_id']);

        $ticket = Ticket::create([
            'reservation_id' => $reservation->id,
            'code' => strtoupper(Str::random(10)),
            'type' => $data['type'] ?? 'standard',
            'prix' => $data['prix'],
            'statut' => $data['statut'] ?? 'valide',
        ]);

        $this->activityLogger->log($request->user(), 'create', "Création du billet {$ticket->code}", $request, 'Ticket', $ticket->id);
        $this->statRecorder->record('ticket_sold', 'Ticket', $ticket->id, (float) $ticket->prix, ['reservation_id' => $reservation->id]);

        return response()->json($ticket->load('reservation.event'), 201);
    }

    public function show(Ticket $ticket)
    {
        return response()->json($ticket->load('reservation.event'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'type' => 'nullable|string|max:50',
            'prix' => 'nullable|numeric|min:0',
            'statut' => 'nullable|in:valide,utilisé,annulé',
        ]);

        $ticket->update($data);

        $this->activityLogger->log($request->user(), 'update', "Mise à jour du billet {$ticket->code}", $request, 'Ticket', $ticket->id);

        return response()->json($ticket->load('reservation.event'));
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $code = $ticket->code;
        $ticket->delete();

        $this->activityLogger->log($request->user(), 'delete', "Suppression du billet {$code}", $request, 'Ticket', $ticket->id);

        return response()->json([
            'message' => 'Billet supprimé avec succès'
        ]);
    }
}
