<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña · PATS</title>
    <style>
        body { margin:0; padding:0; background:#f0f5ff; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
        .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(59,116,245,.10); }
        .head { background:linear-gradient(135deg,#1b1f6f 0%,#10134a 100%); padding:36px 40px 32px; text-align:center; }
        .head__icon { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.35); display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 16px; }
        .head__logo { font-size:13px; font-weight:600; color:rgba(255,255,255,.65); letter-spacing:.08em; text-transform:uppercase; margin-bottom:10px; }
        .head__title { font-size:20px; font-weight:800; color:#fff; margin:0; }
        .head__sub   { font-size:13px; color:rgba(255,255,255,.6); margin-top:6px; }
        .body  { padding:36px 40px; }
        .greeting { font-size:15.5px; font-weight:700; color:#1e293b; margin-bottom:10px; }
        .msg  { font-size:14.5px; color:#475569; line-height:1.7; margin-bottom:28px; }
        .btn-wrap { text-align:center; margin-bottom:28px; }
        .btn  { display:inline-block; background:linear-gradient(135deg,#083dff 0%,#006fff 50%,#12d8ca 100%); color:#fff !important; text-decoration:none; font-size:14px; font-weight:700; letter-spacing:.05em; padding:14px 36px; border-radius:10px; }
        .divider { border:none; border-top:1px solid #e2e8f0; margin:24px 0; }
        .url-fallback { font-size:12px; color:#94a3b8; line-height:1.7; word-break:break-all; }
        .url-fallback a { color:#3b74f5; }
        .warning { display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1.5px solid #fcd34d; border-radius:10px; padding:12px 16px; font-size:13px; color:#92400e; margin-bottom:24px; }
        .footer { padding:24px 40px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center; font-size:12px; color:#94a3b8; line-height:1.7; }
        .footer a { color:#3b74f5; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="head">
        <div class="head__icon">🔐</div>
        <div class="head__logo">PATS · Pasaporte a tu Salud</div>
        <h1 class="head__title">Restablecer contraseña</h1>
        <p class="head__sub">Recibimos una solicitud para cambiar tu contraseña.</p>
    </div>

    <div class="body">

        <p class="greeting">Hola, {{ $nombre }}:</p>

        <p class="msg">
            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta PATS.
            Haz clic en el botón de abajo para crear una nueva contraseña.
            Este enlace es válido por <strong>60 minutos</strong>.
        </p>

        <div class="btn-wrap">
            <a href="{{ $resetUrl }}" class="btn">Restablecer contraseña</a>
        </div>

        <div class="warning">
            <span>⚠️</span>
            <span>Si no solicitaste este cambio, ignora este correo. Tu contraseña actual seguirá siendo la misma.</span>
        </div>

        <hr class="divider">

        <p class="url-fallback">
            Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
            <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
        </p>

    </div>

    <div class="footer">
        PATS · Pasaporte a tu Salud<br>
        Este correo fue generado automáticamente, por favor no lo respondas.<br>
        ¿Dudas? Escríbenos a <a href="mailto:soporte@pats.mx">soporte@pats.mx</a>
    </div>

</div>
</body>
</html>
