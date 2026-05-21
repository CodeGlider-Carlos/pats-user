{{-- resources/views/pats/solicitud_franquicia_link.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitud de Franquicia · PATS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        :root {
            --white: #ffffff;
            --page: #f0f5ff;
            --surface: #ffffff;
            --surface-2: #f7f9ff;
            --blue-50: #eff4ff;
            --blue-100: #dde8ff;
            --blue-200: #c3d5fe;
            --blue-400: #6b9eff;
            --blue-500: #3b74f5;
            --blue-600: #2558e0;
            --blue-700: #1a3fb5;
            --cyan-400: #22d3ee;
            --cyan-500: #06b6d4;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --success: #10b981;
            --success-bg: #ecfdf5;
            --danger: #ef4444;
            --danger-bg: #fff1f2;
            --border: rgba(59, 116, 245, .14);
            --border-focus: rgba(59, 116, 245, .5);
            --shadow-sm: 0 1px 3px rgba(59,116,245,.08), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 16px rgba(59,116,245,.10), 0 2px 6px rgba(0,0,0,.05);
            --shadow-lg: 0 12px 40px rgba(59,116,245,.12), 0 4px 12px rgba(0,0,0,.06);
            --shadow-card: 0 0 0 1px var(--border), var(--shadow-lg);
            --radius-sm: 10px;
            --radius: 16px;
            --radius-lg: 22px;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
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
            background: rgba(255,255,255,.85); backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar__brand { display: flex; align-items: center; gap: 10px; font-size: 17px; font-weight: 800; color: var(--blue-600); letter-spacing: -.02em; }
        .topbar__brand i { font-size: 22px; }
        .topbar__tag { font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--slate-400); }
        .topbar__secure { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--success); background: var(--success-bg); padding: 5px 12px; border-radius: 100px; }
        .topbar__secure i { font-size: 14px; }

        /* ── Layout ── */
        .sol-layout {
            max-width: 1180px; margin: 0 auto; padding: 40px 24px 80px;
            display: grid; grid-template-columns: 280px 1fr; gap: 32px; align-items: start;
        }

        /* ── Sidebar ── */
        .sidebar { position: sticky; top: 84px; }
        .sidebar__hero {
            background: linear-gradient(135deg, var(--blue-600) 0%, var(--blue-700) 100%);
            border-radius: var(--radius-lg); padding: 28px 24px; color: white;
            margin-bottom: 16px; position: relative; overflow: hidden;
        }
        .sidebar__hero::before {
            content: ''; position: absolute; top: -40px; right: -40px;
            width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,.07);
        }
        .sidebar__hero::after {
            content: ''; position: absolute; bottom: -20px; left: -20px;
            width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,.05);
        }
        .sidebar__icon {
            width: 48px; height: 48px; background: rgba(255,255,255,.18); border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
            margin-bottom: 14px; position: relative; z-index: 1;
        }
        .sidebar__title { font-size: 18px; font-weight: 800; line-height: 1.25; margin-bottom: 6px; position: relative; z-index: 1; }
        .sidebar__sub   { font-size: 13px; opacity: .75; line-height: 1.5; position: relative; z-index: 1; }

        .sidebar__badge {
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
            border-radius: var(--radius-sm); padding: 12px 16px; margin-top: 16px; position: relative; z-index: 1;
        }
        .sidebar__badge-label { font-size: 10px; font-weight: 700; letter-spacing: .10em; text-transform: uppercase; opacity: .7; }
        .sidebar__badge-value { font-size: 16px; font-weight: 800; line-height: 1.1; margin: 2px 0; }

        /* Steps */
        .sidebar__steps {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow-sm);
        }
        .sidebar__steps-title { font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--slate-400); margin-bottom: 16px; }
        .step-list { list-style: none; position: relative; }
        .step-list::before { content: ''; position: absolute; left: 15px; top: 8px; bottom: 8px; width: 2px; background: var(--blue-100); border-radius: 2px; }
        .step-list__fill { position: absolute; left: 15px; top: 8px; width: 2px; background: linear-gradient(to bottom, var(--blue-500), var(--cyan-400)); border-radius: 2px; transition: height .5s cubic-bezier(.65,0,.35,1); height: 0; }
        .step-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; cursor: default; position: relative; z-index: 1; }
        .step-item--clickable { cursor: pointer; }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%; background: var(--blue-50); border: 2px solid var(--blue-100);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            font-size: 12px; font-weight: 700; color: var(--slate-400);
            transition: background .3s, border-color .3s, color .3s;
        }
        .step-item.is-active .step-num  { background: var(--blue-500); border-color: var(--blue-500); color: white; }
        .step-item.is-done .step-num    { background: var(--success-bg); border-color: var(--success); color: var(--success); }
        .step-item.is-done .step-num span::before { content: '✓'; }
        .step-item.is-done .step-num span { display: none; }
        .step-item.is-done .step-num::after { content: '✓'; font-size: 13px; color: var(--success); }
        .step-item.is-done .step-num span { display: none; }
        .step-label { font-size: 13px; font-weight: 600; color: var(--slate-700); }
        .step-desc  { font-size: 11.5px; color: var(--slate-400); }
        .step-item.is-active .step-label { color: var(--blue-600); }
        .step-item.is-done   .step-label { color: var(--success); }
        .step-item.is-prefilled .step-num { background: #ecfeff; border-color: var(--cyan-400); color: var(--cyan-500); }
        .step-item.is-prefilled .step-label { color: var(--cyan-500); }
        .step-item.is-prefilled .step-desc  { color: var(--cyan-400); }
        .step-item.is-error .step-num { background: var(--danger-bg); border-color: var(--danger); color: var(--danger); }
        .step-item.is-error .step-label { color: var(--danger); font-weight: 700; }
        .step-item.is-error .step-desc  { color: rgba(239,68,68,.65); }

        /* ── Main card ── */
        .main-card { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); overflow: hidden; }

        .progress-bar { height: 4px; background: var(--blue-100); }
        .progress-bar__fill { height: 100%; background: linear-gradient(90deg, var(--blue-500), var(--cyan-400)); transition: width .5s cubic-bezier(.65,0,.35,1); }

        .card-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 32px 36px 24px; border-bottom: 1px solid var(--border); }
        .card-header__tag { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--blue-500); background: var(--blue-50); padding: 4px 10px; border-radius: 100px; margin-bottom: 10px; }
        .card-header__title { font-size: 22px; font-weight: 800; color: var(--slate-800); letter-spacing: -.02em; }
        .card-header__desc  { font-size: 14px; color: var(--slate-500); margin-top: 4px; line-height: 1.5; }
        .card-header__counter { font-size: 26px; font-weight: 800; color: var(--blue-100); font-variant-numeric: tabular-nums; white-space: nowrap; margin-left: 16px; flex-shrink: 0; }

        .card-body { padding: 36px; }

        /* ── Panels ── */
        .panel { display: none; animation: fadeIn .35s ease; }
        .panel.is-active { display: block; }
        .panel.slide-back { animation: slideBack .35s ease; }
        @keyframes fadeIn   { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: none; } }
        @keyframes slideBack { from { opacity: 0; transform: translateX(-24px); } to { opacity: 1; transform: none; } }

        /* ── Fields ── */
        .fields { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field--full { grid-column: 1 / -1; }
        .label { font-size: 13px; font-weight: 700; color: var(--slate-700); }
        .label__req { color: var(--danger); }
        .label__opt { color: var(--slate-400); font-weight: 500; font-size: 11.5px; }

        .input-wrap { position: relative; }
        .icon-left   { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--slate-400); pointer-events: none; }
        .icon-status { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--success); opacity: 0; pointer-events: none; transition: opacity .2s; }
        .input.is-valid  ~ .icon-status { opacity: 1; }
        .input.is-invalid { border-color: var(--danger); background: var(--danger-bg); }
        .input.is-valid   { border-color: var(--success); }

        .input, .select {
            width: 100%; height: 44px; padding: 0 40px 0 40px;
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: var(--font); font-size: 14px; color: var(--slate-800);
            background: var(--surface-2); outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .select { padding-left: 40px; padding-right: 16px; appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; }
        .input:focus, .select:focus { border-color: var(--blue-500); box-shadow: 0 0 0 3px rgba(59,116,245,.12); background: var(--white); }

        .field-msg { font-size: 11.5px; }
        .field-msg--error { color: var(--danger); }
        .field-msg--hint  { color: var(--slate-400); }

        /* ── Prefill notice ── */
        .prefill-notice {
            display: flex; align-items: center; gap: 10px; grid-column: 1/-1;
            background: #ecfeff; border: 1.5px solid rgba(6,182,212,.25);
            border-radius: var(--radius-sm); padding: 12px 16px;
            font-size: 13px; color: var(--cyan-500); font-weight: 600; margin-bottom: 4px;
        }
        .prefill-notice i { font-size: 18px; flex-shrink: 0; }

        /* ── Divider ── */
        .divider { display: flex; align-items: center; gap: 12px; grid-column: 1/-1; margin: 8px 0; }
        .divider__line  { flex: 1; height: 1px; background: var(--blue-100); }
        .divider__label { font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--slate-400); white-space: nowrap; }

        /* ── Tipo persona ── */
        .tipo-cards { display: flex; gap: 12px; grid-column: 1/-1; }
        .tipo-radio { position: absolute; opacity: 0; width: 0; }
        .tipo-card {
            flex: 1; display: flex; align-items: center; gap: 12px; padding: 14px 16px;
            border: 2px solid var(--border); border-radius: var(--radius-sm);
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .tipo-radio:checked + .tipo-card { border-color: var(--blue-500); background: var(--blue-50); }
        .tipo-card__dot { width: 16px; height: 16px; border-radius: 50%; border: 2px solid var(--slate-300); flex-shrink: 0; transition: border-color .15s; }
        .tipo-radio:checked + .tipo-card .tipo-card__dot { border-color: var(--blue-500); background: var(--blue-500); }
        .tipo-card__title { font-size: 13px; font-weight: 700; color: var(--slate-700); }
        .tipo-card__sub   { font-size: 11.5px; color: var(--slate-400); }
        .moral-section { grid-column: 1/-1; overflow: hidden; max-height: 0; transition: max-height .3s ease; }
        .moral-section.open { max-height: 600px; }
        .moral-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; padding-bottom: 4px; }

        /* ── Doc cards ── */
        .docs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .doc-card {
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            overflow: hidden; transition: border-color .2s;
        }
        .doc-card.filled { border-color: var(--success); }
        .doc-card__top {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 12px; background: var(--blue-50); border-bottom: 1px solid var(--blue-100);
        }
        .doc-card__icon { font-size: 18px; color: var(--blue-500); flex-shrink: 0; }
        .doc-card__name { font-size: 12px; font-weight: 700; color: var(--slate-700); flex: 1; }
        .doc-card__req  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--slate-400); white-space: nowrap; }
        .doc-card.filled .doc-card__req { color: var(--success); }

        .file-zone {
            cursor: pointer; text-align: center; padding: 20px 12px;
            transition: background .15s;
        }
        .file-zone:hover { background: var(--blue-50); }
        .file-zone.dragover { background: var(--blue-100); border-color: var(--blue-500); }
        .file-zone.filled { background: var(--success-bg); }
        .file-zone input[type=file] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
        .file-zone__icon  { font-size: 26px; color: var(--slate-400); margin-bottom: 6px; }
        .file-zone__title { font-size: 12.5px; font-weight: 600; color: var(--slate-700); }
        .file-zone__sub   { font-size: 11px; color: var(--slate-400); margin-top: 2px; }
        .file-zone__name  { font-size: 11px; color: var(--success); font-weight: 600; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── Selfie ── */
        .selfie-cam-wrap {
            border: 2px dashed var(--blue-200); border-radius: var(--radius-sm);
            overflow: hidden; background: #fafbff; min-height: 180px;
            display: flex; align-items: center; justify-content: center;
        }
        .selfie-cam-wrap video, .selfie-cam-wrap .selfie-capture-img { display: block; }
        .selfie-cam-placeholder { display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--slate-400); padding: 32px; }
        .selfie-cam-placeholder i { font-size: 40px; }
        .selfie-cam-placeholder span { font-size: 13px; text-align: center; }

        /* ── Firma ── */
        .firma-wrap {
            border: 2px dashed var(--blue-200); border-radius: var(--radius-sm);
            background: #fdfdff; position: relative; cursor: crosshair; overflow: hidden;
            touch-action: none;
        }
        .firma-wrap.signed { border-color: var(--blue-500); border-style: solid; }
        .firma-wrap canvas { display: block; width: 100%; height: 160px; }
        .firma-placeholder {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            gap: 8px; color: var(--slate-400); font-size: 13px; pointer-events: none;
        }
        .firma-placeholder i { font-size: 18px; }
        .firma-wrap.signed .firma-placeholder { display: none; }

        /* ── Contract preview ── */
        .contrato-viewer {
            border: 1.5px solid var(--blue-200); border-radius: var(--radius-sm); overflow: hidden;
        }
        .contrato-viewer__header {
            display: flex; align-items: center; gap: 8px;
            background: var(--blue-100); border-bottom: 1px solid var(--blue-200);
            padding: 10px 14px; font-size: 13px; color: var(--slate-600);
        }
        .contrato-viewer__header i { color: var(--blue-500); font-size: 16px; flex-shrink: 0; }
        .contrato-viewer__body {
            width: 100%; height: 480px; overflow-y: auto; overflow-x: hidden;
            background: #faf8f4; display: block;
        }

        /* ── Summary ── */
        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .summary-item { background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 12px 16px; }
        .summary-item__label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--slate-400); margin-bottom: 4px; }
        .summary-item__value { font-size: 14px; font-weight: 700; color: var(--slate-800); }

        /* ── Terms ── */
        .terms-box { display: flex; align-items: flex-start; gap: 12px; cursor: pointer; }
        .terms-box input { width: 18px; height: 18px; flex-shrink: 0; accent-color: var(--blue-500); margin-top: 2px; }
        .terms-box__text { font-size: 13px; color: var(--slate-600); line-height: 1.6; }
        .terms-box__text a { color: var(--blue-500); text-decoration: none; font-weight: 600; }
        .terms-box__text a:hover { text-decoration: underline; }

        /* ── Nav buttons ── */
        .card-footer { display: flex; align-items: center; justify-content: space-between; padding: 24px 36px; border-top: 1px solid var(--border); background: var(--surface-2); }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; border-radius: var(--radius-sm); font-family: var(--font); font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: opacity .15s, transform .1s; }
        .btn:active { transform: scale(.98); }
        .btn:disabled { opacity: .45; cursor: not-allowed; }
        .btn--primary { background: linear-gradient(135deg, var(--blue-500), var(--blue-700)); color: white; box-shadow: 0 4px 12px rgba(37,99,235,.35); }
        .btn--primary:hover:not(:disabled) { opacity: .92; }
        .btn--ghost   { background: white; color: var(--slate-700); border: 1.5px solid var(--border); }
        .btn--ghost:hover:not(:disabled) { border-color: var(--blue-400); color: var(--blue-600); }
        .btn--submit  { background: linear-gradient(135deg, var(--success), #059669); color: white; box-shadow: 0 4px 12px rgba(16,185,129,.35); }
        .btn--submit:hover:not(:disabled) { opacity: .92; }

        /* ── Toast ── */
        .toast { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(80px); z-index: 9999; display: flex; align-items: center; gap: 10px; padding: 13px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; opacity: 0; transition: transform .3s, opacity .3s; pointer-events: none; min-width: 280px; max-width: 440px; box-shadow: 0 8px 24px rgba(0,0,0,.18); }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .toast--error   { background: #fee2e2; color: #b91c1c; border: 1px solid rgba(239,68,68,.25); }
        .toast--success { background: #dcfce7; color: #166534; border: 1px solid rgba(22,163,74,.2); }
        .toast--info    { background: var(--blue-50); color: var(--blue-700); border: 1px solid var(--blue-100); }
        .toast i { font-size: 18px; flex-shrink: 0; }

        /* ── Success screen ── */
        .success-screen { display: none; flex-direction: column; align-items: center; text-align: center; padding: 64px 40px; }
        .success-screen.show { display: flex; }
        .success-icon { width: 80px; height: 80px; border-radius: 50%; background: var(--success-bg); display: flex; align-items: center; justify-content: center; font-size: 40px; color: var(--success); margin-bottom: 24px; }
        .success-title { font-size: 26px; font-weight: 800; color: var(--slate-800); margin-bottom: 10px; }
        .success-ref   { font-family: var(--mono); font-size: 16px; font-weight: 600; color: var(--blue-600); background: var(--blue-50); border: 1.5px solid var(--blue-100); border-radius: 8px; padding: 8px 18px; margin: 12px 0; }
        .success-note  { font-size: 14px; color: var(--slate-500); line-height: 1.6; max-width: 400px; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .sol-layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .sidebar__hero { display: none; }
            .sidebar__steps { display: flex; gap: 0; border-radius: var(--radius); padding: 16px 20px; }
            .sidebar__steps-title { display: none; }
            .step-list { display: flex; gap: 0; width: 100%; }
            .step-list::before, .step-list__fill { display: none; }
            .step-item { flex: 1; flex-direction: column; text-align: center; gap: 4px; }
            .step-desc { display: none; }
            .step-label { font-size: 10px; }
        }
        @media (max-width: 640px) {
            .card-header, .card-body, .card-footer { padding-left: 20px; padding-right: 20px; }
            .fields { grid-template-columns: 1fr; }
            .field--full { grid-column: auto; }
            .docs-grid { grid-template-columns: 1fr; }
            .moral-inner { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
        }

        /* ── Selfie/firma buttons ── */
        .cam-btns { display: flex; gap: 8px; margin-top: 10px; justify-content: center; flex-wrap: wrap; }
    </style>
</head>

<body>

{{-- Topbar --}}
<header class="topbar">
    <div class="topbar__brand">
        <i class="mdi mdi-store-outline"></i>
        PATS Franquicias
    </div>
    <span class="topbar__tag">Solicitud de alta</span>
    <div class="topbar__secure">
        <i class="mdi mdi-shield-check-outline"></i>
        Seguro
    </div>
</header>

<div class="sol-layout">

    {{-- ════ SIDEBAR ════ --}}
    <aside class="sidebar">
        <div class="sidebar__hero">
            <div class="sidebar__icon"><i class="mdi mdi-store-outline"></i></div>
            <div class="sidebar__title">Solicitud de<br>Franquicia PATS</div>
            <p class="sidebar__sub">Completa el formulario para iniciar tu proceso de alta como franquiciatario.</p>
            <div class="sidebar__badge">
                <div class="sidebar__badge-label">Modalidad</div>
                <div class="sidebar__badge-value">Sin costo</div>
            </div>
        </div>

        <div class="sidebar__steps">
            <div class="sidebar__steps-title">Tu progreso</div>
            @php
                $stepsData = [
                    ['label' => 'Domicilio',   'desc' => 'País, estado y dirección'],
                    ['label' => 'Titular',     'desc' => 'Datos personales y fiscales'],
                    ['label' => 'Bancarios',   'desc' => 'Datos de tu cuenta'],
                    ['label' => 'Documentos',  'desc' => 'Archivos requeridos'],
                    ['label' => 'Biometría',   'desc' => 'Selfie, contrato y firma'],
                ];
            @endphp
            <ul class="step-list" id="stepList">
                <div class="step-list__fill" id="stepFill"></div>
                @foreach ($stepsData as $i => $s)
                    <li class="step-item {{ $i === 0 ? 'is-active' : '' }}" data-step="{{ $i + 1 }}">
                        <div class="step-num"><span>{{ $i + 1 }}</span></div>
                        <div>
                            <div class="step-label">{{ $s['label'] }}</div>
                            <div class="step-desc">{{ $s['desc'] }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    {{-- ════ MAIN ════ --}}
    <main>
        <form id="frmFranq" novalidate enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="public_token" value="{{ $token }}">

            <div class="main-card">

                {{-- Progress bar --}}
                <div class="progress-bar">
                    <div class="progress-bar__fill" id="progressFill" style="width:20%"></div>
                </div>

                {{-- Header --}}
                <div class="card-header">
                    <div>
                        <div class="card-header__tag" id="cardTag">
                            <i class="mdi mdi-map-marker-outline"></i> Paso 1 de 5
                        </div>
                        <h1 class="card-header__title" id="cardTitle">Domicilio</h1>
                        <p class="card-header__desc" id="cardDesc">Indica tu ubicación y dirección de operación.</p>
                    </div>
                    <div class="card-header__counter" id="cardCounter">1 / 5</div>
                </div>

                {{-- Body --}}
                <div class="card-body">

                    {{-- ── PANEL 1: DOMICILIO ── --}}
                    @if(!empty($prefill))
                    <div class="prefill-notice" style="grid-column:1/-1;margin-bottom:4px;">
                        <i class="mdi mdi-lightning-bolt-circle"></i>
                        <span>Datos precargados — Solo completa los campos faltantes: bancarios, documentos y biometría.</span>
                    </div>
                    @endif

                    <div class="panel is-active" data-panel="1">
                        <div class="fields">

                            <div class="field field--full">
                                <label class="label" for="pais">País <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-earth icon-left"></i>
                                    <select class="select" id="pais" name="pais" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="MX" {{ old('pais', $prefill['pais'] ?? '') === 'MX' ? 'selected' : '' }}>México</option>
                                        <option value="US" {{ old('pais', $prefill['pais'] ?? '') === 'US' ? 'selected' : '' }}>Estados Unidos</option>
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="region">Estado <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-map icon-left"></i>
                                    <select class="select" id="region" name="region" required>
                                        <option value="">Seleccionar estado...</option>
                                        @foreach ($estados as $code => $name)
                                            <option value="{{ $code }}" {{ old('region', $prefill['region'] ?? '') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="municipio">Municipio / Alcaldía <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-city-variant-outline icon-left"></i>
                                    <input class="input" type="text" id="municipio" name="municipio"
                                        placeholder="Municipio o alcaldía" required
                                        value="{{ old('municipio', $prefill['municipio'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="calle">Calle <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-road-variant icon-left"></i>
                                    <input class="input" type="text" id="calle" name="calle"
                                        placeholder="Nombre de la calle" required
                                        value="{{ old('calle', $prefill['calle'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="num_ext">Número exterior <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-numeric icon-left"></i>
                                    <input class="input" type="text" id="num_ext" name="num_ext"
                                        placeholder="Ej. 42" required
                                        value="{{ old('num_ext', $prefill['num_ext'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="num_int">Número interior <span class="label__opt">Opcional</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-numeric icon-left"></i>
                                    <input class="input" type="text" id="num_int" name="num_int"
                                        placeholder="Depto, piso, etc."
                                        value="{{ old('num_int', $prefill['num_int'] ?? '') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="colonia">Colonia <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-home-group icon-left"></i>
                                    <input class="input" type="text" id="colonia" name="colonia"
                                        placeholder="Colonia o fraccionamiento" required
                                        value="{{ old('colonia', $prefill['colonia'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="cp">Código postal <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-mailbox-outline icon-left"></i>
                                    <input class="input" type="text" id="cp" name="cp"
                                        placeholder="5 dígitos" maxlength="5" inputmode="numeric" required
                                        value="{{ old('cp', $prefill['cp'] ?? '') }}"
                                        style="font-family:var(--mono)">
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ── PANEL 2: DATOS DEL TITULAR ── --}}
                    <div class="panel" data-panel="2">
                        <div class="fields">

                            {{-- Tipo persona --}}
                            <div class="field field--full" style="margin-bottom:4px;">
                                <label class="label">Tipo de persona <span class="label__req">*</span></label>
                                <div class="tipo-cards">
                                    <input type="radio" class="tipo-radio" name="tipo_persona" id="tp_fisica" value="FISICA" checked>
                                    <label class="tipo-card" for="tp_fisica">
                                        <div class="tipo-card__dot"></div>
                                        <div>
                                            <div class="tipo-card__title">Persona Física</div>
                                            <div class="tipo-card__sub">Con actividad empresarial</div>
                                        </div>
                                    </label>
                                    <input type="radio" class="tipo-radio" name="tipo_persona" id="tp_moral" value="MORAL">
                                    <label class="tipo-card" for="tp_moral">
                                        <div class="tipo-card__dot"></div>
                                        <div>
                                            <div class="tipo-card__title">Persona Moral</div>
                                            <div class="tipo-card__sub">Sociedad o empresa</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="apellido_paterno">Apellido paterno <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-account-outline icon-left"></i>
                                    <input class="input" type="text" id="apellido_paterno" name="apellido_paterno"
                                        placeholder="Primer apellido" required
                                        value="{{ old('apellido_paterno', $prefill['apellido_paterno'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="apellido_materno">Apellido materno <span class="label__opt">Opcional</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-account-outline icon-left"></i>
                                    <input class="input" type="text" id="apellido_materno" name="apellido_materno"
                                        placeholder="Segundo apellido"
                                        value="{{ old('apellido_materno', $prefill['apellido_materno'] ?? '') }}">
                                </div>
                            </div>

                            <div class="field field--full">
                                <label class="label" for="nombre">Nombre(s) <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-account-outline icon-left"></i>
                                    <input class="input" type="text" id="nombre" name="nombre"
                                        placeholder="Nombre(s) de pila" required
                                        value="{{ old('nombre', $prefill['nombre'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="correo">Correo electrónico <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-email-outline icon-left"></i>
                                    <input class="input" type="email" id="correo" name="correo"
                                        placeholder="ejemplo@correo.com" required
                                        value="{{ old('correo', $prefill['correo'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                                <span class="field-msg" id="emailMsg"></span>
                            </div>

                            <div class="field">
                                <label class="label" for="telefono">Teléfono <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-phone-outline icon-left"></i>
                                    <input class="input" type="tel" id="telefono" name="telefono"
                                        placeholder="10 dígitos" maxlength="10" inputmode="numeric" required
                                        value="{{ old('telefono', $prefill['telefono'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                                <span class="field-msg" id="telMsg"></span>
                            </div>

                            <div class="field">
                                <label class="label" for="rfc">RFC <span class="label__opt">Opcional</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-card-account-details-outline icon-left"></i>
                                    <input class="input" type="text" id="rfc" name="rfc"
                                        placeholder="XXXX000000XX0" maxlength="13"
                                        style="text-transform:uppercase;font-family:var(--mono)"
                                        value="{{ old('rfc', $prefill['rfc'] ?? '') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="nacionalidad">Nacionalidad <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-flag-outline icon-left"></i>
                                    <input class="input" type="text" id="nacionalidad" name="nacionalidad"
                                        placeholder="Ej. Mexicana" required
                                        value="{{ old('nacionalidad', $prefill['nacionalidad'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field field--full">
                                <label class="label" for="ocupacion">Actividad / Ocupación / Profesión <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-briefcase-outline icon-left"></i>
                                    <input class="input" type="text" id="ocupacion" name="ocupacion"
                                        placeholder="Ej. Comerciante" required
                                        value="{{ old('ocupacion', $prefill['ocupacion'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="moral-section" id="moralSection">
                                <div class="divider" style="margin-bottom:8px;">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Solo persona moral</span>
                                    <div class="divider__line"></div>
                                </div>
                                <div class="field field--full" id="razonWrap">
                                    <label class="label" for="razon_social">Razón social <span class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-domain icon-left"></i>
                                        <input class="input" type="text" id="razon_social" name="razon_social"
                                            placeholder="Nombre de la empresa"
                                            value="{{ old('razon_social', $prefill['razon_social'] ?? '') }}">
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ── PANEL 3: BANCARIOS ── --}}
                    <div class="panel" data-panel="3">
                        <div class="fields">

                            <div class="field">
                                <label class="label" for="banco">Banco <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-bank-outline icon-left"></i>
                                    <select class="select" id="banco" name="banco" required>
                                        <option value="">Seleccionar banco...</option>
                                        @foreach (['BBVA', 'Banamex / Citibanamex', 'Banorte', 'Santander', 'HSBC', 'Scotiabank', 'Inbursa', 'Afirme', 'BanBajío', 'Multiva', 'Banca Mifel', 'Azteca', 'BanRegio'] as $b)
                                            <option {{ old('banco', $prefill['banco'] ?? '') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="titular_cuenta">Titular de la cuenta <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-account-check-outline icon-left"></i>
                                    <input class="input" type="text" id="titular_cuenta" name="titular_cuenta"
                                        placeholder="Como aparece en el banco" required
                                        value="{{ old('titular_cuenta', $prefill['titular_cuenta'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="numero_cuenta">Número de cuenta <span class="label__opt">Opcional</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-credit-card-outline icon-left"></i>
                                    <input class="input" type="text" id="numero_cuenta" name="numero_cuenta"
                                        placeholder="11 dígitos" maxlength="11" inputmode="numeric"
                                        style="font-family:var(--mono)"
                                        value="{{ old('numero_cuenta', $prefill['numero_cuenta'] ?? '') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label" for="clabe">CLABE interbancaria <span class="label__req">*</span></label>
                                <div class="input-wrap">
                                    <i class="mdi mdi-bank-transfer icon-left"></i>
                                    <input class="input" type="text" id="clabe" name="clabe"
                                        placeholder="18 dígitos" maxlength="18" inputmode="numeric" required
                                        style="font-family:var(--mono)"
                                        value="{{ old('clabe', $prefill['clabe'] ?? '') }}">
                                    <i class="mdi mdi-check-circle icon-status"></i>
                                </div>
                                <span class="field-msg" id="clabeMsg"></span>
                            </div>

                            <div class="field field--full" style="margin-top:8px;">
                                <label class="label">Carátula bancaria <span class="label__opt">Opcional</span></label>
                                <div class="doc-card" id="card_doc_caratula_bancaria" style="max-width:320px;">
                                    <div class="doc-card__top">
                                        <i class="mdi mdi-file-document-outline doc-card__icon"></i>
                                        <span class="doc-card__name">Carátula bancaria</span>
                                        <span class="doc-card__req" id="req_doc_caratula_bancaria">Opcional</span>
                                    </div>
                                    <div class="file-zone" id="zone_doc_caratula_bancaria" style="padding:16px 12px;">
                                        <input type="file" name="doc_caratula_bancaria" id="doc_caratula_bancaria" accept=".pdf,.png,.jpg,.jpeg">
                                        <div class="file-zone__icon" style="font-size:22px;margin-bottom:6px;"><i class="mdi mdi-upload"></i></div>
                                        <div class="file-zone__title" style="font-size:12.5px;">Subir archivo</div>
                                        <div class="file-zone__sub" style="font-size:11px;">PDF · JPG · PNG</div>
                                        <div class="file-zone__name" id="name_doc_caratula_bancaria"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ── PANEL 4: DOCUMENTOS ── --}}
                    <div class="panel" data-panel="4">
                        <div class="fields">

                            <div class="field field--full">
                                <div class="docs-grid">
                                    @foreach ([
                                        ['id' => 'doc_ine',       'icon' => 'card-account-details', 'label' => 'INE / IFE'],
                                        ['id' => 'doc_domicilio', 'icon' => 'home-city',             'label' => 'Comprobante de domicilio'],
                                        ['id' => 'doc_cedula',    'icon' => 'file-certificate',      'label' => 'Cédula fiscal'],
                                    ] as $doc)
                                        <div class="doc-card" id="card_{{ $doc['id'] }}">
                                            <div class="doc-card__top">
                                                <i class="mdi mdi-{{ $doc['icon'] }} doc-card__icon"></i>
                                                <span class="doc-card__name">{{ $doc['label'] }}</span>
                                                <span class="doc-card__req" id="req_{{ $doc['id'] }}">Requerido</span>
                                            </div>
                                            <div class="file-zone" id="zone_{{ $doc['id'] }}" style="padding:16px 12px;">
                                                <input type="file" name="{{ $doc['id'] }}" id="{{ $doc['id'] }}" accept=".pdf,.png,.jpg,.jpeg">
                                                <div class="file-zone__icon" style="font-size:22px;margin-bottom:6px;"><i class="mdi mdi-upload"></i></div>
                                                <div class="file-zone__title" style="font-size:12.5px;">Subir archivo</div>
                                                <div class="file-zone__sub" style="font-size:11px;">PDF · JPG · PNG</div>
                                                <div class="file-zone__name" id="name_{{ $doc['id'] }}"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Persona moral extras --}}
                            <div class="moral-section" id="moralSectionDocs">
                                <div class="divider" style="margin-bottom:12px;">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Solo persona moral</span>
                                    <div class="divider__line"></div>
                                </div>
                                <div class="moral-inner">
                                    @foreach ([
                                        ['id' => 'doc_acta',  'name' => 'doc_acta_constitutiva', 'icon' => 'file-document',  'label' => 'Acta constitutiva'],
                                        ['id' => 'doc_poder', 'name' => 'doc_poder_notarial',    'icon' => 'gavel',          'label' => 'Poder notarial'],
                                    ] as $doc)
                                        <div class="file-zone" id="zone_{{ $doc['id'] }}" style="border:1.5px dashed var(--border);border-radius:var(--radius-sm);padding:20px 12px;text-align:center;">
                                            <input type="file" name="{{ $doc['name'] }}" id="{{ $doc['id'] }}" accept=".pdf,.png,.jpg,.jpeg">
                                            <div class="file-zone__icon"><i class="mdi mdi-{{ $doc['icon'] }}"></i></div>
                                            <div class="file-zone__title">{{ $doc['label'] }}</div>
                                            <div class="file-zone__sub">Al menos uno es requerido</div>
                                            <div class="file-zone__name" id="name_{{ $doc['id'] }}"></div>
                                        </div>
                                    @endforeach
                                </div>
                                <p style="font-size:12px;color:var(--slate-400);margin-top:8px;display:flex;align-items:center;gap:4px;">
                                    <i class="mdi mdi-information-outline" style="color:var(--blue-400);"></i>
                                    Sube al menos el acta constitutiva o el poder notarial.
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- ── PANEL 5: BIOMETRÍA Y CONTRATO ── --}}
                    <div class="panel" data-panel="5">
                        <div class="fields">

                            <p class="field--full" style="font-size:13.5px;color:var(--slate-500);line-height:1.6;margin-bottom:4px;">
                                Toma tu fotografía directamente desde la cámara, revisa el contrato de franquicia,
                                dibuja tu firma y acepta los términos.
                            </p>

                            {{-- Selfie --}}
                            <div class="divider" style="margin-top:4px;">
                                <div class="divider__line"></div>
                                <span class="divider__label">Fotografía del titular</span>
                                <div class="divider__line"></div>
                            </div>

                            <div class="field field--full">
                                <label class="label">Fotografía del titular <span class="label__req">*</span></label>
                                <div id="selfie_cam_area">
                                    <div class="selfie-cam-wrap" id="selfieCamWrap">
                                        <div class="selfie-cam-placeholder" id="selfiePlaceholder">
                                            <i class="mdi mdi-camera-off"></i>
                                            <span>Activa la cámara para tomar la fotografía</span>
                                        </div>
                                        <video id="selfieVideo" autoplay playsinline muted style="display:none;width:100%;max-height:220px;object-fit:cover;transform:scaleX(-1);"></video>
                                        <img id="selfieCaptura" style="display:none;width:100%;max-height:220px;object-fit:cover;" alt="Foto capturada">
                                    </div>
                                    <canvas id="selfieCanvas" style="display:none;"></canvas>
                                    <div class="cam-btns">
                                        <button type="button" class="btn btn--primary" id="btnStartCamera" style="font-size:13px;padding:9px 16px;">
                                            <i class="mdi mdi-camera"></i> Activar cámara
                                        </button>
                                        <button type="button" class="btn btn--primary" id="btnTakePhoto" style="display:none;font-size:13px;padding:9px 16px;">
                                            <i class="mdi mdi-camera-iris"></i> Tomar foto
                                        </button>
                                        <button type="button" class="btn btn--ghost" id="btnRetakePhoto" style="display:none;font-size:13px;padding:9px 16px;">
                                            <i class="mdi mdi-camera-retake"></i> Repetir
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="selfie_data" id="selfie_data">
                            </div>

                            {{-- Contrato --}}
                            <div class="divider">
                                <div class="divider__line"></div>
                                <span class="divider__label">Contrato de franquicia</span>
                                <div class="divider__line"></div>
                            </div>

                            {{-- Contrato incrustado como template --}}
                            <template id="contratoTpl">@include('pats.contrato_franq')</template>

                            <div class="field field--full">
                                <label class="label">Lee el contrato antes de firmar</label>
                                <div class="contrato-viewer">
                                    <div class="contrato-viewer__header">
                                        <i class="mdi mdi-file-sign"></i>
                                        <span>Los datos se llenan automáticamente con la información que ingresas en el formulario.</span>
                                    </div>
                                    <div id="contratoContainer" class="contrato-viewer__body"></div>
                                </div>
                                <p style="font-size:11.5px;color:var(--slate-400);margin-top:6px;">
                                    <i class="mdi mdi-information-outline"></i>
                                    Lee el contrato completo, luego dibuja tu firma en la sección siguiente y acepta los términos.
                                </p>
                            </div>

                            {{-- Firma digital --}}
                            <div class="divider">
                                <div class="divider__line"></div>
                                <span class="divider__label">Firma digital</span>
                                <div class="divider__line"></div>
                            </div>

                            <div class="field field--full">
                                <label class="label">Firma del titular <span class="label__req">*</span></label>
                                <div class="firma-wrap" id="firmaWrap">
                                    <canvas id="firmaCanvas"></canvas>
                                    <div class="firma-placeholder" id="firmaPlaceholder">
                                        <i class="mdi mdi-draw-pen"></i> Firma aquí con el mouse o el dedo
                                    </div>
                                </div>
                                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                    <button type="button" class="btn btn--ghost" id="btnClearFirma" style="font-size:12px;padding:7px 14px;">
                                        <i class="mdi mdi-eraser"></i> Limpiar firma
                                    </button>
                                </div>
                                <input type="hidden" name="firma_data" id="firma_data">
                            </div>

                            {{-- Resumen --}}
                            <div class="divider">
                                <div class="divider__line"></div>
                                <span class="divider__label">Resumen de solicitud</span>
                                <div class="divider__line"></div>
                            </div>

                            <div class="field--full">
                                <div class="summary-grid">
                                    <div class="summary-item"><div class="summary-item__label">Titular</div><div class="summary-item__value" id="sum_nombre">—</div></div>
                                    <div class="summary-item"><div class="summary-item__label">Correo</div><div class="summary-item__value" id="sum_correo">—</div></div>
                                    <div class="summary-item"><div class="summary-item__label">Ubicación</div><div class="summary-item__value" id="sum_ubicacion">—</div></div>
                                    <div class="summary-item"><div class="summary-item__label">Tipo persona</div><div class="summary-item__value" id="sum_tipo">—</div></div>
                                    <div class="summary-item"><div class="summary-item__label">Banco</div><div class="summary-item__value" id="sum_banco">—</div></div>
                                    <div class="summary-item"><div class="summary-item__label">Modalidad</div><div class="summary-item__value" id="sum_modal">Sin costo</div></div>
                                </div>
                            </div>

                            {{-- Aceptación --}}
                            <div class="divider">
                                <div class="divider__line"></div>
                                <span class="divider__label">Aceptación</span>
                                <div class="divider__line"></div>
                            </div>

                            <div class="field--full">
                                <label class="terms-box">
                                    <input type="checkbox" id="acepta_terminos" name="acepta_terminos" required>
                                    <span class="terms-box__text">
                                        He leído y acepto los <a href="#" target="_blank">Términos y Condiciones</a>
                                        y el <a href="#" target="_blank">Contrato de Franquicia PATS</a>.
                                        Confirmo que toda la información proporcionada es verídica.
                                    </span>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>{{-- .card-body --}}

                {{-- Footer --}}
                <div class="card-footer">
                    <button type="button" class="btn btn--ghost" id="btnPrev" disabled>
                        <i class="mdi mdi-arrow-left"></i> Anterior
                    </button>
                    <button type="button" class="btn btn--primary" id="btnNext">
                        Siguiente <i class="mdi mdi-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn--submit" id="btnSubmit" style="display:none;" disabled>
                        <i class="mdi mdi-check-circle"></i> Enviar solicitud
                    </button>
                </div>

            </div>{{-- .main-card --}}

            {{-- Success screen --}}
            <div class="main-card success-screen" id="successScreen">
                <div class="success-icon"><i class="mdi mdi-check-circle"></i></div>
                <div class="success-title">¡Solicitud enviada!</div>
                <div class="success-ref" id="successRef">—</div>
                <p class="success-note">Tu solicitud de franquicia ha sido recibida correctamente. El equipo PATS la revisará y se pondrá en contacto contigo a la brevedad.</p>
                <p style="font-size:13px;color:var(--slate-400);margin-top:12px;">Recibirás una confirmación en tu correo electrónico.</p>
            </div>

        </form>
    </main>

</div>{{-- .sol-layout --}}

{{-- Toast --}}
<div class="toast" id="toast" role="alert" aria-live="assertive">
    <i id="toastIcon"></i>
    <span id="toastMsg"></span>
</div>

<script>
    (function() {
        'use strict';

        /* ─── Config ─────────────────────────────── */
        const TOTAL = 5;
        let current = 1;
        let goingBack = false;

        /* ─── Prefill ────────────────────────────── */
        const PREFILL = @json($prefill ?? null);
        const SKIP_STEPS = (() => {
            if (!PREFILL) return new Set();
            const s = new Set();
            const s1 = ['pais','region','municipio','calle','num_ext','cp','colonia'];
            if (s1.every(f => PREFILL[f] != null && String(PREFILL[f]).trim() !== '')) s.add(1);
            const s2 = ['apellido_paterno','nombre','telefono','correo','nacionalidad','ocupacion'];
            if (s2.every(f => PREFILL[f] != null && String(PREFILL[f]).trim() !== '')) s.add(2);
            return s;
        })();
        const VISIBLE_STEPS = Array.from({length: TOTAL}, (_, i) => i + 1)
            .filter(s => !SKIP_STEPS.has(s));

        function nextVisible(from) {
            for (let s = from + 1; s <= TOTAL; s++) if (!SKIP_STEPS.has(s)) return s;
            return null;
        }
        function prevVisible(from) {
            for (let s = from - 1; s >= 1; s--) if (!SKIP_STEPS.has(s)) return s;
            return null;
        }
        function getPrevStep() {
            return SKIP_STEPS.has(current) ? (current > 1 ? current - 1 : null) : prevVisible(current);
        }

        const STEP_META = [
            { tag: 'Paso 1 de 5', icon: 'mdi-map-marker-outline',  title: 'Domicilio',           desc: 'Indica tu ubicación y dirección de operación.' },
            { tag: 'Paso 2 de 5', icon: 'mdi-account-outline',      title: 'Datos del titular',   desc: 'Información personal y fiscal del solicitante.' },
            { tag: 'Paso 3 de 5', icon: 'mdi-bank-outline',         title: 'Datos bancarios',     desc: 'Cuenta donde recibirás y realizarás operaciones.' },
            { tag: 'Paso 4 de 5', icon: 'mdi-folder-open-outline',  title: 'Documentación',       desc: 'Sube los documentos solicitados en formato digital.' },
            { tag: 'Paso 5 de 5', icon: 'mdi-camera-account',       title: 'Biometría y contrato','desc': 'Selfie, contrato firmado y firma digital.' },
        ];

        /* ─── Helpers ────────────────────────────── */
        const $ = id => document.getElementById(id);
        const $$ = sel => [...document.querySelectorAll(sel)];
        const digits = v => String(v ?? '').replace(/\D/g, '');
        const validEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v).trim());

        function validClabe(v) {
            if (v.length !== 18) return false;
            const f = [3, 7, 1];
            let s = 0;
            for (let i = 0; i < 17; i++) s += (Number(v[i]) * f[i % 3]) % 10;
            return (10 - (s % 10)) % 10 === Number(v[17]);
        }

        function toast(msg, type = 'info', ms = 3200) {
            const el = $('toast'), ic = $('toastIcon'), tx = $('toastMsg');
            const icons = { error: 'mdi-alert-circle', success: 'mdi-check-circle', info: 'mdi-information-outline' };
            el.className = `toast toast--${type}`;
            ic.className = `mdi ${icons[type]}`;
            tx.textContent = msg;
            el.classList.add('show');
            clearTimeout(el._t);
            el._t = setTimeout(() => el.classList.remove('show'), ms);
        }

        function setValidity(inp, ok, msgId, msg = '') {
            if (!inp) return;
            inp.classList.toggle('is-valid',   ok && !!inp.value);
            inp.classList.toggle('is-invalid', !ok && !!inp.value);
            const el = $(msgId);
            if (el) { el.textContent = msg; el.className = `field-msg field-msg--${ok?'hint':'error'}`; }
        }

        /* ─── UI sync ────────────────────────────── */
        function syncUI() {
            const visIdx    = VISIBLE_STEPS.indexOf(current);
            const visCnt    = VISIBLE_STEPS.length;
            const reviewing = visIdx === -1;
            const pct       = reviewing ? 0 : (visCnt > 1 ? Math.round((visIdx / (visCnt - 1)) * 100) : 100);
            const meta      = STEP_META[current - 1];
            const isLast    = nextVisible(current) === null;

            $('progressFill').style.width = pct + '%';
            $('cardTag').innerHTML = `<i class="mdi ${meta.icon}"></i> ${meta.tag}`;
            $('cardTitle').textContent = meta.title;
            $('cardDesc').textContent  = meta.desc;
            $('cardCounter').textContent = reviewing ? 'Revisando' : `${visIdx + 1} / ${visCnt}`;

            $$('.step-item').forEach(el => {
                const s = Number(el.dataset.step);
                el.classList.toggle('is-active',    s === current);
                el.classList.toggle('is-done',      s < current && !SKIP_STEPS.has(s) && !STEP_ERRORS.has(s));
                el.classList.toggle('is-prefilled', SKIP_STEPS.has(s) && s !== current);
                el.classList.toggle('is-error',     STEP_ERRORS.has(s) && s !== current);
                if (s < current || SKIP_STEPS.has(s)) el.classList.add('step-item--clickable');
            });

            const fill = $('stepFill');
            if (fill) fill.style.height = `${reviewing ? 0 : (visCnt > 1 ? (visIdx / (visCnt - 1)) * 100 : 100)}%`;

            $('btnPrev').disabled = getPrevStep() === null;
            $('btnNext').style.display   = isLast ? 'none' : '';
            $('btnSubmit').style.display = isLast ? '' : 'none';

            if (isLast) updateSummary();
        }

        /* ─── Panel transitions ──────────────────── */
        function goStep(next) {
            if (next === current) return;
            goingBack = next < current;
            const leaving  = document.querySelector(`[data-panel="${current}"]`);
            const entering = document.querySelector(`[data-panel="${next}"]`);
            if (!entering) return;
            leaving.classList.remove('is-active');
            setTimeout(() => {
                entering.classList.toggle('slide-back', goingBack);
                entering.classList.add('is-active');
                setTimeout(() => entering.classList.remove('slide-back'), 450);
                current = next;
                syncUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (next === 5) window.dispatchEvent(new Event('resize'));
            }, 60);
        }

        /* ─── Step errors ────────────────────────── */
        const STEP_ERRORS = new Set();

        function hasStepError(s) {
            if (SKIP_STEPS.has(s)) return false;
            if (s === 1) return ['pais','region','municipio','calle','num_ext','cp','colonia'].some(id => !$(id)?.value.trim());
            if (s === 2) return !$('nombre')?.value.trim() || !validEmail($('correo')?.value||'') || digits($('telefono')?.value||'').length !== 10;
            if (s === 3) {
                const c = digits($('clabe')?.value||'');
                return !$('banco')?.value || !$('titular_cuenta')?.value.trim() || (c !== '' && !validClabe(c));
            }
            if (s === 4) {
                if (['doc_ine','doc_domicilio','doc_cedula'].some(id => !$(id)?.files?.length)) return true;
                if (document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL') {
                    if (!$('doc_acta')?.files?.length && !$('doc_poder')?.files?.length) return true;
                }
                return false;
            }
            if (s === 5) return !$('selfie_data')?.value || !$('firma_data')?.value || !$('acepta_terminos')?.checked;
            return false;
        }

        /* ─── Validation per step ────────────────── */
        function validateStep(s) {
            if (s === 1) {
                for (const id of ['pais','region','municipio','calle','num_ext','cp','colonia']) {
                    const el = $(id);
                    if (!el?.value.trim()) { toast('Completa el campo requerido.','error'); el?.focus(); return false; }
                }
            }
            if (s === 2) {
                if (!$('nombre')?.value.trim()) { toast('Escribe el nombre del titular.','error'); $('nombre').focus(); return false; }
                if (!validEmail($('correo')?.value||'')) { toast('El correo no es válido.','error'); $('correo').focus(); return false; }
                if (digits($('telefono')?.value||'').length !== 10) { toast('El teléfono debe tener 10 dígitos.','error'); $('telefono').focus(); return false; }
                if (!$('nacionalidad')?.value.trim()) { toast('Indica la nacionalidad.','error'); $('nacionalidad').focus(); return false; }
                if (!$('ocupacion')?.value.trim()) { toast('Indica la ocupación.','error'); $('ocupacion').focus(); return false; }
                const tp = document.querySelector('input[name=tipo_persona]:checked')?.value;
                if (tp === 'MORAL' && !$('razon_social')?.value.trim()) { toast('Para persona moral ingresa la razón social.','error'); $('razon_social').focus(); return false; }
            }
            if (s === 3) {
                if (!$('banco')?.value) { toast('Selecciona el banco.','error'); return false; }
                if (!$('titular_cuenta')?.value.trim()) { toast('Escribe el titular de la cuenta.','error'); $('titular_cuenta').focus(); return false; }
                const c = digits($('clabe')?.value||'');
                if (!c) { toast('La CLABE es obligatoria.','error'); $('clabe').focus(); return false; }
                if (!validClabe(c)) { toast('La CLABE no es válida (18 dígitos con dígito verificador).','error'); $('clabe').focus(); return false; }
            }
            if (s === 4) {
                for (const id of ['doc_ine','doc_domicilio','doc_cedula']) {
                    if (!$(id)?.files?.length) { toast('Sube todos los documentos requeridos.','error'); return false; }
                }
                const tp = document.querySelector('input[name=tipo_persona]:checked')?.value;
                if (tp === 'MORAL') {
                    if (!$('doc_acta')?.files?.length && !$('doc_poder')?.files?.length) {
                        toast('Para persona moral sube al menos el acta constitutiva o el poder notarial.','error');
                        return false;
                    }
                }
            }
            if (s === 5) {
                if (!$('selfie_data')?.value) { toast('Toma la fotografía del titular.','error'); return false; }
                if (!$('firma_data')?.value)  { toast('Dibuja la firma digital del titular.','error'); return false; }
                if (!$('acepta_terminos')?.checked) { toast('Debes aceptar los términos del contrato.','error'); return false; }
            }
            return true;
        }

        /* ─── Live validation ────────────────────── */
        function bindLive() {
            $('correo')?.addEventListener('input', e => {
                const ok = validEmail(e.target.value);
                setValidity(e.target, ok, 'emailMsg', ok ? '' : 'Correo no válido');
            });
            $('telefono')?.addEventListener('input', e => {
                e.target.value = digits(e.target.value).slice(0, 10);
                const ok = e.target.value.length === 10;
                setValidity(e.target, ok, 'telMsg', ok ? '' : `${10-e.target.value.length} dígitos restantes`);
            });
            $('clabe')?.addEventListener('input', e => {
                e.target.value = digits(e.target.value).slice(0, 18);
                const len = e.target.value.length;
                if (len === 18) { const ok = validClabe(e.target.value); setValidity(e.target, ok, 'clabeMsg', ok ? '✓ CLABE válida' : 'CLABE inválida'); }
                else if (len > 0) { setValidity(e.target, false, 'clabeMsg', `${18-len} dígitos restantes`); }
            });
            $('cp')?.addEventListener('input', e => {
                e.target.value = digits(e.target.value).slice(0, 5);
                if (e.target.value.length === 5) e.target.classList.add('is-valid');
            });
            $('rfc')?.addEventListener('input', e => { e.target.value = e.target.value.toUpperCase(); });
            $$('.input[required],.select[required]').forEach(el => {
                el.addEventListener('blur', () => {
                    if (el.value.trim()) el.classList.add('is-valid');
                    else if (el.value !== '') el.classList.add('is-invalid');
                });
            });
            $('acepta_terminos')?.addEventListener('change', e => {
                $('btnSubmit').disabled = !e.target.checked;
            });
        }

        /* ─── Tipo persona ───────────────────────── */
        function bindTipo() {
            $$('input[name=tipo_persona]').forEach(r => {
                r.addEventListener('change', () => {
                    const moral = r.value === 'MORAL';
                    $('moralSection')?.classList.toggle('open', moral);
                    $('moralSectionDocs')?.classList.toggle('open', moral);
                    const razon = $('razon_social');
                    if (razon) razon.required = moral;
                });
            });
        }

        /* ─── File zones ─────────────────────────── */
        function bindFiles() {
            $$('.file-zone').forEach(zone => {
                const input  = zone.querySelector('input[type=file]');
                const nameEl = zone.querySelector('.file-zone__name');
                if (!input || !nameEl) return;
                input.addEventListener('change', () => {
                    const f = input.files[0];
                    if (!f) return;
                    nameEl.textContent = `✓ ${f.name}`;
                    zone.classList.add('filled');
                    zone.closest('.doc-card')?.classList.add('filled');
                    const reqEl = zone.closest('.doc-card')?.querySelector('.doc-card__req');
                    if (reqEl) reqEl.textContent = '✓ OK';
                });
                zone.addEventListener('click', e => { if (e.target !== input) input.click(); });
                zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
                zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
                zone.addEventListener('drop', e => {
                    e.preventDefault(); zone.classList.remove('dragover');
                    if (e.dataTransfer.files.length) {
                        const dt = new DataTransfer(); dt.items.add(e.dataTransfer.files[0]);
                        input.files = dt.files; input.dispatchEvent(new Event('change'));
                    }
                });
            });
        }

        /* ─── Contrato HTML live (igual que distribuidores) ── */
        function bindContractContent() {
            const container = $('contratoContainer');
            if (!container) return;

            const ESTADOS_NOMBRES = {
                AGS:'Aguascalientes',BCN:'Baja California',BCS:'Baja California Sur',
                CAM:'Campeche',CHP:'Chiapas',CHH:'Chihuahua',CDMX:'Ciudad de México',
                COA:'Coahuila',COL:'Colima',DUR:'Durango',MEX:'Estado de México',
                GTO:'Guanajuato',GRO:'Guerrero',HGO:'Hidalgo',JAL:'Jalisco',
                MIC:'Michoacán',MOR:'Morelos',NAY:'Nayarit',NLE:'Nuevo León',
                OAX:'Oaxaca',PUE:'Puebla',QRO:'Querétaro',ROO:'Quintana Roo',
                SLP:'San Luis Potosí',SIN:'Sinaloa',SON:'Sonora',TAB:'Tabasco',
                TAM:'Tamaulipas',TLAX:'Tlaxcala',VER:'Veracruz',YUC:'Yucatán',ZAC:'Zacatecas'
            };

            const MESES = ['enero','febrero','marzo','abril','mayo','junio',
                           'julio','agosto','septiembre','octubre','noviembre','diciembre'];

            function setField(id, val) {
                const el = container.querySelector('#' + id);
                if (!el) return;
                if (val && String(val).trim()) {
                    el.textContent = val;
                    el.style.cssText = 'font-style:normal;font-weight:600;color:#1a1a1a;background:rgba(59,116,245,.08);border:1px solid rgba(59,116,245,.25);padding:2px 8px;border-radius:3px';
                } else {
                    el.textContent = '( ___ )';
                    el.style.cssText = '';
                }
            }

            function fillContract() {
                if (!container.innerHTML) return;
                const moral    = document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL';
                const nombre   = $('nombre')?.value.trim()           || '';
                const apPat    = $('apellido_paterno')?.value.trim() || '';
                const apMat    = $('apellido_materno')?.value.trim() || '';
                const nombreCompleto = [nombre, apPat, apMat].filter(Boolean).join(' ');
                const razonSocial    = $('razon_social')?.value.trim() || '';
                const denominacion   = moral && razonSocial ? razonSocial : nombreCompleto;
                const cargo          = moral ? 'Representante Legal' : 'Titular';

                const calle   = $('calle')?.value.trim()   || '';
                const numExt  = $('num_ext')?.value.trim() || '';
                const numInt  = $('num_int')?.value.trim() || '';
                const colonia = $('colonia')?.value.trim() || '';
                const cp      = $('cp')?.value.trim()      || '';
                const muni    = $('municipio')?.value.trim() || '';
                const reg     = $('region')?.value          || '';
                const estadoNombre = ESTADOS_NOMBRES[reg] || reg;

                const domicilio = [
                    calle + (numExt ? ' ' + numExt : '') + (numInt ? ' Int.' + numInt : ''),
                    colonia ? 'Col. ' + colonia : '',
                    cp ? 'C.P. ' + cp : '',
                    muni, estadoNombre,
                ].filter(Boolean).join(', ');

                const hoy = new Date();
                const dia  = String(hoy.getDate()).padStart(2, '0');
                const mes  = MESES[hoy.getMonth()];

                // Encabezado y tabla de firmas
                setField('c_denominacion',       denominacion);
                setField('c_cargo',              cargo);
                setField('c_representante',      nombreCompleto);
                setField('c_denominacion_firma', denominacion);
                setField('c_cargo_firma',        cargo);
                setField('c_representante_firma',nombreCompleto);
                setField('c_denominacion_anexo', denominacion);

                // Declaración II — datos del franquiciatario
                setField('c_rfc',      $('rfc')?.value.trim().toUpperCase() || '');
                setField('c_domicilio', domicilio);

                // Fecha de firma
                setField('c_dia_firma', dia);
                setField('c_mes_firma', mes);

                // Anexo 1 — territorio
                const territorio = [muni, estadoNombre].filter(Boolean).join(', ');
                setField('c_territorio', territorio);
            }

            // Cargar el contrato desde el <template>
            const tpl  = document.getElementById('contratoTpl');
            const html = tpl ? tpl.innerHTML : '';
            if (html.trim()) {
                const bodyMatch  = html.match(/<body[^>]*>([\s\S]*)<\/body>/i);
                const styleMatch = html.match(/<style[^>]*>([\s\S]*?)<\/style>/gi) || [];
                const styles     = styleMatch.map(s => s.replace(/<\/?style[^>]*>/gi, '')).join('\n');
                container.innerHTML =
                    `<style>${styles}</style>` +
                    (bodyMatch ? bodyMatch[1] : html);
                fillContract();
            } else {
                container.innerHTML = '<p style="padding:20px;color:#64748b;">No se pudo cargar el contrato.</p>';
            }

            // Re-llenar cuando cambian los campos relevantes
            ['nombre','apellido_paterno','apellido_materno','razon_social',
             'rfc','calle','num_ext','num_int','colonia','cp','municipio','region',
             'correo','telefono',
            ].forEach(id => {
                $(id)?.addEventListener('input',  fillContract);
                $(id)?.addEventListener('change', fillContract);
            });
            document.querySelectorAll('input[name=tipo_persona]')
                .forEach(r => r.addEventListener('change', fillContract));
        }

        function loadContrato() { bindContractContent(); }

        /* ─── Summary ────────────────────────────── */
        function updateSummary() {
            const g = id => $(id)?.value?.trim() || '—';
            const tipo = document.querySelector('input[name=tipo_persona]:checked')?.value;
            $('sum_nombre').textContent   = g('nombre');
            $('sum_correo').textContent   = g('correo');
            $('sum_ubicacion').textContent = [g('municipio'), g('region')].filter(v => v !== '—').join(', ');
            $('sum_tipo').textContent     = tipo === 'MORAL' ? 'Persona Moral' : 'Persona Física';
            $('sum_banco').textContent    = g('banco');
        }

        /* ─── Submit ─────────────────────────────── */
        function bindSubmit() {
            const preValidarUrl = '{{ route("franq.link.pre-validar", $token) }}';
            const submitUrl     = '{{ route("franq.link.guardar", $token) }}';

            $('frmFranq')?.addEventListener('submit', async e => {
                e.preventDefault();

                let firstErrStep = null;
                for (let s = 1; s <= TOTAL; s++) {
                    if (hasStepError(s)) { STEP_ERRORS.add(s); if (!firstErrStep) firstErrStep = s; }
                    else { STEP_ERRORS.delete(s); }
                }
                if (firstErrStep !== null) {
                    syncUI();
                    if (firstErrStep !== current) goStep(firstErrStep);
                    toast('Revisa los pasos marcados en rojo antes de enviar.', 'error', 5000);
                    return;
                }
                if (!validateStep(TOTAL)) return;

                const btn  = $('btnSubmit');
                const csrf = document.querySelector('meta[name=csrf-token]').content;
                btn.disabled = true;

                try {
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Verificando datos...';
                    const fdPre = new FormData(e.target);
                    const preRes = await fetch(preValidarUrl, {
                        method: 'POST', body: fdPre,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    });
                    const preData = await preRes.json().catch(() => ({}));
                    if (!preRes.ok || preData.ok === false) throw new Error(preData.error || 'Error en la validación de datos.');

                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Guardando solicitud...';
                    const fd = new FormData(e.target);
                    const res = await fetch(submitUrl, {
                        method: 'POST', body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || data.ok === false) throw new Error(data.error || 'Error al guardar la solicitud.');

                    // Show success screen
                    document.querySelector('.main-card:not(.success-screen)').style.display = 'none';
                    const ss = $('successScreen');
                    ss.classList.add('show');
                    $('successRef').textContent = data.referencia || '—';

                } catch (err) {
                    toast(err.message || 'No fue posible enviar la solicitud.', 'error', 5000);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-check-circle"></i> Enviar solicitud';
                }
            });
        }

        /* ─── Nav ────────────────────────────────── */
        function bindNav() {
            $('btnNext')?.addEventListener('click', () => {
                if (!SKIP_STEPS.has(current) && !validateStep(current)) { STEP_ERRORS.add(current); syncUI(); return; }
                STEP_ERRORS.delete(current);
                const next = nextVisible(current);
                if (next !== null) {
                    if (next === 5) loadContrato();
                    goStep(next);
                }
            });
            $('btnPrev')?.addEventListener('click', () => {
                const prev = getPrevStep();
                if (prev !== null) goStep(prev);
            });
            $$('.step-item').forEach(el => {
                el.addEventListener('click', () => {
                    const s = Number(el.dataset.step);
                    if (s !== current && (s < current || SKIP_STEPS.has(s))) goStep(s);
                });
            });
        }

        /* ─── Live error clearing ────────────────── */
        function bindErrorClearing() {
            ['input', 'change'].forEach(evt => {
                document.addEventListener(evt, () => {
                    if (!STEP_ERRORS.has(current)) return;
                    if (!hasStepError(current)) { STEP_ERRORS.delete(current); syncUI(); }
                });
            });
        }

        /* ─── Selfie ─────────────────────────────── */
        let selfieStream = null;

        function bindSelfie() {
            const video      = $('selfieVideo');
            const canvas     = $('selfieCanvas');
            const captura    = $('selfieCaptura');
            const placeholder = $('selfiePlaceholder');
            const btnStart   = $('btnStartCamera');
            const btnTake    = $('btnTakePhoto');
            const btnRetake  = $('btnRetakePhoto');
            const dataInput  = $('selfie_data');

            btnStart?.addEventListener('click', async () => {
                if (!navigator.mediaDevices?.getUserMedia) {
                    toast('La cámara requiere conexión segura (HTTPS). Contacta al administrador.', 'error'); return;
                }
                try {
                    selfieStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } });
                    if (video) { video.srcObject = selfieStream; video.style.display = ''; }
                    if (placeholder) placeholder.style.display = 'none';
                    if (captura) captura.style.display = 'none';
                    btnStart.style.display = 'none';
                    if (btnTake) btnTake.style.display = '';
                } catch (err) {
                    toast('No se pudo acceder a la cámara: ' + (err.message || err.name), 'error');
                }
            });

            btnTake?.addEventListener('click', () => {
                if (!video || !canvas) return;
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.save(); ctx.scale(-1, 1); ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height); ctx.restore();
                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                if (dataInput) dataInput.value = dataUrl;
                if (captura) { captura.src = dataUrl; captura.style.display = ''; }
                if (video) video.style.display = 'none';
                if (placeholder) placeholder.style.display = 'none';
                btnTake.style.display = 'none';
                if (btnRetake) btnRetake.style.display = '';
                if (selfieStream) { selfieStream.getTracks().forEach(t => t.stop()); selfieStream = null; }
            });

            btnRetake?.addEventListener('click', async () => {
                if (dataInput) dataInput.value = '';
                if (captura) captura.style.display = 'none';
                btnRetake.style.display = 'none';
                btnStart.style.display = '';
            });
        }

        /* ─── Firma ──────────────────────────────── */
        function bindFirma() {
            const canvas     = $('firmaCanvas');
            const wrap       = $('firmaWrap');
            const placeholder = $('firmaPlaceholder');
            const dataInput  = $('firma_data');
            const btnClear   = $('btnClearFirma');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            let drawing = false, hasMark = false;

            function resizeCanvas() {
                const w = wrap.clientWidth;
                const h = 160;
                const img = hasMark ? canvas.toDataURL() : null;
                canvas.width = w; canvas.height = h;
                ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
                if (img) { const i = new Image(); i.onload = () => ctx.drawImage(i, 0, 0, w, h); i.src = img; }
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            function getPos(e) {
                const r = canvas.getBoundingClientRect();
                const src = e.touches ? e.touches[0] : e;
                return { x: src.clientX - r.left, y: src.clientY - r.top };
            }
            function startDraw(e) { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
            function doDraw(e) {
                if (!drawing) return; e.preventDefault();
                const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke();
                if (!hasMark) { hasMark = true; wrap.classList.add('signed'); if (placeholder) placeholder.style.display = 'none'; }
            }
            function endDraw(e) {
                if (!drawing) return; drawing = false;
                if (dataInput) dataInput.value = canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown',  startDraw);
            canvas.addEventListener('mousemove',  doDraw);
            canvas.addEventListener('mouseup',    endDraw);
            canvas.addEventListener('mouseleave', endDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove',  doDraw,    { passive: false });
            canvas.addEventListener('touchend',   endDraw);

            btnClear?.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasMark = false;
                wrap.classList.remove('signed');
                if (placeholder) placeholder.style.display = '';
                if (dataInput) dataInput.value = '';
            });
        }

        /* ─── Apply prefill ──────────────────────── */
        function applyPrefill() {
            if (!PREFILL) return;
            Object.entries(PREFILL).forEach(([key, val]) => {
                const el = document.querySelector(`[name="${key}"]`);
                if (!el || !val) return;
                if (el.tagName === 'SELECT') {
                    [...el.options].forEach(o => { if (o.value === String(val)) o.selected = true; });
                } else if (el.type === 'radio') {
                    $$(`[name="${key}"]`).forEach(r => { if (r.value === String(val)) r.checked = true; });
                } else {
                    if (!el.value) el.value = val;
                }
            });
            // Trigger tipo persona display
            const tp = document.querySelector('input[name=tipo_persona]:checked');
            if (tp?.value === 'MORAL') { $('moralSection')?.classList.add('open'); $('moralSectionDocs')?.classList.add('open'); }
        }

        /* ─── Init ───────────────────────────────── */
        function init() {
            applyPrefill();
            bindLive();
            bindTipo();
            bindFiles();
            bindSelfie();
            bindFirma();
            bindNav();
            bindSubmit();
            bindErrorClearing();
            bindContractContent();
            syncUI();
        }

        document.addEventListener('DOMContentLoaded', init);

    })();
</script>
</body>
</html>
