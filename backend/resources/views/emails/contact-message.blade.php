<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px;">
            <h2 style="color: #1d4ed8; margin-top: 0;">Nouveau message de contact ✉️</h2>

            <p>
                Vous avez reçu un nouveau message via le formulaire de contact du site
                <strong>Eventify</strong> :
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; width: 30%;">Nom</td>
                    <td style="padding: 6px 0; font-weight: 600;">{{ $name }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Email</td>
                    <td style="padding: 6px 0; font-weight: 600;">{{ $email }}</td>
                </tr>
            </table>

            <p style="color: #6b7280; margin-bottom: 4px;">Message :</p>
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; white-space: pre-wrap;">{{ $messageBody }}</div>

            <p style="margin-top: 32px; color: #6b7280; font-size: 13px;">
                Vous pouvez répondre directement à cet email : la réponse sera envoyée à {{ $email }}.
            </p>
        </div>
    </div>
</body>
</html>
