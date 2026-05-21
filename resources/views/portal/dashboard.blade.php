@extends('portal.layout')

@section('title', 'Mi Pasaporte')

@push('styles')
<style>
    .passport-card {
        background: linear-gradient(135deg, #1a3fb5 0%, #2558e0 50%, #3b74f5 100%);
        border-radius: 18px;
        padding: 28px 28px 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .passport-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }
    .passport-card::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 20px;
        width: 240px; height: 240px;
        background: rgba(255,255,255,.04);
        border-radius: 50%;
    }
    .passport-card__top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 22px; position: relative; z-index: 1; }
    .passport-card__brand { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; opacity: .7; margin-bottom: 6px; }
    .passport-card__name { font-size: 20px; font-weight: 800; line-height: 1.2; }
    .passport-card__patient { font-size: 13px; opacity: .75; margin-top: 2px; }
    .passport-card__badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 5px; flex-shrink: 0; }
    .passport-card__badge.success  { background: rgba(16,185,129,.25); color: #6ee7b7; }
    .passport-card__badge.warning  { background: rgba(245,158,11,.25); color: #fcd34d; }
    .passport-card__badge.danger   { background: rgba(239,68,68,.25);  color: #fca5a5; }
    .passport-card__badge.secondary{ background: rgba(255,255,255,.15);color: rgba(255,255,255,.7); }
    .passport-card__id { position: relative; z-index: 1; font-size: 26px; font-weight: 800; letter-spacing: 4px; font-family: 'Courier New', monospace; margin-bottom: 18px; }
    .passport-card__id small { display: block; font-size: 10px; font-weight: 600; letter-spacing: 1.5px; opacity: .6; margin-bottom: 4px; font-family: sans-serif; }
    .passport-card__footer { position: relative; z-index: 1; display: flex; gap: 28px; border-top: 1px solid rgba(255,255,255,.15); padding-top: 16px; }
    .passport-card__footer-item small { display: block; font-size: 10px; letter-spacing: 1px; opacity: .6; text-transform: uppercase; margin-bottom: 2px; }
    .passport-card__footer-item span { font-size: 13.5px; font-weight: 700; }
    .passport-card__icon { position: absolute; right: 24px; bottom: 20px; font-size: 64px; opacity: .08; z-index: 0; }

    .stats-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
    .stat-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 18px 18px 14px; }
    .stat-card__icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 20px; }
    .stat-card__icon.blue   { background: var(--blue-50);   color: var(--blue-500); }
    .stat-card__icon.green  { background: #ecfdf5; color: #059669; }
    .stat-card__icon.yellow { background: #fffbeb; color: #b45309; }
    .stat-card__icon.red    { background: #fff1f2; color: #dc2626; }
    .stat-card__val  { font-size: 22px; font-weight: 800; color: var(--slate-800); line-height: 1; }
    .stat-card__label{ font-size: 12px; color: var(--slate-400); margin-top: 4px; }

    .empty-passport {
        text-align: center; padding: 48px 24px;
        color: var(--slate-400);
    }
    .empty-passport i { font-size: 52px; margin-bottom: 12px; display: block; }
    .empty-passport p { font-size: 14px; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1>Mi Pasaporte</h1>
    <p>Consulta el estado de tu tarjeta PATS y tu información de afiliación</p>
</div>

@if($pasaporte)

    {{-- ── Tarjeta principal ── --}}
    <div class="passport-card">
        <div class="passport-card__top">
            <div>
                <div class="passport-card__brand">Pasaporte a tu Salud · PATS</div>
                <div class="passport-card__name">
                    {{ trim(($pasaporte->apellido_pa ?? '') . ' ' . ($pasaporte->apellido_ma ?? '') . ' ' . ($pasaporte->nombres ?? '')) ?: ($acceso->nombre_paciente ?? $acceso->nombre_usuario ?? '—') }}
                </div>
                @if($acceso->nombre_paciente && $acceso->nombre_usuario && $acceso->nombre_paciente !== $acceso->nombre_usuario)
                    <div class="passport-card__patient">Titular de acceso: {{ $acceso->nombre_usuario }}</div>
                @endif
            </div>
            <div class="passport-card__badge {{ $estadoColor }}">
                <i class="mdi mdi-{{ $estadoColor === 'success' ? 'check-circle' : ($estadoColor === 'warning' ? 'alert' : 'close-circle') }}"></i>
                {{ $estadoTexto }}
            </div>
        </div>

        <div class="passport-card__id">
            <small>N.º de Pasaporte</small>
            {{ str_pad($pasaporte->id_pasaporte, 8, '0', STR_PAD_LEFT) }}
        </div>

        <div class="passport-card__footer">
            <div class="passport-card__footer-item">
                <small>Vigencia</small>
                <span>{{ \Carbon\Carbon::parse($pasaporte->vigencia)->format('d/m/Y') }}</span>
            </div>
            <div class="passport-card__footer-item">
                <small>Estado</small>
                <span>{{ $pasaporte->region ?? '—' }}</span>
            </div>
            @if($pasaporte->zona)
            <div class="passport-card__footer-item">
                <small>Zona</small>
                <span>{{ $pasaporte->zona }}</span>
            </div>
            @endif
            @if($pasaporte->unidad)
            <div class="passport-card__footer-item">
                <small>Unidad</small>
                <span>{{ $pasaporte->unidad }}</span>
            </div>
            @endif
        </div>

        <i class="mdi mdi-passport passport-card__icon"></i>
    </div>

    {{-- ── Stats ── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card__icon {{ $estadoColor === 'success' ? 'green' : ($estadoColor === 'warning' ? 'yellow' : 'red') }}">
                <i class="mdi mdi-shield-check-outline"></i>
            </div>
            <div class="stat-card__val">{{ strtoupper($pasaporte->estatus ?? '—') }}</div>
            <div class="stat-card__label">Estatus de tarjeta</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon {{ $diasVigencia < 0 ? 'red' : ($diasVigencia <= 15 ? 'yellow' : 'blue') }}">
                <i class="mdi mdi-calendar-clock"></i>
            </div>
            <div class="stat-card__val">{{ $diasVigencia < 0 ? 0 : $diasVigencia }}</div>
            <div class="stat-card__label">Días de vigencia restantes</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon blue">
                <i class="mdi mdi-account-outline"></i>
            </div>
            <div class="stat-card__val">{{ ucfirst(strtolower($acceso->tipo_acceso ?? 'Paciente')) }}</div>
            <div class="stat-card__label">Tipo de acceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon green">
                <i class="mdi mdi-clock-check-outline"></i>
            </div>
            <div class="stat-card__val">
                {{ $acceso->ultimo_login ? \Carbon\Carbon::parse($acceso->ultimo_login)->format('d/m/Y') : '—' }}
            </div>
            <div class="stat-card__label">Último acceso</div>
        </div>
    </div>

    {{-- ── Datos de afiliación ── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header-bar">
            <i class="mdi mdi-information-outline"></i>
            <h2>Datos de afiliación</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item__label">CURP</div>
                    <div class="info-item__value mono">{{ $pasaporte->curp ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-item__label">Fecha de nacimiento</div>
                    <div class="info-item__value">
                        {{ $pasaporte->fecha_nacimiento ? \Carbon\Carbon::parse($pasaporte->fecha_nacimiento)->format('d/m/Y') : '—' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item__label">Correo de acceso</div>
                    <div class="info-item__value">{{ $acceso->correo_usuario }}</div>
                </div>
                <div class="info-item">
                    <div class="info-item__label">Teléfono</div>
                    <div class="info-item__value">{{ $acceso->telefono_usuario ?? '—' }}</div>
                </div>
                @if($orden && $orden->dom_estado)
                <div class="info-item">
                    <div class="info-item__label">Estado de domicilio</div>
                    <div class="info-item__value">{{ $orden->dom_estado }}</div>
                </div>
                @endif
                @if($orden && $orden->tipo_cliente)
                <div class="info-item">
                    <div class="info-item__label">Tipo de cliente</div>
                    <div class="info-item__value">{{ ucfirst(strtolower($orden->tipo_cliente)) }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

@else

    {{-- Sin pasaporte ── --}}
    <div class="card">
        <div class="empty-passport">
            <i class="mdi mdi-passport-remove"></i>
            <p>No se encontró un pasaporte activo vinculado a tu cuenta.</p>
            <p style="margin-top:8px;font-size:13px;">Contacta a soporte PATS para más información.</p>
        </div>
    </div>

@endif

@endsection
