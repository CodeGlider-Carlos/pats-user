{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>

        :root {
            --white: #ffffff;
            --bg: #f0f2f7;
            --text: #0f1923;
            --muted: #6b7a8d;
            --radius: 22px;
            --shadow-card: 0 8px 40px rgba(0, 0, 0, .10);
            --shadow-hover: 0 24px 60px rgba(0, 0, 0, .18);
        }

        .db-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 28px 80px;
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Header ─────────────────────────────── */
        .db-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 52px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .db-header__greeting {
            font-family: sans-serif;
            font-size: clamp(28px, 4vw, 44px);
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
            margin: 0 0 6px;
        }

        .db-header__greeting em {
            font-style: normal;
            background: linear-gradient(90deg, #529cb2, #303e84);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .db-header__sub {
            font-size: 15px;
            color: var(--muted);
            margin: 0;
        }

        .db-badges {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 4px;
        }

        .db-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 13px;
            border-radius: 100px;
            letter-spacing: .04em;
        }

        .db-badge--plan {
            background: #e8f0ff;
            color: #303e84;
        }

        .db-badge--status {
            background: #e8f9f1;
            color: #198754;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .db-badge--status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #198754;
        }

        .db-badge--warning {
            background: #fff8e1;
            color: #b45309;
        }

        .db-badge--danger {
            background: #fff1f2;
            color: #dc2626;
        }

        .db-badge--muted {
            background: #f1f5f9;
            color: #64748b;
        }

        /* ── Grid ───────────────────────────────── */
        .db-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        /* ── Card ───────────────────────────────── */
        .db-card {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            background: var(--white);
            cursor: pointer;
            text-decoration: none;
            display: block;
            transition: transform .28s cubic-bezier(.34, 1.56, .64, 1), box-shadow .28s ease;
            box-shadow: var(--shadow-card);
            border: none;
            outline: none;
        }

        .db-card:hover {
            transform: translateY(-8px) scale(1.015);
            box-shadow: var(--shadow-hover);
        }

        .db-card:hover .db-card__arrow {
            opacity: 1;
            transform: translate(0, 0);
        }

        .db-card:hover .db-card__glow {
            opacity: .18;
        }

        /* Faja de color superior */
        .db-card__stripe {
            height: 6px;
            width: 100%;
            display: block;
        }

        /* Glow animado */
        .db-card__glow {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            opacity: 0;
            pointer-events: none;
            transition: opacity .4s;
            filter: blur(40px);
        }

        /* Contenido */
        .db-card__body {
            padding: 28px 28px 26px;
        }

        /* Ícono SVG */
        .db-card__icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .28s cubic-bezier(.34, 1.56, .64, 1);
        }

        .db-card:hover .db-card__icon-wrap {
            transform: scale(1.08) rotate(-2deg);
        }

        .db-card__icon-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .25);
            border-radius: inherit;
        }

        .db-card__icon-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: brightness(0) invert(1);
        }

        /* Texto */
        .db-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
            margin: 0 0 6px;
            transition: color .2s;
        }

        .db-card__subtitle {
            font-size: 13px;
            color: var(--muted);
            margin: 0 0 22px;
            line-height: 1.4;
        }

        /* Footer de la card */
        .db-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid rgba(0, 0, 0, .07);
        }

        .db-card__label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .10em;
            opacity: .55;
        }

        .db-card__arrow {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            opacity: .75;
            transform: translate(-4px, 4px);
            transition: opacity .22s, transform .22s;
        }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 900px) {
            .db-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 560px) {
            .db-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .db-wrap {
                padding: 24px 16px 60px;
            }

            .db-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 28px;
                gap: 12px;
            }

            .db-badges {
                flex-wrap: wrap;
            }

            .db-card__body {
                padding: 22px 20px 18px;
            }

            .db-header__greeting {
                font-size: 28px;
            }
        }

        @media (max-width: 400px) {
            .db-card__icon-wrap {
                width: 58px;
                height: 58px;
                border-radius: 14px;
                margin-bottom: 14px;
            }

            .db-card__icon-img {
                width: 34px;
                height: 34px;
            }

            .db-card__title {
                font-size: 15px;
            }

            .db-card__body {
                padding: 18px 16px 16px;
            }
        }
    </style>

    <div class="db-wrap">

        {{-- Header --}}
        @php
            $nombreSaludo = $user?->nombre_usuario ?? $user?->nombre_paciente ?? explode('@', $user?->correo_usuario ?? 'visitante@')[0];
            $primerNombre = ucfirst(strtolower(explode(' ', trim($nombreSaludo))[0]));

            if ($pasaporte) {
                $vigencia     = \Carbon\Carbon::parse($pasaporte->vigencia);
                $diasRestantes = now()->diffInDays($vigencia, false);
                $estatusPas   = strtolower($pasaporte->estatus ?? '');
                if ($estatusPas === 'vencido' || $diasRestantes < 0) {
                    $badgeEstatus = ['label' => 'Vencido',     'class' => 'db-badge--danger'];
                } elseif ($diasRestantes <= 15) {
                    $badgeEstatus = ['label' => 'Por vencer',  'class' => 'db-badge--warning'];
                } else {
                    $badgeEstatus = ['label' => 'Vigente',     'class' => 'db-badge--status'];
                }
            } else {
                $badgeEstatus = ['label' => 'Sin pasaporte', 'class' => 'db-badge--muted'];
            }
        @endphp
        <div class="db-header">
            <div>
                <h1 class="db-header__greeting">Hola, <em>{{ $primerNombre }}.</em></h1>
                <p class="db-header__sub">¿En qué servicio puedo ayudarte hoy?</p>
            </div>
            <div class="db-badges">
                @if($pasaporte)
                <span class="db-badge db-badge--plan">
                    {{ $pasaporte->code_pasaporte ?: str_pad($pasaporte->id_pasaporte, 6, '0', STR_PAD_LEFT) }}
                </span>
                @endif
                <span class="db-badge {{ $badgeEstatus['class'] }}">{{ $badgeEstatus['label'] }}</span>
            </div>
        </div>

        {{-- Grid de cards --}}
        @php
            $cards = [
                [
                    'title' => 'Consulta General',
                    'subtitle' => 'Atención médica básica con nuestros médicos',
                    'color' => '#87a924',
                    'icon' => 'consulta-general.svg',
                    'url' => '/hospitales',
                    'label' => 'Ver hospitales',
                ],
                [
                    'title' => 'Consulta de Especialidad',
                    'subtitle' => 'Médicos especialistas listos para atenderte',
                    'color' => '#698519',
                    'icon' => 'especialidades.svg',
                    'url' => '/especialidades',
                    'label' => 'Ver especialistas',
                ],
                [
                    'title' => 'Servicios y Cirugías',
                    'subtitle' => 'Procedimientos médicos y quirúrgicos',
                    'color' => '#529cb2',
                    'icon' => 'Cirugias.svg',
                    'url' => '/atencion-medica',
                    'label' => 'Ver servicios',
                ],
                [
                    'title' => 'Estudios de Laboratorio',
                    'subtitle' => 'Análisis clínicos y estudios de sangre',
                    'color' => '#303e84',
                    'icon' => 'Estudios.svg',
                    'url' => '/estudios-clinicos',
                    'label' => 'Ver catálogo',
                ],
                [
                    'title' => 'Imagenología',
                    'subtitle' => 'Rayos X, tomografías y ultrasonidos',
                    'color' => '#1b1f6f',
                    'icon' => 'Imagenologia.svg',
                    'url' => '/rayos',
                    'label' => 'Ver equipos',
                ],
                [
                    'title' => 'Farmacia',
                    'subtitle' => 'Medicamentos y entregas en tu unidad',
                    'color' => '#0b43e6',
                    'icon' => 'Farmacia.svg',
                    'url' => '/farmacia',
                    'label' => 'Ver medicamentos',
                ],
            ];
        @endphp

        <div class="db-grid">
            @foreach ($cards as $card)
                @php
                    // Derive a lighter tint from the brand color for icon background
                    $hex = ltrim($card['color'], '#');
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                    $tint = "rgba($r,$g,$b,.13)";
                    $glow = "rgba($r,$g,$b,1)";
                @endphp

                <a href="{{ $card['url'] }}" class="db-card" style="color:inherit;">

                    {{-- Glow --}}
                    <div class="db-card__glow" style="background:{{ $glow }};"></div>

                    {{-- Stripe superior --}}
                    <span class="db-card__stripe" style="background:{{ $card['color'] }};"></span>

                    <div class="db-card__body">
                        {{-- Ícono --}}
                        <div class="db-card__icon-wrap" style="background:{{ $card['color'] }};">
                            <img src="{{ asset('icons/' . $card['icon']) }}" alt="{{ $card['title'] }}"
                                class="db-card__icon-img">
                        </div>

                        {{-- Texto --}}
                        <h3 class="db-card__title" style="color:{{ $card['color'] }};">
                            {{ $card['title'] }}
                        </h3>
                        <p class="db-card__subtitle">{{ $card['subtitle'] }}</p>

                        {{-- Footer --}}
                        <div class="db-card__footer">
                            <span class="db-card__label" style="color:{{ $card['color'] }};">
                                {{ $card['label'] }}
                            </span>
                            <span class="db-card__arrow" style="background:{{ $card['color'] }};">
                                <i class="mdi mdi-arrow-right"></i>
                            </span>
                        </div>
                    </div>

                </a>
            @endforeach
        </div>

    </div>

    {{-- ───────────────────────────────────────────────────────────
         Encuesta de satisfacción: aparece cuando el usuario tiene una
         tarjeta_misional cerrada (activo = 0) sin reseña registrada.
    ──────────────────────────────────────────────────────────── --}}
    @if (!empty($encuestaPendiente))
        <div id="encuestaOverlay" class="enc-overlay" role="dialog" aria-modal="true" aria-labelledby="encTitle">
            <div class="enc-modal">
                <div class="enc-modal__head">
                    <span class="enc-modal__icon"><i class="mdi mdi-clipboard-text-outline"></i></span>
                    <h2 id="encTitle" class="enc-modal__title">Encuesta de satisfacción</h2>
                    <p class="enc-modal__sub">
                        Cuéntanos sobre tu experiencia en
                        <strong>{{ $encuestaPendiente['tipo_label'] }}</strong>.
                        Toma ~2 minutos y todas las preguntas son opcionales.
                    </p>
                </div>

                <div class="enc-body">
                    @foreach ($encuestaPendiente['preguntas'] as $p)
                        <div class="enc-q">
                            <p class="enc-q__title">{{ $p['numero'] }}. {{ $p['titulo'] }}</p>

                            @if ($p['tipo'] === 'escala5')
                                <div class="enc-scale">
                                    <div class="enc-scale__opts">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" class="enc-pill" data-target="{{ $p['key'] }}" data-value="{{ $i }}">{{ $i }}</button>
                                        @endfor
                                    </div>
                                    <div class="enc-scale__legend"><span>Muy insatisfecho</span><span>Muy satisfecho</span></div>
                                </div>
                                <input type="hidden" data-field="{{ $p['key'] }}" value="">
                            @elseif ($p['tipo'] === 'escala10')
                                <div class="enc-scale">
                                    <div class="enc-scale__opts enc-scale__opts--nps">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <button type="button" class="enc-pill enc-pill--sm" data-target="{{ $p['key'] }}" data-value="{{ $i }}">{{ $i }}</button>
                                        @endfor
                                    </div>
                                    <div class="enc-scale__legend"><span>Nada probable</span><span>Muy probable</span></div>
                                </div>
                                <input type="hidden" data-field="{{ $p['key'] }}" value="">
                            @else
                                <textarea class="enc-textarea" data-field="{{ $p['key'] }}" rows="2" maxlength="2000" placeholder="Escribe tu respuesta (opcional)…"></textarea>
                            @endif

                            @if (! empty($p['comentario']))
                                <textarea class="enc-textarea enc-textarea--com" data-field="{{ $p['key'] }}_com" rows="1" maxlength="1000" placeholder="Comentarios (opcional)…"></textarea>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="enc-actions">
                    <button type="button" class="enc-btn enc-btn--ghost" id="encRechazar">Ahora no</button>
                    <button type="button" class="enc-btn enc-btn--primary" id="encEnviar">Enviar encuesta</button>
                </div>
                <p class="enc-error" id="encError"></p>
            </div>
        </div>

        <style>
            .enc-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 25, 35, .55);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1080;
                padding: 20px;
                opacity: 0;
                transition: opacity .25s ease;
            }

            .enc-overlay.is-open {
                opacity: 1;
            }

            .enc-modal {
                background: #fff;
                border-radius: 22px;
                max-width: 520px;
                width: 100%;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                box-shadow: 0 24px 70px rgba(0, 0, 0, .30);
                font-family: 'DM Sans', sans-serif;
                transform: translateY(16px) scale(.97);
                transition: transform .28s cubic-bezier(.34, 1.56, .64, 1);
            }

            .enc-overlay.is-open .enc-modal {
                transform: translateY(0) scale(1);
            }

            .enc-modal__head {
                padding: 26px 30px 18px;
                text-align: center;
                border-bottom: 1px solid #eef2f6;
            }

            .enc-modal__icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 54px;
                height: 54px;
                border-radius: 50%;
                font-size: 27px;
                color: #fff;
                background: linear-gradient(90deg, #529cb2, #303e84);
                margin-bottom: 12px;
            }

            .enc-modal__title {
                font-family: 'Syne', sans-serif;
                font-size: 21px;
                font-weight: 800;
                color: #0f1923;
                margin: 0 0 6px;
            }

            .enc-modal__sub {
                font-size: 13.5px;
                color: #6b7a8d;
                margin: 0;
                line-height: 1.45;
            }

            .enc-body {
                padding: 4px 30px;
                overflow-y: auto;
            }

            .enc-q {
                padding: 16px 0;
                border-bottom: 1px solid #f1f5f9;
            }

            .enc-q:last-child {
                border-bottom: none;
            }

            .enc-q__title {
                font-size: 14px;
                font-weight: 600;
                color: #0f1923;
                margin: 0 0 12px;
                line-height: 1.4;
            }

            .enc-scale__opts {
                display: flex;
                gap: 8px;
            }

            .enc-scale__opts--nps {
                flex-wrap: wrap;
                gap: 6px;
            }

            .enc-pill {
                flex: 1;
                min-width: 40px;
                border: 1px solid #e2e8f0;
                background: #fff;
                border-radius: 12px;
                padding: 10px 0;
                font-size: 15px;
                font-weight: 700;
                color: #64748b;
                cursor: pointer;
                font-family: inherit;
                transition: all .15s ease;
            }

            .enc-pill--sm {
                flex: 0 0 auto;
                min-width: 34px;
                padding: 8px 0;
                font-size: 13px;
            }

            .enc-pill:hover {
                border-color: #529cb2;
                color: #303e84;
            }

            .enc-pill.is-active {
                background: linear-gradient(90deg, #529cb2, #303e84);
                border-color: transparent;
                color: #fff;
                box-shadow: 0 6px 16px rgba(48, 62, 132, .25);
            }

            .enc-scale__legend {
                display: flex;
                justify-content: space-between;
                margin-top: 6px;
                font-size: 11px;
                color: #94a3b8;
            }

            .enc-textarea {
                width: 100%;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 10px 12px;
                font-size: 13.5px;
                font-family: inherit;
                color: #0f1923;
                resize: vertical;
                outline: none;
                transition: border-color .2s ease;
                margin-top: 10px;
            }

            .enc-textarea--com {
                margin-top: 8px;
                background: #f8fafc;
            }

            .enc-textarea:focus {
                border-color: #529cb2;
            }

            .enc-actions {
                display: flex;
                gap: 12px;
                padding: 16px 30px;
                border-top: 1px solid #eef2f6;
            }

            .enc-btn {
                flex: 1;
                border: none;
                border-radius: 100px;
                padding: 13px 18px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                font-family: inherit;
                transition: transform .15s ease, opacity .2s ease;
            }

            .enc-btn:disabled {
                opacity: .6;
                cursor: default;
            }

            .enc-btn--ghost {
                background: #f1f5f9;
                color: #64748b;
            }

            .enc-btn--ghost:hover {
                background: #e6edf3;
            }

            .enc-btn--primary {
                background: linear-gradient(90deg, #529cb2, #303e84);
                color: #fff;
                box-shadow: 0 8px 24px rgba(48, 62, 132, .30);
            }

            .enc-btn--primary:hover:not(:disabled) {
                transform: translateY(-2px);
            }

            .enc-error {
                color: #dc2626;
                font-size: 13px;
                margin: 0 30px 14px;
                min-height: 16px;
            }

            @media (max-width: 560px) {
                .enc-modal__head, .enc-body, .enc-actions {
                    padding-left: 18px;
                    padding-right: 18px;
                }

                .enc-error {
                    margin-left: 18px;
                    margin-right: 18px;
                }
            }
        </style>

        @push('scripts')
            <script>
                (function () {
                    const overlay = document.getElementById('encuestaOverlay');
                    if (!overlay) {
                        return;
                    }

                    const btnEnviar = document.getElementById('encEnviar');
                    const btnRechazar = document.getElementById('encRechazar');
                    const errorEl = document.getElementById('encError');

                    const idTarjeta = {{ (int) $encuestaPendiente['id_tarjeta'] }};
                    const url = "{{ route('servicios.encuesta') }}";
                    const csrf = "{{ csrf_token() }}";

                    overlay.querySelectorAll('.enc-pill').forEach((pill) => {
                        pill.addEventListener('click', () => {
                            const key = pill.dataset.target;
                            const hidden = overlay.querySelector('input[type=hidden][data-field="' + key + '"]');
                            if (hidden) {
                                hidden.value = pill.dataset.value;
                            }
                            overlay.querySelectorAll('.enc-pill[data-target="' + key + '"]').forEach((p) => {
                                p.classList.toggle('is-active', p === pill);
                            });
                        });
                    });

                    const abrir = () => {
                        overlay.style.display = 'flex';
                        requestAnimationFrame(() => overlay.classList.add('is-open'));
                    };

                    const cerrar = () => {
                        overlay.classList.remove('is-open');
                        setTimeout(() => { overlay.style.display = 'none'; }, 260);
                    };

                    const enviar = (accion) => {
                        btnEnviar.disabled = true;
                        btnRechazar.disabled = true;
                        errorEl.textContent = '';

                        const payload = { id_tarjeta: idTarjeta, accion: accion };
                        if (accion === 'enviar') {
                            overlay.querySelectorAll('[data-field]').forEach((el) => {
                                const val = (el.value || '').toString().trim();
                                if (val !== '') {
                                    payload[el.dataset.field] = val;
                                }
                            });
                        }

                        axios.post(url, payload, {
                            headers: { 'X-CSRF-TOKEN': csrf }
                        }).then(() => {
                            cerrar();
                        }).catch((err) => {
                            btnEnviar.disabled = false;
                            btnRechazar.disabled = false;
                            errorEl.textContent = (err && err.response && err.response.data && err.response.data.error)
                                ? err.response.data.error
                                : 'No se pudo guardar tu encuesta. Inténtalo de nuevo.';
                        });
                    };

                    btnEnviar.addEventListener('click', () => enviar('enviar'));
                    btnRechazar.addEventListener('click', () => enviar('rechazar'));

                    abrir();
                })();
            </script>
        @endpush
    @endif
@endsection
