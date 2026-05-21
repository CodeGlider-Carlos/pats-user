{{-- resources/views/pats/distribucion_link_password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al formulario · PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:     #0d1b3e;
            --blue:     #2563eb;
            --blue-l:   #3b82f6;
            --cyan:     #06b6d4;
            --surface:  #ffffff;
            --page:     #f0f5ff;
            --border:   rgba(37,99,235,.14);
            --slate-4:  #94a3b8;
            --slate-5:  #64748b;
            --slate-7:  #334155;
            --slate-8:  #1e293b;
            --danger:   #ef4444;
            --font:     'Plus Jakarta Sans', system-ui, sans-serif;
        }

        html { height: 100%; }

        body {
            font-family: var(--font);
            background: var(--page);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            -webkit-font-smoothing: antialiased;
        }

        .wrap {
            width: 100%;
            max-width: 420px;
        }

        /* ── Logo ── */
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo__icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff;
        }
        .logo__name {
            font-size: 20px; font-weight: 800;
            color: var(--navy); letter-spacing: -.02em;
        }
        .logo__sub { font-size: 11px; color: var(--slate-4); font-weight: 500; letter-spacing: .06em; text-transform: uppercase; margin-top: 1px; }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: 0 0 0 1px var(--border), 0 12px 40px rgba(13,27,62,.10);
            overflow: hidden;
        }

        .card__head {
            background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
            padding: 32px 32px 28px;
            text-align: center;
        }
        .card__head-icon {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            border: 2px solid rgba(255,255,255,.3);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; color: #fff; margin-bottom: 14px;
        }
        .card__head-title {
            font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 6px;
        }
        .card__head-sub {
            font-size: 13px; color: rgba(255,255,255,.7); line-height: 1.5;
        }

        .card__body { padding: 32px; }

        /* ── Amount badge ── */
        .amount-badge {
            display: flex; align-items: center; gap: 10px;
            background: #f0f5ff;
            border: 1.5px solid rgba(37,99,235,.18);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .amount-badge__icon { font-size: 22px; color: var(--blue); }
        .amount-badge__label { font-size: 12px; color: var(--slate-5); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
        .amount-badge__value { font-size: 20px; font-weight: 800; color: var(--navy); line-height: 1; }

        @if($link->type_pay !== 'card')
        .amount-badge { background: #ecfdf5; border-color: rgba(16,185,129,.2); }
        .amount-badge__icon { color: #10b981; }
        .amount-badge__value { color: #065f46; }
        @endif

        /* ── Field ── */
        .field { margin-bottom: 20px; }
        .label { display: block; font-size: 13px; font-weight: 700; color: var(--slate-7); margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--slate-4); pointer-events: none; }
        .input-wrap .toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: var(--slate-4); padding: 4px; }
        .input-wrap .toggle:hover { color: var(--blue); }
        .input {
            width: 100%; padding: 12px 44px 12px 44px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: var(--font); font-size: 15px; color: var(--slate-8);
            background: #f8faff;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.12); background: #fff; }

        /* ── Error ── */
        .error-box {
            display: flex; align-items: center; gap: 10px;
            background: #fff1f2; border: 1.5px solid rgba(239,68,68,.25);
            border-radius: 10px; padding: 12px 16px;
            font-size: 13px; color: #b91c1c; font-weight: 600;
            margin-bottom: 18px;
        }
        .error-box i { font-size: 18px; flex-shrink: 0; }

        /* ── Button ── */
        .btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
            color: #fff; border: none; border-radius: 12px;
            font-family: var(--font); font-size: 15px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity .15s, transform .1s;
        }
        .btn:hover { opacity: .92; }
        .btn:active { transform: scale(.98); }

        /* ── Footer ── */
        .footer { text-align: center; font-size: 12px; color: var(--slate-4); margin-top: 20px; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="logo">
        <div class="logo__icon"><i class="mdi mdi-handshake-outline"></i></div>
        <div>
            <div class="logo__name">PATS</div>
            <div class="logo__sub">Red de Distribución</div>
        </div>
    </div>

    <div class="card">
        <div class="card__head">
            <div class="card__head-icon"><i class="mdi mdi-lock-outline"></i></div>
            <div class="card__head-title">Acceso al formulario</div>
            <p class="card__head-sub">Este enlace está protegido. Ingresa la contraseña que te fue proporcionada para continuar.</p>
        </div>

        <div class="card__body">

            {{-- Monto / tipo de pago --}}
            <div class="amount-badge">
                @if($link->type_pay === 'card')
                    <i class="mdi mdi-credit-card-outline amount-badge__icon"></i>
                    <div>
                        <div class="amount-badge__label">Monto a pagar</div>
                        <div class="amount-badge__value">${{ number_format($link->amount, 0, '.', ',') }} MXN</div>
                    </div>
                @else
                    <i class="mdi mdi-check-circle-outline amount-badge__icon"></i>
                    <div>
                        <div class="amount-badge__label">Modalidad</div>
                        <div class="amount-badge__value">Sin costo</div>
                    </div>
                @endif
            </div>

            {{-- Error --}}
            @if($errors->has('password'))
                <div class="error-box">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    {{ $errors->first('password') }}
                </div>
            @endif

            <form method="POST" action="{{ route('dist.link.auth', $token) }}">
                @csrf
                <div class="field">
                    <label class="label" for="password">Contraseña de acceso</label>
                    <div class="input-wrap">
                        <i class="mdi mdi-key-outline icon"></i>
                        <input class="input" type="password" id="password" name="password"
                               placeholder="Ingresa tu contraseña" autocomplete="current-password" autofocus>
                        <button type="button" class="toggle" id="togglePwd" tabindex="-1">
                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="mdi mdi-arrow-right-circle-outline"></i>
                    Acceder al formulario
                </button>
            </form>
        </div>
    </div>

    <div class="footer">
        PATS · Red de Distribución Médica<br>
        Este enlace es de uso único y personal.
    </div>

</div>

<script>
    document.getElementById('togglePwd')?.addEventListener('click', function () {
        const inp  = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'mdi mdi-eye-off-outline';
        } else {
            inp.type = 'password';
            icon.className = 'mdi mdi-eye-outline';
        }
    });
</script>
</body>
</html>
