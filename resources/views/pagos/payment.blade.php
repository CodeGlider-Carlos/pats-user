@extends('layouts.app')

@section('title', 'Mis pagos')

@section('content')

    <link rel="stylesheet" href="{{ asset('styles/payments.css') }}">
    <style>
        @keyframes toastIn {
            from { opacity:0; transform:translateY(12px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes psOverlayIn {
            from { opacity:0; }
            to   { opacity:1; }
        }
        @keyframes psBannerIn {
            from { opacity:0; transform:translateY(-28px) scale(.95); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }
        @keyframes psCheckPop {
            0%   { transform:scale(0) rotate(-30deg); opacity:0; }
            65%  { transform:scale(1.18) rotate(6deg); opacity:1; }
            100% { transform:scale(1) rotate(0); opacity:1; }
        }
        @keyframes psRingPulse {
            0%,100% { box-shadow:0 0 0 0 rgba(22,163,74,.35); }
            50%      { box-shadow:0 0 0 18px rgba(22,163,74,0); }
        }
        #ps-overlay {
            position:fixed;inset:0;z-index:10000;
            background:rgba(10,20,10,.62);
            backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);
            display:flex;align-items:center;justify-content:center;
            animation:psOverlayIn .25s ease;
            padding:1rem;
        }
        #ps-banner {
            background:#fff;border-radius:24px;
            max-width:480px;width:100%;
            padding:2.8rem 2.4rem 2.4rem;
            box-shadow:0 24px 60px rgba(0,0,0,.28);
            animation:psBannerIn .35s cubic-bezier(.22,1,.36,1);
            text-align:center;position:relative;
        }
        #ps-banner .ps-ring {
            width:90px;height:90px;border-radius:50%;
            background:linear-gradient(135deg,#16a34a,#22c55e);
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 1.6rem;
            animation:psCheckPop .5s cubic-bezier(.22,1,.36,1) .1s both,
                       psRingPulse 2s ease-in-out 1s infinite;
        }
        #ps-banner .ps-ring i { color:#fff;font-size:2.8rem;line-height:1; }
        #ps-banner .ps-title {
            font-size:1.6rem;font-weight:800;
            color:#15803d;margin:0 0 .6rem;letter-spacing:-.02em;
        }
        #ps-banner .ps-msg {
            font-size:1rem;color:#374151;line-height:1.6;
            margin:0 0 2rem;
        }
        #ps-banner .ps-close-btn {
            display:inline-flex;align-items:center;gap:.5rem;
            background:linear-gradient(135deg,#16a34a,#22c55e);
            color:#fff;border:none;border-radius:12px;
            padding:.85rem 2.4rem;font-size:1rem;font-weight:700;
            cursor:pointer;transition:transform .15s,box-shadow .15s;
            box-shadow:0 4px 14px rgba(22,163,74,.4);
        }
        #ps-banner .ps-close-btn:hover {
            transform:translateY(-2px);
            box-shadow:0 8px 20px rgba(22,163,74,.5);
        }
        #ps-banner .ps-x {
            position:absolute;top:1rem;right:1rem;
            background:none;border:none;cursor:pointer;
            color:#9ca3af;font-size:1.4rem;line-height:1;
            padding:.25rem;border-radius:50%;
            transition:color .15s,background .15s;
        }
        #ps-banner .ps-x:hover { color:#374151;background:#f3f4f6; }
        #ps-banner .ps-confetti {
            position:absolute;top:0;left:0;width:100%;height:100%;
            pointer-events:none;border-radius:24px;overflow:hidden;
        }
    </style>

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
                    @php
                        $fvr = $pasaporte?->fecha_vencimiento_real ?? $pasaporte?->vigencia ?? null;
                        $estaVigente = $fvr && \Carbon\Carbon::parse($fvr)->gte(\Carbon\Carbon::now());
                    @endphp
                    @if($pasaporte && $estaVigente)
                        <div class="stat-val" style="color:#10b981;">Activo</div>
                    @elseif($pasaporte)
                        <div class="stat-val" style="color:#dc2626;">No activo</div>
                    @else
                        <div class="stat-val" style="color:#94a3b8;">Sin Pasaporte</div>
                    @endif
                    <div class="stat-lbl">Pasaporte</div>
                </div>
            </div>
        </div>

        {{-- ① Passport Card dinámica --}}
        @include('pagos._passport_card')

        {{-- ② Panel de pago (solo para clientes NO empresa) --}}
        @if (strtoupper($pasaporte?->tipo_cliente ?? '') === 'EMPRESA')
            <div class="section-card" style="text-align:center;padding:2.5rem 1.5rem;">
                <i class="mdi mdi-office-building-outline" style="font-size:3rem;color:#94a3b8;display:block;margin-bottom:1rem;"></i>
                <h3 style="font-size:1.1rem;color:#1e3a5f;margin-bottom:.5rem;">Pasaporte corporativo</h3>
                <p style="color:#64748b;font-size:.9rem;max-width:400px;margin:0 auto;">
                    Tu pasaporte está vinculado a un plan empresarial.
                    Los pagos y renovaciones son gestionados por tu empresa.
                    Contacta a tu área de RH o a tu representante PATS.
                </p>
            </div>
        @else
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
                <li><button class="ptab-btn" data-target="tab-recurring" id="tabBtnRecurrente">
                        <i class="mdi mdi-repeat"></i> Recurrente
                        <span id="badgeRecurrente" style="display:none;background:#10b981;color:#fff;font-size:.6rem;font-weight:700;padding:.1rem .35rem;border-radius:4px;vertical-align:middle;margin-left:.25rem;">ACTIVO</span>
                    </button></li>
                <li><button class="ptab-btn" data-target="tab-cash">
                        <i class="mdi mdi-store"></i> OXXO
                    </button></li>
                <li><button class="ptab-btn" data-target="tab-historial">
                        <i class="mdi mdi-history"></i> Historial
                        @if($totalPagos > 0)
                            <span style="background:#1e3a5f;color:#fff;font-size:.6rem;font-weight:700;padding:.1rem .35rem;border-radius:4px;vertical-align:middle;margin-left:.25rem;">{{ $totalPagos }}</span>
                        @endif
                    </button></li>
            </ul>

            {{-- Toast container (global) --}}
            <div id="toast-container" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:.6rem;pointer-events:none;"></div>

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

                            {{-- Dirección de facturación (requerida por 3-D Secure) --}}
                            <div class="mb-3">
                                <label class="form-lbl">Dirección (calle y número)</label>
                                <input class="form-ctrl" id="bill-street" type="text" placeholder="Calle y número">
                            </div>
                            <div class="form-row mb-3">
                                <div>
                                    <label class="form-lbl">Ciudad</label>
                                    <input class="form-ctrl" id="bill-city" type="text" placeholder="Ciudad">
                                </div>
                                <div>
                                    <label class="form-lbl">Código postal</label>
                                    <input class="form-ctrl" id="bill-postcode" type="text" maxlength="10" placeholder="C.P.">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-lbl">País</label>
                                <input class="form-ctrl" id="bill-country" type="text" maxlength="2" value="MX"
                                    placeholder="MX">
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
                <div id="tokenList"></div>
                <div class="mb-3">
                    <label class="form-lbl">CVV</label>
                    <input class="form-ctrl" id="inp-token-cvv" type="password" maxlength="4" placeholder="•••" style="max-width:140px;">
                </div>
                <button class="btn btn-primary btn-w" id="btnPagarToken" disabled>
                    <i class="mdi mdi-lock"></i>
                    <span id="btnTokenTxt">Selecciona una tarjeta</span>
                </button>
            </div>

            {{-- TAB: Recurrente --}}
            <div class="ptab-panel" id="tab-recurring">

                {{-- === Stripe: recurrente === --}}
                <div id="stripe-recurring-section">

                    {{-- ── Estado de renovación automática ── --}}
                    <div id="rec-estado-wrap" style="margin-bottom:1.25rem;">
                        <div id="rec-estado-loading" style="font-size:.84rem;color:var(--text-muted);padding:.6rem 0;">
                            <span class="spin" style="width:14px;height:14px;border-width:2px;margin-right:.4rem;vertical-align:middle;display:inline-block;"></span>
                            Verificando estado de renovación...
                        </div>

                        {{-- Con tarjeta guardada → renovación activa --}}
                        <div id="rec-activo-box" style="display:none;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:1rem 1.1rem;margin-bottom:1rem;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                                <div>
                                    <div style="font-weight:700;color:#15803d;font-size:.9rem;margin-bottom:.35rem;">
                                        <i class="mdi mdi-check-circle"></i> Renovación automática activa
                                    </div>
                                    <div id="rec-tarjeta-info" style="font-size:.82rem;color:var(--text-muted);"></div>
                                </div>
                                <button type="button" id="btnCancelarRecurrente"
                                    style="border:1.5px solid #dc2626;background:transparent;color:#dc2626;border-radius:8px;padding:.45rem .9rem;font-size:.82rem;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap;flex-shrink:0;">
                                    <i class="mdi mdi-cancel"></i>
                                    <span id="btnCancelarRecurrenteTxt">Cancelar renovación</span>
                                </button>
                            </div>
                            <div id="rec-cancel-msg" style="font-size:.8rem;margin-top:.6rem;min-height:1em;"></div>
                        </div>

                        {{-- Sin tarjeta → no hay renovación --}}
                        <div id="rec-inactivo-box" style="display:none;background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.83rem;color:var(--text-muted);">
                            <i class="mdi mdi-information-outline"></i>
                            No tienes renovación automática configurada. Ingresa tu tarjeta para activarla.
                        </div>
                    </div>

                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1.5rem;font-size:.84rem;color:#1e40af;">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Cobro recurrente:</strong> Guardamos tu tarjeta de forma segura y renovamos tu membresía automáticamente en cada ciclo.
                    </div>
                    <div class="row g-4">
                        <div class="col-md-7">
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

                                {{-- Dirección de facturación (requerida por 3-D Secure) --}}
                                <div class="mb-3">
                                    <label class="form-lbl">Dirección (calle y número)</label>
                                    <input class="form-ctrl" id="rec-bill-street" type="text" placeholder="Calle y número">
                                </div>
                                <div class="form-row mb-3">
                                    <div>
                                        <label class="form-lbl">Ciudad</label>
                                        <input class="form-ctrl" id="rec-bill-city" type="text" placeholder="Ciudad">
                                    </div>
                                    <div>
                                        <label class="form-lbl">Código postal</label>
                                        <input class="form-ctrl" id="rec-bill-postcode" type="text" maxlength="10"
                                            placeholder="C.P.">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-lbl">País</label>
                                    <input class="form-ctrl" id="rec-bill-country" type="text" maxlength="2" value="MX"
                                        placeholder="MX">
                                </div>

                                <button type="submit" class="btn btn-primary btn-w" id="btnRecurring">
                                    <i class="mdi mdi-repeat"></i>
                                    <span id="btnRecurringTxt">Iniciar cobro recurrente $800 MXN</span>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-5">
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

            {{-- TAB: Historial --}}
            <div class="ptab-panel" id="tab-historial">
                @if ($pagos->isEmpty())
                    <div style="text-align:center;padding:2.5rem;color:#64748b;">
                        <i class="mdi mdi-receipt-text-outline" style="font-size:3rem;display:block;margin-bottom:.75rem;color:#cbd5e1;"></i>
                        <p>Aún no tienes pagos registrados.</p>
                    </div>
                @else
                    <div style="margin-bottom:1rem;font-size:.82rem;color:var(--text-muted);">
                        {{ $totalPagos }} pago(s) · Total pagado: <strong style="color:var(--cream);">${{ number_format($totalPagado, 2) }} MXN</strong>
                    </div>
                    {{-- Desktop --}}
                    <div class="d-sm-none">
                        <table class="htable">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Producto</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Estatus</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pagos as $p)
                                    <tr>
                                        <td><strong>{{ $p['folio'] }}</strong></td>
                                        <td>{{ $p['producto'] }}</td>
                                        <td>{{ $p['fecha'] }}</td>
                                        <td><strong>${{ number_format($p['monto'], 2) }}</strong></td>
                                        <td>
                                            @php
                                                $metodoIcon = match(strtolower($p['metodo'] ?? '')) {
                                                    'oxxo'   => 'mdi-store',
                                                    'tarjeta', 'stripe' => 'mdi-credit-card-outline',
                                                    default  => 'mdi-cash',
                                                };
                                            @endphp
                                            <i class="mdi {{ $metodoIcon }}"></i> {{ $p['metodo'] }}
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($p['estatus']) {
                                                    'Pagado'   => 'success',
                                                    'Pendiente', 'Pendiente Oxxo', 'Pendiente Validacion' => 'warning',
                                                    default    => 'danger',
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ $p['estatus'] }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline btn-sm" onclick="verDetalle({{ json_encode($p) }})">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Móvil --}}
                    <div class="d-sm-block">
                        @foreach ($pagos as $p)
                            <div class="mob-card">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.5rem;">
                                    <span style="font-size:.78rem;color:var(--text-muted);">{{ $p['fecha'] }}</span>
                                    @php
                                        $bc = match($p['estatus']) {
                                            'Pagado' => 'success',
                                            'Pendiente', 'Pendiente Oxxo', 'Pendiente Validacion' => 'warning',
                                            default  => 'danger',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $bc }}">{{ $p['estatus'] }}</span>
                                </div>
                                <div style="font-weight:600;color:var(--cream);margin-bottom:.25rem;">{{ $p['producto'] }}</div>
                                <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.75rem;">
                                    {{ $p['folio'] }}@if(!empty($p['authnum'])) · Auth: {{ $p['authnum'] }}@endif
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <strong style="color:var(--blue);">${{ number_format($p['monto'], 2) }} MXN</strong>
                                    <button class="btn btn-outline btn-sm" onclick="verDetalle({{ json_encode($p) }})">Ver</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
        @endif {{-- fin @else empresa --}}

    </div>

    {{-- ══ Modal vencimiento ══ --}}
    @php
        // fecha_vencimiento_real es la fuente de verdad (datetime fin de dia)
        $vigRaw  = $pasaporte?->fecha_vencimiento_real ?? $pasaporte?->vigencia ?? null;
        $vigDate = $vigRaw ? \Carbon\Carbon::parse($vigRaw)->startOfDay() : null;
        $hoy     = \Carbon\Carbon::now()->startOfDay();
        $diasRestantes = $vigDate ? (int)$hoy->diffInDays($vigDate, false) : null;
        $pasaporteVencido   = $diasRestantes !== null && $diasRestantes < 0;
        $pasaportePorVencer = $diasRestantes !== null && $diasRestantes >= 0 && $diasRestantes <= 30;
        $mostrarModalVenc   = ($pasaporteVencido || $pasaportePorVencer) && $pasaporte;
        $vigFormato = $vigDate ? $vigDate->format('d/m/Y') : '—';
    @endphp
    @if($mostrarModalVenc)
    <div class="modal fade" id="modalVencimiento" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 25px 60px rgba(0,0,0,.35);overflow:hidden;background:#fff;">

                {{-- Franja superior de color --}}
                <div style="height:5px;background:{{ $pasaporteVencido ? '#dc2626' : '#f59e0b' }};"></div>

                {{-- Cuerpo blanco --}}
                <div style="padding:2rem 1.75rem 1.75rem;background:#fff;text-align:center;">

                    {{-- Icono en círculo --}}
                    <div style="width:68px;height:68px;border-radius:50%;background:{{ $pasaporteVencido ? '#fef2f2' : '#fffbeb' }};display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="mdi {{ $pasaporteVencido ? 'mdi-shield-off-outline' : 'mdi-clock-alert-outline' }}"
                           style="font-size:2rem;color:{{ $pasaporteVencido ? '#dc2626' : '#f59e0b' }};"></i>
                    </div>

                    {{-- Título --}}
                    <h5 style="font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:700;color:#0f172a;margin:0 0 .4rem;">
                        {{ $pasaporteVencido ? '¡Tu pasaporte ha expirado!' : 'Tu pasaporte vence pronto' }}
                    </h5>
                    <p style="font-size:.85rem;color:#64748b;margin:0 0 1.25rem;line-height:1.55;">
                        @if($pasaporteVencido)
                            Renueva ahora para recuperar el acceso a todos tus beneficios PATS.
                        @else
                            Vence en <strong style="color:#0f172a;">{{ $diasRestantes }} {{ $diasRestantes === 1 ? 'día' : 'días' }}</strong>. Renueva antes de perder tus beneficios.
                        @endif
                    </p>

                    {{-- Tarjeta de fechas --}}
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:.9rem 1.25rem;margin-bottom:1.5rem;display:flex;justify-content:space-around;align-items:center;">
                        <div>
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#94a3b8;letter-spacing:.05em;margin-bottom:.3rem;">Vigencia</div>
                            <div style="font-size:.95rem;font-weight:700;color:#0f172a;">{{ $vigFormato }}</div>
                        </div>
                        <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                        <div>
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#94a3b8;letter-spacing:.05em;margin-bottom:.3rem;">
                                {{ $pasaporteVencido ? 'Venció hace' : 'Vence en' }}
                            </div>
                            <div style="font-size:1.1rem;font-weight:700;color:{{ $pasaporteVencido ? '#dc2626' : '#f59e0b' }};">
                                {{ abs($diasRestantes) }} {{ abs($diasRestantes) === 1 ? 'día' : 'días' }}
                            </div>
                        </div>
                        @php
                            $mesesVenc = $pasaporteVencido && $pasaporte->fecha_vencimiento_real
                                ? (int) \Carbon\Carbon::parse($pasaporte->fecha_vencimiento_real)->startOfDay()->diffInMonths(\Carbon\Carbon::now()->startOfDay())
                                : 0;
                        @endphp
                        @if($pasaporteVencido && $mesesVenc > 0)
                        <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                        <div>
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#94a3b8;letter-spacing:.05em;margin-bottom:.3rem;">Con recargo</div>
                            <div style="font-size:.95rem;font-weight:700;color:#dc2626;">{{ $mesesVenc }} mes(es)</div>
                        </div>
                        @endif
                    </div>

                    {{-- Botón principal --}}
                    <button type="button" data-bs-dismiss="modal" data-renovar-tab="tab-card"
                        style="width:100%;padding:.9rem 1rem;border-radius:12px;border:none;background:{{ $pasaporteVencido ? '#dc2626' : '#2563eb' }};color:#fff;font-size:.95rem;font-weight:700;cursor:pointer;font-family:inherit;margin-bottom:.6rem;display:flex;align-items:center;justify-content:center;gap:.5rem;">
                        <i class="mdi mdi-credit-card-outline"></i> Renovar ahora
                    </button>

                    {{-- Botón secundario --}}
                    <button type="button" data-bs-dismiss="modal"
                        style="width:100%;padding:.65rem 1rem;border-radius:12px;border:1.5px solid #e2e8f0;background:transparent;color:#64748b;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;">
                        Recordármelo después
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
        // Recolecta la dirección de facturación (3-D Secure) de los inputs dados.
        function billingFrom(streetId, cityId, postcodeId, countryId) {
            const g = id => (document.getElementById(id)?.value || '').trim();
            return {
                street1: g(streetId),
                city: g(cityId),
                postcode: g(postcodeId),
                country: (g(countryId) || 'MX').toUpperCase(),
            };
        }

        // Convierte "MM/AA" → { expMonth: 'MM', expYear: 'AA' }
        function splitExp(mmaa) {
            const clean = (mmaa || '').replace(/\D/g, '');
            return {
                expMonth: clean.substring(0, 2),
                expYear: clean.substring(2, 4),
            };
        }

        function showToast(msg, type) {
            if (!msg) return;
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const isSuccess = type === 'success';
            toast.style.cssText = `
                pointer-events:auto;display:flex;align-items:flex-start;gap:.65rem;
                background:${isSuccess ? '#f0fdf4' : '#fef2f2'};
                border:1.5px solid ${isSuccess ? '#86efac' : '#fca5a5'};
                color:${isSuccess ? '#15803d' : '#991b1b'};
                border-radius:12px;padding:.85rem 1rem;min-width:280px;max-width:360px;
                box-shadow:0 8px 24px rgba(0,0,0,.12);
                animation:toastIn .25s ease;font-size:.85rem;font-family:inherit;
                opacity:1;transition:opacity .3s ease;
            `;
            toast.innerHTML = `
                <i class="mdi ${isSuccess ? 'mdi-check-circle' : 'mdi-alert-circle'}" style="font-size:1.2rem;margin-top:.05rem;flex-shrink:0;"></i>
                <span style="flex:1;line-height:1.45;">${msg}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;opacity:.6;padding:0;font-size:1rem;line-height:1;flex-shrink:0;">✕</button>
            `;
            container.appendChild(toast);
            const delay = isSuccess ? 5000 : 7000;
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, delay);
        }

        function showSuccess(msg) { showToast(msg, 'success'); }
        function showError(msg)   { if (msg) showToast(msg, 'error'); }

        function showPaymentSuccessBanner(msg) {
            const overlay = document.createElement('div');
            overlay.id = 'ps-overlay';
            overlay.innerHTML = `
                <div id="ps-banner">
                    <button class="ps-x" onclick="document.getElementById('ps-overlay').remove()" title="Cerrar">✕</button>
                    <div class="ps-ring"><i class="mdi mdi-check-bold"></i></div>
                    <p class="ps-title">¡Pago exitoso!</p>
                    <p class="ps-msg">${msg}</p>
                    <button class="ps-close-btn" onclick="document.getElementById('ps-overlay').remove()">
                        <i class="mdi mdi-check-circle-outline"></i> Entendido
                    </button>
                </div>
            `;
            // Cerrar al hacer clic en el fondo oscuro
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) overlay.remove();
            });
            document.body.appendChild(overlay);
        }

        // Muestra banner de éxito guardado antes del último reload
        document.addEventListener('DOMContentLoaded', function () {
            const pendingMsg = sessionStorage.getItem('pats_success_msg');
            if (pendingMsg) {
                sessionStorage.removeItem('pats_success_msg');
                setTimeout(() => showPaymentSuccessBanner(pendingMsg), 350);
            }
        });

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
                    billing: billingFrom('bill-street', 'bill-city', 'bill-postcode', 'bill-country'),
                    saveCard,   // si true, OPPWA tokeniza la tarjeta junto con el cobro
                    alias,
                    frecuencia: plan.frecuencia,
                    meses: plan.meses,
                    id_tipo_precio: plan.id_tipo_precio,
                    monto_membresia: plan.monto,
                    recargo: plan.recargo,
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
                    billing:         billingFrom('rec-bill-street', 'rec-bill-city', 'rec-bill-postcode', 'rec-bill-country'),
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
            showConfirm({
                title: 'Eliminar tarjeta',
                msg: '¿Deseas eliminar esta tarjeta guardada? Esta acción no se puede deshacer.',
                icon: 'mdi-credit-card-remove-outline',
                color: 'danger',
                okLabel: '<i class="mdi mdi-delete"></i> Sí, eliminar',
                onOk: async () => {
            try {
                await axios.delete(`${API_BASE}/token/${id}`);
                cargarTokens();
            } catch {
                showError('No se pudo eliminar la tarjeta');
            }
                },
            });
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

        // ── Modal vencimiento: auto-show ─────────────────
        @if(!empty($mostrarModalVenc) && $mostrarModalVenc)
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('modalVencimiento');
            if (el) new bootstrap.Modal(el).show();
        });
        @endif

        // Botón "Renovar con tarjeta" del modal de vencimiento
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-renovar-tab]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.renovarTab || 'tab-card';
                    document.querySelectorAll('.ptab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.ptab-panel').forEach(p => p.classList.remove('active'));
                    const tabBtn = document.querySelector(`.ptab-btn[data-target="${target}"]`);
                    const tabPanel = document.getElementById(target);
                    if (tabBtn) tabBtn.classList.add('active');
                    if (tabPanel) tabPanel.classList.add('active');
                });
            });
        });

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


    {{-- Modal de confirmación reutilizable --}}
    <div class="modal fade" id="modalConfirm" tabindex="-1" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
            <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 25px 60px rgba(0,0,0,.35);overflow:hidden;background:#fff;">
                <div id="modalConfirmStripe" style="height:4px;"></div>
                <div style="padding:1.75rem 1.75rem 1.5rem;text-align:center;">
                    <div id="modalConfirmIcon" style="width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .9rem;">
                        <i id="modalConfirmIconI" style="font-size:1.8rem;"></i>
                    </div>
                    <h5 id="modalConfirmTitle" style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#0f172a;margin:0 0 .45rem;"></h5>
                    <p  id="modalConfirmMsg"   style="font-size:.85rem;color:#64748b;margin:0 0 1.5rem;line-height:1.55;"></p>
                    <div style="display:flex;flex-direction:column;gap:.5rem;">
                        <button id="modalConfirmOk"
                            style="width:100%;padding:.8rem 1rem;border-radius:12px;border:none;font-size:.9rem;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:.45rem;transition:opacity .15s;">
                        </button>
                        <button data-bs-dismiss="modal"
                            style="width:100%;padding:.65rem 1rem;border-radius:12px;border:1.5px solid #e2e8f0;background:transparent;color:#64748b;font-size:.85rem;font-weight:600;cursor:pointer;font-family:inherit;">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        let _confirmCallback = null;
        let _confirmModal = null;

        function getConfirmModal() {
            if (!_confirmModal) {
                _confirmModal = new bootstrap.Modal(document.getElementById('modalConfirm'));
            }
            return _confirmModal;
        }

        /**
         * showConfirm(options)
         * options: { title, msg, icon, color, okLabel, onOk }
         * color: 'danger' | 'warning' | 'primary'
         */
        window.showConfirm = function ({ title, msg, icon = 'mdi-help-circle', color = 'danger', okLabel = 'Confirmar', onOk }) {
            const palette = {
                danger:  { bg: '#fef2f2', fg: '#dc2626', stripe: '#dc2626' },
                warning: { bg: '#fffbeb', fg: '#d97706', stripe: '#f59e0b' },
                primary: { bg: '#eff6ff', fg: '#2563eb', stripe: '#2563eb' },
            };
            const p = palette[color] ?? palette.danger;

            document.getElementById('modalConfirmStripe').style.background = p.stripe;
            document.getElementById('modalConfirmIcon').style.background   = p.bg;
            document.getElementById('modalConfirmIconI').className         = `mdi ${icon}`;
            document.getElementById('modalConfirmIconI').style.color       = p.fg;
            document.getElementById('modalConfirmTitle').textContent       = title;
            document.getElementById('modalConfirmMsg').textContent         = msg;

            const okBtn = document.getElementById('modalConfirmOk');
            okBtn.style.background = p.stripe;
            okBtn.style.color      = '#fff';
            okBtn.innerHTML        = okLabel;

            _confirmCallback = onOk;
            getConfirmModal().show();
        };

        document.getElementById('modalConfirmOk')?.addEventListener('click', () => {
            getConfirmModal().hide();
            if (typeof _confirmCallback === 'function') _confirmCallback();
            _confirmCallback = null;
        });
    })();
    </script>

@endsection
