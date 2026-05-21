<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud recibida · PATS</title>
    <style>
        body { margin:0; padding:0; background:#f0f5ff; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
        .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(59,116,245,.10); }
        .head { background:linear-gradient(135deg,#2558e0 0%,#1a3fb5 100%); padding:36px 40px 32px; text-align:center; }
        .head__logo { font-size:22px; font-weight:800; color:#fff; letter-spacing:-.02em; margin-bottom:6px; }
        .head__sub  { font-size:13px; color:rgba(255,255,255,.7); }
        .success-ring { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,.18); border:2px solid rgba(255,255,255,.4); display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 16px; }
        .head__title { font-size:20px; font-weight:800; color:#fff; margin:0 0 6px; }
        .head__ref { display:inline-block; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:6px 18px; font-family:monospace; font-size:14px; font-weight:700; color:#fff; margin-top:10px; letter-spacing:.04em; }
        .body { padding:36px 40px; }
        .greeting { font-size:16px; font-weight:700; color:#1e293b; margin-bottom:10px; }
        .msg { font-size:14.5px; color:#475569; line-height:1.7; margin-bottom:24px; }
        .status-badge { display:inline-flex; align-items:center; gap:8px; background:#fffbeb; border:1.5px solid #fcd34d; border-radius:100px; padding:7px 18px; font-size:13px; font-weight:700; color:#92400e; margin-bottom:28px; }
        .dot { width:8px; height:8px; border-radius:50%; background:#f59e0b; animation:pulse 1.6s infinite; display:inline-block; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
        .steps { margin-bottom:28px; }
        .steps__title { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin-bottom:14px; }
        .step { display:flex; align-items:flex-start; gap:14px; margin-bottom:14px; }
        .step__dot { width:32px; height:32px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; }
        .step__dot--done { background:#ecfdf5; color:#10b981; border:1.5px solid #6ee7b7; }
        .step__dot--wait { background:#fffbeb; color:#f59e0b; border:1.5px solid #fcd34d; }
        .step__dot--pending { background:#f8fafc; color:#94a3b8; border:1.5px solid #e2e8f0; }
        .step__text__head { font-size:13.5px; font-weight:700; color:#334155; }
        .step__text__sub { font-size:12px; color:#94a3b8; margin-top:2px; }
        .divider { border:none; border-top:1px solid #e2e8f0; margin:24px 0; }
        .footer { padding:24px 40px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center; font-size:12px; color:#94a3b8; line-height:1.7; }
        .footer a { color:#3b74f5; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">

    {{-- Cabecera --}}
    <div class="head">
        <div class="success-ring">✓</div>
        <div class="head__logo">PATS · Red de Distribución</div>
        <h1 class="head__title">¡Solicitud recibida!</h1>
        <p class="head__sub">Tu información fue enviada correctamente.</p>
        <div class="head__ref">{{ $referencia }}</div>
    </div>

    {{-- Cuerpo --}}
    <div class="body">

        <p class="greeting">Hola, {{ $nombreSolicitante }}:</p>

        <p class="msg">
            Hemos recibido tu solicitud de alta como distribuidor PATS. Nuestro equipo revisará
            tu documentación e información y nos pondremos en contacto contigo en un plazo de
            <strong>24 a 48 horas hábiles</strong>.
        </p>

        <div class="status-badge">
            <span class="dot"></span>
            En revisión · Validando información
        </div>

        {{-- Pasos siguientes --}}
        <div class="steps">
            <div class="steps__title">¿Qué sigue?</div>

            <div class="step">
                <div class="step__dot step__dot--done">✓</div>
                <div class="step__text">
                    <div class="step__text__head">Solicitud enviada</div>
                    <div class="step__text__sub">Tus documentos e información fueron recibidos correctamente.</div>
                </div>
            </div>

            <div class="step">
                <div class="step__dot step__dot--wait">&#128269;</div>
                <div class="step__text">
                    <div class="step__text__head">Revisión y validación</div>
                    <div class="step__text__sub">Nuestro equipo revisa tu documentación (24–48 horas hábiles).</div>
                </div>
            </div>

            <div class="step">
                <div class="step__dot step__dot--pending">&#128222;</div>
                <div class="step__text">
                    <div class="step__text__head">Contacto de confirmación</div>
                    <div class="step__text__sub">Te avisaremos por correo y/o teléfono con el resultado.</div>
                </div>
            </div>

            <div class="step">
                <div class="step__dot step__dot--pending">&#129309;</div>
                <div class="step__text">
                    <div class="step__text__head">Activación como distribuidor</div>
                    <div class="step__text__sub">Una vez aprobado, te damos acceso completo a la plataforma.</div>
                </div>
            </div>
        </div>

        <hr class="divider">

        <p style="font-size:13px;color:#64748b;line-height:1.6;">
            Guarda este correo como comprobante. Tu folio de referencia es
            <strong style="font-family:monospace;color:#2558e0;">{{ $referencia }}</strong>.
            Si tienes alguna duda, contáctanos respondiendo este correo.
        </p>

    </div>

    {{-- Pie --}}
    <div class="footer">
        PATS · Red de Distribución Médica<br>
        Este correo fue generado automáticamente, por favor no lo respondas directamente.<br>
        ¿Dudas? Escríbenos a <a href="mailto:soporte@pats.mx">soporte@pats.mx</a>
    </div>

</div>
</body>
</html>
