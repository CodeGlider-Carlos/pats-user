<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $estado === 'vencido' ? 'Tu pasaporte PATS está vencido' : 'Tu pasaporte PATS está por vencer' }}</title>
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
        .msg  { font-size:14.5px; color:#475569; line-height:1.7; margin-bottom:24px; }
        .info-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; margin-bottom:24px; }
        .info-row { display:flex; justify-content:space-between; font-size:13.5px; padding:6px 0; }
        .info-row span:first-child { color:#64748b; }
        .info-row span:last-child { font-weight:700; color:#1e293b; }
        .btn-wrap { text-align:center; margin-bottom:28px; }
        .btn  { display:inline-block; background:linear-gradient(135deg,#083dff 0%,#006fff 50%,#12d8ca 100%); color:#fff !important; text-decoration:none; font-size:14px; font-weight:700; letter-spacing:.05em; padding:14px 36px; border-radius:10px; }
        .warning { display:flex; align-items:flex-start; gap:10px; background:#fef2f2; border:1.5px solid #fca5a5; border-radius:10px; padding:12px 16px; font-size:13px; color:#991b1b; margin-bottom:8px; }
        .footer { padding:24px 40px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center; font-size:12px; color:#94a3b8; line-height:1.7; }
        .footer a { color:#3b74f5; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="head">
        <div class="head__icon">{{ $estado === 'vencido' ? '⚠️' : '⏰' }}</div>
        <div class="head__logo">PATS · Pasaporte a tu Salud</div>
        <h1 class="head__title">{{ $estado === 'vencido' ? 'Tu pasaporte ha expirado' : 'Tu pasaporte vence pronto' }}</h1>
        <p class="head__sub">
            {{ $estado === 'vencido' ? 'Renueva ahora para recuperar tus beneficios.' : 'Renueva antes de perder tus beneficios.' }}
        </p>
    </div>

    <div class="body">

        <p class="greeting">Hola, {{ $nombre }}:</p>

        <p class="msg">
            @if($estado === 'vencido')
                Tu pasaporte PATS venció {{ $fechaVencimiento ? 'el ' . $fechaVencimiento : '' }} y dejaste de tener acceso a tus beneficios.
                Renueva tu membresía para reactivarlo.
            @else
                Tu pasaporte PATS vence {{ $fechaVencimiento ? 'el ' . $fechaVencimiento : 'pronto' }}.
                Renueva con tiempo para que no pierdas el acceso a tus beneficios.
            @endif
        </p>

        <div class="info-card">
            @if($fechaVencimiento)
            <div class="info-row"><span>Vigencia</span><span>{{ $fechaVencimiento }}</span></div>
            @endif
            <div class="info-row">
                <span>{{ $estado === 'vencido' ? 'Venció hace' : 'Vence en' }}</span>
                <span>{{ abs($diasRestantes) }} {{ abs($diasRestantes) === 1 ? 'día' : 'días' }}</span>
            </div>
            @if($mesesVencidos > 0)
            <div class="info-row"><span>Meses vencidos</span><span>{{ $mesesVencidos }}</span></div>
            <div class="info-row"><span>Recargo acumulado</span><span>${{ number_format($recargoAcumulado, 2) }} MXN</span></div>
            @endif
        </div>

        <div class="btn-wrap">
            <a href="{{ $loginUrl }}" class="btn">Renovar mi pasaporte</a>
        </div>

        @if($estado === 'vencido')
        <div class="warning">
            <span>⚠️</span>
            <span>Mientras tu pasaporte esté vencido no podrás usar tus beneficios PATS.</span>
        </div>
        @endif

    </div>

    <div class="footer">
        PATS · Pasaporte a tu Salud<br>
        Este correo fue generado automáticamente, por favor no lo respondas.<br>
        ¿Dudas? Escríbenos a <a href="mailto:soporte@pats.mx">soporte@pats.mx</a>
    </div>

</div>
</body>
</html>
