<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Portal — PATS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --blue-500: #3b74f5;
            --blue-600: #2558e0;
            --blue-50:  #eff4ff;
            --blue-100: #dde8ff;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --danger:   #ef4444;
            --border:   rgba(59,116,245,.16);
            --font: 'Plus Jakarta Sans', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font);
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 64px rgba(0,0,0,.28);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, var(--blue-600), #1a3fb5);
            padding: 36px 40px 28px;
            text-align: center;
            color: #fff;
        }
        .card-header .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px; height: 58px;
            background: rgba(255,255,255,.15);
            border-radius: 50%;
            margin-bottom: 14px;
        }
        .card-header .logo i { font-size: 28px; color: #fff; }
        .card-header h1 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .card-header p  { font-size: 13px; opacity: .75; }
        .card-body { padding: 32px 40px 36px; }

        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 12.5px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 17px; pointer-events: none; }
        input[type=email], input[type=password], input[type=text] {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 14px;
            color: var(--slate-800);
            outline: none;
            transition: border-color .15s;
        }
        input:focus { border-color: var(--blue-500); }

        .error-box {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: var(--danger);
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .error-box i { flex-shrink: 0; margin-top: 1px; }

        .remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--slate-500); margin-bottom: 20px; cursor: pointer; }
        .remember input { width: 15px; height: 15px; accent-color: var(--blue-500); }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--blue-500);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover { background: var(--blue-600); }
        .footer-note { text-align: center; font-size: 12px; color: var(--slate-400); margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="logo"><i class="mdi mdi-passport"></i></div>
            <h1>Portal PATS</h1>
            <p>Accede con tu correo y contraseña</p>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="error-box">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="mdi mdi-email-outline"></i>
                        <input type="email" id="correo" name="correo"
                            value="{{ old('correo') }}"
                            placeholder="tu@correo.com"
                            autocomplete="username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrap">
                        <i class="mdi mdi-lock-outline"></i>
                        <input type="password" id="password" name="password"
                            placeholder="••••••••"
                            autocomplete="current-password" required>
                    </div>
                </div>

                <label class="remember">
                    <input type="checkbox" name="remember" value="1">
                    Mantener sesión iniciada
                </label>

                <button type="submit" class="btn-login">
                    <i class="mdi mdi-login"></i> Iniciar sesión
                </button>
            </form>

            <p class="footer-note">¿Problemas para acceder? Contacta a soporte PATS</p>
        </div>
    </div>
</body>
</html>
