<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal') — PATS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --blue-500: #3b74f5;
            --blue-600: #2558e0;
            --blue-700: #1a3fb5;
            --blue-50:  #eff4ff;
            --blue-100: #dde8ff;
            --cyan-400: #22d3ee;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger:  #ef4444;
            --border:  rgba(59,116,245,.13);
            --font: 'Plus Jakarta Sans', sans-serif;
            --sidebar-w: 240px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font); background: #f0f5ff; color: var(--slate-800); min-height: 100vh; display: flex; flex-direction: column; -webkit-font-smoothing: antialiased; }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            height: 56px;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px;
        }
        .topbar__brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; color: var(--blue-700); text-decoration: none; }
        .topbar__brand i { font-size: 22px; color: var(--blue-500); }
        .topbar__user { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--slate-600); }
        .topbar__avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--blue-100); color: var(--blue-600); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
        .btn-logout { display: flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1.5px solid var(--border); border-radius: 8px; background: none; color: var(--slate-500); font-family: var(--font); font-size: 12.5px; cursor: pointer; text-decoration: none; transition: all .15s; }
        .btn-logout:hover { background: #fff1f2; border-color: #fecdd3; color: var(--danger); }

        /* ── BODY LAYOUT ── */
        .app-body { display: flex; flex: 1; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 24px 0;
            flex-shrink: 0;
            position: sticky;
            top: 56px;
            height: calc(100vh - 56px);
            overflow-y: auto;
        }
        .sidebar__section { padding: 0 16px; margin-bottom: 6px; }
        .sidebar__label { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--slate-400); padding: 0 8px; margin-bottom: 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--slate-600);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .15s;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item i { font-size: 18px; flex-shrink: 0; }
        .nav-item:hover { background: var(--blue-50); color: var(--blue-600); }
        .nav-item.active { background: var(--blue-50); color: var(--blue-600); font-weight: 700; }
        .nav-item.active::before { content: ''; position: absolute; left: 0; top: 6px; bottom: 6px; width: 3px; background: var(--blue-500); border-radius: 0 3px 3px 0; }
        .nav-badge { margin-left: auto; font-size: 9px; font-weight: 700; letter-spacing: .5px; background: var(--slate-200); color: var(--slate-500); padding: 2px 7px; border-radius: 20px; text-transform: uppercase; }
        .sidebar__divider { border: none; border-top: 1px solid var(--border); margin: 12px 16px; }

        /* ── MAIN ── */
        .main { flex: 1; padding: 28px 32px; min-width: 0; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 22px; font-weight: 800; color: var(--slate-800); }
        .page-header p  { font-size: 13.5px; color: var(--slate-500); margin-top: 3px; }

        /* ── CARD ── */
        .card { background: #fff; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .card-header-bar { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
        .card-header-bar i { color: var(--blue-500); font-size: 20px; }
        .card-header-bar h2 { font-size: 15px; font-weight: 700; color: var(--slate-800); }
        .card-body { padding: 22px; }

        /* ── BADGES ── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .badge-success  { background: #ecfdf5; color: #059669; }
        .badge-warning  { background: #fffbeb; color: #b45309; }
        .badge-danger   { background: #fff1f2; color: #dc2626; }
        .badge-secondary{ background: var(--slate-100); color: var(--slate-500); }

        /* ── INFO GRID ── */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .info-item { }
        .info-item__label { font-size: 11px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: var(--slate-400); margin-bottom: 4px; }
        .info-item__value { font-size: 14.5px; font-weight: 600; color: var(--slate-800); }
        .info-item__value.mono { font-family: 'JetBrains Mono', 'Courier New', monospace; font-size: 14px; }

        /* ── PROXIMAMENTE ── */
        .prox-banner {
            background: linear-gradient(135deg, var(--blue-50), #e0f2fe);
            border: 1.5px dashed var(--blue-500);
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
        }
        .prox-banner i { font-size: 48px; color: var(--blue-400, #6b9eff); margin-bottom: 16px; display: block; }
        .prox-banner h2 { font-size: 20px; font-weight: 800; color: var(--blue-700); margin-bottom: 8px; }
        .prox-banner p  { font-size: 14px; color: var(--slate-500); max-width: 380px; margin: 0 auto; }
        .prox-tag { display: inline-block; margin-bottom: 14px; background: var(--blue-500); color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { padding: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- TOPBAR --}}
    <header class="topbar">
        <a class="topbar__brand" href="{{ route('portal.dashboard') }}">
            <i class="mdi mdi-passport"></i> Portal PATS
        </a>
        <div class="topbar__user">
            @php $acceso = auth('pasaporte')->user(); @endphp
            <div class="topbar__avatar">
                {{ strtoupper(substr($acceso->nombre_usuario ?? $acceso->nombre_paciente ?? 'U', 0, 1)) }}
            </div>
            <span style="display:none;font-size:13px;" class="d-sm-inline">
                {{ $acceso->nombre_usuario ?? $acceso->nombre_paciente ?? $acceso->correo_usuario }}
            </span>
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="mdi mdi-logout"></i> Salir
                </button>
            </form>
        </div>
    </header>

    <div class="app-body">

        {{-- SIDEBAR --}}
        <nav class="sidebar">
            <div class="sidebar__section">
                <div class="sidebar__label">Principal</div>

                <a href="{{ route('portal.dashboard') }}"
                   class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <i class="mdi mdi-view-dashboard-outline"></i> Mi Pasaporte
                </a>

                <a href="{{ route('portal.perfil') }}"
                   class="nav-item {{ request()->routeIs('portal.perfil') ? 'active' : '' }}">
                    <i class="mdi mdi-account-outline"></i> Mi Perfil
                </a>
            </div>

            <hr class="sidebar__divider">

            <div class="sidebar__section">
                <div class="sidebar__label">Servicios</div>

                <a href="{{ route('portal.proximamente', 'pagos') }}"
                   class="nav-item {{ request()->routeIs('portal.proximamente') && request()->route('seccion') === 'pagos' ? 'active' : '' }}">
                    <i class="mdi mdi-credit-card-outline"></i> Mis Pagos
                    <span class="nav-badge">Pronto</span>
                </a>

                <a href="{{ route('portal.proximamente', 'beneficios') }}"
                   class="nav-item {{ request()->is('portal/beneficios') ? 'active' : '' }}">
                    <i class="mdi mdi-hospital-box-outline"></i> Mis Beneficios
                    <span class="nav-badge">Pronto</span>
                </a>

                <a href="{{ route('portal.proximamente', 'soporte') }}"
                   class="nav-item {{ request()->is('portal/soporte') ? 'active' : '' }}">
                    <i class="mdi mdi-headset"></i> Soporte
                    <span class="nav-badge">Pronto</span>
                </a>
            </div>
        </nav>

        {{-- MAIN CONTENT --}}
        <main class="main">
            @yield('content')
        </main>

    </div>

    @stack('scripts')

    @include('partials.chatbot')
</body>
</html>
