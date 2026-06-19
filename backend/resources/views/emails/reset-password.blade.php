<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Réinitialisation du mot de passe</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #0a0f1e; color: #e2e8f0; }
    .wrapper { max-width: 560px; margin: 0 auto; padding: 40px 20px; }
    .card {
      background: linear-gradient(135deg, #0d1b2a, #112233);
      border: 1px solid rgba(0,180,216,0.25);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .header {
      background: linear-gradient(135deg, #00b4d8, #0077b6);
      padding: 32px 36px;
      text-align: center;
    }
    .header .logo { font-size: 1.6rem; font-weight: 900; color: #fff; letter-spacing: -0.02em; }
    .header .logo span { color: #ffd166; }
    .header p { color: rgba(255,255,255,0.85); margin-top: 6px; font-size: 0.95rem; }
    .body { padding: 36px; }
    .body h2 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 12px; }
    .body p { color: rgba(255,255,255,0.72); line-height: 1.65; font-size: 0.95rem; margin-bottom: 16px; }
    .btn-wrap { text-align: center; margin: 28px 0; }
    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #00b4d8, #0096c7);
      color: #fff !important;
      text-decoration: none;
      padding: 14px 36px;
      border-radius: 10px;
      font-weight: 800;
      font-size: 1rem;
      letter-spacing: 0.02em;
      box-shadow: 0 6px 20px rgba(0,180,216,0.4);
    }
    .link-fallback {
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 0.78rem;
      color: rgba(255,255,255,0.45);
      word-break: break-all;
      margin-top: 8px;
    }
    .warning {
      background: rgba(251,191,36,0.1);
      border: 1px solid rgba(251,191,36,0.3);
      border-radius: 8px;
      padding: 12px 16px;
      color: #fbbf24;
      font-size: 0.85rem;
      margin-top: 16px;
    }
    .footer {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding: 20px 36px;
      text-align: center;
      color: rgba(255,255,255,0.3);
      font-size: 0.78rem;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="header">
        <div class="logo">🎟 Eventify</div>
        <p>Plateforme de gestion d'événements</p>
      </div>

      <div class="body">
        <h2>🔑 Réinitialisation du mot de passe</h2>
        <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.</p>

        <div class="btn-wrap">
          <a href="{{ $resetUrl }}" class="btn">Réinitialiser mon mot de passe</a>
        </div>

        <p style="font-size:0.85rem;">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
        <div class="link-fallback">{{ $resetUrl }}</div>

        <div class="warning">
          ⚠️ Ce lien est valable <strong>60 minutes</strong>. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email — votre mot de passe reste inchangé.
        </div>
      </div>

      <div class="footer">
        © {{ date('Y') }} Eventify — Cet email a été envoyé automatiquement, ne pas répondre.
      </div>
    </div>
  </div>
</body>
</html>
