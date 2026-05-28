{{-- resources/views/servicios/farmacia.blade.php --}}
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Farmacia')

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

        .far-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 28px 80px;
            overflow-x: hidden;
        }

        /* ── Header ─────────────────────────────────── */
        .far-header {
            padding: 48px 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 32px;
        }

        .far-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 8px;
        }

        .far-title {
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: var(--cream);
            letter-spacing: -.02em;
            margin: 0 0 6px;
        }

        .far-title span {
            color: var(--blue);
        }

        .far-subtitle {
            font-size: 14.5px;
            color: var(--text-muted);
            margin: 0;
        }

        .far-fecha-badge {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 20px;
            text-align: right;
            box-shadow: var(--shadow);
        }

        .far-fecha-badge__dia {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            font-weight: 700;
        }

        .far-fecha-badge__full {
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            margin-top: 2px;
        }

        .far-fecha-badge__hora {
            font-size: 22px;
            font-weight: 800;
            color: var(--blue);
            font-variant-numeric: tabular-nums;
        }

        /* ── Stats ──────────────────────────────────── */
        .far-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .far-stat {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 22px;
            flex: 1;
            min-width: 140px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .far-stat__icon {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .far-stat__icon--pill {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .far-stat__icon--hoy {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .far-stat__icon--unit {
            background: rgba(96, 165, 250, .12);
            color: var(--blue-light);
        }

        .far-stat__icon--inac {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .far-stat__num {
            font-size: 28px;
            font-weight: 800;
            color: var(--cream);
            line-height: 1;
        }

        .far-stat__label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 3px;
        }

        /* ── Layout ─────────────────────────────────── */
        .far-layout {
            display: grid;
            /* grid-template-columns: 1fr 320px; */
            gap: 24px;
            align-items: start;
        }

        /* ── Buscador ───────────────────────────────── */
        .far-search-wrap {
            position: relative;
            margin-bottom: 20px;
            box-sizing: border-box;
            width: 100%;
        }

        .far-search {
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

        .far-search:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }

        .far-search::placeholder {
            color: var(--text-muted);
        }

        .far-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 20px;
            pointer-events: none;
        }

        .far-search-clear {
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

        .far-search-clear.visible {
            display: flex;
        }

        .far-search-clear:hover {
            background: var(--navy-light);
            color: var(--blue);
        }

        /* Dropdown sugerencias */
        .far-sug {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 8px 32px rgba(37, 99, 235, .12);
            z-index: 100;
            max-height: 260px;
            overflow-y: auto;
            display: none;
        }

        .far-sug.open {
            display: block;
        }

        .far-sug-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 16px;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .far-sug-item:last-child {
            border-bottom: none;
        }

        .far-sug-item:hover {
            background: var(--navy);
        }

        .far-sug-item__name {
            font-size: 14px;
            color: var(--cream);
            font-weight: 500;
        }

        .far-sug-item__price {
            font-size: 13px;
            font-weight: 700;
            color: var(--blue);
        }

        /* ── Tabs ───────────────────────────────────── */
        .far-tabs-wrap {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .far-tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border);
            background: var(--navy-light);
        }

        .far-tab-btn {
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

        .far-tab-btn.is-active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            background: var(--white);
        }

        .far-tab-count {
            font-size: 11px;
            font-weight: 700;
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
            padding: 2px 8px;
            border-radius: 100px;
        }

        .far-tab-panel {
            display: none;
            padding: 20px 24px;
        }

        .far-tab-panel.is-active {
            display: block;
        }

        /* ── Acordeones ─────────────────────────────── */
        .far-accordion-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 18px;
            cursor: pointer;
            transition: all .18s;
        }

        .far-accordion-btn.is-open {
            border-bottom-color: transparent;
            border-radius: var(--radius) var(--radius) 0 0;
            background: var(--navy-light);
        }

        .far-accordion-btn__left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--cream);
        }

        .far-accordion-btn__left i {
            color: var(--blue);
            font-size: 20px;
        }

        .far-accordion-btn__right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .far-accordion-chevron {
            font-size: 20px;
            color: var(--text-muted);
            transition: transform .2s;
        }

        .far-accordion-btn.is-open .far-accordion-chevron {
            transform: rotate(180deg);
        }

        .far-accordion-panel {
            display: none;
            border: 1.5px solid var(--border);
            border-top: none;
            border-radius: 0 0 var(--radius) var(--radius);
            background: var(--white);
        }

        .far-accordion-panel.is-open {
            display: block;
        }

        /* ── Header precios ─────────────────────────── */
        .far-price-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            background: var(--navy);
            border-bottom: 1px solid var(--border);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
        }

        .far-price-header__name {
            flex: 1;
        }

        .far-price-header__price {
            width: 100px;
            text-align: center;
        }

        /* ── Estudio / Medicamento individual ───────── */
        .far-estudio {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .far-estudio:last-child {
            border-bottom: none;
        }

        .far-estudio:hover {
            background: var(--navy);
        }

        .far-estudio__info {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
            padding-right: 20px;
        }

        .far-estudio__icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            background: rgba(37, 99, 235, .08);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 18px;
        }

        .far-estudio__text {
            min-width: 0;
        }

        .far-estudio__nombre {
            font-size: 14px;
            font-weight: 600;
            color: var(--cream);
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .far-price-cols {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .far-price-tag {
            width: 100px;
            text-align: center;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1px;
        }

        .far-price-tag__label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .far-price-tag__val {
            font-size: 14px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .far-price-tag--pats {
            background: rgba(14, 122, 95, .09);
        }

        .far-price-tag--pats .far-price-tag__label,
        .far-price-tag--pats .far-price-tag__val {
            color: #0e7a5f;
        }

        .far-price-tag--pub {
            background: rgba(37, 99, 235, .08);
        }

        .far-price-tag--pub .far-price-tag__label,
        .far-price-tag--pub .far-price-tag__val {
            color: var(--blue);
        }

        /* Sin resultados */
        .far-empty {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        .far-empty i {
            font-size: 36px;
            display: block;
            margin-bottom: 8px;
            opacity: .3;
        }

        /* ── Panel: Unidades / contacto ─────────────── */
        .far-panel {
            position: sticky;
            top: 24px;
        }

        .far-panel-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
        }

        .far-panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .far-panel-head i {
            color: var(--blue);
            font-size: 22px;
        }

        .far-panel-head__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
        }

        .far-panel-head__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .far-panel-body {
            padding: 16px 18px;
        }

        /* Unidad card */
        .far-unidad {
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 10px;
            transition: border-color .18s, background .18s;
        }

        .far-unidad:last-child {
            margin-bottom: 0;
        }

        .far-unidad:hover {
            border-color: rgba(37, 99, 235, .28);
            background: var(--navy);
        }

        .far-unidad__nombre {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .far-unidad__nombre i {
            color: var(--blue);
            font-size: 16px;
        }

        .far-unidad__dato {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .far-unidad__dato i {
            color: var(--blue-light);
            font-size: 14px;
            flex-shrink: 0;
        }

        .far-unidad__btns {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .far-btn-tel {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: var(--blue);
            color: #fff;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s;
            flex: 1;
            justify-content: center;
        }

        .far-btn-tel:hover {
            background: #1d52d4;
            color: #fff;
        }

        .far-btn-mail {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: var(--navy);
            color: var(--blue);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all .15s;
            flex: 1;
            justify-content: center;
        }

        .far-btn-mail:hover {
            border-color: var(--blue);
            background: rgba(37, 99, 235, .05);
        }

        /* Citas hoy */
        .far-cita-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 7px;
            font-size: 13px;
            transition: background .12s;
        }

        .far-cita-row:last-child {
            margin-bottom: 0;
        }

        .far-cita-row:hover {
            background: var(--navy);
        }

        .far-cita-row__hora {
            font-weight: 800;
            color: var(--blue);
            font-size: 12.5px;
            min-width: 44px;
        }

        .far-cita-row__pac {
            font-weight: 600;
            color: var(--cream);
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 12.5px;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width:960px) {
            .far-layout {
                grid-template-columns: 1fr;
            }

            .far-panel {
                position: static;
            }
        }

        @media (max-width:600px) {
            .far-wrap {
                padding: 0 12px 60px;
            }

            .far-header {
                padding: 28px 0 22px;
            }

            .far-stats {
                flex-direction: column;
            }

            .far-tab-panel {
                padding: 14px 10px;
            }

            .far-accordion-btn {
                padding: 12px;
            }

            .far-accordion-btn__left {
                flex: 1;
                min-width: 0;
                overflow: hidden;
                font-size: 13px;
            }

            .far-price-header__price {
                width: 72px;
                font-size: 9px;
            }

            .far-price-tag {
                width: 64px;
                padding: 4px 5px;
            }

            .far-price-tag__val {
                font-size: 12px;
            }

            .far-estudio__nombre {
                white-space: normal;
            }
        }

        /* Stack prices below name on narrow phones */
        @media (max-width: 430px) {
            .far-price-header { display: none; }

            .far-estudio {
                flex-wrap: wrap;
                row-gap: 6px;
                align-items: flex-start;
            }

            /* flex-basis:100% forces info onto its own line so it never collapses to 0 */
            .far-estudio__info {
                flex: 0 0 100%;
                padding-right: 0;
                min-width: 0;
            }

            .far-price-cols {
                width: 100%;
                padding-left: 50px;
                justify-content: flex-start;
            }

            .far-price-tag {
                flex: 1;
                width: auto;
                min-width: 0;
            }

            .far-price-tag__val {
                font-size: 13px;
            }
        }
    </style>

    <div class="far-wrap">

        {{-- Header ─────────────────────────────────────── --}}
        <div class="far-header">
            <div>
                <div class="far-kicker">PATS · Inventario farmacéutico</div>
                <h1 class="far-title"><span>Farmacia</span></h1>
                <p class="far-subtitle">Medicamentos disponibles en la red · Contacto por unidad</p>
            </div>
            <div class="far-fecha-badge">
                <div class="far-fecha-badge__dia">Hoy</div>
                <div class="far-fecha-badge__full">{{ ucfirst($fecha['fecha_display']) }}</div>
                <div class="far-fecha-badge__hora" id="reloj">{{ $fecha['hora_actual'] }}</div>
            </div>
        </div>

        {{-- Stats ──────────────────────────────────────── --}}
        <div class="far-stats">
            <div class="far-stat">
                <div class="far-stat__icon far-stat__icon--pill">
                    <i class="mdi mdi-pill"></i>
                </div>
                <div>
                    <div class="far-stat__num">{{ $stats['total_activos'] }}</div>
                    <div class="far-stat__label">Medicamentos</div>
                </div>
            </div>
            <div class="far-stat">
                <div class="far-stat__icon far-stat__icon--unit">
                    <i class="mdi mdi-hospital-building"></i>
                </div>
                <div>
                    {{-- Usamos pats_unidades para contar unidades activas --}}
                    <div class="far-stat__num">{{ $totalUnidades }}</div>
                    <div class="far-stat__label">Unidades</div>
                </div>
            </div>
        </div>

        <div class="far-layout">

            {{-- COL IZQUIERDA: Catálogo ─────────────────────── --}}
            <div>

                {{-- Buscador --}}
                <div class="far-search-wrap">
                    <i class="mdi mdi-magnify far-search-icon"></i>
                    <input type="text" id="farSearch" class="far-search"
                        placeholder="Buscar medicamento (ej: Paracetamol, Ibuprofeno...)" autocomplete="off">
                    <button class="far-search-clear" id="farClear">
                        <i class="mdi mdi-close" style="font-size:15px;"></i>
                    </button>
                    <div class="far-sug" id="farSug"></div>
                </div>

                {{-- Tabs Catálogo --}}
                <div class="far-tabs-wrap">
                    <div class="far-tabs-nav">
                        <button class="far-tab-btn is-active" data-tab="med">
                            <i class="mdi mdi-pill"></i>
                            Catálogo de Medicamentos
                            <span class="far-tab-count">{{ $medicamentos->count() }}</span>
                        </button>
                    </div>

                    <div class="far-tab-panel is-active" id="panel-med">
                        @php
                            $gruposMed = $medicamentos->groupBy(fn($med) => $med->proveedor ? $med->proveedor->nombre_unidad : 'Proveedor General');
                        @endphp

                        @forelse($gruposMed as $proveedorNombre => $meds)
                            @php 
                                $accordionId = 'faracc-' . Str::slug($proveedorNombre); 
                                $proveedor = $meds->first()->proveedor;
                            @endphp
                            <div class="far-proveedor-group" style="margin-bottom: 10px;">
                                <button class="far-accordion-btn" data-target="#{{ $accordionId }}" aria-expanded="true">
                                    <span class="far-accordion-btn__left">
                                        <i class="mdi mdi-hospital-building"></i>
                                        {{ $proveedorNombre }}
                                    </span>
                                    <span class="far-accordion-btn__right">
                                        <span class="far-tab-count">{{ $meds->count() }} meds</span>
                                        <i class="mdi mdi-chevron-down far-accordion-chevron"></i>
                                    </span>
                                </button>

                                <div class="far-accordion-panel" id="{{ $accordionId }}">
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
                                    
                                    <div class="far-price-header">
                                        <span class="far-price-header__name">Medicamento</span>
                                        <span class="far-price-header__price">Público</span>
                                        <span class="far-price-header__price">PATS</span>
                                    </div>

                                    @foreach($meds as $med)
                                        <div class="far-estudio" data-nombre="{{ strtolower($med->descripcion) }}">
                                            <div class="far-estudio__info">
                                                <div class="far-estudio__icon"><i class="mdi mdi-pill"></i></div>
                                                <div class="far-estudio__text">
                                                    <div class="far-estudio__nombre" title="{{ $med->descripcion }}">
                                                        {{ $med->descripcion }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="far-price-cols">
                                                <div class="far-price-tag far-price-tag--pub">
                                                    <span class="far-price-tag__label">Público</span>
                                                    <span class="far-price-tag__val">${{ number_format($med->precio_nopats, 2) }}</span>
                                                </div>
                                                <div class="far-price-tag far-price-tag--pats">
                                                    <span class="far-price-tag__label">PATS</span>
                                                    <span class="far-price-tag__val">${{ number_format($med->precio_pats, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="far-empty">
                                <i class="mdi mdi-pill"></i>
                                Sin medicamentos registrados.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- /col izq --}}

            {{-- COL DERECHA: Unidades y citas ──────────────── --}}
            <!-- <div class="far-panel">

                {{-- Unidades / Contacto --}}
                <div class="far-panel-card">
                    <div class="far-panel-head">
                        <i class="mdi mdi-hospital-building"></i>
                        <div>
                            <div class="far-panel-head__title">Unidades con farmacia</div>
                            <div class="far-panel-head__sub">{{ $unidades->count() }} unidades activas</div>
                        </div>
                    </div>
                    <div class="far-panel-body">
                        @forelse($unidades as $unidad)
                            <div class="far-unidad">
                                <p class="far-unidad__nombre">
                                    <i class="mdi mdi-hospital-building"></i>
                                    {{ $unidad->nombre_unidad }}
                                </p>
                                @if ($unidad->direccion)
                                    <div class="far-unidad__dato">
                                        <i class="mdi mdi-map-marker-outline"></i>
                                        {{ $unidad->direccion }}
                                    </div>
                                @endif
                                @if ($unidad->telefono)
                                    <div class="far-unidad__dato">
                                        <i class="mdi mdi-phone-outline"></i>
                                        {{ $unidad->telefono }}
                                    </div>
                                @endif
                                @if ($unidad->correo)
                                    <div class="far-unidad__dato">
                                        <i class="mdi mdi-email-outline"></i>
                                        {{ $unidad->correo }}
                                    </div>
                                @endif
                                <div class="far-unidad__btns">
                                    @if ($unidad->telefono)
                                        <a href="tel:{{ $unidad->telefono }}" class="far-btn-tel">
                                            <i class="mdi mdi-phone" style="font-size:14px;"></i>
                                            Llamar
                                        </a>
                                    @endif
                                    @if ($unidad->correo)
                                        <a href="mailto:{{ $unidad->correo }}" class="far-btn-mail">
                                            <i class="mdi mdi-email-outline" style="font-size:14px;"></i>
                                            Email
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="far-empty" style="padding:24px;">
                                <i class="mdi mdi-hospital-building"></i>
                                Sin unidades registradas.
                            </div>
                        @endforelse
                    </div>
                </div>


            </div>{{-- /far-panel --}} -->

        </div>{{-- /far-layout --}}
    </div>{{-- /far-wrap --}}

    <script>
        (function() {
            'use strict';

            // ── Reloj ─────────────────────────────────────
            const reloj = document.getElementById('reloj');
            if (reloj) {
                function tick() {
                    const n = new Date();
                    reloj.textContent =
                        `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
                }
                tick();
                setInterval(tick, 30000);
            }

            // ── Búsqueda y Acordeones ───────────────────────
            const inp = document.getElementById('farSearch');
            const clear = document.getElementById('farClear');

            function normalize(s) {
                return s ? s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '') : '';
            }

            function filterMeds(term) {
                const t = normalize(term);
                document.querySelectorAll('.far-proveedor-group').forEach(group => {
                    let visibleCount = 0;
                    group.querySelectorAll('.far-estudio').forEach(el => {
                        const nombre = el.dataset.nombre;
                        const match = !t || normalize(nombre).includes(t);
                        el.style.display = match ? 'flex' : 'none';
                        if (match) visibleCount++;
                    });
                    group.style.display = (t && visibleCount === 0) ? 'none' : 'block';
                });
            }

            if (inp && clear) {
                inp.addEventListener('input', () => {
                    const term = inp.value.trim();
                    clear.classList.toggle('visible', term.length > 0);
                    filterMeds(term);
                });

                clear.addEventListener('click', () => {
                    inp.value = '';
                    inp.focus();
                    clear.classList.remove('visible');
                    filterMeds('');
                });

                inp.addEventListener('keydown', e => {
                    if (e.key === 'Escape') clear.click();
                });
            }

            // ── Lógica de acordeones ───────────────────────
            document.querySelectorAll('.far-accordion-btn').forEach(btn => {
                const targetSel = btn.dataset.target;
                const panel = document.querySelector(targetSel);
                if (!panel) return;

                // Empezar abiertos por defecto
                btn.classList.add('is-open');
                panel.classList.add('is-open');

                btn.addEventListener('click', () => {
                    const isOpen = btn.classList.toggle('is-open');
                    panel.classList.toggle('is-open', isOpen);
                    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });

            inp.addEventListener('keydown', e => {
                if (e.key === 'Escape') clear.click();
            });

            document.addEventListener('click', e => {
                if (!inp.contains(e.target) && !sug.contains(e.target)) sug.classList.remove('open');
            });

        })();
    </script>
@endsection
