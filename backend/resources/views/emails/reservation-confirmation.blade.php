<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmation de réservation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px;">
            <h2 style="color: #1d4ed8; margin-top: 0;">Réservation confirmée ✅</h2>

            <p>Bonjour {{ $reservation->nom_client }},</p>

            <p>
                Votre réservation pour l'événement <strong>{{ $reservation->event->titre }}</strong>
                a bien été enregistrée. Voici le récapitulatif :
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Événement</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">{{ $reservation->event->titre }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Date &amp; heure</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">{{ $reservation->event->date_event }} à {{ $reservation->event->heure }}</td>
                </tr>
                @if ($reservation->event->salle)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Salle</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">{{ $reservation->event->salle->nom }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Nombre de places</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">{{ $reservation->nombre_places }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Référence</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">#{{ $reservation->id }}</td>
                </tr>
            </table>

            <p>
                Votre billet au format PDF est joint à cet email : présentez-le (imprimé ou
                sur votre téléphone) à l'entrée de l'événement pour accéder à la salle.
            </p>

            <p style="margin-top: 32px; color: #6b7280; font-size: 13px;">
                Cet email est envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
