{{-- resources/views/servicios/estudios-clinicos.blade.php --}}
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Estudios Clínicos')

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

        .ec-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 28px 80px;
        }

        /* ── Header ─────────────────────────────────── */
        .ec-header {
            padding: 48px 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 32px;
        }

        .ec-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 8px;
        }

        .ec-title {
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: var(--cream);
            letter-spacing: -.02em;
            margin: 0 0 6px;
        }

        .ec-title span {
            color: var(--blue);
        }

        .ec-subtitle {
            font-size: 14.5px;
            color: var(--text-muted);
            margin: 0;
        }

        .ec-fecha-badge {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 20px;
            text-align: right;
            box-shadow: var(--shadow);
        }

        .ec-fecha-badge__dia {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            font-weight: 700;
        }

        .ec-fecha-badge__full {
            font-size: 13px;
            font-weight: 700;
            color: var(--cream);
            margin-top: 2px;
        }

        .ec-fecha-badge__hora {
            font-size: 22px;
            font-weight: 800;
            color: var(--blue);
            font-variant-numeric: tabular-nums;
        }

        /* ── Stats ──────────────────────────────────── */
        .ec-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .ec-stat {
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

        .ec-stat__icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .ec-stat__icon--lab {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .ec-stat__icon--img {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .ec-stat__icon--hoy {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .ec-stat__num {
            font-size: 26px;
            font-weight: 800;
            color: var(--cream);
            line-height: 1;
        }

        .ec-stat__label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-top: 3px;
        }

        /* ── Buscador ───────────────────────────────── */
        .ec-search-wrap {
            position: relative;
            margin-bottom: 24px;
        }

        .ec-search {
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

        .ec-search:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }

        .ec-search::placeholder {
            color: var(--text-muted);
        }

        .ec-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 20px;
            pointer-events: none;
        }

        .ec-search-clear {
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

        .ec-search-clear.visible {
            display: flex;
        }

        .ec-search-clear:hover {
            background: var(--navy-light);
            color: var(--blue);
        }

        /* ── Tabs ───────────────────────────────────── */
        .ec-tabs-wrap {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .ec-tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border);
            background: var(--navy-light);
        }

        .ec-tab-btn {
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

        .ec-tab-btn i {
            font-size: 18px;
        }

        .ec-tab-btn:hover {
            color: var(--blue);
        }

        .ec-tab-btn.is-active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            background: var(--white);
        }

        .ec-tab-btn .ec-tab-count {
            font-size: 11px;
            font-weight: 700;
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
            padding: 2px 8px;
            border-radius: 100px;
        }

        .ec-tab-btn.is-active .ec-tab-count {
            background: var(--blue);
            color: #fff;
        }

        /* Panel de cada tab */
        .ec-tab-panel {
            display: none;
            padding: 20px 24px;
        }

        .ec-tab-panel.is-active {
            display: block;
        }

        /* ── Estudios lista ─────────────────────────── */
        .ec-estudio {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 13px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 8px;
            transition: border-color .15s, background .15s;
            background: var(--navy);
        }

        .ec-estudio:last-child {
            margin-bottom: 0;
        }

        .ec-estudio:hover {
            border-color: rgba(37, 99, 235, .28);
            background: rgba(37, 99, 235, .03);
        }

        .ec-estudio.hidden {
            display: none;
        }

        .ec-estudio__icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 17px;
        }

        .ec-estudio__body {
            flex: 1;
            min-width: 0;
        }

        .ec-estudio__nombre {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 4px;
        }

        .ec-estudio__prep {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        .ec-estudio__meta {
            display: flex;
            gap: 8px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .ec-pill {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ec-pill--dur {
            background: rgba(37, 99, 235, .09);
            color: var(--blue);
        }

        .ec-pill--cita {
            background: rgba(217, 119, 6, .10);
            color: var(--warning);
        }

        .ec-pill--libre {
            background: rgba(14, 122, 95, .09);
            color: #0e7a5f;
        }

        /* ── Disponibilidad de horarios ─────────────── */
        .ec-disp-section {
            margin-top: 20px;
            border-top: 1px solid var(--border);
            padding-top: 18px;
        }

        .ec-disp-title {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ec-disp-title i {
            color: var(--blue);
            font-size: 15px;
        }

        .ec-fecha-group {
            margin-bottom: 14px;
        }

        .ec-fecha-label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .ec-fecha-label i {
            color: var(--blue-light);
            font-size: 14px;
        }

        .ec-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .ec-slot {
            padding: 7px 13px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            color: var(--cream);
            transition: border-color .15s;
        }

        .ec-slot:hover {
            border-color: var(--blue);
        }

        .ec-slot__hora {
            display: block;
        }

        .ec-slot__rec {
            display: block;
            font-size: 10px;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .ec-disp-empty {
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            padding: 20px;
        }

        .ec-disp-empty i {
            font-size: 30px;
            display: block;
            margin-bottom: 6px;
            opacity: .3;
        }

        /* ── Citas de hoy (panel lateral) ───────────── */
        .ec-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            align-items: start;
        }

        .ec-panel-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: sticky;
            top: 24px;
        }

        .ec-panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ec-panel-head__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
        }

        .ec-panel-head__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .ec-panel-body {
            padding: 14px 18px;
            max-height: 560px;
            overflow-y: auto;
        }

        .ec-cita-row {
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

        .ec-cita-row:last-child {
            margin-bottom: 0;
        }

        .ec-cita-row:hover {
            background: var(--navy);
        }

        .ec-cita-row__hora {
            font-weight: 800;
            color: var(--blue);
            font-size: 12.5px;
            min-width: 44px;
        }

        .ec-cita-row__pac {
            font-weight: 600;
            color: var(--cream);
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 12.5px;
        }

        .ec-cita-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            flex-shrink: 0;
        }

        .ec-cita-badge--lab {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .ec-cita-badge--img {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .ec-panel-empty {
            padding: 32px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
        }

        .ec-panel-empty i {
            font-size: 34px;
            display: block;
            margin-bottom: 8px;
            opacity: .3;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width:960px) {
            .ec-layout {
                grid-template-columns: 1fr;
            }

            .ec-panel-card {
                position: static;
            }
        }

        @media (max-width:600px) {
            .ec-wrap {
                padding: 0 16px 60px;
            }

            .ec-header {
                padding: 28px 0 22px;
            }

            .ec-stats {
                flex-direction: column;
            }

            .ec-tabs-nav {
                flex-direction: column;
            }
        }
    </style>

    <div class="ec-wrap">

        {{-- Header ─────────────────────────────────────── --}}
        <div class="ec-header">
            <div>
                <div class="ec-kicker">PATS · Diagnóstico clínico</div>
                <h1 class="ec-title">Estudios <span>clínicos</span></h1>
                <p class="ec-subtitle">Laboratorio e imagenología — catálogo de estudios y disponibilidad</p>
            </div>
            <div class="ec-fecha-badge">
                <div class="ec-fecha-badge__dia">Hoy</div>
                <div class="ec-fecha-badge__full">{{ ucfirst($fecha['fecha_display']) }}</div>
                <div class="ec-fecha-badge__hora" id="reloj">{{ $fecha['hora_actual'] }}</div>
            </div>
        </div>

        {{-- Layout ──────────────────────────────────────── --}}
        <div class="ec-layout">

            {{-- COL IZQUIERDA ───────────────────────────────── --}}
            <div>

                {{-- Stats --}}
                <div class="ec-stats">
                    <div class="ec-stat">
                        <div class="ec-stat__icon ec-stat__icon--lab">
                            <i class="mdi mdi-flask"></i>
                        </div>
                        <div>
                            <div class="ec-stat__num">{{ $estudiosLab->count() }}</div>
                            <div class="ec-stat__label">Estudios Lab</div>
                        </div>
                    </div>
                    {{-- <div class="ec-stat">
                        <div class="ec-stat__icon ec-stat__icon--img">
                            <i class="mdi mdi-scan-helper"></i>
                        </div>
                        <div>
                            <div class="ec-stat__num">{{ $estudiosImagen->count() }}</div>
                            <div class="ec-stat__label">Imagenología</div>
                        </div>
                    </div> --}}
                    <div class="ec-stat">
                        <div class="ec-stat__icon ec-stat__icon--lab"
                            style="background:rgba(37,99,235,.10);color:var(--blue);">
                            <i class="mdi mdi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="ec-stat__num">{{ $totalBloqLab + $totalBloqImagen }}</div>
                            <div class="ec-stat__label">Horarios disp.</div>
                        </div>
                    </div>
                    <div class="ec-stat">
                        <div class="ec-stat__icon ec-stat__icon--hoy">
                            <i class="mdi mdi-clock-outline"></i>
                        </div>
                        <div>
                            <div class="ec-stat__num">{{ $citasHoy->count() }}</div>
                            <div class="ec-stat__label">Citas hoy</div>
                        </div>
                    </div>
                </div>

                {{-- Buscador --}}
                <div class="ec-search-wrap">
                    <i class="mdi mdi-magnify ec-search-icon"></i>
                    <input type="text" id="ecSearch" class="ec-search"
                        placeholder="Buscar estudio (ej: Biometría, TAC, Ultrasonido...)" autocomplete="off">
                    <button class="ec-search-clear" id="ecClear">
                        <i class="mdi mdi-close" style="font-size:15px;"></i>
                    </button>
                </div>

                {{-- Tabs Lab / Imagen --}}
                <div class="ec-tabs-wrap">
                    <div class="ec-tabs-nav">
                        <button class="ec-tab-btn is-active" data-tab="lab">
                            <i class="mdi mdi-flask"></i>
                            Laboratorio
                            <span class="ec-tab-count">{{ $estudiosLab->count() }}</span>
                        </button>
                        {{-- <button class="ec-tab-btn" data-tab="imagen">
                            <i class="mdi mdi-scan-helper"></i>
                            Imagenología
                            <span class="ec-tab-count">{{ $estudiosImagen->count() }}</span>
                        </button> --}}
                    </div>

                    {{-- ── PANEL LABORATORIO ── --}}
                    <div class="ec-tab-panel is-active" id="panel-lab">

                        {{-- Lista de estudios --}}
                        @forelse($estudiosLab as $est)
                            <div class="ec-estudio" data-nombre="{{ strtolower($est->nombre_estudio) }}">
                                <div class="ec-estudio__icon">
                                    <i class="mdi mdi-test-tube"></i>
                                </div>
                                <div class="ec-estudio__body">
                                    <p class="ec-estudio__nombre">{{ $est->nombre_estudio }}</p>
                                    @if ($est->preparacion_resumen)
                                        <p class="ec-estudio__prep">
                                            <i class="mdi mdi-information-outline"
                                                style="font-size:13px;color:var(--blue);"></i>
                                            {{ $est->preparacion_resumen }}
                                        </p>
                                    @endif
                                    <div class="ec-estudio__meta">
                                        <span class="ec-pill ec-pill--dur">
                                            <i class="mdi mdi-clock-outline" style="font-size:11px;"></i>
                                            {{ $est->duracion_min }} min
                                        </span>
                                        @if ($est->requiere_cita)
                                            <span class="ec-pill ec-pill--cita">
                                                <i class="mdi mdi-calendar" style="font-size:11px;"></i>
                                                Requiere cita
                                            </span>
                                        @else
                                            <span class="ec-pill ec-pill--libre">
                                                <i class="mdi mdi-check" style="font-size:11px;"></i>
                                                Sin cita
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ec-disp-empty">
                                <i class="mdi mdi-flask"></i>
                                Sin estudios de laboratorio registrados.
                            </div>
                        @endforelse

                        {{-- Disponibilidad lab --}}
                        <div class="ec-disp-section">
                            <div class="ec-disp-title">
                                <i class="mdi mdi-calendar-clock"></i>
                                Próximos horarios disponibles — Laboratorio
                            </div>
                            @forelse($proximoLab as $fechaStr => $bloques)
                                @php $carbon = Carbon::parse($fechaStr); @endphp
                                <div class="ec-fecha-group">
                                    <div class="ec-fecha-label">
                                        <i class="mdi mdi-calendar-outline"></i>
                                        {{ ucfirst($carbon->isoFormat('dddd D [de] MMMM')) }}
                                        @if ($fechaStr === $fecha['fecha_hoy'])
                                            · <strong style="color:var(--blue)">Hoy</strong>
                                        @endif
                                    </div>
                                    <div class="ec-slots">
                                        @foreach ($bloques->sortBy('fecha_inicio') as $bloque)
                                            <div class="ec-slot">
                                                <span
                                                    class="ec-slot__hora">{{ Carbon::parse($bloque->fecha_inicio)->format('H:i') }}</span>
                                                <span
                                                    class="ec-slot__rec">{{ \Illuminate\Support\Str::limit($bloque->nombre_recurso, 16) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="ec-disp-empty">
                                    <i class="mdi mdi-calendar-remove"></i>
                                    Sin horarios disponibles próximamente.
                                </div>
                            @endforelse
                        </div>

                    </div>{{-- /panel-lab --}}

                    {{-- ── PANEL IMAGENOLOGÍA ── --}}
                    <div class="ec-tab-panel" id="panel-imagen">

                        @forelse($estudiosImagen as $est)
                            <div class="ec-estudio" data-nombre="{{ strtolower($est->nombre_estudio) }}">
                                <div class="ec-estudio__icon" style="color:#28c7e8;">
                                    <i
                                        class="mdi mdi-{{ str_contains(strtolower($est->nombre_estudio), 'tac') ? 'circle-slice-8' : (str_contains(strtolower($est->nombre_estudio), 'ultra') ? 'waves' : 'radioactive') }}"></i>
                                </div>
                                <div class="ec-estudio__body">
                                    <p class="ec-estudio__nombre">{{ $est->nombre_estudio }}</p>
                                    @if ($est->preparacion_resumen)
                                        <p class="ec-estudio__prep">
                                            <i class="mdi mdi-information-outline"
                                                style="font-size:13px;color:var(--blue);"></i>
                                            {{ $est->preparacion_resumen }}
                                        </p>
                                    @endif
                                    <div class="ec-estudio__meta">
                                        <span class="ec-pill ec-pill--dur">
                                            <i class="mdi mdi-clock-outline" style="font-size:11px;"></i>
                                            {{ $est->duracion_min }} min
                                        </span>
                                        @if ($est->requiere_cita)
                                            <span class="ec-pill ec-pill--cita">
                                                <i class="mdi mdi-calendar" style="font-size:11px;"></i>
                                                Requiere cita
                                            </span>
                                        @else
                                            <span class="ec-pill ec-pill--libre">
                                                <i class="mdi mdi-check" style="font-size:11px;"></i>
                                                Sin cita
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ec-disp-empty">
                                <i class="mdi mdi-scan-helper"></i>
                                Sin estudios de imagen registrados.
                            </div>
                        @endforelse

                        {{-- Disponibilidad imagen --}}
                        <div class="ec-disp-section">
                            <div class="ec-disp-title">
                                <i class="mdi mdi-calendar-clock"></i>
                                Próximos horarios disponibles — Imagenología
                            </div>
                            @forelse($proximoImagen as $fechaStr => $bloques)
                                @php $carbon = Carbon::parse($fechaStr); @endphp
                                <div class="ec-fecha-group">
                                    <div class="ec-fecha-label">
                                        <i class="mdi mdi-calendar-outline"></i>
                                        {{ ucfirst($carbon->isoFormat('dddd D [de] MMMM')) }}
                                        @if ($fechaStr === $fecha['fecha_hoy'])
                                            · <strong style="color:var(--blue)">Hoy</strong>
                                        @endif
                                    </div>
                                    <div class="ec-slots">
                                        @foreach ($bloques->sortBy('fecha_inicio') as $bloque)
                                            <div class="ec-slot">
                                                <span
                                                    class="ec-slot__hora">{{ Carbon::parse($bloque->fecha_inicio)->format('H:i') }}</span>
                                                <span
                                                    class="ec-slot__rec">{{ \Illuminate\Support\Str::limit($bloque->nombre_recurso, 16) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="ec-disp-empty">
                                    <i class="mdi mdi-calendar-remove"></i>
                                    Sin horarios disponibles próximamente.
                                </div>
                            @endforelse
                        </div>

                    </div>{{-- /panel-imagen --}}
                </div>{{-- /tabs-wrap --}}

                {{-- Link a agenda completa --}}
                <a href="{{ route('agenda.index') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:var(--blue);color:#fff;border-radius:var(--radius);font-size:14px;font-weight:700;text-decoration:none;transition:background .15s;box-shadow:0 4px 16px rgba(37,99,235,.22);">
                    <i class="mdi mdi-calendar-month" style="font-size:18px;"></i>
                    Ver calendario completo de estudios
                </a>

            </div>{{-- /col izq --}}

            {{-- COL DERECHA: Citas de hoy ───────────────── --}}
            <div>
                <div class="ec-panel-card">
                    <div class="ec-panel-head">
                        <i class="mdi mdi-calendar-today" style="color:var(--blue);font-size:22px;"></i>
                        <div>
                            <div class="ec-panel-head__title">Citas de hoy</div>
                            <div class="ec-panel-head__sub">
                                {{ ucfirst(Carbon::parse($fecha['fecha_hoy'])->isoFormat('dddd D [de] MMMM')) }}
                            </div>
                        </div>
                    </div>
                    <div class="ec-panel-body">
                        @forelse($citasHoy as $cita)
                            @php
                                $esLab = $cita->id_servicio == $idLab;
                            @endphp
                            <div class="ec-cita-row">
                                <div class="ec-cita-row__hora">
                                    {{ \Illuminate\Support\Str::limit($cita->hora_inicio, 5, '') }}
                                </div>
                                <div class="ec-cita-row__pac">{{ $cita->nombre_paciente }}</div>
                                <span class="ec-cita-badge {{ $esLab ? 'ec-cita-badge--lab' : 'ec-cita-badge--img' }}">
                                    {{ $esLab ? 'LAB' : 'IMG' }}
                                </span>
                            </div>
                        @empty
                            <div class="ec-panel-empty">
                                <i class="mdi mdi-calendar-blank"></i>
                                Sin citas de estudios para hoy.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recursos disponibles --}}
                <div class="ec-panel-card" style="margin-top:16px;">
                    <div class="ec-panel-head">
                        <i class="mdi mdi-hospital-box" style="color:var(--blue);font-size:22px;"></i>
                        <div>
                            <div class="ec-panel-head__title">Recursos disponibles</div>
                            <div class="ec-panel-head__sub">Unidades activas en la red</div>
                        </div>
                    </div>
                    <div class="ec-panel-body">
                        @foreach ($recursosLab->merge($recursosImagen) as $rec)
                            <div class="ec-cita-row" style="gap:12px;">
                                <i class="mdi mdi-{{ $rec->tipo_recurso === 'LABORATORIO' ? 'flask' : 'radioactive' }}"
                                    style="color:var(--blue);font-size:18px;flex-shrink:0;"></i>
                                <div style="flex:1;min-width:0;">
                                    <div
                                        style="font-size:12.5px;font-weight:700;color:var(--cream);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $rec->nombre_recurso }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $rec->tipo_recurso }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>{{-- /ec-layout --}}
    </div>{{-- /ec-wrap --}}

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

            // ── Tabs ──────────────────────────────────────
            document.querySelectorAll('.ec-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.ec-tab-btn').forEach(b => b.classList.remove(
                        'is-active'));
                    document.querySelectorAll('.ec-tab-panel').forEach(p => p.classList.remove(
                        'is-active'));
                    btn.classList.add('is-active');
                    document.getElementById(`panel-${btn.dataset.tab}`).classList.add('is-active');
                    // Limpiar búsqueda al cambiar tab
                    const inp = document.getElementById('ecSearch');
                    if (inp.value) {
                        inp.value = '';
                        document.getElementById('ecClear').classList.remove('visible');
                        filterEstudios('');
                    }
                });
            });

            // ── Búsqueda ──────────────────────────────────
            function filterEstudios(term) {
                const activePanel = document.querySelector('.ec-tab-panel.is-active');
                activePanel.querySelectorAll('.ec-estudio').forEach(el => {
                    el.classList.toggle('hidden', term && !el.dataset.nombre.includes(term));
                });
            }

            const inp = document.getElementById('ecSearch');
            const clear = document.getElementById('ecClear');

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

        })();
    </script>
@endsection
