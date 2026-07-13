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
        @include('feenicia._passport_card')

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
            @include('feenicia._plan_selector')

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
                <li><button class="ptab-btn" data-target="tab-cash" style="gap:.5rem;">
                    Pago OXXO    
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
                        {{-- <div
                            style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem;font-size:.78rem;color:#166534;margin-top:.5rem;">
                            <strong>Tarjeta QA:</strong> 5439240350653004 · CVV 123 · Vence 01/27 · Nombre: PASAPORTE
                        </div> --}}
                    </div>
                    <div class="col-md-7">
                        <form id="formCard" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label class="form-lbl">Datos de tu tarjeta</label>
                                <div id="stripe-card-element"
                                    style="border:1.5px solid #dce4f0;border-radius:8px;padding:12px 14px;background:#fff;transition:border-color .2s;">
                                </div>
                                <div id="stripe-card-error" style="color:#dc2626;font-size:.8rem;margin-top:.4rem;min-height:1.1em;" role="alert"></div>
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

                {{-- === Stripe: tarjetas guardadas === --}}
                <div id="stripe-saved-section">
                    <div id="stripe-saved-list">
                        <div style="text-align:center;padding:2rem;color:var(--text-muted);">
                            <div class="spin" style="border-color:var(--blue);border-top-color:transparent;margin:0 auto 1rem;width:28px;height:28px;"></div>
                            Cargando tarjetas...
                        </div>
                    </div>

                    <div style="margin-top:1rem;">
                        <button class="btn btn-outline" id="btnMostrarAgregarTarjeta" style="font-size:.85rem;">
                            <i class="mdi mdi-plus"></i> Añadir nueva tarjeta
                        </button>
                        <div id="stripe-add-card-form" style="display:none;margin-top:1rem;max-width:420px;">
                            <label class="form-lbl">Datos de la nueva tarjeta</label>
                            <div id="stripe-setup-element" style="border:1.5px solid #dce4f0;border-radius:8px;padding:12px 14px;background:#fff;transition:border-color .2s;"></div>
                            <div id="stripe-setup-error" style="color:#dc2626;font-size:.8rem;margin-top:.4rem;min-height:1.1em;"></div>
                            <div style="display:flex;gap:.5rem;margin-top:.85rem;">
                                <button class="btn btn-primary" id="btnGuardarTarjeta">
                                    <i class="mdi mdi-content-save"></i> Guardar tarjeta
                                </button>
                                <button type="button" class="btn btn-outline" id="btnCancelarAgregar">Cancelar</button>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:1.5rem;border-top:1px solid var(--border);padding-top:1.25rem;">
                        <button class="btn btn-primary btn-w" id="btnPagarConGuardada" disabled>
                            <i class="mdi mdi-lock"></i>
                            <span id="btnGuardadaTxt">Selecciona una tarjeta para pagar</span>
                        </button>
                    </div>
                </div>

                {{-- === Feenicia: tarjetas guardadas (preservado) === --}}
                <div id="feenicia-token-section" style="display:none;">
                    <div id="tokenList"></div>
                    <div class="mb-3">
                        <label class="form-lbl">CVV</label>
                        <input class="form-ctrl" id="inp-token-cvv" type="password" maxlength="4" placeholder="•••" style="max-width:140px;">
                    </div>
                    <button class="btn btn-primary" id="btnPagarToken" disabled>
                        <i class="mdi mdi-lock"></i>
                        <span id="btnTokenTxt">Selecciona una tarjeta</span>
                    </button>
                </div>
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
                            <div class="mb-3">
                                <label class="form-lbl">Datos de tu tarjeta</label>
                                <div id="stripe-recurring-element" style="border:1.5px solid #dce4f0;border-radius:8px;padding:12px 14px;background:#fff;transition:border-color .2s;"></div>
                                <div id="stripe-recurring-error" style="color:#dc2626;font-size:.8rem;margin-top:.4rem;min-height:1.1em;"></div>
                            </div>
                            <button type="button" class="btn btn-primary btn-w" id="btnStripeRecurring">
                                <i class="mdi mdi-repeat"></i>
                                <span id="btnStripeRecurringTxt">Configurar renovación automática</span>
                            </button>
                        </div>
                        <div class="col-md-5">
                            <div style="background:var(--navy);border-radius:12px;padding:1.25rem;">
                                <p style="font-size:.85rem;font-weight:600;color:var(--cream);margin:0 0 .75rem;">¿Cómo funciona?</p>
                                <ul style="list-style:none;padding:0;margin:0;font-size:.83rem;color:var(--text-muted);">
                                    <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> Guardamos tu tarjeta de forma segura con Stripe
                                    </li>
                                    <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> Se cobra el primer período ahora mismo
                                    </li>
                                    <li style="padding:.35rem 0;display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> Los siguientes cobros son automáticos
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === Feenicia: recurrente (preservado) === --}}
                <div id="feenicia-recurring-section" style="display:none;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <form id="formRecurring">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-lbl">Número de tarjeta</label>
                                    <input class="form-ctrl" id="rec-num" type="text" maxlength="19" placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="mb-3">
                                    <label class="form-lbl">Nombre del titular</label>
                                    <input class="form-ctrl" id="rec-name" type="text"
                                        value="{{ strtoupper(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}">
                                </div>
                                <div class="form-row mb-3">
                                    <div>
                                        <label class="form-lbl">Vencimiento (MM/AA)</label>
                                        <input class="form-ctrl" id="rec-exp" type="text" maxlength="5" placeholder="MM/AA">
                                    </div>
                                    <div>
                                        <label class="form-lbl">CVV</label>
                                        <input class="form-ctrl" id="rec-cvv" type="password" maxlength="4">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-lbl">Número de contrato</label>
                                    <input class="form-ctrl" id="rec-contract" type="text"
                                        value="{{ $pasaporte ? ($pasaporte->code_pasaporte ?: str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT)) : '' }}">
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
                                <p style="font-size:.85rem;font-weight:600;color:var(--cream);margin:0 0 .75rem;">¿Cómo funciona?</p>
                                <ul style="list-style:none;padding:0;margin:0;font-size:.83rem;color:var(--text-muted);">
                                    <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> Se genera un contrato con tu referencia</li>
                                    <li style="padding:.35rem 0;border-bottom:1px solid var(--border);display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> El cobro se ejecuta automáticamente</li>
                                    <li style="padding:.35rem 0;display:flex;gap:.5rem;">
                                        <i class="mdi mdi-check" style="color:var(--success);"></i> Puedes cancelar en cualquier momento</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: OXXO --}}
            <div class="ptab-panel" id="tab-cash">

                {{-- === OXXO via Stripe === --}}
                <div id="oxxo-section" style="max-width:480px;margin:0 auto;">

                    {{-- Header OXXO --}}
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
                        <div style="background:#fff;border-radius:10px;padding:.4rem .75rem;border:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:center;">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Oxxo_Logo.svg/1280px-Oxxo_Logo.svg.png"
                                 alt="OXXO" style="height:28px;width:auto;display:block;">
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--cream);font-size:.95rem;">Pago en OXXO</div>
                            <div style="font-size:.78rem;color:var(--text-muted);">Genera tu ficha y paga en cualquier tienda OXXO</div>
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <div id="oxxo-form-section">
                        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.25rem;font-size:.83rem;color:#9a3412;line-height:1.55;">
                            <i class="mdi mdi-information-outline"></i>
                            El pago en OXXO puede tardar hasta <strong>1 día hábil</strong> en reflejarse. Tu pasaporte se activará automáticamente al confirmarse.
                        </div>

                        <div class="mb-3">
                            <label class="form-lbl">Nombre del titular</label>
                            <input class="form-ctrl" id="oxxo-nombre" type="text"
                                value="{{ trim(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-lbl">Correo para recibo</label>
                            <input class="form-ctrl" id="oxxo-correo" type="email"
                                value="{{ $user->correo_usuario }}">
                        </div>
                        <div id="oxxo-form-error" style="color:#dc2626;font-size:.8rem;margin-bottom:.75rem;min-height:1.1em;"></div>
                        <button type="button" class="btn btn-primary btn-w" id="btnOxxo">
                            <i class="mdi mdi-barcode"></i>
                            <span id="btnOxxoTxt">Generar ficha OXXO</span>
                        </button>
                    </div>

                    {{-- Voucher OXXO --}}
                    <div id="oxxo-voucher-section" style="display:none;">
                        <div style="border:2px dashed #dc0c2c;border-radius:12px;padding:1.5rem;background:#fff;margin-bottom:1.25rem;">

                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px dashed #e2e8f0;">
                                <div style="background:#fff;border-radius:6px;padding:.25rem .6rem;border:1.5px solid #e2e8f0;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Oxxo_Logo.svg/1280px-Oxxo_Logo.svg.png"
                                         alt="OXXO" style="height:22px;width:auto;display:block;">
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Monto a pagar</div>
                                    <div id="oxxo-monto-display" style="font-size:1.4rem;font-weight:700;color:var(--cream);font-family:'Syne',sans-serif;"></div>
                                </div>
                            </div>

                            <div style="margin-bottom:1rem;">
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);margin-bottom:.4rem;">Número de referencia</div>
                                <div id="oxxo-number-display" style="font-family:monospace;font-size:1.1rem;font-weight:700;color:var(--cream);letter-spacing:.15em;word-break:break-all;background:#f8fafc;border:1px solid var(--border);border-radius:6px;padding:.6rem .9rem;"></div>
                            </div>

                            <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--text-muted);">
                                <span><i class="mdi mdi-calendar-clock"></i> Válido hasta: <strong id="oxxo-expiry-display" style="color:var(--text);"></strong></span>
                                <span id="oxxo-folio-display" style="font-family:monospace;font-size:.72rem;"></span>
                            </div>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:.6rem;">
                            <a id="oxxo-voucher-link" href="#" target="_blank" class="btn btn-outline btn-w" style="text-align:center;text-decoration:none;">
                                <i class="mdi mdi-open-in-new"></i> Ver ficha completa (imprimir / guardar)
                            </a>
                            <button type="button" class="btn btn-primary btn-w" id="btnVerificarOxxo">
                                <i class="mdi mdi-refresh"></i>
                                <span id="btnVerificarOxxoTxt">Verificar si ya pagué</span>
                            </button>
                        </div>

                        <div id="oxxo-verificar-msg" style="margin-top:.75rem;font-size:.83rem;text-align:center;min-height:1.2em;"></div>
                    </div>
                </div>

                {{-- === Feenicia: cash (preservado) === --}}
                <div id="feenicia-cash-section" style="display:none;">
                    <form id="formCash">
                        @csrf
                        <div class="mb-3">
                            <label class="form-lbl">Nombre del pagador</label>
                            <input class="form-ctrl" id="cash-name" type="text"
                                value="{{ trim(($pasaporte->nombres ?? ($user->nombre_usuario ?? '')) . ' ' . ($pasaporte->apellido_pa ?? '')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-lbl">Propina (opcional)</label>
                            <input class="form-ctrl" id="cash-tip" type="number" min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-lbl">Correo para recibo</label>
                            <input class="form-ctrl" id="cash-email" type="email" value="{{ $user->correo_usuario }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-w" id="btnCash">
                            <i class="mdi mdi-cash-register"></i>
                            <span id="btnCashTxt">Registrar pago $800 MXN</span>
                        </button>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_BASE = '/api/feenicia';
        const AFFILIATION = '{{ config('feenicia.affiliation') }}';
        const CSRF = '{{ csrf_token() }}';

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
        function toYYMM(mmaa) {
            const clean = mmaa.replace('/', '');
            return clean.length === 4 ? clean.substring(2, 4) + clean.substring(0, 2) : clean;
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

        // El tab "Tarjeta" usa Stripe Elements — el handler está abajo.

        // ── Tokenizar ────────────────────────────────────
        async function tokenizarTarjeta(pan, expDate, cardholderName, cvv2) {
            try {
                await axios.post(`${API_BASE}/token/generate`, {
                    pan,
                    expDate,
                    cardholderName,
                    cvv2,
                    affiliation: AFFILIATION,
                    alias: document.getElementById('inp-alias')?.value || null,
                });
            } catch {
                console.warn('No se pudo tokenizar');
            }
        }

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
                    div.innerHTML = `
                <div class="token-brand">${card.brand ?? 'Tarjeta'} ${card.product ?? ''}</div>
                <div class="token-num">•••• •••• •••• ${card.last4 ?? '????'}</div>
                <div class="token-exp">Vence: ${card.expDate ?? '—'}</div>
                ${card.isDefault ? '<span class="token-default">Default</span>' : ''}
                <div class="token-actions">
                    ${!card.isDefault ? `<button class="btn btn-outline btn-sm" onclick="setDefault(${card.id},event)">Default</button>` : ''}
                    <button class="btn btn-danger btn-sm" onclick="eliminarToken(${card.id},event)"><i class="mdi mdi-delete"></i></button>
                </div>`;
                    div.addEventListener('click', () => {
                        document.querySelectorAll('.token-card').forEach(c => c.classList.remove(
                            'selected'));
                        div.classList.add('selected');
                        selectedToken = card.id;
                        const fmt = new Intl.NumberFormat('es-MX').format(getPlan().total);
                        document.getElementById('btnTokenTxt').textContent = `Pagar $${fmt} MXN`;
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
            if (!selectedToken) return;
            const plan = getPlan();
            const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
            setLoading('btnPagarToken', true);
            try {
                const res = await axios.post(`${API_BASE}/token/sale`, {
                    tokenId: selectedToken,
                    amount: plan.total,
                    cvv2: document.getElementById('inp-token-cvv').value,
                    transactionDate: Date.now(),
                });
                if (res.data.success) {
                    sessionStorage.setItem('pats_success_msg', `✓ Pago de $${fmt} MXN aprobado. Auth: ${res.data.authnum}`);
                    window.location.reload();
                }
            } catch (err) {
                showError(err.response?.data?.error ?? 'Error al procesar el pago');
            } finally {
                document.getElementById('btnPagarToken').disabled = false;
                document.getElementById('btnTokenTxt').textContent = `Pagar $${fmt} MXN`;
            }
        });

        // ── Recurrente ───────────────────────────────────
        document.getElementById('formRecurring')?.addEventListener('submit', async e => {
            e.preventDefault();
            const plan = getPlan();
            const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
            setLoading('btnRecurring', true);
            try {
                const res = await axios.post(`${API_BASE}/sale/recurring`, {
                    affiliation: AFFILIATION,
                    amount: plan.total,
                    cardholderName: document.getElementById('rec-name').value,
                    expDate: toYYMM(document.getElementById('rec-exp').value),
                    pan: document.getElementById('rec-num').value.replace(/\s/g, ''),
                    contractNumber: document.getElementById('rec-contract').value,
                    transactionDate: Date.now(),
                    sendReceiptTo: document.getElementById('rec-email').value || null,
                });
                if (res.data.success) {
                    showSuccess(`✓ Cobro recurrente configurado. Folio: ${res.data.folio ?? res.data.orderId}`);
                    document.getElementById('formRecurring').reset();
                }
            } catch (err) {
                showError(err.response?.data?.error ?? 'Error');
            } finally {
                setLoading('btnRecurring', false, `Iniciar cobro recurrente $${fmt} MXN`);
            }
        });

        // ── Efectivo ─────────────────────────────────────
        document.getElementById('formCash')?.addEventListener('submit', async e => {
            e.preventDefault();
            const plan = getPlan();
            const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
            setLoading('btnCash', true);
            try {
                const res = await axios.post(`${API_BASE}/sale/cash`, {
                    affiliation: AFFILIATION,
                    amount: plan.total,
                    transactionDate: Date.now(),
                    cardholderName: document.getElementById('cash-name').value,
                    tip: parseFloat(document.getElementById('cash-tip').value) || 0,
                    sendReceiptTo: document.getElementById('cash-email').value || null,
                });
                if (res.data.success) {
                    showSuccess(`✓ Pago en efectivo de $${fmt} MXN registrado. ID: ${res.data.transactionId}`);
                    document.getElementById('formCash').reset();
                }
            } catch (err) {
                showError(err.response?.data?.error ?? 'Error');
            } finally {
                setLoading('btnCash', false, `Registrar pago $${fmt} MXN`);
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
                <div>${pago.proveedor ?? 'FEENICIA'}</div></div>
            ${pago.authnum ? `<div style="grid-column:1/-1;"><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Autorización</small>
                    <div style="font-family:monospace;font-size:1rem;">${pago.authnum}</div></div>` : ''}
            <div><small style="font-size:.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Estatus</small>
                <span class="badge badge-success"><i class="mdi mdi-check-circle"></i> ${pago.estatus}</span></div>
        </div>`;
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        }
    </script>

    @if (strtoupper($pasaporte?->tipo_cliente ?? '') !== 'EMPRESA')
    {{-- ── Stripe Elements (tarjeta + guardadas + recurrente) ──────── --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
    (function () {
        const STRIPE_PK  = @json(config('services.stripe_renovacion.key'));
        const URL_INTENT = @json(route('pagos.stripe.intent'));
        const URL_CONF   = @json(route('pagos.stripe.confirmar'));
        const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        if (!STRIPE_PK) return;

        const stripe   = Stripe(STRIPE_PK);
        const elements = stripe.elements();
        const cardEl   = elements.create('card', {
            style: {
                base: {
                    fontFamily: "'Outfit', 'Syne', sans-serif",
                    fontSize: '15px',
                    color: '#1e293b',
                    '::placeholder': { color: '#94a3b8' },
                },
            },
            hidePostalCode: true,
        });
        cardEl.mount('#stripe-card-element');
        cardEl.addEventListener('change', e => {
            document.getElementById('stripe-card-error').textContent = e.error?.message ?? '';
        });

        function setBtnLoading(loading) {
            const btn = document.getElementById('btnPagarCard');
            const txt = document.getElementById('btnCardTxt');
            if (!btn) return;
            btn.disabled = loading;
            if (!loading && txt) {
                const plan = getPlan();
                txt.textContent = `Pagar $${new Intl.NumberFormat('es-MX').format(plan.total)} MXN`;
            }
        }

        async function fetchJson(url, data) {
            const body = new URLSearchParams({ ...data, _token: CSRF });
            const r = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body });
            return r.json();
        }

        document.getElementById('formCard')?.addEventListener('submit', async e => {
            e.preventDefault();
            setBtnLoading(true);
            document.getElementById('stripe-card-error').textContent = '';
            showError('');

            try {
                const plan  = getPlan();
                const monto = plan.total;
                const freq  = plan.frecuencia;
                const meses = plan.meses;

                // 1. Crear PaymentIntent
                const intentData = await fetchJson(URL_INTENT, { frecuencia: freq, monto_orden: monto });
                if (!intentData.ok) throw new Error(intentData.error ?? 'Error al iniciar el pago.');

                // 2. Crear PM y confirmar PI
                const { paymentMethod, error: pmErr } = await stripe.createPaymentMethod({ type: 'card', card: cardEl });
                if (pmErr) throw new Error(pmErr.message);

                const { paymentIntent, error: confErr } = await stripe.confirmCardPayment(intentData.client_secret, {
                    payment_method: paymentMethod.id,
                });
                if (confErr) throw new Error(confErr.message);

                // 3. Confirmar en el servidor para actualizar el pasaporte
                const conf = await fetchJson(URL_CONF, {
                    payment_intent_id: paymentIntent.id,
                    frecuencia:  freq,
                    monto_orden: monto,
                    _pats_meses: meses,
                });
                if (!conf.ok) throw new Error(conf.error ?? 'El pago fue procesado pero no se pudo actualizar tu membresía. Contacta soporte.');

                // Éxito
                sessionStorage.setItem('pats_success_msg', `Pago confirmado. Tu membresía ${freq.toLowerCase()} ha sido renovada hasta ${conf.vigencia ?? '—'}.`);
                window.location.reload();

            } catch (err) {
                document.getElementById('stripe-card-error').textContent = err.message;
                showError(err.message);
            } finally {
                setBtnLoading(false);
            }
        });
    })();
    </script>

    {{-- ── Stripe: Tarjetas guardadas ──────────────────────────────── --}}
    <script>
    (function () {
        const STRIPE_PK    = @json(config('services.stripe_renovacion.key'));
        const URL_LIST     = @json(route('pagos.stripe.tarjetas'));
        const URL_SETUP    = @json(route('pagos.stripe.setup'));
        const URL_DELETE   = @json(route('pagos.stripe.tarjeta.eliminar'));
        const URL_PAGAR    = @json(route('pagos.stripe.tarjeta.pagar'));
        const URL_CONF     = @json(route('pagos.stripe.confirmar'));
        const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        if (!STRIPE_PK) return;

        const stripe   = Stripe(STRIPE_PK);
        const elements = stripe.elements();
        let setupCardEl   = null;
        let selectedPmId  = null;

        async function post(url, data) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new URLSearchParams({ _token: CSRF, ...data }),
            });
            return r.json();
        }

        async function cargarTarjetasStripe() {
            const container = document.getElementById('stripe-saved-list');
            container.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--text-muted);"><div class="spin" style="border-color:var(--blue);border-top-color:transparent;margin:0 auto 1rem;width:28px;height:28px;"></div>Cargando tarjetas...</div>`;
            try {
                const r   = await fetch(URL_LIST, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
                const res = await r.json();
                if (!res.ok) throw new Error(res.error);

                if (!res.cards.length) {
                    container.innerHTML = `<div style="text-align:center;padding:1.5rem;color:var(--text-muted);"><i class="mdi mdi-credit-card-off" style="font-size:2.5rem;display:block;margin-bottom:.5rem;color:var(--border);"></i>No tienes tarjetas guardadas.</div>`;
                    return;
                }

                container.innerHTML = `<div class="token-grid" id="stripe-token-grid"></div>`;
                const grid = document.getElementById('stripe-token-grid');
                selectedPmId = null;
                document.getElementById('btnPagarConGuardada').disabled = true;
                document.getElementById('btnGuardadaTxt').textContent   = 'Selecciona una tarjeta para pagar';

                res.cards.forEach(card => {
                    const div = document.createElement('div');
                    div.className = 'token-card';
                    div.innerHTML = `
                        <div class="token-brand">${card.brand}</div>
                        <div class="token-num">•••• •••• •••• ${card.last4}</div>
                        <div class="token-exp">Vence: ${card.exp}</div>
                        <div class="token-actions">
                            <button class="btn btn-danger btn-sm" onclick="eliminarTarjetaStripe('${card.id}',event)">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>`;
                    div.addEventListener('click', () => {
                        document.querySelectorAll('#stripe-token-grid .token-card').forEach(c => c.classList.remove('selected'));
                        div.classList.add('selected');
                        selectedPmId = card.id;
                        const fmt = new Intl.NumberFormat('es-MX').format(getPlan().total);
                        document.getElementById('btnGuardadaTxt').textContent = `Pagar $${fmt} MXN con ${card.brand} ···${card.last4}`;
                        document.getElementById('btnPagarConGuardada').disabled = false;
                    });
                    grid.appendChild(div);
                });
            } catch (err) {
                container.innerHTML = `<div class="alert alert-danger show"><i class="mdi mdi-alert"></i> ${err.message}</div>`;
            }
        }

        // Cargar al hacer clic en el tab
        document.querySelectorAll('.ptab-btn').forEach(btn => {
            if (btn.dataset.target === 'tab-token') {
                btn.addEventListener('click', cargarTarjetasStripe);
            }
        });

        // Mostrar / ocultar formulario de nueva tarjeta
        document.getElementById('btnMostrarAgregarTarjeta')?.addEventListener('click', () => {
            const form = document.getElementById('stripe-add-card-form');
            const visible = form.style.display !== 'none';
            form.style.display = visible ? 'none' : 'block';
            if (!visible && !setupCardEl) {
                setupCardEl = elements.create('card', {
                    hidePostalCode: true,
                    style: { base: { fontFamily: "'DM Sans', sans-serif", fontSize: '15px', color: '#1e293b', '::placeholder': { color: '#94a3b8' } } },
                });
                setupCardEl.mount('#stripe-setup-element');
                setupCardEl.addEventListener('change', e => {
                    document.getElementById('stripe-setup-error').textContent = e.error?.message ?? '';
                });
            }
        });

        document.getElementById('btnCancelarAgregar')?.addEventListener('click', () => {
            document.getElementById('stripe-add-card-form').style.display = 'none';
        });

        // Guardar nueva tarjeta
        document.getElementById('btnGuardarTarjeta')?.addEventListener('click', async () => {
            const btn  = document.getElementById('btnGuardarTarjeta');
            const errEl = document.getElementById('stripe-setup-error');
            btn.disabled = true;
            btn.innerHTML = `<span class="spin"></span> Guardando...`;
            errEl.textContent = '';
            try {
                const si = await post(URL_SETUP, {});
                if (!si.ok) throw new Error(si.error);

                const { setupIntent, error } = await stripe.confirmCardSetup(si.client_secret, {
                    payment_method: { card: setupCardEl },
                });
                if (error) throw new Error(error.message);

                document.getElementById('stripe-add-card-form').style.display = 'none';
                showSuccess('✓ Tarjeta guardada correctamente.');
                await cargarTarjetasStripe();
            } catch (err) {
                errEl.textContent = err.message;
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-content-save"></i> Guardar tarjeta`;
            }
        });

        // Eliminar tarjeta
        window.eliminarTarjetaStripe = function (pmId, event) {
            event.stopPropagation();
            showConfirm({
                title: 'Eliminar tarjeta',
                msg: '¿Deseas eliminar esta tarjeta guardada? Esta acción no se puede deshacer.',
                icon: 'mdi-credit-card-remove-outline',
                color: 'danger',
                okLabel: '<i class="mdi mdi-delete"></i> Sí, eliminar',
                onOk: async () => {
                    try {
                        await post(URL_DELETE, { pm_id: pmId });
                        if (selectedPmId === pmId) {
                            selectedPmId = null;
                            document.getElementById('btnPagarConGuardada').disabled = true;
                            document.getElementById('btnGuardadaTxt').textContent   = 'Selecciona una tarjeta para pagar';
                        }
                        await cargarTarjetasStripe();
                    } catch {
                        showError('No se pudo eliminar la tarjeta.');
                    }
                },
            });
        };

        // Pagar con tarjeta guardada
        document.getElementById('btnPagarConGuardada')?.addEventListener('click', async () => {
            if (!selectedPmId) return;
            const plan = getPlan();
            const btn  = document.getElementById('btnPagarConGuardada');
            btn.disabled = true;
            btn.innerHTML = `<span class="spin"></span> Procesando...`;

            try {
                const res = await post(URL_PAGAR, {
                    payment_method_id: selectedPmId,
                    frecuencia: plan.frecuencia,
                    monto_orden: plan.total,
                    _pats_meses: plan.meses,
                });

                if (res.requires_action) {
                    const { paymentIntent, error } = await stripe.confirmCardPayment(res.client_secret);
                    if (error) throw new Error(error.message);
                    const conf = await post(URL_CONF, {
                        payment_intent_id: paymentIntent.id,
                        frecuencia: plan.frecuencia,
                        monto_orden: plan.total,
                        _pats_meses: plan.meses,
                    });
                    if (!conf.ok) throw new Error(conf.error ?? 'Error al confirmar el pago.');
                    sessionStorage.setItem('pats_success_msg', `✓ Pago confirmado. Membresía renovada hasta ${conf.vigencia}.`);
                } else if (res.ok) {
                    sessionStorage.setItem('pats_success_msg', `✓ Pago confirmado. Membresía renovada hasta ${res.vigencia}.`);
                } else {
                    throw new Error(res.error ?? 'Error al procesar el pago.');
                }
                window.location.reload();
            } catch (err) {
                showError(err.message);
                btn.disabled = false;
                const fmt = new Intl.NumberFormat('es-MX').format(plan.total);
                btn.innerHTML = `<i class="mdi mdi-lock"></i> <span id="btnGuardadaTxt">Pagar $${fmt} MXN</span>`;
            }
        });
    })();
    </script>

    {{-- ── Stripe: Recurrente ───────────────────────────────────────── --}}
    <script>
    (function () {
        const STRIPE_PK      = @json(config('services.stripe_renovacion.key'));
        const URL_SETUP      = @json(route('pagos.stripe.setup'));
        const URL_SUSCRIPCION = @json(route('pagos.stripe.suscripcion'));
        const URL_CONF       = @json(route('pagos.stripe.confirmar'));
        const CSRF           = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        if (!STRIPE_PK) return;

        const stripe   = Stripe(STRIPE_PK);
        const elements = stripe.elements();
        const recEl    = elements.create('card', {
            hidePostalCode: true,
            style: { base: { fontFamily: "'DM Sans', sans-serif", fontSize: '15px', color: '#1e293b', '::placeholder': { color: '#94a3b8' } } },
        });
        recEl.mount('#stripe-recurring-element');
        recEl.addEventListener('change', e => {
            document.getElementById('stripe-recurring-error').textContent = e.error?.message ?? '';
        });

        async function post(url, data) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new URLSearchParams({ _token: CSRF, ...data }),
            });
            return r.json();
        }

        document.getElementById('btnStripeRecurring')?.addEventListener('click', async () => {
            const plan  = getPlan();
            const btn   = document.getElementById('btnStripeRecurring');
            const errEl = document.getElementById('stripe-recurring-error');
            btn.disabled = true;
            btn.innerHTML = `<span class="spin"></span> Procesando...`;
            errEl.textContent = '';

            try {
                // 1. Crear SetupIntent para guardar la tarjeta
                const si = await post(URL_SETUP, {});
                if (!si.ok) throw new Error(si.error);

                // 2. Confirmar SetupIntent (guardar tarjeta)
                const { setupIntent, error: siErr } = await stripe.confirmCardSetup(si.client_secret, {
                    payment_method: { card: recEl },
                });
                if (siErr) throw new Error(siErr.message);

                // 3. Cobrar el primer período con la tarjeta guardada
                const res = await post(URL_SUSCRIPCION, {
                    setup_intent_id: setupIntent.id,
                    frecuencia: plan.frecuencia,
                    monto_orden: plan.total,
                    _pats_meses: plan.meses,
                });

                if (res.requires_action) {
                    const { paymentIntent, error } = await stripe.confirmCardPayment(res.client_secret);
                    if (error) throw new Error(error.message);
                    const conf = await post(URL_CONF, {
                        payment_intent_id: paymentIntent.id,
                        frecuencia: plan.frecuencia,
                        monto_orden: plan.total,
                        _pats_meses: plan.meses,
                    });
                    if (!conf.ok) throw new Error(conf.error ?? 'Error al confirmar el pago.');
                    sessionStorage.setItem('pats_success_msg', `✓ Renovación automática configurada. Membresía renovada hasta ${conf.vigencia}.`);
                } else if (res.ok) {
                    sessionStorage.setItem('pats_success_msg', `✓ Renovación automática configurada. Membresía renovada hasta ${res.vigencia}.`);
                } else {
                    throw new Error(res.error ?? 'Error al configurar el cobro recurrente.');
                }
                window.location.reload();
            } catch (err) {
                errEl.textContent = err.message;
                showError(err.message);
                btn.disabled = false;
                const fmt = new Intl.NumberFormat('es-MX').format(getPlan().total);
                btn.innerHTML = `<i class="mdi mdi-repeat"></i> <span id="btnStripeRecurringTxt">Configurar renovación automática $${fmt} MXN</span>`;
            }
        });

        // Actualizar texto del botón al cambiar el plan
        document.addEventListener('planChanged', e => {
            const fmt = new Intl.NumberFormat('es-MX').format(e.detail.total);
            const el  = document.getElementById('btnStripeRecurringTxt');
            if (el) el.textContent = `Configurar renovación automática $${fmt} MXN`;
            if (window._stripeSavedSelected) {
                const gEl = document.getElementById('btnGuardadaTxt');
                if (gEl) gEl.textContent = `Pagar $${fmt} MXN con tarjeta seleccionada`;
            }
        });

        // ── Estado de renovación + botón cancelar ──────────────────────
        const URL_TARJETAS = @json(route('pagos.stripe.tarjetas'));
        const URL_CANCELAR = @json(route('pagos.recurrente.cancelar'));
        const CSRF_REC     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        async function cargarEstadoRecurrente() {
            const loading  = document.getElementById('rec-estado-loading');
            const activo   = document.getElementById('rec-activo-box');
            const inactivo = document.getElementById('rec-inactivo-box');
            const info     = document.getElementById('rec-tarjeta-info');
            const badge    = document.getElementById('badgeRecurrente');

            if (!loading) return;
            loading.style.display  = '';
            activo.style.display   = 'none';
            inactivo.style.display = 'none';

            try {
                const r   = await fetch(URL_TARJETAS, { headers: { 'Accept': 'application/json' } });
                const res = await r.json();

                loading.style.display = 'none';

                if (res.ok && res.cards && res.cards.length > 0) {
                    const card  = res.cards[0];
                    const extra = res.cards.length > 1 ? ` y ${res.cards.length - 1} más` : '';
                    info.innerHTML = `<i class="mdi mdi-credit-card-outline"></i> ${card.brand} •••• ${card.last4} · vence ${card.exp}${extra}`;
                    activo.style.display = '';
                    if (badge) badge.style.display = '';
                } else {
                    inactivo.style.display = '';
                    if (badge) badge.style.display = 'none';
                }
            } catch (_) {
                loading.style.display  = 'none';
                inactivo.style.display = '';
                if (badge) badge.style.display = 'none';
            }
        }

        // Cargar al hacer clic en el tab Recurrente
        document.querySelectorAll('.ptab-btn').forEach(btn => {
            if (btn.dataset.target === 'tab-recurring') {
                btn.addEventListener('click', cargarEstadoRecurrente);
            }
        });

        // Cargar en background al abrir la página para actualizar el badge
        cargarEstadoRecurrente();

        // Cancelar renovación
        document.getElementById('btnCancelarRecurrente')?.addEventListener('click', () => {
            showConfirm({
                title: 'Cancelar renovación automática',
                msg: 'Se eliminarán todas tus tarjetas guardadas y deberás renovar tu pasaporte manualmente. ¿Deseas continuar?',
                icon: 'mdi-repeat-off',
                color: 'warning',
                okLabel: '<i class="mdi mdi-cancel"></i> Sí, cancelar renovación',
                onOk: async () => {
                    const btn = document.getElementById('btnCancelarRecurrente');
                    const msg = document.getElementById('rec-cancel-msg');
                    btn.disabled = true;
                    document.getElementById('btnCancelarRecurrenteTxt').textContent = 'Cancelando...';
                    msg.style.color = '#64748b';
                    msg.textContent = '';
                    try {
                        const r   = await fetch(URL_CANCELAR, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF_REC, 'Accept': 'application/json' },
                            body: new URLSearchParams({ _token: CSRF_REC }),
                        });
                        const res = await r.json();
                        if (res.ok) {
                            document.getElementById('rec-activo-box').style.display  = 'none';
                            document.getElementById('rec-inactivo-box').style.display = '';
                            const badge = document.getElementById('badgeRecurrente');
                            if (badge) badge.style.display = 'none';
                            showSuccess('Renovación automática cancelada.');
                        } else {
                            msg.style.color = '#dc2626';
                            msg.textContent = res.error ?? 'No se pudo cancelar.';
                            btn.disabled = false;
                            document.getElementById('btnCancelarRecurrenteTxt').textContent = 'Cancelar renovación';
                        }
                    } catch (err) {
                        msg.style.color = '#dc2626';
                        msg.textContent = 'Error de conexión.';
                        btn.disabled = false;
                        document.getElementById('btnCancelarRecurrenteTxt').textContent = 'Cancelar renovación';
                    }
                },
            });
        });
    })();
    </script>

    {{-- ── OXXO via Stripe ──────────────────────────────────────────── --}}
    <script>
    (function () {
        const URL_OXXO     = @json(route('pagos.oxxo.intent'));
        const URL_VERIFICAR = @json(route('pagos.oxxo.verificar'));
        const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        let _oxxoPiId      = null;
        let _oxxoFrecuencia = null;
        let _oxxoMonto     = null;

        function fmtMonto(m) {
            return '$' + parseFloat(m).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN';
        }

        function fmtExpiry(ts) {
            if (!ts) return '—';
            const d = new Date(ts * 1000);
            return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
        }

        function fmtOxxoNumber(num) {
            if (!num) return '—';
            // formato: XXXX XXXX XXXX XXXX XX
            return num.replace(/(.{4})(?=.)/g, '$1 ').trim();
        }

        // ── Generar ficha OXXO ──────────────────────────────────────────
        document.getElementById('btnOxxo')?.addEventListener('click', async () => {
            const plan   = getPlan();
            const btn    = document.getElementById('btnOxxo');
            const errEl  = document.getElementById('oxxo-form-error');
            const nombre = document.getElementById('oxxo-nombre')?.value?.trim() ?? '';
            const correo = document.getElementById('oxxo-correo')?.value?.trim() ?? '';

            errEl.textContent = '';

            if (!nombre) { errEl.textContent = 'Ingresa tu nombre completo.'; return; }
            if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                errEl.textContent = 'Ingresa un correo válido.'; return;
            }

            btn.disabled = true;
            btn.innerHTML = `<span class="spin"></span> Generando ficha...`;

            try {
                const r   = await fetch(URL_OXXO, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: new URLSearchParams({
                        _token:      CSRF,
                        frecuencia:  plan.frecuencia,
                        monto_orden: plan.total,
                        nombre:      nombre,
                        correo:      correo,
                    }),
                });
                const res = await r.json();
                if (!res.ok) throw new Error(res.error ?? 'No se pudo generar la ficha OXXO.');

                // Guardar para verificación
                _oxxoPiId       = res.payment_intent_id;
                _oxxoFrecuencia = plan.frecuencia;
                _oxxoMonto      = plan.total;

                // Mostrar voucher
                document.getElementById('oxxo-monto-display').textContent  = fmtMonto(res.monto);
                document.getElementById('oxxo-number-display').textContent  = fmtOxxoNumber(res.voucher_number);
                document.getElementById('oxxo-expiry-display').textContent  = fmtExpiry(res.expires_after);
                document.getElementById('oxxo-folio-display').textContent   = res.referencia ?? '';

                const link = document.getElementById('oxxo-voucher-link');
                if (res.hosted_voucher_url) {
                    link.href             = res.hosted_voucher_url;
                    link.style.display    = '';
                } else {
                    link.style.display    = 'none';
                }

                document.getElementById('oxxo-form-section').style.display    = 'none';
                document.getElementById('oxxo-voucher-section').style.display  = 'block';

            } catch (err) {
                errEl.textContent = err.message;
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-barcode"></i> <span id="btnOxxoTxt">Generar ficha OXXO</span>`;
            }
        });

        // ── Verificar si ya pagué ────────────────────────────────────────
        document.getElementById('btnVerificarOxxo')?.addEventListener('click', async () => {
            const btn  = document.getElementById('btnVerificarOxxo');
            const msg  = document.getElementById('oxxo-verificar-msg');

            if (!_oxxoPiId) { msg.textContent = 'Genera primero tu ficha OXXO.'; return; }

            btn.disabled = true;
            btn.innerHTML = `<span class="spin"></span> <span id="btnVerificarOxxoTxt">Verificando...</span>`;
            msg.style.color = '#64748b';
            msg.textContent = 'Consultando con Stripe...';

            try {
                const r   = await fetch(URL_VERIFICAR, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: new URLSearchParams({
                        _token:             CSRF,
                        payment_intent_id:  _oxxoPiId,
                        frecuencia:         _oxxoFrecuencia,
                        monto_orden:        _oxxoMonto,
                    }),
                });
                const res = await r.json();

                if (res.ok) {
                    msg.style.color  = '#10b981';
                    msg.innerHTML    = `<i class="mdi mdi-check-circle"></i> ¡Pago confirmado! Tu pasaporte ha sido renovado.`;
                    btn.innerHTML    = `<i class="mdi mdi-check-circle"></i> Pago confirmado`;
                    sessionStorage.setItem('pats_success_msg', '✓ ¡Pago en OXXO confirmado! Tu pasaporte ha sido renovado.');
                    setTimeout(() => window.location.reload(), 2500);
                } else {
                    msg.style.color  = '#f59e0b';
                    msg.innerHTML    = `<i class="mdi mdi-clock-outline"></i> ${res.msg ?? 'Aún no se detecta el pago. Inténtalo de nuevo después de pagar en OXXO.'}`;
                    btn.disabled     = false;
                    btn.innerHTML    = `<i class="mdi mdi-refresh"></i> <span id="btnVerificarOxxoTxt">Verificar si ya pagué</span>`;
                }
            } catch (err) {
                msg.style.color  = '#dc2626';
                msg.textContent  = 'Error al verificar: ' + err.message;
                btn.disabled     = false;
                btn.innerHTML    = `<i class="mdi mdi-refresh"></i> <span id="btnVerificarOxxoTxt">Verificar si ya pagué</span>`;
            }
        });
    })();
    </script>
    @endif

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
