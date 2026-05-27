<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1b1f6f">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #1b1f6f;
            --navy-d: #10134a;
            --green:  #87a924;
            --green-d:#6b8a1a;
            --blue:   #0b43e6;
            --white:  #ffffff;
            --off:    #f7f8fc;
            --muted:  #8892a4;
            --border: #dce2ee;
            --danger: #e74c3c;
            --info:   #2980b9;
            --cyan:   #00D9C8;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--navy-d);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }

        /* ── Wrapper ──────────────────────────────────────────── */
        .auth-shell {
            display: flex;
            width: 100%;
            max-width: 1060px;
            min-height: 580px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.55);
            animation: fadeUp .55s cubic-bezier(.22,.68,0,1.2) both;
        }

        /* ── LEFT — brand panel ────────────────────────────────── */
        .brand {
            flex: 1;
            position: relative;
            background: linear-gradient(150deg, var(--navy) 0%, #0e1250 55%, #090c38 100%);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-end;
            padding: 3.5rem;
            overflow: hidden;
            clip-path: polygon(0 0, 93% 0, 100% 100%, 0 100%);
        }

        /* decorative rings */
        .brand::before {
            content: '';
            position: absolute;
            top: -140px; right: -120px;
            width: 480px; height: 480px;
            border-radius: 50%;
            border: 1.5px solid rgba(135,169,36,.18);
            pointer-events: none;
        }
        .brand::after {
            content: '';
            position: absolute;
            top: -80px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 1.5px solid rgba(135,169,36,.1);
            pointer-events: none;
        }

        /* dot-grid texture */
        .brand-dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(135,169,36,.18) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* decorative cross */
        .brand-cross {
            position: absolute;
            top: 2.8rem; left: 3.5rem;
            width: 38px; height: 38px;
            opacity: .55;
        }
        .brand-cross span {
            position: absolute;
            background: linear-gradient(135deg, #083dff 0%,  #12d8ca 100%);
            border-radius: 3px;
        }
        .brand-cross span:first-child {
            width: 38px; height: 12px;
            top: 50%; left: 0;
            transform: translateY(-50%);
        }
        .brand-cross span:last-child {
            width: 12px; height: 38px;
            left: 50%; top: 0;
            transform: translateX(-50%);
        }

        .brand-logo {
            position: absolute;
            top: 2.2rem; left: 50%;
            transform: translateX(-50%);
            width: 155px;
        }
        .brand-logo img {
            width: 100%;
            filter: brightness(0) invert(1);
            opacity: .92;
        }

        /* bottom content */
        .brand-body {
            position: relative;
            z-index: 2;
        }
        .brand-tag {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(135,169,36,.18);
            border: 1px solid rgba(135,169,36,.35);
            color: var(--cyan);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .35rem .75rem;
            border-radius: 50px;
            margin-bottom: 1.2rem;
        }
        .brand-headline {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            font-weight: 500;
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 1.1rem;
            max-width: 340px;
        }
        .brand-headline em {
            background: linear-gradient(90deg, #ffffff 0%, #cfe0ff 20%, #79b5ff 46%, #1fd6c8 76%, #b8f21d 100%);
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-style: normal;
            
        }
        .brand-sub {
            font-size: .9rem;
            font-weight: 300;
            color: rgba(255,255,255,.55);
            max-width: 300px;
            line-height: 1.65;
            margin-bottom: 2.5rem;
        }
        .brand-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;

        }
        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            color: rgba(255,255,255,.75);
            font-size: .76rem;
            font-weight: 400;
            padding: .35rem .8rem;
            border-radius: 50px;
        }
        .brand-pill i {
            color: var(--cyan);
            font-size: .9rem;
        }

        /* ── RIGHT — form panel ────────────────────────────────── */
        .form-panel {
            width: 420px;
            flex-shrink: 0;
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem 3rem;
        }

        .form-kicker {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: .7rem;
        }
        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 500;
            color: var(--navy);
            margin-bottom: .45rem;
            line-height: 1.15;
        }
        .form-hint {
            font-size: .85rem;
            color: var(--muted);
            margin-bottom: 2rem;
            font-weight: 300;
        }

        /* alert */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            font-size: .84rem;
            padding: .85rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.4rem;
            line-height: 1.5;
            animation: fadeUp .3s ease both;
        }
        .alert-danger { background: #fdf3f2; color: #c0392b; border: 1px solid #f5c6c2; }
        .alert-info   { background: #eef5fb; color: #1a5f8a; border: 1px solid #b8d8ef; }
        .alert i { font-size: 1.05rem; flex-shrink: 0; margin-top: .05rem; }

        /* field groups */
        .field {
            margin-bottom: 1.25rem;
        }
        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .04em;
            color: var(--navy);
            margin-bottom: .5rem;
            text-transform: uppercase;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 1.05rem;
            pointer-events: none;
            transition: color .2s;
        }
        .input-wrap input {
            width: 100%;
            height: 50px;
            padding: 0 1rem 0 2.8rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: .93rem;
            font-weight: 400;
            color: var(--navy);
            background: var(--off);
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .input-wrap input::placeholder { color: var(--muted); }
        .input-wrap input:focus {
            border-color: var(--navy);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(27,31,111,.08);
        }
        .input-wrap input:focus + i,
        .input-wrap:focus-within i { color: var(--navy); }

        /* password toggle */
        .pass-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            font-size: 1.05rem;
            padding: 0;
            line-height: 1;
            transition: color .2s;
        }
        .pass-toggle:hover { color: var(--navy); }

        /* remember / forgot row */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            margin-top: -.1rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
            font-size: .82rem;
            color: var(--muted);
            user-select: none;
        }
        .remember input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
        }
        .forgot {
            font-size: .82rem;
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
            transition: color .2s;
        }
        .forgot:hover { color: var(--navy); }

        /* submit */
        .btn-login {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #083dff 0%, #006fff 48%, #12d8ca 100%);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .92rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            transition: background .2s, transform .15s, box-shadow .2s;
            /* box-shadow: 0 4px 18px rgba(135,169,36,.35); */
        }
        .btn-login:hover {
            background: var(--blue);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(135,169,36,.4);
        }
        .btn-login:active { transform: translateY(0); }

        /* divider */
        .form-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.8rem 0 1.4rem;
        }

        .form-footer {
            text-align: center;
            font-size: .8rem;
            color: var(--muted);
        }
        .form-footer strong { color: var(--navy); font-weight: 600; }

        /* ── Animations ────────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 780px) {
            body { padding: 0; align-items: stretch; }
            .auth-shell {
                flex-direction: column;
                border-radius: 0;
                min-height: 100dvh;
                max-width: 100%;
                box-shadow: none;
            }
            .brand {
                clip-path: none;
                min-height: 220px;
                padding: 2.2rem 2rem 2.2rem;
                align-items: center;
                justify-content: center;
            }
            .brand-logo {
                position: static;
                transform: none;
                margin-bottom: 1.5rem;
                width: 120px;
            }
            .brand-headline { font-size: 1.7rem; text-align: center; max-width: 100%; }
            .brand-sub { display: none; }
            .brand-tag { display: none; }
            .brand-cross { display: none; }
            .brand-pills { justify-content: center; }
            .form-panel {
                width: 100%;
                padding: 2.5rem 1.75rem 3rem;
                flex: 1;
            }
        }
    </style>
</head>
<body>

<div class="auth-shell">

    {{-- ── LEFT: brand panel ───────────────────────────────── --}}
    <div class="brand">
        <div class="brand-dots"></div>

        <div class="brand-cross">
            <span></span><span></span>
        </div>

        <div class="brand-logo" style="display:flex; align-items:center;">
            <img src="{{ asset('images/logo_50.png') }}" alt="" style="filter: brightness(0) invert(1); height:36px;">
            <span style="color:#fff; font-weight:300; margin:0 8px; opacity:.6; font-size:1.4rem;">|</span>
            <img src="{{ asset('images/PATS_LOGO.png') }}" alt="PATS" style="filter: brightness(0) invert(1); height:36px;">
        </div>

        <div class="brand-body">
            <div class="brand-tag">
                <i class="mdi mdi-shield-check"></i>
                Pasaporte a tu Salud
            </div>
            <h1 class="brand-headline">
                Tu bienestar,<br>
                siempre <em>protegido</em>
            </h1>
            <p class="brand-sub">
                Accede a tus servicios médicos, historial y beneficios desde un solo lugar.
            </p>
            <div class="brand-pills">
                <span class="brand-pill"><i class="mdi mdi-hospital-box"></i> Atención médica</span>
                <span class="brand-pill"><i class="mdi mdi-test-tube"></i> Estudios clínicos</span>
                <span class="brand-pill"><i class="mdi mdi-pill"></i> Farmacia</span>
                <span class="brand-pill"><i class="mdi mdi-calendar-check"></i> Agenda en línea</span>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: form panel ──────────────────────────────── --}}
    <div class="form-panel">

        <p class="form-kicker">Portal de acceso</p>
        <h2 class="form-title">Bienvenido</h2>
        <p class="form-hint">Ingresa tus credenciales para continuar</p>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="mdi mdi-alert-circle-outline"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Mensaje de sesión --}}
        @if (session('status'))
            <div class="alert alert-info">
                <i class="mdi mdi-information-outline"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" autocomplete="on">
            @csrf

            {{-- Correo --}}
            <div class="field">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="tu@correo.com"
                        required
                        autofocus
                        autocomplete="email"
                    >
                    <i class="mdi mdi-email-outline"></i>
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="field">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                    <i class="mdi mdi-lock-outline"></i>
                    <button type="button" class="pass-toggle" onclick="togglePass()" aria-label="Mostrar contraseña" id="passBtn">
                        <i class="mdi mdi-eye-outline" id="passIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Extras --}}
            <div class="form-extras">
                <label class="remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Mantener sesión activa
                </label>
                <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">
                <i class="mdi mdi-login-variant"></i>
                Iniciar sesión
            </button>

        </form>

        <hr class="form-divider">

        <p class="form-footer">
            ¿Necesitas ayuda? Contacta a <strong>soporte PATS</strong>
        </p>

    </div>

</div>

<script>
    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('passIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'mdi mdi-eye-off-outline';
        } else {
            input.type = 'password';
            icon.className = 'mdi mdi-eye-outline';
        }
    }
</script>

{{-- PWA --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    (function () {
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches
                        || window.navigator.standalone === true;
        if (isStandalone) return;

        var deferredPrompt = null;

        var banner = document.createElement('div');
        banner.setAttribute('style', [
            'position:fixed', 'bottom:0', 'left:0', 'right:0', 'z-index:99999',
            'background:#1b1f6f', 'color:#fff', 'padding:12px 16px',
            'display:flex', 'align-items:center', 'justify-content:space-between',
            'gap:12px', 'box-shadow:0 -2px 12px rgba(0,0,0,0.3)', 'font-family:sans-serif'
        ].join(';'));

        banner.innerHTML =
            '<div style="display:flex;align-items:center;gap:10px;">' +
                '<div style="width:36px;height:36px;background:#87a924;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;color:#fff;">P</div>' +
                '<div>' +
                    '<div style="font-weight:700;font-size:14px;">Instalar PATS</div>' +
                    '<div id="pwa-sub" style="font-size:12px;opacity:.85;">Accede más rápido desde tu dispositivo</div>' +
                '</div>' +
            '</div>' +
            '<div style="display:flex;gap:8px;flex-shrink:0;">' +
                '<button id="pwa-install" style="background:#87a924;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-weight:700;font-size:13px;cursor:pointer;">Instalar</button>' +
                '<button id="pwa-close" style="background:transparent;color:#fff;border:1px solid rgba(255,255,255,.4);border-radius:8px;padding:8px 10px;font-size:13px;cursor:pointer;">✕</button>' +
            '</div>';

        document.body.appendChild(banner);

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
        });

        document.getElementById('pwa-install').addEventListener('click', async function () {
            if (!deferredPrompt) {
                document.getElementById('pwa-sub').textContent = 'Usa el menú del navegador → "Instalar aplicación"';
                return;
            }
            banner.style.display = 'none';
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
        });

        document.getElementById('pwa-close').addEventListener('click', function () {
            banner.style.display = 'none';
        });

        window.addEventListener('appinstalled', function () {
            banner.style.display = 'none';
        });
    })();
</script>

</body>
</html>