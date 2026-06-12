<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Annulation d'événement</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 580px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div style="background: #dc2626; padding: 28px 32px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">❌ Événement annulé</h1>
            <p style="color: #fecaca; margin: 6px 0 0; font-size: 14px;">Nous sommes désolés de vous informer de cette annulation.</p>
        </div>

        <!-- Body -->
        <div style="padding: 32px;">
            <p>Bonjour <strong>{{ $reservation->nom_client }}</strong>,</p>

            <p>
                Nous vous informons que l'événement auquel vous étiez inscrit a été
                <strong style="color: #dc2626;">annulé</strong>.
            </p>

            <!-- Event card -->
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px; margin: 24px 0;">
                <h2 style="color: #991b1b; margin: 0 0 12px; font-size: 18px;">{{ $event->titre }}</h2>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280; width: 35%;">Date</td>
                        <td style="padding: 6px 0; font-weight: 600;">
                            {{ \Carbon\Carbon::parse($event->date_event)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Heure</td>
                        <td style="padding: 6px 0; font-weight: 600;">{{ $event->heure }} - {{ $event->heure_fin }}</td>
                    </tr>
                    @if($event->salle)
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Salle</td>
                        <td style="padding: 6px 0; font-weight: 600;">{{ $event->salle->nom }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Vos places</td>
                        <td style="padding: 6px 0; font-weight: 600;">{{ $reservation->nombre_places }} place(s)</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Réservation</td>
                        <td style="padding: 6px 0; font-weight: 600;">#{{ $reservation->id }}</td>
                    </tr>
                </table>
            </div>

            <p>
                Si vous avez effectué un paiement, un remboursement intégral sera traité dans les meilleurs délais.
                Pour toute question, contactez-nous en répondant directement à cet email.
            </p>

            <p>Nous nous excusons sincèrement pour la gêne occasionnée.</p>

            <p style="margin-top: 32px;">
                Cordialement,<br>
                <strong>L'équipe Eventify</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f9fafb; padding: 16px 32px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af;">
            Cet email vous a été envoyé car vous aviez une réservation pour cet événement.
        </div>

    </div>
</body>
</html>
