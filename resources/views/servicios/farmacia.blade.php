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
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        /* ── Buscador ───────────────────────────────── */
        .far-search-wrap {
            position: relative;
            margin-bottom: 20px;
        }

        .far-search {
            width: 100%;
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

        /* ── Tabla de medicamentos ──────────────────── */
        .far-table-wrap {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .far-table-head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .far-table-head-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
        }

        .far-table-head-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .far-table-head i {
            color: var(--blue);
            font-size: 22px;
        }

        .far-table {
            width: 100%;
            border-collapse: collapse;
        }

        .far-table thead tr {
            background: var(--navy-light);
        }

        .far-table thead th {
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .far-table thead th:last-child {
            text-align: right;
        }

        .far-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }

        .far-table tbody tr:last-child {
            border-bottom: none;
        }

        .far-table tbody tr:hover {
            background: var(--navy);
        }

        .far-table tbody tr.hidden {
            display: none;
        }

        .far-table td {
            padding: 12px 16px;
            font-size: 13.5px;
        }

        .far-table td:first-child {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .far-table td:last-child {
            text-align: right;
        }

        .far-td-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            background: rgba(37, 99, 235, .08);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 15px;
        }

        .far-td-nombre {
            font-weight: 600;
            color: var(--cream);
        }

        .far-td-precio {
            font-weight: 800;
            color: var(--blue);
            font-size: 14px;
        }

        .far-pill-disp {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
            background: rgba(14, 122, 95, .09);
            color: #0e7a5f;
        }

        .far-pill-inac {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
            background: rgba(217, 119, 6, .09);
            color: var(--warning);
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
                padding: 0 16px 60px;
            }

            .far-header {
                padding: 28px 0 22px;
            }

            .far-stats {
                flex-direction: column;
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

                {{-- Tabla de medicamentos --}}
                <div class="far-table-wrap">
                    <div class="far-table-head">
                        <i class="mdi mdi-pill"></i>
                        <div>
                            <div class="far-table-head-title">Catálogo de medicamentos</div>
                            <div class="far-table-head-sub">
                                {{ $medicamentos->count() }} activos
                                @if ($medicamentosInactivos->count() > 0)
                                    · {{ $medicamentosInactivos->count() }} inactivos
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($medicamentos->isEmpty())
                        <div class="far-empty">
                            <i class="mdi mdi-pill"></i>
                            Sin medicamentos registrados en esta unidad.
                        </div>
                    @else
                        <table class="far-table">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Estado</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody id="farTbody">
                                @foreach ($medicamentos as $med)
                                    <tr class="far-row" data-nombre="{{ strtolower($med->medicamento) }}">
                                        <td>
                                            <div class="far-td-icon">
                                                <i class="mdi mdi-pill"></i>
                                            </div>
                                            <span class="far-td-nombre">{{ $med->medicamento }}</span>
                                        </td>
                                        <td>
                                            <span class="far-pill-disp">
                                                <i class="mdi mdi-check" style="font-size:10px;"></i>
                                                Disponible
                                            </span>
                                        </td>
                                        <td>
                                            <span class="far-td-precio">${{ number_format($med->precio, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Sin resultados de búsqueda --}}
                        <div class="far-empty" id="farEmpty" style="display:none;">
                            <i class="mdi mdi-magnify"></i>
                            Sin resultados para tu búsqueda.
                        </div>
                    @endif
                </div>

            </div>{{-- /col izq --}}

            {{-- COL DERECHA: Unidades y citas ──────────────── --}}
            <div class="far-panel">

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

                {{-- Entregas de hoy --}}
                @if ($entregasHoy->isNotEmpty())
                    <div class="far-panel-card">
                        <div class="far-panel-head">
                            <i class="mdi mdi-package-variant-closed"></i>
                            <div>
                                <div class="far-panel-head__title">Entregas de hoy</div>
                                <div class="far-panel-head__sub">{{ $entregasHoy->count() }} programadas</div>
                            </div>
                        </div>
                        <div class="far-panel-body" style="max-height:300px;overflow-y:auto;">
                            @foreach ($entregasHoy as $entrega)
                                <div class="far-cita-row">
                                    <div class="far-cita-row__hora">
                                        {{ \Illuminate\Support\Str::limit($entrega->hora_inicio, 5, '') }}
                                    </div>
                                    <div class="far-cita-row__pac">{{ $entrega->nombre_paciente }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /far-panel --}}

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

            // ── Búsqueda ──────────────────────────────────
            const inp = document.getElementById('farSearch');
            const clear = document.getElementById('farClear');
            const sug = document.getElementById('farSug');
            const rows = [...document.querySelectorAll('.far-row')];
            const empty = document.getElementById('farEmpty');

            if (!inp) return;

            function normalize(s) {
                return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }

            function filter(term) {
                const t = normalize(term);
                let visible = 0;
                rows.forEach(row => {
                    const match = normalize(row.dataset.nombre).includes(t);
                    row.classList.toggle('hidden', !match);
                    if (match) visible++;
                });
                if (empty) empty.style.display = (visible === 0 && t) ? 'block' : 'none';
            }

            function buildSug(term) {
                sug.innerHTML = '';
                if (!term) {
                    sug.classList.remove('open');
                    return;
                }
                const t = normalize(term);
                const matches = rows.filter(r => normalize(r.dataset.nombre).includes(t)).slice(0, 8);
                if (!matches.length) {
                    sug.classList.remove('open');
                    return;
                }

                matches.forEach(row => {
                    const nombre = row.querySelector('.far-td-nombre').textContent;
                    const precio = row.querySelector('.far-td-precio').textContent;
                    const item = document.createElement('div');
                    item.className = 'far-sug-item';
                    item.innerHTML =
                        `<span class="far-sug-item__name">${nombre}</span><span class="far-sug-item__price">${precio}</span>`;
                    item.addEventListener('click', () => {
                        inp.value = nombre;
                        filter(nombre);
                        sug.classList.remove('open');
                        clear.classList.add('visible');
                    });
                    sug.appendChild(item);
                });
                sug.classList.add('open');
            }

            inp.addEventListener('input', () => {
                const term = inp.value.trim();
                clear.classList.toggle('visible', term.length > 0);
                filter(term);
                buildSug(term);
            });

            clear.addEventListener('click', () => {
                inp.value = '';
                inp.focus();
                clear.classList.remove('visible');
                filter('');
                sug.classList.remove('open');
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
