@extends('layouts.app')

@section('title', 'Hospitales')

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
            --transition: 0.2s ease;
            --radius-md: 8px;
            --radius-lg: 16px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .digi-container-h {
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

        /* Breadcrumb */
        .digi-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-family: 'DM Sans', sans-serif;
        }

        .digi-breadcrumb__link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all var(--transition);
        }

        .digi-breadcrumb__link:hover {
            color: var(--blue);
        }

        .digi-breadcrumb__link i {
            font-size: 1.2rem;
        }

        .digi-breadcrumb__separator {
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        .digi-breadcrumb__current {
            color: var(--text);
            font-weight: 500;
            font-size: 0.95rem;
        }

        /* Cards de hospitales */
        .digi-hospital-card {
            display: block;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
            text-decoration: none;
            color: inherit;
            height: 100%;
            overflow: hidden;
        }

        .digi-hospital-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--blue);
        }

        .digi-hospital-card__image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform var(--transition);
        }

        .digi-hospital-card:hover .digi-hospital-card__image {
            transform: scale(1.05);
        }

        .digi-hospital-card__body {
            padding: 1.5rem;
        }

        .digi-hospital-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 0.5rem 0;
        }

        .digi-hospital-card__address {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .digi-hospital-card__address i {
            color: var(--blue);
            font-size: 1rem;
            margin-top: 0.2rem;
        }

        .digi-hospital-card__actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
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

        .digi-btn--success {
            background: #25D366;
            color: white;
        }

        .digi-btn--success:hover {
            background: #128C7E;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            color: white;
        }

        .digi-btn--secondary {
            background: transparent;
            border-color: var(--border);
            color: var(--text);
        }

        .digi-btn--secondary:hover {
            border-color: var(--text);
            color: var(--text);
            background: var(--navy);
        }

        .digi-btn--sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Badge */
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

        .digi-badge--info {
            background: #dbeafe !important;
            color: #1e40af !important;
        }

        /* Grid */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -12px;
            margin-left: -12px;
        }

        .col-md-4 {
            padding-right: 12px;
            padding-left: 12px;
        }

        .g-4 {
            gap: 1.5rem;
        }

        /* Utilities */
        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        /* ── Responsive ───────────────────────────── */
        @media (min-width: 769px) {
            .col-md-4 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
        }

        @media (max-width: 900px) and (min-width: 561px) {
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 560px) {
            .digi-container-h {
                padding: 0 14px;
            }

            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .digi-page-title {
                font-size: 1.4rem;
            }

            .digi-hospital-card__image {
                height: 160px;
            }

            .digi-hospital-card__actions {
                flex-direction: column;
            }

            .digi-hospital-card__actions .digi-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 400px) {
            .digi-hospital-card__body {
                padding: 1rem;
            }

            .digi-hospital-card__title {
                font-size: 1rem;
            }
        }
    </style>

    <div class="digi-container-h py-4">

        {{-- Breadcrumb --}}
        <div class="digi-breadcrumb">
            <a href="{{ url('/servicios') }}" class="digi-breadcrumb__link">
                <i class="mdi mdi-arrow-left"></i>
                Regresar
            </a>
            <span class="digi-breadcrumb__separator">/</span>
            <span class="digi-breadcrumb__current">Hospitales</span>
        </div>

        {{-- Header --}}
        <div class="digi-page-header">
            <div>
                <h1 class="digi-page-title">
                    <i class="mdi mdi-hospital-building digi-page-title__icon"></i>
                    Hospitales disponibles
                </h1>
                <p class="digi-page-subtitle">
                    Red hospitalaria afiliada a PATS
                </p>
            </div>
            <span class="digi-badge digi-badge--info">{{ $hospitales->count() }} centros</span>
        </div>

        {{-- Grid de hospitales --}}
        <div class="row">
            @foreach ($hospitales as $hospital)
                <div class="col-md-4 mb-4">
                    <div class="digi-hospital-card">
                        {{-- Imagen --}}
                        <img src="{{ $hospital->imagen_url }}" class="digi-hospital-card__image"
                            alt="{{ $hospital->nombre_unidad }}" onerror="this.src='https://placehold.co/600x400?text=Hospital'">

                        <div class="digi-hospital-card__body">
                            <h3 class="digi-hospital-card__title">
                                {{ $hospital->nombre_unidad }}
                            </h3>

                            <div class="digi-hospital-card__address">
                                <i class="mdi mdi-map-marker"></i>
                                <span>{{ $hospital->direccion }}</span>
                            </div>

                            <div class="digi-hospital-card__actions">
                                <a href="tel:{{ $hospital->telefono }}" class="digi-btn digi-btn--outline digi-btn--sm">
                                    <i class="mdi mdi-phone"></i>
                                    Llamar
                                </a>

                                <a href="https://wa.me/52{{ $hospital->telefono }}" target="_blank"
                                    class="digi-btn digi-btn--success digi-btn--sm">
                                    <i class="mdi mdi-whatsapp"></i>
                                    WhatsApp
                                </a>

                                <a href="https://maps.google.com/?q={{ urlencode($hospital->direccion) }}" target="_blank"
                                    class="digi-btn digi-btn--secondary digi-btn--sm">
                                    <i class="mdi mdi-directions"></i>
                                    Ubicación
                                </a>
                            </div>

                            {{-- Teléfono visible para referencia --}}
                            <small
                                style="display: block; margin-top: 0.75rem; color: var(--text-muted); font-size: 0.8rem;">
                                <i class="mdi mdi-phone-outline"></i> {{ $hospital->telefono }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Mensaje si no hay hospitales --}}
        @if ($hospitales->isEmpty())
            <div class="digi-card" style="padding: 3rem; text-align: center;">
                <i class="mdi mdi-hospital-building"
                    style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <h3 style="font-family: 'Syne', sans-serif; color: var(--text); margin-bottom: 0.5rem;">No hay hospitales
                    disponibles</h3>
                <p style="color: var(--text-muted);">Por el momento no hay hospitales en la red. Intenta más tarde.</p>
            </div>
        @endif

    </div>

@endsection
