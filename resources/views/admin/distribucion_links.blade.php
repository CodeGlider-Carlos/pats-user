{{-- resources/views/admin/distribucion_links.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links de Distribución · PATS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy:   #0d1b3e; --navy-2: #162550;
            --blue:   #2563eb; --blue-l: #3b82f6; --blue-50: #eff4ff;
            --cyan:   #06b6d4; --cyan-l: #22d3ee;
            --surface:#fff;    --page:   #f0f5ff;
            --border: rgba(37,99,235,.13);
            --s4: #94a3b8; --s5: #64748b; --s6: #475569; --s7: #334155; --s8: #1e293b;
            --success:#10b981; --danger:#ef4444; --warning:#f59e0b;
            --font: 'Plus Jakarta Sans', system-ui, sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --r: 14px; --r-lg: 20px;
        }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font); background: var(--page); color: var(--s8); min-height: 100vh; -webkit-font-smoothing: antialiased; }

        /* ── Top bar ── */
        .topbar { background: var(--navy); padding: 0 32px; height: 58px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,.06); position: sticky; top: 0; z-index: 100; }
        .topbar__logo { font-size: 17px; font-weight: 800; color: #fff; letter-spacing: -.02em; }
        .topbar__sep  { width: 1px; height: 20px; background: rgba(255,255,255,.15); }
        .topbar__page { font-size: 13px; color: rgba(255,255,255,.6); font-weight: 500; }

        /* ── Layout ── */
        .page { max-width: 1060px; margin: 0 auto; padding: 32px 20px 60px; }

        /* ── Section title ── */
        .sec-title { font-size: 20px; font-weight: 800; color: var(--navy); margin-bottom: 4px; }
        .sec-sub   { font-size: 13px; color: var(--s5); margin-bottom: 24px; }

        /* ── Card ── */
        .card {
            background: var(--surface); border-radius: var(--r-lg);
            box-shadow: 0 0 0 1px var(--border), 0 8px 28px rgba(13,27,62,.08);
            overflow: hidden; margin-bottom: 32px;
        }
        .card__head {
            padding: 20px 28px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .card__head-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--blue-50); color: var(--blue); display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
        .card__head-title { font-size: 15px; font-weight: 700; color: var(--s8); }
        .card__head-sub   { font-size: 12px; color: var(--s5); margin-top: 1px; }
        .card__body { padding: 28px; }

        /* ── Grid ── */
        .grid { display: grid; gap: 18px; }
        .grid-2 { grid-template-columns: 1fr 1fr; }
        .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        .grid-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
        @media (max-width: 700px) { .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; } }

        /* ── Field ── */
        .field { display: flex; flex-direction: column; gap: 6px; }
        .label { font-size: 12.5px; font-weight: 700; color: var(--s7); }
        .label span { color: var(--danger); margin-left: 2px; }
        .input, .select {
            height: 42px; padding: 0 14px;
            border: 1.5px solid var(--border); border-radius: var(--r);
            font-family: var(--font); font-size: 14px; color: var(--s8);
            background: #f8faff; outline: none;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
        }
        .input:focus, .select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); background: #fff; }
        .input--mono { font-family: var(--mono); }
        .input-hint { font-size: 11.5px; color: var(--s4); }

        /* ── Section divider inside form ── */
        .form-section { margin: 24px 0 0; padding-top: 22px; border-top: 1px solid var(--border); }
        .form-section__title {
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--s4); margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .form-section__title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        /* ── Toggle switch ── */
        .toggle-group { display: flex; gap: 8px; }
        .toggle-opt { position: relative; }
        .toggle-opt input { position: absolute; opacity: 0; width: 0; }
        .toggle-opt label {
            display: flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: var(--r);
            border: 1.5px solid var(--border);
            font-size: 13px; font-weight: 600; color: var(--s6);
            cursor: pointer; background: #f8faff;
            transition: all .15s;
        }
        .toggle-opt input:checked + label { background: var(--blue); border-color: var(--blue); color: #fff; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; border-radius: var(--r); font-family: var(--font); font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: opacity .15s, transform .1s; }
        .btn:active { transform: scale(.98); }
        .btn--primary { background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%); color: #fff; }
        .btn--primary:hover { opacity: .9; }
        .btn--ghost { background: transparent; border: 1.5px solid var(--border); color: var(--s6); }
        .btn--ghost:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-50); }
        .btn--danger { background: #fff1f2; border: 1.5px solid rgba(239,68,68,.25); color: var(--danger); }
        .btn--danger:hover { background: var(--danger); color: #fff; }
        .btn--sm { padding: 7px 14px; font-size: 12.5px; }

        /* ── Alert ── */
        .alert { display: flex; align-items: flex-start; gap: 12px; padding: 16px 20px; border-radius: var(--r); margin-bottom: 24px; font-size: 13.5px; }
        .alert--success { background: #ecfdf5; border: 1.5px solid rgba(16,185,129,.25); color: #065f46; }
        .alert--error   { background: #fff1f2; border: 1.5px solid rgba(239,68,68,.25); color: #991b1b; }
        .alert i { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
        .alert__url {
            margin-top: 10px; padding: 10px 14px;
            background: rgba(255,255,255,.7); border-radius: 8px;
            font-family: var(--mono); font-size: 13px; word-break: break-all;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .alert__url a { color: #065f46; text-decoration: none; flex: 1; }
        .alert__url a:hover { text-decoration: underline; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--s5); background: #f8faff; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8faff; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 100px; font-size: 11.5px; font-weight: 700; }
        .badge--active   { background: #ecfdf5; color: #065f46; border: 1px solid rgba(16,185,129,.3); }
        .badge--used     { background: #f0f5ff; color: #1e40af; border: 1px solid rgba(37,99,235,.25); }
        .badge--inactive { background: #f1f5f9; color: var(--s5); border: 1px solid rgba(148,163,184,.3); }
        .badge--card     { background: #eff4ff; color: #3730a3; border: 1px solid rgba(99,102,241,.25); }
        .badge--free     { background: #ecfdf5; color: #065f46; border: 1px solid rgba(16,185,129,.25); }

        .token-cell { font-family: var(--mono); font-size: 12px; color: var(--s5); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .amount-cell { font-family: var(--mono); font-weight: 700; color: var(--navy); }

        .copy-btn { background: none; border: none; cursor: pointer; color: var(--s4); font-size: 16px; padding: 4px; border-radius: 6px; transition: color .15s; }
        .copy-btn:hover { color: var(--blue); }

        .prefill-pill { display: inline-flex; align-items: center; gap: 4px; background: rgba(6,182,212,.1); color: #0e7490; border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 700; }

        .empty-state { padding: 48px 20px; text-align: center; color: var(--s4); }
        .empty-state i { font-size: 40px; display: block; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        /* ── Password reveal ── */
        .pw-wrap { position: relative; }
        .pw-wrap .input { padding-right: 42px; }
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--s4); font-size: 18px; padding: 4px; }
        .pw-toggle:hover { color: var(--blue); }

        /* ── Error ── */
        .field-error { font-size: 12px; color: var(--danger); font-weight: 600; }

        /* ── Collapsible prefill ── */
        .collapse-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; font-size: 13px; font-weight: 700; color: var(--blue); background: none; border: none; padding: 0; font-family: var(--font); }
        .collapse-toggle i { transition: transform .2s; }
        .collapse-toggle.open i.mdi-chevron-right { transform: rotate(90deg); }
        .collapse-body { display: none; margin-top: 18px; }
        .collapse-body.open { display: block; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar__logo"><i class="mdi mdi-handshake-outline" style="margin-right:6px"></i>PATS</div>
    <div class="topbar__sep"></div>
    <div class="topbar__page">Admin · Links de Distribución</div>
</div>

<div class="page">

    {{-- Alert éxito ─────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert--success">
        <i class="mdi mdi-check-circle-outline"></i>
        <div>
            <div style="font-weight:700;">{{ session('success') }}</div>
            @if(session('new_token'))
            <div class="alert__url">
                <a id="newLinkUrl" href="{{ $appUrl }}/distribucion/link/{{ session('new_token') }}" target="_blank">
                    {{ $appUrl }}/distribucion/link/{{ session('new_token') }}
                </a>
                <button class="copy-btn" onclick="copyUrl()" title="Copiar">
                    <i class="mdi mdi-content-copy"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert--error">
        <i class="mdi mdi-alert-circle-outline"></i>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    {{-- ──────────────────────────────────────────────────────────────── --}}
    {{-- FORMULARIO                                                       --}}
    {{-- ──────────────────────────────────────────────────────────────── --}}
    <div class="sec-title">Crear nuevo link</div>
    <p class="sec-sub">Genera un enlace protegido con contraseña para que el distribuidor complete su solicitud.</p>

    <div class="card">
        <div class="card__head">
            <div class="card__head-icon"><i class="mdi mdi-link-variant-plus"></i></div>
            <div>
                <div class="card__head-title">Configuración del link</div>
                <div class="card__head-sub">Define el monto, tipo de pago y contraseña de acceso.</div>
            </div>
        </div>
        <div class="card__body">

            <form method="POST" action="{{ route('admin.dist-links.store') }}" id="frmCreate">
                @csrf

                {{-- Configuración base ──────────────────────────────── --}}
                <div class="grid grid-3" style="margin-bottom:18px;">
                    <div class="field">
                        <label class="label" for="amount">Monto <span>*</span></label>
                        <input class="input input--mono" type="number" id="amount" name="amount"
                               min="0" step="0.01" placeholder="20000.00"
                               value="{{ old('amount', '20000') }}" required>
                        <span class="input-hint">MXN · Solo relevante si type_pay = card</span>
                    </div>
                    <div class="field">
                        <label class="label">Tipo de pago <span>*</span></label>
                        <div class="toggle-group" style="margin-top:4px;">
                            <div class="toggle-opt">
                                <input type="radio" name="type_pay" id="tp_card" value="card"
                                       {{ old('type_pay','card') === 'card' ? 'checked' : '' }}>
                                <label for="tp_card"><i class="mdi mdi-credit-card-outline"></i> Con cobro</label>
                            </div>
                            <div class="toggle-opt">
                                <input type="radio" name="type_pay" id="tp_free" value="free"
                                       {{ old('type_pay') === 'free' ? 'checked' : '' }}>
                                <label for="tp_free"><i class="mdi mdi-check-circle-outline"></i> Sin cobro</label>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="id_franquicia">ID Franquicia</label>
                        <input class="input input--mono" type="number" id="id_franquicia" name="id_franquicia"
                               min="0" placeholder="0" value="{{ old('id_franquicia', '0') }}">
                        <span class="input-hint">0 = sin franquicia asignada</span>
                    </div>
                </div>

                <div class="field" style="max-width:360px; margin-bottom:24px;">
                    <label class="label" for="password">Contraseña de acceso <span>*</span></label>
                    <div class="pw-wrap">
                        <input class="input" type="password" id="password" name="password"
                               placeholder="Contraseña que recibirá el distribuidor"
                               value="{{ old('password') }}" required autocomplete="new-password">
                        <button type="button" class="pw-toggle" id="pwToggle" tabindex="-1">
                            <i class="mdi mdi-eye-outline" id="pwIcon"></i>
                        </button>
                    </div>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Datos precargados (colapsable) ─────────────────────── --}}
                <div class="form-section">
                    <button type="button" class="collapse-toggle" id="prefillToggle">
                        <i class="mdi mdi-chevron-right"></i>
                        Datos precargados <span style="font-size:11px;font-weight:500;color:var(--s4);margin-left:4px;">(opcional — si los tienes, el distribuidor solo sube docs y firma)</span>
                    </button>

                    <div class="collapse-body" id="prefillBody">

                        <div class="form-section__title">Datos de identidad</div>
                        <div class="grid grid-3" style="margin-bottom:18px;">
                            <div class="field">
                                <label class="label" for="p_nombre">Nombre(s)</label>
                                <input class="input" type="text" id="p_nombre" name="prefill_nombre" value="{{ old('prefill_nombre') }}" placeholder="Carlos">
                            </div>
                            <div class="field">
                                <label class="label" for="p_apPa">Apellido paterno</label>
                                <input class="input" type="text" id="p_apPa" name="prefill_apellido_paterno" value="{{ old('prefill_apellido_paterno') }}" placeholder="González">
                            </div>
                            <div class="field">
                                <label class="label" for="p_apMa">Apellido materno</label>
                                <input class="input" type="text" id="p_apMa" name="prefill_apellido_materno" value="{{ old('prefill_apellido_materno') }}" placeholder="López">
                            </div>
                            <div class="field">
                                <label class="label" for="p_correo">Correo</label>
                                <input class="input" type="email" id="p_correo" name="prefill_correo" value="{{ old('prefill_correo') }}" placeholder="correo@ejemplo.com">
                            </div>
                            <div class="field">
                                <label class="label" for="p_tel">Teléfono</label>
                                <input class="input input--mono" type="text" id="p_tel" name="prefill_telefono" value="{{ old('prefill_telefono') }}" placeholder="3312345678" maxlength="10">
                            </div>
                            <div class="field">
                                <label class="label" for="p_tipo_persona">Tipo persona</label>
                                <select class="select" id="p_tipo_persona" name="prefill_tipo_persona">
                                    <option value="">— No especificado —</option>
                                    <option value="FISICA"  {{ old('prefill_tipo_persona') === 'FISICA'  ? 'selected' : '' }}>Física</option>
                                    <option value="MORAL"   {{ old('prefill_tipo_persona') === 'MORAL'   ? 'selected' : '' }}>Moral</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="p_fecNac">Fecha nacimiento</label>
                                <input class="input" type="date" id="p_fecNac" name="prefill_fecha_nacimiento" value="{{ old('prefill_fecha_nacimiento') }}">
                            </div>
                            <div class="field">
                                <label class="label" for="p_paisNac">País nacimiento</label>
                                <input class="input" type="text" id="p_paisNac" name="prefill_pais_nacimiento" value="{{ old('prefill_pais_nacimiento', 'México') }}" placeholder="México">
                            </div>
                            <div class="field">
                                <label class="label" for="p_nac">Nacionalidad</label>
                                <input class="input" type="text" id="p_nac" name="prefill_nacionalidad" value="{{ old('prefill_nacionalidad', 'Mexicana') }}" placeholder="Mexicana">
                            </div>
                            <div class="field">
                                <label class="label" for="p_ocup">Ocupación</label>
                                <input class="input" type="text" id="p_ocup" name="prefill_ocupacion" value="{{ old('prefill_ocupacion') }}" placeholder="Empresario">
                            </div>
                            <div class="field">
                                <label class="label" for="p_rfc">RFC</label>
                                <input class="input input--mono" type="text" id="p_rfc" name="prefill_rfc" value="{{ old('prefill_rfc') }}" placeholder="XXXX000000XXX" maxlength="13" style="text-transform:uppercase">
                            </div>
                        </div>

                        <div class="form-section__title">Identificación oficial</div>
                        <div class="grid grid-3" style="margin-bottom:18px;">
                            <div class="field">
                                <label class="label" for="p_tipoId">Tipo de identificación</label>
                                <select class="select" id="p_tipoId" name="prefill_tipo_identificacion">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(['INE','Pasaporte','Cédula profesional','Licencia de conducir'] as $id)
                                        <option value="{{ $id }}" {{ old('prefill_tipo_identificacion') === $id ? 'selected' : '' }}>{{ $id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="p_idEmitida">Emitida por</label>
                                <input class="input" type="text" id="p_idEmitida" name="prefill_identificacion_emitida_por" value="{{ old('prefill_identificacion_emitida_por', 'INE') }}" placeholder="INE">
                            </div>
                            <div class="field">
                                <label class="label" for="p_numId">Número de identificación</label>
                                <input class="input input--mono" type="text" id="p_numId" name="prefill_numero_identificacion" value="{{ old('prefill_numero_identificacion') }}" placeholder="1234567890">
                            </div>
                        </div>

                        <div class="form-section__title">Domicilio</div>
                        <div class="grid grid-4" style="margin-bottom:4px;">
                            <div class="field">
                                <label class="label" for="p_pais">País</label>
                                <select class="select" id="p_pais" name="prefill_pais">
                                    <option value="">— No especificado —</option>
                                    <option value="MX" {{ old('prefill_pais', 'MX') === 'MX' ? 'selected' : '' }}>México</option>
                                    <option value="US" {{ old('prefill_pais') === 'US' ? 'selected' : '' }}>Estados Unidos</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="p_region">Estado</label>
                                <select class="select" id="p_region" name="prefill_region">
                                    <option value="">— Estado —</option>
                                    @foreach($estados as $acr => $nombre)
                                        <option value="{{ $acr }}" {{ old('prefill_region') === $acr ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="p_mun">Municipio</label>
                                <input class="input" type="text" id="p_mun" name="prefill_municipio" value="{{ old('prefill_municipio') }}" placeholder="Guadalajara">
                            </div>
                            <div class="field">
                                <label class="label" for="p_ciudad">Ciudad</label>
                                <input class="input" type="text" id="p_ciudad" name="prefill_ciudad" value="{{ old('prefill_ciudad') }}" placeholder="Guadalajara">
                            </div>
                        </div>
                        <div class="grid grid-4" style="margin-bottom:4px;">
                            <div class="field" style="grid-column:span 2;">
                                <label class="label" for="p_calle">Calle</label>
                                <input class="input" type="text" id="p_calle" name="prefill_calle" value="{{ old('prefill_calle') }}" placeholder="Av. Independencia">
                            </div>
                            <div class="field">
                                <label class="label" for="p_ext">Núm. ext.</label>
                                <input class="input" type="text" id="p_ext" name="prefill_num_ext" value="{{ old('prefill_num_ext') }}" placeholder="100">
                            </div>
                            <div class="field">
                                <label class="label" for="p_int">Núm. int.</label>
                                <input class="input" type="text" id="p_int" name="prefill_num_int" value="{{ old('prefill_num_int') }}" placeholder="—">
                            </div>
                        </div>
                        <div class="grid grid-3">
                            <div class="field">
                                <label class="label" for="p_cp">C.P.</label>
                                <input class="input input--mono" type="text" id="p_cp" name="prefill_cp" value="{{ old('prefill_cp') }}" placeholder="44100" maxlength="5">
                            </div>
                            <div class="field" style="grid-column:span 2;">
                                <label class="label" for="p_colonia">Colonia</label>
                                <input class="input" type="text" id="p_colonia" name="prefill_colonia" value="{{ old('prefill_colonia') }}" placeholder="Centro Histórico">
                            </div>
                        </div>

                    </div>{{-- /collapse-body --}}
                </div>

                <div style="margin-top:28px; display:flex; gap:12px; align-items:center;">
                    <button type="submit" class="btn btn--primary">
                        <i class="mdi mdi-link-variant-plus"></i> Generar link
                    </button>
                    <button type="reset" class="btn btn--ghost">
                        <i class="mdi mdi-refresh"></i> Limpiar
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────── --}}
    {{-- LISTA DE LINKS                                                   --}}
    {{-- ──────────────────────────────────────────────────────────────── --}}
    <div class="sec-title" style="margin-top:8px;">Links generados</div>
    <p class="sec-sub">{{ $links->count() }} link(s) registrados.</p>

    <div class="card">
        <div class="card__body" style="padding:0;">
            @if($links->isEmpty())
                <div class="empty-state">
                    <i class="mdi mdi-link-off"></i>
                    <p>No hay links generados aún.</p>
                </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Token</th>
                            <th>Monto</th>
                            <th>Pago</th>
                            <th>Prefill</th>
                            <th>Estado</th>
                            <th>Solicitud</th>
                            <th>Creado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($links as $lnk)
                        <tr>
                            <td style="color:var(--s4);font-size:12px;">{{ $lnk->id }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span class="token-cell" title="{{ $lnk->token }}">{{ $lnk->token }}</span>
                                    <button class="copy-btn" onclick="copyText('{{ $appUrl }}/distribucion/link/{{ $lnk->token }}')" title="Copiar URL">
                                        <i class="mdi mdi-content-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="amount-cell">${{ number_format($lnk->amount, 0, '.', ',') }}</td>
                            <td>
                                @if($lnk->type_pay === 'free')
                                    <span class="badge badge--free"><i class="mdi mdi-check-circle-outline"></i> Sin cobro</span>
                                @else
                                    <span class="badge badge--card"><i class="mdi mdi-credit-card-outline"></i> Card</span>
                                @endif
                            </td>
                            <td>
                                @if($lnk->prefill_json)
                                    @php $pf = json_decode($lnk->prefill_json, true) ?? []; @endphp
                                    <span class="prefill-pill"><i class="mdi mdi-lightning-bolt"></i> {{ count($pf) }} campos</span>
                                @else
                                    <span style="color:var(--s4);font-size:12px;">—</span>
                                @endif
                            </td>
                            <td>
                                @if(!$lnk->active)
                                    <span class="badge badge--inactive"><i class="mdi mdi-lock-outline"></i> Inactivo</span>
                                @elseif($lnk->id_solicitud > 0)
                                    <span class="badge badge--used"><i class="mdi mdi-check-circle"></i> Usado</span>
                                @else
                                    <span class="badge badge--active"><i class="mdi mdi-circle-slice-8"></i> Activo</span>
                                @endif
                            </td>
                            <td style="font-family:var(--mono);font-size:12px;color:var(--s5);">
                                {{ $lnk->id_solicitud > 0 ? '#'.$lnk->id_solicitud : '—' }}
                            </td>
                            <td style="font-size:12px;color:var(--s4);white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($lnk->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($lnk->active && $lnk->id_solicitud == 0)
                                <form method="POST" action="{{ route('admin.dist-links.destroy', $lnk->id) }}"
                                      onsubmit="return confirm('¿Eliminar este link?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn--danger btn--sm">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>{{-- /page --}}

<script>
    // Toggle contraseña
    document.getElementById('pwToggle')?.addEventListener('click', function () {
        const inp  = document.getElementById('password');
        const icon = document.getElementById('pwIcon');
        if (inp.type === 'password') { inp.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
        else { inp.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
    });

    // Collapse prefill
    const prefillToggle = document.getElementById('prefillToggle');
    const prefillBody   = document.getElementById('prefillBody');
    prefillToggle?.addEventListener('click', () => {
        const open = prefillBody.classList.toggle('open');
        prefillToggle.classList.toggle('open', open);
    });

    // Si había old() en campos prefill, abrir collapse
    @if(old('prefill_nombre') || old('prefill_apellido_paterno') || old('prefill_correo') || old('prefill_calle'))
    prefillBody?.classList.add('open');
    prefillToggle?.classList.add('open');
    @endif

    // RFC uppercase
    document.getElementById('p_rfc')?.addEventListener('input', e => {
        e.target.value = e.target.value.toUpperCase();
    });

    // Teléfono: solo dígitos
    document.getElementById('p_tel')?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
    });

    // Copiar URL
    function copyUrl() {
        const url = document.getElementById('newLinkUrl')?.href;
        if (url) copyText(url);
    }
    function copyText(text) {
        navigator.clipboard?.writeText(text).then(() => {
            showToast('URL copiada al portapapeles');
        }).catch(() => {
            // Fallback
            const ta = document.createElement('textarea');
            ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast('URL copiada');
        });
    }

    let toastTimer;
    function showToast(msg) {
        let t = document.getElementById('_toast');
        if (!t) {
            t = document.createElement('div');
            t.id = '_toast';
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;z-index:9999;transition:opacity .3s;font-family:var(--font)';
            document.body.appendChild(t);
        }
        t.textContent = msg; t.style.opacity = '1';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { t.style.opacity = '0'; }, 2200);
    }
</script>
</body>
</html>
