{{-- resources/views/servicios/especialidades-agenda.blade.php --}}
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Agendar cita — ' . $medico->nombre_recurso)

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
            --border: rgba(37, 99, 235, .12);
            --radius: 14px;
            --radius-sm: 9px;
            --radius-lg: 20px;
            --shadow: 0 4px 24px rgba(37, 99, 235, .08);
            --shadow-sm: 0 2px 8px rgba(37, 99, 235, .06);
        }

        /* ── Layout ───────────────────────────────────── */
        .ea-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 28px 80px;
        }

        .ea-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 28px;
            align-items: start;
        }

        /* ── Breadcrumb ───────────────────────────────── */
        .ea-bread {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 28px 0 20px;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .ea-bread a {
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color .15s;
        }

        .ea-bread a:hover {
            color: var(--blue);
        }

        .ea-bread__sep {
            opacity: .4;
        }

        .ea-bread__cur {
            color: var(--cream);
            font-weight: 600;
        }

        /* ── Doctor hero ──────────────────────────────── */
        .ea-hero {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 24px;
            box-shadow: var(--shadow);
        }

        .ea-hero__avatar {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            background: rgba(37, 99, 235, .08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 32px;
        }

        .ea-hero__kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 5px;
        }

        .ea-hero__nombre {
            font-size: 22px;
            font-weight: 800;
            color: var(--cream);
            margin: 0 0 4px;
            letter-spacing: -.01em;
        }

        .ea-hero__esp {
            font-size: 14px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ea-hero__esp i {
            color: var(--blue);
            font-size: 16px;
        }

        .ea-hero__stat {
            margin-left: auto;
            text-align: center;
            flex-shrink: 0;
        }

        .ea-hero__num {
            font-size: 32px;
            font-weight: 800;
            color: var(--blue);
            line-height: 1;
        }

        .ea-hero__label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 2px;
        }

        /* ── Calendario de bloques ────────────────────── */
        .ea-cal {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .ea-cal__head {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ea-cal__title {
            font-size: 15px;
            font-weight: 700;
            color: var(--cream);
        }

        .ea-cal__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Días con bloques */
        .ea-dias {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .ea-dia {
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .ea-dia__head {
            padding: 12px 16px;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: background .15s;
        }

        .ea-dia__head:hover {
            background: var(--navy-light);
        }

        .ea-dia__fecha {
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ea-dia__fecha i {
            color: var(--blue);
            font-size: 17px;
        }

        .ea-dia--hoy .ea-dia__fecha {
            color: var(--blue);
        }

        .ea-dia__badge {
            font-size: 11px;
            font-weight: 700;
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
            padding: 3px 10px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ea-dia__toggle {
            color: var(--text-muted);
            font-size: 18px;
            transition: transform .2s;
        }

        .ea-dia.is-open .ea-dia__toggle {
            transform: rotate(180deg);
        }

        /* Slots de horario */
        .ea-slots {
            display: none;
            padding: 14px 16px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
        }

        .ea-dia.is-open .ea-slots {
            display: grid;
        }

        .ea-dia:not(.is-open) .ea-slots {
            display: none;
        }

        .ea-slot {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--white);
            cursor: pointer;
            transition: all .18s;
            text-align: center;
        }

        .ea-slot:hover {
            border-color: var(--blue);
            background: rgba(37, 99, 235, .04);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .ea-slot.is-selected {
            border-color: var(--blue);
            background: rgba(37, 99, 235, .08);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .ea-slot__hora {
            font-size: 15px;
            font-weight: 800;
            color: var(--cream);
        }

        .ea-slot__dur {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .ea-slot__cupos {
            font-size: 10.5px;
            font-weight: 600;
            color: #0e7a5f;
            margin-top: 4px;
        }

        /* Sin slots */
        .ea-empty {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        .ea-empty i {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
            opacity: .3;
        }

        /* ── Panel derecho: formulario ────────────────── */
        .ea-panel {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: sticky;
            top: 24px;
        }

        .ea-panel__head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
        }

        .ea-panel__title {
            font-size: 15px;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 2px;
        }

        .ea-panel__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
        }

        /* Slot seleccionado (resumen) */
        .ea-sel {
            margin: 16px;
            padding: 14px 16px;
            background: rgba(37, 99, 235, .06);
            border: 1.5px solid rgba(37, 99, 235, .20);
            border-radius: var(--radius-sm);
            display: none;
        }

        .ea-sel.visible {
            display: block;
        }

        .ea-sel__row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--cream);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .ea-sel__row:last-child {
            margin-bottom: 0;
        }

        .ea-sel__row i {
            color: var(--blue);
            font-size: 15px;
        }

        /* Formulario */
        .ea-form {
            padding: 16px;
        }

        .ea-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .ea-field:last-child {
            margin-bottom: 0;
        }

        .ea-label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .ea-input,
        .ea-select,
        .ea-textarea {
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            color: var(--text);
            background: var(--navy);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
            font-family: inherit;
        }

        .ea-input:focus,
        .ea-select:focus,
        .ea-textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
            background: var(--white);
        }

        .ea-input::placeholder,
        .ea-textarea::placeholder {
            color: var(--text-muted);
        }

        .ea-textarea {
            resize: vertical;
            min-height: 72px;
        }

        .ea-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b85a8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }

        /* Hint / error */
        .ea-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .ea-error {
            font-size: 11.5px;
            color: var(--danger);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Botón submit */
        .ea-submit {
            width: 100%;
            padding: 13px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s, transform .18s, box-shadow .18s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }

        .ea-submit:hover:not(:disabled) {
            background: #1d52d4;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, .30);
        }

        .ea-submit:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* Aviso de selección pendiente */
        .ea-pending {
            padding: 32px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13.5px;
        }

        .ea-pending i {
            font-size: 36px;
            display: block;
            margin-bottom: 10px;
            opacity: .35;
        }

        /* ── Citas ya agendadas ────────────────────────── */
        .ea-agendadas {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-top: 24px;
        }

        .ea-agendadas__head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            background: var(--navy);
            font-size: 14px;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ea-agendadas__head i {
            color: var(--blue);
        }

        .ea-cita-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            transition: background .12s;
        }

        .ea-cita-row:last-child {
            border-bottom: none;
        }

        .ea-cita-row:hover {
            background: var(--navy);
        }

        .ea-cita-row__hora {
            font-weight: 700;
            color: var(--blue);
            font-size: 13px;
            min-width: 80px;
        }

        .ea-cita-row__pac {
            font-weight: 600;
            color: var(--cream);
            flex: 1;
        }

        .ea-cita-row__fecha {
            font-size: 12px;
            color: var(--text-muted);
            min-width: 90px;
        }

        .ea-cita-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 100px;
        }

        .ea-cita-badge--prog {
            background: rgba(37, 99, 235, .10);
            color: var(--blue);
        }

        .ea-cita-badge--conf {
            background: rgba(14, 122, 95, .10);
            color: #0e7a5f;
        }

        .ea-cita-badge--proc {
            background: rgba(217, 119, 6, .10);
            color: #d97706;
        }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width:960px) {
            .ea-layout {
                grid-template-columns: 1fr;
            }

            .ea-panel {
                position: static;
            }
        }

        @media (max-width:600px) {
            .ea-wrap {
                padding: 0 14px 60px;
            }

            .ea-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .ea-hero__stat {
                margin-left: 0;
            }

            .ea-slots {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            }
        }
    </style>

    <div class="ea-wrap">

        {{-- Breadcrumb ─────────────────────────────────── --}}
        <div class="ea-bread">
            <a href="{{ route('especialidades.index') }}">
                <i class="mdi mdi-arrow-left"></i> Especialidades
            </a>
            <span class="ea-bread__sep">/</span>
            <span class="ea-bread__cur">{{ $medico->nombre_recurso }}</span>
        </div>

        {{-- Hero del médico ─────────────────────────────── --}}
        <div class="ea-hero">
            <div class="ea-hero__avatar">
                <i class="mdi mdi-doctor"></i>
            </div>
            <div>
                <div class="ea-hero__kicker">Consulta de especialidad</div>
                <h1 class="ea-hero__nombre">{{ $medico->nombre_recurso }}</h1>
                <div class="ea-hero__esp">
                    <i class="mdi mdi-tag-outline"></i>
                    {{ $medico->especialidad ?? 'Especialidad general' }}
                    &nbsp;·&nbsp;
                    <i class="mdi mdi-map-marker-outline"></i>
                    {{ $medico->region }} · {{ $medico->unidad }}
                </div>
            </div>
            <div class="ea-hero__stat">
                <div class="ea-hero__num">{{ $bloques->sum(fn($d) => $d->count()) }}</div>
                <div class="ea-hero__label">Horarios disponibles</div>
            </div>
        </div>

        {{-- Flash ──────────────────────────────────────── --}}
        @if (session('success'))
            <div
                style="background:rgba(14,122,95,.08);border:1px solid rgba(14,122,95,.25);border-left:4px solid #0e7a5f;border-radius:var(--radius);padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#0e7a5f;font-weight:600;font-size:14px;">
                <i class="mdi mdi-check-circle" style="font-size:20px;"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                style="background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.20);border-left:4px solid var(--danger);border-radius:var(--radius);padding:14px 18px;margin-bottom:20px;color:var(--danger);font-size:13.5px;">
                <strong>Corrige los siguientes errores:</strong>
                <ul style="margin:6px 0 0 16px;padding:0;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Layout ─────────────────────────────────────── --}}
        <div class="ea-layout">

            {{-- ── Calendario de bloques ─────────────────── --}}
            <div>
                <div class="ea-cal">
                    <div class="ea-cal__head">
                        <div>
                            <div class="ea-cal__title">Horarios disponibles</div>
                            <div class="ea-cal__sub">Próximos 30 días · Selecciona un horario para agendar</div>
                        </div>
                        <i class="mdi mdi-calendar-check" style="font-size:28px;color:var(--blue);opacity:.5;"></i>
                    </div>

                    @if ($bloques->isEmpty())
                        <div class="ea-empty">
                            <i class="mdi mdi-calendar-remove"></i>
                            Sin horarios disponibles en los próximos 30 días.<br>
                            <small>Contacta la unidad para más información.</small>
                        </div>
                    @else
                        <div class="ea-dias">
                            @foreach ($bloques as $fecha => $bloquesDelDia)
                                @php
                                    $carbon = Carbon::parse($fecha);
                                    $esHoy = $fecha === $fecha_hoy;
                                    $diaNom = ucfirst($carbon->isoFormat('dddd D [de] MMMM'));
                                    $totalDia = $bloquesDelDia->count();
                                @endphp

                                <div class="ea-dia {{ $esHoy ? 'ea-dia--hoy' : '' }} {{ $loop->first ? 'is-open' : '' }}"
                                    data-fecha="{{ $fecha }}">

                                    <div class="ea-dia__head" onclick="toggleDia(this.parentElement)">
                                        <div class="ea-dia__fecha">
                                            <i class="mdi mdi-calendar{{ $esHoy ? '-today' : '-outline' }}"></i>
                                            {{ $diaNom }}{{ $esHoy ? ' · HOY' : '' }}
                                        </div>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <span class="ea-dia__badge">
                                                <i class="mdi mdi-clock-outline" style="font-size:12px;"></i>
                                                {{ $totalDia }} {{ $totalDia === 1 ? 'horario' : 'horarios' }}
                                            </span>
                                            <i class="mdi mdi-chevron-down ea-dia__toggle"></i>
                                        </div>
                                    </div>

                                    <div class="ea-slots">
                                        @foreach ($bloquesDelDia->sortBy('fecha_inicio') as $bloque)
                                            @php
                                                $ini = Carbon::parse($bloque->fecha_inicio);
                                                $fin = Carbon::parse($bloque->fecha_fin);
                                                $dur = $ini->diffInMinutes($fin);
                                                $lib = $bloque->cupos - $bloque->ocupado;
                                            @endphp
                                            <div class="ea-slot" data-id="{{ $bloque->id_agenda }}"
                                                data-fecha="{{ $fecha }}" data-fecha-display="{{ $diaNom }}"
                                                data-hora-ini="{{ $ini->format('H:i') }}"
                                                data-hora-fin="{{ $fin->format('H:i') }}" data-dur="{{ $dur }}"
                                                data-cupos="{{ $lib }}" onclick="selectSlot(this)">
                                                <div class="ea-slot__hora">{{ $ini->format('H:i') }}</div>
                                                <div class="ea-slot__dur">{{ $dur }} min</div>
                                                <div class="ea-slot__cupos">
                                                    {{ $lib }} {{ $lib === 1 ? 'cupo' : 'cupos' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>


            </div>{{-- /col izq --}}

            {{-- ── Panel: formulario de cita ─────────────── --}}
            <div class="ea-panel">
                <div class="ea-panel__head">
                    <p class="ea-panel__title">Nueva cita</p>
                    <p class="ea-panel__sub">Selecciona un horario y completa los datos del paciente</p>
                </div>

                {{-- Resumen del slot seleccionado --}}
                <div class="ea-sel" id="slotResumen">
                    <div class="ea-sel__row">
                        <i class="mdi mdi-calendar"></i>
                        <span id="slotFecha">—</span>
                    </div>
                    <div class="ea-sel__row">
                        <i class="mdi mdi-clock-outline"></i>
                        <span id="slotHora">—</span>
                    </div>
                    <div class="ea-sel__row">
                        <i class="mdi mdi-account-clock"></i>
                        <span id="slotCupos">—</span>
                    </div>
                </div>

                {{-- Placeholder cuando no hay slot seleccionado --}}
                <div class="ea-pending" id="pendingMsg">
                    <i class="mdi mdi-calendar-blank"></i>
                    Selecciona un horario del calendario para continuar
                </div>

                {{-- Formulario (oculto hasta seleccionar slot) --}}
                <form action="{{ route('especialidades.guardar') }}" method="POST" id="formCita" style="display:none;">
                    @csrf
                    <input type="hidden" name="id_agenda" id="inputIdAgenda">

                    <div class="ea-form">

                        <div class="ea-field">
                            <label class="ea-label" for="curp">CURP *</label>
                            <input class="ea-input" type="text" id="curp" name="curp"
                                placeholder="18 caracteres" maxlength="18" value="{{ old('curp') }}"
                                style="text-transform:uppercase;" required>
                            @error('curp')
                                <span class="ea-error"><i class="mdi mdi-alert-circle"
                                        style="font-size:14px;"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="ea-field">
                            <label class="ea-label" for="nombre">Nombre completo *</label>
                            <input class="ea-input" type="text" id="nombre" name="nombre"
                                placeholder="Nombre del paciente" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <span class="ea-error"><i class="mdi mdi-alert-circle"
                                        style="font-size:14px;"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="ea-field">
                                <label class="ea-label" for="fecha_nac">Fecha de nac. *</label>
                                <input class="ea-input" type="date" id="fecha_nac" name="fecha_nac"
                                    value="{{ old('fecha_nac') }}" max="{{ now()->subYears(1)->toDateString() }}"
                                    required>
                            </div>
                            <div class="ea-field">
                                <label class="ea-label" for="sexo">Sexo *</label>
                                <select class="ea-select" id="sexo" name="sexo" required>
                                    <option value="">—</option>
                                    <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="ea-field">
                            <label class="ea-label" for="observaciones">Observaciones</label>
                            <textarea class="ea-textarea" id="observaciones" name="observaciones"
                                placeholder="Motivo de consulta u observaciones adicionales...">{{ old('observaciones') }}</textarea>
                        </div>

                        <button type="submit" class="ea-submit" id="btnSubmit" disabled>
                            <i class="mdi mdi-calendar-check"></i>
                            Confirmar cita
                        </button>

                    </div>{{-- /ea-form --}}
                </form>

            </div>{{-- /ea-panel --}}

        </div>{{-- /ea-layout --}}
    </div>{{-- /ea-wrap --}}

    <script>
        (function() {
            'use strict';

            // ── Toggle de día ─────────────────────────────
            window.toggleDia = function(el) {
                el.classList.toggle('is-open');
            };

            // ── Selección de slot ─────────────────────────
            let slotActivo = null;

            window.selectSlot = function(el) {
                // Quitar selección anterior
                if (slotActivo) slotActivo.classList.remove('is-selected');
                el.classList.add('is-selected');
                slotActivo = el;

                const idAgenda = el.dataset.id;
                const fechaDisp = el.dataset.fechaDisplay;
                const horaIni = el.dataset.horaIni;
                const horaFin = el.dataset.horaFin;
                const dur = el.dataset.dur;
                const cupos = el.dataset.cupos;

                // Actualizar resumen
                document.getElementById('slotFecha').textContent = fechaDisp;
                document.getElementById('slotHora').textContent = `${horaIni} – ${horaFin} (${dur} min)`;
                document.getElementById('slotCupos').textContent =
                    `${cupos} cupo${cupos != 1 ? 's' : ''} disponible${cupos != 1 ? 's' : ''}`;

                // Mostrar resumen, ocultar placeholder, mostrar form
                document.getElementById('slotResumen').classList.add('visible');
                document.getElementById('pendingMsg').style.display = 'none';
                document.getElementById('formCita').style.display = 'block';

                // Rellenar hidden
                document.getElementById('inputIdAgenda').value = idAgenda;

                // Habilitar botón
                document.getElementById('btnSubmit').disabled = false;

                // Scroll suave al panel en móvil
                if (window.innerWidth < 960) {
                    document.querySelector('.ea-panel').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            };

            // ── CURP a mayúsculas automático ──────────────
            const curpInput = document.getElementById('curp');
            if (curpInput) {
                curpInput.addEventListener('input', () => {
                    const pos = curpInput.selectionStart;
                    curpInput.value = curpInput.value.toUpperCase();
                    curpInput.setSelectionRange(pos, pos);
                });
            }

            // ── Validación básica antes de submit ─────────
            const form = document.getElementById('formCita');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const curp = document.getElementById('curp').value.trim();
                    if (curp.length !== 18) {
                        e.preventDefault();
                        alert('La CURP debe tener exactamente 18 caracteres.');
                        document.getElementById('curp').focus();
                        return;
                    }
                    document.getElementById('btnSubmit').disabled = true;
                    document.getElementById('btnSubmit').innerHTML =
                        '<i class="mdi mdi-loading mdi-spin"></i> Guardando...';
                });
            }

            // Abrir el primer día automáticamente ya viene hecho con `is-open` en Blade
        })();
    </script>
@endsection
