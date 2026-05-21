@extends('layouts.app')

@section('title', 'Solicitudes de Distribuidor')

@section('content')
<style>
:root {
    --sd-border: #e2e8f0;
    --sd-navy:   #f8fafc;
    --sd-blue:   #2563eb;
    --sd-cream:  #1e3a5f;
    --sd-text:   #1e293b;
    --sd-muted:  #64748b;
    --sd-white:  #ffffff;
    --sd-radius: 12px;
    --sd-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.sd-wrap { width:100%; max-width:1400px; margin:0 auto; }

/* ── Header ─────────────────────────────────────────────────────────── */
.sd-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem; }
.sd-title  { font-family:'Syne',sans-serif; font-size:1.75rem; font-weight:700; color:var(--sd-cream); margin:0; display:flex; align-items:center; gap:.6rem; }
.sd-title i { color:var(--sd-blue); font-size:2rem; }
.sd-subtitle { font-size:.875rem; color:var(--sd-muted); margin:.25rem 0 0; }

/* ── Chips de conteo ─────────────────────────────────────────────────── */
.sd-chips { display:flex; flex-wrap:wrap; gap:.6rem; margin-bottom:1.5rem; }
.sd-chip { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .85rem; border-radius:99px; font-size:.78rem; font-weight:600; cursor:pointer; text-decoration:none; border:2px solid transparent; transition:all .15s; background:var(--sd-white); box-shadow:var(--sd-shadow); color:var(--sd-text); }
.sd-chip:hover { transform:translateY(-1px); box-shadow:0 4px 8px rgba(0,0,0,.1); }
.sd-chip.is-active { border-color:var(--sd-blue); background:#eff6ff; color:var(--sd-blue); }
.sd-chip__count { background:var(--sd-navy); border-radius:99px; padding:.1rem .45rem; font-size:.72rem; font-weight:700; }
.sd-chip.is-active .sd-chip__count { background:var(--sd-blue); color:#fff; }

/* Badge chips */
.chip-warning   { border-left:3px solid #f59e0b; }
.chip-info      { border-left:3px solid #0ea5e9; }
.chip-success   { border-left:3px solid #10b981; }
.chip-danger    { border-left:3px solid #ef4444; }
.chip-primary   { border-left:3px solid #6366f1; }
.chip-secondary { border-left:3px solid #94a3b8; }
.chip-dark      { border-left:3px solid #334155; }

/* ── Filtro ──────────────────────────────────────────────────────────── */
.sd-filters { background:var(--sd-white); border-radius:var(--sd-radius); border:1px solid var(--sd-border); padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; box-shadow:var(--sd-shadow); }
.sd-filters input, .sd-filters select { height:36px; border:1px solid var(--sd-border); border-radius:8px; padding:0 .75rem; font-size:.875rem; color:var(--sd-text); outline:none; transition:border .15s; background:#fff; }
.sd-filters input:focus, .sd-filters select:focus { border-color:var(--sd-blue); }
.sd-filters input { min-width:220px; flex:1; max-width:320px; }
.sd-filters select { min-width:160px; }
.sd-btn { display:inline-flex; align-items:center; gap:.35rem; height:36px; padding:0 1rem; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; border:none; transition:all .15s; text-decoration:none; }
.sd-btn-primary  { background:var(--sd-blue); color:#fff; }
.sd-btn-primary:hover { background:#1d4ed8; color:#fff; }
.sd-btn-ghost    { background:var(--sd-navy); color:var(--sd-muted); border:1px solid var(--sd-border); }
.sd-btn-ghost:hover { background:var(--sd-border); color:var(--sd-text); }

/* ── Tabla ───────────────────────────────────────────────────────────── */
.sd-card { background:var(--sd-white); border-radius:var(--sd-radius); border:1px solid var(--sd-border); box-shadow:var(--sd-shadow); overflow:hidden; }
.sd-card__footer { padding:.75rem 1.25rem; border-top:1px solid var(--sd-border); background:var(--sd-navy); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.5rem; font-size:.8rem; color:var(--sd-muted); }
.sd-table { width:100%; border-collapse:collapse; }
.sd-table th { padding:.75rem 1rem; text-align:left; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--sd-muted); background:var(--sd-navy); border-bottom:1px solid var(--sd-border); white-space:nowrap; }
.sd-table td { padding:.85rem 1rem; border-bottom:1px solid var(--sd-border); font-size:.875rem; color:var(--sd-text); vertical-align:middle; }
.sd-table tr:last-child td { border-bottom:none; }
.sd-table tbody tr:hover { background:#f8fafc; }
.sd-table__name { font-weight:600; color:var(--sd-cream); }
.sd-table__sub  { font-size:.78rem; color:var(--sd-muted); margin-top:.15rem; }

/* ── Badges ──────────────────────────────────────────────────────────── */
.sd-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .65rem; border-radius:99px; font-size:.72rem; font-weight:700; white-space:nowrap; }
.sd-badge-warning   { background:#fef3c7; color:#92400e; }
.sd-badge-info      { background:#e0f2fe; color:#0369a1; }
.sd-badge-success   { background:#d1fae5; color:#065f46; }
.sd-badge-danger    { background:#fee2e2; color:#991b1b; }
.sd-badge-primary   { background:#ede9fe; color:#4c1d95; }
.sd-badge-secondary { background:#f1f5f9; color:#475569; }
.sd-badge-dark      { background:#1e293b; color:#f1f5f9; }

/* ── Botón ver detalle ───────────────────────────────────────────────── */
.sd-btn-ver { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .8rem; border-radius:8px; font-size:.8rem; font-weight:600; color:var(--sd-blue); background:#eff6ff; border:1px solid #bfdbfe; text-decoration:none; transition:all .15s; }
.sd-btn-ver:hover { background:var(--sd-blue); color:#fff; border-color:var(--sd-blue); }

/* ── Vacío ───────────────────────────────────────────────────────────── */
.sd-empty { text-align:center; padding:4rem 1rem; color:var(--sd-muted); }
.sd-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.4; }

/* ── Paginación ──────────────────────────────────────────────────────── */
.sd-pagination { display:flex; gap:.35rem; flex-wrap:wrap; }
.sd-pagination a, .sd-pagination span { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 .5rem; border-radius:8px; font-size:.8rem; font-weight:500; text-decoration:none; border:1px solid var(--sd-border); color:var(--sd-text); background:var(--sd-white); transition:all .15s; }
.sd-pagination a:hover { background:var(--sd-blue); color:#fff; border-color:var(--sd-blue); }
.sd-pagination .active span, .sd-pagination span.active { background:var(--sd-blue); color:#fff; border-color:var(--sd-blue); font-weight:700; }
.sd-pagination span.disabled { opacity:.45; cursor:default; }

@media(max-width:768px) {
    .sd-table__rfc, .sd-table__region, .sd-table__fecha { display:none; }
    .sd-filters input { max-width:100%; }
}
</style>

<div class="sd-wrap">

    {{-- Header --}}
    <div class="sd-header">
        <div>
            <h1 class="sd-title">
                <i class="mdi mdi-account-group"></i> Solicitudes de Distribuidor
            </h1>
            <p class="sd-subtitle">{{ number_format($totalGeneral) }} solicitudes en total &mdash; Gestiona y controla el flujo de cada solicitud</p>
        </div>
    </div>

    {{-- Chips de conteo por estatus --}}
    <div class="sd-chips">
        <a href="{{ route('admin.dist.index') }}"
           class="sd-chip {{ $filtroEstatus === '' ? 'is-active' : '' }}">
            <i class="mdi mdi-view-list"></i> Todas
            <span class="sd-chip__count">{{ $totalGeneral }}</span>
        </a>
        @foreach($estatusLabels as $key => $info)
            @php $cnt = $conteoEstatus[$key] ?? 0; @endphp
            <a href="{{ route('admin.dist.index', ['estatus' => $key] + ($filtroRegion ? ['region' => $filtroRegion] : []) + ($filtroQ ? ['q' => $filtroQ] : [])) }}"
               class="sd-chip chip-{{ $info['badge'] }} {{ $filtroEstatus === $key ? 'is-active' : '' }}">
                {{ $info['texto'] }}
                <span class="sd-chip__count">{{ $cnt }}</span>
            </a>
        @endforeach
    </div>

    {{-- Barra de filtros --}}
    <form method="GET" action="{{ route('admin.dist.index') }}" class="sd-filters">
        <input type="text" name="q" value="{{ $filtroQ }}"
               placeholder="Buscar por nombre, RFC, correo, teléfono…">
        <select name="estatus">
            <option value="">Todos los estatus</option>
            @foreach($estatusLabels as $key => $info)
                <option value="{{ $key }}" {{ $filtroEstatus === $key ? 'selected' : '' }}>{{ $info['texto'] }}</option>
            @endforeach
        </select>
        <button type="submit" class="sd-btn sd-btn-primary">
            <i class="mdi mdi-magnify"></i> Buscar
        </button>
        @if($filtroQ || $filtroEstatus || $filtroRegion)
            <a href="{{ route('admin.dist.index') }}" class="sd-btn sd-btn-ghost">
                <i class="mdi mdi-close"></i> Limpiar
            </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="sd-card">
        <div style="overflow-x:auto;">
            <table class="sd-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titular</th>
                        <th class="sd-table__rfc">RFC</th>
                        <th class="sd-table__region">Región</th>
                        <th>Modalidad</th>
                        <th>Valor Total</th>
                        <th>Estatus</th>
                        <th class="sd-table__fecha">Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $s)
                        @php
                            $info = $estatusLabels[$s->estatus] ?? ['texto' => $s->estatus, 'badge' => 'secondary'];
                        @endphp
                        <tr>
                            <td style="color:var(--sd-muted); font-size:.78rem; font-weight:600;">
                                #{{ $s->id_solicitud }}
                            </td>
                            <td>
                                <div class="sd-table__name">{{ $s->nombre }}</div>
                                @if($s->razon_social)
                                    <div class="sd-table__sub">{{ $s->razon_social }}</div>
                                @endif
                                <div class="sd-table__sub">{{ $s->correo }}</div>
                            </td>
                            <td class="sd-table__rfc" style="font-size:.8rem;">
                                {{ $s->rfc ?: '—' }}
                            </td>
                            <td class="sd-table__region" style="font-size:.8rem; font-weight:600;">
                                {{ $s->region }}
                            </td>
                            <td>
                                <span class="sd-badge {{ $s->modalidad_pago === 'CONTADO' ? 'sd-badge-info' : 'sd-badge-warning' }}">
                                    {{ $s->modalidad_pago }}
                                </span>
                            </td>
                            <td style="font-weight:600; white-space:nowrap;">
                                ${{ number_format($s->valor_total, 2) }}
                            </td>
                            <td>
                                <span class="sd-badge sd-badge-{{ $info['badge'] }}">
                                    {{ $info['texto'] }}
                                </span>
                            </td>
                            <td class="sd-table__fecha" style="font-size:.78rem; color:var(--sd-muted); white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.dist.show', $s->id_solicitud) }}" class="sd-btn-ver">
                                    <i class="mdi mdi-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="sd-empty">
                                    <i class="mdi mdi-file-search-outline"></i>
                                    No se encontraron solicitudes con los filtros aplicados.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($solicitudes->hasPages())
            <div class="sd-card__footer">
                <span>
                    Mostrando {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }}
                    de {{ number_format($solicitudes->total()) }} solicitudes
                </span>
                <div class="sd-pagination">
                    {{-- Anterior --}}
                    @if($solicitudes->onFirstPage())
                        <span class="disabled"><i class="mdi mdi-chevron-left"></i></span>
                    @else
                        <a href="{{ $solicitudes->previousPageUrl() }}"><i class="mdi mdi-chevron-left"></i></a>
                    @endif

                    {{-- Números --}}
                    @foreach($solicitudes->getUrlRange(max(1, $solicitudes->currentPage()-2), min($solicitudes->lastPage(), $solicitudes->currentPage()+2)) as $page => $url)
                        @if($page == $solicitudes->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Siguiente --}}
                    @if($solicitudes->hasMorePages())
                        <a href="{{ $solicitudes->nextPageUrl() }}"><i class="mdi mdi-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="mdi mdi-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
