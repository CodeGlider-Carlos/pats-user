@extends('layouts.app')

@section('title', 'Mi pasaporte')

@section('content')

    <style>
        :root {
            --border: #e2e8f0;
            --navy: #f8fafc;
            --blue: #2563eb;
            --cream: #1e3a5f;
            --text: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --transition: 0.2s ease;
            --radius-md: 8px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .digi-container-pasaporte {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .digi-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .digi-page-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .digi-page-title i {
            font-size: 2rem;
            color: var(--blue);
        }

        .digi-page-subtitle {
            font-size: .95rem;
            color: var(--text-muted);
            margin: .25rem 0 0;
        }

        /* ── Tabs custom (sin depender de Bootstrap tabs JS) ── */
        .digi-tabs-wrapper {
            border-bottom: 2px solid var(--border);
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        .digi-tabs {
            display: flex;
            gap: .25rem;
            list-style: none;
            margin: 0;
            padding: 0;
            min-width: min-content;
        }

        .digi-tabs__item {
            flex-shrink: 0;
        }

        .digi-tabs__link {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .75rem 1.25rem;
            font-size: .9rem;
            font-weight: 500;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s;
            font-family: inherit;
        }

        .digi-tabs__link i {
            font-size: 1.1rem;
        }

        /* .digi-tabs__link:hover {
            color: var(--blue);
            background: var(--navy);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        } */

        .digi-tabs__link.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            font-weight: 600;
        }

        .digi-tabs__link:disabled {
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* ── Panels ── */
        .digi-panel {
            display: none;
        }

        .digi-panel.active {
            display: block;
        }

        /* ── Cards ── */
        .digi-card {
            display: block;
            width: 100%;
            margin-bottom: 1.5rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .digi-card__header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, var(--white), var(--navy));
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .digi-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .digi-card__title i {
            color: var(--blue);
            font-size: 1.4rem;
        }

        .digi-card__body {
            padding: 1.5rem;
        }

        .digi-card__footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            background: var(--navy);
        }

        /* ── Passport grid ── */
        .digi-passport-grid {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2rem;
            align-items: start;
        }

        @media(max-width:991px) {
            .digi-passport-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .digi-passport-photo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .digi-avatar--placeholder {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid var(--border);
            box-shadow: var(--shadow-md);
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 4rem;
        }

        .digi-passport-info {
            background: var(--navy);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        .digi-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        @media(max-width:768px) {
            .digi-info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media(max-width:480px) {
            .digi-container-pasaporte {
                padding: 0 14px;
            }

            .digi-page-title {
                font-size: 1.4rem;
            }

            .digi-page-title i {
                font-size: 1.6rem;
            }

            .digi-avatar--placeholder {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }

            .digi-card__body {
                padding: 1rem;
            }

            .digi-card__header {
                padding: 1rem;
            }

            .digi-passport-info {
                padding: 1rem;
            }

            .digi-membership-card {
                min-width: unset;
                width: 100%;
            }

            .digi-tabs__link {
                padding: 0.625rem 0.75rem;
                font-size: 0.82rem;
                gap: 0.3rem;
            }
        }

        @media(max-width:360px) {
            .digi-tabs__link i {
                display: none;
            }

            .digi-page-title {
                font-size: 1.25rem;
            }
        }

        .digi-info-item {
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .digi-info-label {
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--text-muted);
        }

        .digi-info-value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text);
        }

        .digi-membership-card {
            background: linear-gradient(135deg, var(--cream), var(--blue));
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            color: white;
            min-width: 200px;
            box-shadow: var(--shadow-md);
        }

        .digi-membership__plan {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .digi-membership__date {
            font-size: .85rem;
            opacity: .9;
            margin-bottom: .75rem;
        }

        .info-pill {
            display: inline-block;
            padding: .2rem .6rem;
            background: var(--navy);
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: .75rem;
            color: var(--text-muted);
        }

        .info-pill--highlight {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
        }

        .info-pill--success {
            background: #d1fae5;
            color: #065f46;
            border-color: #6ee7b7;
        }

        .info-pill--warning {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }

        .info-pill--danger {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        .info-icon {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            font-size: .85rem;
        }

        .info-icon--success i {
            color: #059669;
        }

        .info-icon--warning i {
            color: #d97706;
        }

        .info-icon--danger i {
            color: #dc2626;
        }

        .info-icon--info i {
            color: #2563eb;
        }

        .digi-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .625rem 1.25rem;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }

        .digi-btn--primary {
            background: var(--blue);
            color: white;
        }

        .digi-btn--primary:hover {
            background: var(--cream);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
            color: white;
        }

        /* Pagos */
        .pago-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: .85rem 1rem;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: .5rem;
            transition: all .15s;
        }

        .pago-row:hover {
            border-color: var(--blue);
            box-shadow: var(--shadow-sm);
        }

        .pago-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--blue), var(--cream));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .pago-monto {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--cream);
        }

        /* Empty */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--border);
            display: block;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-family: 'Syne', sans-serif;
            color: var(--cream);
            margin-bottom: .5rem;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        /* Clinical */
        .digi-section-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            color: var(--text);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .digi-section-title i {
            color: var(--blue);
        }

        .digi-clinical-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        @media(max-width:768px) {
            .digi-clinical-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .digi-clinical-section {
            background: var(--navy);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
        }

        .digi-clinical-section--alert {
            border-left: 4px solid #dc2626;
        }

        .digi-clinical-section__title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .digi-clinical-row {
            display: flex;
            justify-content: space-between;
            padding: .5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .digi-clinical-row:last-child {
            border-bottom: none;
        }

        .digi-clinical-label {
            font-size: .9rem;
            color: var(--text-muted);
        }

        .digi-clinical-value {
            font-size: .95rem;
            font-weight: 500;
            color: var(--text);
        }

        /* Accordion */
        .digi-accordion__item {
            border-bottom: 1px solid var(--border);
        }

        .digi-accordion__button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1.1rem 1.5rem;
            background: transparent;
            border: none;
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--cream);
            cursor: pointer;
            text-align: left;
        }

        .digi-accordion__button:hover {
            background: var(--navy);
        }

        .digi-accordion__icon {
            transition: transform .25s;
            font-size: 1.2rem;
            color: var(--blue);
            flex-shrink: 0;
            margin-left: auto;
        }

        .digi-accordion__button.collapsed .digi-accordion__icon {
            transform: rotate(-90deg);
        }

        .digi-accordion__body {
            padding: 1.5rem;
        }

        .mt-5 {
            margin-top: 3rem;
        }

        .d-none {
            display: none;
        }
    </style>

    <div class="digi-container-pasaporte">

        {{-- Header --}}
        <div class="digi-page-header">
            <div>
                <h1 class="digi-page-title"><i class="mdi mdi-card-account-details-outline"></i> Mi pasaporte</h1>
                <p class="digi-page-subtitle">Información general del pasaporte y acceso a servicios</p>
            </div>
            @if ($pasaporte)
                <button class="digi-btn digi-btn--primary" disabled style="background:#d1d5db;border-color:#d1d5db;color:#9ca3af;cursor:not-allowed;pointer-events:none;">
                    <i class="mdi mdi-credit-card-outline"></i> Renovar pasaporte
                </button>
            @endif
        </div>

        @if (!$pasaporte)
            <div class="digi-card">
                <div class="digi-card__body empty-state">
                    <i class="mdi mdi-card-off-outline"></i>
                    <h3>No tienes un pasaporte activo</h3>
                    <p>Adquiere tu Pasaporte a tu Salud y accede a todos los beneficios.</p>
                    <a href="{{ route('pagos') }}" class="digi-btn digi-btn--primary">
                        <i class="mdi mdi-plus"></i> Adquirir pasaporte
                    </a>
                </div>
            </div>
        @else
            {{-- Tabs --}}
            <div class="digi-tabs-wrapper">
                <ul class="digi-tabs" id="passportTabs">
                    <li class="digi-tabs__item">
                        <button class="digi-tabs__link active" data-panel="miPasaporte">
                            <i class="mdi mdi-card-account-details"></i> Mi pasaporte
                        </button>
                    </li>
                    @if (false) {{-- Historial de pagos: temporalmente oculto --}}
                    <li class="digi-tabs__item">
                        <button class="digi-tabs__link" data-panel="historialPagos">
                            <i class="mdi mdi-history"></i> Historial de pagos
                        </button>
                    </li>
                    @endif
                    <li class="digi-tabs__item">
                        <button class="digi-tabs__link" data-panel="pasaporteQR" disabled>
                            <i class="mdi mdi-qrcode"></i> Código QR
                        </button>
                    </li>
                    <li class="digi-tabs__item">
                        <button class="digi-tabs__link" data-panel="beneficios" disabled>
                            <i class="mdi mdi-gift"></i> Beneficios
                        </button>
                    </li>
                </ul>
            </div>

            {{-- PANEL: Mi Pasaporte --}}
            <div class="digi-panel active" id="miPasaporte">
                <div class="digi-card">
                    <div class="digi-card__body">
                        <div class="digi-passport-grid">

                            <div class="digi-passport-photo-section">
                                @if (!empty($pasaporte->foto_usuario))
                                    <img src="{{ route('perfil.foto') }}"
                                         class="digi-avatar--placeholder"
                                         style="object-fit:cover;"
                                         alt="Foto de perfil">
                                @else
                                    <div class="digi-avatar--placeholder">
                                        <i class="mdi mdi-account"></i>
                                    </div>
                                @endif
                                <div style="text-align:center;">
                                    <span class="info-pill info-pill--highlight">ID Pasaporte</span>
                                    <span style="display:block;margin-top:.5rem;font-weight:600;color:var(--text);">
                                        #PATS-{{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                                @if ($estadoColor === 'success')
                                    <span class="info-pill info-pill--success"><i class="mdi mdi-check-circle"></i>
                                        {{ $estadoTexto }}</span>
                                @elseif($estadoColor === 'warning')
                                    <span class="info-pill info-pill--warning"><i class="mdi mdi-clock-alert"></i>
                                        {{ $estadoTexto }}</span>
                                @else
                                    <span class="info-pill info-pill--danger"><i class="mdi mdi-alert-circle"></i>
                                        {{ $estadoTexto }}</span>
                                @endif
                            </div>

                            <div class="digi-passport-info">
                                <div class="digi-info-grid">
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Nombre completo</span>
                                        <span class="digi-info-value">{{ $pasaporte->nombres }}
                                            {{ $pasaporte->apellido_pa }} {{ $pasaporte->apellido_ma }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">CURP</span>
                                        <span class="digi-info-value">{{ $pasaporte->curp }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Fecha de nacimiento</span>
                                        <span
                                            class="digi-info-value">{{ \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Edad</span>
                                        <span class="digi-info-value">{{ $edad }} años</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Correo</span>
                                        <span class="digi-info-value">{{ $pasaporte->correo }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Teléfono</span>
                                        <span class="digi-info-value">{{ $pasaporte->telefono }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Región / Zona</span>
                                        <span class="digi-info-value">{{ $pasaporte->region }} /
                                            {{ $pasaporte->zona }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Tipo de cliente</span>
                                        <span class="digi-info-value">{{ ucfirst($pasaporte->tipo_cliente ?? '—') }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Fecha de alta</span>
                                        <span
                                            class="digi-info-value">{{ \Carbon\Carbon::parse($pasaporte->fecha_alta)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="digi-info-item">
                                        <span class="digi-info-label">Último pago</span>
                                        <span class="digi-info-value">
                                            {{ $pasaporte->fecha_ultimo_pago ? \Carbon\Carbon::parse($pasaporte->fecha_ultimo_pago)->format('d/m/Y') : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="digi-membership-card">
                                    <div style="font-weight:600;margin-bottom:1rem;">Pasaporte {{ $estadoTexto }}</div>
                                    <div class="digi-membership__plan">Plan
                                        {{ ucfirst(strtolower($pasaporte->frecuencia_pago)) }}</div>
                                    <div class="digi-membership__date">
                                        ${{ number_format($pasaporte->valor_final_pasaporte, 0) }} MXN</div>
                                    <div style="font-size:.85rem;margin-bottom:.5rem;">Vence:
                                        {{ $vencimiento->format('d/m/Y') }}</div>
                                    @if ($diasVigencia > 0)
                                        <div style="font-size:.8rem;opacity:.9;"><i class="mdi mdi-calendar-clock"></i>
                                            {{ $diasVigencia }} días restantes</div>
                                    @else
                                        <div style="font-size:.8rem;color:#fca5a5;"><i class="mdi mdi-alert"></i> Venció
                                            hace {{ abs($diasVigencia) }} días</div>
                                    @endif
                                </div>

                                @if ($pasaporte->meses_vencidos > 0)
                                    <div
                                        style="margin-top:1rem;padding:.75rem;background:#fee2e2;border-radius:var(--radius-md);border:1px solid #fca5a5;">
                                        <small style="color:#991b1b;">
                                            <i class="mdi mdi-alert-circle"></i>
                                            Meses vencidos: {{ $pasaporte->meses_vencidos }}<br>
                                            Recargo: ${{ number_format($pasaporte->recargo_acumulado, 2) }}
                                        </small>
                                    </div>
                                @endif

                                <div style="margin-top:1rem;">
                                    <button class="digi-btn digi-btn--primary" disabled
                                        style="width:100%;justify-content:center;background:#d1d5db;border-color:#d1d5db;color:#9ca3af;cursor:not-allowed;pointer-events:none;">
                                        <i class="mdi mdi-credit-card"></i>
                                        {{ $pasaporte->estatus === 'vencido' ? 'Reactivar' : 'Renovar' }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            @if (false) {{-- PANEL: Historial de pagos — temporalmente oculto --}}
            <div class="digi-panel" id="historialPagos">
                <div class="digi-card">
                    <div class="digi-card__header">
                        <h3 class="digi-card__title"><i class="mdi mdi-history"></i> Historial de pagos</h3>
                        <span class="info-pill">{{ $pagos->count() }} pago(s)</span>
                    </div>
                    <div class="digi-card__body">
                        @forelse($pagos as $pago)
                            <div class="pago-row">
                                <div class="pago-icon"><i class="mdi mdi-credit-card-check"></i></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;color:var(--cream);font-size:.95rem;">
                                        {{ ucfirst($pago->tipo_operacion ?? 'Pago PATS') }}
                                    </div>
                                    <div style="font-size:.8rem;color:var(--text-muted);">
                                        {{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') : '—' }}
                                        &nbsp;·&nbsp; {{ strtoupper($pago->proveedor_pasarela ?? '—') }}
                                        &nbsp;·&nbsp; {{ ucfirst($pago->frecuencia) }}
                                    </div>
                                    @if ($pago->referencia_pago)
                                        <div style="font-size:.75rem;color:var(--text-muted);font-family:monospace;">
                                            Ref: {{ $pago->referencia_pago }}
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align:right;">
                                    <div class="pago-monto">${{ number_format($pago->monto, 2) }}</div>
                                    @if ($pago->estatus_pago === 'confirmado')
                                        <span class="info-pill info-pill--success">Confirmado</span>
                                    @else
                                        <span class="info-pill info-pill--warning">{{ $pago->estatus_pago }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="mdi mdi-receipt-text-outline"></i>
                                <h3>Sin pagos registrados</h3>
                                <p>Aún no hay pagos asociados a tu pasaporte.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif {{-- /Historial de pagos --}}

            {{-- PANEL: QR --}}
            <div class="digi-panel" id="pasaporteQR">
                <div class="digi-card">
                    <div class="digi-card__body" style="text-align:center;">
                        <div style="max-width:440px;margin:0 auto;padding:2rem;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PATS-{{ $pasaporte->id_pasaporte }}|{{ urlencode($pasaporte->nombres) }}|{{ $pasaporte->curp }}"
                                style="width:200px;height:200px;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);margin-bottom:1.5rem;"
                                alt="QR PATS">
                            <h3
                                style="font-family:'Syne',sans-serif;font-size:1.4rem;color:var(--text);margin-bottom:.75rem;">
                                Pasaporte de Salud PATS</h3>
                            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Presenta este código QR para validar
                                tu identidad en la red hospitalaria.</p>
                            <div
                                style="background:var(--navy);padding:1rem;border-radius:var(--radius-md);margin-bottom:1.5rem;text-align:left;">
                                <ul style="margin:0;padding-left:1.2rem;color:var(--text);">
                                    <li>ID: #PATS-{{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}</li>
                                    <li>Nombre: {{ $pasaporte->nombres }} {{ $pasaporte->apellido_pa }}</li>
                                    <li>CURP: {{ $pasaporte->curp }}</li>
                                    <li>Vigencia: {{ $vencimiento->format('d/m/Y') }}</li>
                                </ul>
                            </div>
                            <button class="digi-btn digi-btn--primary"><i class="mdi mdi-download"></i> Descargar
                                QR</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL: Beneficios --}}
            <div class="digi-panel" id="beneficios">
                <div class="digi-card">
                    <div class="digi-card__header">
                        <h3 class="digi-card__title"><i class="mdi mdi-gift-outline"></i> Beneficios incluidos</h3>
                        <span class="info-pill info-pill--success">Plan
                            {{ ucfirst(strtolower($pasaporte->frecuencia_pago)) }}</span>
                    </div>
                    <div class="digi-card__body">
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
                            @foreach ([['mdi-stethoscope', 'Consultas médicas', 'Sin restricciones en toda la red hospitalaria.'], ['mdi-radiology-box', 'Estudios de gabinete', 'Rayos X, ultrasonidos y laboratorio con descuento.'], ['mdi-tooth-outline', 'Dental', 'Limpiezas, extracciones y valoraciones.'], ['mdi-eye-outline', 'Optometría', 'Consulta y lentes con 30% de descuento.'], ['mdi-ambulance', 'Urgencias 24/7', 'Atención en hospitales afiliados sin copago.'], ['mdi-pill', 'Medicamentos', 'Hasta 40% de descuento en farmacias afiliadas.']] as [$icon, $titulo, $desc])
                                <div
                                    style="display:flex;gap:1rem;padding:1rem;background:var(--navy);border-radius:var(--radius-md);">
                                    <div
                                        style="width:48px;height:48px;background:linear-gradient(135deg,var(--blue),var(--cream));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem;flex-shrink:0;">
                                        <i class="mdi {{ $icon }}"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:var(--text);margin-bottom:.25rem;">
                                            {{ $titulo }}</div>
                                        <div style="font-size:.85rem;color:var(--text-muted);">{{ $desc }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        @endif

        @if (true) {{-- Historia clínica --}}
        @if ($pasaporte)
            @php
                $hc = $historiaClinica ?? null;
                $hf = $hc?->heredo_familiares ?? [];
                $sinInfo = '<span style="color:var(--text-muted);font-style:italic;">Sin información registrada</span>';
            @endphp
            <div class="mt-5">
                <h2 class="digi-section-title"><i class="mdi mdi-file-document-multiple"></i> Historia clínica</h2>
                <div class="digi-card">

                    {{-- Accordion: Perfil social y hábitos --}}
                    <div class="digi-accordion__item">
                        <button class="digi-accordion__button collapsed" data-accordion="collapsePerfil">
                            <i class="mdi mdi-account-group"></i> Perfil social y hábitos
                            <i class="mdi mdi-chevron-down digi-accordion__icon"></i>
                        </button>
                        <div id="collapsePerfil" style="display:none;">
                            <div class="digi-accordion__body">
                                <div class="digi-clinical-section">
                                    @foreach ([
                                        ['Ocupación',      $hc->ocupacion       ?? null],
                                        ['Estado civil',   $hc->estado_civil    ?? null],
                                        ['Escolaridad',    $hc->escolaridad     ?? null],
                                        ['Actividad física', $hc->actividad_fisica ?? null],
                                        ['Tabaquismo',     $hc->tabaquismo      ?? null],
                                        ['Alcohol',        $hc->alcohol         ?? null],
                                        ['Alimentación',   $hc->alimentacion    ?? null],
                                    ] as [$lbl, $val])
                                        <div class="digi-clinical-row">
                                            <span class="digi-clinical-label">{{ $lbl }}:</span>
                                            <span class="digi-clinical-value">
                                                {!! $val ? e($val) : $sinInfo !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Accordion: Antecedentes médicos --}}
                    <div class="digi-accordion__item">
                        <button class="digi-accordion__button collapsed" data-accordion="collapseAntecedentes">
                            <i class="mdi mdi-family-tree"></i> Antecedentes médicos
                            <i class="mdi mdi-chevron-down digi-accordion__icon"></i>
                        </button>
                        <div id="collapseAntecedentes" style="display:none;">
                            <div class="digi-accordion__body">
                                <div class="digi-clinical-section">
                                    <div class="digi-clinical-row">
                                        <span class="digi-clinical-label">Heredo-familiares:</span>
                                        <span class="digi-clinical-value">
                                            @if (!empty($hf))
                                                @foreach ($hf as $item)
                                                    <span class="info-icon info-icon--warning" style="{{ !$loop->first ? 'margin-left:.5rem;' : '' }}">
                                                        <i class="mdi mdi-alert"></i> {{ $item }}
                                                    </span>
                                                @endforeach
                                            @else
                                                {!! $sinInfo !!}
                                            @endif
                                        </span>
                                    </div>
                                    @foreach ([
                                        ['Personales patológicos',     $hc->personales_patologicos    ?? null],
                                        ['Personales no patológicos',  $hc->personales_no_patologicos ?? null],
                                        ['Enfermedades previas',       $hc->enfermedades_previas      ?? null],
                                    ] as [$lbl, $val])
                                        <div class="digi-clinical-row">
                                            <span class="digi-clinical-label">{{ $lbl }}:</span>
                                            <span class="digi-clinical-value">
                                                {!! $val ? e($val) : $sinInfo !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Accordion: Alertas de seguridad --}}
                    <div class="digi-accordion__item">
                        <button class="digi-accordion__button collapsed" data-accordion="collapseAlertas">
                            <i class="mdi mdi-alert-circle" style="color:#e74c3c;"></i> Alertas de seguridad
                            <i class="mdi mdi-chevron-down digi-accordion__icon"></i>
                        </button>
                        <div id="collapseAlertas" style="display:none;">
                            <div class="digi-accordion__body">
                                <div class="digi-clinical-section digi-clinical-section--alert">
                                    @foreach ([
                                        ['Alergias',     $hc->alergias     ?? null],
                                        ['Cirugías',     $hc->cirugias     ?? null],
                                        ['Medicamentos', $hc->medicamentos ?? null],
                                        ['Intolerancias', $hc->intolerancias ?? null],
                                    ] as [$lbl, $val])
                                        <div class="digi-clinical-row">
                                            <span class="digi-clinical-label">{{ $lbl }}:</span>
                                            <span class="digi-clinical-value">
                                                {!! $val ? e($val) : $sinInfo !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Accordion: Estado general --}}
                    <div class="digi-accordion__item">
                        <button class="digi-accordion__button collapsed" data-accordion="collapseEstado">
                            <i class="mdi mdi-heart-pulse"></i> Estado general
                            <i class="mdi mdi-chevron-down digi-accordion__icon"></i>
                        </button>
                        <div id="collapseEstado" style="display:none;">
                            <div class="digi-accordion__body">
                                <div class="digi-clinical-section">
                                    @foreach ([
                                        ['Peso',   $hc && $hc->peso   ? $hc->peso   . ' kg' : null],
                                        ['Altura', $hc && $hc->altura ? $hc->altura . ' m'  : null],
                                        ['IMC',    $hc && $hc->imc    ? $hc->imc           : null],
                                    ] as [$lbl, $val])
                                        <div class="digi-clinical-row">
                                            <span class="digi-clinical-label">{{ $lbl }}:</span>
                                            <span class="digi-clinical-value">
                                                {!! $val ? e($val) : $sinInfo !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif
        @endif {{-- /Historia clínica --}}

    </div>

    <script>
        // ── Tabs custom — sin depender de Bootstrap JS ──
        document.querySelectorAll('#passportTabs .digi-tabs__link').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                // Desactivar todos
                document.querySelectorAll('#passportTabs .digi-tabs__link').forEach(b => b.classList.remove(
                    'active'));
                document.querySelectorAll('.digi-panel').forEach(p => p.classList.remove('active'));
                // Activar el clickeado
                btn.classList.add('active');
                const panelId = btn.getAttribute('data-panel');
                document.getElementById(panelId)?.classList.add('active');
            });
        });

        // ── Accordion custom ─────────────────────────────
        document.querySelectorAll('[data-accordion]').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-accordion');
                const panel = document.getElementById(targetId);
                const isOpen = panel.style.display !== 'none';

                // cerrar todos los demás (comportamiento acordeón)
                document.querySelectorAll('[data-accordion]').forEach(otherBtn => {
                    const otherId = otherBtn.getAttribute('data-accordion');
                    const otherPanel = document.getElementById(otherId);
                    if (otherId !== targetId) {
                        otherPanel.style.display = 'none';
                        otherBtn.classList.add('collapsed');
                    }
                });

                // toggle del clickeado
                panel.style.display = isOpen ? 'none' : 'block';
                btn.classList.toggle('collapsed', isOpen);
            });
        });
    </script>

@endsection
