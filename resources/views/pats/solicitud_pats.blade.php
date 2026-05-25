{{-- resources/views/pats/solicitud_pats.blade.php --}}
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PATS · Activa tu Pasaporte</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ez.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        :root {
            --navy: #0d1b3e;
            --navy-2: #162550;
            --blue: #1d4ed8;
            --blue-mid: #2563eb;
            --blue-light: #3b82f6;
            --cyan: #06b6d4;
            --cyan-light: #22d3ee;
            --surface: #ffffff;
            --surface-2: #f8faff;
            --border: rgba(37, 99, 235, .13);
            --border-2: rgba(37, 99, 235, .22);
            --slate-100: #f0f4ff;
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
            --warning: #f59e0b;
            --shadow-sm: 0 1px 4px rgba(13, 27, 62, .08), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow-md: 0 4px 18px rgba(13, 27, 62, .10), 0 2px 6px rgba(0, 0, 0, .05);
            --shadow-lg: 0 12px 40px rgba(13, 27, 62, .13), 0 4px 12px rgba(0, 0, 0, .06);
            --shadow-card: 0 0 0 1px var(--border), var(--shadow-lg);
            --radius-sm: 10px;
            --radius: 16px;
            --radius-lg: 22px;
            --radius-xl: 28px;
            --font: 'Plus Jakarta Sans', system-ui, sans-serif;
            --mono: 'JetBrains Mono', monospace;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font);
            background: var(--slate-100);
            color: var(--slate-800);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13, 27, 62, .97);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            height: 60px;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar__brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.02em;
        }

        .topbar__brand i {
            font-size: 22px;
            color: var(--cyan);
        }

        .topbar__tag {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .35);
        }

        .topbar__badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--cyan);
            background: rgba(6, 182, 212, .12);
            border: 1px solid rgba(6, 182, 212, .3);
            padding: 5px 14px;
            border-radius: 100px;
        }

        .topbar__badge i {
            font-size: 13px;
        }

        /* ── LAYOUT ── */
        .layout {
            max-width: 1160px;
            margin: 0 auto;
            padding: 36px 24px 80px;
            display: grid;
            grid-template-columns: 270px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: sticky;
            top: 80px;
        }

        .sidebar__hero {
            background: linear-gradient(145deg, var(--navy) 0%, var(--navy-2) 40%, #0e2d6a 100%);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            color: white;
            margin-bottom: 14px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(13, 27, 62, .35);
        }

        .sidebar__hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, .18) 0%, transparent 70%);
        }

        .sidebar__hero::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(29, 78, 216, .22) 0%, transparent 70%);
        }

        .hero__icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(6, 182, 212, .3), rgba(29, 78, 216, .3));
            border: 1px solid rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .hero__title {
            font-size: 19px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .hero__sub {
            font-size: 12.5px;
            opacity: .65;
            line-height: 1.55;
            position: relative;
            z-index: 1;
        }

        .hero__price {
            background: rgba(255, 255, 255, .09);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 12px;
            padding: 14px 16px;
            margin-top: 18px;
            position: relative;
            z-index: 1;
        }

        .hero__price-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .10em;
            text-transform: uppercase;
            opacity: .6;
            margin-bottom: 4px;
        }

        .hero__price-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .hero__price-amount {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
        }

        .hero__price-freq {
            font-size: 12px;
            opacity: .65;
        }

        .hero__secure {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            font-size: 11.5px;
            opacity: .55;
        }

        .hero__secure i {
            font-size: 13px;
            color: var(--cyan);
            opacity: 1;
        }

        /* ── STEP LIST ── */
        .step-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
        }

        .step-box__label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--slate-400);
            margin-bottom: 16px;
        }

        .step-list {
            list-style: none;
            position: relative;
        }

        .step-list::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: var(--border);
            border-radius: 2px;
        }

        .step-list__fill {
            position: absolute;
            left: 15px;
            top: 8px;
            width: 2px;
            background: linear-gradient(to bottom, var(--blue-mid), var(--cyan));
            border-radius: 2px;
            transition: height .5s cubic-bezier(.65, 0, .35, 1);
            height: 0;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            cursor: default;
            position: relative;
            z-index: 1;
        }

        .step-item--back {
            cursor: pointer;
        }

        .step-num {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            background: var(--slate-100);
            border: 2px solid var(--border);
            color: var(--slate-400);
            transition: all .3s cubic-bezier(.34, 1.56, .64, 1);
        }

        .step-item.is-active .step-num {
            background: var(--blue-mid);
            border-color: var(--blue-mid);
            color: #fff;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, .15);
            transform: scale(1.08);
        }

        .step-item.is-done .step-num {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }

        .step-item.is-done .step-num::before {
            content: '';
        }

        .step-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-400);
            line-height: 1.2;
        }

        .step-desc {
            font-size: 11px;
            color: var(--slate-300);
            margin-top: 1px;
        }

        .step-item.is-active .step-label {
            color: var(--blue-mid);
        }

        .step-item.is-done .step-label {
            color: var(--slate-600);
        }

        /* ── MAIN CARD ── */
        .main-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        /* Progress bar */
        .progress-wrap {
            height: 4px;
            background: var(--slate-100);
        }

        .progress-fill {
            height: 100%;
            width: 16.66%;
            background: linear-gradient(90deg, var(--blue-mid), var(--cyan));
            border-radius: 0 4px 4px 0;
            transition: width .55s cubic-bezier(.65, 0, .35, 1);
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            right: -4px;
            top: -3px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 10px var(--cyan);
        }

        /* Card header */
        .card-header {
            padding: 30px 38px 24px;
            border-bottom: 1px solid var(--slate-100);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .card-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--slate-100);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 4px 12px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--blue-mid);
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 21px;
            font-weight: 800;
            color: var(--slate-800);
            letter-spacing: -.02em;
            margin-bottom: 5px;
        }

        .card-desc {
            font-size: 13.5px;
            color: var(--slate-500);
            line-height: 1.55;
        }

        .card-counter {
            font-family: var(--mono);
            font-size: 12.5px;
            font-weight: 500;
            color: var(--slate-400);
            white-space: nowrap;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            flex-shrink: 0;
        }

        /* Card body */
        .card-body {
            padding: 32px 38px;
        }

        /* Card footer */
        .card-footer {
            padding: 20px 38px 26px;
            border-top: 1px solid var(--slate-100);
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        /* ── PANELS ── */
        .pp-panel {
            display: none;
        }

        .pp-panel.is-active {
            display: block;
            animation: fadeSlide .38s cubic-bezier(.25, .46, .45, .94) both;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ── FIELDS ── */
        .fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field--full {
            grid-column: 1/-1;
        }

        .label {
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .label__req {
            color: var(--blue-mid);
        }

        .label__opt {
            font-weight: 500;
            font-size: 10px;
            text-transform: none;
            letter-spacing: 0;
            color: var(--slate-300);
            margin-left: auto;
        }

        /* Input wrap */
        .input-wrap {
            position: relative;
        }

        .input-wrap .icon-l {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-300);
            font-size: 17px;
            pointer-events: none;
            transition: color .2s;
        }

        .input-wrap:focus-within .icon-l {
            color: var(--blue-mid);
        }

        .input,
        .sel,
        .ta {
            width: 100%;
            padding: 12px 14px;
            font-family: var(--font);
            font-size: 14px;
            color: var(--slate-800);
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            outline: none;
            transition: border-color .18s, box-shadow .18s, background .18s;
            -webkit-appearance: none;
        }

        .input-wrap .input,
        .input-wrap .sel {
            padding-left: 42px;
        }

        .input::placeholder {
            color: var(--slate-300);
        }

        .input:hover,
        .sel:hover {
            border-color: rgba(37, 99, 235, .28);
        }

        .input:focus,
        .sel:focus,
        .ta:focus {
            border-color: var(--blue-mid);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
            background: #fff;
        }

        .input[readonly] {
            background: var(--slate-100);
            color: var(--slate-500);
            cursor: default;
        }

        .sel {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            padding-right: 36px;
        }

        /* ── DIVIDER ── */
        .divider {
            grid-column: 1/-1;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0;
        }

        .divider__line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider__lbl {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--slate-300);
        }

        /* ── IDENTITY PILLS ── */
        .id-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .id-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(37, 99, 235, .07);
            border: 1px solid rgba(37, 99, 235, .15);
            border-radius: 100px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--blue-mid);
        }

        .id-pill i {
            font-size: 13px;
        }

        /* ── MINI CARD (acompañantes) ── */
        .mini-card {
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .mini-card__head {
            padding: 10px 16px;
            background: linear-gradient(135deg, var(--navy), var(--navy-2));
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mini-card__head i {
            font-size: 15px;
            color: var(--cyan);
        }

        .mini-card__body {
            padding: 16px;
        }

        /* ── FILE ZONE ── */
        .file-zone {
            position: relative;
            border: 2px dashed rgba(37, 99, 235, .25);
            border-radius: var(--radius-sm);
            background: rgba(37, 99, 235, .03);
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
        }

        .file-zone:hover,
        .file-zone.dragover {
            border-color: var(--blue-mid);
            background: rgba(37, 99, 235, .07);
        }

        .file-zone.filled {
            border-style: solid;
            border-color: var(--success);
            background: var(--success-bg);
        }

        .file-zone input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-zone__icon {
            font-size: 26px;
            color: var(--blue-light);
            margin-bottom: 6px;
        }

        .file-zone.filled .file-zone__icon {
            color: var(--success);
        }

        .file-zone__title {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-600);
        }

        .file-zone__sub {
            font-size: 11px;
            color: var(--slate-400);
            margin-top: 3px;
        }

        .file-zone__name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--success);
            margin-top: 6px;
            display: none;
        }

        .file-zone.filled .file-zone__name {
            display: block;
        }

        .file-zone.filled .file-zone__title,
        .file-zone.filled .file-zone__sub {
            display: none;
        }

        .docs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* ── CAMERA ── */
        .cam-stage {
            position: relative;
            width: 100%;
            max-width: 420px;
            aspect-ratio: 4/3;
            background: var(--slate-800);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
        }

        .cam-stage video,
        .cam-stage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cam-guide {
            position: absolute;
            width: 50%;
            aspect-ratio: 3/4;
            border: 2px dashed rgba(6, 182, 212, .7);
            border-radius: 8px;
            pointer-events: none;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, .25);
        }

        .cam-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: var(--slate-400);
            padding: 40px;
            text-align: center;
        }

        .cam-placeholder i {
            font-size: 44px;
            color: var(--slate-500);
        }

        .cam-placeholder span {
            font-size: 13px;
        }

        /* ── SIGNATURE ── */
        .sig-wrap {
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .sig-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 8px;
        }

        .sig-top__lbl strong {
            display: block;
            font-size: 13.5px;
            color: var(--navy);
        }

        .sig-top__lbl small {
            font-size: 11.5px;
            color: var(--slate-400);
        }

        .sig-canvas {
            width: 100%;
            height: 150px;
            background: #fafcff;
            cursor: crosshair;
            touch-action: none;
            display: block;
        }

        /* ── CONTRACT ── */
        .contract-box {
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .contract-banner {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, .05);
            border-bottom: 1px solid var(--border);
            padding: 10px 14px;
            font-size: 12.5px;
            color: var(--slate-600);
        }

        .contract-banner i {
            color: var(--blue-mid);
            font-size: 15px;
            flex-shrink: 0;
        }

        .contract-view {
            max-height: 280px;
            overflow-y: auto;
            padding: 16px;
            font-size: 13px;
            line-height: 1.65;
            color: var(--slate-700);
            background: #fff;
        }

        .contract-view h3 {
            font-size: 14px;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .contract-view h4 {
            font-size: 13px;
            color: var(--navy);
            margin: 8px 0 4px;
        }

        .contract-view p {
            margin-bottom: 6px;
        }

        .contract-empty {
            padding: 20px;
            text-align: center;
            color: var(--slate-400);
            font-size: 13px;
        }

        /* ── TERMS ── */
        .terms-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(37, 99, 235, .04);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: border-color .2s;
        }

        .terms-box:has(input:checked) {
            border-color: var(--blue-mid);
        }

        .terms-box input[type=checkbox] {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
            accent-color: var(--blue-mid);
        }

        .terms-box__text {
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.55;
        }

        /* ── SUMMARY ── */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .summary-item {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
        }

        .summary-item__lbl {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--slate-400);
            margin-bottom: 4px;
        }

        .summary-item__val {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-700);
        }

        /* ── PAYMENT SELECT ── */
        .pay-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .pay-radio {
            display: none;
        }

        .pay-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: var(--surface-2);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all .2s;
        }

        .pay-card:hover {
            border-color: rgba(37, 99, 235, .3);
        }

        .pay-radio:checked+.pay-card {
            border-color: var(--blue-mid);
            background: rgba(37, 99, 235, .05);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
        }

        .pay-dot {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            border-radius: 50%;
            border: 2px solid var(--slate-300);
            margin-top: 2px;
            position: relative;
            transition: all .2s;
        }

        .pay-radio:checked+.pay-card .pay-dot {
            border-color: var(--blue-mid);
            background: var(--blue-mid);
        }

        .pay-radio:checked+.pay-card .pay-dot::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: #fff;
        }

        .pay-card__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 2px;
        }

        .pay-card__sub {
            font-size: 12px;
            color: var(--slate-400);
            line-height: 1.4;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13.5px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all .18s;
            white-space: nowrap;
        }

        .btn i {
            font-size: 17px;
        }

        .btn:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn--ghost {
            background: #fff;
            color: var(--slate-500);
            border: 1.5px solid var(--border);
        }

        .btn--ghost:hover {
            border-color: rgba(37, 99, 235, .3);
            color: var(--slate-700);
        }

        .btn--primary {
            background: var(--blue-mid);
            color: #fff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .28);
        }

        .btn--primary:hover:not(:disabled) {
            background: var(--blue);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, .35);
        }

        .btn--success {
            background: var(--success);
            color: #fff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, .28);
        }

        .btn--success:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, .35);
        }

        .btn--soft {
            background: linear-gradient(135deg, var(--blue-mid), var(--blue-light));
            color: #fff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .25);
        }

        .btn--soft:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, .35);
        }

        /* ── TOAST ── */
        #ppToastHost {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
            pointer-events: none;
        }

        /* ── MODAL ── */
        .pp-modal {
            position: fixed;
            inset: 0;
            background: rgba(13, 27, 62, .55);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .pp-modal.pp-hidden {
            display: none !important;
        }

        .pp-modal__box {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 28px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 24px 64px rgba(13, 27, 62, .22);
        }

        .pp-modal__head {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue-mid);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pp-modal__head i {
            font-size: 16px;
        }

        .pp-modal__body {
            font-size: 14.5px;
            color: var(--slate-700);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .pp-modal__actions {
            display: flex;
            justify-content: flex-end;
        }

        /* ── UTILITIES ── */
        .pp-hidden {
            display: none !important;
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .info-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            background: rgba(37, 99, 235, .05);
            border-left: 3px solid var(--blue-mid);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            font-size: 13px;
            color: var(--slate-600);
            line-height: 1.55;
        }

        .info-note i {
            font-size: 16px;
            color: var(--blue-mid);
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width:960px) {
            .layout {
                grid-template-columns: 1fr;
                padding: 16px 16px 60px;
                gap: 16px;
            }

            .sidebar {
                position: static;
            }

            .sidebar__hero {
                display: none;
            }

            .step-box {
                display: flex;
                align-items: center;
                gap: 8px;
                overflow-x: auto;
                padding: 12px 14px;
            }

            .step-box__label {
                display: none;
            }

            .step-list {
                display: flex;
                gap: 4px;
            }

            .step-list::before,
            .step-list__fill {
                display: none;
            }

            .step-item {
                gap: 6px;
            }

            .step-desc {
                display: none;
            }

            .step-label {
                font-size: 11px;
                white-space: nowrap;
            }

            .card-header {
                padding: 20px 22px 16px;
                flex-direction: column;
            }

            .card-body {
                padding: 22px 22px;
            }

            .card-footer {
                padding: 14px 22px 20px;
            }

            .fields {
                grid-template-columns: 1fr;
            }

            .docs-grid {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .pay-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:480px) {
            .topbar {
                padding: 0 16px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="topbar__brand">
            <i class="mdi mdi-hospital-box"></i>
            PATS
            <span class="topbar__tag">· Pasaporte de Atención</span>
        </div>
        <div class="topbar__badge">
            <i class="mdi mdi-shield-check"></i> Proceso seguro
        </div>
    </header>

    <div class="layout">

        {{-- ════ SIDEBAR ════ --}}
        <aside class="sidebar">

            <div class="sidebar__hero">
                <div class="hero__icon"><i class="mdi mdi-card-account-details-outline"></i></div>
                <div class="hero__title">Activa tu<br>Pasaporte PATS</div>
                <div class="hero__sub">Acceso completo a la red médica más grande de México.</div>
                <div class="hero__price">
                    <div class="hero__price-label">Pasaporte mensual</div>
                    <div class="hero__price-row">
                        <div class="hero__price-amount">${{ number_format($montoMensual ?? 0, 0, '.', ',') }}</div>
                        <div class="hero__price-freq">MXN / mes</div>
                    </div>
                </div>
                <div class="hero__secure">
                    <i class="mdi mdi-lock"></i>
                    Pago 100% cifrado · PCI DSS
                </div>
            </div>

            <div class="step-box">
                <div class="step-box__label">Tu progreso</div>
                @php
                    $stepsInfo = [
                        ['label' => 'Acceso', 'desc' => 'Correo y teléfono', 'icon' => 'mdi-email-check-outline'],
                        ['label' => 'Personales', 'desc' => 'Nombre y CURP', 'icon' => 'mdi-account-outline'],
                        ['label' => 'Domicilio', 'desc' => 'Tu dirección', 'icon' => 'mdi-map-marker-outline'],
                        ['label' => 'Documentos', 'desc' => 'INE y CURP', 'icon' => 'mdi-folder-open-outline'],
                        ['label' => 'Fotografía', 'desc' => 'Tu selfie de acceso', 'icon' => 'mdi-camera-account'],
                        ['label' => 'Contrato', 'desc' => 'Firma y pago', 'icon' => 'mdi-file-sign'],
                    ];
                @endphp
                <ul class="step-list" id="ppStepList">
                    <div class="step-list__fill" id="ppStepFill"></div>
                    @foreach ($stepsInfo as $i => $s)
                        <li class="step-item {{ $i === 0 ? 'is-active' : '' }} {{ $i < 0 ? 'step-item--back' : '' }}"
                            data-step="{{ $i + 1 }}">
                            <div class="step-num">
                                @if ($i === 0)
                                    <i class="mdi mdi-check" style="font-size:14px;display:none;" class="done-icon"></i>
                                    <span>{{ $i + 1 }}</span>
                                @else
                                    <span>{{ $i + 1 }}</span>
                                @endif
                            </div>
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
            <form id="frmPagoPatsPublico" novalidate enctype="multipart/form-data">
                @csrf

                <div class="main-card">

                    {{-- Progress --}}
                    <div class="progress-wrap">
                        <div class="progress-fill" id="ppProgressFill" style="width:16.66%"></div>
                    </div>

                    {{-- Header --}}
                    <div class="card-header">
                        <div>
                            <div class="card-tag" id="ppCardTag">
                                <i class="mdi mdi-email-check-outline"></i> Paso 1 de 6
                            </div>
                            <h1 class="card-title" id="ppCardTitle">Datos de acceso</h1>
                            <p class="card-desc" id="ppCardDesc">Ingresa tu correo y teléfono para comenzar el proceso.
                            </p>
                        </div>
                        <div class="card-counter" id="ppCardCounter">1 / 6</div>
                    </div>

                    {{-- Body --}}
                    <div class="card-body">

                        {{-- ── PASO 1: ACCESO ── --}}
                        <section class="pp-panel is-active" data-step-panel="1">
                            <div class="fields">
                                <div class="field">
                                    <label class="label" for="login_correo">Correo electrónico <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-email-outline icon-l"></i>
                                        <input class="input" type="email" id="login_correo"
                                            placeholder="ejemplo@correo.com" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="login_telefono">Teléfono celular <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-phone-outline icon-l"></i>
                                        <input class="input" type="text" id="login_telefono" maxlength="10"
                                            inputmode="numeric" placeholder="10 dígitos" required>
                                    </div>
                                </div>
                                <div class="field--full">
                                    <div class="info-note">
                                        <i class="mdi mdi-information-outline"></i>
                                        <span>Estos datos son tu identificación en la plataforma PATS. Asegúrate de
                                            capturarlos correctamente.</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- ── PASO 2: PERSONALES ── --}}
                        <section class="pp-panel" data-step-panel="2">

                            <div class="id-pills">
                                <div class="id-pill"><i class="mdi mdi-email-outline"></i><span
                                        id="pillCorreo">Correo: —</span></div>
                                <div class="id-pill"><i class="mdi mdi-phone-outline"></i><span
                                        id="pillTelefono">Teléfono: —</span></div>
                            </div>

                            <div class="fields">
                                <div class="field field--full">
                                    <label class="label" for="nombre_usuario">Nombre(s) <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-outline icon-l"></i>
                                        <input class="input" type="text" id="nombre_usuario"
                                            name="nombre_usuario" placeholder="Nombre(s) de pila" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="apellido_pa">Apellido paterno <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-outline icon-l"></i>
                                        <input class="input" type="text" id="apellido_pa" name="apellido_pa"
                                            placeholder="Primer apellido" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="apellido_ma">Apellido materno <span
                                            class="label__opt">Opcional</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-outline icon-l"></i>
                                        <input class="input" type="text" id="apellido_ma" name="apellido_ma"
                                            placeholder="Segundo apellido">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="curp_usuario">CURP <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-card-account-details-outline icon-l"></i>
                                        <input class="input" type="text" id="curp_usuario" name="curp_usuario"
                                            maxlength="18" placeholder="18 caracteres" required
                                            style="font-family:var(--mono);text-transform:uppercase;">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="fecha_nacimiento">Fecha de nacimiento <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-cake-variant-outline icon-l"></i>
                                        <input class="input" type="date" id="fecha_nacimiento"
                                            name="fecha_nacimiento" required style="font-family:var(--mono);">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="tipo_cliente">Tipo de cliente <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-group-outline icon-l"></i>
                                        <select class="sel" id="tipo_cliente" name="tipo_cliente" required>
                                            <option value="privado">Privado</option>
                                            <option value="empresa">Empresa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field field--full pp-hidden" id="wrapNombreEmpresa">
                                    <label class="label" for="nombre_empresa">Nombre de empresa</label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-office-building-outline icon-l"></i>
                                        <input class="input" type="text" id="nombre_empresa"
                                            name="nombre_empresa" placeholder="Razón social">
                                    </div>
                                </div>
                            </div>

                            {{-- Acompañantes (mayores 65) --}}
                            <div class="pp-hidden" id="adultosMayoresWrap" style="margin-top:20px;">
                                <div class="divider" style="margin-bottom:16px;">
                                    <div class="divider__line"></div>
                                    <span class="divider__lbl">Acompañantes obligatorios</span>
                                    <div class="divider__line"></div>
                                </div>
                                <div class="info-note" style="margin-bottom:16px;">
                                    <i class="mdi mdi-account-multiple-plus-outline"></i>
                                    <span>Por tu edad, debes registrar 2 usuarios adicionales acompañantes.</span>
                                </div>
                                <div class="stack">
                                    @foreach ([1, 2] as $ac)
                                        <div class="mini-card">
                                            <div class="mini-card__head">
                                                <i class="mdi mdi-account-heart-outline"></i> Acompañante
                                                {{ $ac }}
                                            </div>
                                            <div class="mini-card__body">
                                                <div class="fields">
                                                    <div class="field field--full">
                                                        <label class="label">Nombre(s)</label>
                                                        <div class="input-wrap"><i
                                                                class="mdi mdi-account-outline icon-l"></i>
                                                            <input class="input" type="text"
                                                                name="ac{{ $ac }}_nombre"
                                                                placeholder="Nombre(s)">
                                                        </div>
                                                    </div>
                                                    <div class="field">
                                                        <label class="label">Apellido paterno</label>
                                                        <div class="input-wrap"><i
                                                                class="mdi mdi-account-outline icon-l"></i>
                                                            <input class="input" type="text"
                                                                name="ac{{ $ac }}_apellido_pa">
                                                        </div>
                                                    </div>
                                                    <div class="field">
                                                        <label class="label">Apellido materno</label>
                                                        <div class="input-wrap"><i
                                                                class="mdi mdi-account-outline icon-l"></i>
                                                            <input class="input" type="text"
                                                                name="ac{{ $ac }}_apellido_ma">
                                                        </div>
                                                    </div>
                                                    <div class="field">
                                                        <label class="label">CURP</label>
                                                        <div class="input-wrap"><i
                                                                class="mdi mdi-card-account-details-outline icon-l"></i>
                                                            <input class="input" type="text"
                                                                name="ac{{ $ac }}_curp" maxlength="18"
                                                                style="font-family:var(--mono);text-transform:uppercase;">
                                                        </div>
                                                    </div>
                                                    <div class="field">
                                                        <label class="label">Fecha de nacimiento</label>
                                                        <div class="input-wrap"><i
                                                                class="mdi mdi-calendar-outline icon-l"></i>
                                                            <input class="input" type="date"
                                                                name="ac{{ $ac }}_fecha_nacimiento"
                                                                style="font-family:var(--mono);">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>

                        {{-- ── PASO 3: DOMICILIO ── --}}
                        <section class="pp-panel" data-step-panel="3">
                            <div class="fields">
                                <div class="field field--full">
                                    <label class="label" for="dom_calle">Calle <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-road icon-l"></i>
                                        <input class="input" type="text" id="dom_calle" name="dom_calle"
                                            placeholder="Nombre de la calle" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_num_ext">Número exterior <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-numeric icon-l"></i>
                                        <input class="input" type="text" id="dom_num_ext" name="dom_num_ext"
                                            placeholder="Ej. 42" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_num_int">Número interior <span
                                            class="label__opt">Opcional</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-home-outline icon-l"></i>
                                        <input class="input" type="text" id="dom_num_int" name="dom_num_int"
                                            placeholder="Ej. 3-B">
                                    </div>
                                </div>
                                <div class="field field--full">
                                    <label class="label" for="dom_colonia">Colonia <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-map-marker-outline icon-l"></i>
                                        <input class="input" type="text" id="dom_colonia" name="dom_colonia"
                                            placeholder="Nombre de la colonia" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_cp">Código postal <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-mailbox-outline icon-l"></i>
                                        <input class="input" type="text" id="dom_cp" name="dom_cp"
                                            maxlength="5" inputmode="numeric" placeholder="5 dígitos" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_municipio">Ciudad / Municipio <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-city-outline icon-l"></i>
                                        <input class="input" type="text" id="dom_municipio" name="dom_municipio"
                                            placeholder="Municipio" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_estado_acronimo">Estado <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-map icon-l"></i>
                                        <select class="sel" id="dom_estado_acronimo" name="dom_estado_acronimo"
                                            required>
                                            @foreach ($estados as $acr => $nombre)
                                                <option value="{{ $acr }}"
                                                    {{ $estadoAcronimo === $acr ? 'selected' : '' }}>{{ $nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="dom_estado">Estado (nombre) <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-flag-outline icon-l"></i>
                                        <input class="input" type="text" id="dom_estado" name="dom_estado"
                                            value="{{ $estadoNombre }}" required>
                                    </div>
                                </div>
                                <div class="field field--full">
                                    <label class="label" for="dom_pais">País <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap"><i class="mdi mdi-earth icon-l"></i>
                                        <input class="input" type="text" id="dom_pais" name="dom_pais"
                                            value="{{ $pais }}" required>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- ── PASO 4: DOCUMENTOS ── --}}
                        <section class="pp-panel" data-step-panel="4">
                            <div class="info-note" style="margin-bottom:20px;">
                                <i class="mdi mdi-shield-lock-outline"></i>
                                <span>Todos los archivos se transmiten de forma cifrada (SSL). Formatos: PDF, JPG, PNG,
                                    WEBP.</span>
                            </div>
                            <div class="docs-grid">
                                @foreach ([['id' => 'doc_ine_frente', 'icon' => 'mdi-card-account-details', 'label' => 'INE frente', 'req' => true], ['id' => 'doc_ine_reverso', 'icon' => 'mdi-card-account-details-outline', 'label' => 'INE reverso', 'req' => true], ['id' => 'doc_curp', 'icon' => 'mdi-file-certificate-outline', 'label' => 'CURP documento', 'req' => true]] as $doc)
                                    <div>
                                        <div class="label" style="margin-bottom:7px;">
                                            <i class="mdi {{ $doc['icon'] }}"></i>
                                            {{ $doc['label'] }}
                                            @if ($doc['req'])
                                                <span class="label__req">*</span>
                                            @endif
                                        </div>
                                        <div class="file-zone" id="zone_{{ $doc['id'] }}">
                                            <input type="file" id="{{ $doc['id'] }}"
                                                name="{{ $doc['id'] }}" accept=".pdf,.png,.jpg,.jpeg,.webp"
                                                {{ $doc['req'] ? 'required' : '' }}>
                                            <div class="file-zone__icon"><i class="mdi mdi-upload"></i></div>
                                            <div class="file-zone__title">Clic para subir</div>
                                            <div class="file-zone__sub">PDF · JPG · PNG</div>
                                            <div class="file-zone__name" id="name_{{ $doc['id'] }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- ── PASO 5: FOTOGRAFÍA ── --}}
                        <section class="pp-panel" data-step-panel="5">
                            <div class="info-note" style="margin-bottom:18px;">
                                <i class="mdi mdi-camera-account"></i>
                                <span>Toma tu fotografía con la cámara o súbela desde tu dispositivo. Debe ser reciente
                                    y con buena iluminación.</span>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:14px;align-items:center;">
                                <div class="cam-stage">
                                    <div class="cam-placeholder" id="camPlaceholder">
                                        <i class="mdi mdi-camera-off-outline"></i>
                                        <span>Activa la cámara o sube una foto</span>
                                    </div>
                                    <video id="camVideo" autoplay playsinline muted class="pp-hidden"></video>
                                    <canvas id="camCanvas" class="pp-hidden"></canvas>
                                    <img id="camPreview" alt="Vista previa" class="pp-hidden">
                                    <div class="cam-guide"></div>
                                </div>
                                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;">
                                    <button type="button" class="btn btn--ghost" id="btnIniciarCamara">
                                        <i class="mdi mdi-camera"></i> Activar cámara
                                    </button>
                                    <button type="button" class="btn btn--primary pp-hidden" id="btnCapturarFoto">
                                        <i class="mdi mdi-camera-iris"></i> Capturar
                                    </button>
                                    <label class="btn btn--ghost" style="cursor:pointer;">
                                        <i class="mdi mdi-upload"></i> Subir foto
                                        <input type="file" id="foto_manual" accept="image/*"
                                            capture="environment" class="pp-hidden">
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" id="foto_base64" name="foto_base64">
                        </section>

                        {{-- ── PASO 6: CONTRATO Y PAGO ── --}}
                        <section class="pp-panel" data-step-panel="6">
                            <div style="display:flex;flex-direction:column;gap:20px;">

                                {{-- Contrato --}}
                                <div>
                                    <div class="label" style="margin-bottom:10px;"><i
                                            class="mdi mdi-file-document-outline"></i> Contrato de pasporte</div>
                                    <div class="contract-box">
                                        <div class="contract-banner">
                                            <i class="mdi mdi-information-outline"></i>
                                            Lee el contrato completo antes de firmar.
                                        </div>
                                        <div id="contractPreview" class="contract-view">
                                            <div class="contract-empty">El contrato se cargará con tus datos al llegar
                                                a este paso.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Firma --}}
                                <div>
                                    <div class="label" style="margin-bottom:10px;"><i class="mdi mdi-draw-pen"></i>
                                        Firma digital <span class="label__req">*</span></div>
                                    <div class="sig-wrap">
                                        <div class="sig-top">
                                            <div class="sig-top__lbl">
                                                <strong>Firma del afiliado</strong>
                                                <small>Firma con el mouse o con tu dedo en pantalla.</small>
                                            </div>
                                            <button type="button" class="btn btn--ghost" id="btnLimpiarFirma"
                                                style="padding:7px 14px;font-size:12px;">
                                                <i class="mdi mdi-eraser"></i> Limpiar
                                            </button>
                                        </div>
                                        <canvas id="signaturePad" class="sig-canvas"></canvas>
                                    </div>
                                </div>

                                {{-- Frecuencia y monto --}}
                                <div class="fields">
                                    <div class="divider">
                                        <div class="divider__line"></div>
                                        <span class="divider__lbl">Plan de pasaporte</span>
                                        <div class="divider__line"></div>
                                    </div>
                                    <div class="field">
                                        <label class="label" for="frecuencia_pago_publica">Frecuencia de pago</label>
                                        <div class="input-wrap"><i class="mdi mdi-calendar-sync-outline icon-l"></i>
                                            <select class="sel" id="frecuencia_pago_publica">
                                                <option value="MENSUAL" selected>Mensual</option>
                                                <option value="ANUAL">Anual</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label" for="monto_visual_publico">Monto a pagar</label>
                                        <div class="input-wrap"><i class="mdi mdi-currency-mxn icon-l"></i>
                                            <input class="input" type="text" id="monto_visual_publico" readonly
                                                style="font-family:var(--mono);font-size:15px;font-weight:700;color:var(--blue-mid);background:rgba(37,99,235,.05);border-color:var(--border);">
                                        </div>
                                    </div>
                                </div>

                                {{-- Resumen --}}
                                <div>
                                    <div class="label" style="margin-bottom:10px;"><i
                                            class="mdi mdi-clipboard-check-outline"></i> Resumen final</div>
                                    <div class="summary-grid">
                                        <div class="summary-item">
                                            <div class="summary-item__lbl">Correo</div>
                                            <div class="summary-item__val" id="sumCorreo">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__lbl">Teléfono</div>
                                            <div class="summary-item__val" id="sumTelefono">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__lbl">Frecuencia</div>
                                            <div class="summary-item__val" id="sumFrecuencia">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__lbl">Monto</div>
                                            <div class="summary-item__val" id="sumMonto"
                                                style="color:var(--blue-mid);">$0</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Términos --}}
                                <label class="terms-box">
                                    <input type="checkbox" id="acepta_contrato" name="acepta_contrato">
                                    <span class="terms-box__text">
                                        He leído, entendido y acepto el contrato de pasaporte PATS. Reconozco que mi
                                        firma digital
                                        forma parte de la evidencia contractual del proceso.
                                    </span>
                                </label>

                            </div>
                        </section>

                    </div>{{-- /card-body --}}

                    {{-- Footer --}}
                    <div class="card-footer">
                        <button type="button" class="btn btn--ghost" id="btnPrev">
                            <i class="mdi mdi-arrow-left"></i> Anterior
                        </button>
                        <div style="display:flex;gap:10px;">
                            <button type="button" class="btn btn--soft" id="btnNext">
                                Siguiente <i class="mdi mdi-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn--success pp-hidden" id="btnSubmitPago">
                                <i class="mdi mdi-lock-check-outline"></i> Continuar a pago seguro
                            </button>
                        </div>
                    </div>

                </div>{{-- /main-card --}}

                {{-- Campos ocultos --}}
                <input type="hidden" name="token_publico" value="{{ $token }}">
                <input type="hidden" name="id_distribuidor" value="{{ (int) ($ctx['id_distribuidor'] ?? 0) }}">
                <input type="hidden" name="id_franquicia" value="{{ (int) ($ctx['id_franquicia'] ?? 0) }}">
                <input type="hidden" name="pais" value="{{ $pais }}">
                <input type="hidden" name="region" value="{{ $ctx['region'] ?? '' }}">
                <input type="hidden" name="zona" value="{{ $ctx['zona'] ?? '' }}">
                <input type="hidden" name="unidad" value="{{ $ctx['unidad'] ?? '' }}">
                <input type="hidden" name="tipo_origen" value="DISTRIBUIDOR">
                <input type="hidden" name="origen_checkout" value="PORTAL_PUBLICO">
                <input type="hidden" name="tipo_operacion" value="ALTA_PATS">
                <input type="hidden" name="moneda" value="MXN">
                <input type="hidden" name="id_tipo_precio" id="id_tipo_precio" value="2">
                <input type="hidden" name="frecuencia" id="frecuencia" value="MENSUAL">
                <input type="hidden" name="monto_orden" id="monto_orden" value="{{ $montoMensual }}">
                <input type="hidden" name="correo_usuario_pats" id="hidden_correo" value="">
                <input type="hidden" name="telefono_usuario" id="hidden_telefono" value="">
                <input type="hidden" name="firma_base64" id="firma_base64" value="">

            </form>
        </main>
    </div>{{-- /layout --}}

    <div id="ppToastHost"></div>

    <div id="ppModal" class="pp-modal pp-hidden">
        <div class="pp-modal__box">
            <div class="pp-modal__head"><i class="mdi mdi-hospital-box"></i> PATS</div>
            <div id="ppModalMsg" class="pp-modal__body">Mensaje</div>
            <div class="pp-modal__actions">
                <button type="button" class="btn btn--primary" id="btnCloseModal">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        window.PATS_PUBLIC_CFG = {
            token: @json($token),
            monto_anual: @json($montoAnual),
            monto_mensual: @json($montoMensual),
            url_orden: @json(route('pats.registro.orden')),
            url_contrato: @json(route('pats.registro.contrato')),
            csrf: @json(csrf_token()),
        };
    </script>

    <script>
        (() => {
            "use strict";

            const $ = (s) => document.querySelector(s);
            const $$ = (s) => Array.from(document.querySelectorAll(s));
            const CFG = window.PATS_PUBLIC_CFG;

            const STEP_META = [{
                    tag: 'Paso 1 de 6',
                    icon: 'mdi-email-check-outline',
                    title: 'Datos de acceso',
                    desc: 'Ingresa tu correo y teléfono para comenzar.'
                },
                {
                    tag: 'Paso 2 de 6',
                    icon: 'mdi-account-outline',
                    title: 'Datos personales',
                    desc: 'Tu nombre completo, CURP y fecha de nacimiento.'
                },
                {
                    tag: 'Paso 3 de 6',
                    icon: 'mdi-map-marker-outline',
                    title: 'Domicilio',
                    desc: 'La dirección donde recibirás comunicaciones.'
                },
                {
                    tag: 'Paso 4 de 6',
                    icon: 'mdi-folder-open-outline',
                    title: 'Documentos',
                    desc: 'Sube tu INE y documento CURP.'
                },
                {
                    tag: 'Paso 5 de 6',
                    icon: 'mdi-camera-account',
                    title: 'Fotografía',
                    desc: 'Tu foto de acceso a la plataforma PATS.'
                },
                {
                    tag: 'Paso 6 de 6',
                    icon: 'mdi-file-sign',
                    title: 'Contrato y pago',
                    desc: 'Firma el contrato y continúa al pago seguro.'
                },
            ];

            let currentStep = 1;
            const totalSteps = 6;
            let mediaStream = null;
            let signPad = null,
                signCtx = null,
                signDrawing = false,
                signHasStroke = false;

            const onlyDigits = (v) => String(v || '').replace(/\D+/g, '');
            const validPhone = (v) => onlyDigits(v).length === 10;
            const validEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || '').trim());

            function calcAge(dateStr) {
                if (!dateStr) return 0;
                const birth = new Date(dateStr + 'T00:00:00');
                const today = new Date();
                let age = today.getFullYear() - birth.getFullYear();
                const m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                return age;
            }

            function money(v) {
                return new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN',
                    maximumFractionDigits: 0
                }).format(Number(v || 0));
            }

            function toast(msg, type = 'info') {
                const host = $('#ppToastHost');
                if (!host) return;
                const item = document.createElement('div');
                const colors = {
                    error: 'linear-gradient(135deg,#991b1b,#dc2626)',
                    success: 'linear-gradient(135deg,#065f46,#10b981)',
                    info: 'linear-gradient(135deg,#1e3a8a,#2563eb)',
                };
                Object.assign(item.style, {
                    minWidth: '240px',
                    maxWidth: '340px',
                    padding: '12px 16px',
                    borderRadius: '12px',
                    color: '#fff',
                    fontSize: '13px',
                    lineHeight: '1.4',
                    fontWeight: '600',
                    pointerEvents: 'auto',
                    boxShadow: '0 16px 36px rgba(0,0,0,.22)',
                    background: colors[type] || colors.info,
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px',
                });
                const icons = {
                    error: '❌',
                    success: '✓',
                    info: 'ℹ'
                };
                item.innerHTML = `<span style="font-size:16px">${icons[type]||''}</span><span>${msg}</span>`;
                host.appendChild(item);
                setTimeout(() => {
                    item.style.transition = 'opacity .25s,transform .25s';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-6px)';
                    setTimeout(() => item.remove(), 260);
                }, 2800);
            }

            function showModal(msg) {
                const modal = $('#ppModal'),
                    box = $('#ppModalMsg');
                if (!modal || !box) return;
                box.textContent = msg || 'Mensaje';
                modal.classList.remove('pp-hidden');
            }

            function hideModal() {
                $('#ppModal')?.classList.add('pp-hidden');
            }

            // ── Sync UI ────────────────────────────────────────────────────────────────

            function syncSteps() {
                $$('[data-step-panel]').forEach(p => {
                    p.classList.toggle('is-active', Number(p.dataset.stepPanel) === currentStep);
                });

                const items = $$('[data-step]');
                items.forEach(b => {
                    const s = Number(b.dataset.step);
                    b.classList.toggle('is-active', s === currentStep);
                    b.classList.toggle('is-done', s < currentStep);
                    b.classList.toggle('step-item--back', s < currentStep);
                });

                // Progress fill line in sidebar
                const fill = $('#ppStepFill');
                if (fill) fill.style.height = `${((currentStep-1)/(totalSteps-1))*100}%`;

                // Top progress bar
                const bar = $('#ppProgressFill');
                if (bar) bar.style.width = `${Math.round((currentStep/totalSteps)*100)}%`;

                // Header meta
                const meta = STEP_META[currentStep - 1];
                if (meta) {
                    const tag = $('#ppCardTag');
                    if (tag) tag.innerHTML = `<i class="mdi ${meta.icon}"></i> ${meta.tag}`;
                    const title = $('#ppCardTitle');
                    if (title) title.textContent = meta.title;
                    const desc = $('#ppCardDesc');
                    if (desc) desc.textContent = meta.desc;
                    const ctr = $('#ppCardCounter');
                    if (ctr) ctr.textContent = `${currentStep} / ${totalSteps}`;
                }

                // Buttons
                const prev = $('#btnPrev'),
                    next = $('#btnNext'),
                    submit = $('#btnSubmitPago');
                if (prev) prev.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
                if (next) next.classList.toggle('pp-hidden', currentStep === totalSteps);
                if (submit) submit.classList.toggle('pp-hidden', currentStep !== totalSteps);
            }

            function syncEmpresa() {
                const tipo = ($('#tipo_cliente')?.value || 'privado').toLowerCase();
                $('#wrapNombreEmpresa')?.classList.toggle('pp-hidden', tipo !== 'empresa');
            }

            function syncAdultosMayores() {
                const age = calcAge($('#fecha_nacimiento')?.value || '');
                $('#adultosMayoresWrap')?.classList.toggle('pp-hidden', age < 65);
            }

            const ESTADOS_MX = @json($estados);

            function syncEstadoDesdeAcronimo() {
                const acr = ($('#dom_estado_acronimo')?.value || '').toUpperCase();
                if ($('#dom_estado')) $('#dom_estado').value = ESTADOS_MX[acr] || acr;
            }

            function syncIdentityFromLogin() {
                const correo = ($('#login_correo')?.value || '').trim();
                const tel = onlyDigits($('#login_telefono')?.value || '').slice(0, 10);
                if ($('#hidden_correo')) $('#hidden_correo').value = correo;
                if ($('#hidden_telefono')) $('#hidden_telefono').value = tel;
                if ($('#pillCorreo')) $('#pillCorreo').textContent = correo || '—';
                if ($('#pillTelefono')) $('#pillTelefono').textContent = tel || '—';
                if ($('#sumCorreo')) $('#sumCorreo').textContent = correo || '—';
                if ($('#sumTelefono')) $('#sumTelefono').textContent = tel || '—';
            }

            function syncMonto() {
                const freq = String($('#frecuencia_pago_publica')?.value || 'MENSUAL').toUpperCase();
                const esMens = freq === 'MENSUAL';
                const monto = esMens ? Number(CFG.monto_mensual) : Number(CFG.monto_anual);
                const tipoPx = esMens ? '2' : '1';
                if ($('#frecuencia')) $('#frecuencia').value = freq;
                if ($('#monto_orden')) $('#monto_orden').value = String(monto);
                if ($('#id_tipo_precio')) $('#id_tipo_precio').value = tipoPx;
                if ($('#sumFrecuencia')) $('#sumFrecuencia').textContent = esMens ? 'MENSUAL' : 'ANUAL';
                if ($('#sumMonto')) $('#sumMonto').textContent = money(monto);
                if ($('#monto_visual_publico')) $('#monto_visual_publico').value = money(monto);
            }

            // ── Contract preview ───────────────────────────────────────────────────────

            let contractTimer = null,
                contractLoading = false,
                contractLastKey = '';

            function queueContractPreview() {
                clearTimeout(contractTimer);
                contractTimer = setTimeout(refreshContractPreview, 350);
            }

            async function refreshContractPreview() {
                const wrap = $('#contractPreview');
                if (!wrap) return;
                const key = JSON.stringify({
                    nombre_usuario: $('#nombre_usuario')?.value,
                    apellido_pa: $('#apellido_pa')?.value,
                    apellido_ma: $('#apellido_ma')?.value,
                    curp_usuario: $('#curp_usuario')?.value,
                    correo: $('#hidden_correo')?.value,
                    telefono: $('#hidden_telefono')?.value,
                    frecuencia: $('#frecuencia')?.value,
                    monto_orden: $('#monto_orden')?.value,
                    dom_calle: $('#dom_calle')?.value,
                    dom_num_ext: $('#dom_num_ext')?.value,
                    dom_colonia: $('#dom_colonia')?.value,
                    dom_estado: $('#dom_estado')?.value,
                });
                if (key === contractLastKey || contractLoading) return;
                contractLastKey = key;
                contractLoading = true;
                const fd = new FormData();
                fd.append('nombre_usuario', $('#nombre_usuario')?.value || '');
                fd.append('apellido_pa', $('#apellido_pa')?.value || '');
                fd.append('apellido_ma', $('#apellido_ma')?.value || '');
                fd.append('curp_usuario', $('#curp_usuario')?.value || '');
                fd.append('correo_usuario_pats', $('#hidden_correo')?.value || '');
                fd.append('telefono_usuario', $('#hidden_telefono')?.value || '');
                fd.append('frecuencia', $('#frecuencia')?.value || 'MENSUAL');
                fd.append('monto_orden', $('#monto_orden')?.value || String(CFG.monto_mensual));
                fd.append('dom_calle', $('#dom_calle')?.value || '');
                fd.append('dom_num_ext', $('#dom_num_ext')?.value || '');
                fd.append('dom_num_int', $('#dom_num_int')?.value || '');
                fd.append('dom_colonia', $('#dom_colonia')?.value || '');
                fd.append('dom_cp', $('#dom_cp')?.value || '');
                fd.append('dom_municipio', $('#dom_municipio')?.value || '');
                fd.append('dom_estado', $('#dom_estado')?.value || '');
                fd.append('dom_pais', $('#dom_pais')?.value || '');
                try {
                    const res = await fetch(CFG.url_contrato, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CFG.csrf
                        },
                        body: fd
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || data.ok === false) {
                        wrap.innerHTML =
                            `<div class="contract-empty">${data.error || 'No fue posible renderizar el contrato.'}</div>`;
                        return;
                    }
                    wrap.innerHTML = data.html || '';
                } catch (e) {
                    console.error(e);
                } finally {
                    contractLoading = false;
                }
            }

            // ── Signature pad ──────────────────────────────────────────────────────────

            function setupSignaturePad(forceReset = false) {
                signPad = $('#signaturePad');
                if (!signPad) return;
                const rect = signPad.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                const dpr = Math.max(window.devicePixelRatio || 1, 1);
                const oldData = (!forceReset && signHasStroke) ? signPad.toDataURL('image/png') : null;
                signPad.width = Math.floor(rect.width * dpr);
                signPad.height = Math.floor(rect.height * dpr);
                signCtx = signPad.getContext('2d');
                signCtx.setTransform(1, 0, 0, 1, 0, 0);
                signCtx.scale(dpr, dpr);
                signCtx.lineWidth = 2.4;
                signCtx.lineCap = 'round';
                signCtx.lineJoin = 'round';
                signCtx.strokeStyle = '#1d4ed8';
                if (oldData) {
                    const img = new Image();
                    img.onload = () => signCtx.drawImage(img, 0, 0, rect.width, rect.height);
                    img.src = oldData;
                }
                if (signPad.dataset.boundSign === '1') return;
                signPad.dataset.boundSign = '1';
                const getXY = (ev) => {
                    const r = signPad.getBoundingClientRect();
                    const t = ev.touches?.[0] ?? null;
                    return {
                        x: (t ? t.clientX : ev.clientX) - r.left,
                        y: (t ? t.clientY : ev.clientY) - r.top
                    };
                };
                const start = (ev) => {
                    ev.preventDefault();
                    signDrawing = true;
                    signHasStroke = true;
                    const {
                        x,
                        y
                    } = getXY(ev);
                    signCtx.beginPath();
                    signCtx.moveTo(x, y);
                };
                const move = (ev) => {
                    if (!signDrawing) return;
                    ev.preventDefault();
                    const {
                        x,
                        y
                    } = getXY(ev);
                    signCtx.lineTo(x, y);
                    signCtx.stroke();
                };
                const end = (ev) => {
                    if (!signDrawing) return;
                    ev.preventDefault();
                    signDrawing = false;
                    $('#firma_base64').value = signPad.toDataURL('image/png');
                };
                signPad.addEventListener('mousedown', start);
                signPad.addEventListener('mousemove', move);
                window.addEventListener('mouseup', end);
                signPad.addEventListener('touchstart', start, {
                    passive: false
                });
                signPad.addEventListener('touchmove', move, {
                    passive: false
                });
                signPad.addEventListener('touchend', end, {
                    passive: false
                });
            }

            function clearSignature() {
                if (!signCtx || !signPad) return;
                signCtx.clearRect(0, 0, signPad.width, signPad.height);
                signHasStroke = false;
                $('#firma_base64').value = '';
                setupSignaturePad(true);
            }

            // ── File inputs ────────────────────────────────────────────────────────────

            function bindFileInputs() {
                $$('.file-zone input[type="file"]').forEach(input => {
                    if (input.dataset.boundFile === '1') return;
                    input.dataset.boundFile = '1';
                    input.addEventListener('change', () => {
                        const zone = input.closest('.file-zone');
                        const nameEl = zone?.querySelector('.file-zone__name');
                        const f = input.files?.[0];
                        if (!f || !nameEl) return;
                        nameEl.textContent = `✓ ${f.name}`;
                        zone.classList.add('filled');
                    });
                    const zone = input.closest('.file-zone');
                    zone?.addEventListener('dragover', e => {
                        e.preventDefault();
                        zone.classList.add('dragover');
                    });
                    zone?.addEventListener('dragleave', () => zone.classList.remove('dragover'));
                    zone?.addEventListener('drop', e => {
                        e.preventDefault();
                        zone.classList.remove('dragover');
                        if (e.dataTransfer.files.length) {
                            const dt = new DataTransfer();
                            dt.items.add(e.dataTransfer.files[0]);
                            input.files = dt.files;
                            input.dispatchEvent(new Event('change'));
                        }
                    });
                });
            }

            // ── Camera ─────────────────────────────────────────────────────────────────

            async function startCamera() {
                const video = $('#camVideo'),
                    preview = $('#camPreview'),
                    canvas = $('#camCanvas'),
                    ph = $('#camPlaceholder');
                try {
                    if (mediaStream) mediaStream.getTracks().forEach(t => t.stop());
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                ideal: 'user'
                            },
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        },
                        audio: false
                    });
                    video.srcObject = mediaStream;
                    video.classList.remove('pp-hidden');
                    preview.classList.add('pp-hidden');
                    canvas.classList.add('pp-hidden');
                    if (ph) ph.style.display = 'none';
                    $('#btnIniciarCamara')?.classList.add('pp-hidden');
                    $('#btnCapturarFoto')?.classList.remove('pp-hidden');
                } catch (e) {
                    console.error(e);
                    showModal('No fue posible abrir la cámara. Verifica los permisos del navegador.');
                }
            }

            function capturePhoto() {
                const video = $('#camVideo'),
                    canvas = $('#camCanvas'),
                    preview = $('#camPreview'),
                    hidden = $('#foto_base64');
                if (!video || !canvas || !hidden || video.readyState < 2) {
                    toast('Primero inicia la cámara.', 'error');
                    return;
                }
                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth || 1280;
                canvas.height = video.videoHeight || 720;
                ctx.filter = 'brightness(1.1) contrast(1.04)';
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                hidden.value = dataUrl;
                preview.src = dataUrl;
                preview.classList.remove('pp-hidden');
                canvas.classList.add('pp-hidden');
                video.classList.add('pp-hidden');
                $('#btnCapturarFoto')?.classList.add('pp-hidden');
                if (mediaStream) {
                    mediaStream.getTracks().forEach(t => t.stop());
                    mediaStream = null;
                }
                toast('¡Foto capturada correctamente!', 'success');
            }

            function bindManualPhoto() {
                const input = $('#foto_manual'),
                    preview = $('#camPreview'),
                    hidden = $('#foto_base64'),
                    ph = $('#camPlaceholder');
                if (!input) return;
                input.addEventListener('change', () => {
                    const file = input.files?.[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = () => {
                        hidden.value = String(reader.result || '');
                        preview.src = hidden.value;
                        preview.classList.remove('pp-hidden');
                        $('#camVideo')?.classList.add('pp-hidden');
                        $('#camCanvas')?.classList.add('pp-hidden');
                        if (ph) ph.style.display = 'none';
                        toast('Foto cargada correctamente.', 'success');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // ── Validations ────────────────────────────────────────────────────────────

            function validateStep1() {
                if (!validEmail($('#login_correo')?.value || '')) {
                    toast('Captura un correo válido.', 'error');
                    return false;
                }
                if (!validPhone($('#login_telefono')?.value || '')) {
                    toast('Teléfono: 10 dígitos.', 'error');
                    return false;
                }
                syncIdentityFromLogin();
                return true;
            }

            function validateStep2() {
                if (!($('#nombre_usuario')?.value || '').trim()) {
                    toast('Falta nombre(s).', 'error');
                    return false;
                }
                if (!($('#apellido_pa')?.value || '').trim()) {
                    toast('Falta apellido paterno.', 'error');
                    return false;
                }
                if (!($('#curp_usuario')?.value || '').trim()) {
                    toast('Falta CURP.', 'error');
                    return false;
                }
                if (!($('#fecha_nacimiento')?.value || '').trim()) {
                    toast('Falta fecha de nacimiento.', 'error');
                    return false;
                }
                const age = calcAge($('#fecha_nacimiento')?.value || '');
                if (age >= 65) {
                    if (!($('[name="ac1_nombre"]')?.value || '').trim()) {
                        toast('Falta nombre del acompañante 1.', 'error');
                        return false;
                    }
                    if (!($('[name="ac2_nombre"]')?.value || '').trim()) {
                        toast('Falta nombre del acompañante 2.', 'error');
                        return false;
                    }
                }
                return true;
            }

            function validateStep3() {
                if (!($('#dom_calle')?.value || '').trim()) {
                    toast('Falta calle.', 'error');
                    return false;
                }
                if (!($('#dom_num_ext')?.value || '').trim()) {
                    toast('Falta número exterior.', 'error');
                    return false;
                }
                if (!($('#dom_colonia')?.value || '').trim()) {
                    toast('Falta colonia.', 'error');
                    return false;
                }
                if (onlyDigits($('#dom_cp')?.value || '').length !== 5) {
                    toast('Código postal inválido.', 'error');
                    return false;
                }
                if (!($('#dom_municipio')?.value || '').trim()) {
                    toast('Falta municipio.', 'error');
                    return false;
                }
                if (!($('#dom_estado')?.value || '').trim()) {
                    toast('Falta estado.', 'error');
                    return false;
                }
                if (!($('#dom_pais')?.value || '').trim()) {
                    toast('Falta país.', 'error');
                    return false;
                }
                return true;
            }

            function validateStep4() {
                for (const id of ['doc_ine_frente', 'doc_ine_reverso', 'doc_curp']) {
                    if (!$('#' + id)?.files?.length) {
                        toast('Debes cargar todos los documentos obligatorios.', 'error');
                        return false;
                    }
                }
                return true;
            }

            function validateStep5() {
                if (!($('#foto_base64')?.value || '').trim()) {
                    toast('Debes capturar o subir la fotografía.', 'error');
                    return false;
                }
                return true;
            }

            function validateStep6() {
                if (!$('#acepta_contrato')?.checked) {
                    toast('Debes aceptar el contrato.', 'error');
                    return false;
                }
                if (!signHasStroke || !($('#firma_base64')?.value || '').trim()) {
                    toast('Debes firmar en pantalla.', 'error');
                    return false;
                }
                return true;
            }

            function validateCurrentStep() {
                return [null, validateStep1, validateStep2, validateStep3, validateStep4, validateStep5, validateStep6][
                    currentStep
                ]?.() ?? true;
            }

            // ── Submit ─────────────────────────────────────────────────────────────────

            async function submitWizard(ev) {
                ev.preventDefault();
                syncIdentityFromLogin();
                for (let s = 1; s <= 6; s++) {
                    const fn = [null, validateStep1, validateStep2, validateStep3, validateStep4, validateStep5,
                        validateStep6
                    ][s];
                    if (fn && !fn()) return;
                }
                const btn = $('#btnSubmitPago');
                btn.disabled = true;
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Procesando...';
                const fd = new FormData(ev.currentTarget);
                try {
                    const res = await fetch(CFG.url_orden, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CFG.csrf
                        },
                        body: fd
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || data.ok === false) {
                        showModal(data.error || 'No fue posible generar la orden.');
                        return;
                    }
                    if (data.checkout_url) {
                        window.location.href = data.checkout_url;
                        return;
                    }
                    showModal('Registro guardado. Referencia: ' + (data.referencia || '-'));
                } catch (e) {
                    console.error(e);
                    showModal('No fue posible continuar a la pasarela. Intenta nuevamente.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-lock-check-outline"></i> Continuar a pago seguro';
                }
            }

            // ── Init ───────────────────────────────────────────────────────────────────

            document.addEventListener('DOMContentLoaded', () => {
                syncSteps();
                syncEmpresa();
                syncMonto();
                bindFileInputs();
                bindManualPhoto();
                syncIdentityFromLogin();
                syncEstadoDesdeAcronimo();
                setupSignaturePad();

                $('#btnCloseModal')?.addEventListener('click', hideModal);
                $('#btnLimpiarFirma')?.addEventListener('click', clearSignature);
                $('#btnIniciarCamara')?.addEventListener('click', startCamera);
                $('#btnCapturarFoto')?.addEventListener('click', capturePhoto);

                $('#login_telefono')?.addEventListener('input', e => {
                    e.target.value = onlyDigits(e.target.value).slice(0, 10);
                    syncIdentityFromLogin();
                });
                $('#login_correo')?.addEventListener('input', syncIdentityFromLogin);
                $('#curp_usuario')?.addEventListener('input', e => {
                    e.target.value = e.target.value.toUpperCase().slice(0, 18);
                    queueContractPreview();
                });
                $('#dom_cp')?.addEventListener('input', e => {
                    e.target.value = onlyDigits(e.target.value).slice(0, 5);
                    queueContractPreview();
                });
                $('#dom_estado_acronimo')?.addEventListener('change', () => {
                    syncEstadoDesdeAcronimo();
                    queueContractPreview();
                });
                $('#tipo_cliente')?.addEventListener('change', syncEmpresa);
                $('#fecha_nacimiento')?.addEventListener('change', () => {
                    syncAdultosMayores();
                    queueContractPreview();
                });
                $('#frecuencia_pago_publica')?.addEventListener('change', () => {
                    syncMonto();
                    queueContractPreview();
                });

                ['#nombre_usuario', '#apellido_pa', '#apellido_ma', '#dom_calle', '#dom_num_ext',
                    '#dom_num_int', '#dom_colonia', '#dom_municipio', '#dom_estado', '#dom_pais'
                ].forEach(sel => {
                    $(sel)?.addEventListener('input', queueContractPreview);
                    $(sel)?.addEventListener('change', queueContractPreview);
                });

                window.addEventListener('resize', () => {
                    if (currentStep === 6) setTimeout(() => setupSignaturePad(false), 40);
                });

                $('#btnPrev')?.addEventListener('click', () => {
                    if (currentStep > 1) {
                        currentStep--;
                        syncSteps();
                    }
                });

                $('#btnNext')?.addEventListener('click', () => {
                    if (!validateCurrentStep()) return;
                    if (currentStep < totalSteps) {
                        currentStep++;
                        syncSteps();
                        if (currentStep === 6) {
                            syncIdentityFromLogin();
                            syncMonto();
                            refreshContractPreview();
                            setTimeout(() => setupSignaturePad(false), 60);
                        }
                    }
                });

                // Step items (click on done steps to go back)
                $$('[data-step]').forEach(el => {
                    el.addEventListener('click', () => {
                        const s = Number(el.dataset.step);
                        if (s < currentStep) {
                            currentStep = s;
                            syncSteps();
                        }
                    });
                });

                $('#frmPagoPatsPublico')?.addEventListener('submit', submitWizard);
            });
        })();
    </script>
</body>

</html>
