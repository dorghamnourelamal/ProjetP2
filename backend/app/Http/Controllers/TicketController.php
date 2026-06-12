<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use App\Services\StatRecorder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
        $tickets = Ticket::with('reservation.event.salle')->latest()->get();

        $tickets->transform(function (Ticket $ticket) {
            return $this->attachQrData($ticket);
        });

        return response()->json($tickets);
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

        $this->activityLogger->log(
            $request->user(),
            'create',
            "Création du billet {$ticket->code}",
            $request,
            'Ticket',
            $ticket->id
        );

        $this->statRecorder->record(
            'ticket_sold',
            'Ticket',
            $ticket->id,
            (float) $ticket->prix,
            ['reservation_id' => $reservation->id]
        );

        $ticket->load('reservation.event.salle');

        return response()->json($this->attachQrData($ticket), 201);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('reservation.event.salle');

        return response()->json($this->attachQrData($ticket));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'statut' => 'required|in:valide,utilisé,annulé',
        ]);

        $ticket->update($data);

        $this->activityLogger->log(
            $request->user(),
            'update',
            "Mise à jour du billet {$ticket->code}",
            $request,
            'Ticket',
            $ticket->id
        );

        $ticket->load('reservation.event.salle');

        return response()->json($this->attachQrData($ticket));
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $code = $ticket->code;
        $ticketId = $ticket->id;

        $ticket->delete();

        $this->activityLogger->log(
            $request->user(),
            'delete',
            "Suppression du billet {$code}",
            $request,
            'Ticket',
            $ticketId
        );

        return response()->json([
            'message' => 'Billet supprimé avec succès'
        ]);
    }

    public function qrcodeByCode(string $code)
    {
        $ticket = Ticket::where('code', strtoupper($code))->firstOrFail();

        $renderer = new ImageRenderer(
            new RendererStyle(240),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $svg = $writer->writeString($this->getFrontendVerificationUrl($ticket));

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function verifyByCode(string $code)
    {
        $ticket = Ticket::with('reservation.event.salle')
            ->where('code', strtoupper($code))
            ->first();

        if (! $ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Billet introuvable.'
            ], 404);
        }

        return response()->json($this->buildVerificationPayload($ticket));
    }

    public function useByCode(Request $request, string $code)
    {
        $ticket = Ticket::with('reservation.event.salle')
            ->where('code', strtoupper($code))
            ->first();

        if (! $ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Billet introuvable.'
            ], 404);
        }

        if ($ticket->statut !== 'valide') {
            return response()->json(
                array_merge($this->buildVerificationPayload($ticket), [
                    'message' => $ticket->statut === 'utilisé'
                        ? 'Ce billet a déjà été utilisé.'
                        : 'Ce billet n’est pas valide.'
                ]),
                409
            );
        }

        $ticket->update([
            'statut' => 'utilisé',
        ]);

        $this->activityLogger->log(
            $request->user(),
            'update',
            "Validation d'entrée du billet {$ticket->code}",
            $request,
            'Ticket',
            $ticket->id
        );

        $ticket->load('reservation.event.salle');

        return response()->json(
            array_merge($this->buildVerificationPayload($ticket), [
                'message' => 'Entrée validée avec succès. Le billet est maintenant marqué comme utilisé.'
            ])
        );
    }

    private function attachQrData(Ticket $ticket): Ticket
    {
        $ticket->qr_code_url = url("/api/tickets/qrcode/{$ticket->code}");
        $ticket->verification_url = $this->getFrontendVerificationUrl($ticket);

        return $ticket;
    }

    private function getFrontendVerificationUrl(Ticket $ticket): string
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:4200'), '/');

        return "{$frontendUrl}/tickets/verify/{$ticket->code}";
    }

    private function buildVerificationPayload(Ticket $ticket): array
    {
        $message = match ($ticket->statut) {
            'valide' => 'Billet valide.',
            'utilisé' => 'Billet déjà utilisé.',
            'annulé' => 'Billet annulé.',
            default => 'Statut inconnu.',
        };

        return [
            'valid' => $ticket->statut === 'valide',
            'message' => $message,
            'code' => $ticket->code,
            'statut' => $ticket->statut,
            'type' => $ticket->type,
            'prix' => $ticket->prix,
            'event' => [
                'titre' => $ticket->reservation?->event?->titre,
                'date_event' => $ticket->reservation?->event?->date_event,
                'heure' => $ticket->reservation?->event?->heure,
                'heure_fin' => $ticket->reservation?->event?->heure_fin,
                'salle' => $ticket->reservation?->event?->salle?->nom,
            ],
            'reservation' => [
                'id' => $ticket->reservation?->id,
                'nom_client' => $ticket->reservation?->nom_client,
                'email_client' => $ticket->reservation?->email_client,
                'nombre_places' => $ticket->reservation?->nombre_places,
            ],
        ];
    }
}
