<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Billet de réservation #{{ $reservation->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; margin: 0; padding: 0; }

        .ticket {
            border: 2px dashed #0891b2;
            border-radius: 12px;
            margin: 24px;
            padding: 24px;
        }

        .ticket__header { text-align: center; margin-bottom: 16px; }
        .ticket__header h1 { color: #0891b2; margin: 0; font-size: 22px; }
        .ticket__header p { margin: 4px 0 0; color: #6b7280; font-size: 12px; }

        table.info { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.info td { padding: 6px 4px; font-size: 13px; }
        table.info td.label { color: #6b7280; width: 40%; }
        table.info td.value { font-weight: bold; text-align: right; }

        .divider {
            border: none;
            border-top: 1px dashed #d1d5db;
            margin: 20px 0;
        }

        .tickets-section h2 {
            font-size: 14px;
            color: #374151;
            margin: 0 0 12px;
        }

        .ticket-item {
            display: table;
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            background: #f9fafb;
        }

        .ticket-item__left {
            display: table-cell;
            vertical-align: middle;
            width: 75%;
        }

        .ticket-item__right {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            width: 25%;
        }

        .ticket-item__code {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #0891b2;
            margin-bottom: 4px;
        }

        .ticket-item__type {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .ticket-item__prix {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            margin-top: 6px;
        }

        .qr-img {
            width: 110px;
            height: 110px;
        }

        .qr-label {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 4px;
            text-align: center;
        }

        .ticket__footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="ticket">

        <div class="ticket__header">
            <h1>🎫 Billet de réservation</h1>
            <p>Eventify — à présenter à l'entrée</p>
        </div>

        <table class="info">
            <tr>
                <td class="label">Événement</td>
                <td class="value">{{ $reservation->event->titre }}</td>
            </tr>
            <tr>
                <td class="label">Date &amp; heure</td>
                <td class="value">{{ $reservation->event->date_event }} à {{ $reservation->event->heure }}</td>
            </tr>
            @if ($reservation->event->salle)
            <tr>
                <td class="label">Salle</td>
                <td class="value">{{ $reservation->event->salle->nom }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Titulaire</td>
                <td class="value">{{ $reservation->nom_client }}</td>
            </tr>
            <tr>
                <td class="label">Nombre de places</td>
                <td class="value">{{ $reservation->nombre_places }}</td>
            </tr>
        </table>

        <hr class="divider">

        <div class="tickets-section">
            <h2>Vos billets ({{ $reservation->tickets->count() }})</h2>

            @foreach ($reservation->tickets as $ticket)
            <div class="ticket-item">
                <div class="ticket-item__left">
                    <div class="ticket-item__code">{{ $ticket->code }}</div>
                    <div class="ticket-item__type">Billet {{ $loop->iteration }} / {{ $loop->count }} — {{ $ticket->type }}</div>
                    <div class="ticket-item__prix">
                        {{ $ticket->prix > 0 ? number_format($ticket->prix, 2, ',', ' ') . ' €' : 'Gratuit' }}
                    </div>
                </div>
                <div class="ticket-item__right">
                    @if(isset($qrCodes[$ticket->code]))
                    <img
                        class="qr-img"
                        src="data:image/svg+xml;base64,{{ $qrCodes[$ticket->code] }}"
                        alt="QR Code {{ $ticket->code }}"
                    />
                    <div class="qr-label">Scanner pour vérifier</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="ticket__footer">
            Réservation #{{ $reservation->id }} — Document généré automatiquement, à conserver jusqu'à la fin de l'événement.
        </div>

    </div>
</body>
</html>
