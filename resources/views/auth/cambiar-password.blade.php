<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crea tu contraseña | PATS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1b1f6f; --navy-d: #10134a;
            --green: #87a924; --green-d: #6b8a1a;
            --white: #ffffff; --off: #f7f8fc;
            --muted: #8892a4; --border: #dce2ee;
            --danger: #e74c3c;
        }
        html, body { height: 100%; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--navy-d);
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 1.5rem;
        }
        .auth-shell {
            display: flex; width: 100%; max-width: 1060px; min-height: 580px;
            border-radius: 20px; overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.55);
            animation: fadeUp .55s cubic-bezier(.22,.68,0,1.2) both;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }

        /* LEFT */
        .brand {
            flex: 1; position: relative;
            background: linear-gradient(150deg, var(--navy) 0%, #0e1250 55%, #090c38 100%);
            display: flex; flex-direction: column; justify-content: center;
            padding: 3rem 2.5rem; color: var(--white);
        }
        .brand__logo { width: 130px; margin-bottom: 2.5rem; }
        .brand__icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: rgba(135,169,36,.18); border: 2px solid rgba(135,169,36,.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: var(--green); margin-bottom: 1.5rem;
        }
        .brand__title { font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 600; line-height: 1.3; margin-bottom: .75rem; }
        .brand__sub { font-size: .9rem; color: rgba(255,255,255,.55); line-height: 1.6; }

        /* RIGHT */
        .form-panel {
            width: 420px; flex-shrink: 0; background: var(--white);
            display: flex; flex-direction: column; justify-content: center;
            padding: 3rem 2.5rem;
        }
        .form-panel__tag {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: .7rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
            color: var(--green); background: rgba(135,169,36,.1); border-radius: 20px;
            padding: 4px 12px; margin-bottom: 1.2rem;
        }
        .form-panel__title { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--navy); margin-bottom: .4rem; }
        .form-panel__sub { font-size: .85rem; color: var(--muted); margin-bottom: 2rem; line-height: 1.5; }

        .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.1rem; }
        .field label { font-size: .75rem; font-weight: 600; color: #4a5568; letter-spacing: .04em; text-transform: uppercase; }
        .input-wrap { position: relative; }
        .input-wrap .icon-l { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 18px; pointer-events: none; }
        .input-wrap input {
            width: 100%; padding: 11px 14px 11px 42px;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'Outfit', sans-serif; font-size: .95rem; color: #1a202c;
            outline: none; transition: border-color .18s, box-shadow .18s;
        }
        .input-wrap input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(27,31,111,.1); }
        .input-wrap .toggle-eye {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--muted); font-size: 18px; padding: 2px;
        }

        .field-error { font-size: .78rem; color: var(--danger); margin-top: 2px; display: flex; align-items: center; gap: 4px; }

        .rules { background: var(--off); border-radius: 10px; padding: 12px 14px; margin-bottom: 1.4rem; }
        .rules__item { font-size: .78rem; color: var(--muted); display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .rules__item:last-child { margin-bottom: 0; }
        .rules__item i { font-size: 14px; }
        .rules__item.ok { color: var(--green); }
        .rules__item.ok i { color: var(--green); }

        .btn-submit {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            background: linear-gradient(135deg, var(--navy), #2d33a0);
            color: var(--white); font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 600;
            cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(27,31,111,.35); }

        .logout-link { text-align: center; margin-top: 1.2rem; font-size: .82rem; }
        .logout-link a { color: var(--muted); text-decoration: none; }
        .logout-link a:hover { color: var(--navy); }

        @media (max-width: 700px) {
            .brand { display: none; }
            .form-panel { width: 100%; }
        }
    </style>
</head>
<body>

<div class="auth-shell">

    <!-- LEFT -->
    <div class="brand">
        <img src="{{ asset('assets/img/pats-logo-white.png') }}" alt="PATS" class="brand__logo"
             onerror="this.style.display='none'">
        <div class="brand__icon"><i class="mdi mdi-shield-key-outline"></i></div>
        <div class="brand__title">Crea tu contraseña personal</div>
        <div class="brand__sub">
            Por seguridad, necesitas establecer una contraseña propia antes de continuar.
            Elige una que solo tú conozcas.
        </div>
    </div>

    <!-- RIGHT -->
    <div class="form-panel">
        <div class="form-panel__tag"><i class="mdi mdi-lock-reset"></i> Primer acceso</div>
        <h1 class="form-panel__title">Crea tu contraseña</h1>
        <p class="form-panel__sub">Hola, <strong>{{ auth('pasaporte')->user()->nombre_usuario }}</strong>. Elige una contraseña segura para proteger tu cuenta.</p>

        @if ($errors->any())
            <div style="background:#fdf2f2;border:1px solid rgba(231,76,60,.25);border-radius:10px;padding:10px 14px;margin-bottom:1.2rem;font-size:.83rem;color:var(--danger);">
                <i class="mdi mdi-alert-circle-outline"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.cambiar.post') }}">
            @csrf

            <div class="field">
                <label for="password_nuevo">Nueva contraseña</label>
                <div class="input-wrap">
                    <i class="mdi mdi-lock-outline icon-l"></i>
                    <input type="password" id="password_nuevo" name="password_nuevo"
                           autocomplete="new-password" required>
                    <button type="button" class="toggle-eye" data-target="password_nuevo">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>
            </div>

            <div class="field">
                <label for="password_nuevo_confirmation">Confirmar contraseña</label>
                <div class="input-wrap">
                    <i class="mdi mdi-lock-check-outline icon-l"></i>
                    <input type="password" id="password_nuevo_confirmation" name="password_nuevo_confirmation"
                           autocomplete="new-password" required>
                    <button type="button" class="toggle-eye" data-target="password_nuevo_confirmation">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>
            </div>

            <div class="rules" id="passwordRules">
                <div class="rules__item" id="rule-len"><i class="mdi mdi-circle-small"></i> Mínimo 8 caracteres</div>
                <div class="rules__item" id="rule-match"><i class="mdi mdi-circle-small"></i> Las contraseñas coinciden</div>
            </div>

            <button type="submit" class="btn-submit" id="btnGuardar">
                <i class="mdi mdi-check-bold"></i> Guardar y continuar
            </button>
        </form>

        <div class="logout-link">
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:.82rem;font-family:'Outfit',sans-serif;">
                    <i class="mdi mdi-logout"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</div>

<script>
document.querySelectorAll('.toggle-eye').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.querySelector('i').className = isPass ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
    });
});

const inputNew    = document.getElementById('password_nuevo');
const inputConf   = document.getElementById('password_nuevo_confirmation');
const ruleLen     = document.getElementById('rule-len');
const ruleMatch   = document.getElementById('rule-match');

function checkRules() {
    const len   = inputNew.value.length >= 8;
    const match = inputNew.value === inputConf.value && inputConf.value !== '';
    ruleLen.classList.toggle('ok', len);
    ruleLen.querySelector('i').className   = len   ? 'mdi mdi-check-circle' : 'mdi mdi-circle-small';
    ruleMatch.classList.toggle('ok', match);
    ruleMatch.querySelector('i').className = match ? 'mdi mdi-check-circle' : 'mdi mdi-circle-small';
}
inputNew.addEventListener('input', checkRules);
inputConf.addEventListener('input', checkRules);
</script>
</body>
</html>
