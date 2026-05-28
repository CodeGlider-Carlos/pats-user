{{-- resources/views/servicios/rayos.blade.php --}}
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Imagenología')

@section('content')
    <style>
        :root {
            --navy: #f0f4fb;
            --navy-light: #e2eaf6;
            --blue: #2563eb;
            --blue-light: #60a5fa;
            --cream: #1e3a5f;
            --cream-dark: #2d4a6e;
            --text: #1e3a5f;
            --text-muted: #6b85a8;
            --white: #ffffff;
            --success: #0e7a5f;
            --danger: #dc2626;
            --warning: #d97706;
            --border: rgba(37, 99, 235, .12);
            --radius: 14px;
            --radius-sm: 9px;
            --radius-lg: 20px;
            --shadow: 0 4px 24px rgba(37, 99, 235, .08);
            --shadow-sm: 0 2px 8px rgba(37, 99, 235, .06);
        }

        .rx-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 28px 80px;
            overflow-x: hidden;
        }

        /* ── Header ─────────────────────────────────── */
        .rx-header {
            padding: 48px 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 32px;
        }

        .rx-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 8px;
        }

        .rx-title {
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: var(--cream);
            letter-spacing: -.02em;
            margin: 0 0 6px;
        }

        .rx-title span {
            color: var(--blue);
        }

        .rx-subtitle {
            font-size: 14.5px;
            color: var(--text-muted);
            margin: 0;
        }

        .rx-fecha-badge {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 20px;
            text-align: right;
            box-shadow: var(--shadow);
        }

        .rx-fecha-badge__dia {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            font-weight: 700;
        }

        .rx-fecha-badge__full {
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            margin-top: 2px;
        }

        .rx-fecha-badge__hora {
            font-size: 22px;
            font-weight: 800;
            color: var(--blue);
            font-variant-numeric: tabular-nums;
        }

        /* ── Stats ──────────────────────────────────── */
        .rx-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .rx-stat {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 24px;
            flex: 1;
            min-width: 150px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .rx-stat__icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .rx-stat__icon--scan {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .rx-stat__icon--disp {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .rx-stat__icon--hoy {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .rx-stat__icon--rec {
            background: rgba(96, 165, 250, .12);
            color: var(--blue-light);
        }

        .rx-stat__num {
            font-size: 30px;
            font-weight: 800;
            color: var(--cream);
            line-height: 1;
        }

        .rx-stat__label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 4px;
        }

        /* ── Buscador ───────────────────────────────── */
        .rx-search-wrap {
            position: relative;
            margin-bottom: 24px;
            box-sizing: border-box;
            width: 100%;
        }

        .rx-search {
            width: 100%;
            box-sizing: border-box;
            padding: 13px 16px 13px 48px;
            font-size: 15px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--text);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
            box-shadow: var(--shadow-sm);
        }

        .rx-search:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }

        .rx-search::placeholder {
            color: var(--text-muted);
        }

        .rx-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 20px;
            pointer-events: none;
        }

        .rx-search-clear {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--navy);
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: background .15s;
        }

        .rx-search-clear.visible {
            display: flex;
        }

        .rx-search-clear:hover {
            background: var(--navy-light);
            color: var(--blue);
        }

        /* ── Tabs ───────────────────────────────────── */
        .rx-tabs-wrap {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .rx-tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border);
            background: var(--navy-light);
        }

        .rx-tab-btn {
            flex: 1;
            padding: 16px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .18s;
        }

        .rx-tab-btn i {
            font-size: 18px;
        }

        .rx-tab-btn:hover {
            color: var(--blue);
        }

        .rx-tab-btn.is-active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            background: var(--white);
        }

        .rx-tab-btn .rx-tab-count {
            font-size: 11px;
            font-weight: 700;
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
            padding: 2px 8px;
            border-radius: 100px;
        }

        .rx-tab-btn.is-active .rx-tab-count {
            background: var(--blue);
            color: #fff;
        }

        /* Panel de cada tab */
        .rx-tab-panel {
            display: none;
            padding: 20px 24px;
        }

        .rx-tab-panel.is-active {
            display: block;
        }

        /* ── Layout ─────────────────────────────────── */
        .rx-layout {
            display: grid;
            /* grid-template-columns: 1fr 320px; */
            gap: 24px;
            align-items: start;
        }

        /* ── Card base ──────────────────────────────── */
        .rx-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .rx-card__head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rx-card__head-icon {
            color: var(--blue);
            font-size: 22px;
        }

        .rx-card__head-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
        }

        .rx-card__head-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .rx-card__body {
            padding: 20px 22px;
        }

        /* ── Recursos de imagenología ───────────────── */
        .rx-recurso {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 12px;
            transition: border-color .18s, background .18s;
        }

        .rx-recurso:last-child {
            margin-bottom: 0;
        }

        .rx-recurso:hover {
            border-color: rgba(37, 99, 235, .30);
            background: var(--navy);
        }

        .rx-recurso__avatar {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(37, 99, 235, .08);
            color: var(--blue);
        }

        .rx-recurso__nombre {
            font-size: 15px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 3px;
        }

        .rx-recurso__tipo {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rx-recurso__tipo i {
            color: var(--blue-light);
            font-size: 14px;
        }

        .rx-recurso__btn {
            margin-left: auto;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: var(--blue);
            color: #fff;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s, transform .15s;
        }

        .rx-recurso__btn:hover {
            background: #1d52d4;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(37, 99, 235, .28);
        }

        /* ── Agenda del día (timeline) ──────────────── */
        .rx-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .rx-bloque {
            display: flex;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .rx-bloque:last-child {
            border-bottom: none;
        }

        .rx-bloque__time {
            min-width: 56px;
            text-align: right;
            flex-shrink: 0;
            padding-top: 2px;
        }

        .rx-bloque__hora-ini {
            font-size: 13px;
            font-weight: 800;
            color: var(--blue);
        }

        .rx-bloque__hora-fin {
            font-size: 10.5px;
            color: var(--text-muted);
        }

        .rx-bloque__line {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20px;
            flex-shrink: 0;
            position: relative;
        }

        .rx-bloque__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid currentColor;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .rx-bloque__dot--disp {
            color: #0e7a5f;
            background: rgba(14, 122, 95, .15);
        }

        .rx-bloque__dot--res {
            color: var(--blue);
            background: rgba(37, 99, 235, .15);
        }

        .rx-bloque__dot--bloq {
            color: var(--warning);
            background: rgba(217, 119, 6, .15);
        }

        .rx-bloque__dot--mant {
            color: var(--danger);
            background: rgba(220, 38, 38, .12);
        }

        .rx-bloque__line::after {
            content: '';
            flex: 1;
            width: 1.5px;
            background: var(--border);
            margin-top: 4px;
        }

        .rx-bloque:last-child .rx-bloque__line::after {
            display: none;
        }

        .rx-bloque__body {
            flex: 1;
            min-width: 0;
        }

        .rx-bloque__recurso {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 3px;
        }

        .rx-bloque__dur {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .rx-bloque__dur i {
            font-size: 13px;
            color: var(--blue-light);
        }

        .rx-bloque__tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
            margin-top: 5px;
        }

        .rx-bloque__tag--disp {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .rx-bloque__tag--res {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .rx-bloque__tag--bloq {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .rx-bloque__tag--mant {
            background: rgba(220, 38, 38, .10);
            color: var(--danger);
        }

        /* ── Disponibilidad próximos días ───────────── */
        .rx-fecha-group {
            margin-bottom: 18px;
        }

        .rx-fecha-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rx-fecha-label i {
            color: var(--blue);
            font-size: 15px;
        }

        .rx-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .rx-slot {
            padding: 9px 14px;
            background: var(--navy);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            text-align: center;
            cursor: default;
            transition: border-color .15s, background .15s;
        }

        .rx-slot:hover {
            border-color: var(--blue);
            background: rgba(37, 99, 235, .04);
        }

        .rx-slot__hora {
            display: block;
        }

        .rx-slot__rec {
            display: block;
            font-size: 10.5px;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── Panel derecho ──────────────────────────── */
        .rx-panel {
            position: sticky;
            top: 24px;
        }

        .rx-panel-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
        }

        .rx-panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rx-panel-head__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
        }

        .rx-panel-head__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .rx-panel-body {
            padding: 14px 18px;
            max-height: 420px;
            overflow-y: auto;
        }

        .rx-cita-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 8px;
            transition: background .12s;
        }

        .rx-cita-row:last-child {
            margin-bottom: 0;
        }

        .rx-cita-row:hover {
            background: var(--navy);
        }

        .rx-cita-row__hora {
            font-weight: 800;
            color: var(--blue);
            font-size: 13px;
            min-width: 44px;
        }

        .rx-cita-row__pac {
            font-weight: 600;
            color: var(--cream);
            flex: 1;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rx-cita-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 100px;
            flex-shrink: 0;
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .rx-cita-badge--conf {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .rx-cita-badge--proc {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .rx-empty {
            padding: 32px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13.5px;
        }

        .rx-empty i {
            font-size: 36px;
            display: block;
            margin-bottom: 8px;
            opacity: .3;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width:960px) {
            .rx-layout {
                grid-template-columns: 1fr;
            }

            .rx-panel {
                position: static;
            }
        }

        @media (max-width:600px) {
            .rx-wrap {
                padding: 0 12px 60px;
            }

            .rx-header {
                padding: 28px 0 22px;
            }

            .rx-stats {
                flex-direction: column;
            }

            .rx-tab-panel {
                padding: 14px 10px;
            }

            .rx-accordion-btn {
                padding: 11px 12px;
                font-size: 13px;
            }

            .rx-accordion-btn__left {
                flex: 1;
                min-width: 0;
                overflow: hidden;
            }
        }

        /* ── Acordeón por proveedor ──────────────────── */
        .rx-accordion-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 18px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--cream);
            transition: background .15s, border-color .15s;
            box-shadow: var(--shadow-sm);
            text-align: left;
            margin-bottom: 0;
        }

        .rx-accordion-btn:hover {
            background: var(--navy);
            border-color: rgba(37, 99, 235, .28);
        }

        .rx-accordion-btn.is-open {
            border-color: var(--blue);
            background: rgba(37, 99, 235, .04);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .rx-accordion-btn__left {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .rx-accordion-btn__left i {
            color: var(--blue);
            font-size: 18px;
        }

        .rx-accordion-btn__right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .rx-accordion-chevron {
            font-size: 20px;
            color: var(--text-muted);
            transition: transform .22s ease;
        }

        .rx-accordion-btn.is-open .rx-accordion-chevron {
            transform: rotate(180deg);
            color: var(--blue);
        }

        .rx-accordion-panel {
            border: 1.5px solid var(--blue);
            border-top: none;
            border-bottom-left-radius: var(--radius-sm);
            border-bottom-right-radius: var(--radius-sm);
            overflow: hidden;
            display: none;
            margin-bottom: 10px;
        }

        .rx-accordion-panel.is-open {
            display: block;
        }

        .rx-proveedor-group {
            margin-bottom: 10px;
        }

        /* ── Cabecera de precios ─────────────────── */
        .rx-price-header {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            background: var(--navy-light);
            border-bottom: 1px solid var(--border);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-muted);
        }

        .rx-price-header__name { flex: 1; }
        .rx-price-header__price { width: 110px; text-align: center; }

        /* ── Fila estudio ─────────────────────── */
        .rx-estudio {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 11px 16px;
            transition: background .12s;
        }

        .rx-estudio:last-child { border-bottom: none; }
        .rx-estudio:hover { background: var(--navy); }

        .rx-estudio__icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            background: rgba(37, 99, 235, .08);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 16px;
        }

        .rx-estudio__body {
            flex: 1;
            min-width: 0;
        }

        .rx-estudio__nombre {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
        }

        .rx-price-cols {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .rx-price-tag {
            width: 100px;
            text-align: center;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1px;
        }

        .rx-price-tag__label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .rx-price-tag__val {
            font-size: 13.5px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .rx-price-tag--pats {
            background: rgba(14, 122, 95, .09);
        }
        .rx-price-tag--pats .rx-price-tag__label,
        .rx-price-tag--pats .rx-price-tag__val { color: #0e7a5f; }

        .rx-price-tag--pub {
            background: rgba(37, 99, 235, .08);
        }
        .rx-price-tag--pub .rx-price-tag__label,
        .rx-price-tag--pub .rx-price-tag__val { color: var(--blue); }

        /* ── Search bar ──────────────────────── */
        .rx-search-wrap { position: relative; margin-bottom: 0; box-sizing: border-box; width: 100%; }
        .rx-search {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 14px 11px 44px;
            font-size: 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--navy);
            color: var(--text);
            outline: none;
            transition: border-color .18s;
        }
        .rx-search:focus { border-color: var(--blue); }
        .rx-search::placeholder { color: var(--text-muted); }
        .rx-search-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 19px; pointer-events: none;
        }

        @media (max-width: 560px) {
            .rx-price-header__price { width: 70px; font-size: 9px; }
            .rx-price-tag { width: 62px; padding: 4px 4px; }
            .rx-price-tag__val { font-size: 11.5px; }
        }

        /* Stack prices below name on narrow phones */
        @media (max-width: 430px) {
            .rx-price-header { display: none; }
            .rx-estudio {
                flex-wrap: wrap;
                row-gap: 6px;
                align-items: flex-start;
            }
            .rx-estudio__body { min-width: 0; }
            .rx-price-cols {
                width: 100%;
                padding-left: 46px;
                justify-content: flex-start;
            }
            .rx-price-tag {
                flex: 1;
                width: auto;
                min-width: 0;
            }
            .rx-price-tag__val { font-size: 13px; }
        }
    </style>

    <div class="rx-wrap">

        {{-- Header ─────────────────────────────────────── --}}
        <div class="rx-header">
            <div>
                <div class="rx-kicker">PATS · Diagnóstico por imagen</div>
                <h1 class="rx-title"><span>Imagenología</span></h1>
                <p class="rx-subtitle">
                    Disponibilidad de equipos y citas ·
                    {{ ucfirst(Carbon::now('America/Mexico_City')->isoFormat('MMMM YYYY')) }}
                </p>
            </div>
            <div class="rx-fecha-badge">
                <div class="rx-fecha-badge__dia">Hoy</div>
                <div class="rx-fecha-badge__full">{{ ucfirst($fecha['fecha_display']) }}</div>
                <div class="rx-fecha-badge__hora" id="reloj">{{ $fecha['hora_actual'] }}</div>
            </div>
        </div>

        {{-- Stats ──────────────────────────────────────── --}}
        <div class="rx-stats">

            <div class="rx-stat">
                <div class="rx-stat__icon" style="background:rgba(37,99,235,.10);color:var(--blue);">
                    <i class="mdi mdi-file-image"></i>
                </div>
                <div>
                    <div class="rx-stat__num">{{ $estudios->count() }}</div>
                    <div class="rx-stat__label">Estudios RX</div>
                </div>
            </div>
        </div>
                {{-- Buscador --}}
                <div class="rx-search-wrap">
                    <i class="mdi mdi-magnify rx-search-icon"></i>
                    <input type="text" id="rxSearch" class="rx-search"
                        placeholder="Buscar estudio (ej: Radiografía, TAC, Ultrasonido...)" autocomplete="off">
                    <button class="rx-search-clear" id="rxClear">
                        <i class="mdi mdi-close" style="font-size:15px;"></i>
                    </button>
                </div>

                <div class="rx-layout">

            {{-- COL IZQUIERDA ───────────────────────────────── --}}
            <div>

                {{-- Tabs Imagenología --}}
                <div class="rx-tabs-wrap">
                    <div class="rx-tabs-nav">
                        <button class="rx-tab-btn is-active" data-tab="imagen">
                            <i class="mdi mdi-file-image"></i>
                            Imagenología
                            <span class="rx-tab-count">{{ $estudios->count() }}</span>
                        </button>
                    </div>

                    {{-- ── PANEL IMAGEN ── --}}
                    <div class="rx-tab-panel is-active" id="panel-imagen">
                        @php
                            $gruposRx = $estudios->groupBy(fn($est) => $est->proveedor ? $est->proveedor->nombre_unidad : 'Proveedor General');
                        @endphp

                        @forelse($gruposRx as $proveedorNombre => $grupoEstudios)
                            @php 
                                $accordionId = 'rxacc-' . Str::slug($proveedorNombre); 
                                $proveedor = $grupoEstudios->first()->proveedor;
                            @endphp
                            <div class="rx-proveedor-group">

                                <button class="rx-accordion-btn" data-target="#{{ $accordionId }}" aria-expanded="true">
                                    <span class="rx-accordion-btn__left">
                                        <i class="mdi mdi-hospital-building"></i>
                                        {{ $proveedorNombre }}
                                    </span>
                                    <span class="rx-accordion-btn__right">
                                        <span style="font-size:11px;font-weight:700;background:rgba(37,99,235,.10);color:var(--blue);padding:2px 8px;border-radius:100px;">
                                            {{ $grupoEstudios->count() }} estudios
                                        </span>
                                        <i class="mdi mdi-chevron-down rx-accordion-chevron"></i>
                                    </span>
                                </button>

                                <div class="rx-accordion-panel" id="{{ $accordionId }}">
                                    @if($proveedor && ($proveedor->direccion || $proveedor->telefono))
                                                                        <div style="padding: 12px 18px; background: rgba(37,99,235,0.04); border-bottom: 1px solid var(--border); font-size: 12.5px; color: var(--text-muted); display: flex; gap: 20px; flex-wrap: wrap;">
                                        @if($proveedor->direccion)
                                        <a href="https://maps.google.com/?q={{ urlencode($proveedor->direccion) }}" target="_blank"
                                            class="digi-btn digi-btn--secondary digi-btn--sm">
                                            <i class="mdi mdi-directions"></i>
                                            Ubicación
                                        </a>
                                        <!-- <span><i class="mdi mdi-map-marker" style="color:var(--blue); font-size:14px; vertical-align:middle; margin-right:4px;"></i>{{ $proveedor->direccion }}</span> -->
                                        @endif
                                        @if($proveedor->telefono)
                                        <a href="tel:{{ $proveedor->telefono }}" class="digi-btn digi-btn--outline digi-btn--sm">
                                        <span><i class="mdi mdi-phone" style="color:var(--blue); font-size:14px; vertical-align:middle; margin-right:4px;"></i>{{ $proveedor->telefono }}</span>
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="rx-price-header">
                                        <span class="rx-price-header__name">Estudio</span>
                                        <span class="rx-price-header__price">Sin pasaporte</span>
                                        <span class="rx-price-header__price">Con pasaporte</span>
                                    </div>

                                    @foreach ($grupoEstudios as $est)
                                        <div class="rx-estudio" data-nombre="{{ strtolower($est->nombre_estudio) }}">
                                            <div class="rx-estudio__icon">
                                                <i class="mdi mdi-scan-helper"></i>
                                            </div>
                                            <div class="rx-estudio__body">
                                                <p class="rx-estudio__nombre">{{ $est->nombre_estudio }}</p>
                                            </div>
                                            <div class="rx-price-cols">
                                                <div class="rx-price-tag rx-price-tag--pub">
                                                    <span class="rx-price-tag__val">
                                                        ${{ number_format($est->precio_nopats, 2) }}
                                                    </span>
                                                </div>
                                                <div class="rx-price-tag rx-price-tag--pats">
                                                    <span class="rx-price-tag__val">
                                                        ${{ number_format($est->precio_pats, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @empty
                            <div class="rx-empty">
                                <i class="mdi mdi-file-image"></i>
                                Sin estudios de imagenología registrados.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Equipos / recursos de imagen --}}
                <!-- <div class="rx-card">
                    <div class="rx-card__head">
                        <i class="mdi mdi-radioactive rx-card__head-icon"></i>
                        <div>
                            <div class="rx-card__head-title">Equipos de imagen disponibles</div>
                            <div class="rx-card__head-sub">{{ $recursos->count() }} unidades en la red ·
                                {{ $servicio?->region }} {{ $servicio?->unidad ?? '' }}</div>
                        </div>
                    </div>
                    <div class="rx-card__body">
                        @forelse($recursos as $rec)
                            <div class="rx-recurso">
                                <div class="rx-recurso__avatar">
                                    <i
                                        class="mdi mdi-{{ str_contains(strtolower($rec->tipo_recurso), 'rayos') ? 'radioactive' : 'scan-helper' }}"></i>
                                </div>
                                <div>
                                    <p class="rx-recurso__nombre">{{ $rec->nombre_recurso }}</p>
                                    <p class="rx-recurso__tipo">
                                        <i class="mdi mdi-tag-outline"></i>
                                        {{ $rec->tipo_recurso }}
                                        &nbsp;·&nbsp; Cap. {{ $rec->capacidad }}
                                    </p>
                                </div>
                                <a href="{{ route('agenda.index', ['servicio' => $servicio?->id_servicio, 'recurso' => $rec->id_recurso]) }}"
                                    class="rx-recurso__btn">
                                    <i class="mdi mdi-calendar-search" style="font-size:15px;"></i>
                                    Ver agenda
                                </a>
                            </div>
                        @empty
                            <div class="rx-empty">
                                <i class="mdi mdi-radioactive"></i>
                                Sin equipos de imagen registrados en esta unidad.
                            </div>
                        @endforelse
                    </div>
                </div> -->

                {{-- Agenda de HOY (timeline) --}}
                <!-- <div class="rx-card">
                    <div class="rx-card__head">
                        <i class="mdi mdi-calendar-today rx-card__head-icon"></i>
                        <div>
                            <div class="rx-card__head-title">Agenda de hoy</div>
                            <div class="rx-card__head-sub">
                                {{ ucfirst(Carbon::parse($fecha['fecha_hoy'])->isoFormat('dddd D [de] MMMM')) }} ·
                                {{ $agendaHoy->count() }} {{ $agendaHoy->count() === 1 ? 'bloque' : 'bloques' }}
                            </div>
                        </div>
                    </div>
                    <div class="rx-card__body">
                        @if ($agendaHoy->isEmpty())
                            <div class="rx-empty">
                                <i class="mdi mdi-calendar-blank"></i>
                                Sin bloques registrados para hoy.
                            </div>
                        @else
                            <div class="rx-timeline">
                                @foreach ($agendaHoy->sortBy('fecha_inicio') as $bloque)
                                    @php
                                        $ini = Carbon::parse($bloque->fecha_inicio);
                                        $fin = Carbon::parse($bloque->fecha_fin);
                                        $dur = $ini->diffInMinutes($fin);
                                        $tipo = strtolower($bloque->tipo_bloque);
                                    @endphp
                                    <div class="rx-bloque">
                                        <div class="rx-bloque__time">
                                            <div class="rx-bloque__hora-ini">{{ $ini->format('H:i') }}</div>
                                            <div class="rx-bloque__hora-fin">{{ $fin->format('H:i') }}</div>
                                        </div>
                                        <div class="rx-bloque__line">
                                            <div class="rx-bloque__dot rx-bloque__dot--{{ $tipo }}"></div>
                                        </div>
                                        <div class="rx-bloque__body">
                                            <p class="rx-bloque__recurso">{{ $bloque->nombre_recurso }}</p>
                                            <div class="rx-bloque__dur">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $dur }} min
                                            </div>
                                            <span class="rx-bloque__tag rx-bloque__tag--{{ $tipo }}">
                                                {{ $bloque->tipo_bloque }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div> -->

                {{-- Disponibilidad próximos días --}}
                <!-- @if ($proximaDisp->isNotEmpty())
                    <div class="rx-card">
                        <div class="rx-card__head">
                            <i class="mdi mdi-calendar-clock rx-card__head-icon"></i>
                            <div>
                                <div class="rx-card__head-title">Próximos horarios disponibles</div>
                                <div class="rx-card__head-sub">Bloques con cupos libres</div>
                            </div>
                        </div>
                        <div class="rx-card__body">
                            @foreach ($proximaDisp as $fechaStr => $bloques)
                                @php $carbon = Carbon::parse($fechaStr); @endphp
                                <div class="rx-fecha-group">
                                    <div class="rx-fecha-label">
                                        <i class="mdi mdi-calendar-outline"></i>
                                        {{ ucfirst($carbon->isoFormat('dddd D [de] MMMM')) }}
                                    </div>
                                    <div class="rx-slots">
                                        @foreach ($bloques->sortBy('fecha_inicio') as $b)
                                            <div class="rx-slot">
                                                <span
                                                    class="rx-slot__hora">{{ Carbon::parse($b->fecha_inicio)->format('H:i') }}
                                                    – {{ Carbon::parse($b->fecha_fin)->format('H:i') }}</span>
                                                <span
                                                    class="rx-slot__rec">{{ \Illuminate\Support\Str::limit($b->nombre_recurso, 18) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <a href="{{ route('agenda.index', ['servicio' => $servicio?->id_servicio]) }}"
                                style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:16px;padding:12px;background:var(--blue);color:#fff;border-radius:var(--radius-sm);font-size:13.5px;font-weight:700;text-decoration:none;box-shadow:0 3px 12px rgba(37,99,235,.22);transition:background .15s;">
                                <i class="mdi mdi-calendar-month" style="font-size:17px;"></i>
                                Ver calendario completo
                            </a>
                        </div>
                    </div>
                @endif -->

            </div>{{-- /col izq --}}

            {{-- PANEL DERECHO ───────────────────────────── --}}
            <!-- <div class="rx-panel">

                {{-- Citas de hoy --}}
                <div class="rx-panel-card">
                    <div class="rx-panel-head">
                        <i class="mdi mdi-account-clock" style="color:var(--blue);font-size:22px;"></i>
                        <div>
                            <div class="rx-panel-head__title">Citas de hoy</div>
                            <div class="rx-panel-head__sub">{{ $citasHoy->count() }} programadas</div>
                        </div>
                    </div>
                    <div class="rx-panel-body">
                        @forelse($citasHoy as $cita)
                            @php
                                $badgeCls = match ($cita->estatus) {
                                    'CONFIRMADO' => 'conf',
                                    'EN_PROCESO' => 'proc',
                                    default => '',
                                };
                            @endphp
                            <div class="rx-cita-row">
                                <div class="rx-cita-row__hora">
                                    {{ \Illuminate\Support\Str::limit($cita->hora_inicio, 5, '') }}
                                </div>
                                <div class="rx-cita-row__pac">{{ $cita->nombre_paciente }}</div>
                                <span class="rx-cita-badge rx-cita-badge--{{ $badgeCls }}">
                                    {{ $cita->estatus }}
                                </span>
                            </div>
                        @empty
                            <div class="rx-empty">
                                <i class="mdi mdi-calendar-blank"></i>
                                Sin citas de imagen para hoy.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Mini resumen de bloques HOY por tipo --}}
                <div class="rx-panel-card">
                    <div class="rx-panel-head">
                        <i class="mdi mdi-chart-donut" style="color:var(--blue);font-size:22px;"></i>
                        <div>
                            <div class="rx-panel-head__title">Resumen del día</div>
                            <div class="rx-panel-head__sub">Bloques por estado</div>
                        </div>
                    </div>
                    <div class="rx-panel-body" style="padding:16px 18px;">
                        @foreach ([['DISPONIBLE', '#0e7a5f', 'rgba(14,122,95,.10)', 'calendar-check'], ['RESERVADO', '#2563eb', 'rgba(37,99,235,.10)', 'calendar-account'], ['BLOQUEADO', '#d97706', 'rgba(217,119,6,.10)', 'calendar-remove'], ['MANTENIMIENTO', '#dc2626', 'rgba(220,38,38,.10)', 'tools']] as [$tipo, $color, $bg, $icon])
                            @php $cnt = $agendaHoy->where('tipo_bloque', $tipo)->count(); @endphp
                            @if ($cnt > 0)
                                <div
                                    style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
                                    <div
                                        style="width:36px;height:36px;border-radius:var(--radius-sm);background:{{ $bg }};display:flex;align-items:center;justify-content:center;color:{{ $color }};font-size:17px;flex-shrink:0;">
                                        <i class="mdi mdi-{{ $icon }}"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:13px;font-weight:700;color:var(--cream);">
                                            {{ ucfirst(strtolower($tipo)) }}</div>
                                    </div>
                                    <div style="font-size:22px;font-weight:800;color:{{ $color }};">
                                        {{ $cnt }}</div>
                                </div>
                            @endif
                        @endforeach

                        @if ($agendaHoy->isEmpty())
                            <div class="rx-empty" style="padding:20px;">
                                <i class="mdi mdi-calendar-blank"></i>
                                Sin actividad hoy.
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /rx-panel --}} -->

        </div>{{-- /rx-layout --}}
    </div>{{-- /rx-wrap --}}

    <style>
        .rx-search-wrap { position: relative; margin-bottom: 20px; box-sizing: border-box; width: 100%; }
        .rx-search-input { width: 100%; box-sizing: border-box; padding: 12px 40px 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-sm); }
        .rx-search-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: none; background: none; border: none; cursor: pointer; }
        .rx-search-clear.visible { display: block; }
        .rx-tabs-wrap { display: flex; flex-direction: column; gap: 15px; }
    </style>

    <script>
        const reloj = document.getElementById('reloj');
        if (reloj) {
            // ── Búsqueda ──────────────────────────────────
            function filterEstudios(term) {
                document.querySelectorAll('.rx-proveedor-group').forEach(group => {
                    let visibleCount = 0;
                    group.querySelectorAll('.rx-estudio').forEach(el => {
                        const nombre = el.dataset.nombre ? el.dataset.nombre.toLowerCase() : '';
                        const match = !term || nombre.includes(term);
                        el.style.display = match ? 'flex' : 'none';
                        if (match) visibleCount++;
                    });
                    group.style.display = (term && visibleCount === 0) ? 'none' : 'block';
                });
            }

            const inp = document.getElementById('rxSearch');
            const clear = document.getElementById('rxClear');

            if (inp && clear) {
                inp.addEventListener('input', () => {
                    const term = inp.value.toLowerCase().trim();
                    clear.classList.toggle('visible', term.length > 0);
                    filterEstudios(term);
                });

                clear.addEventListener('click', () => {
                    inp.value = '';
                    inp.focus();
                    clear.classList.remove('visible');
                    filterEstudios('');
                });

                inp.addEventListener('keydown', e => {
                    if (e.key === 'Escape') clear.click();
                });
            }         function tick() {
                const n = new Date();
                reloj.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
            }
            tick();
            setInterval(tick, 30000);
        }

        // ── Acordeón RX por proveedor ────────────────
        document.querySelectorAll('.rx-accordion-btn').forEach(btn => {
            const panel = document.querySelector(btn.dataset.target);
            if (!panel) return;
            btn.classList.add('is-open');
            panel.classList.add('is-open');
            btn.addEventListener('click', () => {
                const isOpen = btn.classList.toggle('is-open');
                panel.classList.toggle('is-open', isOpen);
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        // ── Búsqueda RX ────────────────────────
        const rxSearch = document.getElementById('rxSearch');
        if (rxSearch) {
            rxSearch.addEventListener('input', () => {
                const term = rxSearch.value.toLowerCase().trim();
                document.querySelectorAll('.rx-proveedor-group').forEach(group => {
                    let visible = 0;
                    group.querySelectorAll('.rx-estudio').forEach(el => {
                        const match = !term || el.dataset.nombre.includes(term);
                        el.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    group.style.display = (term && visible === 0) ? 'none' : '';
                    // Auto-open accordion when searching
                    if (term && visible > 0) {
                        const btn = group.querySelector('.rx-accordion-btn');
                        const panel = group.querySelector('.rx-accordion-panel');
                        if (btn && panel) {
                            btn.classList.add('is-open');
                            panel.classList.add('is-open');
                        }
                    }
                });
            });
        }
    </script>
@endsection
