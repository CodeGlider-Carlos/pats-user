{{-- resources/views/pats/pago_distribucion.blade.php --}}
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PATS · Distribución y pago</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ez.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/pago_distribucion.css') }}?v={{ time() }}">
</head>

<body>

    <div class="pd-wrap">

        {{-- ── Hero ─────────────────────────────────────────────── --}}
        <section class="pd-hero">
            <div class="pd-kicker">PATS · DISTRIBUCIÓN · ONBOARDING COMERCIAL</div>
            <h1 class="pd-title">Activa tu <span>distribución</span></h1>
            <p class="pd-subtitle">
                Completa tu registro comercial y continúa a la pasarela para activar tu distribución con PATS.
            </p>
            <div class="pd-chips">
                <span class="pd-chip">{{ $labelOrigen }}</span>
                <span class="pd-chip">{{ $nombreFranquicia ?: 'Franquicia no identificada' }}</span>
                <span class="pd-chip">{{ $estadoNombre }}{{ $zona ? ' · ' . $zona : '' }}</span>
            </div>
        </section>

        <div class="pd-divider"></div>

        {{-- ── Shell ──────────────────────────────────────────────── --}}
        <div class="pd-shell">

            {{-- Cabecera --}}
            <div class="pd-shell__head">
                <div class="pd-headline">Registro comercial y esquema de pago</div>
                <h2 class="pd-shell__title">Distribución PATS</h2>
                <p class="pd-shell__sub">{{ $descOrigen }}</p>
            </div>

            {{-- Steps nav --}}
            <div class="pd-steps-wrap">
                <div class="pd-steps" id="pdSteps">
                    @foreach ([1 => 'Acceso', 2 => 'General', 3 => 'Legal', 4 => 'Contacto', 5 => 'Finanzas', 6 => 'Confirmación'] as $n => $label)
                        <button type="button" class="pd-step {{ $n === 1 ? 'is-active' : '' }}"
                            data-step="{{ $n }}">
                            <span class="pd-step__num">{{ $n }}</span>
                            <span>{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Formulario principal --}}
            <form id="frmPagoDistribucion" class="pd-body" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Campos ocultos de contexto --}}
                <input type="hidden" name="public_token" value="{{ $token }}">
                <input type="hidden" name="actor_tipo_publico" value="{{ $actorTipo }}">
                <input type="hidden" name="id_distribuidor_origen" value="{{ (int) ($ctx['id_distribuidor'] ?? 0) }}">
                <input type="hidden" name="id_gestor_origen" value="{{ (int) ($ctx['id_gestor'] ?? 0) }}">
                <input type="hidden" name="id_franquicia" value="{{ (int) ($ctx['id_franquicia'] ?? 0) }}">
                <input type="hidden" name="public_checkout_token" value="{{ $ctx['public_checkout_token'] ?? '' }}">

                {{-- ── PASO 1: Acceso / Contexto ──────────────────────── --}}
                <section class="pd-panel is-active" data-step-panel="1">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Contexto del enlace</h3>
                        <div class="pd-note">
                            Este link público está vinculado a una estructura comercial válida dentro de PATS.
                            Revisa el contexto antes de continuar.
                        </div>

                        <div class="pd-identity-pills" style="margin-top:16px;">
                            <span class="pd-pill">Origen: {{ $labelOrigen }}</span>
                            <span class="pd-pill">Actor: {{ $nombreActor ?: '-' }}</span>
                            <span class="pd-pill">Franquicia: {{ $nombreFranquicia ?: '-' }}</span>
                            <span class="pd-pill">Región: {{ $estadoNombre ?: '-' }}</span>
                            <span class="pd-pill">Zona: {{ $zona ?: '-' }}</span>
                            <span class="pd-pill">Unidad: {{ $unidad ?: '-' }}</span>
                        </div>

                        <div class="pd-fields" style="margin-top:20px;">
                            <div class="pd-field">
                                <label>País</label>
                                <input type="text" name="pais" value="{{ $pais }}" readonly>
                            </div>
                            <div class="pd-field">
                                <label>Región</label>
                                <input type="text" name="region" value="{{ $region }}" readonly>
                            </div>
                            <div class="pd-field">
                                <label>Zona</label>
                                <input type="text" name="zona" value="{{ $zona }}" readonly>
                            </div>
                            <div class="pd-field">
                                <label>Unidad</label>
                                <input type="text" name="unidad" value="{{ $unidad }}" readonly>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── PASO 2: Información general ─────────────────────── --}}
                <section class="pd-panel" data-step-panel="2">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Información general del distribuidor</h3>

                        <div class="pd-fields">
                            <div class="pd-field">
                                <label for="nombre">Nombre del distribuidor</label>
                                <input type="text" id="nombre" name="nombre"
                                    placeholder="Nombre completo o comercial" required>
                            </div>
                            <div class="pd-field">
                                <label for="razon_social">Razón social</label>
                                <input type="text" id="razon_social" name="razon_social"
                                    placeholder="Razón social si aplica">
                            </div>
                            <div class="pd-field full">
                                <label for="direccion">Dirección</label>
                                <textarea id="direccion" name="direccion" placeholder="Dirección operativa o fiscal"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── PASO 3: Documentación legal ─────────────────────── --}}
                <section class="pd-panel" data-step-panel="3">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Documentación legal</h3>
                        <div class="pd-note">
                            Carga los documentos básicos para alta comercial. Puedes usar PDF o imagen.
                        </div>

                        <div class="pd-stack" style="margin-top:16px;">
                            @foreach (['doc_ine' => 'INE', 'doc_domicilio' => 'Comprobante de domicilio', 'doc_cedula' => 'Cédula fiscal'] as $fieldName => $docLabel)
                                <div class="pd-mini-card">
                                    <div class="pd-mini-card__head">{{ $docLabel }}</div>
                                    <div class="pd-mini-card__body">
                                        <label class="pd-file-input">
                                            <input type="file" id="{{ $fieldName }}"
                                                name="{{ $fieldName }}" accept=".pdf,.png,.jpg,.jpeg,.webp"
                                                required>
                                            <span class="pd-file-btn">Seleccionar archivo</span>
                                            <span class="pd-file-text">Ningún archivo seleccionado</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- ── PASO 4: Datos de contacto y bancarios ────────────── --}}
                <section class="pd-panel" data-step-panel="4">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Datos de contacto</h3>

                        <div class="pd-fields">
                            <div class="pd-field">
                                <label for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" placeholder="10 dígitos"
                                    maxlength="10" required>
                            </div>
                            <div class="pd-field">
                                <label for="correo">Correo</label>
                                <input type="email" id="correo" name="correo" placeholder="correo@dominio.com"
                                    required>
                            </div>
                            <div class="pd-field">
                                <label for="rfc">RFC</label>
                                <input type="text" id="rfc" name="rfc" placeholder="RFC">
                            </div>
                            <div class="pd-field">
                                <label for="clabe">CLABE</label>
                                <input type="text" id="clabe" name="clabe" placeholder="18 dígitos"
                                    maxlength="18">
                            </div>
                            <div class="pd-field">
                                <label for="banco">Banco</label>
                                <input type="text" id="banco" name="banco" placeholder="Banco">
                            </div>
                            <div class="pd-field">
                                <label for="numero_cuenta">Número de cuenta</label>
                                <input type="text" id="numero_cuenta" name="numero_cuenta"
                                    placeholder="Número de cuenta">
                            </div>
                            <div class="pd-field full">
                                <label for="titular_cuenta">Titular de la cuenta</label>
                                <input type="text" id="titular_cuenta" name="titular_cuenta"
                                    placeholder="Titular bancario">
                            </div>
                            <div class="pd-field full">
                                <label>Carátula bancaria</label>
                                <label class="pd-file-input">
                                    <input type="file" id="doc_caratula_bancaria" name="doc_caratula_bancaria"
                                        accept=".pdf,.png,.jpg,.jpeg,.webp" required>
                                    <span class="pd-file-btn">Seleccionar archivo</span>
                                    <span class="pd-file-text">Ningún archivo seleccionado</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── PASO 5: Condiciones financieras ─────────────────── --}}
                <section class="pd-panel" data-step-panel="5">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Condiciones financieras</h3>
                        <div class="pd-note">
                            El valor de distribución se configura según el catálogo vigente. Puedes liquidar de
                            contado o diferir según el esquema autorizado.
                        </div>

                        <div class="pd-fields pd-fields--2col" style="margin-top:16px;">
                            <div class="pd-field">
                                <label for="modalidad_pago">Modalidad de pago</label>
                                <select id="modalidad_pago" name="modalidad_pago" required>
                                    <option value="CONTADO">Contado</option>
                                    <option value="ENGANCHE_DIFERIDO">Enganche + diferido</option>
                                    <option value="DIFERIDO">Diferido</option>
                                </select>
                            </div>

                            <div class="pd-field">
                                <label for="valor_total">Valor total</label>
                                <input type="number" id="valor_total" name="valor_total"
                                    value="{{ number_format($precioDistribucion, 2, '.', '') }}" readonly>
                            </div>

                            <div class="pd-field">
                                <label for="enganche">Enganche</label>
                                <input type="number" id="enganche" name="enganche" value="0" min="0"
                                    step="0.01">
                            </div>

                            <div class="pd-field">
                                <label for="saldo_financiado">Saldo financiado</label>
                                <input type="number" id="saldo_financiado" name="saldo_financiado"
                                    value="{{ number_format($precioDistribucion, 2, '.', '') }}" readonly>
                            </div>

                            <div class="pd-field">
                                <label for="plazo_meses">Plazo (meses)</label>
                                <input type="number" id="plazo_meses" name="plazo_meses" value="0"
                                    min="0" step="1">
                            </div>

                            <div class="pd-field">
                                <label for="periodicidad">Periodicidad</label>
                                <select id="periodicidad" name="periodicidad">
                                    <option value="MENSUAL" selected>Mensual</option>
                                    <option value="QUINCENAL">Quincenal</option>
                                    <option value="SEMANAL">Semanal</option>
                                    <option value="UNICA">Única</option>
                                </select>
                            </div>

                            <div class="pd-field">
                                <label for="fecha_inicio">Fecha de inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                            </div>

                            <div class="pd-field">
                                <label for="fecha_primer_vencimiento">Primer vencimiento</label>
                                <input type="date" id="fecha_primer_vencimiento" name="fecha_primer_vencimiento">
                            </div>
                        </div>

                        {{-- Vista previa de parcialidades --}}
                        <div class="pd-block" id="planPreviewCard" style="margin-top:16px;" hidden>
                            <h3 class="pd-block__title">Vista previa de parcialidades</h3>
                            <div id="planPreviewBody"></div>
                        </div>
                    </div>
                </section>

                {{-- ── PASO 6: Confirmación final ───────────────────────── --}}
                <section class="pd-panel" data-step-panel="6">
                    <div class="pd-block">
                        <h3 class="pd-block__title">Confirmación final</h3>
                        <div class="pd-note">
                            Estás a punto de generar la orden de distribución y continuar a la pasarela de pago.
                            Verifica los datos comerciales y financieros antes de continuar.
                        </div>

                        <label class="pd-check" style="margin-top:20px;">
                            <input type="checkbox" id="acepta_confirmacion" name="acepta_confirmacion">
                            <span>Confirmo que la información capturada es correcta y autorizo la generación de la orden
                                de distribución.</span>
                        </label>

                        <div class="pd-block" style="margin-top:20px;">
                            <h3 class="pd-block__title">Resumen final</h3>
                            <div class="pd-summary">
                                <div class="pd-summary__item">
                                    <span>Franquicia</span>
                                    <strong id="sumFranquicia">{{ $nombreFranquicia ?: '-' }}</strong>
                                </div>
                                <div class="pd-summary__item">
                                    <span>Origen</span>
                                    <strong id="sumOrigen">{{ $labelOrigen }}</strong>
                                </div>
                                <div class="pd-summary__item"><span>Solicitante</span><strong
                                        id="sumNombre">-</strong></div>
                                <div class="pd-summary__item"><span>Correo</span> <strong id="sumCorreo">-</strong>
                                </div>
                                <div class="pd-summary__item"><span>Teléfono</span> <strong
                                        id="sumTelefono">-</strong></div>
                                <div class="pd-summary__item"><span>Modalidad</span> <strong
                                        id="sumModalidad">-</strong></div>
                                <div class="pd-summary__item">
                                    <span>Valor total</span>
                                    <strong id="sumValorTotal">${{ number_format($precioDistribucion, 2) }}</strong>
                                </div>
                                <div class="pd-summary__item">
                                    <span>Saldo financiado</span>
                                    <strong id="sumSaldo">${{ number_format($precioDistribucion, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── Barra de acciones ────────────────────────────────── --}}
                <div class="pd-actions">
                    <div>
                        <button type="button" class="pd-btn pd-btn--ghost" id="btnPrev">← Anterior</button>
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <button type="button" class="pd-btn pd-btn--soft" id="btnPreviewPlan" hidden>
                            Ver parcialidades
                        </button>
                        <button type="button" class="pd-btn pd-btn--soft" id="btnNext">Siguiente →</button>
                        <button type="submit" class="pd-btn pd-btn--primary" id="btnSubmit" hidden>
                            Continuar a pago →
                        </button>
                    </div>
                </div>

            </form>
        </div>{{-- /.pd-shell --}}
    </div>{{-- /.pd-wrap --}}

    {{-- ── Toast container ─────────────────────────────────────── --}}
    <div id="pdToastHost"></div>

    <script>
        (() => {
            "use strict";

            const $ = (s) => document.querySelector(s);
            const $$ = (s) => Array.from(document.querySelectorAll(s));

            // ── Estado del wizard ────────────────────────────────────────
            let currentStep = 1;
            const TOTAL = 6;

            // ── Helpers ──────────────────────────────────────────────────
            const parseNum = (v) => {
                const n = Number(v || 0);
                return Number.isFinite(n) ? n : 0;
            };
            const onlyDigits = (v) => String(v || '').replace(/\D+/g, '');
            const validPhone = (v) => onlyDigits(v).length === 10;
            const validEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || '').trim());

            const validClabe = (v) => {
                const c = onlyDigits(v);
                if (c.length !== 18) return false;
                const factors = [3, 7, 1];
                let sum = 0;
                for (let i = 0; i < 17; i++) sum += (Number(c[i]) * factors[i % 3]) % 10;
                return ((10 - (sum % 10)) % 10) === Number(c[17]);
            };

            const money = (v) => new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
                maximumFractionDigits: 2
            }).format(Number(v || 0));

            // ── Toast ────────────────────────────────────────────────────
            function toast(msg, type = 'info') {
                const host = $('#pdToastHost');
                const item = document.createElement('div');
                item.textContent = msg;
                Object.assign(item.style, {
                    minWidth: '220px',
                    maxWidth: '400px',
                    padding: '11px 15px',
                    borderRadius: '10px',
                    color: '#fff',
                    fontSize: '13px',
                    fontWeight: '600',
                    boxShadow: '0 8px 32px rgba(0,0,0,.35)',
                    border: '1px solid rgba(255,255,255,.08)',
                    backdropFilter: 'blur(10px)',
                    background: type === 'error' ?
                        'linear-gradient(135deg, rgba(55,16,24,.96), rgba(108,26,48,.96))' :
                        type === 'success' ?
                        'linear-gradient(135deg, rgba(10,40,34,.96), rgba(18,92,80,.96))' :
                        'linear-gradient(135deg, rgba(12,21,46,.96), rgba(36,28,74,.96))',
                    pointerEvents: 'auto',
                    transition: 'opacity .22s ease, transform .22s ease',
                });
                host.appendChild(item);
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-4px)';
                    setTimeout(() => item.remove(), 240);
                }, 2800);
            }

            // ── Wizard sync ──────────────────────────────────────────────
            function syncWizard() {
                $$('[data-step-panel]').forEach((p) => {
                    const active = Number(p.dataset.stepPanel) === currentStep;
                    p.classList.toggle('is-active', active);
                    p.hidden = !active;
                });
                $$('[data-step]').forEach((btn) => {
                    const s = Number(btn.dataset.step);
                    btn.classList.toggle('is-active', s === currentStep);
                    btn.classList.toggle('is-done', s < currentStep);
                });

                const prev = $('#btnPrev');
                const next = $('#btnNext');
                const submit = $('#btnSubmit');
                const preview = $('#btnPreviewPlan');

                if (prev) prev.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
                if (next) next.hidden = currentStep === TOTAL;
                if (submit) submit.hidden = currentStep !== TOTAL;
                if (preview) preview.hidden = currentStep !== 5;

                if (currentStep === TOTAL) syncSummary();
            }

            function goStep(step) {
                currentStep = Math.max(1, Math.min(TOTAL, step));
                syncWizard();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // ── Financiero ───────────────────────────────────────────────
            function syncSaldo() {
                const total = parseNum($('#valor_total')?.value);
                const enganche = parseNum($('#enganche')?.value);
                const saldo = Math.max(0, total - enganche);
                if ($('#saldo_financiado')) $('#saldo_financiado').value = saldo.toFixed(2);
                syncSummary();
            }

            function syncFinancialMode() {
                const modalidad = ($('#modalidad_pago')?.value || '').toUpperCase();
                const isContado = modalidad === 'CONTADO';
                const plazo = $('#plazo_meses');
                const period = $('#periodicidad');
                const primerV = $('#fecha_primer_vencimiento');

                if (isContado) {
                    if ($('#enganche')) $('#enganche').value = '0';
                    if (plazo) {
                        plazo.value = '0';
                        plazo.disabled = true;
                    }
                    if (period) period.disabled = true;
                    if (primerV) {
                        primerV.value = '';
                        primerV.disabled = true;
                    }
                } else {
                    if (plazo) plazo.disabled = false;
                    if (period) period.disabled = false;
                    if (primerV) primerV.disabled = false;
                }
                syncSaldo();
            }

            // ── Preview parcialidades ────────────────────────────────────
            function buildPlanPreview() {
                syncSaldo();
                const modalidad = ($('#modalidad_pago')?.value || 'CONTADO').toUpperCase();
                const saldo = parseNum($('#saldo_financiado')?.value);
                const plazo = parseInt($('#plazo_meses')?.value || '0', 10);
                const period = $('#periodicidad')?.value || 'MENSUAL';
                const primerV = $('#fecha_primer_vencimiento')?.value || $('#fecha_inicio')?.value;

                const wrap = $('#planPreviewBody');
                const card = $('#planPreviewCard');
                if (!wrap || !card) return;

                if (!primerV) {
                    wrap.innerHTML = `<div class="pd-note">Captura fecha de inicio o primer vencimiento.</div>`;
                    card.hidden = false;
                    return;
                }

                if (modalidad === 'CONTADO' || saldo <= 0 || plazo <= 0) {
                    wrap.innerHTML = `
        <div class="pd-summary">
          <div class="pd-summary__item"><span>Modalidad</span><strong>${modalidad}</strong></div>
          <div class="pd-summary__item"><span>Saldo financiado</span><strong>${money(saldo)}</strong></div>
          <div class="pd-summary__item"><span>Parcialidades</span><strong>No aplica</strong></div>
        </div>`;
                    card.hidden = false;
                    return;
                }

                const base = new Date(primerV + 'T00:00:00');
                const fechas = [];
                for (let i = 0; i < plazo; i++) {
                    const d = new Date(base);
                    if (period === 'SEMANAL') d.setDate(base.getDate() + i * 7);
                    else if (period === 'QUINCENAL') d.setDate(base.getDate() + i * 15);
                    else if (period === 'UNICA') d.setDate(base.getDate());
                    else d.setMonth(base.getMonth() + i);
                    fechas.push(
                        `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
                        );
                    if (period === 'UNICA') break;
                }

                const n = fechas.length || 1;
                const baseMonto = Math.floor((saldo / n) * 100) / 100;
                let acumulado = 0;
                let html = `<div class="pd-stack">`;
                fechas.forEach((fecha, idx) => {
                    let monto = baseMonto;
                    acumulado += monto;
                    if (idx === fechas.length - 1) monto += +(saldo - acumulado).toFixed(2);
                    html += `
        <div class="pd-mini-card">
          <div class="pd-mini-card__body" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <strong>Parcialidad ${idx + 1}</strong>
            <span style="color:var(--text-2)">${fecha}</span>
            <strong style="color:var(--jade)">${money(monto)}</strong>
          </div>
        </div>`;
                });
                html += `</div>`;
                wrap.innerHTML = html;
                card.hidden = false;
            }

            // ── Summary ──────────────────────────────────────────────────
            function syncSummary() {
                const nombre = ($('#nombre')?.value || '').trim();
                const correo = ($('#correo')?.value || '').trim();
                const telefono = ($('#telefono')?.value || '').trim();
                const modalidad = ($('#modalidad_pago')?.value || '').trim();
                const total = parseNum($('#valor_total')?.value);
                const saldo = parseNum($('#saldo_financiado')?.value);

                if ($('#sumNombre')) $('#sumNombre').textContent = nombre || '-';
                if ($('#sumCorreo')) $('#sumCorreo').textContent = correo || '-';
                if ($('#sumTelefono')) $('#sumTelefono').textContent = telefono || '-';
                if ($('#sumModalidad')) $('#sumModalidad').textContent = modalidad || '-';
                if ($('#sumValorTotal')) $('#sumValorTotal').textContent = money(total);
                if ($('#sumSaldo')) $('#sumSaldo').textContent = money(saldo);
            }

            // ── Validaciones por paso ─────────────────────────────────────
            const validators = {
                1: () => true,
                2: () => {
                    const n = ($('#nombre')?.value || '').trim();
                    if (!n) {
                        toast('Debes capturar el nombre del distribuidor.', 'error');
                        $('#nombre')?.focus();
                        return false;
                    }
                    return true;
                },
                3: () => {
                    for (const [id, label] of [
                            ['doc_ine', 'INE'],
                            ['doc_domicilio', 'comprobante de domicilio'],
                            ['doc_cedula', 'cédula fiscal']
                        ]) {
                        if (!$(`#${id}`)?.files?.length) {
                            toast(`Debes cargar el/la ${label}.`, 'error');
                            return false;
                        }
                    }
                    return true;
                },
                4: () => {
                    const tel = $('#telefono')?.value || '';
                    const correo = ($('#correo')?.value || '').trim();
                    const clabe = $('#clabe')?.value || '';
                    if (!validPhone(tel)) {
                        toast('El teléfono debe tener 10 dígitos.', 'error');
                        $('#telefono')?.focus();
                        return false;
                    }
                    if (!validEmail(correo)) {
                        toast('El correo no es válido.', 'error');
                        $('#correo')?.focus();
                        return false;
                    }
                    if (clabe && !validClabe(clabe)) {
                        toast('La CLABE no es válida.', 'error');
                        $('#clabe')?.focus();
                        return false;
                    }
                    if (!$('#doc_caratula_bancaria')?.files?.length) {
                        toast('Debes cargar la carátula bancaria.', 'error');
                        return false;
                    }
                    return true;
                },
                5: () => {
                    if (!$('#modalidad_pago')?.value) {
                        toast('Selecciona la modalidad de pago.', 'error');
                        return false;
                    }
                    if (!$('#fecha_inicio')?.value) {
                        toast('Debes capturar la fecha de inicio.', 'error');
                        $('#fecha_inicio')?.focus();
                        return false;
                    }
                    return true;
                },
                6: () => {
                    if (!$('#acepta_confirmacion')?.checked) {
                        toast('Debes confirmar la información para continuar.', 'error');
                        return false;
                    }
                    return true;
                },
            };

            // ── Bind file inputs ─────────────────────────────────────────
            function bindFileInputs() {
                $$('.pd-file-input input[type="file"]').forEach((input) => {
                    if (input.dataset.bound) return;
                    input.dataset.bound = '1';
                    input.addEventListener('change', () => {
                        const text = input.closest('.pd-file-input')?.querySelector('.pd-file-text');
                        if (!text) return;
                        text.textContent = input.files?.length ?
                            (input.files.length === 1 ? input.files[0].name :
                                `${input.files.length} archivos`) :
                            'Ningún archivo seleccionado';
                    });
                });
            }

            // ── Submit ───────────────────────────────────────────────────
            async function onSubmit(ev) {
                ev.preventDefault();
                syncSaldo();
                syncSummary();

                for (let i = 1; i <= TOTAL; i++) {
                    if (!validators[i]()) {
                        goStep(i);
                        return;
                    }
                }

                const btn = $('#btnSubmit');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Generando orden…';
                }

                try {
                    const res = await fetch("{{ route('pats.pago-distribucion.generar-orden') }}", {
                        method: 'POST',
                        body: new FormData($('#frmPagoDistribucion')),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    });

                    const text = await res.text();
                    let data = {};
                    try {
                        data = text ? JSON.parse(text) : {};
                    } catch {
                        toast('Respuesta inesperada del servidor. Revisa consola.', 'error');
                        console.error('RAW:', text);
                        return;
                    }

                    if (!res.ok || data.ok === false) {
                        toast(data.error || 'No fue posible generar la orden.', 'error');
                        return;
                    }

                    toast('Orden generada. Redirigiendo a pago…', 'success');
                    if (data.checkout_url) setTimeout(() => {
                        window.location.href = data.checkout_url;
                    }, 700);

                } catch (err) {
                    console.error(err);
                    toast('Error de red al generar la orden.', 'error');
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = 'Continuar a pago →';
                    }
                }
            }

            // ── Init ─────────────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', () => {
                bindFileInputs();
                syncFinancialMode();
                syncWizard();

                $('#btnPrev')?.addEventListener('click', () => {
                    if (currentStep > 1) goStep(currentStep - 1);
                });
                $('#btnNext')?.addEventListener('click', () => {
                    if (validators[currentStep]()) goStep(currentStep + 1);
                });
                $('#btnPreviewPlan')?.addEventListener('click', buildPlanPreview);
                $('#frmPagoDistribucion')?.addEventListener('submit', onSubmit);

                $$('[data-step]').forEach((btn) =>
                    btn.addEventListener('click', () => {
                        const t = Number(btn.dataset.step);
                        if (t < currentStep) goStep(t);
                    })
                );

                // Sync en tiempo real
                $('#telefono')?.addEventListener('input', (e) => {
                    e.target.value = onlyDigits(e.target.value).slice(0, 10);
                    syncSummary();
                });
                $('#clabe')?.addEventListener('input', (e) => {
                    e.target.value = onlyDigits(e.target.value).slice(0, 18);
                });
                ['nombre', 'correo'].forEach((id) => $(`#${id}`)?.addEventListener('input', syncSummary));
                $('#modalidad_pago')?.addEventListener('change', () => {
                    syncFinancialMode();
                    syncSummary();
                });
                $('#enganche')?.addEventListener('input', syncSaldo);
                ['plazo_meses', 'periodicidad', 'fecha_inicio', 'fecha_primer_vencimiento']
                .forEach((id) => $(`#${id}`)?.addEventListener('change', syncSummary));
            });
        })();
    </script>
</body>

</html>
