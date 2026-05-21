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

        /* ── Layout ─────────────────────────────────── */
        .rx-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
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
                padding: 0 16px 60px;
            }

            .rx-header {
                padding: 28px 0 22px;
            }

            .rx-stats {
                flex-direction: column;
            }
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
                <div class="rx-stat__icon rx-stat__icon--rec">
                    <i class="mdi mdi-radioactive"></i>
                </div>
                <div>
                    <div class="rx-stat__num">{{ $recursos->count() }}</div>
                    <div class="rx-stat__label">Equipos activos</div>
                </div>
            </div>
            <div class="rx-stat">
                <div class="rx-stat__icon rx-stat__icon--disp">
                    <i class="mdi mdi-calendar-check"></i>
                </div>
                <div>
                    <div class="rx-stat__num">{{ $agendaHoy->where('tipo_bloque', 'DISPONIBLE')->count() }}</div>
                    <div class="rx-stat__label">Libres hoy</div>
                </div>
            </div>
            <div class="rx-stat">
                <div class="rx-stat__icon rx-stat__icon--scan">
                    <i class="mdi mdi-clock-outline"></i>
                </div>
                <div>
                    <div class="rx-stat__num">{{ $agendaHoy->where('tipo_bloque', 'RESERVADO')->count() }}</div>
                    <div class="rx-stat__label">Reservados hoy</div>
                </div>
            </div>
            <div class="rx-stat">
                <div class="rx-stat__icon rx-stat__icon--hoy">
                    <i class="mdi mdi-account-clock"></i>
                </div>
                <div>
                    <div class="rx-stat__num">{{ $citasHoy->count() }}</div>
                    <div class="rx-stat__label">Citas hoy</div>
                </div>
            </div>
        </div>

        <div class="rx-layout">

            {{-- COL IZQUIERDA ───────────────────────────────── --}}
            <div>

                {{-- Equipos / recursos de imagen --}}
                <div class="rx-card">
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
                </div>

                {{-- Agenda de HOY (timeline) --}}
                <div class="rx-card">
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
                </div>

                {{-- Disponibilidad próximos días --}}
                @if ($proximaDisp->isNotEmpty())
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
                @endif

            </div>{{-- /col izq --}}

            {{-- PANEL DERECHO ───────────────────────────── --}}
            <div class="rx-panel">

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

            </div>{{-- /rx-panel --}}

        </div>{{-- /rx-layout --}}
    </div>{{-- /rx-wrap --}}

    <script>
        const reloj = document.getElementById('reloj');
        if (reloj) {
            function tick() {
                const n = new Date();
                reloj.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
            }
            tick();
            setInterval(tick, 30000);
        }
    </script>
@endsection
