<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activa tu Pasaporte PATS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── Layout ── */
        .wrap {
            max-width: 80%;
            margin: 0 auto;
            padding: 2rem 1.25rem 4rem;
        }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, #1e3a5f, #2563eb);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            color: #fff;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .hero p {
            font-size: .95rem;
            opacity: .85;
        }

        .hero__dist {
            display: inline-block;
            margin-top: 1rem;
            padding: .3rem .9rem;
            background: rgba(255, 255, 255, .15);
            border-radius: 99px;
            font-size: .82rem;
        }

        /* ── Steps ── */
        .steps {
            display: flex;
            gap: 0;
            margin-bottom: 2rem;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #dde3ef;
        }

        .step {
            flex: 1;
            padding: .75rem .5rem;
            text-align: center;
            font-size: .78rem;
            font-weight: 600;
            background: #fff;
            color: #94a3b8;
            border-right: 1px solid #dde3ef;
            cursor: default;
            transition: all .2s;
        }

        .step:last-child {
            border: none;
        }

        .step.done {
            background: #d1fae5;
            color: #065f46;
        }

        .step.active {
            background: #2563eb;
            color: #fff;
        }

        .step__num {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: .15rem;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border: 1px solid #dde3ef;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(30, 58, 95, .06);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }

        .card__head {
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid #dde3ef;
            background: linear-gradient(to right, #fff, #f8fafc);
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .card__title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0;
        }

        .card__title i {
            color: #2563eb;
        }

        .card__body {
            padding: 1.4rem;
        }

        /* ── Panel ── */
        .panel {
            display: none;
        }

        .panel.active {
            display: block;
        }

        /* ── Fields ── */
        .fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        .field.full {
            grid-column: 1/-1;
        }

        .field label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
        }

        .field input,
        .field select {
            padding: .65rem .9rem;
            font-size: .9rem;
            border: 2px solid #dde3ef;
            border-radius: 8px;
            font-family: inherit;
            background: #fff;
            transition: border .15s, box-shadow .15s;
        }

        .field input:focus,
        .field select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .field input[readonly] {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }

        @media(max-width: 580px) {
            .fields {
                grid-template-columns: 1fr;
            }
        }

        /* ── Password strength ── */
        .pwd-bar {
            height: 4px;
            border-radius: 2px;
            background: #e2e8f0;
            margin-top: .35rem;
            overflow: hidden;
        }

        .pwd-bar__fill {
            height: 100%;
            width: 0;
            transition: width .3s, background .3s;
            border-radius: 2px;
        }

        .pwd-hint {
            font-size: .73rem;
            color: #64748b;
            margin-top: .25rem;
        }

        /* ── Planes ── */
        .planes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media(max-width: 480px) {
            .planes {
                grid-template-columns: 1fr;
            }
        }

        .plan {
            border: 2px solid #dde3ef;
            border-radius: 12px;
            padding: 1.25rem;
            cursor: pointer;
            text-align: center;
            transition: all .15s;
        }

        .plan:hover {
            border-color: #2563eb;
        }

        .plan.active {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .plan__price {
            font-size: 1.9rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        .plan.active .plan__price {
            color: #2563eb;
        }

        .plan__label {
            font-size: .82rem;
            color: #64748b;
            margin-bottom: .35rem;
        }

        .plan__save {
            font-size: .73rem;
            background: #d1fae5;
            color: #065f46;
            border-radius: 99px;
            padding: .15rem .6rem;
            display: inline-block;
            margin-top: .4rem;
        }

        /* ── Tarjeta animada ── */
        .plastic-wrap {
            perspective: 1000px;
            width: 100%;
            max-width: 290px;
            height: 170px;
            cursor: pointer;
            margin: 0 auto 1.25rem;
        }

        .plastic {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform .55s cubic-bezier(.4, 0, .2, 1);
        }

        .plastic-wrap.flipped .plastic {
            transform: rotateY(180deg);
        }

        .pface {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 14px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .pfront {
            background: linear-gradient(135deg, #0b3d60, #06283d);
            color: #fff;
        }

        .pback {
            background: linear-gradient(135deg, #1a2540, #0e1a30);
            color: #fff;
            transform: rotateY(180deg);
        }

        .chip {
            width: 32px;
            height: 24px;
            background: linear-gradient(135deg, #b8860b, #daa520);
            border-radius: 4px;
        }

        .cnum {
            font-family: monospace;
            font-size: .9rem;
            letter-spacing: 2px;
            text-align: center;
        }

        .cbot {
            display: flex;
            justify-content: space-between;
            font-size: .72rem;
        }

        .cbot small {
            font-size: .58rem;
            opacity: .6;
            display: block;
        }

        .mag {
            height: 32px;
            background: #000;
            margin: 12px 0;
            border-radius: 2px;
        }

        .cvvbox {
            background: #fff;
            color: #111;
            padding: 5px 10px;
            border-radius: 3px;
            text-align: right;
            font-family: monospace;
            font-size: .9rem;
        }

        /* ── Botones ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .7rem 1.4rem;
            font-size: .9rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s;
        }

        .btn--primary {
            background: #2563eb;
            color: #fff;
        }

        .btn--primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
        }

        .btn--primary:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn--ghost {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #334155;
        }

        .btn--ghost:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        /* ── Actions ── */
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #dde3ef;
        }

        /* ── Alerts ── */
        .alert {
            padding: .85rem 1.1rem;
            border-radius: 8px;
            font-size: .88rem;
            margin-bottom: 1rem;
            display: none;
            align-items: center;
            gap: .6rem;
        }

        .alert.show {
            display: flex;
        }

        .alert--danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert--success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        /* ── Resumen ── */
        .resumen {
            background: #f8fafc;
            border: 1px solid #dde3ef;
            border-radius: 10px;
            padding: 1rem;
        }

        .res-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .45rem 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: .88rem;
        }

        .res-row:last-child {
            border: none;
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        /* ── Comprobante ── */
        .comprobante {
            text-align: center;
            padding: 2.5rem 1rem;
        }

        .comprobante__icon {
            font-size: 4.5rem;
            color: #10b981;
            display: block;
            margin-bottom: 1rem;
        }

        .comprobante h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: .5rem;
        }

        .comprobante p {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        /* ── Spin ── */
        .spin {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: sp .6s linear infinite;
        }

        @keyframes sp {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── QA hint ── */
        .qa-hint {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: .65rem .9rem;
            font-size: .75rem;
            color: #166534;
            margin-top: .6rem;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>

    <div class="wrap">

        {{-- Hero --}}
        <div class="hero">
            <h1><i class="mdi mdi-passport-biometric"></i> Activa tu Pasaporte</h1>
            <p>Completa tu registro, elige tu plan y paga de forma segura con tu tarjeta.</p>
            @if (!empty($ctx['nombre']))
                <span class="hero__dist">
                    <i class="mdi mdi-store-outline"></i> {{ $ctx['nombre'] }}
                </span>
            @endif
        </div>

        {{-- Steps ── --}}
        <div class="steps" id="steps">
            <div class="step active" data-step="1"><span class="step__num">1</span>Cuenta</div>
            <div class="step" data-step="2"><span class="step__num">2</span>Datos</div>
            <div class="step" data-step="3"><span class="step__num">3</span>Domicilio</div>
            <div class="step" data-step="4"><span class="step__num">4</span>Plan y pago</div>
            <div class="step" data-step="5"><span class="step__num">5</span>Listo</div>
        </div>

        {{-- Alerts --}}
        <div class="alert alert--danger" id="alertErr">
            <i class="mdi mdi-alert-circle"></i><span id="alertErrMsg"></span>
        </div>

        <form id="frmAdquirir" autocomplete="off">
            @csrf
            <input type="hidden" name="token_publico" value="{{ $token }}">

            {{-- ══ PASO 1: Cuenta ══ --}}
            <div class="panel active" data-panel="1">
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-account-plus-outline"></i> Crea tu cuenta</h3>
                    </div>
                    <div class="card__body">
                        <div class="fields">
                            <div class="field full">
                                <label>Correo electrónico</label>
                                <input type="email" id="correo" name="correo" placeholder="tu@correo.com"
                                    required>
                            </div>
                            <div class="field">
                                <label>Contraseña</label>
                                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres"
                                    required>
                                <div class="pwd-bar">
                                    <div class="pwd-bar__fill" id="pwdBar"></div>
                                </div>
                                <span class="pwd-hint" id="pwdHint">Escribe tu contraseña</span>
                            </div>
                            <div class="field">
                                <label>Confirmar contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Repite tu contraseña" required>
                            </div>
                            <div class="field full">
                                <label>Teléfono (10 dígitos)</label>
                                <input type="text" id="telefono_usuario" name="telefono_usuario" maxlength="10"
                                    inputmode="numeric" placeholder="3312345678" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ PASO 2: Datos personales ══ --}}
            <div class="panel" data-panel="2">
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-account-outline"></i> Datos personales</h3>
                    </div>
                    <div class="card__body">
                        <div class="fields">
                            <div class="field full">
                                <label>Nombre(s)</label>
                                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
                            </div>
                            <div class="field">
                                <label>Apellido paterno</label>
                                <input type="text" id="apellido_pa" name="apellido_pa" required>
                            </div>
                            <div class="field">
                                <label>Apellido materno</label>
                                <input type="text" id="apellido_ma" name="apellido_ma">
                            </div>
                            <div class="field">
                                <label>CURP</label>
                                <input type="text" id="curp_usuario" name="curp_usuario" maxlength="18"
                                    style="text-transform:uppercase;" required>
                            </div>
                            <div class="field">
                                <label>Fecha de nacimiento</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>
                            <div class="field full">
                                <label>Tipo de cliente</label>
                                <select id="tipo_cliente" name="tipo_cliente">
                                    <option value="privado">Privado</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ PASO 3: Domicilio ══ --}}
            <div class="panel" data-panel="3">
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-map-marker-outline"></i> Domicilio</h3>
                    </div>
                    <div class="card__body">
                        <div class="fields">
                            <div class="field full">
                                <label>Calle</label>
                                <input type="text" id="dom_calle" name="dom_calle" required>
                            </div>
                            <div class="field">
                                <label>Número exterior</label>
                                <input type="text" id="dom_num_ext" name="dom_num_ext" required>
                            </div>
                            <div class="field">
                                <label>Número interior</label>
                                <input type="text" id="dom_num_int" name="dom_num_int">
                            </div>
                            <div class="field full">
                                <label>Colonia</label>
                                <input type="text" id="dom_colonia" name="dom_colonia" required>
                            </div>
                            <div class="field">
                                <label>Código postal</label>
                                <input type="text" id="dom_cp" name="dom_cp" maxlength="5"
                                    inputmode="numeric" required>
                            </div>
                            <div class="field">
                                <label>Ciudad / Municipio</label>
                                <input type="text" id="dom_municipio" name="dom_municipio" required>
                            </div>
                            <div class="field">
                                <label>Estado</label>
                                <select id="dom_estado" name="dom_estado" required>
                                    @foreach (['AGS' => 'Aguascalientes', 'BCN' => 'Baja California', 'BCS' => 'Baja California Sur', 'CAM' => 'Campeche', 'CHP' => 'Chiapas', 'CHH' => 'Chihuahua', 'CDMX' => 'Ciudad de México', 'COA' => 'Coahuila', 'COL' => 'Colima', 'DGO' => 'Durango', 'GTO' => 'Guanajuato', 'GRO' => 'Guerrero', 'HGO' => 'Hidalgo', 'JAL' => 'Jalisco', 'MEX' => 'Estado de México', 'MIC' => 'Michoacán', 'MOR' => 'Morelos', 'NAY' => 'Nayarit', 'NLE' => 'Nuevo León', 'OAX' => 'Oaxaca', 'PUE' => 'Puebla', 'QRO' => 'Querétaro', 'ROO' => 'Quintana Roo', 'SLP' => 'San Luis Potosí', 'SIN' => 'Sinaloa', 'SON' => 'Sonora', 'TAB' => 'Tabasco', 'TAM' => 'Tamaulipas', 'TLAX' => 'Tlaxcala', 'VER' => 'Veracruz', 'YUC' => 'Yucatán', 'ZAC' => 'Zacatecas'] as $acr => $nom)
                                        <option value="{{ $nom }}"
                                            {{ ($ctx['region'] ?? '') === $acr ? 'selected' : '' }}>{{ $nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>País</label>
                                <input type="text" id="dom_pais" name="dom_pais"
                                    value="{{ $ctx['pais'] ?? 'México' }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ PASO 4: Plan y pago ══ --}}
            <div class="panel" data-panel="4">

                {{-- Planes --}}
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-star-outline"></i> Elige tu plan</h3>
                    </div>
                    <div class="card__body">
                        <div class="planes">
                            <div class="plan active" data-frecuencia="MENSUAL"
                                data-monto="{{ $precios['mensual']['monto'] }}"
                                data-tipo="{{ $precios['mensual']['id_tipo_precio'] }}">
                                <div class="plan__label">Mensual</div>
                                <div class="plan__price">${{ number_format($precios['mensual']['monto'], 0) }}</div>
                                <div style="font-size:.78rem;color:#64748b;">MXN / mes</div>
                                <span class="plan__save">Flexible</span>
                            </div>
                            <div class="plan" data-frecuencia="ANUAL"
                                data-monto="{{ $precios['anual']['monto'] }}"
                                data-tipo="{{ $precios['anual']['id_tipo_precio'] }}">
                                <div class="plan__label">Anual</div>
                                <div class="plan__price">${{ number_format($precios['anual']['monto'], 0) }}</div>
                                <div style="font-size:.78rem;color:#64748b;">MXN / año</div>
                                <span class="plan__save">Ahorra más</span>
                            </div>
                        </div>
                        <input type="hidden" name="frecuencia" id="h_frecuencia" value="MENSUAL">
                        <input type="hidden" name="monto_orden" id="h_monto"
                            value="{{ $precios['mensual']['monto'] }}">
                        <input type="hidden" name="id_tipo_precio" id="h_tipo"
                            value="{{ $precios['mensual']['id_tipo_precio'] }}">
                    </div>
                </div>

                {{-- Tarjeta --}}
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-credit-card-outline"></i> Datos de tarjeta</h3>
                    </div>
                    <div class="card__body">
                        <div class="plastic-wrap" id="cardWrap">
                            <div class="plastic">
                                <div class="pface pfront">
                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                        <div class="chip"></div>
                                        <span id="disp-brand"
                                            style="font-size:.95rem;font-weight:700;letter-spacing:1px;">CARD</span>
                                    </div>
                                    <div class="cnum" id="disp-num">•••• •••• •••• ••••</div>
                                    <div class="cbot">
                                        <div><small>Titular</small><span id="disp-name">NOMBRE APELLIDO</span></div>
                                        <div><small>Vence</small><span id="disp-exp">MM/AA</span></div>
                                    </div>
                                </div>
                                <div class="pface pback">
                                    <div class="mag"></div>
                                    <div class="cvvbox"><span id="disp-cvv">•••</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="fields">
                            <div class="field full">
                                <label>Nombre del titular</label>
                                <input type="text" id="cardholderName" name="cardholderName"
                                    placeholder="Como aparece en la tarjeta">
                            </div>
                            <div class="field full">
                                <label>Número de tarjeta</label>
                                <input type="text" id="pan" name="pan" maxlength="19"
                                    placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="field">
                                <label>Vencimiento (MM/AA)</label>
                                <input type="text" id="exp_display" maxlength="5" placeholder="MM/AA">
                                <input type="hidden" id="expDate" name="expDate">
                            </div>
                            <div class="field">
                                <label>CVV</label>
                                <input type="password" id="cvv2" name="cvv2" maxlength="4"
                                    placeholder="•••">
                            </div>
                        </div>

                        <div class="qa-hint">
                            <strong>Tarjeta QA:</strong> 5439240350653004 · CVV 123 · Vence 01/27 · Titular: PASAPORTE
                        </div>
                    </div>
                </div>

                {{-- Resumen --}}
                <div class="card">
                    <div class="card__head">
                        <h3 class="card__title"><i class="mdi mdi-receipt-text-outline"></i> Resumen</h3>
                    </div>
                    <div class="card__body">
                        <div class="resumen">
                            <div class="res-row"><span>Titular</span><strong id="sum-nombre">—</strong></div>
                            <div class="res-row"><span>Correo</span><strong id="sum-correo">—</strong></div>
                            <div class="res-row"><span>Plan</span><strong id="sum-plan">Mensual</strong></div>
                            <div class="res-row"><span>Total</span><strong id="sum-monto" style="color:#2563eb;">$800
                                    MXN</strong></div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ PASO 5: Comprobante ══ --}}
            <div class="panel" data-panel="5">
                <div class="card" id="panelProcesando" style="display:none;">
                    <div class="card__body" style="text-align:center;padding:3rem;">
                        <div class="spin"
                            style="width:36px;height:36px;border-width:3px;border-color:#2563eb;border-top-color:transparent;margin:0 auto 1rem;">
                        </div>
                        <p style="color:#64748b;">Procesando tu pago de forma segura...</p>
                    </div>
                </div>
                <div class="card" id="panelComp" style="display:none;">
                    <div class="card__body comprobante">
                        <i class="mdi mdi-check-circle comprobante__icon"></i>
                        <h3>¡Bienvenido a PATS!</h3>
                        <p>Tu pasaporte ha sido activado y tu cuenta creada exitosamente.</p>
                        <div class="resumen" style="max-width:400px;margin:0 auto 1.5rem;text-align:left;">
                            <div class="res-row"><span>Folio</span><strong id="comp-folio">—</strong></div>
                            <div class="res-row"><span>Autorización</span><strong id="comp-auth">—</strong></div>
                            <div class="res-row"><span>Tarjeta</span><strong id="comp-card">—</strong></div>
                            <div class="res-row"><span>Vigencia</span><strong id="comp-vig">—</strong></div>
                            <div class="res-row"><span>Monto</span><strong id="comp-monto"
                                    style="color:#2563eb;">—</strong></div>
                        </div>
                        <p style="font-size:.82rem;color:#64748b;margin-bottom:1.5rem;">
                            Guardamos tu cuenta con el correo y contraseña que ingresaste.<br>
                            Redirigiendo a tu pasaporte...
                        </p>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="actions" id="actionsBar">
                <button type="button" class="btn btn--ghost" id="btnPrev" style="visibility:hidden;">
                    <i class="mdi mdi-chevron-left"></i> Atrás
                </button>
                <div style="display:flex;gap:.75rem;align-items:center;">
                    <button type="button" class="btn btn--primary" id="btnNext">
                        Siguiente <i class="mdi mdi-chevron-right"></i>
                    </button>
                    <button type="submit" class="btn btn--primary hidden" id="btnPagar">
                        <i class="mdi mdi-lock"></i>
                        <span id="btnPagarTxt">Pagar $800 MXN</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function() {
            'use strict';

            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;
            axios.defaults.headers.common['Accept'] = 'application/json';

            let step = 1;
            const TOTAL = 5;

            // ── UI sync ─────────────────────────────────────────
            function syncUI() {
                document.querySelectorAll('[data-panel]').forEach(p =>
                    p.classList.toggle('active', +p.dataset.panel === step));

                document.querySelectorAll('[data-step]').forEach(s => {
                    const n = +s.dataset.step;
                    s.classList.remove('active', 'done');
                    if (n === step) s.classList.add('active');
                    else if (n < step) s.classList.add('done');
                });

                const btnPrev = document.getElementById('btnPrev');
                const btnNext = document.getElementById('btnNext');
                const btnPagar = document.getElementById('btnPagar');
                const actions = document.getElementById('actionsBar');

                btnPrev.style.visibility = step <= 1 ? 'hidden' : 'visible';
                btnNext.classList.toggle('hidden', step >= TOTAL - 1);
                btnPagar.classList.toggle('hidden', step !== TOTAL - 1);
                actions.classList.toggle('hidden', step === TOTAL);

                syncResumen();
            }

            // ── Validaciones ─────────────────────────────────────
            function validateStep(s) {
                if (s === 1) {
                    if (!val('correo') || !val('correo').includes('@')) return err('Ingresa un correo válido.');
                    const pwd = document.getElementById('password').value;
                    const pwd2 = document.getElementById('password_confirmation').value;
                    if (pwd.length < 8) return err('La contraseña debe tener al menos 8 caracteres.');
                    if (pwd !== pwd2) return err('Las contraseñas no coinciden.');
                    const tel = val('telefono_usuario').replace(/\D/g, '');
                    if (tel.length !== 10) return err('El teléfono debe tener 10 dígitos.');
                }
                if (s === 2) {
                    if (!val('nombre_usuario')) return err('Escribe tu nombre.');
                    if (!val('apellido_pa')) return err('Escribe tu apellido paterno.');
                    if (!val('curp_usuario')) return err('Escribe tu CURP.');
                    if (!val('fecha_nacimiento')) return err('Indica tu fecha de nacimiento.');
                }
                if (s === 3) {
                    if (!val('dom_calle')) return err('Escribe la calle.');
                    if (!val('dom_num_ext')) return err('Escribe el número exterior.');
                    if (!val('dom_colonia')) return err('Escribe la colonia.');
                    const cp = val('dom_cp').replace(/\D/g, '');
                    if (cp.length !== 5) return err('Código postal inválido.');
                    if (!val('dom_municipio')) return err('Escribe la ciudad.');
                }
                if (s === 4) {
                    if (!val('cardholderName')) return err('Escribe el nombre del titular.');
                    const pan = document.getElementById('pan').value.replace(/\s/g, '');
                    if (pan.length < 15) return err('Número de tarjeta inválido.');
                    if (!document.getElementById('expDate').value) return err('Indica la fecha de vencimiento.');
                    if (!val('cvv2')) return err('Escribe el CVV.');
                }
                hideErr();
                return true;
            }

            function val(id) {
                return (document.getElementById(id)?.value || '').trim();
            }

            function err(msg) {
                const el = document.getElementById('alertErr');
                document.getElementById('alertErrMsg').textContent = msg;
                el.classList.add('show');
                setTimeout(() => el.classList.remove('show'), 5000);
                return false;
            }

            function hideErr() {
                document.getElementById('alertErr').classList.remove('show');
            }

            // ── Navegación ───────────────────────────────────────
            document.getElementById('btnPrev')?.addEventListener('click', () => {
                if (step > 1) {
                    step--;
                    syncUI();
                }
            });
            document.getElementById('btnNext')?.addEventListener('click', () => {
                if (!validateStep(step)) return;
                step++;
                syncUI();
            });

            // ── Planes ───────────────────────────────────────────
            document.querySelectorAll('.plan').forEach(p => {
                p.addEventListener('click', () => {
                    document.querySelectorAll('.plan').forEach(x => x.classList.remove('active'));
                    p.classList.add('active');
                    document.getElementById('h_frecuencia').value = p.dataset.frecuencia;
                    document.getElementById('h_monto').value = p.dataset.monto;
                    document.getElementById('h_tipo').value = p.dataset.tipo;
                    const fmt = new Intl.NumberFormat('es-MX').format(p.dataset.monto);
                    document.getElementById('btnPagarTxt').textContent = `Pagar $${fmt} MXN`;
                    syncResumen();
                });
            });

            // ── Tarjeta animada ───────────────────────────────────
            document.getElementById('cardWrap')?.addEventListener('click', () =>
                document.getElementById('cardWrap').classList.toggle('flipped'));

            document.getElementById('cardholderName')?.addEventListener('input', e =>
                document.getElementById('disp-name').textContent = e.target.value.toUpperCase() || 'NOMBRE APELLIDO'
                );
            document.getElementById('cvv2')?.addEventListener('input', e =>
                document.getElementById('disp-cvv').textContent = e.target.value.replace(/./g, '•') || '•••');
            document.getElementById('exp_display')?.addEventListener('input', e => {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2, 4);
                e.target.value = v;
                document.getElementById('disp-exp').textContent = v || 'MM/AA';
                const clean = v.replace('/', '');
                if (clean.length === 4)
                    document.getElementById('expDate').value = clean.substring(2, 4) + clean.substring(0, 2);
            });
            document.getElementById('pan')?.addEventListener('input', e => {
                let v = e.target.value.replace(/\D/g, '').substring(0, 16);
                e.target.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
                document.getElementById('disp-num').textContent = v.replace(/(\d{4})(?=\d)/g, '$1 ') ||
                    '•••• •••• •••• ••••';
                const brand = v[0] === '4' ? 'VISA' : (v[0] === '5' || v[0] === '2') ? 'MASTERCARD' :
                    v.startsWith('34') || v.startsWith('37') ? 'AMEX' : 'CARD';
                document.getElementById('disp-brand').textContent = brand;
            });

            // ── Password strength ────────────────────────────────
            document.getElementById('password')?.addEventListener('input', e => {
                const v = e.target.value;
                const bar = document.getElementById('pwdBar');
                const hint = document.getElementById('pwdHint');
                let strength = 0;
                if (v.length >= 8) strength++;
                if (/[A-Z]/.test(v)) strength++;
                if (/[0-9]/.test(v)) strength++;
                if (/[^A-Za-z0-9]/.test(v)) strength++;
                const pct = strength * 25;
                const color = strength <= 1 ? '#ef4444' : strength === 2 ? '#f59e0b' : strength === 3 ?
                    '#3b82f6' : '#10b981';
                bar.style.width = pct + '%';
                bar.style.background = color;
                const labels = ['', 'Muy débil', 'Débil', 'Media', 'Fuerte'];
                hint.textContent = labels[strength] || 'Escribe tu contraseña';
                hint.style.color = color;
            });

            // ── Resumen ─────────────────────────────────────────
            function syncResumen() {
                const nombre = [val('nombre_usuario'), val('apellido_pa')].filter(Boolean).join(' ') || '—';
                const correo = val('correo') || '—';
                const plan = document.getElementById('h_frecuencia').value === 'ANUAL' ? 'Anual' : 'Mensual';
                const monto = document.getElementById('h_monto').value;
                const fmt = new Intl.NumberFormat('es-MX').format(monto);

                const el = id => document.getElementById(id);
                if (el('sum-nombre')) el('sum-nombre').textContent = nombre;
                if (el('sum-correo')) el('sum-correo').textContent = correo;
                if (el('sum-plan')) el('sum-plan').textContent = plan;
                if (el('sum-monto')) el('sum-monto').textContent = `$${fmt} MXN`;
                if (el('btnPagarTxt')) el('btnPagarTxt').textContent = `Pagar $${fmt} MXN`;
            }

            ['nombre_usuario', 'apellido_pa', 'correo'].forEach(id =>
                document.getElementById(id)?.addEventListener('input', syncResumen));

            // ── Submit ───────────────────────────────────────────
            document.getElementById('frmAdquirir')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!validateStep(4)) return;

                step = 5;
                syncUI();
                document.getElementById('panelProcesando').style.display = 'block';
                document.getElementById('panelComp').style.display = 'none';

                const payload = {
                    token_publico: document.querySelector('input[name="token_publico"]').value,
                    correo: val('correo'),
                    password: document.getElementById('password').value,
                    password_confirmation: document.getElementById('password_confirmation').value,
                    telefono_usuario: val('telefono_usuario'),
                    nombre_usuario: val('nombre_usuario'),
                    apellido_pa: val('apellido_pa'),
                    apellido_ma: val('apellido_ma'),
                    curp_usuario: val('curp_usuario').toUpperCase(),
                    fecha_nacimiento: val('fecha_nacimiento'),
                    tipo_cliente: document.getElementById('tipo_cliente').value,
                    dom_calle: val('dom_calle'),
                    dom_num_ext: val('dom_num_ext'),
                    dom_num_int: val('dom_num_int'),
                    dom_colonia: val('dom_colonia'),
                    dom_cp: val('dom_cp'),
                    dom_municipio: val('dom_municipio'),
                    dom_estado: document.getElementById('dom_estado').value,
                    dom_pais: val('dom_pais'),
                    frecuencia: document.getElementById('h_frecuencia').value,
                    monto_orden: document.getElementById('h_monto').value,
                    id_tipo_precio: document.getElementById('h_tipo').value,
                    pan: document.getElementById('pan').value.replace(/\s/g, ''),
                    cardholderName: val('cardholderName'),
                    cvv2: val('cvv2'),
                    expDate: document.getElementById('expDate').value,
                };

                try {
                    const res = await axios.post('/adquirir/procesar', payload);
                    const d = res.data;
                    const fmt = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    }).format(d.monto);
                    const el = id => document.getElementById(id);

                    document.getElementById('panelProcesando').style.display = 'none';
                    document.getElementById('panelComp').style.display = 'block';

                    el('comp-folio').textContent = d.folio;
                    el('comp-auth').textContent = d.authnum;
                    el('comp-card').textContent = `${d.card?.brand} ···· ${d.card?.last4}`;
                    el('comp-vig').textContent = d.vigencia;
                    el('comp-monto').textContent = fmt;

                    // Redirigir a /pasaporte tras 3 segundos
                    setTimeout(() => {
                        window.location.href = d.redirect;
                    }, 3000);

                } catch (ex) {
                    step = 4;
                    syncUI();
                    document.getElementById('panelProcesando').style.display = 'none';

                    const msg = ex.response?.data?.error ?? 'Error al procesar el pago.';
                    const code = ex.response?.data?.code ?? '';

                    // Si el correo ya existe → ofrecer ir al login
                    if (code === 'EMAIL_EXISTS') {
                        err(
                        `${msg} <a href="/login" style="color:#2563eb;font-weight:600;">Ir al login</a>`);
                    } else {
                        err(`${msg}${code ? ` (${code})` : ''}`);
                    }
                }
            });

            syncUI();
            syncResumen();

        })();
    </script>
</body>

</html>
