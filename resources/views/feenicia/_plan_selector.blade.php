{{--
    Partial: _plan_selector.blade.php
    Variables requeridas: $pasaporte
    Incluir con: @include('feenicia._plan_selector')
--}}

@php
    $montoBase = 800;
    $montoAnual = 9600;
    $mesesVencidos = (int) ($pasaporte->meses_vencidos ?? 0);
    $recargoUnit = (float) ($pasaporte->recargo_acumulado ?? 0);
    $tieneRecargo = $mesesVencidos > 0;
    $esRenovacion = $pasaporte && in_array($pasaporte->estatus, ['activo', 'vencido']);
@endphp

{{-- Inputs ocultos que el JS actualiza --}}
<input type="hidden" id="h_frecuencia" value="MENSUAL">
<input type="hidden" id="h_monto" value="{{ $montoBase }}">
<input type="hidden" id="h_id_tipo_precio" value="2">
<input type="hidden" id="h_meses" value="1">
<input type="hidden" id="h_recargo" value="{{ $recargoUnit }}">
<input type="hidden" id="h_monto_total" value="{{ $montoBase }}">

<div class="plan-section">
    <label class="form-lbl" style="margin-bottom:.75rem;">
        {{ $esRenovacion ? 'Renovación de pasaporte' : 'Activa tu pasaporte' }}
    </label>

    {{-- Tabs de tipo de plan --}}
    <div class="plan-tipo-tabs" id="planTipoTabs">
        <button class="plan-tipo-btn active" data-tipo="mensual">
            <i class="mdi mdi-calendar-month"></i> Mensual
        </button>
        <button class="plan-tipo-btn" data-tipo="anual">
            <i class="mdi mdi-calendar-star"></i> Anual
        </button>
    </div>

    {{-- Panel mensual — selector de meses --}}
    <div class="plan-panel active" id="panel-mensual">
        <p style="font-size:.83rem;color:#64748b;margin-bottom:1rem;">
            Selecciona cuántos meses deseas pagar:
        </p>
        <div class="plan-meses-grid" id="mesesGrid">
            @foreach ([1, 2, 3, 6, 12] as $m)
                @php
                    $montoMeses = $montoBase * $m;
                    $recargoMes = $tieneRecargo && $m === 1 ? $recargoUnit : 0;
                    $totalMeses = $montoMeses + ($tieneRecargo ? $recargoUnit : 0);
                @endphp
                <button class="mes-btn {{ $m === 1 ? 'active' : '' }}" data-meses="{{ $m }}"
                    data-monto="{{ $montoBase * $m }}"
                    data-total="{{ $m === 1 && $tieneRecargo ? $montoBase + $recargoUnit : $montoBase * $m + ($tieneRecargo ? $recargoUnit : 0) }}"
                    data-frecuencia="MENSUAL" data-tipo="2">
                    <span class="mes-btn__num">{{ $m }}</span>
                    <span class="mes-btn__lbl">{{ $m === 1 ? 'mes' : 'meses' }}</span>
                    <span class="mes-btn__price">${{ number_format($montoBase * $m, 0) }}</span>
                    @if ($m === 12)
                        <span class="mes-btn__save">= $9,600</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Desglose del total --}}
        <div class="plan-desglose" id="desgloseMensual">
            <div class="desglose-row">
                <span>Pasaporte <span id="lblMeses">1</span> mes</span>
                <strong id="lblMontoMeses">${{ number_format($montoBase, 0) }} MXN</strong>
            </div>
            @if ($tieneRecargo)
                <div class="desglose-row desglose-recargo">
                    <span>
                        <i class="mdi mdi-alert-circle" style="color:#dc2626;"></i>
                        Recargo por {{ $mesesVencidos }} mes(es) vencido(s)
                    </span>
                    <strong style="color:#dc2626;">${{ number_format($recargoUnit, 2) }} MXN</strong>
                </div>
            @endif
            <div class="desglose-row desglose-total">
                <span>Total a pagar</span>
                <strong id="lblTotal" style="color:#2563eb;font-size:1.1rem;">
                    ${{ number_format($montoBase + ($tieneRecargo ? $recargoUnit : 0), 0) }} MXN
                </strong>
            </div>
        </div>
    </div>

    {{-- Panel anual --}}
    <div class="plan-panel" id="panel-anual">
        <div class="plan-anual-card">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;">
                <div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1e3a5f;">Plan Anual</div>
                    <div style="font-size:.85rem;color:#64748b;margin-top:.2rem;">12 meses de cobertura · Ahorra vs
                        mensual</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:1.8rem;font-weight:700;color:#2563eb;">$9,600</div>
                    <div style="font-size:.75rem;color:#64748b;">MXN / año</div>
                </div>
            </div>

            @if ($tieneRecargo)
                <div class="plan-desglose" style="margin-top:1rem;">
                    <div class="desglose-row">
                        <span>Pasaporte anual</span>
                        <strong>$9,600 MXN</strong>
                    </div>
                    <div class="desglose-row desglose-recargo">
                        <span><i class="mdi mdi-alert-circle" style="color:#dc2626;"></i> Recargo acumulado</span>
                        <strong style="color:#dc2626;">${{ number_format($recargoUnit, 2) }} MXN</strong>
                    </div>
                    <div class="desglose-row desglose-total">
                        <span>Total a pagar</span>
                        <strong
                            style="color:#2563eb;font-size:1.1rem;">${{ number_format($montoAnual + $recargoUnit, 0) }}
                            MXN</strong>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .plan-section {
        margin-bottom: 1.5rem;
    }

    .plan-tipo-tabs {
        display: flex;
        gap: .5rem;
        margin-bottom: 1.25rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0;
    }

    .plan-tipo-btn {
        display: flex;
        align-items: center;
        gap: .4rem;
        padding: .65rem 1.25rem;
        border: none;
        background: transparent;
        font-size: .88rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all .15s;
        font-family: inherit;
    }

    .plan-tipo-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    .plan-tipo-btn:hover:not(.active) {
        color: #334155;
    }

    .plan-panel {
        display: none;
    }

    .plan-panel.active {
        display: block;
    }

    .plan-meses-grid {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        margin-bottom: 1rem;
    }

    .mes-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: .75rem 1rem;
        min-width: 70px;
        border: 2px solid #dde3ef;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: all .15s;
        font-family: inherit;
    }

    .mes-btn:hover {
        border-color: #2563eb;
    }

    .mes-btn.active {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .mes-btn__num {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1e3a5f;
        line-height: 1;
    }

    .mes-btn.active .mes-btn__num {
        color: #2563eb;
    }

    .mes-btn__lbl {
        font-size: .7rem;
        color: #64748b;
    }

    .mes-btn__price {
        font-size: .78rem;
        font-weight: 600;
        color: #334155;
        margin-top: .2rem;
    }

    .mes-btn__save {
        font-size: .65rem;
        background: #d1fae5;
        color: #065f46;
        border-radius: 99px;
        padding: .1rem .4rem;
        margin-top: .2rem;
    }

    .plan-desglose {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: .85rem 1rem;
        margin-top: .75rem;
    }

    .desglose-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .4rem 0;
        font-size: .87rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .desglose-row:last-child {
        border: none;
    }

    .desglose-recargo {
        color: #dc2626;
    }

    .desglose-total {
        font-size: .95rem;
        font-weight: 700;
        padding-top: .6rem;
    }

    .plan-anual-card {
        background: #eff6ff;
        border: 2px solid #2563eb;
        border-radius: 12px;
        padding: 1.25rem;
    }

    .digi-avatar-initials {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 4px 16px rgba(37, 99, 235, .25);
        font-size: 2.2rem;
        font-weight: 700;
        color: #fff;
        font-family: 'Syne', sans-serif;
    }

    .digi-badge {
        display: inline-block;
        padding: .2rem .7rem;
        border-radius: 99px;
        font-size: .75rem;
        font-weight: 600;
    }

    .digi-badge--success {
        background: #d1fae5;
        color: #065f46;
    }

    .digi-badge--danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .digi-badge--muted {
        background: #f1f5f9;
        color: #475569;
    }
</style>

<script>
    (function() {
        const MONTO_BASE = {{ $montoBase }};
        const MONTO_ANUAL = {{ $montoAnual }};
        const RECARGO = {{ $recargoUnit }};
        const TIENE_RECARGO = {{ $tieneRecargo ? 'true' : 'false' }};

        let planActual = {
            tipo: 'mensual',
            meses: 1,
            monto: MONTO_BASE,
            total: MONTO_BASE + (TIENE_RECARGO ? RECARGO : 0)
        };

        function actualizarHiddens() {
            document.getElementById('h_frecuencia').value = planActual.tipo === 'anual' ? 'ANUAL' : 'MENSUAL';
            document.getElementById('h_monto').value = planActual.monto;
            document.getElementById('h_id_tipo_precio').value = planActual.tipo === 'anual' ? 1 : 2;
            document.getElementById('h_meses').value = planActual.meses;
            document.getElementById('h_recargo').value = RECARGO;
            document.getElementById('h_monto_total').value = planActual.total;

            // Actualizar botones de pago
            const fmt = new Intl.NumberFormat('es-MX').format(planActual.total);
            ['btnCardTxt', 'btnRecurringTxt', 'btnCashTxt'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = `Pagar $${fmt} MXN`;
            });
            const btnTokenTxt = document.getElementById('btnTokenTxt');
            if (btnTokenTxt && window.selectedToken) btnTokenTxt.textContent = `Pagar $${fmt} MXN`;

            // Disparar evento para que otros scripts lo lean
            document.dispatchEvent(new CustomEvent('planChanged', {
                detail: planActual
            }));
        }

        // ── Tabs tipo ──────────────────────────────────
        document.querySelectorAll('.plan-tipo-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.plan-tipo-btn').forEach(b => b.classList.remove(
                    'active'));
                document.querySelectorAll('.plan-panel').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                const tipo = btn.dataset.tipo;
                document.getElementById(`panel-${tipo}`).classList.add('active');

                if (tipo === 'anual') {
                    planActual = {
                        tipo: 'anual',
                        meses: 12,
                        monto: MONTO_ANUAL,
                        total: MONTO_ANUAL + (TIENE_RECARGO ? RECARGO : 0)
                    };
                } else {
                    // Restaurar selección de meses activa
                    const mesBtnActive = document.querySelector('.mes-btn.active');
                    const meses = mesBtnActive ? +mesBtnActive.dataset.meses : 1;
                    planActual = {
                        tipo: 'mensual',
                        meses,
                        monto: MONTO_BASE * meses,
                        total: MONTO_BASE * meses + (TIENE_RECARGO ? RECARGO : 0)
                    };
                }
                actualizarHiddens();
            });
        });

        // ── Selector de meses ──────────────────────────
        document.querySelectorAll('.mes-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.mes-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const meses = +btn.dataset.meses;
                const monto = MONTO_BASE * meses;
                const total = monto + (TIENE_RECARGO ? RECARGO : 0);

                planActual = {
                    tipo: 'mensual',
                    meses,
                    monto,
                    total
                };

                // Actualizar desglose
                const lblMeses = document.getElementById('lblMeses');
                const lblMonto = document.getElementById('lblMontoMeses');
                const lblTotal = document.getElementById('lblTotal');
                if (lblMeses) lblMeses.textContent = meses;
                if (lblMonto) lblMonto.textContent =
                    `$${new Intl.NumberFormat('es-MX').format(monto)} MXN`;
                if (lblTotal) lblTotal.textContent =
                    `$${new Intl.NumberFormat('es-MX').format(total)} MXN`;

                actualizarHiddens();
            });
        });

        // Inicializar
        actualizarHiddens();
    })();
</script>
