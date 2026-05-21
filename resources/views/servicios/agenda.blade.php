{{-- resources/views/servicios/agenda.blade.php --}}
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Agenda · ' . $nombreMes)

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

        .ag-page {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 28px 80px;
        }

        .ag-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }

        .ag-header {
            padding: 48px 0 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 28px;
        }

        .ag-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 8px;
        }

        .ag-title {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 800;
            color: var(--cream);
            letter-spacing: -.02em;
            margin: 0 0 6px;
        }

        .ag-title span {
            color: var(--blue);
        }

        .ag-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0;
        }

        .ag-fecha-badge {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 20px;
            text-align: right;
            box-shadow: var(--shadow);
        }

        .ag-fecha-badge__dia {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            font-weight: 700;
        }

        .ag-fecha-badge__full {
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            margin-top: 2px;
        }

        .ag-fecha-badge__hora {
            font-size: 22px;
            font-weight: 800;
            color: var(--blue);
            font-variant-numeric: tabular-nums;
        }

        /* Stats */
        .ag-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .ag-stat {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 20px;
            flex: 1;
            min-width: 130px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ag-stat__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .ag-stat__dot--prog {
            background: #0e7a5f;
        }

        .ag-stat__dot--conf {
            background: var(--blue);
        }

        .ag-stat__dot--canc {
            background: var(--danger);
        }

        .ag-stat__num {
            font-size: 22px;
            font-weight: 800;
            color: var(--cream);
            line-height: 1;
        }

        .ag-stat__label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-top: 2px;
        }

        /* Filtros */
        .ag-filters {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 24px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 18px;
            box-shadow: var(--shadow-sm);
        }

        .ag-filters select {
            padding: 8px 32px 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--text);
            background: var(--navy);
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b85a8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border-color .15s;
        }

        .ag-filters select:focus {
            border-color: var(--blue);
        }

        .ag-filter-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        /* Calendario */
        .ag-calendar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .ag-cal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
        }

        .ag-cal-nav__mes {
            font-size: 17px;
            font-weight: 800;
            color: var(--cream);
            letter-spacing: -.01em;
            text-transform: capitalize;
        }

        .ag-nav-btn {
            width: 36px;
            height: 36px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--blue);
            font-size: 18px;
            transition: all .15s;
            text-decoration: none;
        }

        .ag-nav-btn:hover {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }

        .ag-nav-btn--disabled {
            opacity: .3;
            pointer-events: none;
        }

        .ag-cal-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-bottom: 1px solid var(--border);
        }

        .ag-cal-weekdays span {
            padding: 10px 0;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
        }

        .ag-cal-weekdays span:first-child,
        .ag-cal-weekdays span:last-child {
            color: var(--blue);
            opacity: .7;
        }

        .ag-cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .ag-day {
            min-height: 96px;
            padding: 8px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background .15s;
        }

        .ag-day:nth-child(7n) {
            border-right: none;
        }

        .ag-day:hover:not(.ag-day--vacio):not(.ag-day--pasado) {
            background: rgba(37, 99, 235, .03);
        }

        .ag-day--vacio {
            background: var(--navy);
            cursor: default;
        }

        .ag-day--pasado {
            background: var(--navy);
            opacity: .6;
            cursor: default;
        }

        .ag-day--hoy {
            background: rgba(37, 99, 235, .04);
        }

        .ag-day--activo {
            background: rgba(37, 99, 235, .06);
            outline: 2px solid var(--blue);
            outline-offset: -2px;
        }

        .ag-day--fin-sem .ag-day__num {
            color: var(--blue);
            opacity: .7;
        }

        .ag-day__num {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 4px;
        }

        .ag-day--hoy .ag-day__num {
            background: var(--blue);
            color: #fff;
            font-weight: 800;
        }

        .ag-day__chips {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        /* Chips — ahora representan citas por estatus */
        .ag-chip {
            font-size: 10.5px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }

        .ag-chip--prog {
            background: rgba(14, 122, 95, .12);
            color: #0e7a5f;
        }

        .ag-chip--conf {
            background: rgba(37, 99, 235, .12);
            color: var(--blue);
        }

        .ag-chip--proc {
            background: rgba(217, 119, 6, .12);
            color: var(--warning);
        }

        .ag-chip--canc {
            background: rgba(220, 38, 38, .10);
            color: var(--danger);
        }

        /* Panel lateral */
        .ag-panel {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: sticky;
            top: 24px;
        }

        .ag-panel__head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
        }

        .ag-panel__fecha {
            font-size: 16px;
            font-weight: 800;
            color: var(--cream);
            margin: 0;
        }

        .ag-panel__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin: 4px 0 0;
        }

        .ag-panel__body {
            padding: 16px;
            max-height: 580px;
            overflow-y: auto;
        }

        /* Cita en panel */
        .ag-cita {
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            margin-bottom: 10px;
            border-left: 3px solid var(--border);
            background: var(--navy);
            transition: transform .15s;
        }

        .ag-cita:last-child {
            margin-bottom: 0;
        }

        .ag-cita:hover {
            transform: translateX(2px);
        }

        .ag-cita--prog {
            border-left-color: #0e7a5f;
        }

        .ag-cita--conf {
            border-left-color: var(--blue);
        }

        .ag-cita--proc {
            border-left-color: var(--warning);
        }

        .ag-cita--canc {
            border-left-color: var(--danger);
            opacity: .7;
        }

        .ag-cita__hora {
            font-size: 13px;
            font-weight: 800;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ag-cita__dur {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .ag-cita__pac {
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            margin: 4px 0 2px;
        }

        .ag-cita__srv {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .ag-cita__rec {
            font-size: 12px;
            font-weight: 600;
            color: var(--cream-dark);
            margin-top: 1px;
        }

        .ag-cita__badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 100px;
            margin-top: 7px;
        }

        .ag-cita__badge--prog {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .ag-cita__badge--conf {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .ag-cita__badge--proc {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .ag-cita__badge--canc {
            background: rgba(220, 38, 38, .10);
            color: var(--danger);
        }

        .ag-panel-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .ag-panel-empty i {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
            opacity: .4;
        }

        .ag-panel-empty p {
            font-size: 13px;
            margin: 0;
        }

        .ag-leyenda {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: var(--navy);
        }

        .ag-ley-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .ag-ley-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .ag-ley-dot--prog {
            background: #0e7a5f;
        }

        .ag-ley-dot--conf {
            background: var(--blue);
        }

        .ag-ley-dot--proc {
            background: var(--warning);
        }

        .ag-ley-dot--canc {
            background: var(--danger);
        }

        .ag-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .ag-spinner {
            width: 28px;
            height: 28px;
            border: 3px solid var(--border);
            border-top-color: var(--blue);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width:1024px) {
            .ag-layout {
                grid-template-columns: 1fr;
            }

            .ag-panel {
                position: static;
            }
        }

        @media (max-width:640px) {
            .ag-page {
                padding: 0 14px 60px;
            }

            .ag-header {
                padding: 28px 0 20px;
            }

            .ag-day {
                min-height: 64px;
                padding: 4px;
            }

            .ag-chip {
                display: none;
            }

            .ag-stats {
                flex-direction: column;
            }
        }
    </style>

    <div class="ag-page">

        {{-- Header --}}
        <div class="ag-header">
            <div>
                <div class="ag-kicker">PATS · Módulo de agenda</div>
                <h1 class="ag-title">Mis <span>citas</span></h1>
                <p class="ag-subtitle">Citas agendadas — {{ ucfirst($nombreMes) }}</p>
            </div>
            <div class="ag-fecha-badge">
                <div class="ag-fecha-badge__dia">Hoy</div>
                <div class="ag-fecha-badge__full">{{ ucfirst($fecha_display) }}</div>
                <div class="ag-fecha-badge__hora" id="reloj">{{ $hora_actual }}</div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="ag-stats">
            <div class="ag-stat">
                <div class="ag-stat__dot ag-stat__dot--prog"></div>
                <div>
                    <div class="ag-stat__num">{{ $totalDisp }}</div>
                    <div class="ag-stat__label">Programadas</div>
                </div>
            </div>
            <div class="ag-stat">
                <div class="ag-stat__dot ag-stat__dot--conf"></div>
                <div>
                    <div class="ag-stat__num">{{ $totalReserv }}</div>
                    <div class="ag-stat__label">Confirmadas</div>
                </div>
            </div>
            <div class="ag-stat">
                <div class="ag-stat__dot ag-stat__dot--canc"></div>
                <div>
                    <div class="ag-stat__num">{{ $totalBloq }}</div>
                    <div class="ag-stat__label">Canceladas</div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form class="ag-filters" method="GET" action="{{ route('agenda.index') }}">
            <input type="hidden" name="anio" value="{{ $anio }}">
            <input type="hidden" name="mes" value="{{ $mes }}">
            <span class="ag-filter-label">Filtrar:</span>
            <select name="servicio" onchange="this.form.submit()">
                <option value="">Todos los servicios</option>
                @foreach ($servicios as $srv)
                    <option value="{{ $srv->id_servicio }}" {{ $filtroServicio == $srv->id_servicio ? 'selected' : '' }}>
                        {{ $srv->servicio }}
                    </option>
                @endforeach
            </select>
            <select name="recurso" onchange="this.form.submit()">
                <option value="">Todos los recursos</option>
                @foreach ($recursos as $rec)
                    <option value="{{ $rec->id_recurso }}" {{ $filtroRecurso == $rec->id_recurso ? 'selected' : '' }}>
                        {{ $rec->nombre_recurso }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="ag-layout">

            {{-- Calendario --}}
            <div>
                <div class="ag-calendar">
                    <div class="ag-cal-nav">
                        @if ($puedeRetroceder)
                            <a href="{{ route('agenda.index', array_merge(request()->query(), ['anio' => $mesPrev->year, 'mes' => $mesPrev->month])) }}"
                                class="ag-nav-btn" title="Mes anterior">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                        @else
                            <span class="ag-nav-btn ag-nav-btn--disabled"><i class="mdi mdi-chevron-left"></i></span>
                        @endif

                        <span class="ag-cal-nav__mes">{{ ucfirst($mesNav->isoFormat('MMMM YYYY')) }}</span>

                        @if ($puedeAvanzar)
                            <a href="{{ route('agenda.index', array_merge(request()->query(), ['anio' => $mesSig->year, 'mes' => $mesSig->month])) }}"
                                class="ag-nav-btn" title="Mes siguiente">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        @else
                            <span class="ag-nav-btn ag-nav-btn--disabled"><i class="mdi mdi-chevron-right"></i></span>
                        @endif
                    </div>

                    <div class="ag-cal-weekdays">
                        @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia)
                            <span>{{ $dia }}</span>
                        @endforeach
                    </div>

                    <div class="ag-cal-grid">
                        @for ($i = 0; $i < $offsetInicio; $i++)
                            <div class="ag-day ag-day--vacio"></div>
                        @endfor

                        @for ($d = 1; $d <= $diasMes; $d++)
                            @php
                                $fechaDia = Carbon::createFromDate($anio, $mes, $d)->toDateString();
                                $esPasado = $fechaDia < $fecha_hoy;
                                $esHoy = $fechaDia === $fecha_hoy;
                                $esFinSem = Carbon::parse($fechaDia)->isWeekend();
                                $citasDay = $bloquesPorFecha->get($fechaDia, collect());
                                $progDay = $citasDay->where('estatus', 'PROGRAMADO')->count();
                                $confDay = $citasDay->where('estatus', 'CONFIRMADO')->count();
                                $procDay = $citasDay->where('estatus', 'EN_PROCESO')->count();
                                $cancDay = $citasDay->where('estatus', 'CANCELADO')->count();
                                $totalDay = $citasDay->count();
                            @endphp

                            <div class="ag-day
                        {{ $esPasado ? 'ag-day--pasado' : '' }}
                        {{ $esHoy ? 'ag-day--hoy' : '' }}
                        {{ $esFinSem ? 'ag-day--fin-sem' : '' }}"
                                data-fecha="{{ $fechaDia }}" onclick="{{ !$esPasado ? 'selectDay(this)' : '' }}"
                                title="{{ $totalDay }} cita{{ $totalDay !== 1 ? 's' : '' }}">

                                <div class="ag-day__num">{{ $d }}</div>

                                @if ($totalDay > 0)
                                    <div class="ag-day__chips">
                                        @if ($progDay > 0)
                                            <span class="ag-chip ag-chip--prog">
                                                <i class="mdi mdi-calendar-check" style="font-size:9px;"></i>
                                                {{ $progDay }} prog.
                                            </span>
                                        @endif
                                        @if ($confDay > 0)
                                            <span class="ag-chip ag-chip--conf">{{ $confDay }} conf.</span>
                                        @endif
                                        @if ($procDay > 0)
                                            <span class="ag-chip ag-chip--proc">{{ $procDay }} en proc.</span>
                                        @endif
                                        @if ($cancDay > 0)
                                            <span class="ag-chip ag-chip--canc">{{ $cancDay }} canc.</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endfor

                        @php
                            $total = $offsetInicio + $diasMes;
                            $resto = $total % 7 === 0 ? 0 : 7 - ($total % 7);
                        @endphp
                        @for ($i = 0; $i < $resto; $i++)
                            <div class="ag-day ag-day--vacio"></div>
                        @endfor
                    </div>

                    <div class="ag-leyenda">
                        <div class="ag-ley-item">
                            <div class="ag-ley-dot ag-ley-dot--prog"></div>Programada
                        </div>
                        <div class="ag-ley-item">
                            <div class="ag-ley-dot ag-ley-dot--conf"></div>Confirmada
                        </div>
                        <div class="ag-ley-item">
                            <div class="ag-ley-dot ag-ley-dot--proc"></div>En proceso
                        </div>
                        <div class="ag-ley-item">
                            <div class="ag-ley-dot ag-ley-dot--canc"></div>Cancelada
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel lateral --}}
            <div class="ag-panel">
                <div class="ag-panel__head">
                    <p class="ag-panel__fecha" id="panelTitulo">Selecciona un día</p>
                    <p class="ag-panel__sub" id="panelSub">Haz clic en una fecha del calendario</p>
                </div>
                <div class="ag-panel__body" id="panelBody">
                    <div class="ag-panel-empty">
                        <i class="mdi mdi-calendar-blank"></i>
                        <p>Selecciona un día para ver las citas agendadas</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            'use strict';

            // ── Reloj ─────────────────────────────────────
            const reloj = document.getElementById('reloj');

            function tick() {
                const n = new Date();
                reloj.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
            }
            tick();
            setInterval(tick, 30000);

            // ── Selección de día ──────────────────────────
            window.selectDay = function(el) {
                if (el.classList.contains('ag-day--pasado')) return;
                document.querySelectorAll('.ag-day--activo').forEach(d => d.classList.remove('ag-day--activo'));
                el.classList.add('ag-day--activo');
                cargarDia(el.dataset.fecha);
            };

            const hoyEl = document.querySelector('.ag-day--hoy');
            if (hoyEl && !hoyEl.classList.contains('ag-day--vacio')) selectDay(hoyEl);

            // ── Cargar citas del día vía JSON ─────────────
            function cargarDia(fecha) {
                const titulo = document.getElementById('panelTitulo');
                const sub = document.getElementById('panelSub');
                const body = document.getElementById('panelBody');

                const d = new Date(fecha + 'T12:00:00');
                const leg = d.toLocaleDateString('es-MX', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long'
                });
                titulo.textContent = leg.charAt(0).toUpperCase() + leg.slice(1);
                body.innerHTML = `<div class="ag-loading"><div class="ag-spinner"></div></div>`;

                const p = new URLSearchParams(window.location.search);
                const srv = p.get('servicio') || '';
                const rec = p.get('recurso') || '';
                const url = `{{ route('agenda.dia', ['fecha' => '__F__']) }}`.replace('__F__', fecha) +
                    (srv ? `?servicio=${srv}` : '') +
                    (rec ? `${srv ? '&' : '?'}recurso=${rec}` : '');

                fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => renderPanel(data, body, sub))
                    .catch(() => {
                        body.innerHTML =
                            `<div class="ag-panel-empty"><i class="mdi mdi-alert-circle-outline"></i><p>Error al cargar. Intenta de nuevo.</p></div>`;
                    });
            }

            // ── Mapa estatus → clase CSS ──────────────────
            const clsMap = {
                PROGRAMADO: 'prog',
                CONFIRMADO: 'conf',
                EN_PROCESO: 'proc',
                CANCELADO: 'canc',
            };

            // ── Renderizar panel con citas reales ─────────
            function renderPanel(data, body, sub) {
                const {
                    bloques,
                    total
                } = data;

                sub.textContent = total === 0 ?
                    'Sin citas este día' :
                    `${total} cita${total !== 1 ? 's' : ''} agendada${total !== 1 ? 's' : ''}`;

                if (!bloques || bloques.length === 0) {
                    body.innerHTML = `
        <div class="ag-panel-empty">
          <i class="mdi mdi-calendar-check"></i>
          <p>No hay citas agendadas para este día.</p>
        </div>`;
                    return;
                }

                body.innerHTML = bloques.map(b => {
                    const cls = clsMap[b.estatus] || 'prog';
                    const nombre = b.nombre_paciente || '—';
                    const horaFin = b.hora_fin ? ` – ${b.hora_fin}` : '';
                    const dur = b.duracion_min ? `<span class="ag-cita__dur">${b.duracion_min} min</span>` : '';
                    const recurso = b.nombre_recurso ?
                        `<div class="ag-cita__rec">${b.nombre_recurso}${b.especialidad ? ' · ' + b.especialidad : ''}</div>` :
                        '';

                    return `
        <div class="ag-cita ag-cita--${cls}">
          <div class="ag-cita__hora">
            <i class="mdi mdi-clock-outline" style="font-size:15px;color:var(--blue);opacity:.7;"></i>
            ${b.hora_inicio}${horaFin} ${dur}
          </div>
          <div class="ag-cita__pac">${nombre}</div>
          <div class="ag-cita__srv">${b.servicio || ''}</div>
          ${recurso}
          <span class="ag-cita__badge ag-cita__badge--${cls}">
            ${b.estatus}
          </span>
        </div>`;
                }).join('');
            }

        })();
    </script>
@endsection
