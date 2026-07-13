<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contrato de Distribución — Pasaporte a tu Salud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --navy: #111111;
            --gold: #2b2b2b;
            --gold-light: #aeaeae;
            --cream: #ffffff;
            --ink: #000000;
            --muted: #7a7a7a;
            --rule: #dcdcdc;
            --accent-bg: #f5f5f5
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--cream);
            color: var(--ink);
            font-size: 15px;
            line-height: 1.8
        }

        .cover {
            background: var(--navy);
            color: #fff;
            padding: 60px 80px 50px;
            position: relative;
            overflow: hidden
        }

        .cover::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255, 255, 255, .02) 40px, rgba(255, 255, 255, .02) 41px)
        }

        .cover-inner {
            position: relative;
            z-index: 1;
            max-width: 860px;
            margin: 0 auto
        }

        .cover-label {
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold-light);
            margin-bottom: 18px
        }

        .cover h1 {
            font-family: "EB Garamond", serif;
            font-size: 38px;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 8px;
            color: #fff
        }

        .cover-subtitle {
            font-family: "Montserrat", sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--gold-light);
            text-transform: uppercase;
            margin-bottom: 40px
        }

        .cover-divider {
            width: 60px;
            height: 2px;
            background: var(--gold-light);
            margin-bottom: 32px
        }

        .cover-parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 8px
        }

        .cover-party h3 {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold-light);
            margin-bottom: 6px
        }

        .cover-party p {
            font-size: 14px;
            color: rgba(255, 255, 255, .75);
            line-height: 1.5
        }

        .page {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 40px 80px
        }

        .caratula-section {
            background: #fff;
            border: 1px solid var(--rule);
            border-top: 4px solid var(--gold);
            margin: 48px 0 0;
            border-radius: 2px;
            overflow: hidden
        }

        .caratula-header {
            background: var(--navy);
            color: #fff;
            padding: 20px 32px;
            display: flex;
            align-items: center;
            gap: 16px
        }

        .caratula-header h2 {
            font-family: "Montserrat", sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold-light)
        }

        .caratula-table {
            width: 100%;
            border-collapse: collapse
        }

        .caratula-table tr:nth-child(even) {
            background: var(--accent-bg)
        }

        .caratula-table td {
            padding: 11px 24px;
            border-bottom: 1px solid var(--rule);
            vertical-align: top;
            font-size: 14px
        }

        .caratula-table td:first-child {
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            width: 38%;
            padding-top: 13px
        }

        .badge {
            display: inline-block;
            background: var(--accent-bg);
            border: 1px solid var(--rule);
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 3px 10px;
            border-radius: 2px;
            color: var(--muted);
            font-style: italic
        }

        .annexes-nav {
            margin: 36px 0;
            background: var(--navy);
            border-radius: 2px;
            padding: 24px 28px
        }

        .annexes-nav h3 {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold-light);
            margin-bottom: 16px
        }

        .annexes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px
        }

        .annexes-grid a {
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, .6);
            text-decoration: none;
            padding: 8px 10px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 2px;
            transition: all .2s;
            display: flex;
            gap: 8px;
            align-items: flex-start
        }

        .annexes-grid a:hover {
            border-color: var(--gold-light);
            color: #fff;
            background: rgba(255, 255, 255, .06)
        }

        .annexes-grid a span.num {
            color: var(--gold-light);
            flex-shrink: 0
        }

        .section-head {
            margin: 60px 0 28px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--rule);
            position: relative
        }

        .section-head::after {
            content: "";
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 48px;
            height: 2px;
            background: var(--gold)
        }

        .section-head .label {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px
        }

        .section-head h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--navy)
        }

        .clause {
            margin-bottom: 36px;
            scroll-margin-top: 20px
        }

        .clause-title {
            font-family: "Montserrat", sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--navy);
            padding: 10px 0;
            border-top: 1px solid var(--rule);
            display: flex;
            align-items: center;
            gap: 12px
        }

        .clause-num {
            background: var(--navy);
            color: var(--gold-light);
            font-size: 9px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 2px;
            letter-spacing: 1px
        }

        .clause-body {
            padding: 14px 0 0;
            font-size: 14.5px;
            line-height: 1.85;
            color: var(--ink)
        }

        .clause-body p {
            margin-bottom: 12px
        }

        .clause-body ul,
        .clause-body ol {
            padding-left: 24px;
            margin: 12px 0
        }

        .clause-body li {
            margin-bottom: 8px
        }

        .obl-item {
            display: flex;
            gap: 14px;
            padding: 10px 14px;
            border-radius: 2px;
            margin-bottom: 6px
        }

        .obl-item:nth-child(odd) {
            background: var(--accent-bg)
        }

        .obl-item .obl-key {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--gold-light);
            min-width: 22px;
            padding-top: 3px;
            text-transform: uppercase
        }

        .obl-item .obl-val {
            font-size: 14px;
            line-height: 1.7
        }

        .callout {
            padding: 18px 22px;
            border-radius: 2px;
            margin: 16px 0;
            font-size: 14px;
            line-height: 1.7
        }

        .callout.warning {
            background: #f0f0f0;
            border-left: 4px solid #555;
            color: #2b2b2b
        }

        .callout.info {
            background: #f5f5f5;
            border-left: 4px solid #111;
            color: #111
        }

        .callout.danger {
            background: #e8e8e8;
            border-left: 4px solid #111;
            color: #111
        }

        .callout strong {
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 6px
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            font-size: 14px
        }

        .price-table thead tr {
            background: var(--navy);
            color: var(--gold-light)
        }

        .price-table thead th {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 12px 18px;
            text-align: left
        }

        .price-table tbody tr:nth-child(even) {
            background: var(--accent-bg)
        }

        .price-table tbody td {
            padding: 12px 18px;
            border-bottom: 1px solid var(--rule)
        }

        .price-table .amount {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: var(--navy)
        }

        .annex-section {
            margin: 60px 0 0;
            padding-top: 48px;
            border-top: 2px solid var(--rule)
        }

        .annex-tag {
            display: inline-block;
            background: var(--gold);
            color: #fff;
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 14px;
            margin-bottom: 12px;
            border-radius: 2px
        }

        .annex-section h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 24px
        }

        .manual-item {
            margin-bottom: 28px
        }

        .manual-item h3 {
            font-family: "Montserrat", sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold-light);
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed var(--rule)
        }

        .manual-item p,
        .manual-item li {
            font-size: 14px;
            line-height: 1.75
        }

        .manual-item ul {
            padding-left: 20px;
            margin-top: 8px
        }

        .manual-item li {
            margin-bottom: 5px
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 16px 0
        }

        .benefit-card {
            background: #fff;
            border: 1px solid var(--rule);
            border-top: 3px solid var(--gold);
            padding: 16px 18px;
            border-radius: 2px
        }

        .benefit-card .b-label {
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px
        }

        .benefit-card .b-val {
            font-size: 20px;
            font-weight: 600;
            color: var(--navy)
        }

        .benefit-card .b-desc {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px
        }

        /* ── Bloques de firma ── */
        .sig-block {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 40px 0 0;
            padding-top: 40px;
            border-top: 1px solid var(--rule)
        }

        .sig-party {
            text-align: center
        }

        .sig-line {
            border-bottom: 1px solid var(--ink);
            margin-bottom: 10px;
            height: 60px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            padding-bottom: 4px
        }

        .sig-line img {
            max-height: 54px;
            max-width: 220px;
            object-fit: contain
        }

        .sig-name {
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--navy)
        }

        .sig-role {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px
        }

        .sig-date-block {
            text-align: center;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--ink)
        }

        .sig-date-block strong {
            font-weight: 700
        }

        /* ── Checkbox beneficiario ── */
        .beneficiary-check {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 16px;
            padding: 14px 18px;
            background: #fff;
            border: 1.5px solid var(--rule);
            border-radius: 4px
        }

        .beneficiary-check input[type=checkbox] {
            width: 20px;
            height: 20px;
            accent-color: var(--navy);
            flex-shrink: 0;
            cursor: pointer
        }

        .beneficiary-check label {
            font-size: 14px;
            color: var(--ink);
            line-height: 1.6;
            cursor: pointer
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin: 20px 0
        }

        .form-field {
            border-bottom: 1px solid var(--ink);
            padding-bottom: 4px
        }

        .form-field label {
            display: block;
            font-family: "Montserrat", sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px
        }

        .form-field.full {
            grid-column: span 2
        }

        .doc-footer {
            background: var(--navy);
            color: rgba(255, 255, 255, .4);
            font-family: "Montserrat", sans-serif;
            font-size: 10px;
            letter-spacing: 1px;
            text-align: center;
            padding: 24px 40px;
            margin-top: 80px
        }

        @media print {
            body {
                font-size: 11pt
            }

            .cover {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            .section-head,
            .annex-section {
                page-break-before: auto
            }

            .no-print {
                display: none
            }
        }
    </style>
</head>

<body>
    <div class="cover">
        <div class="cover-inner">
            <div class="cover-label">Documento Contractual</div>
            <h1>Contrato de Distribución</h1>
            <div class="cover-subtitle">Pasaporte a tu Salud, S.A. de C.V.</div>
            <div class="cover-divider"></div>
            <div class="cover-parties">
                <div class="cover-party">
                    <h3>El Proveedor</h3>
                    <p>"Pasaporte a tu Salud",<br />Sociedad Anónima de Capital Variable<br />R.F.C. PTS260126KX3</p>
                </div>
                <div class="cover-party">
                    <h3>El Distribuidor</h3>
                    <p>Persona física señalada<br />en el Apartado 5 de la Carátula</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="caratula-section" id="caratula">
            <div class="caratula-header">
                <h2>Carátula del Contrato</h2>
            </div>
            <table class="caratula-table">
                <tr>
                    <td>Apartado 1 — Nombre del Proveedor</td>
                    <td>"Pasaporte a tu Salud", Sociedad Anónima de Capital Variable</td>
                </tr>
                <tr>
                    <td>Apartado 2 — Representante Legal</td>
                    <td>Emilio Flores Cervantes</td>
                </tr>
                <tr>
                    <td>Apartado 3 — Domicilio del Proveedor</td>
                    <td>Periférico Ecológico 3505, Col. Reserva Territorial Atlixayotl, Piso 4, Int. 412, San Andrés
                        Cholula, Puebla, C.P. 72,820</td>
                </tr>
                <tr>
                    <td>Apartado 4 — R.F.C. del Proveedor</td>
                    <td>PTS260126KX3</td>
                </tr>
                <tr>
                    <td>Apartado 5 — Nombre del Distribuidor</td>
                    <td><span class="badge" id="c_nombre">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 6 — Domicilio del Distribuidor</td>
                    <td><span class="badge" id="c_domicilio">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 7 — R.F.C. del Distribuidor</td>
                    <td><span class="badge" id="c_rfc">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 8 — Nombre del Franquiciatario</td>
                    <td><span class="badge" id="c_franquicia">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 9 — Demarcación Territorial</td>
                    <td><span class="badge" id="c_demarcacion">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 10 — Precio del Derecho de Distribución</td>
                    <td><strong>$20,000.00 M.N.</strong> (Veinte mil pesos, IVA incluido)</td>
                </tr>
                <tr>
                    <td>Apartado 11 — Cuenta Bancaria del Proveedor</td>
                    <td>CLABE: 042650016008666521<br />Banca Mifel, S.A., Institución de Banca Múltiple, Grupo
                        Financiero Mifel</td>
                </tr>
                <tr>
                    <td>Apartado 12 — Fecha de Inicio de Vigencia</td>
                    <td><span class="badge" id="c_fecha_inicio">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 13 — Fecha de Fin de Vigencia</td>
                    <td><span class="badge" id="c_fecha_fin">Por llenar</span></td>
                </tr>
                <tr>
                    <td>Apartado 14 — Anexos</td>
                    <td>Anexos 1 al 12 (ver índice de anexos)</td>
                </tr>
                <tr>
                    <td>Apartado 15 — Fecha de Firma</td>
                    <td><span class="badge" id="c_fecha_firma">Por llenar</span></td>
                </tr>
            </table>
        </div>

        <div class="annexes-nav no-print">
            <h3>Índice de Anexos</h3>
            <div class="annexes-grid">
                <a href="#anexo1"><span class="num">1</span> Contrato de Franquicia</a>
                <a href="#anexo2"><span class="num">2</span> Manual del Distribuidor</a>
                <a href="#anexo3"><span class="num">3</span> Identificación del Distribuidor</a>
                <a href="#anexo4"><span class="num">4</span> Comprobante de Domicilio</a>
                <a href="#anexo5"><span class="num">5</span> Constancia de Situación Fiscal</a>
                <a href="#anexo6"><span class="num">6</span> Manual de Afiliado</a>
                <a href="#anexo7"><span class="num">7</span> Manual de Operación PATS</a>
                <a href="#anexo8"><span class="num">8</span> Costo de la Tarjeta</a>
                <a href="#anexo9"><span class="num">9</span> Comprobante Transferencia</a>
                <a href="#anexo10"><span class="num">10</span> Comisiones del Distribuidor</a>
                <a href="#anexo11"><span class="num">11</span> Aviso de Privacidad</a>
                <a href="#anexo12"><span class="num">12</span> Declaración Dueño Beneficiario</a>
            </div>
        </div>

        <div class="section-head">
            <div class="label">Antecedente</div>
            <h2>Único — Contrato de Franquicia</h2>
        </div>
        <div class="clause-body">
            <p>Con anterioridad a la celebración del presente contrato, "Pasaporte a tu Salud", S.A. de C.V., en su
                carácter de Franquiciante, y la persona física señalada en el Apartado 8 de la Carátula, en su carácter
                de Franquiciatario, celebraron un Contrato de Franquicia por virtud del cual se otorgó una licencia
                temporal y no exclusiva para gestionar, ofrecer, promocionar y comercializar la Tarjeta de Descuento
                denominada comercialmente "Pasaporte a tu Salud".</p>
            <div class="callout info"><strong>Descripción del producto</strong>Programa de beneficios que otorga
                descuentos y permite recibir servicios médicos privados de alta calidad de forma accesible, clara y sin
                restricciones dentro de la Red de Hospitales 50 (Fifty) Doctors, con sede en el Estado de Puebla.</div>
            <p>Para dichos fines, el Franquiciatario conformará una Red de Distribuidores que ejecutarán, en campo y
                dentro de la demarcación territorial señalada, las labores de promoción y comercialización de la Tarjeta
                de Descuento. El Contrato de Franquicia se adjunta como <strong>Anexo 1</strong>.</p>
        </div>

        <div class="section-head">
            <div class="label">Declaraciones</div>
            <h2>De las Partes</h2>
        </div>

        <div class="clause">
            <div class="clause-title"><span class="clause-num">I</span> Declara El Proveedor</div>
            <div class="clause-body">
                <p>A. Sociedad mercantil constituida conforme a las leyes mexicanas, según consta en escritura pública
                    número 54,318, volumen 500, del 26 de enero de 2026, ante el Notario Público número Veintinueve del
                    Distrito Judicial de Puebla, inscrita en el Registro Público de Comercio bajo el folio mercantil
                    electrónico N-2026007106.</p>
                <p>B. El representante legal cuenta con todas las facultades necesarias para la celebración del presente
                    contrato, con nombramiento de Administrador Único.</p>
                <p>C–D. Domicilio fiscal y R.F.C. según Apartados 3 y 4 de la Carátula.</p>
                <p>F. Es su deseo otorgar a El Distribuidor el derecho temporal de distribuir y comercializar la Tarjeta
                    de Descuento en cualquier parte de la República Mexicana, siguiendo los lineamientos del Manual del
                    Distribuidor y bajo la tutela de El Franquiciatario.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title"><span class="clause-num">II</span> Declara El Distribuidor</div>
            <div class="clause-body">
                <p>A–C. Persona física de nacionalidad mexicana, en pleno ejercicio de sus derechos, con domicilio y
                    R.F.C. según Apartados 5, 6 y 7 de la Carátula. Los recursos provienen de fuentes lícitas.</p>
                <p>D–E. Ha leído el Contrato de Franquicia y conoce el esquema de comercialización; actuará bajo la
                    tutela de El Franquiciatario y seguirá el Manual del Distribuidor.</p>
                <p>F. Recibe en este acto el Manual de Afiliado (Anexo 6), que deberá ser entregado a cada adquirente de
                    la Tarjeta.</p>
                <p>G. Ha sido informado sobre los criterios financieros para determinar sus márgenes de utilidad, y
                    recibe el Manual de Operación del Sistema PATS (Anexo 7).</p>
            </div>
        </div>

        <div class="section-head">
            <div class="label">Cuerpo del Contrato</div>
            <h2>Cláusulas</h2>
        </div>

        <div class="clause" id="clausula1">
            <div class="clause-title"><span class="clause-num">Primera</span> Objeto</div>
            <div class="clause-body">
                <p>El Proveedor otorga a El Distribuidor el <strong>derecho oneroso, personal e intransferible</strong>
                    de distribuir y comercializar la Tarjeta de Descuento en favor de cualquier persona física o moral,
                    al precio y condiciones del Anexo 8.</p>
                <div class="callout warning"><strong>Importante</strong>El Distribuidor actuará en todo momento bajo la
                    instrucción y tutela de El Franquiciatario, y deberá apoyarse exclusivamente en los materiales
                    publicitarios entregados por El Proveedor. No podrá ceder, transmitir ni sublicenciar este derecho a
                    terceros, salvo lo dispuesto en la Cláusula Décima Séptima.</div>
                <p>El Proveedor podrá otorgar un derecho igual a cualquier persona física o moral que elija, sin
                    necesidad de consentimiento previo del Distribuidor.</p>
            </div>
        </div>

        <div class="clause" id="clausula2">
            <div class="clause-title"><span class="clause-num">Segunda</span> Precio</div>
            <div class="clause-body">
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Derecho de distribución y comercialización</td>
                            <td><span class="amount">$20,000.00 M.N.</span></td>
                            <td>IVA incluido · Pago único · No reembolsable</td>
                        </tr>
                    </tbody>
                </table>
                <div class="callout danger"><strong>Pago no reembolsable</strong>El pago es único y no reembolsable, aun
                    en caso de rescisión o terminación del contrato. Cualquier erogación realizada en favor de persona
                    diversa a El Proveedor no será reconocida.</div>
                <p>El pago se realiza mediante transferencia electrónica a la cuenta señalada en el Apartado 11 de la
                    Carátula (CLABE: 042650016008666521, Banca Mifel). El comprobante se adjunta como Anexo 9.</p>
            </div>
        </div>

        <div class="clause" id="clausula3">
            <div class="clause-title"><span class="clause-num">Tercera</span> Obligaciones de El Distribuidor</div>
            <div class="clause-body">
                <p>Durante la vigencia del contrato, El Distribuidor se obliga a:</p>
                <div class="obl-item">
                    <div class="obl-key">a</div>
                    <div class="obl-val">Cumplir el objeto del contrato con diligencia, respetando las políticas y
                        lineamientos administrativos del Proveedor.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">b</div>
                    <div class="obl-val">Realizar actividades tendientes a la comercialización de la Tarjeta de
                        Descuento conforme a las tarifas establecidas por El Proveedor.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">c</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de dar entrevistas o realizar publicidad sin
                        autorización previa y por escrito del Proveedor.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">d</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de transmitir información sobre condiciones
                        comerciales o el contenido del Contrato de Franquicia.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">e</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de realizar declaraciones públicas que dañen la
                        imagen del Proveedor, la Tarjeta o la Franquicia.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">f</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de celebrar contratos en nombre del Proveedor y/o
                        del Franquiciatario sin autorización escrita.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">g</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de transmitir el derecho de distribución de
                        cualquier forma sin autorización escrita.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">h</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de recibir recursos económicos en nombre del
                        Proveedor y/o del Franquiciatario.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">i</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de operar, promocionar o adquirir cualquier
                        negocio o modelo de negocio similar al del Proveedor.</div>
                </div>
                <div class="obl-item">
                    <div class="obl-key">j</div>
                    <div class="obl-val"><strong>Abstenerse</strong> de explotar marcas registradas del Proveedor sin
                        autorización expresa por escrito.</div>
                </div>
                <div class="callout warning" style="margin-top:16px"><strong>Cláusula de No Competencia — 5 años
                        posteriores a la terminación</strong>k) No operar, promocionar ni participar en cualquier
                    negocio similar o competitivo al objeto de la Tarjeta de Descuento.<br /><br />l) No contactar,
                    solicitar ni atender a clientes que hubieren adquirido la Tarjeta a través de sus gestiones durante
                    la vigencia del contrato.</div>
            </div>
        </div>

        <div class="clause" id="clausula7">
            <div class="clause-title"><span class="clause-num">Séptima</span> Comisión de El Distribuidor</div>
            <div class="clause-body">
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Precio de Venta</th>
                            <th>Comisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tarjeta de Descuento (venta directa por El Distribuidor)</td>
                            <td><span class="amount">$800.00 M.N.</span></td>
                            <td><span class="amount">$20.00 M.N.</span> por tarjeta pagada al Proveedor</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="clause" id="clausula8">
            <div class="clause-title"><span class="clause-num">Octava</span> Vigencia</div>
            <div class="clause-body">
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="b-label">Vigencia inicial</div>
                        <div class="b-val">3 años</div>
                        <div class="b-desc">A partir de la fecha señalada en el Apartado 12 de la Carátula</div>
                    </div>
                    <div class="benefit-card">
                        <div class="b-label">Prórroga — plazo de solicitud</div>
                        <div class="b-val">90 días</div>
                        <div class="b-desc">Antes del vencimiento, por escrito y con acuse de recibo. Queda a
                            discreción del Proveedor.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clause" id="clausula12">
            <div class="clause-title"><span class="clause-num">Décima Segunda</span> Pena Convencional</div>
            <div class="clause-body">
                <div class="callout danger"><strong>Pena por Incumplimiento</strong>En caso de incumplimiento de
                    cualquier obligación del contrato, El Proveedor tendrá derecho a exigir, además de la rescisión y
                    cualquier acción legal procedente, una pena convencional equivalente al <strong>40% del precio
                        pactado en la Cláusula Segunda</strong>, es decir, el equivalente al 40% de $20,000.00 M.N.
                </div>
            </div>
        </div>

        <div class="clause" id="clausula24">
            <div class="clause-title"><span class="clause-num">Vigésima Cuarta</span> Jurisdicción</div>
            <div class="clause-body">
                <div class="callout info"><strong>Fuero Competente</strong>Las Partes se someten expresamente a la
                    jurisdicción y leyes de los Tribunales del fuero común competentes en el <strong>Distrito Judicial
                        de Puebla, Estado de Puebla</strong>, renunciando a cualquier otro fuero que pudiera
                    corresponderles.</div>
            </div>
        </div>

        {{-- ══ BLOQUE DE FIRMA PRINCIPAL ══ --}}
        <div class="sig-date-block" style="margin-top:60px;">
            <p>H. Puebla de Zaragoza a <strong id="sig_dia">___</strong> del mes de <strong
                    id="sig_mes">_______________</strong> del año <strong id="sig_anio">_______</strong></p>
        </div>
        <div class="sig-block">
            <div class="sig-party">
                <div class="sig-line">
                    <img src="{{ asset('images/firmas/firma_emilio_flores.png') }}" alt="Firma Emilio Flores Cervantes">
                </div>
                <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                <div class="sig-role">Representada por Emilio Flores Cervantes<br />Administrador Único</div>
            </div>
            <div class="sig-party">
                <div class="sig-line">
                    <img id="sig_firma_img" src="" alt="Firma del distribuidor" style="display:none;">
                </div>
                <div class="sig-name" id="sig_nombre_dist">El Distribuidor</div>
                <div class="sig-role">Persona física señalada en el Apartado 5 de la Carátula</div>
            </div>
        </div>

        {{-- ANEXOS --}}
        <div class="annex-section" id="anexo1">
            <span class="annex-tag">Anexo 1</span>
            <h2>Contrato de Franquicia</h2>
            <div class="callout info"><strong>Nota</strong>Copia simple del Contrato de Franquicia descrito en el
                Antecedente Único del presente instrumento. Se adjunta al cuerpo del contrato como documento
                independiente.</div>
        </div>

        <div class="annex-section" id="anexo2">
            <span class="annex-tag">Anexo 2</span>
            <h2>Manual del Distribuidor</h2>
            <div class="manual-item">
                <h3>Monto de Inversión Inicial</h3>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Licencia de distribución</td>
                            <td><span class="amount">$20,000.00 M.N.</span></td>
                            <td>IVA incluido · Único · No reembolsable</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="manual-item">
                <h3>Comisiones</h3>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio de Venta</th>
                            <th>Comisión del Distribuidor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tarjeta de Descuento</td>
                            <td><span class="amount">$800.00 M.N.</span></td>
                            <td><span class="amount">$20.00 M.N.</span> por tarjeta pagada al Proveedor</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="annex-section" id="anexo8">
            <span class="annex-tag">Anexo 8</span>
            <h2>Costo de la Tarjeta de Descuento</h2>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio de Venta</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Licencia de Distribución</td>
                        <td><span class="amount">$20,000.00 M.N.</span> IVA incluido</td>
                    </tr>
                    <tr>
                        <td>Pasaporte a tu Salud ("La Tarjeta de Descuento")</td>
                        <td><span class="amount">$800.00 M.N.</span> IVA incluido</td>
                    </tr>
                </tbody>
            </table>
            <div class="sig-date-block" style="margin-top:40px;">
                <p>H. Puebla de Zaragoza a <strong id="anexo8_dia">___</strong> del mes de <strong
                        id="anexo8_mes">_______________</strong> del año <strong id="anexo8_anio">_______</strong></p>
            </div>
            <div class="sig-block">
                <div class="sig-party">
                    <div class="sig-line">
                        <img src="{{ asset('images/firmas/firma_emilio_flores.png') }}" alt="Firma Emilio Flores Cervantes">
                    </div>
                    <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                    <div class="sig-role">Emilio Flores Cervantes — Administrador Único</div>
                </div>
                <div class="sig-party">
                    <div class="sig-line">
                        <img id="anexo8_firma_img" src="" alt="Firma del distribuidor"
                            style="display:none;">
                    </div>
                    <div class="sig-name" id="anexo8_nombre_dist">El Distribuidor</div>
                    <div class="sig-role">Apartado 5 de la Carátula</div>
                </div>
            </div>
        </div>

        <div class="annex-section" id="anexo10">
            <span class="annex-tag">Anexo 10</span>
            <h2>Comisiones a Devengar por El Distribuidor</h2>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio de Venta</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Comercialización directa de la Tarjeta de Descuento por El Distribuidor</td>
                        <td><span class="amount">$800.00 M.N.</span> IVA incluido</td>
                        <td><span class="amount">$20.00 M.N.</span> IVA incluido<br />por tarjeta pagada al Proveedor
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="sig-date-block" style="margin-top:40px;">
                <p>H. Puebla de Zaragoza a <strong id="anexo10_dia">___</strong> del mes de <strong
                        id="anexo10_mes">_______________</strong> del año <strong id="anexo10_anio">_______</strong>
                </p>
            </div>
            <div class="sig-block">
                <div class="sig-party">
                    <div class="sig-line">
                        <img src="{{ asset('images/firmas/firma_emilio_flores.png') }}" alt="Firma Emilio Flores Cervantes">
                    </div>
                    <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                    <div class="sig-role">Emilio Flores Cervantes — Administrador Único</div>
                </div>
                <div class="sig-party">
                    <div class="sig-line">
                        <img id="anexo10_firma_img" src="" alt="Firma del distribuidor"
                            style="display:none;">
                    </div>
                    <div class="sig-name" id="anexo10_nombre_dist">El Distribuidor</div>
                    <div class="sig-role">Apartado 5 de la Carátula</div>
                </div>
            </div>
        </div>

        <div class="annex-section" id="anexo12">
            <span class="annex-tag">Anexo 12</span>
            <h2>Declaración de Dueño Beneficiario</h2>
            <p
                style="font-size:11px;font-family:'Montserrat',sans-serif;letter-spacing:.5px;color:var(--muted);margin-bottom:24px;">
                De conformidad con los Artículos 18 Fracción I y III de la Ley Federal para Prevención e Identificación
                de Operaciones con Recursos de Procedencia Ilícita, el Artículo 15 de su Reglamento y el Artículo 13 de
                las Reglas de Carácter General.
            </p>
            <div
                style="background:#fff;border:1px solid var(--rule);border-top:3px solid var(--navy);padding:28px 28px 32px;border-radius:2px;">
                <div
                    style="font-family:'Montserrat',sans-serif;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--navy);margin-bottom:20px;">
                    Datos de Identificación del Cliente
                </div>
                <div class="form-grid">
                    <div class="form-field full"><label>1. Apellido paterno, materno y nombre(s)</label><span
                            class="badge" id="c_apellidos_nombre">Por llenar</span></div>
                    <div class="form-field"><label>2. Fecha de nacimiento</label><span class="badge"
                            id="c_fecha_nacimiento">Por llenar</span></div>
                    <div class="form-field"><label>3. País de nacimiento</label><span class="badge"
                            id="c_pais_nacimiento">Por llenar</span></div>
                    <div class="form-field"><label>7. Teléfonos</label><span class="badge" id="c_telefono_12">Por
                            llenar</span></div>
                    <div class="form-field"><label>8. Correo electrónico</label><span class="badge"
                            id="c_correo_12">Por llenar</span></div>
                    <div class="form-field"><label>10. RFC</label><span class="badge" id="c_rfc_12">Por
                            llenar</span></div>
                </div>

                {{-- ── DECLARACIÓN 18 — radios que notifican al padre via postMessage ── --}}
                <div
                    style="margin-top:24px;padding:18px 22px;background:#f5f5f5;border-left:4px solid #111;border-radius:2px;">
                    <div
                        style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;">
                        18. Declaración de Dueño Beneficiario <span style="color:#cc0000;">*</span>
                    </div>
                    <p style="font-size:14px;line-height:1.7;margin-bottom:16px;">
                        Declaro que soy el beneficiario directo de la operación y no existe ningún otro dueño
                        beneficiario o beneficiario controlador.
                    </p>

                    <div id="ben_aviso"
                        style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fffbeb;border:1.5px solid #d97706;border-radius:4px;font-size:13px;color:#92400e;margin-bottom:14px;">
                        <span>⚠</span> Selecciona una opción — es requerida para continuar con el formulario.
                    </div>

                    <div class="beneficiary-check">
                        <input type="radio" id="check_beneficiario_si" name="beneficiario_directo" value="SI">
                        <label for="check_beneficiario_si"><strong>Sí</strong> — Soy el beneficiario directo y único de
                            esta operación</label>
                    </div>
                    <div class="beneficiary-check" style="margin-top:8px;">
                        <input type="radio" id="check_beneficiario_no" name="beneficiario_directo" value="NO">
                        <label for="check_beneficiario_no"><strong>No</strong> — Existe otro dueño beneficiario o
                            beneficiario controlador</label>
                    </div>

                    <div id="ben_ok"
                        style="display:none;margin-top:12px;padding:10px 14px;background:#ecfdf5;border:1.5px solid #10b981;border-radius:4px;font-size:13px;color:#065f46;">
                        ✓ <span id="ben_ok_txt"></span>
                    </div>
                </div>

                {{-- ── Bloque firma Anexo 12 ── --}}
                <div style="margin-top:32px;text-align:center;">
                    <p style="font-size:13px;margin-bottom:20px;">
                        H. Puebla de Zaragoza a <strong id="anexo12_dia">___</strong> del mes de
                        <strong id="anexo12_mes">_______________</strong> del año
                        <strong id="anexo12_anio">_______</strong>
                    </p>
                    <div style="display:inline-block;width:300px;">
                        <div
                            style="border-bottom:1px solid var(--ink);height:60px;margin-bottom:8px;display:flex;align-items:flex-end;justify-content:center;overflow:hidden;padding-bottom:4px;">
                            <img id="anexo12_firma_img" src="" alt="Firma del distribuidor"
                                style="display:none;max-height:54px;max-width:260px;object-fit:contain;">
                        </div>
                        <div style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--navy);"
                            id="anexo12_nombre_dist">Nombre y Firma</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">Dueño Beneficiario</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="doc-footer">Pasaporte a tu Salud, S.A. de C.V. · R.F.C. PTS260126KX3 · Documento generado para uso
        interno</div>

    <script>
        (function() {
            // ── 1. Fecha actual en todos los bloques ──────────────────────────────
            const hoy = new Date();
            const dia = hoy.getDate();
            const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];
            const mes = meses[hoy.getMonth()];
            const anio = hoy.getFullYear();

            ['sig', 'anexo8', 'anexo10', 'anexo12'].forEach(id => {
                const d = document.getElementById(id + '_dia');
                const m = document.getElementById(id + '_mes');
                const a = document.getElementById(id + '_anio');
                if (d) d.textContent = dia;
                if (m) m.textContent = mes;
                if (a) a.textContent = anio;
            });

            // ── Apartados 12, 13 y 15 de la Carátula ─────────────────────────────
            const fmt = d => `${d.getDate()} de ${meses[d.getMonth()]} de ${d.getFullYear()}`;
            const fin = new Date(hoy);
            fin.setFullYear(fin.getFullYear() + 3);

            const cFechaInicio = document.getElementById('c_fecha_inicio');
            const cFechaFin    = document.getElementById('c_fecha_fin');
            const cFechaFirma  = document.getElementById('c_fecha_firma');
            if (cFechaInicio) cFechaInicio.textContent = fmt(hoy);
            if (cFechaFin)    cFechaFin.textContent    = fmt(fin);
            if (cFechaFirma)  cFechaFirma.textContent  = fmt(hoy);

            // ── 2. Leer firma y nombre — sessionStorage primero, luego URL params ──
            // El padre (solicitud_distribuidor) guarda en sessionStorage antes de recargar el iframe.
            let firmaData = '';
            let nombre = '';

            try {
                firmaData = sessionStorage.getItem('pats_firma') || '';
                nombre = sessionStorage.getItem('pats_nombre') || '';
            } catch (_) {}

            // Fallback: URL params (útil si sessionStorage no está disponible)
            if (!firmaData) {
                const params = new URLSearchParams(window.location.search);
                firmaData = params.get('firma') || '';
                nombre = params.get('nombre') || '';
            }

            // ── 3. Inyectar firma en todos los bloques ────────────────────────────
            if (firmaData) {
                ['sig_firma_img', 'anexo8_firma_img', 'anexo10_firma_img', 'anexo12_firma_img'].forEach(imgId => {
                    const img = document.getElementById(imgId);
                    if (img) {
                        img.src = firmaData;
                        img.style.display = '';
                    }
                });
            }

            if (nombre) {
                ['sig_nombre_dist', 'anexo8_nombre_dist', 'anexo10_nombre_dist', 'anexo12_nombre_dist'].forEach(
                nomId => {
                    const el = document.getElementById(nomId);
                    if (el) el.textContent = nombre;
                });
            }

            // ── 3b. Leer datos de carátula (Apartados 5-9) desde sessionStorage ────
            try {
                const caratula = JSON.parse(sessionStorage.getItem('pats_caratula') || '{}');
                const map = {
                    c_nombre: caratula.nombre,
                    c_domicilio: caratula.domicilio,
                    c_rfc: caratula.rfc,
                    c_franquicia: caratula.nombre,
                    c_demarcacion: caratula.demarcacion,
                    // Anexo 12 — Datos de identificación del cliente
                    c_apellidos_nombre: caratula.nombre,
                    c_telefono_12: caratula.telefono,
                    c_correo_12: caratula.correo,
                    c_rfc_12: caratula.rfc,
                    c_pais_nacimiento: caratula.pais,
                };
                Object.entries(map).forEach(([id, val]) => {
                    if (!val) return;
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                });
            } catch (_) {}

            // ── 4. Radios beneficiario directo → postMessage al padre ─────────────
            // El padre (mismo origen) escucha y guarda el valor en un hidden input.
            const radios = document.querySelectorAll('input[name="beneficiario_directo"]');
            const aviso = document.getElementById('ben_aviso');
            const benOk = document.getElementById('ben_ok');
            const benOkTxt = document.getElementById('ben_ok_txt');

            const textos = {
                SI: 'Declaración confirmada: eres el beneficiario directo y único.',
                NO: 'Indicaste que existe otro dueño beneficiario o beneficiario controlador.',
            };

            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    const val = radio.value;

                    // Feedback visual dentro del contrato
                    if (aviso) aviso.style.display = 'none';
                    if (benOk) {
                        benOk.style.display = '';
                        benOk.style.borderColor = val === 'SI' ? '#10b981' : '#ef4444';
                        benOk.style.background = val === 'SI' ? '#ecfdf5' : '#fff1f2';
                        benOk.style.color = val === 'SI' ? '#065f46' : '#991b1b';
                    }
                    if (benOkTxt) benOkTxt.textContent = textos[val] || '';

                    // ── Notificar al padre (solicitud_distribuidor) via postMessage ──
                    // Mismo origen → window.parent funciona sin restricciones CORS.
                    try {
                        window.parent.postMessage({
                                type: 'pats_beneficiario',
                                valor: val
                            },
                            window.location.origin
                        );
                    } catch (_) {}
                });
            });

            // Si ya había un valor guardado en sessionStorage, pre-seleccionarlo
            try {
                const prevVal = sessionStorage.getItem('pats_beneficiario');
                if (prevVal) {
                    const prevRadio = document.querySelector('input[name="beneficiario_directo"][value="' + prevVal +
                        '"]');
                    if (prevRadio) {
                        prevRadio.checked = true;
                        prevRadio.dispatchEvent(new Event('change'));
                    }
                }
            } catch (_) {}

        })();
    </script>
</body>

</html>
