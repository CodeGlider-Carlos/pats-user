<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña | PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #1b1f6f;
            --navy-d: #10134a;
            --blue:   #0b43e6;
            --white:  #ffffff;
            --off:    #f7f8fc;
            --muted:  #8892a4;
            --border: #dce2ee;
            --cyan:   #00D9C8;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--navy-d);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }

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

        /* LEFT */
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
        .brand-dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(135,169,36,.18) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            pointer-events: none;
        }
        .brand-cross { position: absolute; top: 2.8rem; left: 3.5rem; width: 38px; height: 38px; opacity: .55; }
        .brand-cross span { position: absolute; background: linear-gradient(135deg, #083dff 0%, #12d8ca 100%); border-radius: 3px; }
        .brand-cross span:first-child { width: 38px; height: 12px; top: 50%; left: 0; transform: translateY(-50%); }
        .brand-cross span:last-child  { width: 12px; height: 38px; left: 50%; top: 0; transform: translateX(-50%); }

        .brand-logo { position: absolute; top: 2.2rem; left: 50%; transform: translateX(-50%); width: 155px; }
        .brand-logo img { width: 100%; filter: brightness(0) invert(1); opacity: .92; }

        .brand-body { position: relative; z-index: 2; }
        .brand-tag {
            display: inline-flex; align-items: center; gap: .45rem;
            background: rgba(135,169,36,.18); border: 1px solid rgba(135,169,36,.35);
            color: var(--cyan); font-size: .72rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            padding: .35rem .75rem; border-radius: 50px; margin-bottom: 1.2rem;
        }
        .brand-headline {
            font-family: 'Playfair Display', serif; font-size: 2.4rem; font-weight: 500;
            color: var(--white); line-height: 1.2; margin-bottom: 1.1rem; max-width: 340px;
        }
        .brand-headline em {
            background: linear-gradient(90deg, #ffffff 0%, #cfe0ff 20%, #79b5ff 46%, #1fd6c8 76%, #b8f21d 100%);
            background-clip: text; -webkit-text-fill-color: transparent; font-style: normal;
        }
        .brand-sub {
            font-size: .9rem; font-weight: 300;
            color: rgba(255,255,255,.55); max-width: 300px; line-height: 1.65; margin-bottom: 2.5rem;
        }

        /* RIGHT */
        .form-panel {
            width: 420px; flex-shrink: 0;
            background: var(--white);
            display: flex; flex-direction: column; justify-content: center;
            padding: 3.5rem 3rem;
        }

        .form-kicker { font-size: .72rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--blue); margin-bottom: .7rem; }
        .form-title  { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 500; color: var(--navy); margin-bottom: .45rem; line-height: 1.15; }
        .form-hint   { font-size: .85rem; color: var(--muted); margin-bottom: 2rem; font-weight: 300; }

        .alert { display: flex; align-items: flex-start; gap: .6rem; font-size: .84rem; padding: .85rem 1rem; border-radius: 10px; margin-bottom: 1.4rem; line-height: 1.5; animation: fadeUp .3s ease both; }
        .alert-danger { background: #fdf3f2; color: #c0392b; border: 1px solid #f5c6c2; }
        .alert i { font-size: 1.05rem; flex-shrink: 0; margin-top: .05rem; }

        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-size: .78rem; font-weight: 600; letter-spacing: .04em; color: var(--navy); margin-bottom: .5rem; text-transform: uppercase; }

        .input-wrap { position: relative; }
        .input-wrap i.field-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 1.05rem; pointer-events: none; transition: color .2s; }
        .input-wrap input {
            width: 100%; height: 50px; padding: 0 2.8rem 0 2.8rem;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'Outfit', sans-serif; font-size: .93rem;
            color: var(--navy); background: var(--off); outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .input-wrap input::placeholder { color: var(--muted); }
        .input-wrap input:focus { border-color: var(--navy); background: var(--white); box-shadow: 0 0 0 3px rgba(27,31,111,.08); }
        .input-wrap:focus-within i.field-icon { color: var(--navy); }

        .pass-toggle { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); font-size: 1.05rem; padding: 0; line-height: 1; transition: color .2s; }
        .pass-toggle:hover { color: var(--navy); }

        /* requisitos de contraseña */
        .req-list { margin: .5rem 0 1rem; padding: 0; list-style: none; display: grid; grid-template-columns: 1fr 1fr; gap: .3rem .8rem; }
        .req-list li { font-size: .76rem; color: var(--muted); display: flex; align-items: center; gap: .35rem; }
        .req-list li.ok { color: #1a7a52; }
        .req-list li i { font-size: .85rem; }

        .btn-submit {
            width: 100%; height: 52px;
            background: linear-gradient(135deg, #083dff 0%, #006fff 48%, #12d8ca 100%);
            color: var(--white); border: none; border-radius: 12px;
            font-family: 'Outfit', sans-serif; font-size: .92rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            transition: background .2s, transform .15s;
            margin-bottom: 1.25rem;
        }
        .btn-submit:hover { background: var(--blue); transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        .back-link { display: flex; align-items: center; justify-content: center; gap: .4rem; font-size: .82rem; color: var(--muted); text-decoration: none; transition: color .2s; }
        .back-link:hover { color: var(--navy); }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 780px) {
            body { padding: 0; align-items: stretch; }
            .auth-shell { flex-direction: column; border-radius: 0; min-height: 100dvh; max-width: 100%; box-shadow: none; }
            .brand { clip-path: none; min-height: 200px; padding: 2.2rem 2rem; align-items: center; justify-content: center; }
            .brand-logo { position: static; transform: none; margin-bottom: 1.2rem; width: 120px; }
            .brand-headline { font-size: 1.7rem; text-align: center; max-width: 100%; }
            .brand-sub, .brand-tag, .brand-cross { display: none; }
            .form-panel { width: 100%; padding: 2.5rem 1.75rem 3rem; flex: 1; }
            .req-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="auth-shell">

    {{-- LEFT --}}
    <div class="brand">
        <div class="brand-dots"></div>
        <div class="brand-cross"><span></span><span></span></div>

        <div class="brand-logo" style="display:flex; align-items:center;">
            <img src="{{ asset('images/logo_50.png') }}" alt="" style="filter:brightness(0) invert(1); height:36px;">
            <span style="color:#fff; font-weight:300; margin:0 8px; opacity:.6; font-size:1.4rem;">|</span>
            <img src="{{ asset('images/PATS_LOGO.png') }}" alt="PATS" style="filter:brightness(0) invert(1); height:36px;">
        </div>

        <div class="brand-body">
            <div class="brand-tag"><i class="mdi mdi-lock-reset"></i> Restablecer acceso</div>
            <h1 class="brand-headline">Nueva contraseña,<br>acceso <em>restaurado</em></h1>
            <p class="brand-sub">Crea una contraseña segura para proteger tu cuenta PATS.</p>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="form-panel">

        <p class="form-kicker">Nueva contraseña</p>
        <h2 class="form-title">Crea tu contraseña</h2>
        <p class="form-hint">Elige una contraseña segura de al menos 8 caracteres.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="mdi mdi-alert-circle-outline"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.resetear') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Correo --}}
            <div class="field">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        placeholder="tu@correo.com"
                        required
                        autocomplete="email"
                    >
                    <i class="mdi mdi-email-outline field-icon"></i>
                </div>
            </div>

            {{-- Nueva contraseña --}}
            <div class="field">
                <label for="password">Nueva contraseña</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                        oninput="checkReqs(this.value)"
                    >
                    <i class="mdi mdi-lock-outline field-icon"></i>
                    <button type="button" class="pass-toggle" onclick="togglePass('password','icon1')" aria-label="Mostrar">
                        <i class="mdi mdi-eye-outline" id="icon1"></i>
                    </button>
                </div>
                <ul class="req-list" id="reqList">
                    <li id="req-len"><i class="mdi mdi-circle-outline"></i> Mínimo 8 caracteres</li>
                    <li id="req-upper"><i class="mdi mdi-circle-outline"></i> Una mayúscula</li>
                    <li id="req-number"><i class="mdi mdi-circle-outline"></i> Un número</li>
                    <li id="req-special"><i class="mdi mdi-circle-outline"></i> Un carácter especial</li>
                </ul>
            </div>

            {{-- Confirmar --}}
            <div class="field">
                <label for="password_confirmation">Confirmar contraseña</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    >
                    <i class="mdi mdi-lock-check-outline field-icon"></i>
                    <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation','icon2')" aria-label="Mostrar">
                        <i class="mdi mdi-eye-outline" id="icon2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="mdi mdi-check-bold"></i>
                Guardar contraseña
            </button>

        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="mdi mdi-arrow-left"></i>
            Volver al inicio de sesión
        </a>

    </div>

</div>

<script>
    function togglePass(id, iconId) {
        const input = document.getElementById(id);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'mdi mdi-eye-off-outline';
        } else {
            input.type = 'password';
            icon.className = 'mdi mdi-eye-outline';
        }
    }

    function checkReqs(val) {
        check('req-len',     val.length >= 8);
        check('req-upper',   /[A-Z]/.test(val));
        check('req-number',  /[0-9]/.test(val));
        check('req-special', /[^A-Za-z0-9]/.test(val));
    }

    function check(id, ok) {
        const li = document.getElementById(id);
        const i  = li.querySelector('i');
        if (ok) {
            li.classList.add('ok');
            i.className = 'mdi mdi-check-circle';
        } else {
            li.classList.remove('ok');
            i.className = 'mdi mdi-circle-outline';
        }
    }
</script>

</body>
</html>
