{{-- resources/views/servicios/especialidades.blade.php --}}
@extends('layouts.app')

@section('title', 'Consulta por especialidad')

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
            --border: rgba(37, 99, 235, .12);
            --danger: #dc2626;
            --radius: 14px;
            --radius-sm: 9px;
            --radius-lg: 20px;
            --shadow: 0 4px 24px rgba(37, 99, 235, .08);
        }

        /* ── Layout ─────────────────────────────────── */
        .esp-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 28px 80px;
        }

        /* ── Header ─────────────────────────────────── */
        .esp-header {
            padding: 48px 0 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 36px;
        }

        .esp-header__left {}

        .esp-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 10px;
            opacity: .7;
        }

        .esp-title {
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: var(--cream);
            line-height: 1.1;
            letter-spacing: -.02em;
            margin: 0 0 8px;
        }

        .esp-title span {
            color: var(--blue);
        }

        .esp-subtitle {
            font-size: 14.5px;
            color: var(--text-muted);
            margin: 0;
        }

        /* Fecha badge */
        .esp-fecha {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 20px;
            text-align: right;
            box-shadow: var(--shadow);
        }

        .esp-fecha__dia {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            font-weight: 600;
        }

        .esp-fecha__full {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
            margin-top: 2px;
        }

        .esp-fecha__hora {
            font-size: 22px;
            font-weight: 800;
            color: var(--blue);
            font-variant-numeric: tabular-nums;
            margin-top: 2px;
        }

        /* ── Stats row ──────────────────────────────── */
        .esp-stats {
            display: flex;
            gap: 14px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .esp-stat {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 22px;
            flex: 1;
            min-width: 160px;
            box-shadow: var(--shadow);
        }

        .esp-stat__num {
            font-size: 28px;
            font-weight: 800;
            color: var(--blue);
            line-height: 1;
        }

        .esp-stat__label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 4px;
        }

        /* ── Buscador ───────────────────────────────── */
        .esp-search-wrap {
            position: relative;
            margin-bottom: 32px;
        }

        .esp-search {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 15px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--text);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
            box-shadow: var(--shadow);
        }

        .esp-search:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }

        .esp-search::placeholder {
            color: var(--text-muted);
        }

        .esp-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 20px;
            pointer-events: none;
        }

        .esp-search-clear {
            position: absolute;
            right: 14px;
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

        .esp-search-clear.visible {
            display: flex;
        }

        .esp-search-clear:hover {
            background: var(--navy-light);
            color: var(--blue);
        }

        /* Dropdown sugerencias */
        .esp-suggestions {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 8px 32px rgba(37, 99, 235, .12);
            z-index: 100;
            max-height: 280px;
            overflow-y: auto;
            display: none;
        }

        .esp-suggestions.open {
            display: block;
        }

        .esp-sug-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .esp-sug-item:last-child {
            border-bottom: none;
        }

        .esp-sug-item:hover {
            background: var(--navy);
        }

        .esp-sug-item__icon {
            color: var(--blue);
            font-size: 18px;
            flex-shrink: 0;
        }

        .esp-sug-item__name {
            font-size: 14px;
            font-weight: 500;
            color: var(--cream);
        }

        .esp-sug-item__count {
            margin-left: auto;
            font-size: 11.5px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* ── Grid de especialidades ─────────────────── */
        .esp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .esp-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s, border-color .18s;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .esp-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--blue), var(--blue-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .22s ease;
        }

        .esp-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, .14);
            border-color: rgba(37, 99, 235, .30);
        }

        .esp-card:hover::after {
            transform: scaleX(1);
        }

        .esp-card__icon {
            width: 52px;
            height: 52px;
            background: rgba(37, 99, 235, .08);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 24px;
            margin-bottom: 14px;
            transition: background .18s;
        }

        .esp-card:hover .esp-card__icon {
            background: rgba(37, 99, 235, .14);
        }

        .esp-card__esp {
            font-size: 16px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 6px;
            line-height: 1.3;
        }

        .esp-card__row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .esp-card__row i {
            color: var(--blue);
            font-size: 15px;
        }

        /* Pill de disponibilidad */
        .esp-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 100px;
            margin-top: 12px;
        }

        .esp-pill--ok {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .esp-pill--no {
            background: rgba(107, 133, 168, .10);
            color: var(--text-muted);
        }

        /* ── Estado vacío ────────────────────────────── */
        .esp-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 24px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
        }

        .esp-empty i {
            font-size: 52px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: block;
        }

        .esp-empty h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--cream);
            margin-bottom: 6px;
        }

        .esp-empty p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ── Modal agenda ────────────────────────────── */
        .esp-modal .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 16px 60px rgba(37, 99, 235, .16);
        }

        .esp-modal .modal-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-bottom: 1px solid var(--border);
            padding: 20px 24px;
        }

        .esp-modal .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .esp-modal .modal-title i {
            color: var(--blue);
            font-size: 22px;
        }

        .esp-modal .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Doctor card dentro del modal */
        .med-card {
            background: var(--navy);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            margin-bottom: 12px;
            transition: border-color .18s;
        }

        .med-card:last-child {
            margin-bottom: 0;
        }

        .med-card:hover {
            border-color: rgba(37, 99, 235, .30);
        }

        .med-card__head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .med-card__avatar {
            width: 52px;
            height: 52px;
            background: rgba(37, 99, 235, .10);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 22px;
            flex-shrink: 0;
        }

        .med-card__nombre {
            font-size: 15px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 2px;
        }

        .med-card__esp {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        /* Disponibilidad en el modal */
        .med-disp {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            margin-bottom: 14px;
            font-size: 13px;
            color: var(--cream-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .med-disp i {
            color: var(--blue);
            font-size: 18px;
        }

        .med-disp--ok {
            border-left: 3px solid #0e7a5f;
        }

        .med-disp--no {
            border-left: 3px solid var(--text-muted);
        }

        /* Botones del modal */
        .med-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .med-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: var(--radius-sm);
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all .18s;
            text-decoration: none;
        }

        .med-btn--primary {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }

        .med-btn--primary:hover {
            background: #1d52d4;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(37, 99, 235, .30);
        }

        .med-btn--ghost {
            background: transparent;
            color: var(--text-muted);
            border-color: var(--border);
        }

        .med-btn--ghost:hover {
            border-color: rgba(37, 99, 235, .30);
            color: var(--cream);
        }

        /* ── Toast ───────────────────────────────────── */
        #espToast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
            align-items: flex-end;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width: 640px) {
            .esp-wrap {
                padding: 0 16px 60px;
            }

            .esp-header {
                padding: 32px 0 24px;
            }

            .esp-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .esp-stats {
                flex-direction: column;
                gap: 10px;
            }

            .esp-stat {
                padding: 14px 18px;
            }
        }

        @media (max-width: 480px) {
            .esp-wrap {
                padding: 0 12px 60px;
            }

            .esp-header {
                padding: 18px 0 14px;
                gap: 10px;
            }

            .esp-fecha {
                display: none;
            }

            .esp-title {
                font-size: 26px;
            }

            .esp-stat__num {
                font-size: 22px;
            }

            .esp-search {
                padding: 12px 14px 12px 44px;
                font-size: 14px;
            }

            .esp-card {
                padding: 18px;
            }

            .esp-card__icon {
                width: 44px;
                height: 44px;
                font-size: 20px;
                margin-bottom: 10px;
            }

            .esp-card__esp {
                font-size: 14px;
            }
        }
    </style>

    <div class="esp-wrap">

        {{-- Flash ─────────────────────────────────────── --}}
        @if (session('success'))
            <div
                style="background:rgba(14,122,95,.08);border:1px solid rgba(14,122,95,.25);border-left:4px solid #0e7a5f;border-radius:var(--radius);padding:14px 18px;margin-bottom:24px;display:flex;align-items:center;gap:10px;color:#0e7a5f;font-weight:600;font-size:14px;">
                <i class="mdi mdi-check-circle" style="font-size:20px;"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header ─────────────────────────────────────── --}}
        <div class="esp-header">
            <div class="esp-header__left">
                <div class="esp-kicker">PATS · Red de servicios médicos</div>
                <h1 class="esp-title">Consulta por <span>especialidad</span></h1>
                <p class="esp-subtitle">Encuentra médicos y agenda tu cita en la red PATS</p>
            </div>

            <div class="esp-fecha">
                <div class="esp-fecha__dia">Hoy</div>
                <div class="esp-fecha__full">{{ ucfirst($fecha_display) }}</div>
                <div class="esp-fecha__hora" id="relojVivo">{{ $hora_actual }}</div>
            </div>
        </div>

        {{-- Stats ──────────────────────────────────────── --}}
        <div class="esp-stats">
            <div class="esp-stat">
                <div class="esp-stat__num">{{ $porEspecialidad->count() }}</div>
                <div class="esp-stat__label">Especialidades</div>
            </div>
            <div class="esp-stat">
                <div class="esp-stat__num">{{ $totalMedicos }}</div>
                <div class="esp-stat__label">Médicos activos</div>
            </div>
            {{-- <div class="esp-stat">
                <div class="esp-stat__num">{{ $disponibilidad->sum() }}</div>
                <div class="esp-stat__label">Citas disponibles</div>
            </div>
            <div class="esp-stat">
                <div class="esp-stat__num">{{ $citasHoy->count() }}</div>
                <div class="esp-stat__label">Citas hoy</div>
            </div> --}}
        </div>

        {{-- Buscador ────────────────────────────────────── --}}
        <div class="esp-search-wrap">
            <i class="mdi mdi-magnify esp-search-icon"></i>
            <input type="text" id="espSearch" class="esp-search"
                placeholder="Buscar especialidad (ej: Cardiología, Pediatría…)" autocomplete="off">
            <button class="esp-search-clear" id="espClear">
                <i class="mdi mdi-close" style="font-size:16px;"></i>
            </button>
            <div class="esp-suggestions" id="espSug"></div>
        </div>

        {{-- Grid ───────────────────────────────────────── --}}
        <div class="esp-grid" id="espGrid">

            @forelse ($porEspecialidad as $especialidad => $medicos)
                @php
                    // Contar cuántos bloques disponibles tiene esta especialidad
                    $bloquesEsp = $medicos->sum(fn($m) => $disponibilidad->get($m->id_recurso, 0));
                    $icons = [
                        'stethoscope',
                        'heart-pulse',
                        'brain',
                        'eye',
                        'bone',
                        'baby',
                        'lungs',
                        'stomach',
                        'needle',
                        'pill',
                        'tooth',
                        'ear-hearing',
                    ];
                    $icon = $icons[$loop->index % count($icons)];
                    $slug = \Illuminate\Support\Str::slug($especialidad);
                @endphp

                <div class="esp-card" data-nombre="{{ strtolower($especialidad) }}" data-bs-toggle="modal"
                    data-bs-target="#modal-{{ $slug }}">

                    <div class="esp-card__icon">
                        <i class="mdi mdi-{{ $icon }}"></i>
                    </div>

                    <h3 class="esp-card__esp">{{ $especialidad }}</h3>

                    <div class="esp-card__row">
                        <i class="mdi mdi-doctor"></i>
                        <span>{{ $medicos->count() }} {{ $medicos->count() === 1 ? 'médico' : 'médicos' }}</span>
                    </div>

                    @if ($bloquesEsp > 0)
                        <span class="esp-pill esp-pill--ok">
                            <i class="mdi mdi-calendar-check" style="font-size:13px;"></i>
                            {{ $bloquesEsp }} {{ $bloquesEsp === 1 ? 'horario disponible' : 'horarios disponibles' }}
                        </span>
                    @else
                        <span class="esp-pill esp-pill--no">
                            <i class="mdi mdi-calendar-remove" style="font-size:13px;"></i>
                            Sin horarios próximos
                        </span>
                    @endif
                </div>

            @empty
                <div class="esp-empty">
                    <i class="mdi mdi-doctor"></i>
                    <h3>Sin especialidades disponibles</h3>
                    <p>No hay médicos registrados en la red para esta unidad.</p>
                </div>
            @endforelse

            {{-- Vacío de búsqueda (JS lo inyecta) --}}
            <div class="esp-empty" id="espEmptySearch" style="display:none; grid-column:1/-1;">
                <i class="mdi mdi-magnify"></i>
                <h3>Sin resultados</h3>
                <p id="espEmptyMsg"></p>
            </div>

        </div>

    </div>{{-- /esp-wrap --}}

    {{-- ── Modales por especialidad ──────────────────────── --}}
    @foreach ($porEspecialidad as $especialidad => $medicosEsp)
        @php $slug = \Illuminate\Support\Str::slug($especialidad); @endphp

        <div class="modal fade esp-modal" id="modal-{{ $slug }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="mdi mdi-stethoscope"></i>
                            {{ $especialidad }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"
                            style="background:none;border:none;font-size:20px;color:var(--text-muted);cursor:pointer;line-height:1;">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>

                    <div class="modal-body">

                        @foreach ($medicosEsp as $medico)
                            @php
                                $bloquesMedico = $disponibilidad->get($medico->id_recurso, 0);
                            @endphp

                            <div class="med-card">
                                <div class="med-card__head">
                                    <div class="med-card__avatar">
                                        <i class="mdi mdi-doctor"></i>
                                    </div>
                                    <div>
                                        <p class="med-card__nombre">{{ $medico->nombre_recurso }}</p>
                                        <p class="med-card__esp">
                                            <i class="mdi mdi-tag-outline" style="font-size:13px;color:var(--blue);"></i>
                                            {{ $medico->especialidad ?? $especialidad }}

                                        </p>
                                    </div>
                                </div>

                                {{-- Disponibilidad real --}}
                                @if ($bloquesMedico > 0)
                                    <div class="med-disp med-disp--ok">
                                        <i class="mdi mdi-calendar-check"></i>
                                        <span>
                                            <strong>{{ $bloquesMedico }}</strong>
                                            {{ $bloquesMedico === 1 ? 'horario disponible' : 'horarios disponibles' }}
                                            en los próximos 30 días
                                        </span>
                                    </div>
                                @else
                                    <div class="med-disp med-disp--no">
                                        <i class="mdi mdi-calendar-remove"></i>
                                        <span>Sin horarios disponibles por el momento</span>
                                    </div>
                                @endif

                                <div class="med-actions">
                                    @if ($bloquesMedico > 0)
                                        <a href="{{ route('especialidades.agenda', $medico->id_recurso) }}"
                                            class="med-btn med-btn--primary">
                                            <i class="mdi mdi-calendar-plus"></i>
                                            Agendar cita
                                        </a>
                                    @endif
                                    <button type="button" class="med-btn med-btn--ghost" data-bs-dismiss="modal">
                                        Cerrar
                                    </button>
                                </div>

                            </div>
                        @endforeach

                    </div>{{-- /modal-body --}}
                </div>
            </div>
        </div>
    @endforeach

    {{-- Toast host --}}
    <div id="espToast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ── Reloj en vivo ───────────────────────────
            const reloj = document.getElementById('relojVivo');
            if (reloj) {
                function tick() {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    reloj.textContent = `${h}:${m}`;
                }
                tick();
                setInterval(tick, 60000);
            }

            // ── Búsqueda y sugerencias ──────────────────
            const input = document.getElementById('espSearch');
            const clear = document.getElementById('espClear');
            const sug = document.getElementById('espSug');
            const grid = document.getElementById('espGrid');
            const empty = document.getElementById('espEmptySearch');
            const emptyMsg = document.getElementById('espEmptyMsg');
            const cards = [...document.querySelectorAll('.esp-card')];

            function filterCards(term) {
                let visible = 0;
                cards.forEach(c => {
                    const match = c.dataset.nombre.includes(term);
                    c.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                empty.style.display = (visible === 0 && term.length > 0) ? 'flex' : 'none';
                if (emptyMsg) emptyMsg.textContent = `No hay especialidades que coincidan con "${term}".`;
            }

            function buildSuggestions(term) {
                sug.innerHTML = '';
                if (!term) {
                    sug.classList.remove('open');
                    return;
                }

                const matches = cards.filter(c => c.dataset.nombre.includes(term));
                if (!matches.length) {
                    sug.classList.remove('open');
                    return;
                }

                matches.forEach(card => {
                    const item = document.createElement('div');
                    item.className = 'esp-sug-item';
                    const iconEl = card.querySelector('.esp-card__icon i');
                    const iconClass = iconEl ? iconEl.className : 'mdi mdi-stethoscope';
                    const pillEl = card.querySelector('.esp-pill');
                    const pillText = pillEl ? pillEl.textContent.trim() : '';
                    item.innerHTML = `
        <i class="${iconClass} esp-sug-item__icon"></i>
        <span class="esp-sug-item__name">${card.querySelector('.esp-card__esp').textContent}</span>
        <span class="esp-sug-item__count">${pillText}</span>
      `;
                    item.addEventListener('click', () => {
                        input.value = card.querySelector('.esp-card__esp').textContent;
                        filterCards(card.dataset.nombre);
                        sug.classList.remove('open');
                        clear.classList.add('visible');
                        card.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    });
                    sug.appendChild(item);
                });

                sug.classList.add('open');
            }

            input.addEventListener('input', () => {
                const term = input.value.toLowerCase().trim();
                clear.classList.toggle('visible', term.length > 0);
                filterCards(term);
                buildSuggestions(term);
            });

            clear.addEventListener('click', () => {
                input.value = '';
                clear.classList.remove('visible');
                filterCards('');
                sug.classList.remove('open');
                input.focus();
            });

            document.addEventListener('click', e => {
                if (!input.contains(e.target) && !sug.contains(e.target)) {
                    sug.classList.remove('open');
                }
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Escape') clear.click();
            });
        });
    </script>
@endsection
