@extends('layouts.app')

@section('title', 'Atención Médica')

@section('content')
    @php
        use Illuminate\Support\Str;

        $doctores = [
            [
                'nombre' => 'Adrián Castillo Moreno',
                'especialidad' => 'Medicina Interna',
                'telefono' => '5532894412',
                'telefono_format' => '55 3289 4412',
                'direccion' => 'Av. Universidad 850, Col. Narvarte, CDMX',
            ],
            [
                'nombre' => 'Alejandro Martínez López',
                'especialidad' => 'Traumatología y Ortopedia',
                'telefono' => '5541238890',
                'telefono_format' => '55 4123 8890',
                'direccion' => 'Av. Insurgentes Sur 1450, Col. Del Valle, CDMX',
            ],
            [
                'nombre' => 'Andrea Robles Fuentes',
                'especialidad' => 'Dermatología',
                'telefono' => '5590037721',
                'telefono_format' => '55 9003 7721',
                'direccion' => 'Río Rhin 40, Col. Cuauhtémoc, CDMX',
            ],
            [
                'nombre' => 'Arturo Peña Salgado',
                'especialidad' => 'Cirugía General',
                'telefono' => '3320115598',
                'telefono_format' => '33 2011 5598',
                'direccion' => 'Av. Vallarta 3120, Col. Arcos Vallarta, Guadalajara',
            ],
            [
                'nombre' => 'Beatriz Núñez Carrillo',
                'especialidad' => 'Geriatría',
                'telefono' => '8125589032',
                'telefono_format' => '81 2558 9032',
                'direccion' => 'Av. Revolución 2150, Monterrey, NL',
            ],
            [
                'nombre' => 'Carlos Hernández Pineda',
                'especialidad' => 'Cirugía General',
                'telefono' => '5578123345',
                'telefono_format' => '55 7812 3345',
                'direccion' => 'Calz. de Tlalpan 3100, Col. Ex Hacienda Coapa, CDMX',
            ],
            [
                'nombre' => 'Cecilia Torres Aguilar',
                'especialidad' => 'Endocrinología',
                'telefono' => '5560129981',
                'telefono_format' => '55 6012 9981',
                'direccion' => 'Av. Patriotismo 210, Col. Escandón, CDMX',
            ],
            [
                'nombre' => 'Daniel Ortega Ríos',
                'especialidad' => 'Medicina de Urgencias',
                'telefono' => '8130097712',
                'telefono_format' => '81 3009 7712',
                'direccion' => 'Av. Aztlán 410, Monterrey, NL',
            ],
            [
                'nombre' => 'Eduardo Cortés Silva',
                'especialidad' => 'Oftalmología',
                'telefono' => '8125549001',
                'telefono_format' => '81 2554 9001',
                'direccion' => 'Av. Constitución 1900, Monterrey, NL',
            ],
            [
                'nombre' => 'Fernanda Ruiz Gómez',
                'especialidad' => 'Ginecología y Obstetricia',
                'telefono' => '5530984421',
                'telefono_format' => '55 3098 4421',
                'direccion' => 'Calle Dakota 95, Col. Nápoles, CDMX',
            ],
            [
                'nombre' => 'Gabriela Méndez Rangel',
                'especialidad' => 'Pediatría',
                'telefono' => '3314428891',
                'telefono_format' => '33 1442 8891',
                'direccion' => 'Av. Américas 1254, Col. Country Club, Guadalajara',
            ],
            [
                'nombre' => 'Héctor Ramírez Luna',
                'especialidad' => 'Cardiología',
                'telefono' => '5566772134',
                'telefono_format' => '55 6677 2134',
                'direccion' => 'Av. Universidad 1200, Col. Xoco, CDMX',
            ],
            [
                'nombre' => 'Iván Morales Ruiz',
                'especialidad' => 'Anestesiología',
                'telefono' => '3311897745',
                'telefono_format' => '33 1189 7745',
                'direccion' => 'Av. Pablo Neruda 2400, Col. Providencia, Guadalajara',
            ],
            [
                'nombre' => 'Jorge Salinas Cruz',
                'especialidad' => 'Neumología',
                'telefono' => '5544217800',
                'telefono_format' => '55 4421 7800',
                'direccion' => 'Calz. de los Misterios 120, Gustavo A. Madero, CDMX',
            ],
            [
                'nombre' => 'Karla Aguilar Moreno',
                'especialidad' => 'Cirugía Plástica',
                'telefono' => '5541126654',
                'telefono_format' => '55 4112 6654',
                'direccion' => 'Av. Masaryk 111, Polanco, CDMX',
            ],
            [
                'nombre' => 'Luis Alberto Sánchez Torres',
                'especialidad' => 'Urología',
                'telefono' => '3325679912',
                'telefono_format' => '33 2567 9912',
                'direccion' => 'Av. México 3370, Col. Monraz, Guadalajara',
            ],
            [
                'nombre' => 'Mariana López Estrada',
                'especialidad' => 'Pediatría',
                'telefono' => '8114457789',
                'telefono_format' => '81 1445 7789',
                'direccion' => 'Av. Vasconcelos 420, San Pedro Garza García, NL',
            ],
            [
                'nombre' => 'Óscar Vázquez Beltrán',
                'especialidad' => 'Anestesiología',
                'telefono' => '5590083344',
                'telefono_format' => '55 9008 3344',
                'direccion' => 'Av. Observatorio 350, Miguel Hidalgo, CDMX',
            ],
            [
                'nombre' => 'Paola Jiménez Calderón',
                'especialidad' => 'Dermatología',
                'telefono' => '5590015588',
                'telefono_format' => '55 9001 5588',
                'direccion' => 'Río Lerma 160, Col. Cuauhtémoc, CDMX',
            ],
            [
                'nombre' => 'Ricardo Mendoza Salas',
                'especialidad' => 'Neurocirugía',
                'telefono' => '8132218844',
                'telefono_format' => '81 3221 8844',
                'direccion' => 'Av. Gonzalitos 500, Mitras Centro, Monterrey',
            ],
            [
                'nombre' => 'Sofía Ramírez Castillo',
                'especialidad' => 'Medicina Interna',
                'telefono' => '5573349921',
                'telefono_format' => '55 7334 9921',
                'direccion' => 'Eje Central Lázaro Cárdenas 980, CDMX',
            ],
            [
                'nombre' => 'Tomás Herrera Velasco',
                'especialidad' => 'Reumatología',
                'telefono' => '3341206678',
                'telefono_format' => '33 4120 6678',
                'direccion' => 'Av. Niño Obrero 800, Zapopan, Jal.',
            ],
            [
                'nombre' => 'Valeria Ponce Aguilar',
                'especialidad' => 'Nutriología Clínica',
                'telefono' => '5555609987',
                'telefono_format' => '55 5560 9987',
                'direccion' => 'Av. División del Norte 2400, Coyoacán, CDMX',
            ],
        ];

        $especialidades = [
            'Alergología',
            'Algología (Clínica del Dolor)',
            'Anestesiología',
            'Angiología y Cirugía Vascular',
            'Audiología, Otoneurología y Foniatría',
            'Cardiología',
            'Cardiología Pediátrica',
            'Cirugía Cardiotorácica',
            'Cirugía de Trasplantes',
            'Cirugía General',
            'Cirugía Maxilofacial',
            'Cirugía Oncológica',
            'Cirugía Pediátrica',
            'Cirugía Plástica, Estética y Reconstructiva',
            'Coloproctología',
            'Dermatología',
            'Endocrinología',
            'Gastroenterología',
            'Gastroenterología Pediátrica',
            'Geriatría',
            'Ginecología y Obstetricia',
            'Hematología',
            'Infectología',
            'Medicina del Deporte',
            'Medicina Familiar',
            'Medicina Física y Rehabilitación',
            'Medicina Interna',
            'Medicina Paliativa',
            'Nefrología',
            'Nefrología Pediátrica',
            'Neonatología',
            'Neumología',
            'Neurocirugía',
            'Neurología',
            'Neurología Pediátrica',
            'Nutriología Clínica',
            'Oftalmología',
            'Oncología Médica',
            'Oncología Pediátrica',
            'Ortopedia y Traumatología',
            'Otorrinolaringología',
            'Paidopsiquiatría',
            'Pediatría',
            'Psicología Clínica',
            'Psiquiatría',
            'Psiquiatría Infantil y del Adolescente',
            'Reumatología',
            'Salud Pública',
            'Urología',
        ];

        $doctoresPorEspecialidad = collect($doctores)->groupBy('especialidad');
    @endphp
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
            --danger-light: #fee2e2;
            --transition: 0.2s ease;
            --radius-md: 8px;
            --radius-lg: 16px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .digi-container-at {
            display: block;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
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

        /* Emergency Banner */
        .digi-emergency-banner {
            background: linear-gradient(135deg, var(--danger-light), var(--white));
            border: 2px solid var(--danger);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .digi-emergency-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 20px,
                    rgba(220, 38, 38, 0.05) 20px,
                    rgba(220, 38, 38, 0.05) 40px);
            pointer-events: none;
        }

        .digi-emergency-banner__content {
            position: relative;
            z-index: 1;
        }

        .digi-emergency-banner__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--danger);
            margin: 0 0 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-emergency-banner__title i {
            font-size: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .digi-emergency-banner__subtitle {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            margin: 0;
        }

        .digi-emergency-banner__actions {
            display: flex;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
            flex-wrap: wrap;
        }

        /* Search Bar */
        .digi-search-wrapper {
            position: relative;
            margin-bottom: 2rem;
        }

        .digi-search-input {
            width: 100%;
            padding: 1rem 1.25rem 1rem 3rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            transition: all var(--transition);
            background: var(--white);
        }

        .digi-search-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .digi-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.3rem;
            pointer-events: none;
        }

        .digi-search-clear {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            display: none;
            padding: 0.25rem;
            border-radius: 50%;
            transition: all var(--transition);
        }

        .digi-search-clear:hover {
            background: var(--navy);
            color: var(--blue);
        }

        .digi-search-clear.visible {
            display: block;
        }

        /* Servicios Grid */
        .digi-servicios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .digi-servicio-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            transition: all var(--transition);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .digi-servicio-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--blue), var(--cream));
            transform: scaleX(0);
            transition: transform var(--transition);
        }

        .digi-servicio-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--blue);
        }

        .digi-servicio-card:hover::before {
            transform: scaleX(1);
        }

        .digi-servicio-card__icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--navy), var(--border));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--blue);
            font-size: 2.2rem;
            transition: all var(--transition);
        }

        .digi-servicio-card:hover .digi-servicio-card__icon {
            transform: scale(1.1);
            color: var(--cream);
        }

        .digi-servicio-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--cream);
            margin: 0 0 0.5rem 0;
        }

        .digi-servicio-card__count {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
        }

        .digi-servicio-card__count i {
            color: var(--blue);
            font-size: 1rem;
        }

        /* Modal */
        .digi-modal .modal-content {
            border-radius: var(--radius-lg);
            border: none;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .digi-modal .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, var(--white), var(--navy));
        }

        .digi-modal .modal-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--cream);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .digi-modal .modal-title i {
            color: var(--blue);
            font-size: 1.5rem;
        }

        .digi-modal .btn-close {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all var(--transition);
        }

        .digi-modal .btn-close:hover {
            color: var(--blue);
            transform: rotate(90deg);
        }

        .digi-modal .modal-body {
            padding: 1.5rem;
            max-height: 500px;
            overflow-y: auto;
        }

        /* Procedimiento Card */
        .digi-procedimiento-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 1rem;
            transition: all var(--transition);
        }

        .digi-procedimiento-card:last-child {
            margin-bottom: 0;
        }

        .digi-procedimiento-card:hover {
            border-color: var(--blue);
            box-shadow: var(--shadow-sm);
        }

        .digi-procedimiento-card__body {
            padding: 1.5rem;
        }

        .digi-procedimiento-card__header {
            margin-bottom: 1rem;
        }

        .digi-procedimiento-card__title {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--cream);
            margin: 0 0 0.5rem 0;
        }

        .digi-procedimiento-card__prices {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.4rem;
        }

        .digi-procedimiento-card__price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--blue);
            margin: 0;
        }

        .digi-procedimiento-card__price--original {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: line-through;
            margin: 0;
        }

        .digi-price-label {
            font-size: 0.72rem;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .digi-price-label--original {
            color: var(--text-muted);
        }

        .digi-price-label--pats {
            color: var(--blue);
        }

        .digi-price-block {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        /* Hospitales List */
        .digi-hospitales-list {
            margin: 1rem 0;
        }

        .digi-hospital-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--navy);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            transition: all var(--transition);
        }

        .digi-hospital-item:hover {
            background: var(--border);
        }

        .digi-hospital-item:last-child {
            margin-bottom: 0;
        }

        .digi-hospital-item__name {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .digi-hospital-item__name i {
            color: var(--blue);
            font-size: 1rem;
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

        .digi-btn--danger {
            background: var(--danger);
            color: white;
        }

        .digi-btn--danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
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

        .digi-btn--primary {
            background: var(--blue);
            color: white;
        }

        .digi-btn--primary:hover {
            background: var(--cream);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
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

        .digi-badge--danger {
            background: var(--danger-light) !important;
            color: var(--danger) !important;
        }

        /* Empty State */
        .digi-empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            grid-column: 1/-1;
        }

        .digi-empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .digi-empty-state h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .digi-empty-state p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* Utilities */
        .d-none {
            display: none;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
    </style>

    <div class="digi-container-at py-4">
        {{-- Breadcrumb --}}
        <div class="digi-breadcrumb">
            <a href="{{ url('/servicios') }}" class="digi-breadcrumb__link">
                <i class="mdi mdi-arrow-left"></i>
                Servicios
            </a>
            <span class="digi-breadcrumb__separator">/</span>
            <span class="digi-breadcrumb__current">Atención Médica</span>
        </div>

        {{-- Header --}}
        <div class="digi-page-header">
            <div>
                <h1 class="digi-page-title">
                    <i class="mdi mdi-hospital-box digi-page-title__icon"></i>
                    Atención Médica
                </h1>
                <p class="digi-page-subtitle">
                    Procedimientos disponibles dentro de la red hospitalaria
                </p>
            </div>
            <span class="digi-badge digi-badge--info">
                {{ $serviciosMedicos->flatten()->count() }} procedimientos
            </span>
        </div>

        {{-- Emergency Banner --}}
        <div class="digi-emergency-banner">
            <div class="digi-emergency-banner__content">
                <h2 class="digi-emergency-banner__title">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    Urgencias médicas
                </h2>
                <p class="digi-emergency-banner__subtitle">
                    Atención inmediata 24/7 · No requieres cita
                </p>
            </div>
            <div class="digi-emergency-banner__actions">
                @foreach ($hospitales as $item)
                    <a href="tel:{{ $item['telefono'] }}" class="digi-btn digi-btn--danger">
                        <i class="mdi mdi-phone"></i>
                        {{ $item['nombre_unidad'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="digi-search-wrapper">
            <i class="mdi mdi-magnify digi-search-icon"></i>
            <input type="text" class="digi-search-input" placeholder="Buscar servicio o procedimiento..."
                id="searchServicios" autocomplete="off">
            <button class="digi-search-clear" id="clearSearch">
                <i class="mdi mdi-close"></i>
            </button>
        </div>

        {{-- Servicios Grid --}}
        <div class="digi-servicios-grid" id="serviciosGrid">
            @forelse ($serviciosMedicos as $especialidad => $procedimientos)
                @php
                    $slug = Str::slug($especialidad);
                    $icons = ['heart-pulse', 'brain', 'bone', 'eye', 'tooth', 'stethoscope', 'microscope', 'needle'];
                    $icon = $icons[$loop->index % count($icons)];
                @endphp

                <div class="digi-servicio-card servicio-card" data-nombre="{{ strtolower($especialidad) }}"
                    data-bs-toggle="modal" data-bs-target="#modal-srv-{{ $slug }}">

                    <div class="digi-servicio-card__icon">
                        {{-- <i class="mdi mdi-{{ $icon }}"></i> --}}
                    </div>

                    <h3 class="digi-servicio-card__title">{{ $especialidad }}</h3>

                    <div class="digi-servicio-card__count">
                        <i class="mdi mdi-file-document"></i>
                        <span>{{ $procedimientos->count() }} procedimientos</span>
                    </div>
                </div>
            @empty
                <div class="digi-empty-state">
                    <i class="mdi mdi-hospital-box"></i>
                    <h3>No hay servicios disponibles</h3>
                    <p>Por el momento no hay procedimientos médicos registrados.</p>
                    <a href="{{ url('/servicios') }}" class="digi-btn digi-btn--primary">
                        <i class="mdi mdi-arrow-left"></i>
                        Volver a servicios
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modales --}}
    @foreach ($serviciosMedicos as $especialidad => $procedimientos)
        @php $slug = Str::slug($especialidad); @endphp

        <div class="modal fade digi-modal" id="modal-srv-{{ $slug }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="mdi mdi-stethoscope"></i>
                            {{ $especialidad }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ($procedimientos as $proc)
                            <div class="digi-procedimiento-card">
                                <div class="digi-procedimiento-card__body">
                                    <div class="digi-procedimiento-card__header">
                                        <h4 class="digi-procedimiento-card__title">
                                            {{ $proc->procedimiento }}
                                        </h4>
                                        @if ($proc->precio_nopats || $proc->precio_pats)
                                            <div class="digi-procedimiento-card__prices">
                                                @if ($proc->precio_nopats)
                                                    <div class="digi-price-block">
                                                        <span class="digi-price-label digi-price-label--original">Sin descuento</span>
                                                        <p class="digi-procedimiento-card__price--original">
                                                            ${{ number_format($proc->precio_nopats, 0, '.', ',') }} MXN
                                                        </p>
                                                    </div>
                                                @endif
                                                @if ($proc->precio_pats)
                                                    <div class="digi-price-block">
                                                        <span class="digi-price-label digi-price-label--pats">Precio PATS</span>
                                                        <p class="digi-procedimiento-card__price">
                                                            ${{ number_format($proc->precio_pats, 0, '.', ',') }} MXN
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Proveedor asociado al procedimiento --}}
                                    @if ($proc->proveedor)
                                        <div class="digi-hospitales-list">
                                            <div class="digi-hospital-item">
                                                <span class="digi-hospital-item__name">
                                                    <i class="mdi mdi-hospital-building"></i>
                                                    {{ $proc->proveedor->nombre_unidad }}
                                                </span>
                                                @if ($proc->proveedor->telefono)
                                                    <a href="tel:{{ $proc->proveedor->telefono }}"
                                                        class="digi-btn digi-btn--outline digi-btn--sm">
                                                        <i class="mdi mdi-phone"></i>
                                                        Llamar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("searchServicios");
            const clearBtn = document.getElementById("clearSearch");
            const cards = document.querySelectorAll(".servicio-card");
            const grid = document.getElementById("serviciosGrid");

            if (!input) return;

            // Función para filtrar cards
            function filterCards(searchTerm) {
                let hasResults = false;

                cards.forEach(card => {
                    const nombre = card.dataset.nombre;
                    if (nombre.includes(searchTerm)) {
                        card.style.display = "";
                        hasResults = true;
                    } else {
                        card.style.display = "none";
                    }
                });

                // Mostrar mensaje si no hay resultados
                let emptyMessage = document.querySelector(".digi-empty-state-search");
                if (!hasResults && searchTerm.length > 0) {
                    if (!emptyMessage) {
                        emptyMessage = document.createElement("div");
                        emptyMessage.className = "digi-empty-state digi-empty-state-search";
                        emptyMessage.innerHTML = `
                            <i class="mdi mdi-hospital-box"></i>
                            <h3>No se encontraron servicios</h3>
                            <p>No hay resultados para "${searchTerm}"</p>
                            <button class="digi-btn digi-btn--outline" onclick="document.getElementById('clearSearch').click()">
                                <i class="mdi mdi-close"></i>
                                Limpiar búsqueda
                            </button>
                        `;
                        grid.appendChild(emptyMessage);
                    }
                } else if (emptyMessage) {
                    emptyMessage.remove();
                }
            }

            // Evento input
            input.addEventListener("input", function() {
                const value = this.value.toLowerCase().trim();

                if (value.length > 0) {
                    clearBtn.classList.add("visible");
                } else {
                    clearBtn.classList.remove("visible");
                }

                filterCards(value);
            });

            // Botón limpiar
            clearBtn.addEventListener("click", function() {
                input.value = "";
                input.focus();
                clearBtn.classList.remove("visible");
                filterCards("");
            });

            // Tecla ESC para limpiar
            input.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    clearBtn.click();
                }
            });
        });
    </script>
@endsection
