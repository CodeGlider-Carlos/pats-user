{{-- resources/views/pats/solicitud_distribuidor_success.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud recibida · PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --page:       #f0f5ff;
            --surface:    #ffffff;
            --blue-100:   #dde8ff;
            --blue-200:   #c3d5fe;
            --blue-500:   #3b74f5;
            --blue-600:   #2558e0;
            --slate-400:  #94a3b8;
            --slate-500:  #64748b;
            --slate-600:  #475569;
            --slate-700:  #334155;
            --slate-800:  #1e293b;
            --success:    #10b981;
            --success-bg: #ecfdf5;
            --success-dk: #059669;
            --warning:    #f59e0b;
            --border:     rgba(59,116,245,.14);
            --shadow-md:  0 4px 16px rgba(59,116,245,.10),0 2px 6px rgba(0,0,0,.05);
            --shadow-lg:  0 12px 40px rgba(59,116,245,.12),0 4px 12px rgba(0,0,0,.06);
            --radius:     16px;
            --radius-sm:  10px;
            --font:       'Plus Jakarta Sans', sans-serif;
            --mono:       'JetBrains Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font);
            background: var(--page);
            color: var(--slate-800);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar__brand {
            display: flex; align-items: center; gap: 10px;
            font-size: 17px; font-weight: 800;
            color: var(--blue-600); letter-spacing: -.02em;
        }
        .topbar__brand i { font-size: 22px; }
        .topbar__secure {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; color: var(--success);
            background: var(--success-bg); padding: 5px 12px; border-radius: 100px;
        }

        /* ── Layout ── */
        .page {
            max-width: 660px;
            margin: 0 auto;
            padding: 60px 24px 80px;
        }

        /* ── Card principal ── */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: 0 0 0 1px var(--border), var(--shadow-lg);
            overflow: hidden;
        }

        /* ── Cabecera verde ── */
        .card__head {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-bottom: 1px solid #a7f3d0;
            padding: 40px 32px 32px;
            text-align: center;
        }
        .ring {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: var(--success);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 0 0 12px #d1fae5, 0 4px 20px rgba(16,185,129,.3);
            animation: pop .45s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .ring i { font-size: 32px; color: #fff; }
        .card__title {
            font-size: 22px; font-weight: 800; color: #065f46;
            letter-spacing: -.02em; margin-bottom: 6px;
        }
        .card__sub {
            font-size: 14px; color: #047857; line-height: 1.5;
        }

        /* ── Folio ── */
        .folio {
            display: inline-flex; align-items: center; gap: 8px;
            margin-top: 18px;
            background: #fff;
            border: 1.5px solid #6ee7b7;
            border-radius: 8px;
            padding: 8px 18px;
            font-family: var(--mono);
            font-size: 13.5px; font-weight: 600; color: #065f46;
        }
        .folio i { font-size: 15px; color: var(--success); }

        /* ── Cuerpo ── */
        .card__body { padding: 28px 32px 32px; }

        /* ── Badge de estatus ── */
        .status-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fffbeb; border: 1.5px solid #fcd34d;
            border-radius: 100px; padding: 7px 16px;
            font-size: 13px; font-weight: 700; color: #92400e;
            margin-bottom: 24px;
        }
        .status-badge .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--warning);
            animation: pulse 1.6s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .5; transform: scale(.75); }
        }

        /* ── Mensaje principal ── */
        .msg {
            font-size: 15px; line-height: 1.65; color: var(--slate-600);
            margin-bottom: 24px;
        }
        .msg strong { color: var(--slate-800); }

        /* ── Info del solicitante ── */
        .info-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 12px; margin-bottom: 28px;
        }
        @media (max-width: 500px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item {
            background: var(--page);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
        }
        .info-item__label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: var(--slate-400); margin-bottom: 4px;
        }
        .info-item__val {
            font-size: 14px; font-weight: 600; color: var(--slate-700);
            word-break: break-all;
        }
        .info-item__val.empty { color: var(--slate-400); font-style: italic; font-weight: 400; }

        /* ── Timeline ── */
        .timeline { margin-bottom: 28px; }
        .timeline__title {
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .07em; color: var(--slate-400); margin-bottom: 14px;
        }
        .tl-item {
            display: flex; align-items: flex-start; gap: 14px;
            margin-bottom: 14px;
        }
        .tl-item:last-child { margin-bottom: 0; }
        .tl-dot {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 14px;
        }
        .tl-dot.done  { background: var(--success-bg); color: var(--success); border: 1.5px solid #6ee7b7; }
        .tl-dot.wait  { background: #fffbeb; color: var(--warning); border: 1.5px solid #fcd34d; }
        .tl-dot.pending { background: var(--page); color: var(--slate-400); border: 1.5px solid var(--border); }
        .tl-text {}
        .tl-text__head { font-size: 13.5px; font-weight: 700; color: var(--slate-700); }
        .tl-text__sub  { font-size: 12px; color: var(--slate-400); margin-top: 2px; }

        /* ── Footer ── */
        .footer-note {
            border-top: 1px solid var(--border);
            padding-top: 20px;
            font-size: 12px; color: var(--slate-400); text-align: center; line-height: 1.6;
        }
        .footer-note a { color: var(--blue-500); text-decoration: none; }
        .footer-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="topbar__brand">
            <i class="mdi mdi-hospital-box"></i>
            PATS
        </div>
        <div class="topbar__secure">
            <i class="mdi mdi-shield-check"></i>
            Información segura
        </div>
    </header>

    <main class="page">
        <div class="card">

            {{-- Cabecera --}}
            <div class="card__head">
                <div class="ring"><i class="mdi mdi-check-bold"></i></div>
                <h1 class="card__title">¡Solicitud recibida!</h1>
                <p class="card__sub">Tu información fue enviada correctamente y está en proceso de revisión.</p>
                @if($ref)
                <div class="folio">
                    <i class="mdi mdi-identifier"></i>
                    {{ $ref }}
                </div>
                @endif
            </div>

            {{-- Cuerpo --}}
            <div class="card__body">

                {{-- Estatus --}}
                <div class="status-badge">
                    <span class="dot"></span>
                    En revisión · Validando información
                </div>

                {{-- Mensaje --}}
                <p class="msg">
                    Hola<strong>{{ $nombre ? ' ' . e($nombre) . '' : '' }}</strong>,
                    nos pondremos en contacto contigo <strong>muy pronto</strong>.
                    Tu información está siendo validada por nuestro equipo y recibirás
                    una respuesta en un plazo de <strong>24 a 48 horas hábiles</strong>.
                </p>

                {{-- Info del solicitante --}}
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item__label">Titular</div>
                        <div class="info-item__val {{ $nombre ? '' : 'empty' }}">
                            {{ $nombre ?: '—' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">Correo</div>
                        <div class="info-item__val {{ $correo ? '' : 'empty' }}">
                            {{ $correo ?: '—' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">Estatus</div>
                        <div class="info-item__val" style="color:var(--warning);">En revisión</div>
                    </div>
                    @if($ref)
                    <div class="info-item">
                        <div class="info-item__label">Folio</div>
                        <div class="info-item__val" style="font-family:var(--mono);font-size:12.5px;">
                            {{ $ref }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Timeline --}}
                <div class="timeline">
                    <div class="timeline__title">¿Qué sigue?</div>

                    <div class="tl-item">
                        <div class="tl-dot done"><i class="mdi mdi-check"></i></div>
                        <div class="tl-text">
                            <div class="tl-text__head">Solicitud enviada</div>
                            <div class="tl-text__sub">Tus documentos e información fueron recibidos.</div>
                        </div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-dot wait"><i class="mdi mdi-magnify"></i></div>
                        <div class="tl-text">
                            <div class="tl-text__head">Revisión y validación</div>
                            <div class="tl-text__sub">Nuestro equipo revisa tu documentación (24–48 h).</div>
                        </div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-dot pending"><i class="mdi mdi-phone-outline"></i></div>
                        <div class="tl-text">
                            <div class="tl-text__head">Contacto de confirmación</div>
                            <div class="tl-text__sub">Te avisaremos por correo y/o teléfono con el resultado.</div>
                        </div>
                    </div>

                    <div class="tl-item">
                        <div class="tl-dot pending"><i class="mdi mdi-handshake-outline"></i></div>
                        <div class="tl-text">
                            <div class="tl-text__head">Activación como distribuidor</div>
                            <div class="tl-text__sub">Una vez aprobado, te damos acceso completo a la plataforma.</div>
                        </div>
                    </div>
                </div>

                {{-- Pie --}}
                <div class="footer-note">
                    ¿Tienes dudas? Escríbenos a
                    <a href="mailto:soporte@pats.mx">soporte@pats.mx</a>
                    o llámanos al <strong>(55) 1234-5678</strong>.<br>
                    Guarda tu folio de referencia: <strong>{{ $ref ?: '—' }}</strong>
                </div>

            </div>{{-- /card__body --}}
        </div>{{-- /card --}}
    </main>

</body>
</html>