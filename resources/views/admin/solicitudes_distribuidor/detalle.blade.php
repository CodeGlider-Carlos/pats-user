@extends('layouts.app')

@section('title', 'Solicitud #' . $solicitud->id_solicitud)

@section('content')
<style>
:root {
    --sd-border:#e2e8f0; --sd-navy:#f8fafc; --sd-blue:#2563eb; --sd-cream:#1e3a5f;
    --sd-text:#1e293b;   --sd-muted:#64748b; --sd-white:#ffffff;
    --sd-radius:12px;    --sd-shadow:0 1px 3px rgba(0,0,0,.08);
}

.sd-wrap { width:100%; max-width:1300px; margin:0 auto; }

/* Breadcrumb */
.sd-bc { display:flex; align-items:center; gap:.4rem; font-size:.8rem; color:var(--sd-muted); margin-bottom:1.5rem; }
.sd-bc a { color:var(--sd-blue); text-decoration:none; }
.sd-bc a:hover { text-decoration:underline; }

/* Layout 2 col */
.sd-layout { display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start; }
@media(max-width:1024px) { .sd-layout { grid-template-columns:1fr; } }

/* Tarjeta base */
.sd-card { background:var(--sd-white); border-radius:var(--sd-radius); border:1px solid var(--sd-border); box-shadow:var(--sd-shadow); margin-bottom:1.25rem; overflow:hidden; }
.sd-card__head { padding:1rem 1.25rem; border-bottom:1px solid var(--sd-border); background:linear-gradient(to right,var(--sd-white),var(--sd-navy)); display:flex; align-items:center; gap:.6rem; }
.sd-card__head-title { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; color:var(--sd-cream); margin:0; }
.sd-card__head i { color:var(--sd-blue); font-size:1.2rem; }
.sd-card__body { padding:1.25rem; }

/* Grid de info */
.sd-info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; }
@media(max-width:600px) { .sd-info-grid { grid-template-columns:1fr; } }
.sd-info-grid--3 { grid-template-columns:repeat(3,1fr); }
@media(max-width:768px) { .sd-info-grid--3 { grid-template-columns:repeat(2,1fr); } }
.sd-field { display:flex; flex-direction:column; gap:.2rem; }
.sd-field__label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--sd-muted); }
.sd-field__value { font-size:.9rem; color:var(--sd-text); font-weight:500; word-break:break-all; }
.sd-field__value.sd-mono { font-family:monospace; letter-spacing:.03em; }

/* Header hero de solicitud */
.sd-hero { background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%); color:#fff; border-radius:var(--sd-radius); padding:1.5rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; }
.sd-hero__id { font-size:.78rem; font-weight:700; opacity:.7; text-transform:uppercase; letter-spacing:.08em; }
.sd-hero__name { font-family:'Syne',sans-serif; font-size:1.6rem; font-weight:700; margin:.25rem 0; }
.sd-hero__meta { font-size:.875rem; opacity:.8; }
.sd-hero__right { display:flex; flex-direction:column; align-items:flex-end; gap:.5rem; }

/* Stepper de estatus */
.sd-stepper { display:flex; gap:0; overflow-x:auto; margin-bottom:1.5rem; background:var(--sd-white); border-radius:var(--sd-radius); border:1px solid var(--sd-border); padding:.75rem 1rem; }
.sd-step { display:flex; flex-direction:column; align-items:center; gap:.3rem; flex:1; min-width:80px; position:relative; }
.sd-step:not(:last-child)::after { content:''; position:absolute; top:14px; left:calc(50% + 14px); width:calc(100% - 28px); height:2px; background:var(--sd-border); z-index:0; }
.sd-step.is-done::after { background:var(--sd-blue); }
.sd-step__dot { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; border:2px solid var(--sd-border); background:var(--sd-white); color:var(--sd-muted); position:relative; z-index:1; transition:all .2s; }
.sd-step.is-done .sd-step__dot { background:var(--sd-blue); border-color:var(--sd-blue); color:#fff; }
.sd-step.is-current .sd-step__dot { background:#fff; border-color:var(--sd-blue); color:var(--sd-blue); box-shadow:0 0 0 3px #dbeafe; }
.sd-step.is-rejected .sd-step__dot { background:#ef4444; border-color:#ef4444; color:#fff; }
.sd-step__label { font-size:.65rem; font-weight:600; color:var(--sd-muted); text-align:center; white-space:nowrap; }
.sd-step.is-done .sd-step__label, .sd-step.is-current .sd-step__label { color:var(--sd-blue); }
.sd-step.is-rejected .sd-step__label { color:#ef4444; }

/* Badges */
.sd-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .7rem; border-radius:99px; font-size:.75rem; font-weight:700; }
.sd-badge-warning   { background:#fef3c7; color:#92400e; }
.sd-badge-info      { background:#e0f2fe; color:#0369a1; }
.sd-badge-success   { background:#d1fae5; color:#065f46; }
.sd-badge-danger    { background:#fee2e2; color:#991b1b; }
.sd-badge-primary   { background:#ede9fe; color:#4c1d95; }
.sd-badge-secondary { background:#f1f5f9; color:#475569; }
.sd-badge-dark      { background:#1e293b; color:#f8fafc; }

/* Documentos */
.sd-doc-list { display:flex; flex-direction:column; gap:.6rem; }
.sd-doc-item { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.65rem 1rem; background:var(--sd-navy); border-radius:8px; border:1px solid var(--sd-border); }
.sd-doc-item__info { display:flex; align-items:center; gap:.6rem; }
.sd-doc-item__icon { width:36px; height:36px; border-radius:8px; background:#fff; border:1px solid var(--sd-border); display:flex; align-items:center; justify-content:center; color:var(--sd-blue); font-size:1.1rem; flex-shrink:0; }
.sd-doc-item__name { font-size:.83rem; font-weight:600; color:var(--sd-cream); }
.sd-doc-item__sub  { font-size:.72rem; color:var(--sd-muted); }
.sd-doc-btn { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .75rem; border-radius:6px; font-size:.78rem; font-weight:600; text-decoration:none; color:var(--sd-blue); background:#eff6ff; border:1px solid #bfdbfe; transition:all .15s; flex-shrink:0; }
.sd-doc-btn:hover { background:var(--sd-blue); color:#fff; border-color:var(--sd-blue); }

/* Biometría */
.sd-bio-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
@media(max-width:768px) { .sd-bio-grid { grid-template-columns:1fr; } }
.sd-bio-item { border-radius:10px; border:1px solid var(--sd-border); overflow:hidden; background:var(--sd-navy); }
.sd-bio-item__label { padding:.5rem .75rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--sd-muted); border-bottom:1px solid var(--sd-border); background:var(--sd-white); display:flex; align-items:center; justify-content:space-between; }
.sd-bio-item__img { display:flex; align-items:center; justify-content:center; padding:1rem; min-height:160px; }
.sd-bio-item__img img { max-width:100%; max-height:200px; border-radius:8px; object-fit:contain; }

/* Tabla de esquema */
.sd-esquema-table { width:100%; border-collapse:collapse; font-size:.83rem; }
.sd-esquema-table th { padding:.5rem .75rem; background:var(--sd-navy); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--sd-muted); border-bottom:1px solid var(--sd-border); text-align:left; }
.sd-esquema-table td { padding:.55rem .75rem; border-bottom:1px solid var(--sd-border); color:var(--sd-text); }
.sd-esquema-table tr:last-child td { border-bottom:none; }
.sd-esquema-table tbody tr:hover { background:var(--sd-navy); }

/* Historial */
.sd-timeline { display:flex; flex-direction:column; gap:0; }
.sd-tl-item { display:flex; gap:.75rem; position:relative; padding-bottom:1.25rem; }
.sd-tl-item:last-child { padding-bottom:0; }
.sd-tl-item:last-child::before { display:none; }
.sd-tl-item::before { content:''; position:absolute; left:15px; top:28px; bottom:0; width:2px; background:var(--sd-border); }
.sd-tl-dot { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; margin-top:2px; position:relative; z-index:1; }
.sd-tl-content { flex:1; min-width:0; }
.sd-tl-title { font-size:.83rem; font-weight:700; color:var(--sd-cream); }
.sd-tl-sub { font-size:.75rem; color:var(--sd-muted); margin-top:.15rem; }
.sd-tl-date { font-size:.7rem; color:var(--sd-muted); }

/* Panel de acciones */
.sd-action-panel { position:sticky; top:80px; }
.sd-actions-card { background:var(--sd-white); border-radius:var(--sd-radius); border:1px solid var(--sd-border); box-shadow:var(--sd-shadow); overflow:hidden; margin-bottom:1.25rem; }
.sd-actions-head { padding:.9rem 1.1rem; background:linear-gradient(to right,var(--sd-white),var(--sd-navy)); border-bottom:1px solid var(--sd-border); font-family:'Syne',sans-serif; font-size:.9rem; font-weight:700; color:var(--sd-cream); display:flex; align-items:center; gap:.5rem; }
.sd-actions-body { padding:1rem; display:flex; flex-direction:column; gap:.6rem; }
.sd-action-btn { display:flex; align-items:center; gap:.5rem; width:100%; padding:.65rem 1rem; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; border:none; transition:all .15s; justify-content:center; }
.sd-action-btn:disabled { opacity:.5; cursor:not-allowed; }
.sd-action-btn-info      { background:#e0f2fe; color:#0369a1; }
.sd-action-btn-success   { background:#d1fae5; color:#065f46; }
.sd-action-btn-danger    { background:#fee2e2; color:#991b1b; }
.sd-action-btn-primary   { background:#ede9fe; color:#4c1d95; }
.sd-action-btn-secondary { background:#f1f5f9; color:#475569; }
.sd-action-btn-dark      { background:#1e293b; color:#f8fafc; }
.sd-action-btn:hover:not(:disabled) { filter:brightness(.92); transform:translateY(-1px); }
.sd-no-actions { text-align:center; padding:1.5rem 1rem; color:var(--sd-muted); font-size:.85rem; }
.sd-no-actions i { font-size:1.75rem; display:block; margin-bottom:.5rem; opacity:.4; }

/* Modal genérico */
.sd-modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.6); z-index:9999; align-items:center; justify-content:center; padding:1rem; }
.sd-modal-overlay.is-open { display:flex; }
.sd-modal { background:#fff; border-radius:var(--sd-radius); width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.25); animation:sdFadeIn .15s ease; overflow:hidden; }
@keyframes sdFadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.sd-modal__head { padding:1.1rem 1.25rem; border-bottom:1px solid var(--sd-border); display:flex; justify-content:space-between; align-items:center; }
.sd-modal__title { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; color:var(--sd-cream); }
.sd-modal__close { background:none; border:none; cursor:pointer; color:var(--sd-muted); font-size:1.25rem; line-height:1; padding:.2rem; }
.sd-modal__body { padding:1.25rem; }
.sd-modal__footer { padding:1rem 1.25rem; border-top:1px solid var(--sd-border); background:var(--sd-navy); display:flex; gap:.6rem; justify-content:flex-end; }
.sd-modal textarea, .sd-modal input[type=file] { width:100%; border:1px solid var(--sd-border); border-radius:8px; padding:.6rem .75rem; font-size:.875rem; color:var(--sd-text); outline:none; resize:vertical; font-family:inherit; }
.sd-modal textarea:focus, .sd-modal input[type=file]:focus { border-color:var(--sd-blue); }
.sd-modal label { font-size:.78rem; font-weight:700; color:var(--sd-muted); text-transform:uppercase; letter-spacing:.04em; display:block; margin-bottom:.4rem; }
.sd-modal-note { font-size:.8rem; color:var(--sd-muted); margin-top:.5rem; }
.sd-btn-cancel { padding:.6rem 1rem; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; border:1px solid var(--sd-border); background:var(--sd-white); color:var(--sd-muted); }
.sd-btn-confirm { padding:.6rem 1.25rem; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; border:none; color:#fff; }
.sd-btn-confirm.sd-bg-info      { background:#0284c7; }
.sd-btn-confirm.sd-bg-success   { background:#059669; }
.sd-btn-confirm.sd-bg-danger    { background:#dc2626; }
.sd-btn-confirm.sd-bg-primary   { background:#7c3aed; }
.sd-btn-confirm.sd-bg-secondary { background:#64748b; }
.sd-btn-confirm.sd-bg-dark      { background:#1e293b; }
.sd-btn-confirm:disabled { opacity:.5; cursor:not-allowed; }

/* Toast */
.sd-toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:99999; display:flex; flex-direction:column; gap:.5rem; }
.sd-toast-item { padding:.75rem 1.1rem; border-radius:10px; font-size:.875rem; font-weight:500; color:#fff; box-shadow:0 4px 16px rgba(0,0,0,.2); animation:sdFadeIn .2s ease; display:flex; align-items:center; gap:.5rem; min-width:260px; }
.sd-toast-ok  { background:#059669; }
.sd-toast-err { background:#dc2626; }

/* Motivo de rechazo callout */
.sd-callout-danger { background:#fee2e2; border-left:4px solid #ef4444; border-radius:0 8px 8px 0; padding:.75rem 1rem; font-size:.875rem; color:#991b1b; margin-top:.75rem; }
.sd-callout-danger strong { display:block; margin-bottom:.25rem; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; }

/* Sección de observaciones admin */
.sd-obs-box { background:#fefce8; border:1px solid #fde68a; border-radius:8px; padding:.75rem 1rem; font-size:.875rem; color:#78350f; margin-top:.75rem; }
</style>

<div class="sd-wrap">

    {{-- Breadcrumb --}}
    <div class="sd-bc">
        <a href="{{ route('admin.dist.index') }}"><i class="mdi mdi-account-group"></i> Solicitudes</a>
        <i class="mdi mdi-chevron-right"></i>
        <span>Solicitud #{{ $solicitud->id_solicitud }}</span>
    </div>

    {{-- Hero --}}
    @php
        $heroInfo = $estatusLabels[$solicitud->estatus] ?? ['texto' => $solicitud->estatus, 'badge' => 'secondary'];
    @endphp
    <div class="sd-hero">
        <div>
            <div class="sd-hero__id">Solicitud #{{ $solicitud->id_solicitud }}</div>
            <div class="sd-hero__name">{{ $solicitud->nombre }}</div>
            <div class="sd-hero__meta">
                {{ $solicitud->correo }}
                @if($solicitud->rfc) &nbsp;·&nbsp; RFC: {{ $solicitud->rfc }} @endif
                &nbsp;·&nbsp; {{ $solicitud->region }}
            </div>
        </div>
        <div class="sd-hero__right">
            <span class="sd-badge sd-badge-{{ $heroInfo['badge'] }}" style="font-size:.85rem; padding:.35rem 1rem;">
                {{ $heroInfo['texto'] }}
            </span>
            <span style="font-size:.78rem; opacity:.7;">
                {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    {{-- Stepper --}}
    @php
        $stepMap = [
            'ENVIADA'           => 1,
            'EN_REVISION'       => 2,
            'APROBADA'          => 3,
            'CONTRATO_ENVIADO'  => 4,
            'CONTRATO_RECIBIDO' => 5,
            'CONVERTIDA_ALTA'   => 6,
            'RECHAZADA'         => 99,
        ];
        $steps = [
            1 => 'Enviada',
            2 => 'En Revisión',
            3 => 'Aprobada',
            4 => 'Contrato Env.',
            5 => 'Contrato Rec.',
            6 => 'Alta',
        ];
        $currentStep = $stepMap[$solicitud->estatus] ?? 0;
        $isRechazada = $solicitud->estatus === 'RECHAZADA';
    @endphp
    <div class="sd-stepper">
        @foreach($steps as $n => $label)
            @php
                $isDone    = (! $isRechazada) && $currentStep > $n;
                $isCurrent = (! $isRechazada) && $currentStep === $n;
                $classes   = $isDone ? 'is-done' : ($isCurrent ? 'is-current' : '');
                if ($isRechazada && $n === 2) $classes = 'is-rejected';
            @endphp
            <div class="sd-step {{ $classes }}">
                <div class="sd-step__dot">
                    @if($isDone)
                        <i class="mdi mdi-check"></i>
                    @elseif($isRechazada && $n === 2)
                        <i class="mdi mdi-close"></i>
                    @else
                        {{ $n }}
                    @endif
                </div>
                <div class="sd-step__label">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    {{-- Motivo de rechazo --}}
    @if($solicitud->estatus === 'RECHAZADA' && $solicitud->motivo_rechazo)
        <div class="sd-callout-danger" style="margin-bottom:1.25rem;">
            <strong><i class="mdi mdi-alert-circle"></i> Motivo de Rechazo</strong>
            {{ $solicitud->motivo_rechazo }}
        </div>
    @endif

    {{-- Layout 2 columnas --}}
    <div class="sd-layout">

        {{-- ── Columna izquierda ──────────────────────────────────────────── --}}
        <div>

            {{-- Datos del Titular --}}
            <div class="sd-card">
                <div class="sd-card__head">
                    <i class="mdi mdi-account-circle"></i>
                    <h3 class="sd-card__head-title">Datos del Titular</h3>
                </div>
                <div class="sd-card__body">
                    <div class="sd-info-grid">
                        <div class="sd-field">
                            <div class="sd-field__label">Nombre completo</div>
                            <div class="sd-field__value">{{ $solicitud->nombre }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Tipo de persona</div>
                            <div class="sd-field__value">
                                <span class="sd-badge {{ $solicitud->tipo_persona === 'MORAL' ? 'sd-badge-primary' : 'sd-badge-info' }}">
                                    {{ $solicitud->tipo_persona }}
                                </span>
                            </div>
                        </div>
                        @if($solicitud->razon_social)
                            <div class="sd-field" style="grid-column:1/-1;">
                                <div class="sd-field__label">Razón social</div>
                                <div class="sd-field__value">{{ $solicitud->razon_social }}</div>
                            </div>
                        @endif
                        <div class="sd-field">
                            <div class="sd-field__label">RFC</div>
                            <div class="sd-field__value sd-mono">{{ $solicitud->rfc ?: '—' }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Teléfono</div>
                            <div class="sd-field__value sd-mono">{{ $solicitud->telefono }}</div>
                        </div>
                        <div class="sd-field" style="grid-column:1/-1;">
                            <div class="sd-field__label">Correo electrónico</div>
                            <div class="sd-field__value">{{ $solicitud->correo }}</div>
                        </div>
                        <div class="sd-field" style="grid-column:1/-1;">
                            <div class="sd-field__label">Dirección</div>
                            <div class="sd-field__value">{{ $solicitud->direccion }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">País</div>
                            <div class="sd-field__value">{{ $solicitud->pais }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Región</div>
                            <div class="sd-field__value sd-mono">{{ $solicitud->region }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datos Bancarios --}}
            <div class="sd-card">
                <div class="sd-card__head">
                    <i class="mdi mdi-bank"></i>
                    <h3 class="sd-card__head-title">Datos Bancarios</h3>
                </div>
                <div class="sd-card__body">
                    <div class="sd-info-grid">
                        <div class="sd-field">
                            <div class="sd-field__label">Banco</div>
                            <div class="sd-field__value">{{ $solicitud->banco ?: '—' }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Número de cuenta</div>
                            <div class="sd-field__value sd-mono">{{ $solicitud->numero_cuenta ?: '—' }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">CLABE interbancaria</div>
                            <div class="sd-field__value sd-mono">{{ $solicitud->clabe ?: '—' }}</div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Titular de cuenta</div>
                            <div class="sd-field__value">{{ $solicitud->titular_cuenta ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Plan Financiero --}}
            <div class="sd-card">
                <div class="sd-card__head">
                    <i class="mdi mdi-cash-multiple"></i>
                    <h3 class="sd-card__head-title">Plan Financiero</h3>
                </div>
                <div class="sd-card__body">
                    <div class="sd-info-grid sd-info-grid--3">
                        <div class="sd-field">
                            <div class="sd-field__label">Modalidad</div>
                            <div class="sd-field__value">
                                <span class="sd-badge {{ $solicitud->modalidad_pago === 'CONTADO' ? 'sd-badge-info' : 'sd-badge-warning' }}">
                                    {{ $solicitud->modalidad_pago }}
                                </span>
                            </div>
                        </div>
                        <div class="sd-field">
                            <div class="sd-field__label">Valor total</div>
                            <div class="sd-field__value" style="font-size:1.1rem; font-weight:700; color:var(--sd-blue);">
                                ${{ number_format($solicitud->valor_total, 2) }}
                            </div>
                        </div>
                        @if($solicitud->modalidad_pago !== 'CONTADO')
                            <div class="sd-field">
                                <div class="sd-field__label">Plazo</div>
                                <div class="sd-field__value">{{ $solicitud->plazo_meses }} meses</div>
                            </div>
                            <div class="sd-field">
                                <div class="sd-field__label">Periodicidad</div>
                                <div class="sd-field__value">{{ $solicitud->periodicidad }}</div>
                            </div>
                            <div class="sd-field">
                                <div class="sd-field__label">Primer vencimiento</div>
                                <div class="sd-field__value">
                                    {{ $solicitud->fecha_primer_vencimiento
                                        ? \Carbon\Carbon::parse($solicitud->fecha_primer_vencimiento)->format('d/m/Y')
                                        : '—' }}
                                </div>
                            </div>
                            <div class="sd-field">
                                <div class="sd-field__label">Saldo financiado</div>
                                <div class="sd-field__value">
                                    ${{ number_format($solicitud->saldo_financiado, 2) }}
                                </div>
                            </div>
                        @endif
                        <div class="sd-field">
                            <div class="sd-field__label">Fecha inicio</div>
                            <div class="sd-field__value">
                                {{ $solicitud->fecha_inicio
                                    ? \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y')
                                    : '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- Esquema de pagos --}}
                    @if($esquema && count($esquema) > 0)
                        <div style="margin-top:1.25rem;">
                            <div style="font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--sd-muted); margin-bottom:.6rem;">
                                Esquema de pagos ({{ count($esquema) }} parcialidades)
                            </div>
                            <div style="overflow-x:auto; max-height:240px; overflow-y:auto; border:1px solid var(--sd-border); border-radius:8px;">
                                <table class="sd-esquema-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($esquema as $pago)
                                            <tr>
                                                <td>{{ $pago['parcialidad'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</td>
                                                <td style="font-weight:600;">${{ number_format($pago['monto'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Documentos --}}
            <div class="sd-card">
                <div class="sd-card__head">
                    <i class="mdi mdi-folder-multiple"></i>
                    <h3 class="sd-card__head-title">Documentos</h3>
                </div>
                <div class="sd-card__body">
                    @php
                        $docLabels = [
                            'INE'                  => ['label' => 'INE / IFE',              'icono' => 'mdi-card-account-details'],
                            'COMPROBANTE_DOMICILIO' => ['label' => 'Comprobante de domicilio','icono' => 'mdi-home-city'],
                            'CEDULA_FISCAL'        => ['label' => 'Cédula fiscal',           'icono' => 'mdi-file-document'],
                            'CONTRATO_FIRMADO'     => ['label' => 'Contrato firmado',        'icono' => 'mdi-file-sign'],
                            'CARATULA_BANCARIA'    => ['label' => 'Carátula bancaria',       'icono' => 'mdi-credit-card'],
                            'ACTA_CONSTITUTIVA'    => ['label' => 'Acta constitutiva',       'icono' => 'mdi-briefcase'],
                            'PODER_NOTARIAL'       => ['label' => 'Poder notarial',          'icono' => 'mdi-scale-balance'],
                        ];
                    @endphp
                    @if($documentos->isEmpty())
                        <p style="color:var(--sd-muted); font-size:.875rem; text-align:center; padding:1rem 0;">
                            Sin documentos registrados.
                        </p>
                    @else
                        <div class="sd-doc-list">
                            @foreach($documentos as $doc)
                                @php
                                    $dl = $docLabels[$doc->tipo_documento] ?? ['label' => $doc->tipo_documento, 'icono' => 'mdi-file'];
                                @endphp
                                <div class="sd-doc-item">
                                    <div class="sd-doc-item__info">
                                        <div class="sd-doc-item__icon">
                                            <i class="mdi {{ $dl['icono'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="sd-doc-item__name">{{ $dl['label'] }}</div>
                                            <div class="sd-doc-item__sub">
                                                {{ $doc->archivo_nombre_original }}
                                                &nbsp;·&nbsp; {{ $doc->size_kb }} KB
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => $doc->tipo_documento]) }}"
                                       target="_blank" class="sd-doc-btn">
                                        <i class="mdi mdi-eye"></i> Ver
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Contrato admin (si existe) --}}
                    @if($solicitud->contrato_admin_path)
                        <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--sd-border);">
                            <div style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--sd-muted); margin-bottom:.5rem;">
                                Contrato Generado por Admin
                            </div>
                            <div class="sd-doc-item">
                                <div class="sd-doc-item__info">
                                    <div class="sd-doc-item__icon" style="background:#ede9fe; border-color:#c4b5fd;">
                                        <i class="mdi mdi-file-pdf" style="color:#7c3aed;"></i>
                                    </div>
                                    <div>
                                        <div class="sd-doc-item__name">Contrato de distribución</div>
                                        <div class="sd-doc-item__sub">
                                            Enviado {{ $solicitud->fecha_envio_contrato
                                                ? \Carbon\Carbon::parse($solicitud->fecha_envio_contrato)->format('d/m/Y H:i')
                                                : '' }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'contrato_admin']) }}"
                                   target="_blank" class="sd-doc-btn">
                                    <i class="mdi mdi-eye"></i> Ver
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Biometría --}}
            @if($preview)
                <div class="sd-card">
                    <div class="sd-card__head">
                        <i class="mdi mdi-face-recognition"></i>
                        <h3 class="sd-card__head-title">Biometría</h3>
                    </div>
                    <div class="sd-card__body">
                        <div class="sd-bio-grid">
                            {{-- Selfie --}}
                            @if($preview->selfie_path)
                                <div class="sd-bio-item">
                                    <div class="sd-bio-item__label">
                                        <span><i class="mdi mdi-camera"></i> Selfie del titular</span>
                                        <a href="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'selfie']) }}"
                                           target="_blank" class="sd-doc-btn" style="padding:.15rem .5rem; font-size:.7rem;">
                                            <i class="mdi mdi-open-in-new"></i>
                                        </a>
                                    </div>
                                    <div class="sd-bio-item__img">
                                        <img src="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'selfie']) }}"
                                             alt="Selfie" loading="lazy">
                                    </div>
                                </div>
                            @endif
                            {{-- Firma --}}
                            @if($preview->firma_path)
                                <div class="sd-bio-item">
                                    <div class="sd-bio-item__label">
                                        <span><i class="mdi mdi-draw"></i> Firma digital</span>
                                        <a href="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'firma']) }}"
                                           target="_blank" class="sd-doc-btn" style="padding:.15rem .5rem; font-size:.7rem;">
                                            <i class="mdi mdi-open-in-new"></i>
                                        </a>
                                    </div>
                                    <div class="sd-bio-item__img" style="background:#f1f5f9;">
                                        <img src="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'firma']) }}"
                                             alt="Firma" loading="lazy">
                                    </div>
                                </div>
                            @endif
                            {{-- Contrato preview --}}
                            @if($preview->contrato_path)
                                <div class="sd-bio-item">
                                    <div class="sd-bio-item__label">
                                        <span><i class="mdi mdi-file-sign"></i> Contrato firmado</span>
                                        <a href="{{ route('admin.dist.archivo', ['id' => $solicitud->id_solicitud, 'tipo' => 'contrato_preview']) }}"
                                           target="_blank" class="sd-doc-btn" style="padding:.15rem .5rem; font-size:.7rem;">
                                            <i class="mdi mdi-open-in-new"></i>
                                        </a>
                                    </div>
                                    <div class="sd-bio-item__img" style="min-height:120px; display:flex; align-items:center; justify-content:center; color:var(--sd-muted); flex-direction:column; gap:.5rem;">
                                        <i class="mdi mdi-file-pdf" style="font-size:2.5rem; color:#7c3aed;"></i>
                                        <span style="font-size:.78rem;">Ver contrato firmado</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Observaciones admin (si existen) --}}
            @if($solicitud->observaciones_admin)
                <div class="sd-obs-box" style="margin-bottom:1.25rem;">
                    <strong style="font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; display:block; margin-bottom:.25rem; color:#92400e;">
                        <i class="mdi mdi-note-text"></i> Observaciones del Admin
                    </strong>
                    {{ $solicitud->observaciones_admin }}
                </div>
            @endif

        </div>

        {{-- ── Columna derecha ─────────────────────────────────────────────── --}}
        <div class="sd-action-panel">

            {{-- Panel de acciones --}}
            <div class="sd-actions-card">
                <div class="sd-actions-head">
                    <i class="mdi mdi-lightning-bolt"></i> Acciones del flujo
                </div>
                <div class="sd-actions-body" id="actionsBody">
                    @if(count($transicionesDisponibles) > 0)
                        @foreach($transicionesDisponibles as $accion => $estatusNuevo)
                            @php $meta = $accionMeta[$accion] ?? ['label' => $accion, 'color' => 'secondary', 'icono' => 'mdi-arrow-right']; @endphp
                            <button type="button"
                                    class="sd-action-btn sd-action-btn-{{ $meta['color'] }}"
                                    data-accion="{{ $accion }}"
                                    data-color="{{ $meta['color'] }}"
                                    onclick="abrirModal('{{ $accion }}')">
                                <i class="mdi {{ $meta['icono'] }}"></i>
                                {{ $meta['label'] }}
                            </button>
                        @endforeach
                    @else
                        <div class="sd-no-actions">
                            <i class="mdi mdi-check-all"></i>
                            @if($solicitud->estatus === 'CONVERTIDA_ALTA')
                                Esta solicitud fue convertida a alta de distribuidor.
                            @elseif($solicitud->estatus === 'RECHAZADA')
                                Esta solicitud fue rechazada. No hay acciones disponibles.
                            @else
                                No hay acciones disponibles para este estado.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen de hitos --}}
            <div class="sd-actions-card">
                <div class="sd-actions-head">
                    <i class="mdi mdi-calendar-clock"></i> Hitos de fechas
                </div>
                <div style="padding:1rem; display:flex; flex-direction:column; gap:.65rem;">
                    @php
                        $hitos = [
                            ['label' => 'Fecha de envío',          'value' => $solicitud->created_at],
                            ['label' => 'Envío de contrato',       'value' => $solicitud->fecha_envio_contrato],
                            ['label' => 'Contrato recibido',       'value' => $solicitud->fecha_carga_firmado],
                            ['label' => 'Autorización',            'value' => $solicitud->fecha_autorizacion],
                            ['label' => 'Conversión a alta',       'value' => $solicitud->fecha_conversion_alta],
                        ];
                    @endphp
                    @foreach($hitos as $hito)
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:.8rem;">
                            <span style="color:var(--sd-muted);">{{ $hito['label'] }}</span>
                            <span style="font-weight:600; color:{{ $hito['value'] ? 'var(--sd-cream)' : 'var(--sd-border)' }};">
                                {{ $hito['value'] ? \Carbon\Carbon::parse($hito['value'])->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Historial --}}
            <div class="sd-actions-card">
                <div class="sd-actions-head">
                    <i class="mdi mdi-history"></i> Historial de eventos
                </div>
                <div style="padding:1rem 1rem 1rem;">
                    @if($historial->isEmpty())
                        <p style="font-size:.8rem; color:var(--sd-muted); text-align:center; padding:.5rem 0;">Sin historial.</p>
                    @else
                        <div class="sd-timeline">
                            @php
                                $tlColors = [
                                    'solicitud_enviada' => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'icono' => 'mdi-send'],
                                    'iniciar_revision'  => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'icono' => 'mdi-magnify'],
                                    'aprobar'           => ['bg' => '#d1fae5', 'color' => '#059669', 'icono' => 'mdi-check'],
                                    'rechazar'          => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icono' => 'mdi-close'],
                                    'enviar_contrato'   => ['bg' => '#ede9fe', 'color' => '#7c3aed', 'icono' => 'mdi-file-send'],
                                    'marcar_recibido'   => ['bg' => '#f1f5f9', 'color' => '#475569', 'icono' => 'mdi-file-check'],
                                    'convertir_alta'    => ['bg' => '#1e293b', 'color' => '#f8fafc', 'icono' => 'mdi-account-check'],
                                ];
                            @endphp
                            @foreach($historial as $evento)
                                @php
                                    $tlc = $tlColors[$evento->evento_tipo] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'icono' => 'mdi-circle-small'];
                                @endphp
                                <div class="sd-tl-item">
                                    <div class="sd-tl-dot" style="background:{{ $tlc['bg'] }}; color:{{ $tlc['color'] }};">
                                        <i class="mdi {{ $tlc['icono'] }}" style="font-size:.85rem;"></i>
                                    </div>
                                    <div class="sd-tl-content">
                                        <div class="sd-tl-title">
                                            {{ $accionMeta[$evento->evento_tipo]['label'] ?? ucfirst(str_replace('_', ' ', $evento->evento_tipo)) }}
                                        </div>
                                        @if($evento->estatus_anterior && $evento->estatus_nuevo)
                                            <div class="sd-tl-sub">
                                                {{ $evento->estatus_anterior }} → {{ $evento->estatus_nuevo }}
                                            </div>
                                        @endif
                                        @if(isset($evento->payload['motivo']))
                                            <div class="sd-tl-sub" style="color:#dc2626;">
                                                {{ Str::limit($evento->payload['motivo'], 60) }}
                                            </div>
                                        @endif
                                        <div class="sd-tl-date">
                                            {{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ── MODALES ──────────────────────────────────────────────────────────── --}}

{{-- Modal: Rechazar --}}
<div class="sd-modal-overlay" id="modal-rechazar">
    <div class="sd-modal">
        <div class="sd-modal__head">
            <span class="sd-modal__title"><i class="mdi mdi-close-circle" style="color:#dc2626;"></i> Rechazar solicitud</span>
            <button class="sd-modal__close" onclick="cerrarModal('rechazar')">&#215;</button>
        </div>
        <div class="sd-modal__body">
            <label for="motivo_rechazo">Motivo de rechazo *</label>
            <textarea id="motivo_rechazo" rows="4" placeholder="Describe el motivo del rechazo…"></textarea>
            <div class="sd-modal-note">Este motivo quedará registrado en el historial y visible en la solicitud.</div>
        </div>
        <div class="sd-modal__footer">
            <button class="sd-btn-cancel" onclick="cerrarModal('rechazar')">Cancelar</button>
            <button class="sd-btn-confirm sd-bg-danger" id="btn-confirm-rechazar"
                    onclick="ejecutarAccion('rechazar')">
                <i class="mdi mdi-close-circle"></i> Confirmar rechazo
            </button>
        </div>
    </div>
</div>

{{-- Modal: Aprobar --}}
<div class="sd-modal-overlay" id="modal-aprobar">
    <div class="sd-modal">
        <div class="sd-modal__head">
            <span class="sd-modal__title"><i class="mdi mdi-check-circle" style="color:#059669;"></i> Aprobar solicitud</span>
            <button class="sd-modal__close" onclick="cerrarModal('aprobar')">&#215;</button>
        </div>
        <div class="sd-modal__body">
            <label for="obs_aprobar">Observaciones (opcional)</label>
            <textarea id="obs_aprobar" rows="3" placeholder="Agrega observaciones o notas internas…"></textarea>
        </div>
        <div class="sd-modal__footer">
            <button class="sd-btn-cancel" onclick="cerrarModal('aprobar')">Cancelar</button>
            <button class="sd-btn-confirm sd-bg-success" id="btn-confirm-aprobar"
                    onclick="ejecutarAccion('aprobar')">
                <i class="mdi mdi-check-circle"></i> Confirmar aprobación
            </button>
        </div>
    </div>
</div>

{{-- Modal: Enviar contrato --}}
<div class="sd-modal-overlay" id="modal-enviar_contrato">
    <div class="sd-modal">
        <div class="sd-modal__head">
            <span class="sd-modal__title"><i class="mdi mdi-file-send" style="color:#7c3aed;"></i> Enviar contrato</span>
            <button class="sd-modal__close" onclick="cerrarModal('enviar_contrato')">&#215;</button>
        </div>
        <div class="sd-modal__body">
            <label for="contrato_admin_file">Archivo del contrato *</label>
            <input type="file" id="contrato_admin_file" accept=".pdf,.doc,.docx">
            <div class="sd-modal-note">El archivo se guardará y asociará a esta solicitud. Se actualizará el estatus a "Contrato Enviado".</div>
        </div>
        <div class="sd-modal__footer">
            <button class="sd-btn-cancel" onclick="cerrarModal('enviar_contrato')">Cancelar</button>
            <button class="sd-btn-confirm sd-bg-primary" id="btn-confirm-enviar_contrato"
                    onclick="ejecutarAccion('enviar_contrato')">
                <i class="mdi mdi-file-send"></i> Enviar contrato
            </button>
        </div>
    </div>
</div>

{{-- Modal: Confirmación genérica (iniciar_revision, marcar_recibido, convertir_alta) --}}
<div class="sd-modal-overlay" id="modal-confirm">
    <div class="sd-modal">
        <div class="sd-modal__head">
            <span class="sd-modal__title" id="modal-confirm-title">Confirmar acción</span>
            <button class="sd-modal__close" onclick="cerrarModal('confirm')">&#215;</button>
        </div>
        <div class="sd-modal__body">
            <p id="modal-confirm-msg" style="color:var(--sd-text); font-size:.9rem; margin:0;"></p>
        </div>
        <div class="sd-modal__footer">
            <button class="sd-btn-cancel" onclick="cerrarModal('confirm')">Cancelar</button>
            <button class="sd-btn-confirm sd-bg-info" id="btn-confirm-generic"
                    onclick="ejecutarAccionGeneric()">
                Confirmar
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="sd-toast" id="sdToast"></div>

@push('scripts')
<script>
const ID_SOLICITUD = {{ $solicitud->id_solicitud }};
const ACCION_URL   = "{{ route('admin.dist.accion', $solicitud->id_solicitud) }}";
const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

let accionGenericActual = null;

// ── Modales ───────────────────────────────────────────────────────────────

const CONFIRM_MSGS = {
    iniciar_revision: { title: 'Iniciar revisión', msg:  'Se marcará la solicitud como "En Revisión". ¿Confirmas?', color: 'info' },
    marcar_recibido:  { title: 'Contrato recibido', msg: 'Se registrará que el contrato fue recibido correctamente. ¿Confirmas?', color: 'secondary' },
    convertir_alta:   { title: 'Convertir a Alta', msg:  'Se convertirá esta solicitud en un alta de distribuidor. Esta acción es irreversible. ¿Confirmas?', color: 'dark' },
};

function abrirModal(accion) {
    const specificModals = ['rechazar', 'aprobar', 'enviar_contrato'];
    if (specificModals.includes(accion)) {
        document.getElementById(`modal-${accion}`).classList.add('is-open');
    } else {
        const cfg = CONFIRM_MSGS[accion] || { title: 'Confirmar', msg: '¿Confirmas esta acción?', color: 'info' };
        document.getElementById('modal-confirm-title').textContent = cfg.title;
        document.getElementById('modal-confirm-msg').textContent   = cfg.msg;
        const btnConfirm = document.getElementById('btn-confirm-generic');
        btnConfirm.className = `sd-btn-confirm sd-bg-${cfg.color}`;
        accionGenericActual = accion;
        document.getElementById('modal-confirm').classList.add('is-open');
    }
}

function cerrarModal(nombre) {
    const overlay = document.getElementById(`modal-${nombre}`);
    if (overlay) overlay.classList.remove('is-open');
}

// Cerrar al click en overlay
document.querySelectorAll('.sd-modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('is-open');
    });
});

// ── Acciones ──────────────────────────────────────────────────────────────

function ejecutarAccion(accion) {
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('accion', accion);

    if (accion === 'rechazar') {
        const motivo = document.getElementById('motivo_rechazo').value.trim();
        if (!motivo) { toast('El motivo de rechazo es obligatorio.', 'err'); return; }
        fd.append('motivo', motivo);
    }

    if (accion === 'aprobar') {
        const obs = document.getElementById('obs_aprobar').value.trim();
        if (obs) fd.append('observaciones', obs);
    }

    if (accion === 'enviar_contrato') {
        const file = document.getElementById('contrato_admin_file').files[0];
        if (!file) { toast('Selecciona un archivo para el contrato.', 'err'); return; }
        fd.append('contrato_admin', file);
    }

    setLoading(accion, true);

    axios.post(ACCION_URL, fd, {
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => {
        if (r.data.ok) {
            cerrarModal(accion);
            toast(`Estatus actualizado: ${r.data.label}`, 'ok');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            toast(r.data.error || 'Error al procesar.', 'err');
        }
    })
    .catch(e => {
        const msg = e.response?.data?.error || 'Error de conexión.';
        toast(msg, 'err');
    })
    .finally(() => setLoading(accion, false));
}

function ejecutarAccionGeneric() {
    if (!accionGenericActual) return;
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('accion', accionGenericActual);

    const btn = document.getElementById('btn-confirm-generic');
    btn.disabled = true;
    btn.textContent = 'Procesando…';

    axios.post(ACCION_URL, fd, {
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => {
        if (r.data.ok) {
            cerrarModal('confirm');
            toast(`Estatus actualizado: ${r.data.label}`, 'ok');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            toast(r.data.error || 'Error al procesar.', 'err');
            btn.disabled = false;
            btn.textContent = 'Confirmar';
        }
    })
    .catch(e => {
        const msg = e.response?.data?.error || 'Error de conexión.';
        toast(msg, 'err');
        btn.disabled = false;
        btn.textContent = 'Confirmar';
    });
}

function setLoading(accion, loading) {
    const btn = document.getElementById(`btn-confirm-${accion}`);
    if (!btn) return;
    btn.disabled = loading;
    btn.textContent = loading ? 'Procesando…' : (btn.dataset.label || 'Confirmar');
}

// ── Toast ─────────────────────────────────────────────────────────────────

function toast(msg, type = 'ok') {
    const el = document.createElement('div');
    el.className = `sd-toast-item sd-toast-${type}`;
    el.innerHTML = `<i class="mdi ${type === 'ok' ? 'mdi-check-circle' : 'mdi-alert-circle'}"></i> ${msg}`;
    document.getElementById('sdToast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush
@endsection
