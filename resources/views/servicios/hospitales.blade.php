@extends('layouts.app')

@section('title', 'Hospitales')

@section('content')

    {{-- DATA --}}
    @php
        use Illuminate\Support\Str;

        $hospitales = [
            [
                'nombre' => 'Fifty Doctors Angelópolis',
                'direccion' => 'Anillo Perif. Ecológico 3505, Tlaxcalancingo, San Andrés Cholula, Pue.',
                'telefono' => '2226892995',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/angelopolis.jpeg',
            ],
            [
                'nombre' => 'Fifty Doctors San Manuel',
                'direccion' => 'Blvrd 14 Sur 4302, Jardines de San Manuel, Puebla',
                'telefono' => '2226895140',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/angelopolis.jpeg',
            ],
            [
                'nombre' => 'Fifty Doctors Homi La Paz',
                'direccion' => 'Av. Teziutlán Sur 36, Col. La Paz, Puebla',
                'telefono' => '2226895140',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/angelopolis.jpeg',
            ],
        ];

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

        $estudios = [
            'Estudios de Cardiología' => [
                ['nombre' => 'Ecocardiograma', 'precio' => 5200],
                ['nombre' => 'Ecocardiograma Fetal', 'precio' => 5200],
                ['nombre' => 'Ecocardiograma Transesofágico', 'precio' => 6630],
                ['nombre' => 'Ecocardiograma de Estrés', 'precio' => 8450],
                ['nombre' => 'Monitoreo Holter 24 horas', 'precio' => 3250],
                ['nombre' => 'MAPA (Monitoreo Ambulatorio de Presión Arterial)', 'precio' => 3250],
                ['nombre' => 'Electrocardiograma', 'precio' => 520],
                ['nombre' => 'Valoración cardiológica básica', 'precio' => 2600],
                ['nombre' => 'Valoración cardiológica completa', 'precio' => 3380],
                ['nombre' => 'Consulta cardiológica', 'precio' => 1300],
                ['nombre' => 'Prueba de esfuerzo', 'precio' => 3250],
                ['nombre' => 'Placa de tórax', 'precio' => 1716],
            ],

            'Tomografías' => [
                ['nombre' => 'TAC de cráneo simple', 'precio' => 2640],
                ['nombre' => 'TAC de cráneo contrastada', 'precio' => 4498],
                ['nombre' => 'TAC de hipófisis contrastada', 'precio' => 4498],
                ['nombre' => 'TAC facial simple', 'precio' => 3120],
                ['nombre' => 'TAC de senos paranasales', 'precio' => 3120],
                ['nombre' => 'TAC de órbitas', 'precio' => 3120],
                ['nombre' => 'TAC de oído', 'precio' => 3120],
                ['nombre' => 'TAC de cuello simple', 'precio' => 3380],
                ['nombre' => 'TAC de cuello contrastada', 'precio' => 4758],
                ['nombre' => 'TAC de laringe simple', 'precio' => 3380],
                ['nombre' => 'TAC de laringe contrastada', 'precio' => 4758],
                ['nombre' => 'TAC de tórax simple', 'precio' => 5720],
                ['nombre' => 'TAC de tórax contrastada', 'precio' => 7098],
                ['nombre' => 'TAC de abdomen simple', 'precio' => 5720],
                ['nombre' => 'TAC de abdomen contrastada', 'precio' => 7098],
                ['nombre' => 'TAC de pelvis simple', 'precio' => 5720],
                ['nombre' => 'TAC de pelvis contrastada', 'precio' => 7098],
            ],
        ];

        $serviciosMedicos = [
            'Ortopedia y Traumatología' => [
                ['procedimiento' => 'Prótesis total de cadera (artroplastia)', 'cuota' => 10000],
                ['procedimiento' => 'Prótesis total de rodilla (artroplastia)', 'cuota' => 10000],
                ['procedimiento' => 'Artroscopia de rodilla – meniscos', 'cuota' => 10000],
                ['procedimiento' => 'Reconstrucción de ligamento cruzado anterior', 'cuota' => 10000],
                ['procedimiento' => 'Cirugía de síndrome del túnel carpiano', 'cuota' => 10000],
            ],

            'Urología' => [
                ['procedimiento' => 'Resección transuretral de próstata (RTUP)', 'cuota' => 10000],
                ['procedimiento' => 'Sistema Urolift', 'cuota' => 10000],
                ['procedimiento' => 'Circuncisión / Fimosis', 'cuota' => 10000],
                ['procedimiento' => 'Cirugía de próstata con vapor de agua (Rezum)', 'cuota' => 10000],
            ],

            'Cirugía General' => [
                ['procedimiento' => 'Apendicectomía', 'cuota' => 10000],
                ['procedimiento' => 'Colecistectomía laparoscópica', 'cuota' => 10000],
                ['procedimiento' => 'Hernioplastía inguinal', 'cuota' => 10000],
                ['procedimiento' => 'Cirugía de tiroides (tiroidectomía)', 'cuota' => 10000],
            ],

            'Ginecología y Obstetricia' => [
                ['procedimiento' => 'Cesárea', 'cuota' => 10000],
                ['procedimiento' => 'Histerectomía laparoscópica', 'cuota' => 10000],
                ['procedimiento' => 'Legrado uterino', 'cuota' => 10000],
                ['procedimiento' => 'Cirugía de prolapso uterino', 'cuota' => 10000],
            ],

            'Cirugía Plástica' => [
                ['procedimiento' => 'Abdominoplastia', 'cuota' => 10000],
                ['procedimiento' => 'Aumento mamario con implantes', 'cuota' => 10000],
                ['procedimiento' => 'Liposucción asistida (VASER / Lipo HD)', 'cuota' => 10000],
                ['procedimiento' => 'Rinoplastia (estética y/o funcional)', 'cuota' => 10000],
            ],
        ];

        $hospitalesMock = [
            [
                'nombre' => 'Fifty Doctors Angelópolis',
                'direccion' => 'Anillo Perif. Ecológico 3505, Tlaxcalancingo, San Andrés Cholula, Pue.',
                'telefono' => '2226892995',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/angelopolis.jpg',
            ],
            [
                'nombre' => 'Fifty Doctors San Manuel',
                'direccion' => 'Blvrd 14 Sur 4302, Jardines de San Manuel, Puebla',
                'telefono' => '2226895140',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/san-manuel.jpg',
            ],
            [
                'nombre' => 'Fifty Doctors Homi La Paz',
                'direccion' => 'Av. Teziutlán Sur 36, Col. La Paz, Puebla',
                'telefono' => '2226895140',
                'horario' => 'Abierto las 24 hrs',
                'imagen' => 'images/hospitals/la-paz.jpg',
            ],
        ];

        $medicamentos = [
            ['nombre' => '11-Desoxicorticosterona', 'precio' => 7042.04],
            ['nombre' => '17-Cetosteroides 17-KS en orina', 'precio' => 1133.63],
            ['nombre' => '17-Hidroxi corticoesteroides 17-OHCS', 'precio' => 1133.63],
            ['nombre' => '17-Alfa-Hidroxi-Progesterona (17-OHP)', 'precio' => 533.03],
            ['nombre' => 'Acetaminofén (Tylenol)', 'precio' => 1111.11],
            ['nombre' => 'Acetona en orina', 'precio' => 234.23],
            ['nombre' => 'Ácido fólico', 'precio' => 555.56],
            ['nombre' => 'Ácido úrico en suero', 'precio' => 87.09],
            ['nombre' => 'Alcohol (Etanol)', 'precio' => 366.37],
            ['nombre' => 'Aldosterona en sangre', 'precio' => 1681.68],
            ['nombre' => 'Amilasa en suero', 'precio' => 138.14],
            ['nombre' => 'Anfetamina / Metanfetamina', 'precio' => 240.24],
            ['nombre' => 'Anticuerpos anti Citomegalovirus IGG', 'precio' => 247.75],
            ['nombre' => 'Anticuerpos anti Citomegalovirus IGM', 'precio' => 247.75],
            ['nombre' => 'Anticuerpos anti Cardiolipinas IGG e IGM', 'precio' => 1022.52],
        ];
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
            <span class="digi-badge digi-badge--info">{{ count($hospitales) }} centros</span>
        </div>

        {{-- Grid de hospitales --}}
        <div class="row">
            @foreach ($hospitales as $hospital)
                <div class="col-md-4 mb-4">
                    <div class="digi-hospital-card">
                        {{-- Imagen --}}
                        <img src="{{ asset($hospital['imagen']) }}" class="digi-hospital-card__image"
                            alt="{{ $hospital['nombre'] }}" onerror="this.src='https://placehold.co/600x400?text=Hospital'">

                        <div class="digi-hospital-card__body">
                            <h3 class="digi-hospital-card__title">
                                {{ $hospital['nombre'] }}
                            </h3>

                            <div class="digi-hospital-card__address">
                                <i class="mdi mdi-map-marker"></i>
                                <span>{{ $hospital['direccion'] }}</span>
                            </div>

                            <div class="digi-hospital-card__actions">
                                <a href="tel:{{ $hospital['telefono'] }}" class="digi-btn digi-btn--outline digi-btn--sm">
                                    <i class="mdi mdi-phone"></i>
                                    Llamar
                                </a>

                                <a href="https://wa.me/52{{ $hospital['telefono'] }}" target="_blank"
                                    class="digi-btn digi-btn--success digi-btn--sm">
                                    <i class="mdi mdi-whatsapp"></i>
                                    WhatsApp
                                </a>

                                <a href="https://maps.google.com/?q={{ urlencode($hospital['direccion']) }}" target="_blank"
                                    class="digi-btn digi-btn--secondary digi-btn--sm">
                                    <i class="mdi mdi-directions"></i>
                                    Ubicación
                                </a>
                            </div>

                            {{-- Teléfono visible para referencia --}}
                            <small
                                style="display: block; margin-top: 0.75rem; color: var(--text-muted); font-size: 0.8rem;">
                                <i class="mdi mdi-phone-outline"></i> {{ $hospital['telefono'] }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Mensaje si no hay hospitales --}}
        @if (empty($hospitales) || count($hospitales) === 0)
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
