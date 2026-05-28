<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de soporte · PATS</title>
    <style>
        body { margin:0; padding:0; background:#f0f5ff; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
        .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(59,116,245,.10); }
        .head { background:linear-gradient(135deg,#2558e0 0%,#1a3fb5 100%); padding:36px 40px 32px; text-align:center; }
        .head__icon { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,.18); border:2px solid rgba(255,255,255,.4); display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 16px; }
        .head__logo { font-size:13px; color:rgba(255,255,255,.7); margin-bottom:8px; }
        .head__title { font-size:20px; font-weight:800; color:#fff; margin:0; }
        .body { padding:36px 40px; }
        .label { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin-bottom:6px; }
        .value { font-size:15px; color:#1e293b; font-weight:500; margin-bottom:24px; }
        .message-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px 20px; font-size:14.5px; color:#475569; line-height:1.75; white-space:pre-wrap; }
        .divider { border:none; border-top:1px solid #e2e8f0; margin:28px 0; }
        .footer { padding:24px 40px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center; font-size:12px; color:#94a3b8; line-height:1.7; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="head">
        <div class="head__icon">&#128172;</div>
        <div class="head__logo">PATS · Pasaporte a tu Salud</div>
        <h1 class="head__title">Nuevo mensaje de soporte</h1>
    </div>

    <div class="body">

        <div class="label">Nombre</div>
        <div class="value">{{ $contacto->nombre }}</div>

        <div class="label">Correo del solicitante</div>
        <div class="value">
            <a href="mailto:{{ $contacto->correo }}" style="color:#2558e0;text-decoration:none;">{{ $contacto->correo }}</a>
        </div>

        <div class="label">Fecha de envío</div>
        <div class="value">{{ $contacto->created_at->format('d/m/Y H:i') }}</div>

        <hr class="divider">

        <div class="label">¿Cómo podemos ayudarte?</div>
        <div class="message-box">{{ $contacto->mensaje }}</div>

    </div>

    <div class="footer">
        PATS · Pasaporte a tu Salud<br>
        Este mensaje fue enviado desde el formulario de soporte en la página de inicio.
    </div>

</div>
</body>
</html>
