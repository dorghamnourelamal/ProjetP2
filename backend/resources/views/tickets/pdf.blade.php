<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Billet de réservation #{{ $reservation->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; margin: 0; padding: 0; }
        .ticket {
            border: 2px dashed #1d4ed8;
            border-radius: 12px;
            margin: 30px;
            padding: 24px;
        }
        .ticket__header { text-align: center; margin-bottom: 16px; }
        .ticket__header h1 { color: #1d4ed8; margin: 0; font-size: 22px; }
        .ticket__header p { margin: 4px 0 0; color: #6b7280; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 6px 4px; font-size: 13px; }
        td.label { color: #6b7280; width: 40%; }
        td.value { font-weight: bold; text-align: right; }
        .ticket__code {
            text-align: center;
            margin-top: 18px;
            font-size: 20px;
            letter-spacing: 4px;
            font-weight: bold;
            color: #1d4ed8;
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
            <p>Gestion d'Événements — à présenter à l'entrée</p>
        </div>

        <table>
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
                <td class="value">{{ $reservation->event->salle->nom }}{{ $reservation->event->salle->adresse ? ' — ' . $reservation->event->salle->adresse : '' }}</td>
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

        <p class="ticket__code">RES-{{ str_pad((string) $reservation->id, 6, '0', STR_PAD_LEFT) }}</p>

        <div class="ticket__footer">
            Référence de réservation #{{ $reservation->id }} — Document généré automatiquement, à conserver jusqu'à la fin de l'événement.
        </div>
    </div>
</body>
</html>
