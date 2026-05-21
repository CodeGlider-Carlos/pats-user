{{-- resources/views/pats/solicitud_distribuidor.blade.php --}}
{{-- Layout propio — no extiende layouts.app --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitud de Distribuidor · PATS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>

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
            --warning: #f59e0b;

            --border: rgba(59, 116, 245, .14);
            --border-focus: rgba(59, 116, 245, .5);
            --shadow-sm: 0 1px 3px rgba(59, 116, 245, .08), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow-md: 0 4px 16px rgba(59, 116, 245, .10), 0 2px 6px rgba(0, 0, 0, .05);
            --shadow-lg: 0 12px 40px rgba(59, 116, 245, .12), 0 4px 12px rgba(0, 0, 0, .06);
            --shadow-card: 0 0 0 1px var(--border), var(--shadow-lg);

            --radius-sm: 10px;
            --radius: 16px;
            --radius-lg: 22px;
            --radius-xl: 30px;

            --font: 'Plus Jakarta Sans', sans-serif;
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
            background: var(--page);
            color: var(--slate-800);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, .85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 60px;
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
            color: var(--blue-600);
            letter-spacing: -.02em;
        }

        .topbar__brand i {
            font-size: 22px;
        }

        .topbar__tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--slate-400);
        }

        .topbar__secure {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--success);
            background: var(--success-bg);
            padding: 5px 12px;
            border-radius: 100px;
        }

        .topbar__secure i {
            font-size: 14px;
        }

        .sol-layout {
            max-width: 1180px;
            margin: 0 auto;
            padding: 40px 24px 80px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }

        .sidebar {
            position: sticky;
            top: 84px;
        }

        .sidebar__hero {
            background: linear-gradient(135deg, var(--blue-600) 0%, var(--blue-700) 100%);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            color: white;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .sidebar__hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
        }

        .sidebar__hero::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
        }

        .sidebar__icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, .18);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .sidebar__title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }

        .sidebar__sub {
            font-size: 13px;
            opacity: .75;
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }

        .sidebar__price {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-top: 16px;
            position: relative;
            z-index: 1;
        }

        .sidebar__price-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .10em;
            text-transform: uppercase;
            opacity: .7;
        }

        .sidebar__price-amount {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
            margin: 2px 0;
        }

        .sidebar__price-note {
            font-size: 11.5px;
            opacity: .7;
        }

        .sidebar__steps {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .sidebar__steps-title {
            font-size: 11px;
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
            background: var(--blue-100);
            border-radius: 2px;
        }

        .step-list__fill {
            position: absolute;
            left: 15px;
            top: 8px;
            width: 2px;
            background: linear-gradient(to bottom, var(--blue-500), var(--cyan-400));
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

        .step-item--clickable {
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
            background: var(--blue-50);
            border: 2px solid var(--blue-100);
            color: var(--slate-400);
            transition: all .3s cubic-bezier(.34, 1.56, .64, 1);
        }

        .step-item.is-active .step-num {
            background: var(--blue-500);
            border-color: var(--blue-500);
            color: white;
            box-shadow: 0 0 0 5px var(--blue-100);
            transform: scale(1.08);
        }

        .step-item.is-done .step-num {
            background: var(--success);
            border-color: var(--success);
            color: white;
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
            color: var(--blue-600);
        }

        .step-item.is-done .step-label {
            color: var(--slate-600);
        }

        .main-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .progress-bar {
            height: 4px;
            background: var(--blue-50);
        }

        .progress-bar__fill {
            height: 100%;
            width: 20%;
            background: linear-gradient(90deg, var(--blue-500), var(--cyan-400));
            border-radius: 0 4px 4px 0;
            transition: width .55s cubic-bezier(.65, 0, .35, 1);
            position: relative;
        }

        .progress-bar__fill::after {
            content: '';
            position: absolute;
            right: -4px;
            top: -3px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--cyan-400);
            box-shadow: 0 0 8px var(--cyan-400);
        }

        .card-header {
            padding: 32px 40px 26px;
            border-bottom: 1px solid var(--blue-50);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .card-header__tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--blue-50);
            border: 1px solid var(--blue-100);
            border-radius: 100px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--blue-500);
            margin-bottom: 10px;
        }

        .card-header__title {
            font-size: 22px;
            font-weight: 800;
            color: var(--slate-800);
            letter-spacing: -.02em;
            margin-bottom: 5px;
        }

        .card-header__desc {
            font-size: 14px;
            color: var(--slate-500);
            line-height: 1.55;
        }

        .card-header__counter {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 500;
            color: var(--slate-400);
            white-space: nowrap;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
        }

        .card-body {
            padding: 36px 40px;
        }

        .card-footer {
            padding: 22px 40px 28px;
            border-top: 1px solid var(--blue-50);
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .panel {
            display: none;
        }

        .panel.is-active {
            display: block;
            animation: slideIn .4s cubic-bezier(.25, .46, .45, .94) both;
        }

        .panel.slide-back.is-active {
            animation: slideInBack .4s cubic-bezier(.25, .46, .45, .94) both;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInBack {
            from {
                opacity: 0;
                transform: translateX(-24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .label__req {
            color: var(--blue-500);
        }

        .label__opt {
            font-weight: 500;
            font-size: 10.5px;
            text-transform: none;
            letter-spacing: 0;
            color: var(--slate-300);
            margin-left: auto;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-300);
            font-size: 18px;
            pointer-events: none;
            transition: color .2s;
        }

        .input-wrap:focus-within .icon-left {
            color: var(--blue-500);
        }

        .input-wrap .icon-status {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .2s;
        }

        .input-wrap .input.is-valid~.icon-status {
            opacity: 1;
            color: var(--success);
        }

        .input-wrap .input.is-invalid~.icon-status {
            opacity: 1;
            color: var(--danger);
        }

        .input,
        .select,
        .textarea {
            width: 100%;
            padding: 13px 16px;
            font-family: var(--font);
            font-size: 14.5px;
            color: var(--slate-800);
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none;
        }

        .input-wrap .input,
        .input-wrap .select {
            padding-left: 44px;
        }

        .input::placeholder,
        .textarea::placeholder {
            color: var(--slate-300);
        }

        .select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        .textarea {
            resize: vertical;
            min-height: 88px;
            font-size: 14px;
        }

        .input:hover,
        .select:hover {
            border-color: var(--blue-200);
        }

        .input:focus,
        .select:focus,
        .textarea:focus {
            border-color: var(--blue-500);
            box-shadow: 0 0 0 3px var(--blue-100);
            background: var(--white);
        }

        .input.is-valid {
            border-color: var(--success);
            background: var(--success-bg);
        }

        .input.is-invalid {
            border-color: var(--danger);
            background: var(--danger-bg);
        }

        .input.is-valid:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .12);
        }

        .input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .10);
        }

        .field-msg {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            min-height: 14px;
        }

        .field-msg--error {
            color: var(--danger);
        }

        .field-msg--hint {
            color: var(--slate-400);
        }

        .field-msg i {
            font-size: 13px;
        }

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
            background: var(--blue-50);
        }

        .divider__label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--slate-300);
        }

        .toggle-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 4px;
            gap: 4px;
        }

        .toggle-radio {
            display: none;
        }

        .toggle-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-400);
            cursor: pointer;
            transition: all .2s;
        }

        .toggle-label i {
            font-size: 17px;
        }

        .toggle-radio:checked+.toggle-label {
            background: var(--blue-500);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 116, 245, .3);
        }

        .file-zone {
            position: relative;
            border: 2px dashed var(--blue-200);
            border-radius: var(--radius-sm);
            background: var(--blue-50);
            padding: 24px 20px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
        }

        .file-zone:hover,
        .file-zone.dragover {
            border-color: var(--blue-500);
            background: var(--blue-100);
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
            font-size: 28px;
            color: var(--blue-400);
            margin-bottom: 8px;
        }

        .file-zone.filled .file-zone__icon {
            color: var(--success);
        }

        .file-zone__title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--slate-600);
        }

        .file-zone__sub {
            font-size: 11.5px;
            color: var(--slate-400);
            margin-top: 3px;
        }

        .file-zone__name {
            font-size: 13px;
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

        .doc-card {
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px;
            transition: border-color .2s;
        }

        .doc-card.filled {
            border-color: var(--success);
        }

        .doc-card__top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .doc-card__icon {
            color: var(--blue-400);
            font-size: 18px;
        }

        .doc-card.filled .doc-card__icon {
            color: var(--success);
        }

        .doc-card__name {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--slate-600);
            flex: 1;
        }

        .doc-card__req {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--danger);
            background: var(--danger-bg);
            padding: 2px 7px;
            border-radius: 100px;
        }

        .doc-card.filled .doc-card__req {
            color: var(--success);
            background: var(--success-bg);
        }

        .moral-section {
            grid-column: 1/-1;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height .4s ease, opacity .3s ease;
        }

        .moral-section.open {
            max-height: 500px;
            opacity: 1;
        }

        .moral-inner {
            padding: 18px;
            background: rgba(59, 116, 245, .04);
            border: 1.5px solid var(--blue-100);
            border-radius: var(--radius-sm);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 4px;
        }

        .modal-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .modal-radio {
            display: none;
        }

        .modal-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 18px;
            background: var(--surface-2);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all .2s;
        }

        .modal-card:hover {
            border-color: var(--blue-200);
        }

        .modal-radio:checked+.modal-card {
            border-color: var(--blue-500);
            background: var(--blue-50);
            box-shadow: 0 0 0 4px var(--blue-100);
        }

        .modal-card__dot {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            border-radius: 50%;
            border: 2px solid var(--slate-300);
            margin-top: 2px;
            position: relative;
            transition: all .2s;
        }

        .modal-radio:checked+.modal-card .modal-card__dot {
            border-color: var(--blue-500);
            background: var(--blue-500);
        }

        .modal-radio:checked+.modal-card .modal-card__dot::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: white;
        }

        .modal-card__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 2px;
        }

        .modal-card__sub {
            font-size: 12px;
            color: var(--slate-400);
            line-height: 1.4;
        }

        .plan-wrap {
            background: var(--surface-2);
            border: 1.5px solid var(--blue-100);
            border-radius: var(--radius-sm);
            overflow: hidden;
            margin-top: 16px;
        }

        .plan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .plan-table thead tr {
            background: var(--blue-50);
        }

        .plan-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--slate-400);
            border-bottom: 1px solid var(--blue-100);
        }

        .plan-table td {
            padding: 10px 14px;
            font-family: var(--mono);
            font-size: 13px;
            color: var(--slate-600);
            border-bottom: 1px solid var(--blue-50);
        }

        .plan-table tr:last-child td {
            border-bottom: none;
            font-weight: 700;
        }

        .plan-table td:last-child {
            color: var(--blue-600);
            font-weight: 600;
        }

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

        .summary-item__label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--slate-400);
            margin-bottom: 4px;
        }

        .summary-item__value {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .terms-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--blue-50);
            border: 1.5px solid var(--blue-100);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: border-color .2s;
            margin-top: 4px;
        }

        .terms-box:has(input:checked) {
            border-color: var(--blue-500);
        }

        .terms-box input[type=checkbox] {
            width: 17px;
            height: 17px;
            accent-color: var(--blue-500);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .terms-box__text {
            font-size: 13px;
            color: var(--slate-500);
            line-height: 1.55;
        }

        .terms-box__text a {
            color: var(--blue-500);
            text-decoration: none;
        }

        .terms-box__text a:hover {
            text-decoration: underline;
        }

        /* ── Stripe card ── */
        .cc-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .cc-scene {
            perspective: 1000px;
            margin-bottom: 24px;
            display: flex;
            justify-content: center;
        }

        .cc-card {
            width: 340px;
            height: 200px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform .65s cubic-bezier(.4, 0, .2, 1);
            cursor: default;
        }

        .cc-card.is-flipped {
            transform: rotateY(180deg);
        }

        .cc-card__front,
        .cc-card__back {
            position: absolute;
            inset: 0;
            border-radius: 18px;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            padding: 24px 28px;
            box-shadow: 0 20px 60px rgba(37, 88, 224, .25), 0 4px 12px rgba(0, 0, 0, .1);
            overflow: hidden;
        }

        .cc-card__front {
            background: linear-gradient(135deg, var(--blue-600) 0%, var(--blue-700) 50%, #1a3fb5 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cc-card__shine {
            position: absolute;
            top: -60%;
            left: -40%;
            width: 180%;
            height: 180%;
            background: radial-gradient(ellipse at 30% 30%, rgba(255, 255, 255, .18) 0%, transparent 60%);
            pointer-events: none;
        }

        .cc-card__back {
            background: linear-gradient(135deg, #1a3fb5 0%, var(--blue-700) 100%);
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cc-card__strip {
            height: 44px;
            background: rgba(0, 0, 0, .45);
            margin: 0 -28px 20px;
        }

        .cc-card__cvv-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cc-card__cvv-label {
            font-size: 11px;
            color: rgba(255, 255, 255, .6);
            font-weight: 700;
            letter-spacing: .08em;
        }

        .cc-card__cvv-box {
            background: white;
            color: var(--slate-800);
            padding: 6px 16px;
            border-radius: 6px;
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .15em;
            min-width: 60px;
            text-align: center;
        }

        .cc-card__back-brand {
            position: absolute;
            bottom: 20px;
            right: 24px;
            font-size: 28px;
            opacity: .4;
            color: white;
        }

        .cc-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cc-card__chip {
            width: 42px;
            height: 32px;
            background: linear-gradient(135deg, #e8c96d, #c9a227);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cc-chip__inner {
            width: 24px;
            height: 18px;
            border: 1.5px solid rgba(0, 0, 0, .25);
            border-radius: 3px;
            background: linear-gradient(135deg, #f0d078, #d4a830);
        }

        .cc-card__brand {
            font-size: 32px;
            color: rgba(255, 255, 255, .9);
        }

        .cc-card__number {
            font-family: var(--mono);
            font-size: 20px;
            letter-spacing: .12em;
            font-weight: 500;
            color: white;
        }

        .cc-card__bottom {
            display: flex;
            gap: 32px;
        }

        .cc-card__label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .55);
            margin-bottom: 3px;
        }

        .cc-card__value {
            font-size: 13px;
            font-weight: 700;
            color: white;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .cc-security {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
            padding: 12px 0 4px;
        }

        .cc-security span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--slate-400);
        }

        .cc-security i {
            font-size: 14px;
            color: var(--success);
        }

        .stripe-element-wrap {
            padding: 13px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            transition: border-color .2s, box-shadow .2s;
            min-height: 46px;
        }

        .stripe-element-wrap.StripeElement--focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(59, 116, 245, .12);
        }

        .stripe-element-wrap.StripeElement--invalid {
            border-color: var(--danger);
        }

        #stripe-card-errors {
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--danger);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
            text-decoration: none;
        }

        .btn i {
            font-size: 18px;
        }

        .btn:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn--ghost {
            background: white;
            color: var(--slate-500);
            border: 1.5px solid var(--border);
        }

        .btn--ghost:hover {
            border-color: var(--blue-200);
            color: var(--slate-700);
        }

        .btn--primary {
            background: var(--blue-500);
            color: white;
            box-shadow: 0 4px 14px rgba(59, 116, 245, .3);
        }

        .btn--primary:hover:not(:disabled) {
            background: var(--blue-600);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 116, 245, .35);
        }

        .btn--success {
            background: var(--success);
            color: white;
            box-shadow: 0 4px 14px rgba(16, 185, 129, .3);
        }

        .btn--success:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, .38);
        }

        .toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            max-width: 340px;
            padding: 14px 18px;
            border-radius: var(--radius);
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-lg);
            transform: translateY(16px);
            opacity: 0;
            transition: all .32s cubic-bezier(.34, 1.56, .64, 1);
            pointer-events: none;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .toast--error {
            background: white;
            border: 1.5px solid var(--danger);
            color: var(--danger);
        }

        .toast--success {
            background: white;
            border: 1.5px solid var(--success);
            color: var(--success);
        }

        .toast--info {
            background: white;
            border: 1.5px solid var(--blue-400);
            color: var(--blue-600);
        }

        .toast i {
            font-size: 20px;
            flex-shrink: 0;
        }

        .success-screen {
            display: none;
            padding: 64px 40px;
            text-align: center;
        }

        .success-screen.show {
            display: block;
        }

        .success-ring {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: var(--success-bg);
            border: 3px solid var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            font-size: 44px;
            color: var(--success);
            animation: popIn .55s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        @keyframes popIn {
            from {
                transform: scale(.4);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--slate-800);
            margin-bottom: 10px;
        }

        .success-msg {
            font-size: 15px;
            color: var(--slate-500);
            max-width: 400px;
            margin: 0 auto 24px;
            line-height: 1.6;
        }

        .success-ref {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--blue-50);
            border: 1.5px solid var(--blue-200);
            border-radius: 100px;
            padding: 10px 22px;
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 600;
            color: var(--blue-600);
        }

        .success-note {
            font-size: 13px;
            color: var(--slate-400);
            margin-top: 16px;
        }

        /* ── Selfie ── */
        .selfie-cam-wrap {
            background: var(--slate-800);
            border-radius: var(--radius-sm);
            overflow: hidden;
            max-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .selfie-cam-wrap video,
        .selfie-cam-wrap .selfie-capture-img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            transform: scaleX(-1);
        }

        .selfie-cam-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--slate-400);
            padding: 40px;
            text-align: center;
        }

        .selfie-cam-placeholder i {
            font-size: 40px;
            color: var(--slate-500);
        }

        .selfie-cam-placeholder span {
            font-size: 13px;
        }

        /* ── Firma ── */
        .firma-wrap {
            position: relative;
            background: var(--white);
            border: 2px dashed var(--blue-200);
            border-radius: var(--radius-sm);
            overflow: hidden;
            cursor: crosshair;
            touch-action: none;
            user-select: none;
        }

        .firma-wrap.signed {
            border-style: solid;
            border-color: var(--success);
        }

        .firma-wrap canvas {
            display: block;
            width: 100%;
            height: 180px;
            touch-action: none;
        }

        .firma-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: var(--slate-300);
            pointer-events: none;
            user-select: none;
        }

        .firma-placeholder i {
            font-size: 20px;
        }

        .firma-wrap.signed .firma-placeholder {
            display: none;
        }

        /* ── Aviso de firma pendiente ── */
        .firma-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #fffbeb;
            border: 1.5px solid #fde68a;
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: #92400e;
            margin-bottom: 12px;
        }

        .firma-hint i {
            font-size: 16px;
            color: #f59e0b;
            flex-shrink: 0;
        }

        .firma-hint.hidden {
            display: none;
        }

        /* ── Overlay firma sobre iframe ── */
        .contrato-wrapper {
            position: relative;
        }

        #contrato_firma_overlay {
            display: none;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.97);
            border-top: 2px solid var(--blue-100);
            padding: 20px 32px 24px;
            pointer-events: none;
        }

        /* ── Preview dist ── */
        #preview_dist th {
            text-align: center;
        }

        #preview_dist td {
            vertical-align: middle;
            text-align: center;
            padding: 16px;
        }

        #preview_dist img.pd-img {
            max-width: 130px;
            max-height: 100px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            object-fit: cover;
        }

        #preview_dist .pd-empty {
            font-size: 12px;
            color: var(--slate-400);
            font-style: italic;
        }

        /* ── Responsive ── */
        @media (max-width: 960px) {
            .sol-layout {
                grid-template-columns: 1fr;
                padding: 20px 16px 60px;
                gap: 20px;
            }

            .sidebar {
                position: static;
            }

            .sidebar__hero {
                display: none;
            }

            .sidebar__steps {
                display: flex;
                align-items: center;
                gap: 6px;
                overflow-x: auto;
                padding: 14px 16px;
            }

            .sidebar__steps-title {
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
                padding: 22px 22px 18px;
                flex-direction: column;
            }

            .card-body {
                padding: 24px 22px;
            }

            .card-footer {
                padding: 16px 22px 22px;
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

            .modal-cards {
                grid-template-columns: 1fr;
            }

            .moral-inner {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                padding: 0 16px;
            }

            .toggle-group {
                grid-template-columns: 1fr;
            }

            .btn {
                padding: 11px 18px;
                font-size: 13px;
            }

            .success-screen {
                padding: 40px 22px;
            }

            .cc-card {
                width: 290px;
                height: 170px;
            }

            .cc-card__number {
                font-size: 16px;
            }

            .cc-card__front,
            .cc-card__back {
                padding: 18px 20px;
            }

            .cc-fields {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    {{-- TOP BAR --}}
    <header class="topbar">
        <div class="topbar__brand">
            <i class="mdi mdi-shield-check"></i>
            PATS
            <span class="topbar__tag">· Red de Distribuidors</span>
        </div>
        <div class="topbar__secure">
            <i class="mdi mdi-lock"></i> Conexión segura
        </div>
    </header>

    <div class="sol-layout">

        {{-- ════ SIDEBAR ════ --}}
        <aside class="sidebar">
            <div class="sidebar__hero">
                <div class="sidebar__icon"><i class="mdi mdi-handshake-outline"></i></div>
                <div class="sidebar__title">Solicitud de Alta<br>como Distribuidor</div>
                <div class="sidebar__sub">Forma parte de la red comercial más grande del sector médico en México.</div>
                <div class="sidebar__price">
                    <div class="sidebar__price-label">Inversión inicial</div>
                    <div class="sidebar__price-amount">${{ number_format($precioDistribucion ?? 20000, 0, '.', ',') }}
                    </div>
                    <div class="sidebar__price-note">MXN · Sin enganche · A meses disponible</div>
                </div>
            </div>

            <div class="sidebar__steps">
                <div class="sidebar__steps-title">Tu progreso</div>
                @php
                    $stepsData = [
                        ['label' => 'Ubicación', 'desc' => 'País, estado y dirección'],
                        ['label' => 'Distribuidor', 'desc' => 'Perfil del titular'],
                        ['label' => 'Bancarios', 'desc' => 'Datos de tu cuenta'],
                        ['label' => 'Documentos', 'desc' => 'Archivos requeridos'],
                        ['label' => 'Biometría', 'desc' => 'Selfie, firma y contrato'],
                        ['label' => 'Plan de pago', 'desc' => 'Esquema financiero'],
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
            <form id="frmDist" novalidate>
                @csrf
                @if (!empty($token))
                    <input type="hidden" name="public_token" value="{{ $token }}">
                @endif
                <div class="main-card">

                    <div class="progress-bar">
                        <div class="progress-bar__fill" id="progressFill" style="width:20%"></div>
                    </div>

                    <div class="card-header">
                        <div>
                            <div class="card-header__tag" id="cardTag">
                                <i class="mdi mdi-map-marker-outline"></i> Paso 1 de 6
                            </div>
                            <h1 class="card-header__title" id="cardTitle">Datos generales</h1>
                            <p class="card-header__desc" id="cardDesc">Indica tu ubicación y dirección de operación.
                            </p>
                        </div>
                        <div class="card-header__counter" id="cardCounter">1 / 6</div>
                    </div>

                    <div class="card-body">

                        {{-- ── PANEL 1: UBICACIÓN ── --}}
                        <div class="panel is-active" data-panel="1">
                            <div class="fields">
                                <div class="field field--full">
                                    <label class="label" for="pais">País <span class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-earth icon-left"></i>
                                        <select class="select" id="pais" name="pais" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="MX" selected>México</option>
                                            <option value="US">Estados Unidos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="region">Estado <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-map icon-left"></i>
                                        <select class="select" id="region" name="region" required>
                                            <option value="">Seleccionar estado...</option>
                                            @foreach (['AGS' => 'Aguascalientes', 'BCN' => 'Baja California', 'BCS' => 'Baja California Sur', 'CAM' => 'Campeche', 'CHP' => 'Chiapas', 'CHH' => 'Chihuahua', 'CDMX' => 'Ciudad de México', 'COA' => 'Coahuila', 'COL' => 'Colima', 'DUR' => 'Durango', 'MEX' => 'Estado de México', 'GTO' => 'Guanajuato', 'GRO' => 'Guerrero', 'HGO' => 'Hidalgo', 'JAL' => 'Jalisco', 'MIC' => 'Michoacán', 'MOR' => 'Morelos', 'NAY' => 'Nayarit', 'NLE' => 'Nuevo León', 'OAX' => 'Oaxaca', 'PUE' => 'Puebla', 'QRO' => 'Querétaro', 'ROO' => 'Quintana Roo', 'SLP' => 'San Luis Potosí', 'SIN' => 'Sinaloa', 'SON' => 'Sonora', 'TAB' => 'Tabasco', 'TAM' => 'Tamaulipas', 'TLAX' => 'Tlaxcala', 'VER' => 'Veracruz', 'YUC' => 'Yucatán', 'ZAC' => 'Zacatecas'] as $code => $name)
                                                <option value="{{ $code }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="municipio">Municipio / Ciudad <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-city icon-left"></i>
                                        <input class="input" type="text" id="municipio" name="municipio"
                                            placeholder="Ej. Zapopan" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Dirección</span>
                                    <div class="divider__line"></div>
                                </div>
                                <div class="field">
                                    <label class="label" for="calle">Calle <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-road icon-left"></i>
                                        <input class="input" type="text" id="calle" name="calle"
                                            placeholder="Nombre de la calle" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="num_ext">Número exterior <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-numeric icon-left"></i>
                                        <input class="input" type="text" id="num_ext" name="num_ext"
                                            placeholder="Ej. 425" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="num_int">Número interior <span
                                            class="label__opt">Opcional</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-home-outline icon-left"></i>
                                        <input class="input" type="text" id="num_int" name="num_int"
                                            placeholder="Ej. 12-B">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="cp">Código postal <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-mailbox icon-left"></i>
                                        <input class="input" type="text" id="cp" name="cp"
                                            placeholder="5 dígitos" maxlength="5" inputmode="numeric" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="field field--full">
                                    <label class="label" for="colonia">Colonia <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-map-marker icon-left"></i>
                                        <input class="input" type="text" id="colonia" name="colonia"
                                            placeholder="Nombre de la colonia" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── PANEL 2: DISTRIBUCIÓN ── --}}
                        <div class="panel" data-panel="2">
                            <div class="fields">
                                <div class="field field--full">
                                    <label class="label">Tipo de persona <span class="label__req">*</span></label>
                                    <div class="toggle-group">
                                        <input type="radio" class="toggle-radio" name="tipo_persona"
                                            id="tp_fisica" value="FISICA" checked>
                                        <label class="toggle-label" for="tp_fisica"><i
                                                class="mdi mdi-account"></i>Persona Física</label>
                                        <input type="radio" class="toggle-radio" name="tipo_persona"
                                            id="tp_moral" value="MORAL">
                                        <label class="toggle-label" for="tp_moral"><i
                                                class="mdi mdi-office-building"></i>Persona Moral</label>
                                    </div>
                                </div>
                                <div class="field field--full">
                                    <label class="label" for="nombre">Nombre del titular <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-outline icon-left"></i>
                                        <input class="input" type="text" id="nombre" name="nombre"
                                            placeholder="Nombre completo" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="correo">Correo electrónico <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-email-outline icon-left"></i>
                                        <input class="input" type="email" id="correo" name="correo"
                                            placeholder="ejemplo@correo.com" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                    <span class="field-msg" id="emailMsg"></span>
                                </div>
                                <div class="field">
                                    <label class="label" for="telefono">Teléfono <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-phone-outline icon-left"></i>
                                        <input class="input" type="tel" id="telefono" name="telefono"
                                            placeholder="10 dígitos" maxlength="10" inputmode="numeric" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                    <span class="field-msg" id="telMsg"></span>
                                </div>
                                <div class="field">
                                    <label class="label" for="rfc">RFC <span
                                            class="label__opt">Opcional</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-card-account-details-outline icon-left"></i>
                                        <input class="input" type="text" id="rfc" name="rfc"
                                            placeholder="XXXX000000XX0" maxlength="13"
                                            style="text-transform:uppercase;font-family:var(--mono)">
                                    </div>
                                </div>
                                <div class="field" id="razonWrap" style="display:none;">
                                    <label class="label" for="razon_social">Razón social <span class="label__opt"
                                            id="razonOpt">Persona moral</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-domain icon-left"></i>
                                        <input class="input" type="text" id="razon_social" name="razon_social"
                                            placeholder="Nombre de la empresa">
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── PANEL 3: BANCARIOS ── --}}
                        <div class="panel" data-panel="3">
                            <div class="fields">
                                <div class="field">
                                    <label class="label" for="banco">Banco <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-bank-outline icon-left"></i>
                                        <select class="select" id="banco" name="banco" required>
                                            <option value="">Seleccionar banco...</option>
                                            @foreach (['BBVA', 'Banamex / Citibanamex', 'Banorte', 'Santander', 'HSBC', 'Scotiabank', 'Inbursa', 'Afirme', 'BanBajío', 'Multiva', 'Banca Mifel', 'Azteca', 'BanRegio'] as $b)
                                                <option>{{ $b }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="titular_cuenta">Titular de la cuenta <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-account-check-outline icon-left"></i>
                                        <input class="input" type="text" id="titular_cuenta"
                                            name="titular_cuenta" placeholder="Como aparece en el banco" required>
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="numero_cuenta">Número de cuenta <span
                                            class="label__opt">Opcional</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-credit-card-outline icon-left"></i>
                                        <input class="input" type="text" id="numero_cuenta" name="numero_cuenta"
                                            placeholder="00000000000" maxlength="11" inputmode="numeric"
                                            style="font-family:var(--mono)">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label" for="clabe">CLABE interbancaria <span
                                            class="label__req">*</span><span class="label__opt">18
                                            dígitos</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-bank-transfer icon-left"></i>
                                        <input class="input" type="text" id="clabe" name="clabe"
                                            placeholder="000000000000000000" maxlength="18" inputmode="numeric"
                                            required style="font-family:var(--mono);letter-spacing:.05em">
                                        <i class="mdi mdi-check-circle icon-status"></i>
                                    </div>
                                    <span class="field-msg" id="clabeMsg"></span>
                                </div>
                                <div class="field field--full">
                                    <label class="label">Carátula de cuenta bancaria <span
                                            class="label__req">*</span></label>
                                    <div class="file-zone" id="zone_caratula">
                                        <input type="file" name="doc_caratula_bancaria" id="doc_caratula"
                                            accept=".pdf,.png,.jpg,.jpeg">
                                        <div class="file-zone__icon"><i class="mdi mdi-bank"></i></div>
                                        <div class="file-zone__title">Arrastra aquí o haz clic para subir</div>
                                        <div class="file-zone__sub">PDF, JPG o PNG · Máximo 5 MB</div>
                                        <div class="file-zone__name" id="name_caratula"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── PANEL 4: DOCUMENTOS ── --}}
                        <div class="panel" data-panel="4">
                            <div class="fields">
                                <p class="field--full"
                                    style="font-size:13.5px;color:var(--slate-500);line-height:1.6;margin-bottom:4px;">
                                    Todos los archivos se transmiten de forma cifrada. Formatos aceptados: PDF, JPG,
                                    PNG.
                                </p>
                                <div class="field field--full">
                                    <label class="label">Documentos requeridos</label>
                                    <div class="docs-grid">
                                        @foreach ([['id' => 'doc_ine', 'icon' => 'card-account-details', 'label' => 'INE / IFE'], ['id' => 'doc_domicilio', 'icon' => 'home-city', 'label' => 'Comprobante de domicilio'], ['id' => 'doc_cedula', 'icon' => 'file-certificate', 'label' => 'Cédula fiscal']] as $doc)
                                            <div class="doc-card" id="card_{{ $doc['id'] }}">
                                                <div class="doc-card__top">
                                                    <i class="mdi mdi-{{ $doc['icon'] }} doc-card__icon"></i>
                                                    <span class="doc-card__name">{{ $doc['label'] }}</span>
                                                    <span class="doc-card__req"
                                                        id="req_{{ $doc['id'] }}">Requerido</span>
                                                </div>
                                                <div class="file-zone" id="zone_{{ $doc['id'] }}"
                                                    style="padding:16px 12px;">
                                                    <input type="file" name="{{ $doc['id'] }}"
                                                        id="{{ $doc['id'] }}" accept=".pdf,.png,.jpg,.jpeg">
                                                    <div class="file-zone__icon"
                                                        style="font-size:22px;margin-bottom:6px;"><i
                                                            class="mdi mdi-upload"></i></div>
                                                    <div class="file-zone__title" style="font-size:12.5px;">Subir
                                                        archivo</div>
                                                    <div class="file-zone__sub" style="font-size:11px;">PDF · JPG ·
                                                        PNG</div>
                                                    <div class="file-zone__name" id="name_{{ $doc['id'] }}"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="moral-section" id="moralSection">
                                    <div class="divider" style="margin-bottom:12px;">
                                        <div class="divider__line"></div>
                                        <span class="divider__label">Solo persona moral</span>
                                        <div class="divider__line"></div>
                                    </div>
                                    <div class="moral-inner">
                                        @foreach ([['id' => 'doc_acta', 'name' => 'doc_acta_constitutiva', 'icon' => 'file-document', 'label' => 'Acta constitutiva'], ['id' => 'doc_poder', 'name' => 'doc_poder_notarial', 'icon' => 'gavel', 'label' => 'Poder notarial']] as $doc)
                                            <div class="file-zone" id="zone_{{ $doc['id'] }}">
                                                <input type="file" name="{{ $doc['name'] }}"
                                                    id="{{ $doc['id'] }}" accept=".pdf,.png,.jpg,.jpeg">
                                                <div class="file-zone__icon"><i
                                                        class="mdi mdi-{{ $doc['icon'] }}"></i></div>
                                                <div class="file-zone__title">{{ $doc['label'] }}</div>
                                                <div class="file-zone__sub">Al menos uno es requerido</div>
                                                <div class="file-zone__name" id="name_{{ $doc['id'] }}"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p
                                        style="font-size:12px;color:var(--slate-400);margin-top:8px;display:flex;align-items:center;gap:4px;">
                                        <i class="mdi mdi-information-outline" style="color:var(--blue-400);"></i>
                                        Sube al menos el acta constitutiva o el poder notarial.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ── PANEL 5: BIOMETRÍA Y CONTRATO ── --}}
                        <div class="panel" data-panel="5">
                            <div class="fields">

                                <p class="field--full"
                                    style="font-size:13.5px;color:var(--slate-500);line-height:1.6;margin-bottom:4px;">
                                    Toma tu fotografía, dibuja tu firma y luego revisa el contrato con tu firma ya
                                    aplicada antes de aceptar.
                                </p>

                                {{-- ── 1. SELFIE ── --}}
                                <div class="divider" style="margin-top:4px;">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Fotografía del titular</span>
                                    <div class="divider__line"></div>
                                </div>

                                <div class="field field--full">
                                    <label class="label">Fotografía del titular <span
                                            class="label__req">*</span></label>
                                    <div id="selfie_cam_area">
                                        <div class="selfie-cam-wrap" id="selfieCamWrap">
                                            <div class="selfie-cam-placeholder" id="selfiePlaceholder">
                                                <i class="mdi mdi-camera-off"></i>
                                                <span>Activa la cámara para tomar la fotografía</span>
                                            </div>
                                            <video id="selfieVideo" autoplay playsinline muted
                                                style="display:none;width:100%;max-height:220px;object-fit:cover;transform:scaleX(-1);"></video>
                                            <img id="selfieCaptura"
                                                style="display:none;width:100%;max-height:220px;object-fit:cover;"
                                                alt="Foto capturada">
                                        </div>
                                        <canvas id="selfieCanvas" style="display:none;"></canvas>
                                        <div
                                            style="display:flex;gap:8px;margin-top:10px;justify-content:center;flex-wrap:wrap;">
                                            <button type="button" class="btn btn--primary" id="btnStartCamera"
                                                style="font-size:13px;padding:9px 16px;">
                                                <i class="mdi mdi-camera"></i> Activar cámara
                                            </button>
                                            <button type="button" class="btn btn--primary" id="btnTakePhoto"
                                                style="display:none;font-size:13px;padding:9px 16px;">
                                                <i class="mdi mdi-camera-iris"></i> Tomar foto
                                            </button>
                                            <button type="button" class="btn btn--ghost" id="btnRetakePhoto"
                                                style="display:none;font-size:13px;padding:9px 16px;">
                                                <i class="mdi mdi-camera-retake"></i> Repetir
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="selfie_data" id="selfie_data">
                                </div>

                                {{-- ── 2. FIRMA DIGITAL — ANTES DEL CONTRATO ── --}}
                                <div class="divider">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Firma digital</span>
                                    <div class="divider__line"></div>
                                </div>

                                <div class="field field--full">
                                    <label class="label">Firma del titular <span class="label__req">*</span>
                                        <span
                                            style="margin-left:auto;font-size:11px;font-weight:500;text-transform:none;letter-spacing:0;color:var(--blue-500);">
                                            <i class="mdi mdi-information-outline"></i> Tu firma se mostrará en el
                                            contrato
                                        </span>
                                    </label>
                                    <div class="firma-wrap" id="firmaWrap">
                                        <canvas id="firmaCanvas"></canvas>
                                        <div class="firma-placeholder" id="firmaPlaceholder">
                                            <i class="mdi mdi-draw-pen"></i> Firma aquí con el mouse o el dedo
                                        </div>
                                    </div>
                                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                        <button type="button" class="btn btn--ghost" id="btnClearFirma"
                                            style="font-size:12px;padding:7px 14px;">
                                            <i class="mdi mdi-eraser"></i> Limpiar firma
                                        </button>
                                    </div>
                                    <input type="hidden" name="firma_data" id="firma_data">
                                    <input type="hidden" name="beneficiario_directo"
                                        id="beneficiario_directo_input">
                                </div>

                                {{-- ── 3. CONTRATO CON OVERLAY DE FIRMA ── --}}
                                <div class="divider">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Contrato de Distribuidor</span>
                                    <div class="divider__line"></div>
                                </div>

                                {{-- ── PREVIEW CARÁTULA: Apartados 5-9 ── --}}
                                <div class="field field--full">
                                    <div style="background:var(--blue-50);border:1.5px solid var(--blue-100);border-radius:var(--radius-sm);overflow:hidden;">
                                        <div style="display:flex;align-items:center;gap:8px;background:var(--blue-100);border-bottom:1px solid var(--blue-200);padding:10px 14px;font-size:12px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--blue-600);">
                                            <i class="mdi mdi-file-document-check-outline" style="font-size:16px;"></i>
                                            Vista previa — Carátula del Contrato
                                        </div>
                                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                                            <tr style="border-bottom:1px solid var(--blue-100);">
                                                <td style="padding:8px 14px;color:var(--slate-500);width:46%;font-size:12px;">Apartado 5 — Nombre del Distribuidor</td>
                                                <td style="padding:8px 14px;color:var(--slate-800);font-weight:600;" id="prev_apt5">—</td>
                                            </tr>
                                            <tr style="border-bottom:1px solid var(--blue-100);">
                                                <td style="padding:8px 14px;color:var(--slate-500);font-size:12px;">Apartado 6 — Domicilio del Distribuidor</td>
                                                <td style="padding:8px 14px;color:var(--slate-800);font-weight:600;" id="prev_apt6">—</td>
                                            </tr>
                                            <tr style="border-bottom:1px solid var(--blue-100);">
                                                <td style="padding:8px 14px;color:var(--slate-500);font-size:12px;">Apartado 7 — R.F.C. del Distribuidor</td>
                                                <td style="padding:8px 14px;color:var(--slate-800);font-weight:600;font-family:var(--mono);" id="prev_apt7">—</td>
                                            </tr>
                                            <tr style="border-bottom:1px solid var(--blue-100);">
                                                <td style="padding:8px 14px;color:var(--slate-500);font-size:12px;">Apartado 8 — Nombre del Franquiciatario</td>
                                                <td style="padding:8px 14px;color:var(--slate-800);font-weight:600;" id="prev_apt8">—</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 14px;color:var(--slate-500);font-size:12px;">Apartado 9 — Demarcación Territorial</td>
                                                <td style="padding:8px 14px;color:var(--slate-800);font-weight:600;" id="prev_apt9">—</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="field field--full">
                                    <label class="label">Lee el contrato antes de aceptar</label>

                                    {{-- Indicador de declaración beneficiario (se actualiza via postMessage) --}}
                                    <div id="ben_status_wrap"
                                        style="display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:var(--radius-sm);border:1.5px solid #d97706;background:#fffbeb;font-size:13px;color:#92400e;margin-bottom:12px;">
                                        <i class="mdi mdi-alert-outline"
                                            style="font-size:18px;flex-shrink:0;color:#d97706;"></i>
                                        <span id="ben_status_txt">Desplázate hasta el <strong>Anexo 12</strong> del
                                            contrato y selecciona si eres el beneficiario directo.</span>
                                    </div>

                                    {{-- Aviso si aún no hay firma --}}
                                    <div class="firma-hint" id="firmaHint">
                                        <i class="mdi mdi-alert-outline"></i>
                                        Dibuja tu firma arriba para verla aplicada en el contrato.
                                    </div>

                                    <div
                                        style="border:1.5px solid var(--blue-200);border-radius:var(--radius-sm);overflow:hidden;">
                                        <div
                                            style="display:flex;align-items:center;gap:8px;background:var(--blue-100);border-bottom:1px solid var(--blue-200);padding:10px 14px;font-size:13px;color:var(--slate-600);">
                                            <i class="mdi mdi-account-check"
                                                style="color:var(--blue-500);font-size:16px;flex-shrink:0;"></i>
                                            <span>Este contrato se emitirá a nombre de: <strong
                                                    id="contrato_nombre_live">—</strong></span>
                                        </div>

                                        {{-- Iframe + overlay absoluto --}}
                                        <div class="contrato-wrapper">
                                            <iframe id="contratoIframe" src="{{ route('franq.contrato') }}"
                                                style="width:100%;height:500px;border:none;display:block;"
                                                title="Contrato de Distribuidor PATS">
                                            </iframe>

                                            {{-- Overlay con firma y fecha renderizados --}}
                                            <div id="contrato_firma_overlay">
                                                <p
                                                    style="font-size:13px;color:var(--slate-600);margin-bottom:16px;text-align:center;">
                                                    H. Puebla de Zaragoza a <strong id="overlay_dia">—</strong>
                                                    del mes de <strong id="overlay_mes">—</strong>
                                                    del año <strong id="overlay_anio">—</strong>
                                                </p>
                                                <div
                                                    style="display:flex;justify-content:center;gap:60px;flex-wrap:wrap;">
                                                    {{-- Bloque proveedor --}}
                                                    <div style="text-align:center;min-width:200px;">
                                                        <div
                                                            style="border-bottom:1px solid #000;height:40px;margin-bottom:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                            <img src="{{ asset('images/firmas/firma_emilio_flores.png') }}"
                                                                alt="Firma Emilio Flores Cervantes"
                                                                style="max-height:38px;max-width:180px;object-fit:contain;">
                                                        </div>
                                                        <div
                                                            style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-700);">
                                                            "Pasaporte a tu Salud", S.A. de C.V.
                                                        </div>
                                                        <div
                                                            style="font-size:12px;color:var(--slate-400);margin-top:4px;">
                                                            Emilio Flores Cervantes — Administrador Único
                                                        </div>
                                                    </div>
                                                    {{-- Bloque distribuidor con firma renderizada --}}
                                                    <div style="text-align:center;min-width:200px;">
                                                        <div
                                                            style="height:40px;border-bottom:1px solid #000;margin-bottom:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                            <img id="overlay_firma_img" src=""
                                                                alt="Firma del distribuidor"
                                                                style="max-height:38px;max-width:180px;object-fit:contain;display:none;">
                                                        </div>
                                                        <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-700);"
                                                            id="overlay_nombre_dist">El Distribuidor</div>
                                                        <div
                                                            style="font-size:12px;color:var(--slate-400);margin-top:4px;">
                                                            Apartado 5 de la Carátula
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="font-size:11.5px;color:var(--slate-400);margin-top:6px;">
                                        <i class="mdi mdi-information-outline"></i>
                                        Tu firma aparece renderizada al pie del contrato. Desplázate hacia abajo en el
                                        documento para verla completa.
                                    </p>
                                </div>

                                {{-- ── 4. ACEPTACIÓN ── --}}
                                <div class="divider">
                                    <div class="divider__line"></div>
                                    <span class="divider__label">Aceptación</span>
                                    <div class="divider__line"></div>
                                </div>

                                <div class="field--full">
                                    <label class="terms-box">
                                        <input type="checkbox" id="acepta_terminos" name="acepta_terminos" required>
                                        <span class="terms-box__text">
                                            He leído y acepto los <a href="#" target="_blank">Términos y
                                                Condiciones</a>
                                            y el <a href="#" target="_blank">Contrato de Distribuidor PATS</a>.
                                            Confirmo que toda la información es verídica.
                                        </span>
                                    </label>
                                </div>

                            </div>
                        </div>

                        {{-- ── PANEL 6: PLAN DE PAGO ── --}}
                        <div class="panel" data-panel="6">
                            <div class="fields">

                                <div class="field field--full">
                                    <label class="label">Modalidad de pago <span class="label__req">*</span></label>
                                    <div class="modal-cards">
                                        <input type="radio" class="modal-radio" name="modalidad_pago"
                                            id="mp_contado" value="CONTADO" checked>
                                        <label class="modal-card" for="mp_contado">
                                            <div class="modal-card__dot"></div>
                                            <div>
                                                <div class="modal-card__title">Pago de contado</div>
                                                <div class="modal-card__sub">Liquida el total en un solo pago</div>
                                            </div>
                                        </label>
                                        <input type="radio" class="modal-radio" name="modalidad_pago"
                                            id="mp_meses" value="DIFERIDO">
                                        <label class="modal-card" for="mp_meses">
                                            <div class="modal-card__dot"></div>
                                            <div>
                                                <div class="modal-card__title">Meses sin intereses</div>
                                                <div class="modal-card__sub">Sin enganche · Cuotas fijas mensuales
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Valor total</label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-currency-mxn icon-left"></i>
                                        <input class="input" type="text"
                                            value="${{ number_format($precioDistribucion ?? 20000, 2) }} MXN" readonly
                                            style="font-family:var(--mono);font-size:16px;font-weight:700;color:var(--blue-600);background:var(--blue-50);border-color:var(--blue-100);">
                                    </div>
                                </div>

                                <div class="field" id="plazoWrap">
                                    <label class="label" for="plazo_meses">Número de meses</label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-calendar-range icon-left"></i>
                                        <select class="select" id="plazo_meses" name="plazo_meses" disabled>
                                            <option value="">Seleccionar...</option>
                                            @foreach ([3, 6, 9, 12, 18, 24] as $m)
                                                <option value="{{ $m }}">{{ $m }} meses
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="field" style="display:none">
                                    <label class="label" for="fecha_inicio">Fecha de inicio <span
                                            class="label__req">*</span></label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-calendar icon-left"></i>
                                        <input class="input" type="date" id="fecha_inicio" name="fecha_inicio"
                                            value="{{ date('Y-m-d') }}" style="font-family:var(--mono)">
                                    </div>
                                </div>

                                <div class="field" id="primerVencWrap" style="display:none">
                                    <label class="label" for="fecha_primer_vencimiento">Primer vencimiento</label>
                                    <div class="input-wrap">
                                        <i class="mdi mdi-calendar-check icon-left"></i>
                                        <input class="input" type="date" id="fecha_primer_vencimiento"
                                            name="fecha_primer_vencimiento" style="font-family:var(--mono)">
                                    </div>
                                </div>

                                <div class="field--full" id="planWrap" style="display:none;">
                                    <div class="divider" style="margin-bottom:0;">
                                        <div class="divider__line"></div>
                                        <span class="divider__label">Esquema de pagos</span>
                                        <div class="divider__line"></div>
                                    </div>
                                    <div class="plan-wrap">
                                        <table class="plan-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha de pago</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody id="planBody"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="field--full">
                                    <div class="divider" style="margin-bottom:12px;">
                                        <div class="divider__line"></div>
                                        <span class="divider__label">Resumen de solicitud</span>
                                        <div class="divider__line"></div>
                                    </div>
                                    <div class="summary-grid">
                                        <div class="summary-item">
                                            <div class="summary-item__label">Titular</div>
                                            <div class="summary-item__value" id="sum_nombre">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__label">Correo</div>
                                            <div class="summary-item__value" id="sum_correo">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__label">Ubicación</div>
                                            <div class="summary-item__value" id="sum_ubicacion">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__label">Tipo persona</div>
                                            <div class="summary-item__value" id="sum_tipo">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__label">Banco</div>
                                            <div class="summary-item__value" id="sum_banco">—</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-item__label">Modalidad</div>
                                            <div class="summary-item__value" id="sum_modal">—</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="field--full" id="previewDistWrap" style="display:none;">
                                    <div class="divider" style="margin-bottom:12px;">
                                        <div class="divider__line"></div>
                                        <span class="divider__label">Vista previa biometría</span>
                                        <div class="divider__line"></div>
                                    </div>
                                    <div class="plan-wrap">
                                        <table class="plan-table" id="preview_dist">
                                            <thead>
                                                <tr>
                                                    <th><i class="mdi mdi-account-box-outline"></i> Selfie</th>
                                                    <th><i class="mdi mdi-draw-pen"></i> Firma</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="pd_selfie"><span class="pd-empty">Sin captura</span></td>
                                                    <td id="pd_firma"><span class="pd-empty">Sin firma</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- ── Tarjeta de pago ── --}}
                                <div class="field--full">
                                    <div class="divider" style="margin-bottom:14px;">
                                        <div class="divider__line"></div>
                                        <span class="divider__label">Pago con tarjeta</span>
                                        <div class="divider__line"></div>
                                    </div>

                                    <div class="cc-scene">
                                        <div class="cc-card" id="ccCard">
                                            <div class="cc-card__front">
                                                <div class="cc-card__shine"></div>
                                                <div class="cc-card__top">
                                                    <div class="cc-card__chip">
                                                        <div class="cc-chip__inner"></div>
                                                    </div>
                                                    <div class="cc-card__brand" id="ccBrandIcon"><i
                                                            class="mdi mdi-credit-card-outline"></i></div>
                                                </div>
                                                <div class="cc-card__number" id="ccDisplayNumber">•••• &nbsp; ••••
                                                    &nbsp; •••• &nbsp; ••••</div>
                                                <div class="cc-card__bottom">
                                                    <div>
                                                        <div class="cc-card__label">Titular</div>
                                                        <div class="cc-card__value" id="ccDisplayName">NOMBRE EN LA
                                                            TARJETA</div>
                                                    </div>
                                                    <div>
                                                        <div class="cc-card__label">Vence</div>
                                                        <div class="cc-card__value" id="ccDisplayExp">MM/AA</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cc-card__back">
                                                <div class="cc-card__strip"></div>
                                                <div class="cc-card__cvv-area">
                                                    <div class="cc-card__cvv-label">CVV</div>
                                                    <div class="cc-card__cvv-box" id="ccDisplayCvv">•••</div>
                                                </div>
                                                <div class="cc-card__back-brand" id="ccBackBrand"><i
                                                        class="mdi mdi-credit-card-outline"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cc-fields">
                                        <div class="field field--full">
                                            <label class="label" for="cc_nombre">Nombre del titular <span
                                                    class="label__req">*</span></label>
                                            <div class="input-wrap">
                                                <i class="mdi mdi-account-outline icon-left"></i>
                                                <input class="input" type="text" id="cc_nombre" name="cc_nombre"
                                                    placeholder="Como aparece en la tarjeta" autocomplete="cc-name"
                                                    style="text-transform:uppercase">
                                                <i class="mdi mdi-check-circle icon-status"></i>
                                            </div>
                                        </div>
                                        <div class="field field--full">
                                            <label class="label">Datos de tarjeta <span
                                                    class="label__req">*</span></label>
                                            <div id="stripe-card-element" class="stripe-element-wrap"></div>
                                            <div id="stripe-card-errors"></div>
                                        </div>
                                    </div>

                                    <div class="cc-security">
                                        <span><i class="mdi mdi-shield-lock-outline"></i> SSL cifrado</span>
                                        <span><i class="mdi mdi-check-decagram-outline"></i> PCI DSS</span>
                                        <span><i class="mdi mdi-lock-check-outline"></i> 3D Secure</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>{{-- /card-body --}}

                    <div class="card-footer">
                        <button type="button" class="btn btn--ghost" id="btnPrev" disabled>
                            <i class="mdi mdi-arrow-left"></i> Anterior
                        </button>
                        <div style="display:flex;gap:10px;">
                            <button type="button" class="btn btn--primary" id="btnNext">
                                Siguiente <i class="mdi mdi-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn--success" id="btnSubmit" style="display:none;"
                                disabled>
                                <i class="mdi mdi-check-circle"></i> Enviar solicitud
                            </button>
                        </div>
                    </div>

                    <div class="success-screen" id="successScreen">
                        <div class="success-ring"><i class="mdi mdi-check"></i></div>
                        <h2 class="success-title">¡Tu solicitud fue recibida!</h2>
                        <p class="success-msg">Nos contactaremos contigo en un plazo de 24 a 48 horas.</p>
                        <div class="success-ref">
                            <i class="mdi mdi-identifier"></i>
                            <span id="successRef">DIST-XXXXXX-0</span>
                        </div>
                        <p class="success-note">Recibirás una confirmación en tu correo electrónico.</p>
                    </div>

                </div>{{-- /main-card --}}
            </form>
        </main>

    </div>{{-- /sol-layout --}}

    <div class="toast" id="toast">
        <i class="mdi" id="toastIcon"></i>
        <span id="toastMsg"></span>
    </div>

    <script>
        (function() {
            'use strict';

            const TOTAL = 6;
            let current = 1;
            let goingBack = false;

            const STRIPE_KEY = '{{ config('services.stripe.key') }}';
            let stripe, stripeCardElement, stripeCardComplete = false;

            const STEP_META = [{
                    tag: 'Paso 1 de 6',
                    icon: 'mdi-map-marker-outline',
                    title: 'Datos generales',
                    desc: 'Indica tu ubicación y dirección de operación.'
                },
                {
                    tag: 'Paso 2 de 6',
                    icon: 'mdi-account-outline',
                    title: 'Datos de Distribuidor',
                    desc: 'Perfil del titular y tipo de persona.'
                },
                {
                    tag: 'Paso 3 de 6',
                    icon: 'mdi-bank-outline',
                    title: 'Datos bancarios',
                    desc: 'Cuenta donde recibirás y realizarás pagos.'
                },
                {
                    tag: 'Paso 4 de 6',
                    icon: 'mdi-folder-open-outline',
                    title: 'Documentación',
                    desc: 'Sube los documentos solicitados en formato digital.'
                },
                {
                    tag: 'Paso 5 de 6',
                    icon: 'mdi-camera-account',
                    title: 'Biometría y contrato',
                    desc: 'Selfie, firma digital y revisión del contrato.'
                },
                {
                    tag: 'Paso 6 de 6',
                    icon: 'mdi-cash-multiple',
                    title: 'Plan de pago',
                    desc: 'Elige tu modalidad y revisa el esquema de pagos.'
                },
            ];

            /* ─── Helpers ─── */
            const $ = id => document.getElementById(id);
            const $$ = sel => [...document.querySelectorAll(sel)];
            const digits = v => String(v ?? '').replace(/\D/g, '');
            const money = n => new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(n);
            const validEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v).trim());

            function validClabe(v) {
                if (v.length !== 18) return false;
                const f = [3, 7, 1];
                let s = 0;
                for (let i = 0; i < 17; i++) s += (Number(v[i]) * f[i % 3]) % 10;
                return (10 - (s % 10)) % 10 === Number(v[17]);
            }

            function toast(msg, type = 'info', ms = 3200) {
                const el = $('toast'),
                    ic = $('toastIcon'),
                    tx = $('toastMsg');
                const icons = {
                    error: 'mdi-alert-circle',
                    success: 'mdi-check-circle',
                    info: 'mdi-information-outline'
                };
                el.className = `toast toast--${type}`;
                ic.className = `mdi ${icons[type]}`;
                tx.textContent = msg;
                el.classList.add('show');
                clearTimeout(el._t);
                el._t = setTimeout(() => el.classList.remove('show'), ms);
            }

            function setValidity(inp, ok, msgId, msg = '') {
                if (!inp) return;
                inp.classList.toggle('is-valid', ok && !!inp.value);
                inp.classList.toggle('is-invalid', !ok && !!inp.value);
                const el = $(msgId);
                if (el) {
                    el.textContent = msg;
                    el.className = `field-msg field-msg--${ok ? 'hint' : 'error'}`;
                }
            }

            /* ─── UI sync ─── */
            function syncUI() {
                const pct = Math.round((current / TOTAL) * 100);
                const meta = STEP_META[current - 1];
                $('progressFill').style.width = pct + '%';
                $('cardTag').innerHTML = `<i class="mdi ${meta.icon}"></i> ${meta.tag}`;
                $('cardTitle').textContent = meta.title;
                $('cardDesc').textContent = meta.desc;
                $('cardCounter').textContent = `${current} / ${TOTAL}`;
                $$('.step-item').forEach(el => {
                    const s = Number(el.dataset.step);
                    el.classList.toggle('is-active', s === current);
                    el.classList.toggle('is-done', s < current);
                });
                const fill = $('stepFill');
                if (fill) fill.style.height = `${((current - 1) / (TOTAL - 1)) * 100}%`;
                $('btnPrev').disabled = current === 1;
                $('btnNext').style.display = current === TOTAL ? 'none' : '';
                $('btnSubmit').style.display = current === TOTAL ? '' : 'none';
                if (current === TOTAL) updateSummary();
                if (current === 5) updateCaratulaPreview();
            }

            /* ─── Panel transitions ─── */
            function goStep(next) {
                if (next === current) return;
                goingBack = next < current;
                const leaving = document.querySelector(`[data-panel="${current}"]`);
                const entering = document.querySelector(`[data-panel="${next}"]`);
                if (!entering) return;
                leaving.classList.remove('is-active');
                setTimeout(() => {
                    entering.classList.toggle('slide-back', goingBack);
                    entering.classList.add('is-active');
                    setTimeout(() => entering.classList.remove('slide-back'), 450);
                    current = next;
                    syncUI();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 60);
            }

            /* ─── Validation per step ─── */
            function validateStep(s) {
                if (s === 1) {
                    for (const id of ['pais', 'region', 'municipio', 'calle', 'num_ext', 'cp', 'colonia']) {
                        const el = $(id);
                        if (!el?.value.trim()) {
                            toast('Completa el campo requerido.', 'error');
                            el?.focus();
                            return false;
                        }
                    }
                }
                if (s === 2) {
                    if (!$('nombre')?.value.trim()) {
                        toast('Escribe el nombre del titular.', 'error');
                        $('nombre').focus();
                        return false;
                    }
                    if (!validEmail($('correo')?.value || '')) {
                        toast('El correo no es válido.', 'error');
                        $('correo').focus();
                        return false;
                    }
                    if (digits($('telefono')?.value || '').length !== 10) {
                        toast('El teléfono debe tener 10 dígitos.', 'error');
                        $('telefono').focus();
                        return false;
                    }
                }
                if (s === 3) {
                    if (!$('banco')?.value) {
                        toast('Selecciona el banco.', 'error');
                        return false;
                    }
                    if (!$('titular_cuenta')?.value.trim()) {
                        toast('Escribe el titular de la cuenta.', 'error');
                        return false;
                    }
                    const c = digits($('clabe')?.value || '');
                    if (c && !validClabe(c)) {
                        toast('La CLABE interbancaria no es válida.', 'error');
                        $('clabe').focus();
                        return false;
                    }
                    if (!$('doc_caratula')?.files?.length) {
                        toast('Sube la carátula bancaria.', 'error');
                        return false;
                    }
                }
                if (s === 4) {
                    for (const [id, label] of [
                            ['doc_ine', 'INE/IFE'],
                            ['doc_domicilio', 'Comprobante de domicilio'],
                            ['doc_cedula', 'Cédula fiscal']
                        ]) {
                        if (!$(id)?.files?.length) {
                            toast(`Sube el documento: ${label}`, 'error');
                            return false;
                        }
                    }
                    if (document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL') {
                        if (!$('doc_acta')?.files?.length && !$('doc_poder')?.files?.length) {
                            toast('Para persona moral sube el acta constitutiva o el poder notarial.', 'error');
                            return false;
                        }
                    }
                }
                if (s === 5) {
                    if (!$('selfie_data')?.value) {
                        toast('Captura o sube una selfie del titular.', 'error');
                        return false;
                    }
                    if (!$('firma_data')?.value) {
                        toast('Dibuja la firma del titular.', 'error');
                        return false;
                    }
                    const benVal = $('beneficiario_directo_input')?.value;
                    if (!benVal) {
                        toast('Desplázate al Anexo 12 del contrato y selecciona si eres el beneficiario directo.',
                            'error', 5000);
                        return false;
                    }
                    if (benVal === 'NO') {
                        toast('Si existe otro beneficiario controlador, contacta a soporte antes de continuar.',
                            'error', 6000);
                        return false;
                    }
                    if (!$('acepta_terminos')?.checked) {
                        toast('Acepta los términos y condiciones.', 'error');
                        return false;
                    }
                }
                if (s === 6) {
                    if (!$('fecha_inicio')?.value) {
                        toast('Selecciona la fecha de inicio.', 'error');
                        return false;
                    }
                    const modal = document.querySelector('input[name=modalidad_pago]:checked')?.value;
                    if (modal !== 'CONTADO') {
                        if (!$('plazo_meses')?.value) {
                            toast('Selecciona el número de meses.', 'error');
                            return false;
                        }
                        if (!$('fecha_primer_vencimiento')?.value) {
                            toast('Indica la fecha del primer vencimiento.', 'error');
                            return false;
                        }
                    }
                    if (!$('cc_nombre')?.value.trim()) {
                        toast('Escribe el nombre del titular de la tarjeta.', 'error');
                        $('cc_nombre')?.focus();
                        return false;
                    }
                    if (!stripeCardComplete) {
                        toast('Ingresa los datos completos de tu tarjeta.', 'error');
                        return false;
                    }
                }
                return true;
            }

            /* ─── Live validation ─── */
            function bindLive() {
                $('correo')?.addEventListener('input', e => {
                    const ok = validEmail(e.target.value);
                    setValidity(e.target, ok, 'emailMsg', ok ? '' : 'Correo no válido');
                });
                $('telefono')?.addEventListener('input', e => {
                    e.target.value = digits(e.target.value).slice(0, 10);
                    const ok = e.target.value.length === 10;
                    setValidity(e.target, ok, 'telMsg', ok ? '' :
                        `${10 - e.target.value.length} dígitos restantes`);
                });
                $('clabe')?.addEventListener('input', e => {
                    e.target.value = digits(e.target.value).slice(0, 18);
                    const len = e.target.value.length;
                    if (len === 18) {
                        const ok = validClabe(e.target.value);
                        setValidity(e.target, ok, 'clabeMsg', ok ? '✓ CLABE válida' : 'CLABE inválida');
                    } else if (len > 0) {
                        setValidity(e.target, false, 'clabeMsg', `${18 - len} dígitos restantes`);
                    }
                });
                $('cp')?.addEventListener('input', e => {
                    e.target.value = digits(e.target.value).slice(0, 5);
                    if (e.target.value.length === 5) e.target.classList.add('is-valid');
                });
                $('rfc')?.addEventListener('input', e => {
                    e.target.value = e.target.value.toUpperCase();
                });
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

            /* ─── Tipo persona ─── */
            function bindTipo() {
                $$('input[name=tipo_persona]').forEach(r => {
                    r.addEventListener('change', () => {
                        const moral = r.value === 'MORAL';
                        $('moralSection')?.classList.toggle('open', moral);
                        const razonWrap = $('razonWrap');
                        const razon = $('razon_social');
                        if (razonWrap) razonWrap.style.display = moral ? '' : 'none';
                        if (razon) razon.required = moral;
                    });
                });
            }

            /* ─── File zones ─── */
            function bindFiles() {
                $$('.file-zone').forEach(zone => {
                    const input = zone.querySelector('input[type=file]');
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
                    zone.addEventListener('dragover', e => {
                        e.preventDefault();
                        zone.classList.add('dragover');
                    });
                    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
                    zone.addEventListener('drop', e => {
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

            /* ─── Modalidad ─── */
            function bindModalidad() {
                $$('input[name=modalidad_pago]').forEach(r => {
                    r.addEventListener('change', () => {
                        const dif = r.value !== 'CONTADO';
                        const plazo = $('plazo_meses');
                        if (plazo) plazo.disabled = !dif;
                        if (!dif) {
                            if (plazo) plazo.value = '';
                        }
                        buildPlan();
                    });
                });
                [$('plazo_meses'), $('fecha_primer_vencimiento')].forEach(el => el?.addEventListener('change',
                    buildPlan));
            }

            function buildPlan() {
                const modal = document.querySelector('input[name=modalidad_pago]:checked')?.value;
                const plazo = parseInt($('plazo_meses')?.value || '0');
                const pv = $('fecha_primer_vencimiento')?.value;
                const wrap = $('planWrap');
                const body = $('planBody');
                if (!body) return;
                if (modal === 'CONTADO' || !plazo || !pv) {
                    if (wrap) wrap.style.display = 'none';
                    return;
                }
                const total = 20000;
                const mBase = Math.floor((total / plazo) * 100) / 100;
                let acum = 0,
                    html = '';
                const base = new Date(pv + 'T12:00:00');
                for (let i = 0; i < plazo; i++) {
                    const d = new Date(base);
                    d.setMonth(base.getMonth() + i);
                    const fecha =
                        `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                    let m = mBase;
                    acum += m;
                    if (i === plazo - 1) m += +(total - acum).toFixed(2);
                    html += `<tr><td>${i+1}</td><td>${fecha}</td><td>${money(m)}</td></tr>`;
                }
                body.innerHTML = html;
                if (wrap) wrap.style.display = '';
            }

            /* ─── Summary ─── */
            function updateSummary() {
                const g = id => $(id)?.value?.trim() || '—';
                const tipo = document.querySelector('input[name=tipo_persona]:checked')?.value;
                const modal = document.querySelector('input[name=modalidad_pago]:checked')?.value;
                $('sum_nombre').textContent = g('nombre');
                $('sum_correo').textContent = g('correo');
                $('sum_ubicacion').textContent = [g('municipio'), g('region')].filter(v => v !== '—').join(', ');
                $('sum_tipo').textContent = tipo === 'MORAL' ? 'Persona Moral' : 'Persona Física';
                $('sum_banco').textContent = g('banco');
                $('sum_modal').textContent = modal === 'CONTADO' ?
                    `Contado · ${money(20000)}` :
                    `${g('plazo_meses')} meses · ${money(20000 / Number(g('plazo_meses') || 1))}/mes`;
                buildPlan();
                updatePreviewDist();
            }

            /* ─── Preview Carátula Apartados 5-9 ─── */
            function updateCaratulaPreview() {
                const moral = document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL';
                const nombre = moral ?
                    ($('razon_social')?.value.trim() || '—') :
                    ($('nombre')?.value.trim() || '—');

                const calle = $('calle')?.value.trim() || '';
                const numExt = $('num_ext')?.value.trim() || '';
                const numInt = $('num_int')?.value.trim() || '';
                const colonia = $('colonia')?.value.trim() || '';
                const cp = $('cp')?.value.trim() || '';
                const municipio = $('municipio')?.value.trim() || '';
                const regionEl = $('region');
                const estadoIdx = regionEl ? regionEl.selectedIndex : -1;
                const estadoText = (estadoIdx > 0 ? regionEl.options[estadoIdx]?.text : '') || '';

                let domicilio = calle;
                if (numExt) domicilio += ' ' + numExt;
                if (numInt) domicilio += ' Int. ' + numInt;
                if (colonia) domicilio += ', Col. ' + colonia;
                if (cp) domicilio += ', C.P. ' + cp;
                if (municipio) domicilio += ', ' + municipio;
                if (estadoText) domicilio += ', ' + estadoText;

                const rfc = $('rfc')?.value.trim() || '';
                const telefono = $('telefono')?.value.trim() || '';
                const correo = $('correo')?.value.trim() || '';
                const paisEl = $('pais');
                const paisIdx = paisEl ? paisEl.selectedIndex : -1;
                const paisText = (paisIdx > 0 ? paisEl.options[paisIdx]?.text : '') || '';

                let demarcacion = municipio || '';
                if (estadoText) demarcacion += (demarcacion ? ', ' : '') + estadoText;

                const set = (id, val) => { const el = $(id); if (el) el.textContent = val || '—'; };
                set('prev_apt5', nombre);
                set('prev_apt6', domicilio || '—');
                set('prev_apt7', rfc || '—');
                set('prev_apt8', nombre);
                set('prev_apt9', demarcacion || '—');

                // Guardar en sessionStorage para que el iframe los lea
                try {
                    sessionStorage.setItem('pats_caratula', JSON.stringify({
                        nombre,
                        domicilio: domicilio || '',
                        rfc,
                        demarcacion: demarcacion || '',
                        telefono,
                        correo,
                        pais: paisText,
                    }));
                } catch (_) {}
            }

            /* ─── Overlay firma en contrato + recarga iframe con params ─── */
            function updateContratoOverlay() {
                const firmaData = $('firma_data')?.value;
                const overlay = $('contrato_firma_overlay');
                const imgEl = $('overlay_firma_img');
                const nomEl = $('overlay_nombre_dist');
                const hint = $('firmaHint');

                if (!overlay) return;

                if (!firmaData) {
                    overlay.style.display = 'none';
                    if (hint) hint.classList.remove('hidden');
                    return;
                }

                // Ocultar aviso de firma pendiente
                if (hint) hint.classList.add('hidden');

                // Fecha actual en español
                const hoy = new Date();
                const dia = hoy.getDate();
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre',
                    'octubre', 'noviembre', 'diciembre'
                ];
                const mes = meses[hoy.getMonth()];
                const anio = hoy.getFullYear();

                const overlayDia = $('overlay_dia');
                const overlayMes = $('overlay_mes');
                const overlayAnio = $('overlay_anio');
                if (overlayDia) overlayDia.textContent = dia;
                if (overlayMes) overlayMes.textContent = mes;
                if (overlayAnio) overlayAnio.textContent = anio;

                // Nombre del distribuidor
                const moral = document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL';
                const nombreVal = moral ?
                    ($('razon_social')?.value.trim() || '—') :
                    ($('nombre')?.value.trim() || '—');
                if (nomEl) nomEl.textContent = nombreVal;

                // Renderizar firma en el overlay
                if (imgEl) {
                    imgEl.src = firmaData;
                    imgEl.style.display = '';
                }

                overlay.style.display = '';

                // ── Recargar el iframe con firma y nombre como query params ──
                // El contrato es mismo origen (Laravel), así que los params llegan sin restricciones CORS.
                const iframe = $('contratoIframe');
                if (iframe) {
                    const base = iframe.src.split('?')[0];
                    const qs = new URLSearchParams({
                        firma: firmaData,
                        nombre: nombreVal,
                    });
                    // La firma es un dataURL grande; algunos navegadores tienen límite en la URL.
                    // Usamos sessionStorage como canal alternativo más seguro.
                    try {
                        sessionStorage.setItem('pats_firma', firmaData);
                        sessionStorage.setItem('pats_nombre', nombreVal);
                        updateCaratulaPreview(); // también refresca pats_caratula antes de recargar iframe
                        iframe.src = base + '?ts=' + Date.now(); // fuerza recarga sin firma en URL
                    } catch (_) {
                        iframe.src = base + '?' + qs.toString();
                    }
                }
            }

            /* ─── Preview dist ─── */
            function updatePreviewDist() {
                const selfie = $('selfie_data')?.value;
                const firma = $('firma_data')?.value;
                const wrap = $('previewDistWrap');
                const pdSelfie = $('pd_selfie');
                const pdFirma = $('pd_firma');
                if (wrap) wrap.style.display = (selfie || firma) ? '' : 'none';
                if (pdSelfie) pdSelfie.innerHTML = selfie ? `<img src="${selfie}" class="pd-img" alt="Selfie">` :
                    '<span class="pd-empty">Sin captura</span>';
                if (pdFirma) pdFirma.innerHTML = firma ?
                    `<img src="${firma}"  class="pd-img" alt="Firma" style="background:#fff;">` :
                    '<span class="pd-empty">Sin firma</span>';
            }

            /* ─── Nombre live en contrato ─── */
            function bindContractName() {
                const nombreEl = $('nombre');
                const razonEl = $('razon_social');
                const liveEl = $('contrato_nombre_live');
                if (!liveEl) return;

                function update() {
                    const moral = document.querySelector('input[name=tipo_persona]:checked')?.value === 'MORAL';
                    const val = moral ? razonEl?.value.trim() : nombreEl?.value.trim();
                    liveEl.textContent = val || '—';
                    updateContratoOverlay();
                }

                nombreEl?.addEventListener('input', update);
                razonEl?.addEventListener('input', update);
                document.querySelectorAll('input[name=tipo_persona]').forEach(r => r.addEventListener('change',
                    update));
                update();
            }

            /* ─── Submit ─── */
            function bindSubmit() {
                @php
                    $intentUrl = route('dist.stripe.intent'); // ← distribución $20K
                    $submitUrl = route('dist.publico.guardar');
                    $preValidarUrl = route('dist.publico.pre-validar');
                @endphp

                let confirmedPaymentId = null;
                const intentKey = (typeof crypto !== 'undefined' && crypto.randomUUID) ?
                    crypto.randomUUID() :
                    (Date.now().toString(36) + Math.random().toString(36).slice(2));

                $('frmDist')?.addEventListener('submit', async e => {
                    e.preventDefault();
                    if (!validateStep(TOTAL)) return;
                    const btn = $('btnSubmit');
                    const csrf = document.querySelector('meta[name=csrf-token]').content;
                    btn.disabled = true;

                    try {
                        const modalidad = document.querySelector('input[name=modalidad_pago]:checked')
                            ?.value || 'CONTADO';
                        const plazoMeses = parseInt(document.getElementById('plazo_meses')?.value || '0');

                        if (!confirmedPaymentId) {
                            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Verificando datos...';
                            const fdPre = new FormData(e.target);
                            const preRes = await fetch('{{ $preValidarUrl }}', {
                                method: 'POST',
                                body: fdPre,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf
                                },
                            });
                            const preData = await preRes.json().catch(() => ({}));
                            if (!preRes.ok || preData.ok === false) throw new Error(preData.error ||
                                'Error en la validación de datos.');

                            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Iniciando pago...';
                            const intentRes = await fetch('{{ $intentUrl }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    correo: document.getElementById('correo')?.value || '',
                                    nombre: document.getElementById('nombre')?.value || '',
                                    modalidad_pago: modalidad,
                                    plazo_meses: plazoMeses,
                                    intent_key: intentKey,
                                }),
                            });
                            const intentData = await intentRes.json().catch(() => ({}));
                            if (!intentData.ok) throw new Error(intentData.error ||
                                'No fue posible iniciar el pago.');

                            const confirmOpts = {
                                payment_method: {
                                    card: stripeCardElement,
                                    billing_details: {
                                        name: document.getElementById('cc_nombre')?.value || ''
                                    },
                                },
                            };
                            if (modalidad === 'DIFERIDO' && plazoMeses > 0) {
                                confirmOpts.payment_method_options = {
                                    card: {
                                        installments: {
                                            plan: {
                                                count: plazoMeses,
                                                interval: 'month',
                                                type: 'fixed_count'
                                            }
                                        }
                                    },
                                };
                            }

                            if (intentData.split) {
                                btn.innerHTML =
                                    '<i class="mdi mdi-loading mdi-spin"></i> Procesando cobro 1 de 2...';
                                const {
                                    paymentIntent: pi1,
                                    error: err1
                                } = await stripe.confirmCardPayment(intentData.client_secret_1,
                                    confirmOpts);
                                if (err1) throw new Error(err1.message ||
                                    'Primer cobro rechazado por el banco.');
                                if (pi1.status !== 'succeeded') throw new Error(
                                    'El primer cobro no fue confirmado.');

                                btn.innerHTML =
                                    '<i class="mdi mdi-loading mdi-spin"></i> Procesando cobro 2 de 2...';
                                const {
                                    paymentIntent: pi2,
                                    error: err2
                                } = await stripe.confirmCardPayment(intentData.client_secret_2,
                                    confirmOpts);
                                if (err2) throw new Error('El primer cobro fue exitoso. ' + (err2.message ||
                                    'El segundo cobro falló — contacta a soporte.'));
                                if (pi2.status !== 'succeeded') throw new Error(
                                    'El segundo cobro no fue confirmado — contacta a soporte.');
                                confirmedPaymentId = pi1.id + '|' + pi2.id;
                            } else {
                                btn.innerHTML = modalidad === 'DIFERIDO' ?
                                    `<i class="mdi mdi-loading mdi-spin"></i> Procesando ${plazoMeses} MSI...` :
                                    '<i class="mdi mdi-loading mdi-spin"></i> Procesando pago...';
                                const {
                                    paymentIntent,
                                    error
                                } = await stripe.confirmCardPayment(intentData.client_secret, confirmOpts);
                                if (error) throw new Error(error.message || 'Pago rechazado por el banco.');
                                if (paymentIntent.status !== 'succeeded') throw new Error(
                                    'El pago no fue confirmado.');
                                confirmedPaymentId = paymentIntent.id;
                            }
                        } else {
                            btn.innerHTML =
                                '<i class="mdi mdi-loading mdi-spin"></i> Reintentando envío...';
                        }

                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Guardando solicitud...';
                        const fd = new FormData(e.target);
                        fd.append('stripe_payment_intent_id', confirmedPaymentId);
                        const res = await fetch('{{ $submitUrl }}', {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrf
                            },
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || data.ok === false) throw new Error(data.error ||
                            'Error al guardar la solicitud.');

                        const params = new URLSearchParams({
                            ref: data.referencia || '',
                            nombre: $('nombre')?.value.trim() || '',
                            correo: $('correo')?.value.trim() || '',
                        });
                        window.location.href = '{{ route('franq.publico.confirmacion') }}?' + params
                            .toString();

                    } catch (err) {
                        toast(err.message || 'No fue posible enviar la solicitud.', 'error', 5000);
                        btn.disabled = false;
                        btn.innerHTML = confirmedPaymentId ?
                            '<i class="mdi mdi-check-circle"></i> Reintentar envío (pago ya realizado)' :
                            '<i class="mdi mdi-check-circle"></i> Enviar solicitud';
                    }
                });
            }

            /* ─── Nav ─── */
            function bindNav() {
                $('btnNext')?.addEventListener('click', () => {
                    if (!validateStep(current)) return;
                    if (current < TOTAL) goStep(current + 1);
                });
                $('btnPrev')?.addEventListener('click', () => {
                    if (current > 1) goStep(current - 1);
                });
                $$('.step-item').forEach(el => {
                    el.addEventListener('click', () => {
                        const s = Number(el.dataset.step);
                        if (s < current) goStep(s);
                    });
                });
            }

            /* ─── Selfie ─── */
            let selfieStream = null;

            function bindSelfie() {
                const video = $('selfieVideo');
                const canvas = $('selfieCanvas');
                const captura = $('selfieCaptura');
                const placeholder = $('selfiePlaceholder');
                const btnStart = $('btnStartCamera');
                const btnTake = $('btnTakePhoto');
                const btnRetake = $('btnRetakePhoto');
                const dataInput = $('selfie_data');

                btnStart?.addEventListener('click', async () => {
                    try {
                        selfieStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: {
                                    ideal: 640
                                },
                                height: {
                                    ideal: 480
                                }
                            }
                        });
                        if (video) {
                            video.srcObject = selfieStream;
                            video.style.display = '';
                        }
                        if (placeholder) placeholder.style.display = 'none';
                        if (captura) captura.style.display = 'none';
                        btnStart.style.display = 'none';
                        if (btnTake) btnTake.style.display = '';
                    } catch {
                        toast('No se pudo acceder a la cámara. Verifica los permisos del dispositivo.',
                            'error');
                    }
                });

                btnTake?.addEventListener('click', () => {
                    if (!video || !canvas) return;
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.save();
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                    ctx.restore();
                    const data = canvas.toDataURL('image/jpeg', 0.85);
                    if (dataInput) dataInput.value = data;
                    if (captura) {
                        captura.src = data;
                        captura.style.display = '';
                    }
                    if (video) video.style.display = 'none';
                    btnTake.style.display = 'none';
                    if (btnRetake) btnRetake.style.display = '';
                    selfieStream?.getTracks().forEach(t => t.stop());
                    selfieStream = null;
                    updatePreviewDist();
                });

                btnRetake?.addEventListener('click', () => {
                    if (dataInput) dataInput.value = '';
                    if (captura) {
                        captura.src = '';
                        captura.style.display = 'none';
                    }
                    btnRetake.style.display = 'none';
                    if (placeholder) placeholder.style.display = '';
                    if (btnStart) btnStart.style.display = '';
                    updatePreviewDist();
                });
            }

            /* ─── Signature ─── */
            function bindSignature() {
                const wrap = $('firmaWrap');
                const canvas = $('firmaCanvas');
                const dataInput = $('firma_data');
                const btnClear = $('btnClearFirma');
                if (!canvas) return;

                canvas.width = 600;
                canvas.height = 180;

                let drawing = false;
                let hasSigned = false;

                function getPos(e) {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;
                    const src = e.touches ? e.touches[0] : e;
                    return {
                        x: (src.clientX - rect.left) * scaleX,
                        y: (src.clientY - rect.top) * scaleY
                    };
                }

                function startDraw(e) {
                    e.preventDefault();
                    drawing = true;
                    const pos = getPos(e);
                    const ctx = canvas.getContext('2d');
                    ctx.beginPath();
                    ctx.moveTo(pos.x, pos.y);
                }

                function draw(e) {
                    e.preventDefault();
                    if (!drawing) return;
                    const pos = getPos(e);
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#1e293b';
                    ctx.lineWidth = 2.5;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    ctx.lineTo(pos.x, pos.y);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(pos.x, pos.y);
                    if (!hasSigned) {
                        hasSigned = true;
                        wrap?.classList.add('signed');
                    }
                }

                function endDraw(e) {
                    e.preventDefault();
                    if (!drawing) return;
                    drawing = false;
                    if (hasSigned) {
                        if (dataInput) dataInput.value = canvas.toDataURL('image/png');
                        updatePreviewDist();
                        updateContratoOverlay(); // ← Actualiza el overlay en cuanto termina de firmar
                    }
                }

                canvas.addEventListener('mousedown', startDraw);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', endDraw);
                canvas.addEventListener('mouseleave', endDraw);
                canvas.addEventListener('touchstart', startDraw, {
                    passive: false
                });
                canvas.addEventListener('touchmove', draw, {
                    passive: false
                });
                canvas.addEventListener('touchend', endDraw, {
                    passive: false
                });

                btnClear?.addEventListener('click', () => {
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    hasSigned = false;
                    drawing = false;
                    if (dataInput) dataInput.value = '';
                    wrap?.classList.remove('signed');
                    updatePreviewDist();
                    updateContratoOverlay(); // ← Oculta overlay si borra la firma
                });
            }

            /* ─── Beneficiario Directo — postMessage desde iframe ─── */
            function bindBeneficiarioListener() {
                const input = $('beneficiario_directo_input');
                const statusWrap = $('ben_status_wrap');
                const statusTxt = $('ben_status_txt');

                window.addEventListener('message', function(event) {
                    // Solo aceptar mensajes del mismo origen
                    if (event.origin !== window.location.origin) return;
                    const d = event.data;
                    if (!d || d.type !== 'pats_beneficiario') return;

                    const val = d.valor; // 'SI' o 'NO'

                    // Guardar en hidden input para el submit
                    if (input) input.value = val;

                    // Guardar en sessionStorage para que el contrato lo pre-seleccione si se recarga
                    try {
                        sessionStorage.setItem('pats_beneficiario', val);
                    } catch (_) {}

                    // Actualizar indicador visual en el formulario padre
                    if (statusWrap && statusTxt) {
                        if (val === 'SI') {
                            statusWrap.style.borderColor = '#10b981';
                            statusWrap.style.background = '#ecfdf5';
                            statusWrap.style.color = '#065f46';
                            statusWrap.querySelector('i').style.color = '#10b981';
                            statusTxt.innerHTML =
                                '<strong>✓ Declaración confirmada</strong> — eres el beneficiario directo y único de esta operación.';
                        } else {
                            statusWrap.style.borderColor = '#ef4444';
                            statusWrap.style.background = '#fff1f2';
                            statusWrap.style.color = '#991b1b';
                            statusWrap.querySelector('i').style.color = '#ef4444';
                            statusTxt.innerHTML =
                                '<strong>⚠ Indicaste que existe otro beneficiario controlador</strong> — contacta a soporte antes de continuar.';
                        }
                    }
                });
            }

            /* ─── Stripe Card ─── */
            function bindCard() {
                const nomEl = document.getElementById('cc_nombre');
                const dispNom = document.getElementById('ccDisplayName');
                nomEl?.addEventListener('input', () => {
                    nomEl.value = nomEl.value.toUpperCase();
                    if (dispNom) dispNom.textContent = nomEl.value.trim() || 'NOMBRE EN LA TARJETA';
                });
                if (!window.Stripe) return;
                stripe = Stripe(STRIPE_KEY);
                const elements = stripe.elements({
                    fonts: [{
                        cssSrc: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500&display=swap'
                    }],
                });
                stripeCardElement = elements.create('card', {
                    style: {
                        base: {
                            fontFamily: '"Plus Jakarta Sans", sans-serif',
                            fontSize: '15px',
                            color: '#1e293b',
                            '::placeholder': {
                                color: '#94a3b8'
                            }
                        },
                        invalid: {
                            color: '#ef4444',
                            iconColor: '#ef4444'
                        },
                    },
                    hidePostalCode: true,
                });
                stripeCardElement.mount('#stripe-card-element');
                stripeCardElement.on('change', e => {
                    stripeCardComplete = e.complete;
                    const errDiv = document.getElementById('stripe-card-errors');
                    if (errDiv) {
                        errDiv.textContent = e.error ? e.error.message : '';
                        errDiv.style.display = e.error ? '' : 'none';
                    }
                });
            }

            /* ─── Init ─── */
            document.addEventListener('DOMContentLoaded', () => {
                syncUI();
                bindLive();
                bindTipo();
                bindFiles();
                bindModalidad();
                bindSubmit();
                bindNav();
                bindCard();
                bindSelfie();
                bindSignature();
                bindContractName();
                bindBeneficiarioListener();

                const pv = $('fecha_primer_vencimiento');
                if (pv) {
                    const d = new Date();
                    d.setFullYear(d.getFullYear() + 3);
                    pv.value = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                }
            });

        })();
    </script>
</body>

</html>
