@extends('layouts.app')

@section('title', 'Mis pagos')

@section('content')
    <style>
        /* ===========================
                           ESTILOS UNIFICADOS CON PASAPORTE
                           =========================== */

        :root {
            --border: #e2e8f0;
            --navy: #f8fafc;
            --blue: #2563eb;
            --cream: #1e3a5f;
            --text: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --danger: #dc2626;
            --success: #10b981;
            --success-light: #d1fae5;
            --transition: 0.2s ease;
            --radius-md: 8px;
            --radius-lg: 16px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .digi-container-pagosd {
            display: block;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Page Header */
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
            gap: 0.75rem;
        }

        .digi-page-title__icon {
            font-size: 2rem;
            color: var(--blue);
        }

        .digi-page-subtitle {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0.25rem 0 0 0;
        }

        /* Stats Cards */
        .digi-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .digi-stat-card {
            background: linear-gradient(135deg, var(--white), var(--navy));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .digi-stat-card__icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--blue), var(--cream));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .digi-stat-card__content h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0;
            line-height: 1.2;
        }

        .digi-stat-card__content p {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Passport Card */
        .digi-passport-card {
            background: linear-gradient(135deg, var(--white), var(--navy));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .digi-passport-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--blue), var(--cream));
            opacity: 0.03;
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .digi-passport-photo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--white);
            box-shadow: var(--shadow-lg);
            margin-bottom: 1rem;
        }

        .digi-passport-id {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text-muted);
            background: var(--white);
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            display: inline-block;
            border: 1px solid var(--border);
        }

        .digi-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .digi-info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .digi-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .digi-info-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
        }

        .digi-info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text);
        }

        /* Tabs */
        .digi-tabs {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            margin: 0 0 1.5rem 0;
            padding: 0;
            border-bottom: 1px solid var(--border);
        }

        .digi-tabs__item {
            flex-shrink: 0;
        }

        .digi-tabs__link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            transition: all var(--transition);
            cursor: pointer;
            white-space: nowrap;
        }

        .digi-tabs__link i {
            font-size: 1.2rem;
        }

        .digi-tabs__link:hover {
            color: var(--blue);
            background: var(--navy);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }

        .digi-tabs__link.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            font-weight: 600;
        }

        /* Credit Card */
        .credit-card {
            width: 100%;
            max-width: 320px;
            height: 200px;
            margin: 0 auto 2rem;
            perspective: 1000px;
            cursor: pointer;
        }

        .credit-card:hover .card-face {
            box-shadow: var(--shadow-lg);
        }

        .card-face {
            width: 100%;
            height: 100%;
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            position: absolute;
            backface-visibility: hidden;
            transition: transform 0.6s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(135deg, #1a1f2e, #2d3349);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .card-front.visa {
            background: linear-gradient(135deg, #0b3d60, #06283d);
        }

        .card-back {
            transform: rotateY(180deg);
            background: linear-gradient(135deg, #2d3349, #1a1f2e);
        }

        .credit-card.flipped .card-front {
            transform: rotateY(180deg);
        }

        .credit-card.flipped .card-back {
            transform: rotateY(0deg);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-chip {
            width: 40px;
            height: 30px;
            background: linear-gradient(135deg, #b8860b, #daa520);
            border-radius: 6px;
        }

        .card-brand {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .card-number {
            font-size: 1.1rem;
            letter-spacing: 2px;
            font-family: monospace;
            text-align: center;
        }

        .card-bottom {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }

        .card-bottom small {
            font-size: 0.6rem;
            opacity: 0.7;
        }

        .magnetic-strip {
            height: 40px;
            background: #000;
            margin: 20px 0;
        }

        .cvv-box {
            background: white;
            color: black;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: right;
            font-family: monospace;
            font-size: 1rem;
        }

        /* Form Styles */
        .digi-form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
            display: block;
        }

        .digi-form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            transition: all var(--transition);
            background: var(--white);
        }

        .digi-form-control:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Payment Method Cards */
        .digi-payment-card {
            background: linear-gradient(135deg, var(--white), var(--navy));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            transition: all var(--transition);
        }

        .digi-payment-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--blue);
        }

        .digi-payment-card__icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--navy), var(--border));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 2.5rem;
        }

        .digi-payment-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--cream);
            margin-bottom: 0.5rem;
        }

        .digi-payment-card__features {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
            text-align: left;
        }

        .digi-payment-card__features li {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-payment-card__features li i {
            color: var(--blue);
            font-size: 1rem;
        }

        /* Table Styles */
        .digi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .digi-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: 0.75rem;
            text-align: left;
        }

        .digi-table td {
            background: var(--white);
            padding: 1rem;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            color: var(--text);
        }

        .digi-table tr td:first-child {
            border-radius: var(--radius-md) 0 0 var(--radius-md);
        }

        .digi-table tr td:last-child {
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
        }

        .digi-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            font-family: 'DM Sans', sans-serif;
            line-height: 1.5;
            white-space: nowrap;
        }

        .digi-badge--success {
            background: var(--success-light) !important;
            color: var(--success) !important;
        }

        .digi-badge--info {
            background: #dbeafe !important;
            color: #1e40af !important;
        }

        /* Mobile Card */
        .digi-mobile-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all var(--transition);
        }

        .digi-mobile-card:hover {
            border-color: var(--blue);
            box-shadow: var(--shadow-sm);
        }

        /* Buttons */
        .digi-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
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
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .digi-btn--outline {
            background: transparent;
            border-color: var(--border);
            color: var(--text);
        }

        .digi-btn--outline:hover {
            border-color: var(--blue);
            color: var(--blue);
            background: var(--navy);
        }

        .digi-btn--sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Filter Selects */
        .digi-filter-select {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--white);
            cursor: pointer;
            transition: all var(--transition);
        }

        .digi-filter-select:focus {
            outline: none;
            border-color: var(--blue);
        }

        /* Modal */
        .digi-modal .modal-content {
            border-radius: var(--radius-lg);
            border: none;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .digi-modal .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, var(--white), var(--navy));
        }

        .digi-modal .modal-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .digi-modal .modal-title i {
            color: var(--blue);
            font-size: 1.4rem;
        }

        .digi-modal .modal-body {
            padding: 1.5rem;
        }

        .digi-modal .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            background: var(--navy);
        }

        /* Utilities */
        .d-none {
            display: none;
        }

        .d-md-block {
            display: none;
        }

        .d-md-none {
            display: block;
        }

        @media (min-width: 768px) {
            .d-md-block {
                display: block;
            }

            .d-md-none {
                display: none;
            }
        }
    </style>
    <style>
        .digi-plan-selector {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .plan-option {
            border: 1px solid #e5e7eb;
            background: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: .2s;
            display: flex;
            flex-direction: column;
        }

        .plan-option strong {
            font-size: .9rem;
        }

        .plan-option span {
            font-size: .8rem;
            color: #666;
        }

        .plan-option:hover {
            border-color: #4f46e5;
        }

        .plan-option.active {
            border-color: #4f46e5;
            background: #eef2ff;
        }

        @media (max-width: 640px) {
            .digi-plan-selector {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 460px) {
            .digi-plan-selector {
                grid-template-columns: repeat(2, 1fr);
            }

            .plan-option {
                padding: 10px 8px;
            }

            .plan-option strong {
                font-size: 0.82rem;
            }

            .plan-option span {
                font-size: 0.72rem;
            }
        }
    </style>
    <style>
        @media (max-width: 640px) {
            .digi-container-pagosd {
                padding: 0 14px;
            }

            .digi-page-title {
                font-size: 1.4rem;
            }

            .digi-passport-card {
                padding: 1.25rem;
            }

            .digi-passport-photo {
                width: 110px;
                height: 110px;
            }

            .credit-card {
                max-width: 100%;
            }

            .digi-tabs__link {
                padding: 0.625rem 0.875rem;
                font-size: 0.85rem;
            }

            .digi-stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .digi-stat-card {
                padding: 1rem;
            }

            .digi-stat-card__icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .digi-stat-card__content h3 {
                font-size: 1.2rem;
            }

            .digi-passport-card {
                padding: 1rem;
            }

            .digi-passport-photo {
                width: 90px;
                height: 90px;
            }

            .digi-tabs__link i {
                display: none;
            }
        }
    </style>
    @php
        $pagos = [
            [
                'folio' => 'PATS-0001',
                'producto' => 'El Pasaporte a tu Salud (PATS)',
                'fecha' => '15/01/2026',
                'monto' => 1200.0,
                'metodo' => 'Tarjeta de crédito',
                'estatus' => 'Pagado',
                'concepto' => 'Tarjeta anual',
            ],
            [
                'folio' => 'PATS-0002',
                'producto' => 'Renovación anual PATS',
                'fecha' => '15/01/2027',
                'monto' => 1200.0,
                'metodo' => 'Tarjeta de débito',
                'estatus' => 'Pagado',
                'concepto' => 'Renovación',
            ],
        ];

        $totalPagado = collect($pagos)->sum('monto');
        $totalPagos = count($pagos);
        $ultimoPago = collect($pagos)->last()['fecha'];
    @endphp

    <div class="digi-container-pagosd py-4">
        {{-- Header --}}
        <div class="digi-page-header">
            <div>
                <h1 class="digi-page-title">
                    <i class="mdi mdi-credit-card-digi-page-title__icon"></i>
                    Mis pagos
                </h1>
                <p class="digi-page-subtitle">
                    Historial de pagos y renovación de membresía
                </p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="digi-stats-grid">
            <div class="digi-stat-card">
                <div class="digi-stat-card__icon">
                    <i class="mdi mdi-file-document"></i>
                </div>
                <div class="digi-stat-card__content">
                    <h3>{{ $totalPagos }}</h3>
                    <p>Pagos realizados</p>
                </div>
            </div>
            <div class="digi-stat-card">
                <div class="digi-stat-card__icon">
                    <i class="mdi mdi-calendar"></i>
                </div>
                <div class="digi-stat-card__content">
                    <h3>{{ $ultimoPago }}</h3>
                    <p>Último pago</p>
                </div>
            </div>
        </div>

        {{-- Passport Card --}}
        <div class="digi-passport-card">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="digi-passport-photo" alt="perfil">
                    <div class="digi-passport-id mt-2">
                        <i class="mdi mdi-card-account-details"></i>
                        ID: 8219371237498236
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="digi-info-grid">
                        <div class="digi-info-item">
                            <span class="digi-info-label">Nombre completo</span>
                            <span class="digi-info-value">Juan Carlos Hernández López</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">RFC</span>
                            <span class="digi-info-value">HELJ850812XXX</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Fecha nacimiento</span>
                            <span class="digi-info-value">12/08/1985</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Correo</span>
                            <span class="digi-info-value">juan@email.com</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Teléfono</span>
                            <span class="digi-info-value">55 1234 5678</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Dirección</span>
                            <span class="digi-info-value">Av. Reforma 123, CDMX</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Tabs --}}
        <div class="digi-tabs-wrapper" style="margin-bottom: 1.5rem;">
            <ul class="digi-tabs" id="paymentTabs" role="tablist">
                <li class="digi-tabs__item">
                    <button class="digi-tabs__link active" data-bs-toggle="tab" data-bs-target="#pay-card">
                        <i class="mdi mdi-credit-card-outline"></i>
                        Tarjeta
                    </button>
                </li>
                <li class="digi-tabs__item">
                    <button class="digi-tabs__link" data-bs-toggle="tab" data-bs-target="#pay-oxxo">
                        <i class="mdi mdi-store"></i>
                        OXXO
                    </button>
                </li>
                <li class="digi-tabs__item">
                    <button class="digi-tabs__link" data-bs-toggle="tab" data-bs-target="#pay-spei">
                        <i class="mdi mdi-bank-transfer"></i>
                        SPEI
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="mb-4">
                <label class="digi-form-label">Selecciona tu plan</label>

                <div class="digi-plan-selector">

                    <button type="button" class="plan-option active" data-months="1" data-price="1200">
                        <strong>1 mes</strong>
                        <span>$800 MXN</span>
                    </button>

                    <button type="button" class="plan-option" data-months="2" data-price="2200">
                        <strong>2 meses</strong>
                        <span>$1600 MXN</span>
                    </button>

                    <button type="button" class="plan-option" data-months="3" data-price="3000">
                        <strong>3 meses</strong>
                        <span>$2400 MXN</span>
                    </button>

                    <button type="button" class="plan-option" data-months="6" data-price="5500">
                        <strong>6 meses</strong>
                        <span>$4800 MXN</span>
                    </button>


                    <button type="button" class="plan-option" data-months="6" data-price="5500">
                        <strong>1 año</strong>
                        <span>$9600 MXN</span>
                    </button>

                </div>
            </div>
            {{-- Tarjeta --}}
            <div class="tab-pane fade show active" id="pay-card">
                <div class="row">
                    <div class="col-md-5 mb-4 mb-md-0">
                        <div class="credit-card" id="creditCard" onclick="this.classList.toggle('flipped')">
                            <div class="card-face card-front visa">
                                <div class="card-top">
                                    <div class="card-chip"></div>
                                    <div class="card-brand" id="cardBrand">VISA</div>
                                </div>
                                <div class="card-number" id="cardNumberDisplay">•••• •••• •••• ••••</div>
                                <div class="card-bottom">
                                    <div>
                                        <small>Titular</small>
                                        <div id="cardNameDisplay">JUAN C. HERNÁNDEZ</div>
                                    </div>
                                    <div>
                                        <small>Vence</small>
                                        <div id="cardExpiryDisplay">12/25</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-face card-back">
                                <div class="magnetic-strip"></div>
                                <div class="cvv-box">
                                    <span id="cardCvvDisplay">***</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h5 class="digi-page-title" style="font-size: 1.2rem;">Renueva tu Pasaporte de la Salud</h5>
                        <p class="digi-page-subtitle">Paga con tarjeta de crédito o débito de forma segura.</p>

                        <form>
                            <div class="mb-3">
                                <label class="digi-form-label">Nombre del titular</label>
                                <input type="text" class="digi-form-control" id="nameInput"
                                    value="JUAN C. HERNÁNDEZ">
                            </div>
                            <div class="mb-3">
                                <label class="digi-form-label">Número de tarjeta</label>
                                <input type="text" class="digi-form-control" id="cardInput" value="4111111111111111">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="digi-form-label">Vencimiento</label>
                                    <input type="text" class="digi-form-control" id="expiryInput" placeholder="MM/AA"
                                        value="12/25">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="digi-form-label">CVV</label>
                                    <input type="password" class="digi-form-control" id="cvvInput" value="123">
                                </div>
                            </div>
                            <button class="digi-btn digi-btn--primary w-100">
                                <i class="mdi mdi-lock"></i>
                                Pagar $1,200.00 MXN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- OXXO --}}
            <div class="tab-pane fade" id="pay-oxxo">
                <div class="digi-payment-card">
                    <div class="digi-payment-card__icon">
                        <i class="mdi mdi-store"></i>
                    </div>
                    <h3 class="digi-payment-card__title">Pago en OXXO</h3>
                    <p class="digi-page-subtitle">Genera un código y paga en cualquier tienda OXXO</p>

                    <ul class="digi-payment-card__features">
                        <li><i class="mdi mdi-clock-outline"></i> Disponible 24/7</li>
                        <li><i class="mdi mdi-credit-card-outline"></i> No necesitas tarjeta</li>
                        <li><i class="mdi mdi-check-circle"></i> Confirmación automática</li>
                    </ul>

                    <button class="digi-btn digi-btn--primary w-100">
                        <i class="mdi mdi-barcode"></i>
                        Generar código OXXO
                    </button>
                </div>
            </div>

            {{-- SPEI --}}
            <div class="tab-pane fade" id="pay-spei">
                <div class="digi-payment-card">
                    <div class="digi-payment-card__icon">
                        <i class="mdi mdi-bank-transfer"></i>
                    </div>
                    <h3 class="digi-payment-card__title">Transferencia SPEI</h3>
                    <p class="digi-page-subtitle">Transfiere desde tu banca en línea</p>

                    <ul class="digi-payment-card__features">
                        <li><i class="mdi mdi-clock-fast"></i> Pago inmediato</li>
                        <li><i class="mdi mdi-shield"></i> Seguro y rastreable</li>
                        <li><i class="mdi mdi-cash"></i> Sin comisiones</li>
                    </ul>

                    <button class="digi-btn digi-btn--primary w-100">
                        <i class="mdi mdi-qrcode"></i>
                        Generar referencia SPEI
                    </button>
                </div>
            </div>
        </div>

        {{-- Historial de pagos --}}
        {{-- <div class="digi-card mt-5">
            <div class="digi-card__header">
                <h3 class="digi-card__title">
                    <i class="mdi mdi-history"></i>
                    Histórico de pagos
                </h3>

                <div class="d-flex gap-2">
                    <select class="digi-filter-select">
                        <option>Todos los meses</option>
                        <option>Enero</option>
                        <option>Febrero</option>
                        <option>Marzo</option>
                    </select>
                    <select class="digi-filter-select">
                        <option>2026</option>
                        <option>2027</option>
                    </select>
                </div>
            </div>

            <div class="digi-card__body">
                <div class="d-none d-md-block">
                    <table class="digi-table">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Producto</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estatus</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagos as $pago)
                                <tr>
                                    <td><strong>{{ $pago['folio'] }}</strong></td>
                                    <td>{{ $pago['producto'] }}</td>
                                    <td>{{ $pago['fecha'] }}</td>
                                    <td><strong>${{ number_format($pago['monto'], 2) }}</strong></td>
                                    <td>
                                        <span class="digi-badge digi-badge--success">
                                            <i class="mdi mdi-check-circle"></i>
                                            {{ $pago['estatus'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="digi-btn digi-btn--outline digi-btn--sm" data-bs-toggle="modal"
                                            data-bs-target="#modalPago">
                                            <i class="mdi mdi-eye"></i>
                                            Ver detalle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none">
                    @foreach ($pagos as $pago)
                        <div class="digi-mobile-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="digi-info-label">{{ $pago['fecha'] }}</span>
                                <span class="digi-badge digi-badge--success">
                                    {{ $pago['estatus'] }}
                                </span>
                            </div>

                            <h4 class="digi-card__title" style="font-size: 1rem;">{{ $pago['producto'] }}</h4>

                            <p class="digi-page-subtitle mb-2">
                                Folio: {{ $pago['folio'] }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="digi-medicamento-card__price" style="font-size: 1.1rem;">
                                    ${{ number_format($pago['monto'], 2) }}
                                </span>

                                <button class="digi-btn digi-btn--outline digi-btn--sm" data-bs-toggle="modal"
                                    data-bs-target="#modalPago">
                                    Ver
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div> --}}
    </div>

    <div class="modal fade digi-modal" id="modalPago" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-file-document"></i>
                        Ficha de pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="digi-page-subtitle mb-4">
                        El Pasaporte a tu Salud (PATS) es una membresía innovadora creada por
                        <strong>Fifty Doctors</strong>.
                    </p>

                    <div class="digi-info-grid">
                        <div class="digi-info-item">
                            <span class="digi-info-label">Folio</span>
                            <span class="digi-info-value">PATS-0001</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Fecha de pago</span>
                            <span class="digi-info-value">15/01/2026</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Monto</span>
                            <span class="digi-info-value digi-medicamento-card__price">$1,200.00 MXN</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Método de pago</span>
                            <span class="digi-info-value">Tarjeta de crédito</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Producto</span>
                            <span class="digi-info-value">El Pasaporte a tu Salud (PATS)</span>
                        </div>
                        <div class="digi-info-item">
                            <span class="digi-info-label">Estatus</span>
                            <span class="digi-badge digi-badge--success">
                                <i class="mdi mdi-check-circle"></i>
                                Pagado
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="digi-btn digi-btn--outline" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button class="digi-btn digi-btn--primary">
                        <i class="mdi mdi-download"></i>
                        Descargar comprobante
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sincronizar tarjeta de crédito con el formulario
            const nameInput = document.getElementById('nameInput');
            const cardInput = document.getElementById('cardInput');
            const expiryInput = document.getElementById('expiryInput');
            const cvvInput = document.getElementById('cvvInput');

            const cardNumberDisplay = document.getElementById('cardNumberDisplay');
            const cardNameDisplay = document.getElementById('cardNameDisplay');
            const cardExpiryDisplay = document.getElementById('cardExpiryDisplay');
            const cardCvvDisplay = document.getElementById('cardCvvDisplay');

            if (nameInput) {
                nameInput.addEventListener('input', function(e) {
                    cardNameDisplay.textContent = e.target.value.toUpperCase() || 'JUAN C. HERNÁNDEZ';
                });
            }

            if (cardInput) {
                cardInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim();
                    cardNumberDisplay.textContent = value || '•••• •••• •••• ••••';
                });
            }

            if (expiryInput) {
                expiryInput.addEventListener('input', function(e) {
                    cardExpiryDisplay.textContent = e.target.value || '12/25';
                });
            }

            if (cvvInput) {
                cvvInput.addEventListener('input', function(e) {
                    cardCvvDisplay.textContent = e.target.value || '***';
                });
            }
        });

        const plans = document.querySelectorAll('.plan-option');
        const payButton = document.querySelector('.digi-btn--primary');

        plans.forEach(plan => {

            plan.addEventListener('click', () => {

                plans.forEach(p => p.classList.remove('active'));
                plan.classList.add('active');

                const price = plan.dataset.price;

                payButton.innerHTML = `
            <i class="mdi mdi-lock"></i>
            Pagar $${Number(price).toLocaleString()} MXN
        `;

            });

        });
    </script>
@endsection
