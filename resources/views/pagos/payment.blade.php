@extends('layouts.app')

@section('title', 'Mis pagos')

@section('content')

    <link rel="stylesheet" href="{{ asset('styles/payments.css') }}">

    <div class="pago-wrap">

        <div class="pago-header">
            <div>
                <h1 class="pago-title"><i class="mdi mdi-credit-card-outline"></i> Mis pagos</h1>
                <p class="pago-sub">Historial de pagos y renovación de pasaporte</p>
            </div>
        </div>

        {{-- Stats dinámicas --}}
        <div class="pago-stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="mdi mdi-receipt"></i></div>
                <div>
                    <div class="stat-val">{{ $totalPagos }}</div>
                    <div class="stat-lbl">Pagos realizados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="mdi mdi-cash"></i></div>
                <div>
                    <div class="stat-val">${{ number_format($totalPagado, 0) }}</div>
                    <div class="stat-lbl">Total pagado</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="mdi mdi-calendar-check"></i></div>
                <div>
                    <div class="stat-val">{{ $ultimoPago }}</div>
                    <div class="stat-lbl">Último pago</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="mdi mdi-shield-check"></i></div>
                <div>
                    @if ($pasaporte?->estatus === 'activo')
                        <div class="stat-val" style="color:#10b981;">Activa</div>
                    @elseif($pasaporte?->estatus === 'vencido')
                        <div class="stat-val" style="color:#dc2626;">Vencida</div>
                    @else
                        <div class="stat-val" style="color:#94a3b8;">Sin Pasaporte</div>
                    @endif
                    <div class="stat-lbl">Pasaporte</div>
                </div>
            </div>
        </div>

        {{-- ① Passport Card dinámica --}}
        @include('pagos._passport_card')

        {{-- ② Panel de pago --}}
        <div class="section-card">

            {{-- Selector de plan con meses, recargos y renovación --}}
            @include('pagos._plan_selector')

            {{-- Tabs de método de pago --}}
            <ul class="ptabs" id="methodTabs" style="margin-top:1.5rem;">
                <li><button class="ptab-btn active" data-target="tab-card">
                        <i class="mdi mdi-credit-card-outline"></i> Tarjeta
                    </button></li>
                <li><button class="ptab-btn" data-target="tab-token">
                        <i class="mdi mdi-wallet"></i> Tarjetas guardadas
                    </button></li>
                <li><button class="ptab-btn" data-target="tab-recurring">
                        <i class="mdi mdi-repeat"></i> Recurrente
                    </button></li>
                <li><button class="ptab-btn" data-target="tab-cash">
                        <i class="mdi mdi-store"></i> OXXO
                    </button></li>
            </ul>

            <div class="alert alert-success" id="alertSuccess">
                <i class="mdi mdi-check-circle"></i>
                <span id="alertSuccessMsg">Pago procesado correctamente.</span>
            </div>
            <div class="alert alert-danger" id="alertError">
                <i class="mdi mdi-alert-circle"></i>
                <span id="alertErrorMsg">Error al procesar el pago.</span>
            </div>

            {{-- TAB: Tarjeta --}}
            <div class="ptab-panel active" id="tab-card">
                <div class="row g-4 align-items-start">
                    <div class="col-md-5">
                        <div class="plastic-wrap" id="cardWrap">
                            <div class="plastic">
                                <div class="plastic-face plastic-front">
                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                        <div class="chip"></div>
                                        <span id="disp-brand"
                                            style="font-size:1.1rem;font-weight:700;letter-spacing:1px;">CARD</span>
                                    </div>
                                    <div class="cnum" id="disp-num">•••• •••• •••• ••••</div>
                                    <div class="cbot">
                                        <div><small>Titular</small>
                                            <span
                                                id="disp-name">{{ strtoupper(($pasaporte->nombres ?? ($user->nombre_usuario ?? 'NOMBRE')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}</span>
                                        </div>
                                        <div><small>Vence</small><span id="disp-exp">MM/AA</span></div>
                                    </div>
                                </div>
                                <div class="plastic-face plastic-back">
                                    <div class="mag"></div>
                                    <div class="cvv-box"><span id="disp-cvv">•••</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <form id="formCard" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label class="form-lbl">Nombre del titular</label>
                                <input class="form-ctrl" id="inp-name" type="text"
                                    value="{{ strtoupper(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}"
                                    placeholder="Como aparece en la tarjeta">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Número de tarjeta</label>
                                <input class="form-ctrl" id="inp-num" type="text" maxlength="19"
                                    placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="form-row mb-3">
                                <div>
                                    <label class="form-lbl">Vencimiento (MM/AA)</label>
                                    <input class="form-ctrl" id="inp-exp" type="text" maxlength="5"
                                        placeholder="MM/AA">
                                </div>
                                <div>
                                    <label class="form-lbl">CVV</label>
                                    <input class="form-ctrl" id="inp-cvv" type="password" maxlength="4"
                                        placeholder="•••">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label
                                    style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem;color:var(--text-muted);">
                                    <input type="checkbox" id="chk-save"> Guardar tarjeta para pagos futuros
                                </label>
                            </div>
                            <div class="mb-3" id="aliasRow" style="display:none;">
                                <label class="form-lbl">Alias (opcional)</label>
                                <input class="form-ctrl" id="inp-alias" type="text" placeholder="Ej: Mi Mastercard">
                            </div>
                            <button type="submit" class="btn btn-primary btn-w" id="btnPagarCard">
                                <i class="mdi mdi-lock"></i>
                                <span id="btnCardTxt">Pagar $800 MXN</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB: Tarjetas guardadas --}}
            <div class="ptab-panel" id="tab-token">
                <div id="tokenList">
                    <div style="text-align:center;padding:2rem;color:var(--text-muted);">
                        <div class="spin"
                            style="border-color:var(--blue);border-top-color:transparent;margin:0 auto 1rem;width:28px;height:28px;">
                        </div>
                        Cargando tarjetas...
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-lbl">CVV</label>
                    <input class="form-ctrl" id="inp-token-cvv" type="password" maxlength="4" placeholder="•••"
                        style="max-width:140px;">
                </div>
                <button class="btn btn-primary btn-w" id="btnPagarToken" disabled>
                    <i class="mdi mdi-lock"></i>
                    <span id="btnTokenTxt">Pagar</span>
                </button>
            </div>

            {{-- TAB: Recurrente --}}
            <div class="ptab-panel" id="tab-recurring">
                <div class="row g-4">
                    <div class="col-md-6">
                        <form id="formRecurring">
                            @csrf
                            <div class="mb-3">
                                <label class="form-lbl">Número de tarjeta</label>
                                <input class="form-ctrl" id="rec-num" type="text" maxlength="19"
                                    placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Nombre del titular</label>
                                <input class="form-ctrl" id="rec-name" type="text"
                                    value="{{ strtoupper(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}">
                            </div>
                            <div class="form-row mb-3">
                                <div>
                                    <label class="form-lbl">Vencimiento (MM/AA)</label>
                                    <input class="form-ctrl" id="rec-exp" type="text" maxlength="5"
                                        placeholder="MM/AA">
                                </div>
                                <div>
                                    <label class="form-lbl">CVV</label>
                                    <input class="form-ctrl" id="rec-cvv" type="password" maxlength="4">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Número de contrato</label>
                                <input class="form-ctrl" id="rec-contract" type="text"
                                    value="{{ $pasaporte ? 'PATS-' . str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) : '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">Correo para recibo</label>
                                <input class="form-ctrl" id="rec-email" type="email" value="{{ $user->correo_usuario }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-w" id="btnRecurring">
                                <i class="mdi mdi-repeat"></i>
                                <span id="btnRecurringTxt">Iniciar cobro recurrente $800 MXN</span>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div style="background:var(--navy);border-radius:12px;padding:1.25rem;">
                            <p style="font-size:.85rem;font-weight:600;color:var(--cream);margin:0 0 .75rem;">¿Cómo
                                funciona?</p>
                            <ul style="list-style:none;padding:0;margin:0;font-size:.83rem;color:var(--text-muted);">
                                <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                    <i class="mdi mdi-check" style="color:var(--success);"></i> Se registra tu tarjeta de
                                    forma segura</li>
                                <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                    <i class="mdi mdi-check" style="color:var(--success);"></i> El cobro se ejecuta
                                    automáticamente</li>
                                <li style="padding:.35rem 0;display:flex;gap:.5rem;"><i class="mdi mdi-check"
                                        style="color:var(--success);"></i> Puedes cancelar en cualquier momento</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: OXXO --}}
            <div class="ptab-panel" id="tab-cash">

                {{-- Formulario de datos --}}
                <div id="oxxo-form-wrap" style="max-width:460px;margin:0 auto;">
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
                        <div style="width:48px;height:48px;background:#e8423a;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="mdi mdi-store" style="color:#fff;font-size:1.5rem;"></i>
                        </div>
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--cream);">Pago en OXXO</div>
                            <div style="font-size:.8rem;color:var(--text-muted);">Genera tu ficha y paga en cualquier tienda OXXO</div>
                        </div>
                    </div>

                    <form id="formCash" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label class="form-lbl">Nombre del pagador</label>
                            <input class="form-ctrl" id="cash-name" type="text"
                                value="{{ trim(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-lbl">Correo para recibo</label>
                            <input class="form-ctrl" id="cash-email" type="email" value="{{ $user->correo_usuario }}">
                        </div>

                        <div style="background:var(--navy);border:1px solid var(--border);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:var(--text-muted);">
                            <div style="display:flex;gap:.5rem;align-items:flex-start;">
                                <i class="mdi mdi-information-outline" style="color:var(--blue);margin-top:.05rem;flex-shrink:0;"></i>
                                <span>La ficha tiene una vigencia de <strong>3 días</strong>. Una vez que pagues en tienda, la membresía se activa automáticamente.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-w" id="btnCash">
                            <i class="mdi mdi-receipt"></i>
                            <span id="btnCashTxt">Generar ficha OXXO</span>
                        </button>
                    </form>
                </div>

                {{-- Voucher (se muestra tras generar la ficha) --}}
                <div id="oxxo-voucher-wrap" style="display:none;max-width:480px;margin:0 auto;">
                    <div class="oxxo-voucher">
                        <div class="oxxo-voucher__header">
                            <div style="width:44px;height:44px;background:#e8423a;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="mdi mdi-store" style="color:#fff;font-size:1.4rem;"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:1rem;color:var(--cream);">Ficha de pago OXXO</div>
                                <div style="font-size:.78rem;color:var(--text-muted);">Presenta esta referencia en cualquier caja OXXO</div>
                            </div>
                        </div>

                        <div class="oxxo-voucher__amount">
                            <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:.2rem;">Monto a pagar</div>
                            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:700;color:var(--blue);" id="oxxo-amount-disp">—</div>
                        </div>

                        <div class="oxxo-voucher__ref-label">Número de referencia</div>
                        <div class="oxxo-voucher__reference" id="oxxo-reference">—</div>

                        <div id="oxxo-barcode-wrap" style="display:none;text-align:center;margin:1rem 0;">
                            <img id="oxxo-barcode-img" src="" alt="Código de barras OXXO" style="max-width:100%;height:60px;">
                        </div>

                        <div class="oxxo-voucher__steps">
                            <div class="oxxo-step"><span class="oxxo-step__num">1</span><span>Ve a cualquier tienda OXXO</span></div>
                            <div class="oxxo-step"><span class="oxxo-step__num">2</span><span>Indica al cajero que harás un pago de servicio</span></div>
                            <div class="oxxo-step"><span class="oxxo-step__num">3</span><span>Proporciona el número de referencia o el código de barras</span></div>
                            <div class="oxxo-step"><span class="oxxo-step__num">4</span><span>Paga en efectivo y conserva tu comprobante</span></div>
                        </div>

                        <div style="text-align:center;margin-top:1rem;display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
                            <a id="oxxo-redirect-btn" href="#" target="_blank" class="btn btn-outline btn-sm" style="display:none;">
                                <i class="mdi mdi-printer"></i> Imprimir voucher
                            </a>
                            <button class="btn btn-outline btn-sm" onclick="navigator.clipboard?.writeText(document.getElementById('oxxo-reference').textContent);this.textContent='¡Copiado!'">
                                <i class="mdi mdi-content-copy"></i> Copiar referencia
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal detalle --}}
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;border:none;box-shadow:var(--shadow-lg);">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:1.5rem;">
                    <h5 class="modal-title"
                        style="font-family:'Syne',sans-serif;color:var(--cream);display:flex;align-items:center;gap:.6rem;">
                        <i class="mdi mdi-file-document" style="color:var(--blue);"></i> Ficha de pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.5rem;" id="modalDetalleBody"></div>
                <div class="modal-footer"
                    style="background:var(--navy);border-top:1px solid var(--border);padding:1rem 1.5rem;">
                    <button class="btn btn-outline" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @include('prosa._threeds_js')

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_BASE = '/api/prosa';
        const CSRF = '{{ csrf_token() }}';
        const USER_EMAIL = '{{ $user->correo_usuario ?? '' }}';

        axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;
        axios.defaults.headers.common['Accept'] = 'application/json';

        let selectedToken = null;

        // ── Leer plan desde hiddens (lo setea _plan_selector.blade.php) ─
        function getPlan() {
            return {
                frecuencia: document.getElementById('h_frecuencia').value,
                monto: parseFloat(document.getElementById('h_monto').value),
                id_tipo_precio: parseInt(document.getElementById('h_id_tipo_precio').value),
                meses: parseInt(document.getElementById('h_meses').value),
                recargo: parseFloat(document.getElementById('h_recargo').value || 0),
                total: parseFloat(document.getElementById('h_monto_total').value),
            };
        }

        // ── Helpers ──────────────────────────────────────
        // Convierte "MM/AA" → { expMonth: 'MM', expYear: 'AA' }
        function splitExp(mmaa) {
            const clean = (mmaa || '').replace(/\D/g, '');
            return {
                expMonth: clean.substring(0, 2),
                expYear: clean.substring(2, 4),
            };
        }

        function showSuccess(msg) {
            const el = document.getElementById('alertSuccess');
            document.getElementById('alertSuccessMsg').textContent = msg;
            el.classList.add('show');
            document.getElementById('alertError').classList.remove('show');
            setTimeout(() => el.classList.remove('show'), 6000);
        }

        function showError(msg) {
            const el = document.getElementById('alertError');
            document.getElementById('alertErrorMsg').textContent = msg;
            el.classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');
            setTimeout(() => el.classList.remove('show'), 7000);
        }

        function setLoading(btnId, loading, txt) {
            const btn = document.getElementById(btnId);
            if (!btn) return;
            btn.disabled = loading;
            btn.innerHTML = loading ? `<span class="spin"></span> Procesando...` : `<i class="mdi mdi-lock"></i> ${txt}`;
        }

        // ── Tabs método de pago ──────────────────────────
        document.querySelectorAll('.ptab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.ptab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.ptab-panel').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.target).classList.add('active');
                if (btn.dataset.target === 'tab-token') cargarTokens();
            });
        });

        // ── Tarjeta animada ──────────────────────────────
        document.getElementById('cardWrap').addEventListener('click', () =>
            document.getElementById('cardWrap').classList.toggle('flipped'));
        document.getElementById('inp-name')?.addEventListener('input', e =>
            document.getElementById('disp-name').textContent = e.target.value.toUpperCase() || 'NOMBRE APELLIDO');
        document.getElementById('inp-exp')?.addEventListener('input', e =>
            document.getElementById('disp-exp').textContent = e.target.value || 'MM/AA');
        document.getElementById('inp-cvv')?.addEventListener('input', e =>
            document.getElementById('disp-cvv').textContent = e.target.value.replace(/./g, '•') || '•••');
        document.getElementById('inp-num')?.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 16);
            e.target.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
            document.getElementById('disp-num').textContent = v.replace(/(\d{4})(?=\d)/g, '$1 ') ||
                '•••• •••• •••• ••••';
            const brand = v[0] === '4' ? 'VISA' : (v[0] === '5' || v[0] === '2') ? 'MASTERCARD' :
                (v.startsWith('34') || v.startsWith('37')) ? 'AMEX' : 'CARD';
            document.getElementById('disp-brand').textContent = brand;
        });
        document.getElementById('chk-save')?.addEventListener('change', e =>
            document.getElementById('aliasRow').style.display = e.target.checked ? 'block' : 'none');

        // ── Venta directa ────────────────────────────────
        document.getElementById('formCard')?.addEventListener('submit', async e => {
            e.preventDefault();
            const plan = getPlan();
            const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
            setLoading('btnPagarCard', true);

            const pan = document.getElementById('inp-num').value.replace(/\s/g, '');
            const exp = splitExp(document.getElementById('inp-exp').value);
            const saveCard = document.getElementById('chk-save').checked;
            const alias = document.getElementById('inp-alias')?.value || null;

            try {
                const res = await axios.post(`${API_BASE}/sale/one-step`, {
                    amount: plan.total,
                    pan,
                    cardholderName: document.getElementById('inp-name').value,
                    cvv2: document.getElementById('inp-cvv').value,
                    expMonth: exp.expMonth,
                    expYear: exp.expYear,
                    email: USER_EMAIL,
                    browser: window.prosaBrowserData(),
                    saveCard,   // si true, OPPWA tokeniza la tarjeta junto con el cobro
                    alias,
                });

                // Reto 3DS: redirigir al banco emisor.
                if (res.data.status === 'challenge') {
                    showSuccess('Redirigiendo a la autenticación de tu banco...');
                    window.prosaHandleChallenge(res.data.challenge);
                    return;
                }

                if (res.data.status === 'approved') {
                    const msg = saveCard
                        ? `✓ Pago de $${fmt} MXN aprobado y tarjeta guardada.`
                        : `✓ Pago de $${fmt} MXN aprobado.`;
                    showSuccess(msg);
                    setTimeout(() => window.location.href = res.data.redirectUrl || window.location.href, 1500);
                }
            } catch (err) {
                const msg = err.response?.data?.error ?? 'Error al procesar el pago';
                const code = err.response?.data?.code ?? '';
                showError(`${msg}${code ? ` (${code})` : ''}`);
            } finally {
                setLoading('btnPagarCard', false, `Pagar $${fmt} MXN`);
            }
        });

        // ── Mensajes de retorno 3DS (?pago=ok|error) ─────
        (function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get('pago') === 'ok') showSuccess('✓ Pago procesado correctamente.');
            else if (params.get('pago') === 'error') showError('El pago no pudo completarse o fue rechazado.');
        })();

        // ── Cargar tokens ────────────────────────────────
        async function cargarTokens() {
            const container = document.getElementById('tokenList');
            container.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--text-muted);">
        <div class="spin" style="border-color:var(--blue);border-top-color:transparent;margin:0 auto 1rem;width:28px;height:28px;"></div>
        Cargando tarjetas...</div>`;
            try {
                const res = await axios.get(`${API_BASE}/token/cards`);
                const cards = res.data.cards ?? [];
                if (!cards.length) {
                    container.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--text-muted);">
                <i class="mdi mdi-credit-card-off" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:var(--border);"></i>
                No tienes tarjetas guardadas.</div>`;
                    return;
                }
                container.innerHTML = `<div class="token-grid" id="tokenGrid"></div>`;
                const grid = document.getElementById('tokenGrid');
                cards.forEach(card => {
                    const div = document.createElement('div');
                    div.className = 'token-card';
                    div.dataset.tokenId = card.id;
                    div.innerHTML = `
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.25rem;">
                    <span class="token-brand">${card.brand ?? 'Tarjeta'}</span>
                    ${card.isDefault ? '<span class="token-default">Default</span>' : ''}
                </div>
                ${card.alias ? `<div class="token-alias">${card.alias}</div>` : ''}
                <div class="token-num">•••• ${card.last4 ?? '????'}</div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.45rem;">
                    <span class="token-exp">${card.expDate ?? '—'}</span>
                    <div style="display:flex;gap:.3rem;">
                        ${!card.isDefault ? `<button class="btn btn-outline btn-sm token-action-btn" title="Marcar como default" onclick="event.stopPropagation();setDefault(${card.id},event)"><i class="mdi mdi-star-outline"></i></button>` : ''}
                        <button class="btn btn-danger btn-sm token-action-btn" title="Eliminar" onclick="event.stopPropagation();eliminarToken(${card.id},event)"><i class="mdi mdi-delete"></i></button>
                    </div>
                </div>`;
                    div.addEventListener('click', () => {
                        document.querySelectorAll('.token-card').forEach(c => c.classList.remove('selected'));
                        div.classList.add('selected');
                        selectedToken = parseInt(div.dataset.tokenId);
                        document.getElementById('btnPagarToken').disabled = false;
                    });
                    grid.appendChild(div);
                });
            } catch {
                container.innerHTML =
                    `<div class="alert alert-danger show"><i class="mdi mdi-alert"></i> Error al cargar tarjetas.</div>`;
            }
        }

        // ── Pago con token ───────────────────────────────
        document.getElementById('btnPagarToken')?.addEventListener('click', async () => {
            // Read from DOM as source of truth in case the JS variable drifted.
            const selectedCard = document.querySelector('.token-card.selected');
            if (selectedCard) selectedToken = parseInt(selectedCard.dataset.tokenId);
            if (!selectedToken) return;

            const plan = getPlan();
            const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
            const btn = document.getElementById('btnPagarToken');
            const btnTxt = document.getElementById('btnTokenTxt');
            btn.disabled = true;
            if (btnTxt) btnTxt.textContent = 'Procesando...';
            try {
                const res = await axios.post(`${API_BASE}/token/sale`, {
                    tokenId: selectedToken,
                    amount: plan.total,
                    cvv2: document.getElementById('inp-token-cvv').value,
                });
                if (res.data.success) {
                    showSuccess(`✓ Pago de $${fmt} MXN aprobado. Ref: ${res.data.authnum}`);
                    selectedToken = null;
                    document.querySelectorAll('.token-card').forEach(c => c.classList.remove('selected'));
                    document.getElementById('inp-token-cvv').value = '';
                    setTimeout(() => window.location.reload(), 2500);
                    return;
                }
            } catch (err) {
                const data = err.response?.data ?? {};
                showError(data.error ?? 'Error al procesar el pago');
                if (data.cardRemoved) {
                    selectedToken = null;
                    setTimeout(() => cargarTokens(), 1500);
                }
            }
            btn.disabled = false;
            if (btnTxt) btnTxt.textContent = `Pagar $${fmt} MXN`;
        });

        // ── Recurrente ───────────────────────────────────
        document.getElementById('formRecurring')?.addEventListener('submit', async e => {
            e.preventDefault();
            const plan = getPlan();
            const fmt  = new Intl.NumberFormat('es-MX').format(plan.total);
            const exp  = splitExp(document.getElementById('rec-exp').value);
            setLoading('btnRecurring', true);
            try {
                const res = await axios.post(`${API_BASE}/sale/recurring`, {
                    amount:          plan.total,
                    pan:             document.getElementById('rec-num').value.replace(/\s/g, ''),
                    cardholderName:  document.getElementById('rec-name').value,
                    cvv2:            document.getElementById('rec-cvv').value,
                    expMonth:        exp.expMonth,
                    expYear:         exp.expYear,
                    email:           USER_EMAIL,
                    browser:         window.prosaBrowserData(),
                    // Plan context needed to save subscription + update pasaporte.
                    frecuencia:      plan.frecuencia,
                    meses:           plan.meses,
                    id_tipo_precio:  plan.id_tipo_precio,
                    monto_membresia: plan.monto,
                    recargo:         plan.recargo,
                });

                if (res.data.status === 'challenge') {
                    showSuccess('Redirigiendo a la autenticación de tu banco...');
                    window.prosaHandleChallenge(res.data.challenge);
                    return;
                }

                if (res.data.status === 'approved') {
                    showSuccess(`✓ Suscripción recurrente activada. Los cobros futuros son automáticos.`);
                    document.getElementById('formRecurring').reset();
                    setTimeout(() => window.location.reload(), 2500);
                }
            } catch (err) {
                showError(err.response?.data?.error ?? 'Error al configurar el cobro recurrente');
            } finally {
                setLoading('btnRecurring', false, `Iniciar cobro recurrente $${fmt} MXN`);
            }
        });

        // ── OXXO ─────────────────────────────────────────
        document.getElementById('formCash')?.addEventListener('submit', async e => {
            e.preventDefault();
            const plan = getPlan();
            const fmt  = new Intl.NumberFormat('es-MX').format(plan.total);
            setLoading('btnCash', true);
            try {
                const res = await axios.post(`${API_BASE}/sale/oxxo`, {
                    amount:          plan.total,
                    email:           document.getElementById('cash-email').value,
                    name:            document.getElementById('cash-name').value,
                    frecuencia:      plan.frecuencia,
                    meses:           plan.meses,
                    id_tipo_precio:  plan.id_tipo_precio,
                    monto_membresia: plan.monto,
                    recargo:         plan.recargo,
                });

                if (res.data.success) {
                    // Mostrar voucher, ocultar formulario.
                    document.getElementById('oxxo-form-wrap').style.display  = 'none';
                    document.getElementById('oxxo-voucher-wrap').style.display = 'block';
                    document.getElementById('oxxo-amount-disp').textContent  = `$${fmt} MXN`;
                    document.getElementById('oxxo-reference').textContent    = res.data.reference ?? res.data.paymentId;

                    if (res.data.redirectUrl) {
                        const btn = document.getElementById('oxxo-redirect-btn');
                        btn.href = res.data.redirectUrl;
                        btn.style.display = 'inline-flex';
                    }
                }
            } catch (err) {
                showError(err.response?.data?.error ?? 'Error al generar la ficha OXXO');
                setLoading('btnCash', false, 'Generar ficha OXXO');
            }
        });

        // ── Gestión tokens ───────────────────────────────
        async function eliminarToken(id, event) {
            event.stopPropagation();
            if (!confirm('¿Eliminar esta tarjeta?')) return;
            try {
                await axios.delete(`${API_BASE}/token/${id}`);
                cargarTokens();
            } catch {
                showError('No se pudo eliminar la tarjeta');
            }
        }
        async function setDefault(id, event) {
            event.stopPropagation();
            try {
                await axios.patch(`${API_BASE}/token/${id}/default`, {});
                cargarTokens();
            } catch {
                showError('No se pudo actualizar la tarjeta');
            }
        }

        // ── Modal detalle ────────────────────────────────
        function verDetalle(pago) {
            document.getElementById('modalDetalleBody').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Folio</small>
                <div style="font-weight:600;color:var(--cream);">${pago.folio}</div></div>
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Fecha</small>
                <div>${pago.fecha}</div></div>
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Monto</small>
                <div style="font-size:1.1rem;font-weight:700;color:var(--blue);">$${Number(pago.monto).toLocaleString('es-MX',{minimumFractionDigits:2})} MXN</div></div>
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Método</small>
                <div>${pago.metodo}</div></div>
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Producto</small>
                <div>${pago.producto}</div></div>
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Proveedor</small>
                <div>${pago.proveedor ?? 'PROSA'}</div></div>
            ${pago.authnum ? `<div style="grid-column:1/-1;"><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Autorización</small>
                    <div style="font-family:monospace;font-size:1rem;">${pago.authnum}</div></div>` : ''}
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Estatus</small>
                <span class="badge badge-success"><i class="mdi mdi-check-circle"></i> ${pago.estatus}</span></div>
        </div>`;
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        }
    </script>

@endsection
